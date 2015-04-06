<?php 
	
	require_once('../../conectar_db.php');
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$tipo_informe=pg_escape_string($_POST['tipo_informe']*1);
	
	
	$esp_id=$_POST['esp_id']*1;	
	$serv_id=$_POST['centro_ruta0']*1;	
	$doc_id=$_POST['doc_id']*1;	
	
	$condicion=$_POST['condicion_egreso']*1;	
	
	$dias_desde=$_POST['dias_desde']*1;	
	$dias_hasta=$_POST['dias_hasta']*1;	
	
	$filtro=pg_escape_string($_POST['filtro']);
 		
 	$tiempo=$_POST['tiempo_espera']*1;	
	$tipo_camas=$_POST['tipo_camas']*1;	
	
 	
 	if($esp_id!=0) {
		$esp_w="(hosp_esp_id=$esp_id OR hosp_esp_id2=$esp_id)";
	} else {
		$esp_w='true';
	}

	if($serv_id!=0) {
		$serv_w="hosp_servicio=$serv_id";
	} else {
		$serv_w='true';
	}
 
 
 
 
 
 	if($doc_id!=0) {
		$doc_w="hosp_doc_id=$doc_id";
	} else {
		$doc_w='true';
	}
  
	if($dias_hasta>0 AND $dias_desde>=0) {
		$dias_w="((CURRENT_DATE-COALESCE(hosp_fecha_hospitalizacion, hosp_fecha_ing)::date) BETWEEN $dias_desde AND $dias_hasta)";
	} else {
		$dias_w='true';
	}
  
  
	if($filtro!='') {

		$filtro=trim($filtro);

		$pbusca=preg_replace('/[^A-Za-z0-9 ]/','_', $filtro);
		
		$pbusca=preg_replace('/\s{2,}/', ' ', $pbusca);
		
		$pbusca=str_replace(' ', '%', $pbusca);
		
		$filtro_w="
	    (pac_rut='$filtro' OR pac_ficha='$filtro' OR 
		upper(pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) ILIKE '%$pbusca%')
		";		
		
	} else {
		
		$filtro_w="true";
		
	}					
  		
 		
	if(isset($_POST['xls']) AND $_POST['xls']=='1') {
	
  	   header("Content-type: application/vnd.ms-excel");
       header("Content-Disposition: filename=\"Informe_CAMAS.xls\";");			
		
	}
	
?>


<table style='width:100%;'>

<?php
	
	if($tipo_informe==1) {

		if($tiempo!=0) {
			
			if($tiempo==1)
				$tiempo_w="date_part('epoch',CURRENT_TIMESTAMP-hosp_fecha_ing) BETWEEN 0 AND 43200";
			elseif($tiempo==2)
				$tiempo_w="date_part('epoch',CURRENT_TIMESTAMP-hosp_fecha_ing) BETWEEN 43201 AND 86400";
			elseif($tiempo==3)
				$tiempo_w="date_part('epoch',CURRENT_TIMESTAMP-hosp_fecha_ing) BETWEEN 86401 AND 172800";
				
		} else {
			
			$tiempo_w='true';
			
		}
	 
		
		$q=cargar_registros_obj("
		
			SELECT * FROM (
		
			SELECT *,
			
			upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			(CURRENT_DATE-hosp_fecha_ing::date) AS dias_espera,
			EXTRACT(HOUR FROM (CURRENT_DATE-hosp_fecha_ing)) AS horas_espera,
			EXTRACT(MINUTE FROM (CURRENT_DATE-hosp_fecha_ing)) AS minutos_espera
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN clasifica_camas ON hosp_servicio=tcama_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			
			WHERE hosp_numero_cama=0 AND hosp_fecha_egr IS NULL AND
			$esp_w AND $serv_w AND $doc_w AND $filtro_w AND $dias_w AND $tiempo_w
			
			) AS foo ORDER BY dias_espera DESC
			
		", true);

/*
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
*/
		
		print("<tr class='tabla_header'>
		<td>R.U.T.</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>(Sub)Especialidad</td>
		<td>Servicio Ingreso</td>
		<td>Medico Tratante</td>
		<td>Fecha Ingreso</td>
		<td>Diagn&oacute;stico</td>
		<td>Tiempo de espera</td>
		</tr>");
		
		//<td>Estado</td>
		//<td>Servicio / Sala</td>
		//<td>Cama</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($q[$i]['esp_desc']!='')
				$especialidad=$q[$i]['esp_desc'];
			else
				$especialidad='<i>(Sin Asignar...)</i>';

			if($q[$i]['tcama_tipo']!='')
				$servicio=$q[$i]['tcama_tipo'];
			else
				$servicio='<i>(Sin Asignar...)</i>';

			if($q[$i]['doc_rut']!='')
				$med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
			else
				$med_tratante='<i>(Sin Asignar...)</i>';
			
			print("<tr class='$clase'>
			<td style='text-align:right;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres'])."</td>
			<td style='font-size:10px;'>".$especialidad."</td>
			<td style='font-size:10px;'>".$servicio."</td>
			<td style='font-size:10px;'>".$med_tratante."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_ing']."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['hosp_diag_cod']."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['dias_espera']." Días ".$q[$i]['horas_espera']." Horas ".$q[$i]['minutos_espera']." Minutos "."</td>
			</tr>");

			//<td style='text-align:center;'>".$q[$i]['hest_nombre']."</td>
			//<td style='text-align:center;'><b>".$q[$i]['tcama_tipo'].'</b> <br /> '.$q[$i]['cama_tipo']."</td>
			//<td style='text-align:center;'>".(($q[$i]['hosp_numero_cama']*1-$q[$i]['tcama_num_ini']*1)+1)."</td>
			
		}
		
	} elseif($tipo_informe==2) {
	
		if($serv_id!=0) {
			$serv_w="t1.tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 
		
		$q=cargar_registros_obj("
		
			SELECT * FROM (
		
			SELECT *,
			
			upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			(CURRENT_DATE-COALESCE(hosp_fecha_ing,hosp_fecha_hospitalizacion)::date) AS dias_espera,
			t1.tcama_tipo AS tcama_tipo, t1.tcama_num_ini AS tcama_num_ini,
			t2.tcama_tipo AS servicio,hosp_id,
			date_part('year',age( pac_fc_nac ))AS edad_paciente,
			cama_num_ini , cama_num_fin
			
			
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod

			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama

			LEFT JOIN clasifica_camas AS t2 ON 
				t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
			
			WHERE hosp_anulado!=1 AND hosp_numero_cama>0 AND hosp_fecha_egr IS NULL AND
			$esp_w AND $serv_w AND $doc_w AND $filtro_w AND $dias_w
			
			) AS foo ORDER BY hosp_numero_cama", true );
		
		if(isset($_POST['xls']) AND $_POST['xls']=='1') {
			if($serv_id!=''){
				$serv_nombre_q=cargar_registro("SELECT tcama_tipo FROM clasifica_camas WHERE tcama_id=$serv_id");
				$serv_nombre='SERVICIO '.$serv_nombre_q['tcama_tipo'];
			}else{
				$serv_nombre='TODOS LOS SERVICIOS';
			}
			$fecha_rep=date('d/m/y H:i:s');
			print("<tr>
					<td colspan=6><h3>$serv_nombre</h3></td>
					<td coslpan=7><h3>$fecha_rep</h3></td></tr>
					<tr><td colspan=13></td></tr>");
		}
		
		print("<tr class='tabla_header'>
		<td>Cta. Corriente </td>
		<td>R.U.T.</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>Edad</td>
		<td>Servicio Ingreso</td>
		<td>Medico Tratante</td>
		<td>Fecha Ingreso</td>
		<td>Servicio / Sala</td>
		<td>Cama</td>
		<td>Dias Hosp.</td>
		</tr>");
		
		//<td>Edad</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($q[$i]['esp_desc']!='')
				$especialidad=$q[$i]['esp_desc'];
			else
				$especialidad='<i>(Sin Asignar...)</i>';

			if($q[$i]['tcama_tipo']!='')
				$servicio=$q[$i]['tcama_tipo'];
			else
				$servicio='<i>(Sin Asignar...)</i>';

			if($q[$i]['doc_rut']!='')
				$med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
			else
				$med_tratante='<i>(Sin Asignar...)</i>';
				
				$j=1;
                   
            //BUSQUEDA DE NUMERO DE CAMAS    
                for($n=$q[$i]['cama_num_ini']*1;$n<=$q[$i]['cama_num_fin']*1;$n++) {                                
                      if($q[$i]['hosp_numero_cama']*1==$n){
                             $nn=$j;
                      }
                            $j++;
                }
 
			
			print("<tr class='$clase'>

			<td style='text-align:right;'>".$q[$i]['hosp_id']."</td>
			<td style='text-align:right;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres'])."</td>
			<td style='text-align:center;'>".$q[$i]['edad_paciente']."</td>
			<td style='font-size:10px;'>".$servicio."</td>
			<td style='font-size:10px;'>".$med_tratante."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_ing']."</td>
			<td style='text-align:center;'><b>".$q[$i]['tcama_tipo'].'</b> <br /> '.$q[$i]['cama_tipo']."</td>
			<td style='text-align:center;font-weight:bold;font-size:16px;'>".($nn)."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['dias_espera']."</td>
			</tr>");

			//<td style='text-align:center;'>".$q[$i]['hest_nombre']."</td>
			
		}
		
	} elseif($tipo_informe==3 OR $tipo_informe==4 OR $tipo_informe==10 ) {
	
		if($serv_id!=0 AND ($tipo_informe==3 OR $tipo_informe==4 )) {
			$serv_w="t1.tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}	
		
		if($condicion!=0 AND ($tipo_informe==3 OR $tipo_informe==4 )) {
			$cond_w="hosp_condicion_egr=$condicion";
		} else {
			$cond_w='true';
		}	
		
		if($tipo_informe==4 OR $tipo_informe==10)
			$fecha_egr_w="hosp_fecha_egr::date >= '$fecha1' AND hosp_fecha_egr::date <= '$fecha2'";	 
		else
			$fecha_egr_w='true';
			
		$q=cargar_registros_obj("
		
			SELECT * FROM (
			SELECT *,
			upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			(hosp_fecha_egr::date-COALESCE(hosp_fecha_hospitalizacion, hosp_fecha_ing)::date) AS dias_espera,
			t1.tcama_tipo AS tcama_tipo, t1.tcama_num_ini AS tcama_num_ini,
			t2.tcama_tipo AS servicio,
			COALESCE(diag_desc, hosp_diagnostico) AS diag_desc,
			t1.tcama_tipo AS tcama_tipo_egr,
			func_nombre			
			
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN funcionario ON hosp_func_id2=func_id			
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_cama_egreso AND cama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_cama_egreso AND t1.tcama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t2 ON 
				t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
			WHERE hosp_fecha_egr IS NOT NULL AND $fecha_egr_w AND $esp_w AND $serv_w AND $doc_w 
			AND $filtro_w AND $cond_w AND $dias_w
			
			) AS foo ORDER BY dias_espera DESC	", true);
		
		print("<tr class='tabla_header'>
		<td>R.U.T.</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>(Sub)Especialidad</td>
		<td>Servicio Ingreso</td>
		<td>Medico Tratante</td>
		<td>Fecha Ingreso</td>
		<td>Fecha Egreso</td>
		<td>Servicio Egreso</td>
		<td>Destino</td>
		<td>Servicio / Sala</td>
		<td>Cama</td>
		<td style='width:100px;'>Diagn&oacute;stico</td>
		<td>Dias Hosp.</td>
		<td>Funcionario</td>
		</tr>");
		
		//<td>Estado</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($q[$i]['esp_desc']!='')
				$especialidad=$q[$i]['esp_desc'];
			else
				$especialidad='<i>(Sin Asignar...)</i>';

			if($q[$i]['servicio']!='')
				$servicio=$q[$i]['servicio'];
			else
				$servicio='<i>(Sin Asignar...)</i>';

			if($q[$i]['doc_rut']!='')
				$med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
			else
				$med_tratante='<i>(Sin Asignar...)</i>';
				
			$v=$q[$i]['hosp_condicion_egr']*1;
			
			switch($v) {
				
				case 0: $destino='(Sin Dato...)'; break;
				case 1: $destino='Alta a Domicilio'; break;
				case 2: $destino='Derivaci&oacute;n'; break;
				case 3: $destino='Fallecido'; break;
				case 4: $destino='Fugado'; break;
				case 5: $destino='Otro (<i>'.$q[$i]['hosp_otro_destino'].'</i>)'; break;
				
			}
			
			$j=1;
		
			for( $n=$q[$i]['cama_num_ini']*1;$n<=$q[$i]['cama_num_fin']*1;$n++) {
				 if($q[$i]['hosp_cama_egreso']*1==$n){
					$nn=$j;
				 }
				$j++;
			}
			
			print("<tr class='$clase'>
			<td style='text-align:right;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres'])."</td>
			<td style='font-size:10px;'>".$especialidad."</td>
			<td style='font-size:10px;'>".$servicio."</td>
			<td style='font-size:10px;'>".$med_tratante."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_ing']."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_egr']."</td>
			<td style='text-align:center;'>".$q[$i]['tcama_tipo_egr']."</td>
			<td style='text-align:center;'>".$destino."</td>
			<td style='text-align:center;'><b>".$q[$i]['tcama_tipo'].'</b> <br /> '.$q[$i]['cama_tipo']."</td>
			<td style='text-align:center;font-weight:bold;font-size:16px;'>".($nn)."</td>
			<td style='text-align:center;font-size:10px;'><b>".$q[$i]['hosp_diag_cod']."</b> ".$q[$i]['diag_desc']."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['dias_espera']."</td>
			<td style='text-align:center;'>".$q[$i]['func_nombre']."</td>
			</tr>");

			//<td style='text-align:center;'>".$q[$i]['hest_nombre']."</td>
			
		}
		
	} elseif($tipo_informe==5) {

		if($serv_id!=0) {
			$serv_w="t1.tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 
	
		
		
		$q=cargar_registros_obj("
		
			SELECT * FROM (
		
			SELECT *,
			
			upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			(COALESCE(hosp_fecha_egr, CURRENT_DATE)::date-COALESCE(hosp_fecha_hospitalizacion, hosp_fecha_ing)::date) AS dias_espera,
			t1.tcama_tipo AS tcama_tipo, t1.tcama_num_ini AS tcama_num_ini,
			t2.tcama_tipo AS servicio,
			(SELECT hospn_observacion 
			FROM hospitalizacion_necesidades AS h1
			WHERE h1.hosp_id=h0.hosp_id
			ORDER BY hospn_fecha DESC LIMIT 1) AS necesidades,
			(SELECT hcon_nombre
			FROM hospitalizacion_registro AS r1
			JOIN hospitalizacion_condicion USING (hcon_id)
			WHERE r1.hosp_id=h0.hosp_id
			ORDER BY hreg_fecha DESC LIMIT 1) AS condicion,
			COALESCE(hosp_cama_egreso, hosp_numero_cama) AS numero_cama
			
			FROM hospitalizacion AS h0
			JOIN pacientes ON hosp_pac_id=pac_id	
			
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod

			LEFT JOIN tipo_camas ON
				cama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND cama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND t1.tcama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)

			LEFT JOIN clasifica_camas AS t2 ON 
				t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
			
			WHERE $esp_w AND $serv_w AND $doc_w AND $filtro_w AND $dias_w
			
			) AS foo ORDER BY dias_espera DESC
			
		", true);
		
		print("<tr class='tabla_header'>		
		<td>R.U.T.</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<!--<td>(Sub)Especialidad</td>-->
		<td>Servicio Ingreso</td>
		<td>Medico Tratante</td>
		<td>Fecha Ingreso</td>
		<td>Fecha Egreso</td>
		<td>Servicio / Sala</td>
		<td>Cama</td>
		<td style='width:150px;'>Necesidades</td>
		<td>Condici&oacute;n</td>
		<td>Dias Hosp.</td>
		</tr>");
		
		//<td>Estado</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($q[$i]['condicion']!='')
				$condicion=$q[$i]['condicion'];
			else
				$condicion='<i>(Sin Asignar...)</i>';			
			
			if($q[$i]['esp_desc']!='')
				$especialidad=$q[$i]['esp_desc'];
			else
				$especialidad='<i>(Sin Asignar...)</i>';

			if($q[$i]['servicio']!='')
				$servicio=$q[$i]['servicio'];
			else
				$servicio='<i>(Sin Asignar...)</i>';

			if($q[$i]['doc_rut']!='')
				$med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
			else
				$med_tratante='<i>(Sin Asignar...)</i>';
				
			if($q[$i]['hosp_fecha_egr']!='') 
				$egreso=$q[$i]['hosp_fecha_egr'];
			else
				$egreso='<i>(Sin Egreso...)</i>';
				
			$j=1;
		
			for( $n=$q[$i]['cama_num_ini']*1;$n<=$q[$i]['cama_num_fin']*1;$n++) {
				 if($q[$i]['hosp_cama_egreso']*1==$n OR $q[$i]['hosp_numero_cama']*1==$n){
					$nn=$j;
				 }
				$j++;
			}
				
			print("<tr class='$clase'>			
			<td style='text-align:right;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres'])."</td>
			<!--<td style='font-size:10px;'>".$especialidad."</td>-->
			<td style='font-size:10px;'>".$servicio."</td>
			<td style='font-size:10px;'>".$med_tratante."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_ing']."</td>
			<td style='text-align:center;'>".$egreso."</td>
			<td style='text-align:center;'><b>".$q[$i]['tcama_tipo'].'</b> <br /> '.$q[$i]['cama_tipo']."</td>
			<td style='text-align:center;font-weight:bold;font-size:16px;'>".($nn)."</td>
			<td style='text-align:justify;font-size:10px;'>".$q[$i]['necesidades']."</td>
			<td style='text-align:center;'>".$condicion."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['dias_espera']."</td>
			</tr>");

			//<td style='text-align:center;'>".$q[$i]['hest_nombre']."</td>
			
		}
		
	} elseif($tipo_informe==6) {

		if($serv_id!=0) {
			$serv_w="tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 

		if($tipo_camas==1) {
			$tcamas_w="NOT tcama_ambulatorio";
		} elseif($tipo_camas==2) {
			$tcamas_w="tcama_ambulatorio";
		} else {
			$tcamas_w='true';
		}		 

	
		$q=cargar_registros_obj("
		
			SELECT * FROM clasifica_camas 
			
			LEFT JOIN tipo_camas ON
				cama_num_ini>=tcama_num_ini AND cama_num_fin<=tcama_num_fin
			WHERE $serv_w AND $tcamas_w
			ORDER BY tcama_tipo, cama_num_ini
			
		", true);
		
		$q2=pg_query("
			SELECT hosp_numero_cama FROM hospitalizacion 
			JOIN pacientes ON hosp_pac_id=pac_id
			WHERE hosp_numero_cama>0 AND hosp_fecha_egr IS NULL;
		");
		
		$hvig=array();
		
		while($r=pg_fetch_assoc($q2)) {
			$hvig[]=$r['hosp_numero_cama']*1;
		}

		$q3=pg_query("
			SELECT bloq_numero_cama FROM bloqueo_camas 
			WHERE (
				bloq_fecha_ini<=CURRENT_DATE AND 
				(
					bloq_fecha_fin IS NULL OR 
					bloq_fecha_fin>=CURRENT_DATE
				)
			);
		");
		
		$bloq=array();
		
		while($r=pg_fetch_assoc($q3)) {
			$bloq[]=$r['bloq_numero_cama']*1;
		}
		
		print("
		<tr class='tabla_header'>
		<td style='width:35%;'>Servicio</td>
		<td style='width:35%;'>Sala</td>
		<td style='width:15%;'>Cama</td>
		<td>Estado</td>
		</tr>");
		
		//<td>Estado</td>
		
		$c=0;
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			$n=1;
			for($j=$q[$i]['cama_num_ini']*1;$j<=$q[$i]['cama_num_fin']*1;$j++) {
			
				if(!in_array($j, $hvig)) {
					
					$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
					
					if(!in_array($j, $bloq))
						$estado='Libre';
					else
						$estado='Bloqueada';
						
														
					print("
					
						<tr class='$clase'>
						<td style='text-align:center;font-weight:bold;'>".$q[$i]['tcama_tipo']."</td>
						<td style='text-align:center;'>".$q[$i]['cama_tipo']."</td>
						<td style='text-align:center;font-weight:bold;font-size:16px;'>".($n)."</td>
						<td>".$estado."</td>
						</tr>
					
					");
					
					$c++;
				
				}
				$n++;
			}
			
		}
		
	} elseif($tipo_informe==7 OR $tipo_informe==8) {
		
		if($serv_id!=0) {
			$serv_w="t1.tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 
	
		$q=pg_query("
		SELECT tcama_tipo, censo_diario, censo_fecha::date AS fecha
		FROM censo_diario 
		JOIN clasifica_camas AS t1 ON 
		t1.tcama_num_ini<=censo_numero_cama AND t1.tcama_num_fin>=censo_numero_cama
		WHERE censo_fecha::date BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' AND 
		censo_fecha::time='11:00:00' AND $serv_w
		ORDER BY ".(($tipo_informe==7)?'tcama_tipo':'censo_fecha::date')."
		");
		
		print("<table style='width:100%;'>
				   <tr class='tabla_header'>
					   <td>".($tipo_informe==7?'Servicio':'Fecha')."</td>
					   <td>A1</td><td>A2</td><td>A3</td>
					   <td>B1</td><td>B2</td><td>B3</td>
					   <td>C1</td><td>C2</td><td>C3</td>
					   <td>D1</td><td>D2</td><td>D3</td>
					   <td>Subtotal</td>
				   </tr>");
		
		$cnt=array();

		$totales=array();
		
		$ttotal=array();
		
		$ttotal_general=0;
				
		while($r=pg_fetch_assoc($q)) {

			if($tipo_informe==7) {
				
				if(!isset($cnt[$r['tcama_tipo']][$r['censo_diario']])) {
					$cnt[$r['tcama_tipo']][$r['censo_diario']]=0;
				}
			
				$cnt[$r['tcama_tipo']][$r['censo_diario']]++;
			
			} else {
				
				if(!isset($cnt[$r['fecha']][$r['censo_diario']])) {
					$cnt[$r['fecha']][$r['censo_diario']]=0;
				}
			
				$cnt[$r['fecha']][$r['censo_diario']]++;
			
			}
			
			
			if(!isset($totales[$r['censo_diario']]))
				$totales[$r['censo_diario']]=0;
			
			$totales[$r['censo_diario']]++;
			
		}	
		
		$c=0;
	
		foreach($cnt AS $key=>$val) {
			
			$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
			
			$ttotal[$key]=$val['A1']+$val['A2']+$val['A3']+
							$val['B1']+$val['B2']+$val['B3']+
							$val['C1']+$val['C2']+$val['C3']+
							$val['D1']+$val['D2']+$val['D3'];
							
			$ttotal_general+=$ttotal[$key];
			
			print("
			<tr class='$clase'>
			<td style='font-weight:bold;'>".htmlentities($key)."</td>

			<td style='text-align:center;'>".$val['A1']."</td>
			<td style='text-align:center;'>".$val['A2']."</td>
			<td style='text-align:center;'>".$val['A3']."</td>

			<td style='text-align:center;'>".$val['B1']."</td>
			<td style='text-align:center;'>".$val['B2']."</td>
			<td style='text-align:center;'>".$val['B3']."</td>

			<td style='text-align:center;'>".$val['C1']."</td>
			<td style='text-align:center;'>".$val['C2']."</td>
			<td style='text-align:center;'>".$val['C3']."</td>

			<td style='text-align:center;'>".$val['D1']."</td>
			<td style='text-align:center;'>".$val['D2']."</td>
			<td style='text-align:center;'>".$val['D3']."</td>

			<td style='text-align:center;font-weight:bold;'>".$ttotal[$key]."</td>

			</tr>
			");
			
			$c++;
			
		}

		print("

			<tr class='tabla_header'>
			<td style='font-weight:bold;'>Total</td>

			<td style='text-align:center;'>".($totales['A1']*1)."</td>
			<td style='text-align:center;'>".($totales['A2']*1)."</td>
			<td style='text-align:center;'>".($totales['A3']*1)."</td>

			<td style='text-align:center;'>".($totales['B1']*1)."</td>
			<td style='text-align:center;'>".($totales['B2']*1)."</td>
			<td style='text-align:center;'>".($totales['B3']*1)."</td>

			<td style='text-align:center;'>".($totales['C1']*1)."</td>
			<td style='text-align:center;'>".($totales['C2']*1)."</td>
			<td style='text-align:center;'>".($totales['C3']*1)."</td>

			<td style='text-align:center;'>".($totales['D1']*1)."</td>
			<td style='text-align:center;'>".($totales['D2']*1)."</td>
			<td style='text-align:center;'>".($totales['D3']*1)."</td>

			<td style='text-align:center;font-weight:bold;'>".$ttotal_general."</td>

			</tr>

			<tr class='tabla_header'>
			<td style='font-weight:bold;text-align:center;'>%</td>

			<td style='text-align:center;'>".number_format($totales['A1']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['A2']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['A3']*100/$ttotal_general,1,',','.')."%</td>

			<td style='text-align:center;'>".number_format($totales['B1']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['B2']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['B3']*100/$ttotal_general,1,',','.')."%</td>

			<td style='text-align:center;'>".number_format($totales['C1']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['C2']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['C3']*100/$ttotal_general,1,',','.')."%</td>

			<td style='text-align:center;'>".number_format($totales['D1']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['D2']*100/$ttotal_general,1,',','.')."%</td>
			<td style='text-align:center;'>".number_format($totales['D3']*100/$ttotal_general,1,',','.')."%</td>

			<td style='text-align:center;'>100,0%</td>

			</tr>

		");
		
		print("</table>");
		
	} elseif($tipo_informe==9) {
		
		$q=pg_query("
		
		SELECT * FROM (SELECT *, 
		extract(epoch from (hosp_fecha_hospitalizacion-hosp_fecha_ing)) AS delta,
		hosp_fecha_hospitalizacion::date AS hosp_fecha_traslado, 
		hosp_fecha_hospitalizacion::time AS hosp_hora_traslado
		
		FROM (
		SELECT *,
		
		(CASE WHEN 
		
		(hosp_cama_origen BETWEEN 84 AND 94) OR (hosp_cama_origen BETWEEN 111 AND 118)
		OR ((NOT hosp_cama_origen BETWEEN 84 AND 123) AND (NOT hosp_cama_origen BETWEEN 505 AND 511))
		
		THEN
		
		hosp_fecha_hospitalizacion3
		
		WHEN 
		
		(hosp_cama_destino BETWEEN 84 AND 94) OR (hosp_cama_destino BETWEEN 111 AND 118)
		

		THEN
		hosp_fecha_hospitalizacion2
		
		ELSE
		
		hosp_fecha_hospitalizacion1
		
		END) AS hosp_fecha_hospitalizacion
		
		FROM (
		
		SELECT pac_rut, 
		upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
		hosp_fecha_ing::date AS hosp_fecha_ingreso, 
		hosp_fecha_ing::time AS hosp_hora_ingreso,
		hosp_fecha_ing,		 
		
		(SELECT ptras_fecha FROM paciente_traslado AS pt WHERE pt.hosp_id=h1.hosp_id 
		AND (NOT ptras_cama_destino BETWEEN 84 AND 123) 
		AND (NOT ptras_cama_destino BETWEEN 505 AND 511) 
		ORDER BY ptras_fecha LIMIT 1) 
		AS hosp_fecha_hospitalizacion1,
		(SELECT ptras_fecha FROM paciente_traslado AS pt2 WHERE pt2.hosp_id=h1.hosp_id 
		AND ((ptras_cama_destino BETWEEN 84 AND 94) 
		OR(ptras_cama_destino BETWEEN 111 AND 118) )
		ORDER BY ptras_fecha LIMIT 1) 
		AS hosp_fecha_hospitalizacion2,

		hosp_fecha_hospitalizacion AS hosp_fecha_hospitalizacion3,
		
		(SELECT ptras_cama_origen FROM paciente_traslado AS pt WHERE pt.hosp_id=h1.hosp_id 
		ORDER BY ptras_fecha LIMIT 1) 
		AS hosp_cama_origen,
		(SELECT ptras_cama_destino FROM paciente_traslado AS pt WHERE pt.hosp_id=h1.hosp_id 
		ORDER BY ptras_fecha LIMIT 1) 
		AS hosp_cama_destino,
		
		hosp_procedencia
		FROM hospitalizacion AS h1
		JOIN pacientes ON hosp_pac_id=pac_id	
		WHERE 
		hosp_fecha_ing::date BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' 
		AND hosp_procedencia IN (0,1)
		ORDER BY hosp_fecha_ing
		
		) AS fooo2
		
		
		
		
		) AS foo WHERE hosp_fecha_hospitalizacion IS NOT NULL) AS foo2
		
		WHERE delta>=0;
		");
		
		$html="<table style='width:100%;'>
				   <tr class='tabla_header'>
					   <td>RUT</td>
					   <td>Nombre</td>
					   <td>Procedencia</td>
					   <td>Fecha Ingreso</td>
					   <td>Hora Ingreso</td>
					   <td>Fecha Traslado</td>
					   <td>Hora Traslado</td>
					   <td>Tiempo Transcurrido (Hrs.)</td>
				   </tr>";
		
				
		$total=0;
		$menor12=0;		
				
		while($r=pg_fetch_assoc($q)) {

			$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
			
			$procede=$r['hosp_procedencia'];
			
			$horas=floor($r['delta']/3600);
			
			if($horas<12) $menor12++;
			
			$html.="
			<tr class='$clase'>
			<td style='font-weight:bold;text-align:right;'>".$r['pac_rut']."</td>
			<td style=''>".htmlentities($r['pac_nombres']." ".$r['pac_appat']." ".$r['pac_apmat'])."</td>
			<td style='text-align:center;'>".$procede."</td>
			<td style='text-align:center;'>".$r['hosp_fecha_ingreso']."</td>
			<td style='text-align:center;'>".substr($r['hosp_hora_ingreso'],0,5)."</td>
			<td style='text-align:center;'>".$r['hosp_fecha_traslado']."</td>
			<td style='text-align:center;'>".substr($r['hosp_hora_traslado'],0,5)."</td>
			<td style='text-align:center;'>".number_format($r['delta']/3600,0,',','.')."</td>
			</tr>
			";
			
			$total++;
			
		}

		
		$html.="</table>";
		
		if($total>0)
			print("
			<table style='width:100%;'>
					   <tr class='tabla_header'>
					   <td><h1>".$menor12." / ".$total." = ".number_format($menor12*100/$total,2,',','.')."%</h1></td>
					   </tr>
			</table>
			");
		else
			print("
			<table style='width:100%;'>
					   <tr class='tabla_header'>
					   <td><h1>0 / 0 = 0%</h1></td>
					   </tr>
			</table>
			");
		
		print($html);
		
		
		
	} elseif($tipo_informe==11) {
		
		$datos=cargar_registro("
		
			SELECT COUNT(*) AS total, SUM(CASE WHEN hosp_condicion_egr=3 THEN 1 ELSE 0 END) AS fallecidos 
			FROM hospitalizacion 
			WHERE hosp_fecha_egr BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59'

		");
		
		$fallecidos=$datos['fallecidos']*1;
		$total=$datos['total']*1;
		
		print("
			<table style='width:100%;'>
					   <tr class='tabla_header'>
					   <td>
					   <h1><u>Letalidad Hospitalaria Periodo $fecha1 - $fecha2</u></h1><br />
					   <h2>Fallecidos: <u>".$fallecidos."</u> <b>/</b> Total Egresos: <u>".$total."</u> = <b><u>".number_format($fallecidos*100/$total,2,',','.')."%</u></b></h2></td>
					   </tr>
			</table>
		");
		
		
	} elseif($tipo_informe==12) {
		
		$datos=cargar_registro("
		
			SELECT avg((hosp_fecha_egr::date-hosp_fecha_hospitalizacion::date)) AS dias_estada

			FROM hospitalizacion 

			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_cama_egreso AND cama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_cama_egreso AND t1.tcama_num_fin>=hosp_cama_egreso

			WHERE hosp_fecha_egr::date BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' AND $serv_w


		");
		
		$dias_estada=$datos['dias_estada']*1;
		
		print("
			<table style='width:100%;'>
					   <tr class='tabla_header'>
					   <td>
					   <h1><u>Promedio D&iacute;as de Estada Periodo $fecha1 - $fecha2</u></h1><br />
					   <font size='24px'><b>".number_format($dias_estada,2,',','.')."</b></font></td>
					   </tr>
			</table>
		");
		
		
	} elseif($tipo_informe==13) {

		if($serv_id!=0) {
			$serv_w="tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 

		
		$dias_total=cargar_registro("
			SELECT (('$fecha2'::date-'$fecha1'::date)+1) AS dias_total;
		");
		
		$dias_total=$dias_total['dias_total']*1;
	
		$camas_total=cargar_registro("
			SELECT SUM(((tcama_num_fin-tcama_num_ini)+1)) AS nro_camas FROM clasifica_camas 
			WHERE NOT tcama_ambulatorio AND $serv_w;
		");

		$camas_total=$camas_total['nro_camas']*1;
		
		$camas_ocupadas=cargar_registro("
		
			SELECT count(*) AS ocupadas FROM censo_diario 
			
			JOIN clasifica_camas ON NOT tcama_ambulatorio AND 
									tcama_num_ini<=censo_numero_cama AND 
									tcama_num_fin>=censo_numero_cama
									
			WHERE censo_fecha::date BETWEEN '$fecha1' AND '$fecha2' AND censo_fecha::time='09:00:00' AND $serv_w

		");
	
		$camas_ocupadas=$camas_ocupadas['ocupadas']*1;
		
		
		print("
			<table style='width:100%;'>
					   <tr class='tabla_header'>
					   <td>
					   <h1><u>&Iacute;ndice Ocupacional Periodo $fecha1 - $fecha2</u></h1><br />
					   </td>
					   </tr>
			</table>
					   <center>
					   <table style='width:60%;font-size:16px;'>
					   <tr><td style='text-align:right;width:50%;'>Numero de Camas:</td><td style='text-align:right;font-size:18px;'>".number_format($camas_total,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Numero de D&iacute;as Cama:</td><td style='text-align:right;font-size:18px;'>".number_format($dias_total*$camas_total,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Numero de D&iacute;as Cama Utilizados:</td><td style='text-align:right;font-size:18px;'>".number_format($camas_ocupadas,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Indice Ocupacional:</td><td style='text-align:right;font-size:18px;'><b>".number_format($camas_ocupadas*100/($dias_total*$camas_total),2,',','.')."%</b></td></tr>
					   </table>
					   </center>
						
					   
		");
		
		
	} elseif($tipo_informe==14) {

		if($serv_id!=0) {
			$serv_w="tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 

		
		$dias_total=cargar_registro("
			SELECT (('$fecha2'::date-'$fecha1'::date)+1) AS dias_total;
		");
		
		$dias_total=$dias_total['dias_total']*1;
	
		$camas_total=cargar_registro("
			SELECT SUM(((tcama_num_fin-tcama_num_ini)+1)) AS nro_camas FROM clasifica_camas 
			WHERE NOT tcama_ambulatorio AND $serv_w;
		");

		$camas_total=$camas_total['nro_camas']*1;
		
		$egresos=cargar_registro("
		
			SELECT count(*) AS cantidad FROM hospitalizacion 

			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_cama_egreso AND cama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_cama_egreso AND t1.tcama_num_fin>=hosp_cama_egreso

			WHERE hosp_fecha_egr BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' AND $serv_w

		");
	
		$egresos=$egresos['cantidad']*1;
		
		
		print("
			<table style='width:100%;'>
					   <tr class='tabla_header'>
					   <td>
					   <h1><u>&Iacute;ndice de Rotaci&oacute;n Periodo $fecha1 - $fecha2</u></h1><br />
					   </td>
					   </tr>
			</table>
					  <center>
					   <table style='width:60%;font-size:16px;'>
					   <tr><td style='text-align:right;width:50%;'>Camas de Dotaci&oacute;n:</td><td style='text-align:right;font-size:18px;'>".number_format($camas_total,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Egresos del Periodo:</td><td style='text-align:right;font-size:18px;'>".number_format($egresos,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Indice de Rotaci&oacute;n:</td><td style='text-align:right;font-size:18px;'><b>".number_format($egresos/$camas_total,2,',','.')."</b></td></tr>
					   </table>
					  </center>
						
					   
		");
		
		
	} elseif($tipo_informe==15) {

		if($serv_id!=0) {
			$serv_w="tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 

		
		$dias_total=cargar_registro("
			SELECT (('$fecha2'::date-'$fecha1'::date)+1) AS dias_total;
		");
		
		$dias_total=$dias_total['dias_total']*1;
	
		$camas_total=cargar_registro("
			SELECT SUM(((tcama_num_fin-tcama_num_ini)+1)) AS nro_camas FROM clasifica_camas 
			WHERE NOT tcama_ambulatorio AND $serv_w;
		");

		$camas_total=$camas_total['nro_camas']*1;
		
		$camas_ocupadas=cargar_registro("
		
			SELECT count(*) AS ocupadas FROM censo_diario 
			
			JOIN clasifica_camas ON NOT tcama_ambulatorio AND 
									tcama_num_ini<=censo_numero_cama AND 
									tcama_num_fin>=censo_numero_cama
									
			WHERE censo_fecha::date BETWEEN '$fecha1' AND '$fecha2' AND censo_fecha::time='09:00:00' AND $serv_w

		");
	
		$camas_ocupadas=$camas_ocupadas['ocupadas']*1;

		$egresos=cargar_registro("
		
			SELECT count(*) AS cantidad FROM hospitalizacion 

			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_cama_egreso AND cama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_cama_egreso AND t1.tcama_num_fin>=hosp_cama_egreso

			WHERE hosp_fecha_egr BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' AND $serv_w

		");
	
		$egresos=$egresos['cantidad']*1;
		
		
		print("
			<table style='width:100%;'>
					   <tr class='tabla_header'>
					   <td>
					   <h1><u>Intervalo de Sustituci&oacute;n Periodo $fecha1 - $fecha2</u></h1><br />
					   </td>
					   </tr>
			</table>
					   <center>
					   <table style='width:60%;font-size:16px;'>
					   <tr><td style='text-align:right;width:50%;'>Numero de Camas:</td><td style='text-align:right;font-size:18px;'>".number_format($camas_total,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Numero de D&iacute;as Cama Disponibles:</td><td style='text-align:right;font-size:18px;'>".number_format($dias_total*$camas_total,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Numero de D&iacute;as Cama Utilizados:</td><td style='text-align:right;font-size:18px;'>".number_format($camas_ocupadas,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Numero de Egresos:</td><td style='text-align:right;font-size:18px;'>".number_format($egresos,0,',','.')."</td></tr>
					   <tr><td style='text-align:right;'>Intervalo de Sustituci&oacute;n:</td><td style='text-align:right;font-size:18px;'><b>".number_format((($dias_total*$camas_total)-$camas_ocupadas)/$egresos,2,',','.')."</b></td></tr>
					   </table>
					   </center>
						
					   
		");
		
		
	} elseif($tipo_informe==16) {
		
			$procedencia=$_POST['procedencia']*1;
			
			if($procedencia!=-1) {
				$procedencia_w='hosp_procedencia='.$procedencia;
			} else {
				$procedencia_w='true';
			}
			
			$q=cargar_registros_obj("
		
			SELECT * FROM (
		
			SELECT *,
			
			upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
			hosp_fecha_hospitalizacion::date AS hosp_fecha_hosp,
			hosp_fecha_hospitalizacion::time AS hosp_hora_hosp,
			hosp_fecha_egr::date,
			(CURRENT_DATE-hosp_fecha_ing::date) AS dias_espera
			
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id

			LEFT JOIN tipo_camas ON
				cama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND cama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=COALESCE(hosp_cama_egreso, hosp_numero_cama) AND t1.tcama_num_fin>=COALESCE(hosp_cama_egreso, hosp_numero_cama)

			
			WHERE 
			hosp_fecha_hospitalizacion BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59'
			AND $procedencia_w AND $serv_w
			
			) AS foo ORDER BY dias_espera DESC
			
		", true);

/*
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama
*/
		
		print("<tr class='tabla_header'>
		<td>Fecha Ing.</td>
		<td>Hora Ing.</td>
		<td>R.U.T.</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>(Sub)Especialidad</td>
		<td>Servicio Ingreso</td>
		<td>Medico Tratante</td>
		<td>Procedencia</td>
		<td>Diagn&oacute;stico</td>
		<td>Dias Espera</td>
		<td>Edici&oacute;n</td>
		</tr>");
		
		//<td>Estado</td>
		//<td>Servicio / Sala</td>
		//<td>Cama</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($q[$i]['esp_desc']!='')
				$especialidad=$q[$i]['esp_desc'];
			else
				$especialidad='<i>(Sin Asignar...)</i>';

			if($q[$i]['tcama_tipo']!='')
				$servicio=$q[$i]['tcama_tipo'];
			else
				$servicio='<i>(Sin Asignar...)</i>';

			if($q[$i]['doc_rut']!='')
				$med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
			else
				$med_tratante='<i>(Sin Asignar...)</i>';
				
			switch($q[$i]['hosp_procedencia']*1) {
				case 0: $procedencia="UEA"; break;
				case 1: $procedencia="UEI"; break;
				case 2: $procedencia="UEGO"; break;
				case 4: $procedencia="Obst. y Gine."; break;
				case 5: $procedencia="Hospitalizaci&oacute;n"; break;
				case 6: $procedencia="At. Ambulatoria"; break;
				case 3: $procedencia="Otro Hospital"; break;
			}

			
			print("<tr class='$clase'>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_hosp']."</td>
			<td style='text-align:center;'>".substr($q[$i]['hosp_hora_hosp'],0,5)."</td>
			<td style='text-align:right;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres'])."</td>
			<td style='font-size:10px;'>".$especialidad."</td>
			<td style='font-size:10px;'>".$servicio."</td>
			<td style='font-size:10px;'>".$med_tratante."</td>
			<td style='font-size:10px;'>".$procedencia."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['hosp_diag_cod']."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['dias_espera']."</td>
			<td><center>
			<img src='iconos/script_edit.png' style='cursor:pointer;'
			onClick='completa_info(".$q[$i]['hosp_id'].");' />
			</center></td>
			</tr>");

			//<td style='text-align:center;'>".$q[$i]['hest_nombre']."</td>
			//<td style='text-align:center;'><b>".$q[$i]['tcama_tipo'].'</b> <br /> '.$q[$i]['cama_tipo']."</td>
			//<td style='text-align:center;'>".(($q[$i]['hosp_numero_cama']*1-$q[$i]['tcama_num_ini']*1)+1)."</td>
			
		}

		
		
	} elseif($tipo_informe==17) {
		
		if($serv_id!=0) {
			$serv_w="tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 

		if($tipo_camas==1) {
			$tcamas_w="NOT tcama_ambulatorio";
		} elseif($tipo_camas==2) {
			$tcamas_w="tcama_ambulatorio";
		} else {
			$tcamas_w='true';
		}		 

	
		$q=cargar_registros_obj("
		
			SELECT * FROM clasifica_camas 
			LEFT JOIN tipo_camas ON
				cama_num_ini>=tcama_num_ini AND cama_num_fin<=tcama_num_fin
			WHERE $serv_w AND $tcamas_w
			ORDER BY tcama_tipo, cama_num_ini
			
		", true);
		
		$q2=pg_query("
			SELECT censo_numero_cama FROM censo_diario
			WHERE censo_fecha::date='$fecha1';
		");
		
		$hvig=array();
		
		while($r=pg_fetch_assoc($q2)) {
			$hvig[]=$r['censo_numero_cama']*1;
		}

		$q3=pg_query("
			SELECT bloq_numero_cama FROM bloqueo_camas 
			WHERE (
				bloq_fecha_ini<='$fecha1' AND 
				(
					bloq_fecha_fin IS NULL OR 
					bloq_fecha_fin>='$fecha1'
				)
			);
		");
		
		$bloq=array();
		
		while($r=pg_fetch_assoc($q3)) {
			$bloq[]=$r['bloq_numero_cama']*1;
		}
		
		print("
		<tr class='tabla_header'>
		<td style='width:30%;'>Servicio</td>
		<td style='width:30%;'>Sala</td>
		<td style='width:15%;'>Cama</td>
		<td>Estado</td>
		<td>Fecha &Uacute;ltimo Uso</td>
		<td>Cant. D&iacute;as</td>
		</tr>");
		
		//<td>Estado</td>
		
		$c=0;
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			$n=1;
		
			for($j=$q[$i]['cama_num_ini']*1;$j<=$q[$i]['cama_num_fin']*1;$j++) {
			
				if(!in_array($j, $hvig)) {
					
					$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
					
					if(!in_array($j, $bloq))
						$estado='Libre';
					else
						$estado='Bloqueada';
					
					$fec=cargar_registro("SELECT max(censo_fecha)::date AS max_fecha, ('$fecha1'::date-max(censo_fecha)::date) AS cant_dias FROM censo_diario WHERE censo_numero_cama=$j AND censo_fecha<'$fecha1'");
					
					$ultimafecha=$fec['max_fecha'];
					$cant_dias=$fec['cant_dias']*1;
					
					print("
					
						<tr class='$clase'>
						<td style='text-align:center;font-weight:bold;'>".$q[$i]['tcama_tipo']."</td>
						<td style='text-align:center;'>".$q[$i]['cama_tipo']."</td>
						<td style='text-align:center;font-weight:bold;font-size:16px;'>".$n."</td>
						<td>".$estado."</td>
						<td style='text-align:center;'><b>".$ultimafecha."</b></td>
						<td style='text-align:right;'><b>".$cant_dias."</b></td>
						</tr>
					
					");
					
					$c++;
				
				}
				$n++;
			}
			
		}

		
		
	}else if($tipo_informe==18){
	
		$fecha1 = $_POST['fecha1'];
		$fecha2 = $_POST['fecha2'];
	
		$q=cargar_registros_obj("select hosp_id, hosp_fecha_ing::date as fecha, hosp_fecha_ing::time as hora, pac_rut, pac_nombres, pac_appat, pac_apmat, tcama_tipo, prev_desc, func_nombre,
								hosp_fecha_egr, hosp_anulado, hosp_dau
								from hospitalizacion 
								JOIN pacientes ON hosp_pac_id = pac_id
								JOIN clasifica_camas ON hosp_servicio = tcama_id
								LEFT JOIN prevision using (prev_id)
								LEFT JOIN funcionario ON hosp_func_id=func_id
								WHERE (hosp_fecha_ing>='$fecha1 00:00:00' AND hosp_fecha_ing<='$fecha2 23:59:59')
								ORDER BY hosp_fecha_ing DESC");
								
		print("<tr class='tabla_header'>
		<td>Fecha Ing.</td>
		<td>Hora Ing.</td>
		<td>Cta. Corriente</td>
		<td>R.U.T</td>
		<td>Nombre</td>
		<td>Servicio Ingreso</td>
		<td>DAU</td>
		<td>Previsi&oacute;n</td>
		<td>Funcionario (Ingreso)</td>
		<td>Estado</td>
		</tr>");
		
		//<td>Estado</td>
		//<td>Servicio / Sala</td>
		//<td>Cama</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			$estado_hosp='';
			
			if($q[$i]['hosp_anulado']*1==1){
				$estado_hosp="<font color='red'>ANULADA</font>";
			}elseif($q[$i]['hosp_fecha_egr']==''){
				$estado_hosp="<font color='green'>ABIERTA</font>";
			}elseif($q[$i]['hosp_fecha_egr']!=''){
				$estado_hosp="<font color='blue'>CERRADA</font>";
			}
						
			print("<tr class='$clase'>
			<td style='text-align:center;'>".$q[$i]['fecha']."</td>
			<td style='text-align:center;'>".substr($q[$i]['hora'],0,5)."</td>
			<td style='text-align:right;'>".$q[$i]['hosp_id']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_rut']."</td>
			<td style='font-size:10px;'>".htmlentities(($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres']))."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['tcama_tipo'])."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['hosp_dau'])."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['prev_desc'])."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['func_nombre'])."</td>
			<td style='font-size:10px; text-align:center; font-weight:bold;'>$estado_hosp</td>
			</tr>");
		}
			
	}else if($tipo_informe==19){
		
		$fecha1 = $_POST['fecha1'];
		$fecha2 = $_POST['fecha2'];
	
		$q=cargar_registros_obj("select hosp_id, hosp_fecha_ing::date as fecha, hosp_fecha_ing::time as hora, pac_rut, pac_nombres, pac_appat, pac_apmat, tcama_tipo, prev_desc, func_nombre, pac_appat, pac_apmat, tcama_tipo, prev_desc, func_nombre,
								hosp_fecha_egr, hosp_anulado, hosp_dau
								from hospitalizacion 
								JOIN pacientes ON hosp_pac_id = pac_id
								JOIN clasifica_camas ON hosp_servicio = tcama_id
								LEFT JOIN prevision USING(prev_id)
								LEFT JOIN funcionario ON hosp_func_id=func_id
								WHERE (hosp_fecha_ing>='$fecha1 00:00:00' AND hosp_fecha_ing<='$fecha2 23:59:59')
								AND $filtro_w
								ORDER BY hosp_fecha_ing");
								
		print("<tr class='tabla_header'>
		<td>Fecha Ing.</td>
		<td>Hora Ing.</td>
		<td>Cta. Corriente</td>
		<td>R.U.T</td>
		<td>Nombre</td>
		<td>Servicio Ingreso</td>
		<td>DAU</td>
		<td>Previsi&oacute;n</td>
		<td>Funcionario (Ingreso)</td>
		<td>Estado</td>
		</tr>");
		
		//<td>Estado</td>
		//<td>Servicio / Sala</td>
		//<td>Cama</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			$estado_hosp='';
			
			if($q[$i]['hosp_anulado']*1==1){
				$estado_hosp="<font color='red'>ANULADA</font>";
			}elseif($q[$i]['hosp_fecha_egr']==''){
				$estado_hosp="<font color='green'>ABIERTA</font>";
			}elseif($q[$i]['hosp_fecha_egr']!=''){
				$estado_hosp="<font color='blue'>CERRADA</font>";
			}
			
			print("<tr class='$clase'>
			<td style='text-align:center;'>".$q[$i]['fecha']."</td>
			<td style='text-align:center;'>".substr($q[$i]['hora'],0,5)."</td>
			<td style='text-align:right;'>".$q[$i]['hosp_id']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_rut']."</td>
			<td style='font-size:10px;'>".htmlentities(($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres']))."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['tcama_tipo'])."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['hosp_dau'])."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['prev_desc'])."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['func_nombre'])."</td>
			<td style='font-size:10px; text-align:center; font-weight:bold;'>$estado_hosp</td>
			</tr>");
		}
	}else if($tipo_informe==20){
		
		
		$cama = $_POST['cama_id']*1;
	
	
		$q=cargar_registros_obj("SELECT hosp_id, pac_id, pac_ficha, pac_rut, pac_nombres ||' '|| pac_appat ||' '|| pac_apmat as nombres,
							COALESCE(hosp_cama_egreso,hosp_numero_cama) as numero_cama, COALESCE(t1.cama_tipo,t2.cama_tipo) as cama_tipo, hosp_fecha_ing, hosp_fecha_egr,
							COALESCE(t1.cama_num_ini,t2.cama_num_ini) as cama_inicio, COALESCE(t1.cama_num_fin,t2.cama_num_fin) as cama_fin
							FROM hospitalizacion
							LEFT JOIN tipo_camas as t1 ON t1.cama_num_ini<=hosp_numero_cama AND t1.cama_num_fin>=hosp_numero_cama
							LEFT JOIN tipo_camas as t2 ON t2.cama_num_ini<=hosp_cama_egreso AND t2.cama_num_fin>=hosp_cama_egreso
							JOIN pacientes ON hosp_pac_id=pac_id
							WHERE hosp_numero_cama=$cama or hosp_cama_egreso=$cama
							ORDER BY hosp_id");
								
		print("<tr class='tabla_header'>
		<td>Cta.Corriente</td>
		<td>Ficha Paciente</td>
		<td>R.U.T </td>
		<td>Nombre</td>
		<td>Fecha Ingreso</td>
		<td>Fecha Egreso</td>
		<td>N&uacute;mero de cama</td>
		<td>Sala</td>
		</tr>");
		
		//<td>Estado</td>
		//<td>Servicio / Sala</td>
		//<td>Cama</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			$j=1;
			
			for($n=$q[$i]['cama_inicio']*1;$n<=$q[$i]['cama_fin']*1;$n++) {				
				if($q[$i]['numero_cama']*1==$n){
					$nn=$j;
				}
					$j++;
			}
			
				print("<tr class='$clase'>
			<td style='text-align:center;'>".$q[$i]['hosp_id']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;'>".htmlentities($q[$i]['nombres'])."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_ing']."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_egr']."</td>
			<td style='text-align:center;'>".$nn."</td>
			<td style='text-align:center;'>".$q[$i]['cama_tipo']."</td>
			</tr>");
		}
		
		$estadoc=cargar_registro("SELECT CASE WHEN hosp_numero_cama=$cama AND hosp_fecha_egr IS NULL THEN 'Ocupada' ELSE 'Desocupada' END AS estado 
					FROM hospitalizacion 
				  JOIN pacientes ON pac_id=hosp_pac_id					 
				  WHERE hosp_numero_cama = $cama					
				 AND hosp_fecha_egr is null", true);
		
		if($estadoc){
			?>
			<script>
				$('estado_cama').innerHTML="<?php echo '<b><font color=red>'.$estadoc['estado'].'</font></b>'; ?>";
			</script>
			<?php
		}else{
			?>
			<script>
				$('estado_cama').innerHTML="<?php echo '<b><font color=green>Disponible</font></b>'; ?>";
			</script>
			<?php
		}
	}else if($tipo_informe==21){
		
		$q=cargar_registros_obj("select hosp_id, pac_rut, pac_nombres ||' '|| pac_appat ||' '|| pac_apmat as nombres, func_nombre, pac_ficha
								from hospitalizacion 
								JOIN pacientes ON hosp_pac_id = pac_id
								LEFT JOIN funcionario ON hosp_func_id2=func_id
								WHERE (hosp_fecha_ing>='$fecha1 00:00:00' AND hosp_fecha_ing<='$fecha2 23:59:59')
								AND hosp_anulado=1
								ORDER BY hosp_fecha_ing");
								
		print("<tr class='tabla_header'>
		<td>Cta. Corriente</td>
		<td>R.U.T</td>
		<td>Nombre</td>
		<td>Ficha</td>
		<td>Funcionario</td>
		</tr>");
		
		//<td>Estado</td>
		//<td>Servicio / Sala</td>
		//<td>Cama</td>
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			print("<tr class='$clase'>
			<td style='text-align:center;'>".$q[$i]['hosp_id']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['nombres'])."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".htmlentities($q[$i]['func_nombre'])."</td>
			</tr>");
		}
	
	}else if($tipo_informe==22){
		
		if($serv_id!=0) {
			$serv_w="tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		 
	
		$q=cargar_registros_obj("
		
			SELECT * FROM clasifica_camas 
			LEFT JOIN tipo_camas ON
				cama_num_ini>=tcama_num_ini AND cama_num_fin<=tcama_num_fin
			WHERE $serv_w 
			ORDER BY tcama_tipo, cama_tipo, cama_num_ini
			
		", true);
		
		
		print("
		<tr class='tabla_header'>
		<td style='width:30%;'>Servicio</td>
		<td style='width:30%;'>Sala</td>
		<td style='width:15%;'>Cama</td>
		<td>Estado</td>
		</tr>");		
		
		
		$c=0;
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			$n=1;
		
			$cama = $q[$i]['cama_num_ini']*1;
			
			for($j=$q[$i]['cama_num_ini']*1;$j<=$q[$i]['cama_num_fin']*1;$j++) {
			
					$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
					
					$num_ini=$q[$i]['cama_num_ini']*1;
					$num_fin=$q[$i]['cama_num_fin']*1;
										
					
					$reg=cargar_registro("SELECT CASE WHEN hosp_numero_cama=$cama AND hosp_fecha_egr IS NULL THEN 'Ocupada' ELSE 'Desocupada' END AS estado 
											  FROM hospitalizacion 
											  JOIN pacientes ON pac_id=hosp_pac_id					 
											  WHERE hosp_numero_cama = $cama					
											  AND hosp_fecha_egr is null", true);
					
					$reg_f=cargar_registros_obj("select hosp_fecha_ing as fecha_ing, hosp_fecha_egr as fecha_egr 
											from hospitalizacion
											WHERE (hosp_fecha_ing<='$fecha2 23:59:59') 
											AND (hosp_numero_cama=$cama OR hosp_cama_egreso=$cama)
											ORDER BY hosp_fecha_ing DESC", true);					
					
					
										
					if($reg)
						$cama_estado='<b><font color=red>Ocupada</font></b>';
					else
						$cama_estado='<b><font color=green>Disponible</font></b>';
					
					print("
					
						<tr class='$clase'>
						<td style='text-align:center;font-weight:bold;'>".$q[$i]['tcama_tipo']."</td>
						<td style='text-align:center;'>".$q[$i]['cama_tipo']."</td>
						<td style='text-align:center;font-weight:bold;font-size:16px;'>".$n."</td>
						<td style='text-align:center;'><b>".$cama_estado."</b></td>
						</tr>
					
					");
				
				$n++;
				$cama++;
			}
			
		 }
	}elseif($tipo_informe==23) {
		
		$query=cargar_registros_obj("SELECT * 
									FROM hospitalizacion_registro 
									LEFT JOIN hospitalizacion_condicion using (hcon_id)
																		
		");
		
		$q=cargar_registros_obj("
		
			SELECT * FROM (
			SELECT *,
			upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date,
			(CURRENT_DATE-COALESCE(hosp_fecha_ing,hosp_fecha_hospitalizacion)::date) AS dias_espera,
			EXTRACT(HOUR FROM (CURRENT_DATE-COALESCE(hosp_fecha_ing,
			hosp_fecha_hospitalizacion))) AS horas_espera,
			t1.tcama_tipo AS tcama_tipo, t1.tcama_num_ini AS tcama_num_ini,
			t2.tcama_tipo AS servicio,hosp_id,
			date_part('year',age( pac_fc_nac ))AS edad_paciente,
			cama_num_ini , cama_num_fin,hosp_condicion_egr,
			COALESCE(diag_desc, hosp_diagnostico) AS diag_desc
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod
			LEFT JOIN tipo_camas ON
			cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas AS t1 ON 
			t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas AS t2 ON 
			t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
			--LEFT JOIN hospitalizacion_registro USING (hosp_id)
			--LEFT JOIN hospitalizacion_condicion USING (hcon_id)
			WHERE hosp_anulado!=1 AND t1.tcama_id=49 AND hosp_numero_cama>0 AND hosp_fecha_egr IS NULL AND
			$esp_w AND $serv_w AND $doc_w AND $filtro_w AND $dias_w
			
			) AS foo ORDER BY hosp_numero_cama", true );
		
			if(isset($_POST['xls']) AND $_POST['xls']=='1') {
			if($serv_id!=''){
				$serv_nombre_q=cargar_registro("SELECT tcama_tipo FROM clasifica_camas WHERE tcama_id=$serv_id");
				$serv_nombre='SERVICIO '.$serv_nombre_q['tcama_tipo'];
			}else{
				$serv_nombre='TODOS LOS SERVICIOS';
			}
			$fecha_rep=date('d/m/y H:i:s');
			print("<tr>
					<td colspan=6><h3>$serv_nombre</h3></td>
					<td coslpan=7><h3>$fecha_rep</h3></td></tr>
					<tr><td colspan=13></td></tr>");
		}
		
		print("<tr class='tabla_header'>
		<td>Cta. Corriente </td>
		<td>R.U.T.</td>
		<td>Nombre Completo</td>
		<td>Edad</td>
		<td>Servicio / Sala</td>
		<td>Cama</td>
		<td>Estado</td>
		<!--<td>Ficha</td>-->
		<td>Diagnóstico</td>
		<td>Días / Horas Hosp.</td>
		<td>Especialidad</td>
		<!--<td>Medico Tratante</td>-->
		<!--<td>Fecha Ingreso</td>-->
		<!--<td>Dias Hosp.</td>-->
		</tr>");
		
		if($q)
		for($i=0;$i<sizeof($q);$i++) {
			
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			/*$v=$q[$i]['hosp_condicion_egr']*1;
			
			switch($v) {
				
				case 0: $destino='(Sin Dato...)'; break;
				case 1: $destino='Alta a Domicilio'; break;
				case 2: $destino='Derivaci&oacute;n'; break;
				case 3: $destino='Fallecido'; break;
				case 4: $destino='Fugado'; break;
				case 5: $destino='Otro (<i>'.$q[$i]['hosp_otro_destino'].'</i>)'; break;
				
			}*/
			
			if($q[$i]['esp_desc']!='')
				$especialidad=$q[$i]['esp_desc'];
			else
				$especialidad='<i>(Sin Asignar...)</i>';

			if($q[$i]['tcama_tipo']!='')
				$servicio=$q[$i]['tcama_tipo'];
			else
				$servicio='<i>(Sin Asignar...)</i>';

			if($q[$i]['doc_rut']!='')
				$med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
			else
				$med_tratante='<i>(Sin Asignar...)</i>';
				
					
				                   
            //BUSQUEDA DE NUMERO DE CAMAS    
                $j=1;
                for($n=$q[$i]['cama_num_ini']*1;$n<=$q[$i]['cama_num_fin']*1;$n++) {                                
                      if($q[$i]['hosp_numero_cama']*1==$n){
                             $nn=$j;
                      }
                            $j++;
                }
 
			
			print("<tr class='$clase'>
			<td style='text-align:center;'>".$q[$i]['hosp_id']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_rut']."</td>
			<td style='font-size:10px;text-align:center;'>".($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres'])."</td>
			<td style='text-align:center;'>".$q[$i]['edad_paciente']."</td>
			<td style='text-align:center;'><b>".$q[$i]['tcama_tipo'].'</b> <br /> '.$q[$i]['cama_tipo']."</td>
			<td style='text-align:center;font-weight:bold;font-size:16px;'>".($nn)."</td>
			<!--<td style='text-align:right;'>".$q[$i]['hosp_id']."</td>-->
			<td style='text-align:center;font-weight:bold;'>".$query[0]['hcon_nombre']."</td>
			<td style='font-size:10px;'>".$q[$i]['hosp_diag_cod']."</b> ".$q[$i]['diag_desc']."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['dias_espera']." Días ".$q[$i]['horas_espera']." Horas"."</td>
			<td style='font-size:10px;'>".$q[$i]['esp_desc']."</td>
			<!--<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>-->
			<!--<td style='font-size:10px;'>".$servicio."</td>-->
			<!--<td style='font-size:10px;'>".$med_tratante."</td>-->
			<!--<td style='text-align:center;'>".$q[$i]['hosp_fecha_ing']."</td>-->
			</tr>");

			//<td style='text-align:center;'>".$q[$i]['hest_nombre']."</td>
			
		}
		
	}
		
			//<td style='font-size:10px;'>".htmlentities($q[$i]['tcama_tipo'])."</td>
			//<td style='font-size:10px;'>".htmlentities($q[$i]['prev_desc'])."</td>
			//<td style='font-size:10px;'>".htmlentities($q[$i]['func_nombre'])."</td>

?>

</table>

<script>


$('cant_registros').innerHTML="<?php 

	if($q)
		print(''.sizeof($q).'');
	else
		print('0');
		
?>";

</script>
