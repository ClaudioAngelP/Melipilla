<?php 

	require_once('../../conectar_db.php');
	
	$bloq_id=$_POST['bloq_id']*1;
	
	pg_query("DELETE FROM bloqueo_camas WHERE bloq_id=$bloq_id;");

?>
