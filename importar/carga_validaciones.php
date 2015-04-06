<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",utf8_decode(file_get_contents("validaciones.csv")));
	
	for($i=1;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
		
		$r=$f[$i];
		
		$r=ucwords($r);
		
		pg_query("
			INSERT INTO  monitoreo_ges_validaciones VALUES (DEFAULT,'','".pg_escape_string($r)."');
		");
	
	}

?>
