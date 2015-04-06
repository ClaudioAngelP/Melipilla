<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",utf8_decode(file_get_contents('carga_cual.csv')));
	
	for($i=0;$i<sizeof($f);$i++) {
	
		$r=trim($f[$i]);
		
		if($r=='') continue;
		
		$l=explode('|',pg_escape_string($r));
		
		if($l[0]!='') $monv_subclase=$l[0];
		
		$monv_detalle=$l[1];
		
		pg_query("
			INSERT INTO monitoreo_ges_detalle_validaciones VALUES (DEFAULT ,'$monv_subclase' ,'$monv_detalle');
		");
	
	}


?>
