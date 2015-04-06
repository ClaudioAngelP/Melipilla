<?php 

	require_once('../../conectar_db.php');
	
	$nomd_id=$_POST['nomd_id']*1;
	
	pg_query("DELETE FROM nomina_detalle WHERE nomd_id=$nomd_id");

?>