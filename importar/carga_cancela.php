<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",utf8_decode(file_get_contents("motivos_cancelacion.csv")));
	
	for($i=0;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
		
		$r=explode(";", $f[$i]);
		
		pg_query("
		INSERT INTO nomina_codigo_cancela VALUES ('".$r[0]."','".$r[1]."');
		");
	
	}

?>
