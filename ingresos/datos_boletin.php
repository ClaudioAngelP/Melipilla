<?php 

	require_once('../conectar_db.php');
	
	$id_boletin=$_POST['str']*1;
	
	$r=cargar_registro("SELECT * FROM boletines JOIN pacientes USING (pac_id) where bolnum =$id_boletin");

	print(json_encode($r));
?>
