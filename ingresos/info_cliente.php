<?php 

	require_once('../conectar_db.php');

	$clirut=$_GET['clirut'];
	
	if(trim($clirut)=='') {
		echo json_encode(false);
		exit();	
	}
	
	$d=explode('-',$clirut);
	
	if(sizeof($d)!=2 OR ($d[0]*1)==0) {
		echo json_encode(false);
		exit();				
	}
	
	$r=cargar_registro("SELECT clientes.*,comunas.* FROM clientes 
								LEFT JOIN comunas USING (comcod)								
								WHERE clirut=".($d[0]*1), true);

	echo json_encode($r);

?>