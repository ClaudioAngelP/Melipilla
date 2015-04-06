<?php

  require_once('../../conectar_db.php');
  
  
	$busqueda = ($_GET['buscar']*1);
	
	$doctor = pg_query($conn,"
	SELECT
	doc_id,
	doc_rut,
	doc_paterno,
	doc_materno,
	doc_nombres,
  doc_fono,
	doc_mail
	FROM doctores
	WHERE doc_id=$busqueda 
	LIMIT 1
	");
	
	$datos=pg_fetch_row($doctor);
	
	for($i=0;$i<count($datos);$i++) {
		$datos[$i]=htmlentities($datos[$i]);
	}
	
	print(json_encode($datos));
	

?>
