<?php 

	require_once('../../conectar_db.php');
	
	$orden_id=$_POST['orden_id']*1;
	$orden_estado=$_POST['orden_estado']*1;
	
	if(!_cax(500)) exit();
	
	pg_query("UPDATE orden_compra SET orden_estado=$orden_estado WHERE orden_id=$orden_id");

?>
