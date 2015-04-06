<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",file_get_contents('especialidades_gc.csv'));
	
	for($i=1;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
	
		$r=explode('|',$f[$i]);
	
		pg_query("INSERT INTO especialidades_gestion_camas VALUES (DEFAULT, '".pg_escape_string(trim(utf8_decode($r[0])))."', ".($r[1]*1).");");
	
	}

?>
