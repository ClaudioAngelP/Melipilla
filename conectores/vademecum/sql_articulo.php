<?php 

	require_once('../../conectar_db.php');

	$art_id=$_POST['art_id']*1;
	$id_vademecum=$_POST['id_vademecum'];

	pg_query("UPDATE articulo SET id_vademecum='$id_vademecum' WHERE art_id=$art_id");

?>
