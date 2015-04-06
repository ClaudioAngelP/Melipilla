<?php require_once('../../conectar_db.php');
	
	$hospam_id=$_POST['hospam_id']*1;
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	$fundamento=$_POST['fundamento'];
	
	$hosp_id=$h['hosp_id']*1;
	
	$doc_id=pg_escape_string(utf8_decode($_POST['doc_id2']));
	$art_id=pg_escape_string(utf8_decode($_POST['art_id2']));
	$cantidad=$_POST['art_cantidad2']*1;
	$horas=$_POST['art_horas']*1;
	$dias=$_POST['art_dias']*1;
		
	$motivo=pg_escape_string(utf8_decode($_POST['art_motivo']));
	$observa=pg_escape_string(utf8_decode($_POST['art_observa']));
	$terapia=pg_escape_string(utf8_decode($_POST['art_terapia']));
	
	$cultivo=pg_escape_string(utf8_decode(trim($_POST['art_cultivo'])));
	//$diag1=pg_escape_string(utf8_decode(trim($_POST['art_diagnostico'])));
	$diag2=pg_escape_string(utf8_decode(trim($_POST['otro_diag'])));
	
	/*if($diag1!='') {
		$diag=$diag1;
	} else {*/
		$diag=$diag2;
	//}
	
	$original=cargar_registro("SELECT * FROM hospitalizacion_autorizacion_meds WHERE hospam_id=$hospam_id;");
	
	pg_query("INSERT INTO hospam_modificaciones 
			VALUES (DEFAULT,$hospam_id,current_timestamp, $func_id, ".$original['hospam_estado'].", 
			'$fundamento', ".$original['art_id'].", ".$original['hospam_cant'].", 
			".$original['hospam_horas'].", ".$original['hospam_dias'].", '".$original['hospam_motivo']."', 
			'".$original['hospam_terapia']."', '".$original['hospam_observaciones']."',
			'".$original['hospam_cultivo']."', '".$original['hospam_diagnostico']."', ".$original['hospam_doc_id']."
		)");
	
	print_r($original);
	/*if(pg_query("UPDATE hospitalizacion_autorizacion_meds 
		SET hospam_estado=2, hospam_func_id2=$func_id, hospam_fundamento='$fundamento'
		art_id=$art_id, hospam_cant=$cantidad, hospam_horas=$horas, hospam_dias=$dias,
		hospam_motivo='$motivo', hospam_terapia='$terapia', hospam_observaciones='$observa',
		hospam_cultivo='$cultivo', hospam_diagnostico='$diag', hospam_doc_id=$doc_id  
		WHERE hospam_id=$hospam_id;"))
				print('OK');
	*/
	

?>
