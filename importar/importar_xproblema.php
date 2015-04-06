<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$archivo=utf8_decode(file_get_contents('xpatologias.csv'));
	
	$f=explode("\n", $archivo);
	
	$insert=0;
	$update=0;
	
	for( $i=1; $i < sizeof($f); $i++ ) {
		
		if(trim($f[$i])=='') continue;
		
		$r=explode('|',$f[$i]);
		
		$problema=pg_escape_string(trim($r[0]));
		$garantia=pg_escape_string(trim($r[1]));

		$xproblema=pg_escape_string(trim($r[2]));
		$xgarantia=pg_escape_string(trim($r[3]));
		$xrama=pg_escape_string(trim($r[4]));
		
		if($problema=='' OR $garantia=='') continue;
		
		$chk=cargar_registro("SELECT * FROM patologias_sigges_traductor 
			WHERE
			pst_problema_salud='$problema' AND pst_garantia='$garantia'");
			
		if(!$chk) {
			pg_query("INSERT INTO patologias_sigges_traductor VALUES (
				DEFAULT,'$problema','$garantia','$xproblema','$xgarantia','$xrama',0,0
			);");
			$insert++;
		} else {
			pg_query("UPDATE patologias_sigges_traductor SET
				pst_patologia_interna='$xproblema', pst_garantia_interna='$xgarantia', pst_rama_interna='$xrama'
			WHERE pst_problema_salud='$problema' AND pst_garantia='$garantia'");
			$update++;
		}
		
		
		if($i%50==0)
			print("$i ... "); 
		
		flush();
		
	}
	
	print("i:$insert u:$update");

?>
