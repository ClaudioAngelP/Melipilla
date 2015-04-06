<?php

  require_once('../../conectar_db.php');
  
  $busqueda = ($_GET['buscar']*1);
	
	$personal = pg_query($conn,"
	SELECT
	bod_id,
	bod_glosa,
	bod_ubica,
	bod_proveedores,
	bod_desp_ccosto,
	bod_costo,
	centro_nombre,
	bod_inter,
	bod_despacho,
	bod_controlados,
	bod_repone
	FROM bodega
	
	LEFT JOIN centro_costo
	ON centro_ruta=bod_costo
	
	WHERE bod_id=$busqueda 
	LIMIT 1
	");
	
	$datos=pg_fetch_row($personal);
	
	for($i=0;$i<count($datos);$i++) {
		$datos[$i]=htmlentities($datos[$i]);
	}
	
	print(json_encode($datos));
	
?>
