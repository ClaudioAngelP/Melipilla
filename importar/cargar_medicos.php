<?php 

	require_once('../conectar_db.php');
	
	$bod_id=13;
	
	$f=explode("\n", utf8_decode(file_get_contents('medicos_psiq.csv')));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=="") continue;
	
		$r=explode('|',$f[$i]);
		
		$rut=trim($r[0]);
		$nombre=trim($r[1].' '.$r[2]);
		$paterno=trim($r[3]);
		$materno=trim($r[4]);
		
		pg_query("INSERT INTO doctores VALUES (default, '$rut', '$paterno', '$materno', '$nombre');");
	
	}
	
	pg_query("COMMIT;");

?>
