<?php 

	require_once('../../conectar_db.php');
	
	$diag_cod=pg_escape_string(utf8_decode($_POST['diag_cod']));
	
	list($d)=cargar_registros_obj("SELECT * FROM diagnosticos WHERE diag_cod='$diag_cod'");
	
	echo htmlentities($d['diag_desc']);

?>