<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",utf8_decode(file_get_contents("gesmacaut.csv")));
	
	for($i=0;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=='') continue;
		
		$r=explode("|", pg_escape_string($f[$i]));
		
		if($r[0]!='') {
			
			$nombre=strtoupper($r[0]);
			
			$presta=$r[2];
				
		} else {
		
			if($r[2]!='')
				$presta=$r[2];
		
		}
		
		$art_codigo=$r[3];
		
		$art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
		
		if(!$art) {
			print("ERROR $art_codigo NO EXISTE. <br><br>"); continue;
		 }
		
		$art_id=$art['art_id'];

		$chk=cargar_registro("SELECT * FROM autorizacion_farmacos WHERE autf_nombre=upper('$nombre');");
		
		if($chk) {
			$autf_id=$chk['autf_id']*1;
		} else {
			pg_query("INSERT INTO autorizacion_farmacos VALUES (DEFAULT, upper('$nombre'), false);");
			$autf_id="CURRVAL('autorizacion_farmacos_autf_id_seq')";
		}
		
		pg_query("
			INSERT INTO autorizacion_farmacos_detalle VALUES (DEFAULT, $autf_id, $art_id, '$presta');
		");
	
	}

?>
