<?php 

	require_once('../conectar_db.php');
	
	$fa=explode("\n",utf8_decode(file_get_contents('polis_psiqcsv.csv')));
	
	//pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($fa);$i++) {
		
		if(trim($fa[$i])=="") continue;
	
		$r=explode('|',pg_escape_string(($fa[$i])));
		
		$sector=$r[0];
		$poli=(($r[1]));
		
		$f=cargar_registro("SELECT * FROM especialidades WHERE esp_desc='[".$sector."] - ".$poli."';");
		
		if($f) {
			print("[$sector] - $poli. EXISTE<br>");
			$continue;
		} else {
			pg_query("INSERT INTO especialidades VALUES (DEFAULT, '[".$sector."] - ".$poli."');");
			print("[$sector] - $poli. INSERTADO<br>");
			$esp_id="CURRVAL('especialidades_esp_id_seq')";
		}
		
		
	}
	
	//pg_query("COMMIT;");

?>
