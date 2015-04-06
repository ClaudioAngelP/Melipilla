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
	$partos=json_decode($_POST['arr_listado_partos']);	

	$fecha_ing=pg_escape_string($_POST['fecha0']);
	$hora_ing=pg_escape_string($_POST['hora1']);	
	$serv_ing=pg_escape_string($_POST['centro_ruta0']);
	
	
	if((trim($_POST['fecha2'])!='')) {
	
		$fecha_egr="'".pg_escape_string($_POST['fecha2']);
		$hora_egr=pg_escape_string($_POST['hora2'])."'";
		$condicion=$_POST['condicion']*1;
		$parto='null';
		$nacimiento='null';		
		
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
		
	pg_query("DELETE FROM hospitalizacion_partos WHERE hosp_id=$hosp_id");	
	
	for($i=0;$i<sizeof($partos);$i++) {

		list($partos[$i][0])=explode(')',$partos[$i][0]);
		list($partos[$i][1])=explode(')',$partos[$i][1]);
		
		pg_query("INSERT INTO hospitalizacion_partos VALUES (
				$hosp_id,
				$i,
				".($partos[$i][0]*1).",
				".($partos[$i][1]*1).",
				".($partos[$i][2]*1).",
				".($partos[$i][3]*1)."
		);");
	
	}
	

	pg_query("COMMIT;");

	echo $tipo_reg;

?>