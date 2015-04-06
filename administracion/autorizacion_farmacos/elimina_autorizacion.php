<?php

  require_once('../../conectar_db.php');

	$autorizacion_id = ($_GET['autorizacion_id']*1);

	pg_query("DELETE FROM autorizacion_farmacos WHERE autf_id=$autorizacion_id");
	
	print("2");

?>
