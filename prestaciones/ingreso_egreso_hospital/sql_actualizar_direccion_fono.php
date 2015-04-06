<?php

	require_once('../../conectar_db.php');

	$pac_id=$_POST['paciente_id']*1;

	if(isset($_POST['paciente_dire'])) {
		$dire=pg_escape_string(utf8_decode($_POST['paciente_dire']));
		pg_query("UPDATE pacientes SET pac_direccion='$dire' WHERE pac_id=$pac_id");
	}

	if(isset($_POST['paciente_fono'])) {
		$fono=pg_escape_string(utf8_decode($_POST['paciente_fono']));
		pg_query("UPDATE pacientes SET pac_fono='$fono' WHERE pac_id=$pac_id");
	}

?>
