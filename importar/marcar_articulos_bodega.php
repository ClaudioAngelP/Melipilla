<?php require_once('../conectar_db.php');

	
	$fa=explode("\n",utf8_decode(file_get_contents('kits.csv')));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($fa);$i++) {
	
		$r=explode('|',pg_escape_string(($fa[$i])));
		
		$cod=str_replace('-','',trim(strtoupper($r[0])));
		$nom=trim(strtoupper($r[1]));
				
		$chk=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");

		if(!$chk) {
			print("[".$cod."] ".$nom."<br>");
			print("<font color='red'>NO EXISTE</font><br>");
		} else{
			print("[".$cod."] ".$nom."<br>");
			print("<font color='red'>DELETE FROM articulo_bodega where artb_bod_id=25/27 and artb_art_id=".$chk['art_id'].";</font><br>");
			pg_query("DELETE FROM articulo_bodega where artb_bod_id=25 and artb_art_id=".$chk['art_id'].";");
			pg_query("DELETE FROM articulo_bodega where artb_bod_id=27 and artb_art_id=".$chk['art_id'].";");
			print("<font color='green'>INSERT INTO articulo_bodega VALUES (default,".$chk['art_id'].",25/27);</font><br>");
			pg_query("INSERT INTO articulo_bodega VALUES (default,".$chk['art_id'].",25);");
			pg_query("INSERT INTO articulo_bodega VALUES (default,".$chk['art_id'].",27);");
		}
		
	}
	
	pg_query("COMMIT;");

?>
