<?php 

	require_once('../../conectar_db.php');

	$monr_id=$_POST['monr_id']*1;

	if(!_cax(57)) die('ERROR DE PERMISOS');

	pg_query("INSERT INTO monitoreo_ges_registro_backup SELECT * FROM monitoreo_ges_registro WHERE monr_id=$monr_id");
	pg_query("DELETE FROM monitoreo_ges_registro WHERE monr_id=$monr_id");

?>
