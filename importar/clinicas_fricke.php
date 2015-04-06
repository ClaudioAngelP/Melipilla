<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",file_get_contents('clinicas_fricke.csv'));
	
	for($i=1;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
		
		$r=explode('|',utf8_decode($f[$i]));
	
		//if(trim($r[8])!='De Valparaíso') continue;
	
		$nombre=trim($r[3].' ('.trim($r[4].' '.$r[5]).')');
		$codigo=$r[2];
	
		pg_query("INSERT INTO instituciones
		VALUES (DEFAULT, '".pg_escape_string(trim($nombre))."', 0, '$codigo');");
	
	}

?>
