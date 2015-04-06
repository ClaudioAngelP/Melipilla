<?php

	require_once('../../conectar_db.php');

	$id=$_GET['talonario_id']*1;

	pg_query("DELETE FROM talonario WHERE talonario_id=$id;");

?>
