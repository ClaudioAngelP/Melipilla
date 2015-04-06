<?php

  require_once('../../conectar_db.php');
  
  $busqueda = ($_GET['buscar']*1);
	
	$personal = pg_query($conn,"
	SELECT
	func_id,
	func_rut,
	func_clave,
	func_nombre,
	func_cargo
	FROM funcionario
	WHERE func_id=$busqueda 
	LIMIT 1
	");
	
	$datos=pg_fetch_row($personal);
	
	for($i=0;$i<count($datos);$i++) {
		$datos[$i]=htmlentities($datos[$i]);
	}
	
	print(json_encode($datos));


?>
