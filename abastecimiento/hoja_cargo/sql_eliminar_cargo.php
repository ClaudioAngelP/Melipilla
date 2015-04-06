<?php 

	require_once('../../conectar_db.php');
	
	$stock_id=$_POST['stock_id']*1;
	
	$tmp=cargar_registro("SELECT * FROM stock WHERE stock_id=$stock_id");
	
	$log_id=$tmp['stock_log_id']*1;
	
	pg_query("DELETE FROM stock WHERE stock_id=$stock_id;");
	
	$chk=cargar_registros_obj("SELECT * FROM stock WHERE stock_log_id=$log_id;");
	
	if(!$chk)
		pg_query("DELETE FROM stock WHERE log_id=$log_id;");

?>