<?php 

	require_once('../../conectar_db.php');
	
	$pacq_id=$_POST['pacq_id']*1;
	
	pg_query("DELETE FROM pacientes_queue WHERE pacq_id=$pacq_id");

?>