<?php 
	
	require_once('../../conectar_db.php');
	
	function bisiesto($anio_actual)
	{
   	$bisiesto=false;
    	//probamos si el mes de febrero del año actual tiene 29 días
      if (checkdate(2,29,$anio_actual))
      {
      	$bisiesto=true;
    	}
    	return $bisiesto;
	} 	
	
	
	
	function edad($fecha_nac)
	{
	global $dias;
	global $meses;
	global $anos;
	// separamos en partes las fechas
	$fecha_actual=date("d/m/Y");
	$array_nacimiento = explode ( "/", $fecha_nac );
	$array_actual = explode ( "/", $fecha_actual );
	
	$dias =  $array_actual[0] - $array_nacimiento[0]; // calculamos días	
	$meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
	$anos =  $array_actual[2] - $array_nacimiento[2]; // calculamos años
	
	//ajuste de posible negativo en $días
	if ($dias < 0)
	{
   	--$meses;
    
    	//ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
    	switch ($array_actual[1]) 
    	{
			case 1:     $dias_mes_anterior=31; break;
         case 2:     $dias_mes_anterior=31; break;
         case 3: 
                if (bisiesto($array_actual[0]))
                {
                    $dias_mes_anterior=29; break;
                } 
                else 
                {
                    $dias_mes_anterior=28; break;
                }
			case 4:     $dias_mes_anterior=31; break;
         case 5:     $dias_mes_anterior=30; break;
         case 6:     $dias_mes_anterior=31; break;
         case 7:     $dias_mes_anterior=30; break;
         case 8:     $dias_mes_anterior=31; break;
         case 9:     $dias_mes_anterior=31; break;
         case 10:     $dias_mes_anterior=30; break;
         case 11:     $dias_mes_anterior=31; break;
         case 12:     $dias_mes_anterior=30; break;
    	}
		$dias=$dias + $dias_mes_anterior;
	} 
	
	//ajuste de posible negativo en $meses
	if ($meses < 0)
	{
   	--$anos;
    	$meses=$meses + 12;
	} 
	//return "$anos años con $meses meses y $dias días";
	if($anos!=0)
	{
		return $anos;		
	}
	else 
	{
		if($meses!=0)
		{
			return $meses;			
		}
		else 
		{
			return $dias;
		}
	}	
	
	
	
	return "$anos años con $meses meses y $dias días";
	
		
	}


	$csv_end="\n";
	$csv_sep=";";
	$csv_esp=" "; 
	$csv="";
	$encontrado=false;

	$tmp_inicio = microtime(true);
	$fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
   	
        header("Content-type: application/csv");
        header("Content-Disposition: filename=\"Listado de egresos.csv\";");
   
	$datos=cargar_registros_obj("
	SELECT *, hosp_fecha_ing::date, hosp_fecha_ing::time AS hosp_hora_ing, hosp_fecha_egr::date, hosp_fecha_egr::time AS hosp_hora_egr, 
	(hosp_fecha_egr::date - hosp_fecha_ing::date) AS dias,inst_codigo_ifl as cod_inst FROM (
	SELECT *,
	COALESCE( ( SELECT centro_ruta FROM paciente_traslado WHERE 
					ptras_id=( SELECT ptras_id FROM paciente_traslado WHERE 
						     paciente_traslado.hosp_id=hospitalizacion.hosp_id ORDER BY ptras_fecha, ptras_id DESC LIMIT 1) AND
					paciente_traslado.hosp_id=hospitalizacion.hosp_id ), hosp_centro_ruta ) AS hosp_centro_egreso
	FROM hospitalizacion) AS foo
	LEFT JOIN centro_costo ON hosp_centro_egreso=centro_ruta
	JOIN pacientes ON hosp_pac_id=pac_id
	LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id
	LEFT JOIN doctores on hosp_doc_id=doc_id
	left join especialidad_doctor on doctores.doc_id=especialidad_doctor.doc_id
	left join especialidades on especialidad_doctor.esp_id=especialidades.esp_id
	LEFT JOIN instituciones on hosp_inst_id=inst_id
	WHERE
	foo.hosp_fecha_egr::date BETWEEN '$fecha1' AND '$fecha2'
	ORDER BY foo.hosp_folio
	");
	
	$rows = count($datos);
	echo $rows." numero(s) registros.\n";

	

	$csv.="Egreso".$csv_sep."Cod-Hosp".$csv_sep.utf8_decode("Ficha-Clínica").$csv_sep."AP-Paterno";
	$csv.=$csv_sep."AP-Materno".$csv_sep."Nombre-Paciente".$csv_sep."RUT".$csv_sep."SEXO";
	$csv.=$csv_sep."Fecha-Nacimiento".$csv_sep."Edad".$csv_sep."Medida-Edad".$csv_sep.utf8_decode("Teléfono");
	$csv.=$csv_sep."ETNIA".$csv_sep."Domicilio".$csv_sep."Sector-Nombre".$csv_sep."Comuna".$csv_sep.utf8_decode("Código-Comuna");
	$csv.=$csv_sep."Nacionalidad".$csv_sep.utf8_decode("Previsión").$csv_sep."Clase-Beneficiario".$csv_sep."Modalidad";
	$csv.=$csv_sep."GES".$csv_sep."Ley/Prog.-Social".$csv_sep."Procedencia".$csv_sep."Nombre-Procedencia";
	$csv.=$csv_sep."Cod-Procedencia".$csv_sep."Hora-Ingreso".$csv_sep."Fecha-Ingreso".$csv_sep."Nombre-Servicio".$csv_sep."Codigo-Serv-Ingreso";
	$csv.=$csv_sep."Fecha-1er-Traslado".$csv_sep."Nombre-1er-Traslado".$csv_sep.utf8_decode("Código-1er-Traslado");
	$csv.=$csv_sep."Fecha-2do-Traslado".$csv_sep."Nombre-2do-Traslado".$csv_sep.utf8_decode("Código-2do-Traslado");
	$csv.=$csv_sep."Fecha-3er-Traslado".$csv_sep."Nombre-3er-Traslado".$csv_sep.utf8_decode("Código-3er-Traslado");
	$csv.=$csv_sep."Fecha-4to-Traslado".$csv_sep."Nombre-4to-Traslado".$csv_sep.utf8_decode("Código-4to-Traslado");
	$csv.=$csv_sep."Hora-Egreso".$csv_sep."Fecha-Egreso".$csv_sep."Servicio-Egreso".$csv_sep.utf8_decode("Código-Serv-Egreso");
	$csv.=$csv_sep.utf8_decode("Días").$csv_sep.utf8_decode("Condición-Egreso").$csv_sep."Parto".$csv_sep."Nacimiento";
	$csv.=$csv_sep."Diagnostico-1".$csv_sep.utf8_decode("Código-1er-Diagnostico");
	$csv.=$csv_sep."Diagnostico-2".$csv_sep.utf8_decode("Código-2do-Diagnostico");
	$csv.=$csv_sep."Diagnostico-3".$csv_sep.utf8_decode("Código-3er-Diagnostico");
	$csv.=$csv_sep."Diagnostico-4".$csv_sep.utf8_decode("Código-4to-Diagnostico");
	$csv.=$csv_sep."Causa-Externa".$csv_sep.utf8_decode("Código-Causa-Externa");
	$csv.=$csv_sep.utf8_decode("Inter-Quirúrgicas");
	$csv.=$csv_sep."Fecha-Interv-1".$csv_sep.utf8_decode("Detalle-inter-Quirúrgicas-1").$csv_sep.utf8_decode("Código-Interv-1");
	$csv.=$csv_sep."Fecha-Interv-2".$csv_sep.utf8_decode("Detalle-inter-Quirúrgicas-2").$csv_sep.utf8_decode("Código-Interv-2");
	$csv.=$csv_sep."Fecha-Interv-3".$csv_sep.utf8_decode("Detalle-inter-Quirúrgicas-3").$csv_sep.utf8_decode("Código-Interv-3");
	$csv.=$csv_sep."Fecha-Interv-4".$csv_sep.utf8_decode("Detalle-inter-Quirúrgicas-4").$csv_sep.utf8_decode("Código-Interv-4");
	$csv.=$csv_sep."Fecha-Interv-5".$csv_sep.utf8_decode("Detalle-inter-Quirúrgicas-5").$csv_sep.utf8_decode("Código-Interv-5");
	$csv.=$csv_sep."Nombre-Doctor".$csv_sep."RUT-Doctor".$csv_sep."Especialidad";	
	$csv.=$csv_end;
	if($datos)
	{
		for($i=0;$i<count($datos);$i++)
		{
			$edadf=0;			
			$t=cargar_registros_obj("
			SELECT *, ptras_fecha::date AS ptras_fecha1 FROM paciente_traslado JOIN centro_costo USING (centro_ruta) WHERE hosp_id=".$datos[$i]['hosp_id']."		
			order by ptras_fecha");
			if($datos[$i]['pac_fc_nac']!="") 
			{			
				$edadf=edad($datos[$i]['pac_fc_nac']);
			}			
			else 
			{				
				$edadf="";
			}			
			
			$csv.=$datos[$i]['hosp_folio'].$csv_sep."07101".$csv_sep.$datos[$i]['pac_ficha'].$csv_sep.$datos[$i]['pac_appat']
			.$csv_sep.$datos[$i]['pac_apmat'].$csv_sep.$datos[$i]['pac_nombres'].$csv_sep.
			$datos[$i]['pac_rut'].$csv_sep.($datos[$i]['sex_id']+1).$csv_sep.$datos[$i]['pac_fc_nac']
			.$csv_sep.utf8_decode($edadf);
			if($edadf!="") 
			{			
				if($anos!=0)
				{
					$csv.=$csv_sep."3";
				}
				else 
				{
					if($meses!=0)
					{
						$csv.=$csv_sep."2";
					}
					else 
					{
						$csv.=$csv_sep."1";
					}
				}
			}
			else
			{
				$csv.=$csv_sep."Sin Def";
			}
			$csv.=$csv_sep.$datos[$i]['pac_fono'].$csv_sep."0"
			.$csv_sep.$datos[$i]['pac_direccion'].$csv_sep.$datos[$i]['sector_nombre']
			.$csv_sep.$datos[$i]['ciud_desc'].$csv_sep.$datos[$i]['codigo_nacional']
			.$csv_sep.$datos[$i]['nacion_id'];
			
			
			if($datos[$i]['hosp_prevision']==0)
			{
				$csv.=$csv_sep.($datos[$i]['hosp_prevision']+1).$csv_sep.($datos[$i]['hosp_prevision_clase']+1);
			}
			else 
			{
				$csv.=$csv_sep.($datos[$i]['hosp_prevision']+1).$csv_sep."0";
			}			
			$csv.=$csv_sep.($datos[$i]['hosp_modalidad']+1);
			if($datos[$i]['hosp_ges']==0)
			{
				$csv.=$csv_sep."2";
			}
			else 
			{
				$csv.=$csv_sep."1";
			}			
			$csv.=$csv_sep.$datos[$i]['hosp_motivo'];
			if($datos[$i]['hosp_procedencia']==1 or $datos[$i]['hosp_procedencia']==3)
			{
				$csv.=$csv_sep.($datos[$i]['hosp_procedencia']+1).$csv_sep.$datos[$i]['inst_nombre'].$csv_sep.$datos[$i]['cod_inst'];
			}			
			else 
			{
				$csv.=$csv_sep.($datos[$i]['hosp_procedencia']+1).$csv_sep."0".$csv_sep."0";
			}
			$csv.=$csv_sep.$datos[$i]['hosp_hora_ing'].$csv_sep.$datos[$i]['hosp_fecha_ing'];
			
			$c=cargar_registros_obj("
			SELECT * from centro_costo WHERE centro_ruta='".$datos[$i]['hosp_centro_ruta']."'");
			if($c)
			{
				$csv.=$csv_sep.$c[0]['centro_nombre'].$csv_sep.$c[0]['centro_codigo'];
			}
			else 
			{
				$csv.=$csv_sep."No Asignado".$csv_sep."No Asignado";
			}			
			
			for($j=0;$j<4;$j++) {
				if(isset($t[$j]))
				{
					$csv.=$csv_sep.$t[$j]['ptras_fecha1'].$csv_sep.$t[$j]['centro_nombre'].$csv_sep.$t[$j]['centro_codigo'];
				}
				else {
					$csv.=$csv_sep.$csv_sep.$csv_sep;
				}
			}
			$csv.=$csv_sep.$datos[$i]['hosp_hora_egr'].$csv_sep.$datos[$i]['hosp_fecha_egr'];
			
			if($t)
			{
				$csv.=$csv_sep.$t[(count($t)-1)]['centro_nombre'].$csv_sep.$t[(count($t)-1)]['centro_codigo'];
			}
			else 
			{
				$csv.=$csv_sep.$datos[$i]['centro_nombre'].$csv_sep.$datos[$i]['centro_codigo'];
			}

			$csv.=$csv_sep.$datos[$i]['dias'].$csv_sep.$datos[$i]['hosp_condicion_egr'];



			if($datos[$i]['hosp_parto']=="t")
			{
				$csv.=$csv_sep."1";			
			}
			else {
				if($datos[$i]['hosp_parto']=="")
				{
					$csv.=$csv_sep;
				}
				else 
				{				
					$csv.=$csv_sep."2";
				}
			}
			
			if($datos[$i]['hosp_nacimiento']=="t")
			{
				$csv.=$csv_sep."1";
			}
			else
			{
				if($datos[$i]['hosp_nacimiento']=="")
				{
					$csv.=$csv_sep;
				}
				else {				
					$csv.=$csv_sep."2";
				}			
			}
			$d=cargar_registros_obj("SELECT *, pdiag_fecha::date as pg_diag_fecha1 FROM paciente_diagnostico JOIN diagnosticos USING (diag_cod) WHERE hosp_id=".$datos[$i]['hosp_id']." AND pdiag_tipo=0
				order by pdiag_orden");
			if($d)
			{			
				for($x=0;$x<4;$x++) 
				{
					if(isset($d[$x]))
					{
						if(substr($d[$x]['diag_cod'],0,1)!="V" && substr($d[$x]['diag_cod'],0,1)!="W" && substr($d[$x]['diag_cod'],0,1)!="X" && substr($d[$x]['diag_cod'],0,1)!="Y")
						{
							$csv.=$csv_sep.utf8_decode($d[$x]['diag_desc']).$csv_sep.utf8_decode($d[$x]['diag_cod']);
						}
						else 
						{
							$csv.=$csv_sep.$csv_sep;
						}
					}
					else 
					{				
						$csv.=$csv_sep.$csv_sep;
					}
				}
				for($x=0;$x<count($d);$x++) 
				//for($x=0;$x<4;$x++)
				{
					if(isset($d[$x]))
					{
						if(substr($d[$x]['diag_cod'],0,1)=="V" || substr($d[$x]['diag_cod'],0,1)=="W" || substr($d[$x]['diag_cod'],0,1)=="X" || substr($d[$x]['diag_cod'],0,1)=="Y")
						{
							$csv.=$csv_sep.utf8_decode($d[$x]['diag_desc']).$csv_sep.utf8_decode($d[$x]['diag_cod']);
							$encontrado=true;						
						}			
					}
				}
				if($encontrado==false)
				{
					$csv.=$csv_sep.$csv_sep;
				}		
			}			
			else
			{
				for($x=0;$x<5;$x++)
				{
					$csv.=$csv_sep.$csv_sep;
				}		
			}
			$p=cargar_registros_obj("
			SELECT *, presta_fecha::date AS presta_fecha1 
			FROM prestacion JOIN codigos_prestacion ON presta_codigo=codigo WHERE hosp_id=".$datos[$i]['hosp_id']."
			order by presta_fecha1");
			
			echo "SELECT *, presta_fecha::date AS presta_fecha1 
			FROM prestacion JOIN codigos_prestacion ON presta_codigo=codigo WHERE hosp_id=".$datos[$i]['hosp_id']."
			order by presta_fecha1";
			if($p)
			{			
				$csv.=$csv_sep."1";
				for($a=0;$a<5;$a++)
				{
					if(isset($p[$a]))
					{						
						$csv.=$csv_sep.$p[$a]['presta_fecha1'].$csv_sep.str_replace(';',',',utf8_decode($p[$a]['glosa'])).$csv_sep.$p[$a]['codigo'];
					}
					else 
					{
						$csv.=$csv_sep.$csv_sep.$csv_sep;
					}				
				}
			}
			else 
			{
				$csv.=$csv_sep."2";				
				for($a=0;$a<5;$a++)
				{				
					$csv.=$csv_sep.$csv_sep.$csv_sep;
				}
			}
			
			
			$csv.=$csv_sep.$datos[$i]['doc_paterno'].$csv_esp.$datos[$i]['doc_materno'].$csv_esp.$datos[$i]['doc_nombres'];
			$csv.=$csv_sep.$datos[$i]['doc_rut'];			
			$csv.=$csv_sep.$datos[$i]['esp_desc'];
			
			$csv.=$csv_end;
			
			
			$encontrado=false;
			
			
			
			
		}	
	}	
	print($csv);
	

