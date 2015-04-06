<?php 

	require_once('../../conectar_db.php');
	
	$dev_id=$_POST['nDev_id']*1;
	
	$r=cargar_registro("SELECT * FROM devolucion_boletines where devol_id = $dev_id");

	print(json_encode(array(true,$r)));
?>
