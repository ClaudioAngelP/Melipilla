<?php

  require_once('../../conectar_db.php');
  
  
	$busqueda = ($_GET['buscar']*1);
	
	$proveedores = pg_query($conn,"
	SELECT
	prov_id,
	prov_rut,
	prov_glosa,
	prov_direccion,
	prov_ciudad,
	prov_fono,
	prov_fax,
	prov_mail
	FROM proveedor
	WHERE prov_id=$busqueda 
	LIMIT 1
	");
	
	$datos=pg_fetch_row($proveedores);
	
	for($i=0;$i<count($datos);$i++) {
		$datos[$i]=htmlentities($datos[$i]);
	}
	
	print(json_encode($datos));
	

?>
