<?php 

	require_once('../conectar_db.php');
	
	$e=explode("\n",utf8_decode(file_get_contents("poliespecialidad.csv")));
	
	pg_query("START TRANSACTION;");
	
	for($i=0;$i<sizeof($e);$i++) {
	
		$r=explode(';',trim($e[$i]));
		
		if(trim($r[0])=='' OR trim($r[2])=='' OR trim($r[3])=='') continue;
		
		$chk=cargar_registro("SELECT * FROM especialidades WHERE esp_codigo_int='".$r[2]."'");	
		
		if($chk) {
			
			$chk2=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=".$chk['esp_id']);
			
			if(!$chk2) {
				
				$esp_id=$chk['esp_id']*1;
				$codigo=pg_escape_string($r[3]);
				
				pg_query("DELETE FROM procedimiento_codigo WHERE esp_id=$esp_id");
				
				pg_query("INSERT INTO procedimiento_codigo VALUES (DEFAULT, $esp_id, 'CONSULTA CAE', '$codigo');");
				
			}
			
		}

	
	}
	
	pg_query("COMMIT;");

?>
