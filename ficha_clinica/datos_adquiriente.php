<?php 
	
	require_once('../conectar_db.php');
	
	$adq_rut=$_POST['adq_rut'];
	
	$adq=cargar_registro("SELECT * FROM receta_adquiriente WHERE adq_rut='$adq_rut' ORDER BY adq_id DESC LIMIT 1;",true);
	
	print(json_encode($adq));
	
?>
