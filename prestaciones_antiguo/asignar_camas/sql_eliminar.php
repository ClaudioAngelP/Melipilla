<?php 

	require_once('../../conectar_db.php');
	
	$hosp_id=$_POST['hosp_id']*1;
	
	pg_query("DELETE FROM hospitalizacion WHERE hosp_id=$hosp_id;");

?>