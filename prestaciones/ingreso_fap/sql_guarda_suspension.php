<?php

	require_once('../../conectar_db.php');
	
	$fap_id=$_POST['fap_id']*1;
	$suspension=pg_escape_string(utf8_decode($_POST['fap_suspension']));
	
	if($fap_id>0){
		pg_query("UPDATE fap_pabellon SET fap_suspension='$suspension' WHERE fap_id=$fap_id");
	}

?>
