<?php 

	require_once('../conectar_db.php');
	
	$accion=$_POST['accion'];
	$pid=$_POST['pid']*1;
	
	
	if($accion=='A'){
		
		$bandeja_origen=$_POST['origen_bandeja'];
		$bandeja_destino=$_POST['destino_bandeja'];
		$condicion_origen=$_POST['origen_condicion']*1;
		$condicion_destino=$_POST['destino_condicion']*1;
	
		pg_query("INSERT INTO lista_dinamica_proceso VALUES(DEFAULT,$condicion_origen,$condicion_destino,'".$bandeja_origen."','".$bandeja_destino."')");
	
	}else{
	
		pg_query("DELETE FROM lista_dinamica_proceso WHERE pid='".$pid."';");
				
	}
	
	
	
	echo 'OK';
?>