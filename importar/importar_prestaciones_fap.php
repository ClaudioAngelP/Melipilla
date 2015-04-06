<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",file_get_contents('prestaciones.csv'));
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode('|',utf8_decode($f[$i]));

		$chk=cargar_registros_obj("
			SELECT * FROM codigos_prestacion 
			WHERE codigo='".pg_escape_string($r[0])."'		
		");

		if(!$chk)
		pg_query("
			INSERT INTO codigos_prestacion VALUES (
				'".pg_escape_string($r[0])."',
				'".pg_escape_string($r[1])."',
				'fap'			
			);		
		");
		
		
	}

?>