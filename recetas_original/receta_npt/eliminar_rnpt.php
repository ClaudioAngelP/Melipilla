<?php require_once('../../conectar_db.php');

	$rnpt_id=$_GET['rnpt_id']*1;
	
	pg_query("DELETE FROM receta_npt WHERE rnpt_id=$rnpt_id");

	$chk=cargar_registro("SELECT * FROM receta_npt WHERE rnpt_id=$rnpt_id");
	
	if(!$chk)	
		print('OK');
		
?>

