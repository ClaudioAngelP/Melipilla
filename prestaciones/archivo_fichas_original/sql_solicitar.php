<?php require_once('../../conectar_db.php'); error_reporting(E_ALL);

	$pid=$_POST['pac_id']*1;
	$did=$_POST['doc_id']*1;
	$eid=$_POST['esp_id']*1;
	//$motivo=$_POST['motivo'];
	$aid=$_POST['amp_id']*1;

	$p=cargar_registro("SELECT pac_ficha FROM pacientes WHERE pac_id=$pid;");
	
	pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,$eid,$did,$pid,'".$p['pac_ficha']."',current_timestamp,0,null,$aid)");
	

?>
