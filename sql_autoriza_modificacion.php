<?php 

	require_once('conectar_db.php');
	
	if(!_cax(501)) exit();
	
	$doc_id=$_POST['doc_id']*1;
	
	$aut=pg_query("SELECT * FROM documento_modificaciones 
				JOIN funcionario ON func_id1=func_id
				WHERE doc_id=$doc_id AND docm_fecha_realiza IS NULL;");

	if(!($d=pg_fetch_assoc($aut))) {

		pg_query("INSERT INTO documento_modificaciones VALUES (DEFAULT, $doc_id, ".$_SESSION['sgh_usuario_id'].", CURRENT_TIMESTAMP, null, null);");
		
		exit("Modificaci&oacute;n AUTORIZADA.");
	
	} else {

		pg_query("DELETE FROM documento_modificaciones WHERE doc_id=$doc_id;");

		exit("Modificaci&oacute;n NO AUTORIZADA.");
	
	}

?>
