<?php

  require_once('../../conectar_db.php');
  
  $busqueda = ($_GET['buscar']*1);
	
	$personal = pg_query($conn,"
	SELECT
	tec_id,
	tec_rut,
	tec_clave,
	tec_nombre,
	tec_cargo,
	tec_valor_hh
	FROM tecnico
	WHERE tec_id=$busqueda 
	LIMIT 1
	");
	
	$datos=pg_fetch_row($personal);
	
	for($i=0;$i<count($datos);$i++) {
		$datos[$i]=htmlentities($datos[$i]);
	}
	
	print(json_encode($datos));


?>
