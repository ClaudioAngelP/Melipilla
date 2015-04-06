<?php 

	require_once('../conectar_db.php');
	
	$sep=explode('|',utf8_decode($_POST['sepultura']));

	$clase=pg_escape_string(utf8_decode($sep[0]));
	$codigo=pg_escape_string(utf8_decode($sep[1]));
	$numero=$sep[2]*1;	
	$letra=pg_escape_string(trim(utf8_decode($sep[3])));	
	
	$s=cargar_registros_obj("SELECT * FROM propiedad_sepultura
		WHERE ps_clase='$clase' AND 
				ps_codigo='$codigo' AND 
				ps_numero=$numero	AND 
				ps_letra='$letra' AND
				ps_vigente");

	print(json_encode($s));

?>