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
			(CURRENT_DATE-hosp_fecha_ing::date) AS dias_espera
			
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
		<td>Dias Espera</td>
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
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['dias_espera']."</td>
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
			(CURRENT_DATE-COALESCE(hosp_fecha_hospitalizacion, hosp_fecha_ing)::date) AS dias_espera,
			t1.tcama_tipo AS tcama_tipo, t1.tcama_num_ini AS tcama_num_ini,
			t2.tcama_tipo AS servicio
			
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
			
			WHERE hosp_numero_cama>0 AND hosp_fecha_egr IS NULL AND
			$esp_w AND $serv_w AND $doc_w AND $filtro_w AND $dias_w
			
			) AS foo ORDER BY dias_espera DESC
			
		", true);
		
		print("<tr class='tabla_header'>
		<td>R.U.T.</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>(Sub)Especialidad</td>
		<td>Servicio Ingreso</td>
		<td>Medico Tratante</td>
		<td>Fecha Ingreso</td>
		<td>Servicio / Sala</td>
		<td>Cama</td>
		<td>Diagn&oacute;stico</td>
		<td>Dias Hosp.</td>
		</tr>");
		
		//<td>Estado</td>
		
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
			<td style='text-align:center;'><b>".$q[$i]['tcama_tipo'].'</b> <br /> '.$q[$i]['cama_tipo']."</td>
			<td style='text-align:center;font-weight:bold;font-size:16px;'>".(($q[$i]['hosp_numero_cama']*1-$q[$i]['tcama_num_ini']*1)+1)."</td>
			<td style='text-align:center;font-size:10px;'><b>".$q[$i]['hosp_diag_cod']."</b> ".$q[$i]['diag_desc']."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['dias_espera']."</td>
			</tr>");

			//<td style='text-align:center;'>".$q[$i]['hest_nombre']."</td>
			
		}
		
	} elseif($tipo_informe==3 OR $tipo_informe==4 OR $tipo_informe==10) {
	
		if($serv_id!=0 AND ($tipo_informe==3 OR $tipo_informe==4)) {
			$serv_w="t1.tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}	
		
		if($condicion!=0 AND ($tipo_informe==3 OR $tipo_informe==4)) {
			$cond_w="hosp_condicion_egr=$condicion";
		} else {
			$cond_w='true';
		}	
		
		if($tipo_informe==4 OR $tipo_informe==10)
			$fecha_egr_w="hosp_fecha_egr::date BETWEEN '$fecha1' AND '$fecha2'";	 
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
			COALESCE(diag_desc, hosp_diagnostico) AS diag_desc
			
			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id
						
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod

			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_cama_egreso AND cama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_cama_egreso AND t1.tcama_num_fin>=hosp_cama_egreso

			LEFT JOIN clasifica_camas AS t2 ON 
				t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
			
			WHERE hosp_fecha_egr IS NOT NULL AND hosp_condicion_egr<6 AND $fecha_egr_w AND
			$esp_w AND $serv_w AND $doc_w AND $filtro_w AND $cond_w AND $dias_w
			
			) AS foo ORDER BY dias_espera DESC
			
		", true);
		
		print("<tr class='tabla_header'>
		<td>R.U.T.</td>
		<td>Ficha</td>
		<td>Nombre Completo</td>
		<td>(Sub)Especialidad</td>
		<td>Servicio Ingreso</td>
		<td>Medico Tratante</td>
		<td>Fecha Ingreso</td>
		<td>Fecha Egreso</td>
		<td>Destino</td>
		<td>Servicio / Sala</td>
		<td>Cama</td>
		<td style='width:100px;'>Diagn&oacute;stico</td>
		<td>Dias Hosp.</td>
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
			
			print("<tr class='$clase'>
			<td style='text-align:right;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;'>".$q[$i]['pac_ficha']."</td>
			<td style='font-size:10px;'>".($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres'])."</td>
			<td style='font-size:10px;'>".$especialidad."</td>
			<td style='font-size:10px;'>".$servicio."</td>
			<td style='font-size:10px;'>".$med_tratante."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_ing']."</td>
			<td style='text-align:center;'>".$q[$i]['hosp_fecha_egr']."</td>
			<td style='text-align:center;'>".$destino."</td>
			<td style='text-align:center