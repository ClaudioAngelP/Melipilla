<?php 

	require_once('../../conectar_db.php');

	$fap_id=($_POST['fap_id']*1);
	
	pg_query("START TRANSACTION;");
	pg_query("DELETE FROM fap WHERE fap_id=$fap_id");
	pg_query("DELETE FROM fap_pabellon WHERE fap_id=$fap_id");
	pg_query("DELETE FROM fap_prestacion WHERE fap_id=$fap_id");
	pg_query("COMMIT;");

?>