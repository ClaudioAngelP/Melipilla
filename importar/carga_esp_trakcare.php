<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",utf8_decode(file_get_contents("esp_trakcare.csv")));
	
	for($i=0;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
		
		$r=explode(";", $f[$i]);
	
		$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_ifl_usuario='".$r[0]."'");
		if($esp) continue;
		
		pg_query("
			INSERT INTO especialidades VALUES (DEFAULT, '".$r[1]."',0,-1,'".$r[0]."');
		");
	
	}

?>
