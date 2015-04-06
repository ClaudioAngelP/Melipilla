<?php 

	require_once('../../conectar_db.php');
	
	pg_query("START TRANSACTION;");	
	
	$hosp_id=($_POST['hosp_id']*1);	
	
	$pac_id=$_POST['paciente_id']*1;
	$folio=$_POST['nro_folio']*1;
		
	$prev=$_POST['prevision']*1;
	$prev_clase=$_POST['prevision_clase']*1;
	
	$modalidad=$_POST['modalidad']*1;
	$ges=$_POST['ges']*1;
	$motivo=$_POST['motivo']*1;
	$procedencia=$_POST['procedencia']*1;
	$inst_id=$_POST['inst_id']*1;
	$doc_id=$_POST['doc_id']*1;
	
	if(isset($_POST['nro_cama'])) 
		$nro_cama=$_POST['nro_cama']*1;
	else
		$nro_cama=0;

	
	$diag1=json_decode($_POST['arr_diagnosticos']);	
	$diag2=json_decode($_POST['arr_diagnosticos_egreso']);	
	$presta=json_decode($_POST['arr_prestaciones']);	
	$traslados=json_decode($_POST['arr_traslados_int']);	

	$fecha_ing=pg_escape_string($_POST['fecha0']);
	$hora_ing=pg_escape_string($_POST['hora1']);	
	$serv_ing=pg_escape_string($_POST['centro_ruta0']);
	
	
	if((trim($_POST['fecha2'])!='')) {
	
		$fecha_egr="'".pg_escape_string($_POST['fecha2']);
		$hora_egr=pg_escape_string($_POST['hora2'])."'";
		$condicion=$_POST['condicion']*1;
		
		if($_POST['parto']=='-1') 
			$parto='null';
		else 
			$parto=($_POST['parto']=='0')?'true':'false';

		if($_POST['nacimiento']=='-1') 
			$nacimiento='null';		
		else 
			$nacimiento=($_POST['nacimiento']=='0')?'true':'false';
		
	} else {
	
		$fecha_egr='null';
		$hora_egr='';
		$condicion='null';
		$parto='null';
		$nacimiento='null';	

	}


	if($hosp_id==0) {
	
		$tipo_reg='N';	
	
		pg_query("
			INSERT INTO hospitalizacion VALUES (
				DEFAULT, '$fecha_ing $hora_ing', $folio, $pac_id, $modalidad, $ges, 
				$motivo, $procedencia, $inst_id, $fecha_egr $hora_egr, 
				$condicion, $parto, $nacimiento, $doc_id, $nro_cama,
				$prev, $prev_clase, '$serv_ing', current_timestamp,
				".($_SESSION['sgh_usuario_id']*1)."		
			);	
		");

		$hosp_id="CURRVAL('hospitalizacion_hosp_id_seq')";

	} else {

		$tipo_reg='E';	
	
		pg_query("UPDATE hospitalizacion SET
							hosp_folio=$folio,
							hosp_pac_id=$pac_id,
							hosp_modalidad=$modalidad,
							hosp_ges=$ges,
							hosp_motivo=$motivo,
							hosp_procedencia=$procedencia,
							hosp_inst_id=$inst_id,
							hosp_fecha_ing='$fecha_ing $hora_ing',
							hosp_fecha_egr=$fecha_egr $hora_egr,
							hosp_condicion_egr=$condicion,
							hosp_parto=$parto,
							hosp_nacimiento=$nacimiento,
							hosp_doc_id=$doc_id,
							hosp_numero_cama=$nro_cama,
							hosp_prevision=$prev,
							hosp_prevision_clase=$prev_clase,
							hosp_centro_ruta='$serv_ing'
						WHERE hosp_id=$hosp_id");	
	
	}
	
	
	pg_query("DELETE FROM paciente_diagnostico WHERE hosp_id=$hosp_id");	
	
	for($i=0;$i<sizeof($diag1);$i++)
		pg_query("INSERT INTO paciente_diagnostico VALUES (
				DEFAULT,
				$pac_id,
				'".pg_escape_string($diag1[$i][0])."',
				$i, '$fecha_ing $hora_ing', $hosp_id, 1		
		);");

	for($i=0;$i<sizeof($diag2);$i++)
		pg_query("INSERT INTO paciente_diagnostico VALUES (
				DEFAULT,
				$pac_id,
				'".pg_escape_string($diag2[$i][0])."',
				$i, $fecha_egr $hora_egr, $hosp_id, 0		
		);");

	pg_query("DELETE FROM prestacion WHERE hosp_id=$hosp_id");	

	for($i=0;$i<sizeof($presta);$i++)
		pg_query("INSERT INTO prestacion VALUES (
				DEFAULT,
				'".pg_escape_string($presta[$i][2])."',
				$pac_id,
				'', 
				'".pg_escape_string($presta[$i][0])."',				
				1, false, false, 0, 0, $hosp_id		
		);");

	pg_query("DELETE FROM paciente_traslado WHERE hosp_id=$hosp_id");	

	for($i=0;$i<sizeof($traslados);$i++)
		pg_query("INSERT INTO paciente_traslado VALUES (
				DEFAULT,
				'".pg_escape_string($traslados[$i][2])."',
				'".pg_escape_string($traslados[$i][0])."',				
				$hosp_id		
		);");
		
	$datos=cargar_registro("select hosp_id AS cta_corriente, pac_id, pac_ficha, cama_tipo || ' ' ||(hosp_numero_cama-cama_num_ini)+1 as sala_cama, COALESCE(cod_unidad_hosp,'28000000000') as un_hospitalaria, hosp_fecha_ing::date as fecha_ing,
							hosp_fecha_egr::date as fecha_egr, (SELECT count(hosp_id) FROM hospitalizacion WHERE hosp_pac_id=pac_id) as numero_hosp,
							CASE WHEN prev_id=5 THEN 'I' WHEN prev_id=6 THEN 'P' WHEN prev_id=9 THEN 'C' WHEN prev_id=8 THEN 'O' WHEN prev_id IN(1,2,3,4) THEN 'F' END AS prevision,
							'h' as tipo
							from hospitalizacion
							JOIN pacientes ON hosp_pac_id=pac_id AND hosp_id=$hosp_id
							LEFT JOIN prevision USING(prev_id)
							LEFT JOIN tipo_camas ON cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
							WHERE hosp_estado_envio=0
							AND (hosp_fecha_egr IS NULL AND hosp_numero_cama=0)
							OR (hosp_fecha_egr IS NULL AND NOT hosp_numero_cama<=0) AND hosp_anulado=0");
		
		$tipo=$datos['tipo'];
		$hosp_id=$datos['cta_corriente']*1;
		$paciente_id=$datos['pac_id']*1;
		$ficha=$datos['pac_ficha'];
		$sala_cama=$datos['sala_cama'];
		$un_hosp=$datos['un_hospitalaria'];
		$fecha_ing=$datos['fecha_ing'];
		$fecha_egr=$datos['fecha_egr'];
		$numero_hosp=$datos['numero_hosp']*1;
		$prevision=$datos['prevision'];
		
		/*******SE REALIZARAN LOS ENVIOS DE LOS PACIENTES HOSPITALIZADOS ******************************/
			$chk = cargar_registro("SELECT intehosp_cta_corriente FROM inte_hospitalizados WHERE intehosp_cta_corriente=$hosp_id");
			
		if(!$chk){
			
			pg_query("INSERT INTO inte_hospitalizados VALUES(DEFAULT,$hosp_id,$paciente_id,'$ficha','$sala_cama',$un_hosp,
						'$fecha_ing',null,$numero_hosp,'$prevision',1)");
		    pg_query("UPDATE hospitalizacion SET hosp_estado_envio=1 WHERE hosp_id=$hosp_id");	

		}


	pg_query("COMMIT;");

	echo $tipo_reg;

?>
