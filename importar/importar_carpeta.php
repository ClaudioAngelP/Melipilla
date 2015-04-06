<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",file_get_contents('importar_carpeta.csv'));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode('|',pg_escape_string(utf8_decode($f[$i])));
		
		$id=$r[0]*1;		
		$nombre=pg_escape_string(trim($r[1]));
		$ges=$r[2]*1;
		$servicio=pg_escape_string(trim($r[3]));
		$tipo_hosp=$r[4]*1;
		
		pg_query("INSERT INTO orden_carpeta VALUES (
        $id, '$nombre', 
        '$servicio', 
        $tipo_hosp,
        $ges);");
		
	}
	
	pg_query("COMMIT;");

?>
