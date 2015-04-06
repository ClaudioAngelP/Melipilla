<?php 

	require_once('../../conectar_db.php');
	
	pg_query("START TRANSACTION;");	
	
	$hosp_id=($_POST['hosp_id']*1);	

	$folio=-1;
	
	$pac_id=$_POST['paciente_id']*1;
	
	$chk=cargar_registro("select * from hospitalizacion
	where hosp_pac_id=$pac_id and hosp_fecha_egr is null AND hosp_anulado=0");

	
	if($chk){
	
	
	print( 'Paciente actualmente esta ingresado al sistema.|'.$chk['hosp_id']);
	exit();
		
	}
		
	$prev=$_POST['prevision']*1;
	$prev_clase=$_POST['prevision_clase']*1;
	
	$tipo_atencion=$_POST['tipo_atencion']*1;
	$modalidad=$_POST['modalidad']*1;
	$ges=$_POST['ges']*1;
	$motivo=$_POST['motivo']*1;
	$procedencia=$_POST['procedencia']*1;
	$inst_id=$_POST['inst_id']*1;
	
	$criticidad=pg_escape_string($_POST['criticidad']);
	if($criticidad=='-1'){
		$criticidad='';
	}
	$fecha_ing=pg_escape_string($_POST['fecha0']);
	$hora_ing=pg_escape_string($_POST['hora1']);	
	$serv_id=pg_escape_string($_POST['centro_ruta0']*1);

	$doc_id=$_POST['doc_id']*1;
	if($doc_id==''){
		$doc_id=-1;
	}
	$nro_cama=0;
	
	$fecha_egr='null';
	$hora_egr='';
	$condicion='null';
	$parto='null';
	$nacimiento='null';
	
	$cie10=pg_escape_string(utf8_decode($_POST['diag_cod']));
	$diag=pg_escape_string(utf8_decode($_POST['diagnostico']));	
	$esp_id = pg_escape_string($_POST['esp_id']*1);
	$esp_id2 = pg_escape_string($_POST['esp_id2']*1);
	$dau = pg_escape_string($_POST['dau']);

	if($hosp_id==0) {
	
		$tipo_reg='N';	
	
		pg_query("
			INSERT INTO hospitalizacion VALUES (
				DEFAULT, '$fecha_ing $hora_ing', $folio, $pac_id, $modalidad, $ges, 
				$motivo, $procedencia, $inst_id, $fecha_egr $hora_egr, 
				$condicion, $parto, $nacimiento, $doc_id, $nro_cama,
				$prev, $prev_clase, '', current_timestamp,
				".($_SESSION['sgh_usuario_id']*1).", 
				'$criticidad', true,
				'$cie10', '$diag', $esp_id, $serv_id, 
				null, null, null, null, 
				$esp_id2, null, null, null, null, null, null, 
				null, null, null, null, null, 0, 0, null, 0, '$dau',$tipo_atencion
			);	
		");

		$hosp_id="CURRVAL('hospitalizacion_hosp_id_seq')";
		$h_id=cargar_registro("SELECT CURRVAL('hospitalizacion_hosp_id_seq')AS id");
		
		$tipo_reg.='|'.$h_id['id'];	

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
							hosp_servicio=$serv_id,
							hosp_criticidad='$criticidad',
							hosp_solicitud=true,
							hosp_diag_cod='$cie10',
							hosp_diagnostico='$diag',
							hosp_esp_id=$esp_id
						WHERE hosp_id=$hosp_id");
		$tipo_reg.='|'.$hosp_id;	
	
	}
	
	$datos=cargar_registro("select hosp_id AS cta_corriente, pac_id, pac_ficha, cama_tipo || ' ' ||(hosp_numero_cama-cama_num_ini)+1 as sala_cama, COALESCE(cod_unidad_hosp,'28000000000') as un_hospitalaria, hosp_fecha_ing::date as fecha_ing,
							hosp_fecha_egr::date as fecha_egr, (SELECT count(hosp_id) FROM hospitalizacion WHERE hosp_pac_id=pac_id) as numero_hosp,
							CASE WHEN prev_id=5 THEN 'I' WHEN prev_id=6 THEN 'P' WHEN prev_id=9 THEN 'C' WHEN prev_id=8 THEN 'O' WHEN prev_id IN(1,2,3,4) THEN 'F' END AS prevision,
							'h' as tipo
							from hospitalizacion
							JOIN pacientes ON hosp_pac_id=pac_id AND hosp_id=$hosp_id
							LEFT JOIN prevision USING(prev_id)
							LEFT JOIN tipo_camas ON cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
							LEFT JOIN clasifica_camas ON tcama_id=hosp_servicio
							WHERE hosp_estado_envio=0
							AND (hosp_fecha_egr IS NULL AND hosp_numero_cama=0)
							OR (hosp_fecha_egr IS NULL AND NOT hosp_numero_cama<=0) AND hosp_anulado=0");
	if($datos){
		$tipo=$datos['tipo'];
		$hosp_id=$datos['cta_corriente']*1;
		$paciente_id=$datos['pac_id']*1;
		$ficha=$datos['pac_ficha'];
		$sala_cama=$datos['sala_cama'];
		$fecha_ing=$datos['fecha_ing'];
		$fecha_egr=$datos['fecha_egr'];
		$numero_hosp=$datos['numero_hosp']*1;
		$prevision=$datos['prevision'];
		$un_hosp=$datos['un_hospitalaria']*1;
		
		if($fecha_ing==''){
			$fecha_ing='null';
		}else{
			$fecha_ing="'$fecha_ing'";
		}
		
		/*******SE REALIZARAN LOS ENVIOS DE LOS PACIENTES HOSPITALIZADOS ******************************/
			$chk = cargar_registro("SELECT intehosp_cta_corriente FROM inte_hospitalizados WHERE intehosp_cta_corriente=$hosp_id");
			
		if(!$chk){
			
			pg_query("INSERT INTO inte_hospitalizados VALUES(DEFAULT,$hosp_id,$paciente_id,'$ficha','$sala_cama',$un_hosp,
						$fecha_ing,null,$numero_hosp,'$prevision',1)");
		    pg_query("UPDATE hospitalizacion SET hosp_estado_envio=1 WHERE hosp_id=$hosp_id");	

		}
	}
	pg_query("COMMIT;");
	echo $tipo_reg;

?>
