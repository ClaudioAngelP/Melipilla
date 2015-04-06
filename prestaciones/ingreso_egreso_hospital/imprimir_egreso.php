<?php
	
	require_once('../../conectar_db.php');
	require_once('../../fpdf/fpdf.php');

	function espaciar($str) {
	
		$nstr='';
		
		for($i=0;$i<strlen($str);$i++) {
			$nstr.=$str[$i].' ';
		}
		
		return trim($nstr);
		
	}
	
	function espaciar_rut($str) {
	
		$nstr='';
		$str=str_replace('-','',$str);
		
		if(strlen($str)<9) {
			$str=str_repeat('0',9-strlen($str)).''.$str;
		}
		
		for($i=0;$i<strlen($str);$i++) {
			$nstr.=$str[$i].' ';
		}
		
		return trim($nstr);
		
	}

	
	$pdf=new FPDF('P','mm','letter');
	
	$pdf->SetAutoPageBreak(false);
	
	$hosp_id=$_GET['hosp_id']*1;
	$anio=$_POST['anio']*1;

	$h=cargar_registro("
		SELECT *,
		upper(pac_nombres) AS pac_nombres, 
		upper(pac_appat) AS pac_appat, 
		upper(pac_apmat) AS pac_apmat,
		upper(pac_direccion) AS pac_direccion,
		upper(ciud_desc) AS ciud_desc,
		date_part('year',age(hosp_fecha_ing::date, pac_fc_nac)) as edad_anios, 
		date_part('month',age(hosp_fecha_ing::date, pac_fc_nac)) as edad_meses,
		date_part('days',age(hosp_fecha_ing::date, pac_fc_nac)) as edad_dias, 
		hosp_fecha_ing::date as fecha_ing,
		hosp_fecha_ing::time as hora_ing,
		c2.tcama_tipo AS tcama_tipo_ing,
		(hosp_fecha_egr::date-hosp_fecha_hospitalizacion::date) AS dias_estada,
		c3.tcama_tipo AS tcama_tipo_egr,
                CASE WHEN prev_id IN(1,2,3,4) THEN prev_desc ELSE substring(prev_desc from 8 for 8) END as tramo,
		CASE WHEN hosp_condicion_egr=3 THEN 2 ELSE 1 END AS hosp_condicion_egr
		
		FROM hospitalizacion 
		JOIN pacientes ON hosp_pac_id=pac_id
		JOIN prevision USING(prev_id)
		LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
		LEFT JOIN doctores ON doc_id=hosp_doc_id
		LEFT JOIN comunas ON comunas.ciud_id = pacientes.ciud_id
		LEFT JOIN nacionalidad ON nacionalidad.nacion_id = pacientes.nacion_id
		LEFT JOIN grupos_etnicos ON grupos_etnicos.getn_id = pacientes.getn_id
		LEFT JOIN clasifica_camas AS c2 ON hosp_servicio=c2.tcama_id
		LEFT JOIN clasifica_camas AS c3 ON hosp_cama_egreso>=c3.tcama_num_ini AND hosp_cama_egreso<=c3.tcama_num_fin
		LEFT JOIN instituciones ON hosp_inst_id = inst_id
		WHERE hosp_id=$hosp_id;");
		
	$traslados = cargar_registros_obj("
	SELECT ptras_fecha::DATE AS fecha_traslado, ptras_fecha::TIME AS hora_traslado, tcama_tipo
	FROM paciente_traslado
	LEFT JOIN clasifica_camas AS t1 ON 
	t1.tcama_num_ini<=COALESCE(ptras_cama_destino)
	AND t1.tcama_num_fin>=COALESCE(ptras_cama_destino)
	WHERE hosp_id = $hosp_id
	ORDER BY fecha_traslado");
		
	$pdf->AddPage();
		
	$pdf->SetFillColor(200,200,200);	

	$pdf->SetFont('Courier','', 10);
	$pdf->Image('marcagua.png',8,6,198,270);

	$hosp_id=$h['hosp_id'];
	$hosp_pac_nombres=$h['pac_nombres'];
	$hosp_pac_paterno=$h['pac_appat'];
        $hosp_pac_materno=$h['pac_apmat'];
	$hosp_pac_ficha=espaciar($h['pac_ficha']);
	$hosp_pac_fechan=$h['pac_fc_nac'];
	$hosp_pac_edad=$h['edad_anios'];
	$hosp_pac_rut=espaciar_rut($h['pac_rut']);
	$hosp_pac_comuna=$h['ciud_desc'];
	$hosp_pac_direccion=$h['pac_direccion'];
	$hosp_pac_telefono=espaciar($h['pac_fono']);
	$hosp_pac_cod_comuna=$h['ciud_id'];
	$hosp_pac_sexo=($h['sex_id']*1)+1;
	
	//$hosp_pac_nacionalidad=$h[0][65];
	
	$hosp_prevision=($h['prev_id'])*1;
	
	$tmp=explode('(',$h['getn_desc']);
	$tmp[1]=trim($tmp[1],')');
	
	$hosp_pac_grupo_etnico=$tmp[1];
	
	//$hosp_pac_cod_nacion=($h[0][66]);
	$hosp_pac_nacionalidad=($h['nacion_nombre']);
	
	$hosp_leyprog=($h['hosp_motivo']*1);
	
	if($hosp_leyprog==0) $hosp_leyprog='';
	
	switch($hosp_prevision) {
		case '1': case '2': case '3': case '4': $prev='1'; break;
		case '5': $prev='2'; break;
		case '6': $prev='3'; break;
		default: $prev='7'; break;
	}

	/*switch($hosp_pac_procedencia) {
		case '0': case '1': case '2': $proc='1'; break;
		case '4': case '5': $proc='3'; break;
		case '6': $proc='2'; break;
		case '3': $proc='4'; break;
		default: $proc='5'; break;
	}*/
	
	
	
	if($prev=='1') {
		switch($h['tramo']) {
			case 'A': $hosp_prevision_clase='1';  break;
			case 'B': $hosp_prevision_clase='2';  break;
			case 'C': $hosp_prevision_clase='3';  break;
			case 'D': $hosp_prevision_clase='4';  break;
		}
	} else $hosp_prevision_clase='';
	
	$hosp_pac_fadmision=$h['fecha_ing'];
	$hosp_pac_hadmision=$h['hora_ing'];
	
	$servicio_ingreso=$h['tcama_tipo_ing'];
	
	$hosp_pac_sclinico=$h[0][54];
	$hosp_pac_modalidad=($h['hosp_modalidad']*1)+1;
	$hosp_pac_procedencia=($h['hosp_procedencia']*1);
	$proc=$hosp_pac_procedencia;
    $hosp_pac_institucion=($h['inst_nombre']);
    $hosp_pac_cod_institucion=espaciar(str_replace('-','',$h['inst_codigo_ifl']));
	$hosp_comentario=($h[0][23]); //comentario


	if (($h['edad_anios']*1 == 0) AND ($h['edad_meses']*1 == 0)){
		$hosp_pac_edad=espaciar($h['edad_dias']);
		$hosp_pac_edad_tipo=3;
	} elseif (($h['edad_anios']*1 == 0) AND ($h['edad_meses']*1 > 0)) {
		$hosp_pac_edad=espaciar($h['edad_meses']);
		$hosp_pac_edad_tipo=2;
	} else {
		$hosp_pac_edad=espaciar($h['edad_anios']);
		$hosp_pac_edad_tipo=1;
	}

	$pos = strpos($hosp_pac_fechan, "/");
	
	$date = explode("/",$hosp_pac_fechan);
	$anon = $date[2]; 
	$mesn = $date[1]; 
	$dian = $date[0];
	
	$date = explode("/",$hosp_pac_fadmision);
	$anoa = $date[2]; 
	$mesa = $date[1]; 
	$diaa = $date[0];
	
	$hora = explode(":",$hosp_pac_hadmision);
	$minuto = $hora[1]; 
	$hora = $hora[0];

$pdf->Image('logo.png',8,6,21,21);	
$pdf->SetFont('Courier','',13);
$pdf->SetLineWidth(0.5);
$pdf->Text(94, 33.5, 'HOSPITAL SAN JOSE DE MELIPILLA'); //Establecimiento
$pdf->Text(171, 33, '   1 0 1 5 0 '); //Codigo del establecimiento
$pdf->SetXY(48, 34); $pdf->Cell(20, 6, $hosp_pac_ficha,0,1,'L'); //Nº Historia Clinica
$pdf->Ln();
$pdf->SetFont('Courier','',13);
$pdf->SetXY(150, 34); $pdf->Cell(20,6, 'Cta. Corriente:', 0,1,'C'); //codigo interno
$pdf->SetXY(186, 34); $pdf->Cell(20,6, $hosp_id, 0,1,'C'); //codigo interno
$pdf->SetFont('Courier','',13);
$pdf->SetXY(32, 43);$pdf->Cell(41, 7, $hosp_pac_paterno,0,1,'L'); //Apellido Paterno
$pdf->SetXY(77, 43);$pdf->Cell(40, 7, $hosp_pac_materno,0,1,'L'); //Apellido Materno
$pdf->SetXY(123, 43);$pdf->Cell(78, 7, $hosp_pac_nombres,0,1,'L'); // Nombres Paciente
$pdf->SetXY(26.5, 51.5);$pdf->Cell(60, 7, $hosp_pac_rut,0,1,'L'); // Rut Paciente
$pdf->SetXY(94, 51);$pdf->Cell(5, 7, $hosp_pac_sexo,0,1,'L'); // Sexo del paciente

$pdf->SetXY(158, 51.3); $pdf->Cell(6, 7, espaciar($dian),0,1,'L'); // dia nacimiento
$pdf->SetXY(171, 51.3); $pdf->Cell(6, 7, espaciar($mesn),0,1,'L'); // mes nacimiento
$pdf->SetXY(183, 51.3); $pdf->Cell(14, 7, espaciar($anon),0,1,'L'); // año nacimiento

$pdf->SetXY(33, 59); $pdf->Cell(10, 7, $hosp_pac_edad,0,1,'L'); // edad del paciente
$pdf->SetXY(66, 59); $pdf->Cell(6, 7, $hosp_pac_edad_tipo,0,1,'L'); //tipo edad (dias, mes o año)

$pdf->SetXY(95, 59); $pdf->Cell(6, 7, $hosp_pac_grupo_etnico,0,1,'L'); //grupo etnico

$pdf->SetXY(158, 64); $pdf->Cell(40, 7, $hosp_pac_nacionalidad,0,1,'L'); // Nacionalidad

$pdf->SetFont('Courier','',9);

$pdf->SetXY(34, 75.5);$pdf->Cell(169, 7, $hosp_pac_direccion,0,1,'L'); // Direccion del paciente
$pdf->SetFont('Courier','',13);
$pdf->SetXY(160, 84.5); $pdf->Cell(58, 7, $hosp_pac_telefono,0,1,'L'); // Telefono del paciente

$pdf->SetXY(48, 133.5); $pdf->Cell(6, 7, espaciar($hora),0,1,'L'); //hora de admision
$pdf->SetXY(60, 133.5); $pdf->Cell(14, 7, espaciar($minuto),0,1,'L'); //minutos de admision

$pdf->SetXY(72, 133.5); $pdf->Cell(6, 7, espaciar($diaa),0,1,'L'); //dia de admision
$pdf->SetXY(85, 133.5); $pdf->Cell(6, 7, espaciar($mesa),0,1,'L'); //mes de admision
$pdf->SetXY(97, 133.5); $pdf->Cell(14, 7, espaciar(substr($anoa,2,2)),0,1,'L'); //año de admision
$pdf->SetFont('Courier','',9);
$pdf->SetXY(110, 133.5); $pdf->Cell(14, 7, str_replace('SERV. CL.','',$servicio_ingreso),0,1,'L'); //año de admision
$pdf->SetFont('Courier','',13);

    $y = 138.5;
    $tmp = $servicio_ingreso;
    if($traslados!=false){
        for($i=0;$i<sizeof($traslados);$i++) {
            $dif=false;
            if($traslados[$i]['tcama_tipo']!= $tmp and $j<4){
                $tfecha = explode("/", $traslados[$i]['fecha_traslado']);
                $tdia = $tfecha[0];
                $tmes = $tfecha[1];
                $tanio= substr($tfecha[2],2);
                $pdf->SetXY(72, $y); $pdf->Cell(6, 7, espaciar($tdia),0,1,'L'); //dia traslado
                $pdf->SetXY(85, $y); $pdf->Cell(6, 7, espaciar($tmes),0,1,'L'); //mes traslado
                $pdf->SetXY(97, $y); $pdf->Cell(14, 7, espaciar($tanio),0,1,'L'); //año traslado
                $pdf->SetXY(110, $y); $pdf->Cell(14, 7, $traslados[$i]['tcama_tipo'],0,1,'L'); //servicio
                $y+=4.6;
                $j++;
            }
            if ($j==4 and $traslados[$i]['tcama_tipo']!=$tmp){
                $otros_traslados[$k]['fecha_traslado'] = $traslados[$i]['fecha_traslado'];
                $otros_traslados[$k]['tcama_tipo'] = $traslados[$i]['tcama_tipo'];
                $otros_traslados[$k]['hora_traslado'] = $traslados[$i]['hora_traslado'];
                $k++;
            }
            $tmp = $traslados[$i]['tcama_tipo'];
        }
    }

if($h['hosp_fecha_egr']!='') {
	
	list($egr_fecha, $egr_hora)=explode(' ',$h['hosp_fecha_egr']);
	
	$date2 = explode("/",$egr_fecha);
	$anoa2 = $date2[2]; 
	$mesa2 = $date2[1]; 
	$diaa2 = $date2[0];
	
	$hora2 = explode(":",$egr_hora);
	$minuto2 = $hora2[1]; 
	$hora2 = $hora2[0];
	
	$servicio_egreso=substr($h['tcama_tipo_egr'],0,40);

	$pdf->SetXY(48, 157); $pdf->Cell(6, 7, espaciar($hora2),0,1,'L'); //hora de egreso
	$pdf->SetXY(60, 157); $pdf->Cell(14, 7, espaciar($minuto2),0,1,'L'); //minutos de egreso

	$pdf->SetXY(72, 157); $pdf->Cell(6, 7, espaciar($diaa2),0,1,'L'); //dia de egreso
	$pdf->SetXY(85, 157); $pdf->Cell(6, 7, espaciar($mesa2),0,1,'L'); //mes de egreso
	$pdf->SetXY(97, 157); $pdf->Cell(14, 7, espaciar(substr($anoa2,2,2)),0,1,'L'); //año de egreso
	$pdf->SetXY(110, 157); $pdf->Cell(14, 7, str_replace('SERV. CL.','',$servicio_egreso),0,1,'L'); 

	$pdf->SetXY(48, 161.5); $pdf->Cell(6, 7, espaciar($h['dias_estada']),0,1,'L'); //dias estada

	$pdf->SetXY(97, 161.5); $pdf->Cell(6, 7, espaciar($h['hosp_condicion_egr']),0,1,'L'); //condicion de egreso
	
}

//$pdf->SetXY(142, 135); $pdf->Cell(41, 5, $hosp_comentario,0,1,'L'); // Comentario

$pdf->SetXY(45, 84.5); $pdf->Cell(20, 7, $hosp_pac_comuna,0,1,'L'); // Comuna Paciente
$pdf->SetXY(92, 84.5); $pdf->Cell(25, 7, espaciar($hosp_pac_cod_comuna),0,1,'L'); //Codigo comuna

$pdf->SetXY(35, 92.5); $pdf->Cell(5, 5, $prev,0,1,'L'); // Prevision
$pdf->SetXY(93.5, 92.5); $pdf->Cell(5, 5, $hosp_prevision_clase,0,1,'L'); // Clase beneficiario


$pdf->SetXY(125, 92.5); $pdf->Cell(5, 5, $hosp_pac_modalidad,0,1,'L'); // Modalidad de atencion

$pdf->SetXY(182, 92.5); $pdf->Cell(5, 5, $hosp_leyprog,0,1,'L'); // Modalidad de atencion


$pdf->SetXY(40, 111.5); $pdf->Cell(6, 7, $proc,0,1,'L'); // Procedencia

$pdf->SetXY(108, 116); $pdf->Cell(60, 5, $hosp_pac_institucion,0,1,'L'); // Establecimiento de procedencia
$pdf->SetXY(177, 112.5); $pdf->Cell(20, 5, $hosp_pac_cod_institucion,0,1,'L'); // Codigo Institucion
$pdf->SetXY(187.5, 60.5); $pdf->Cell(16, 5, $hosp_pac_cod_nacion,0,1,'L'); // Codigo Nacion


$diag_cod=espaciar(str_replace('.','',$h['hosp_diag_cod2']));
$diagnostico=substr($h['hosp_diagnostico2'],0,40);


$pdf->SetXY(55, 175); $pdf->Cell(60, 7, $diagnostico,0,1,'L'); //diag
$pdf->SetXY(182, 175); $pdf->Cell(60, 7, $diag_cod,0,1,'L'); //diag

for($i=1;$i<6;$i++) {
	$h['hosp_diag_cod'.($i+2)]=espaciar(str_replace('.','',$h['hosp_diag_cod'.($i+2)]));
	$h['hosp_diagnostico'.($i+2)]=substr($h['hosp_diagnostico'.($i+2)],0,40);
	$pdf->SetXY(55, 175+($i*4.7)); $pdf->Cell(60, 7, $h['hosp_diagnostico'.($i+2)],0,1,'L'); //n diag
	$pdf->SetXY(182, 175+($i*4.7)); $pdf->Cell(60, 7, $h['hosp_diag_cod'.($i+2)],0,1,'L'); //n diag
}

$bb=cargar_registros_obj("SELECT * FROM hospitalizacion_partos WHERE hosp_id=$hosp_id ORDER BY hospp_orden, hospp_id DESC LIMIT 4;");

$pdf->SetFont('Courier','',10);
if($bb)
for($i=0;$i<sizeof($bb);$i++) {
	$pdf->SetXY(58, 215+($i*4.25)); $pdf->Cell(10, 7, ($bb[$i]['hospp_condicion']*1)+1,0,1,'L'); //n parto	
	$pdf->SetXY(104, 215+($i*4.25)); $pdf->Cell(10, 7, ($bb[$i]['hospp_sexo']*1)+1,0,1,'L'); //n parto	
	$pdf->SetXY(140, 215+($i*4.25)); $pdf->Cell(10, 7, espaciar($bb[$i]['hospp_peso_gramos']),0,1,'L'); //n parto	
	$pdf->SetXY(181, 215+($i*4.25)); $pdf->Cell(10, 7, espaciar($bb[$i]['hospp_apgar']),0,1,'L'); //n parto	
}
$pdf->SetFont('Courier','',13);

if($h['hosp_doc_id']*1!=0) {
	$pdf->SetXY(32, 252); $pdf->Cell(10, 7, $h['doc_paterno'].' '.$h['doc_materno'].' '.$h['doc_nombres'],0,1,'L'); //nombre doctor
	$pdf->SetXY(140, 254); $pdf->Cell(10, 7, $h['esp_desc'],0,1,'L'); //nombre doctor
	$pdf->SetXY(23, 260.5); $pdf->Cell(10, 7, espaciar_rut($h['doc_rut']),0,1,'L'); //nombre doctor
}

if(isset($otros_traslados)) {
	$pdf->AddPage();
	$pdf->SetFillColor(200,200,200);
	$pdf->SetFont('Courier','U', 10);
	
	$pdf->Text(40, 30, 'OTROS TRASLADOS'); //Encabezado
	
	$pdf->SetFont('Courier','', 10);
	
	$pos = 30;
	
		for($i=0;$i<sizeof($otros_traslados);$i++){
			
			$pdf->SetXY(70, $pos); $pdf->Cell(6, 7, substr($otros_traslados[$i]['hora_traslado'], 0, 8),0,1,'L'); //Hora
			
			$pdf->SetXY(40, $pos); $pdf->Cell(6, 7, $otros_traslados[$i]['fecha_traslado'],0,1,'L'); //Fecha
     
			$pdf->SetXY(100, $pos); $pdf->Cell(14, 7, $otros_traslados[$i]['tcama_tipo'],0,1,'L'); //servicio
			
			$pos += 6;
		}
	}


$pdf->Output('REPORTE_DEIS_'.$hosp_id.'_.pdf','I');

?>
