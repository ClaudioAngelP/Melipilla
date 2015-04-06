<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",file_get_contents('rut_infectologia.csv'));
	
	$pat=pg_escape_string(utf8_decode("INFECTOLOGÍA"));
	
	pg_query("START TRANSACTION;");
	
	$rep=0; $ok=0;
	
	for($i=2;$i<sizeof($f);$i++) {
		
		$r=explode('|',$f[$i]);
		
		$rut=explode('-',strtoupper(trim($r[2])));
		
		$frut=($rut[0]*1).'-'.$rut[1];
		
		$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$frut'");
		
		if(!$pac) continue;
		
		$pac_id=$pac['pac_id']*1;
		
		$chk=cargar_registro("SELECT * FROM pacientes_patologia WHERE pacpat_descripcion='$pat' AND pac_id=$pac_id");
		
		if(!$chk) {
			pg_query("INSERT INTO pacientes_patologia VALUES (DEFAULT, $pac_id, '$pat');");
			$ok++;
		} else
			$rep++;
		
	}
	
	pg_query("COMMIT;");
	
	print("OK: $ok REPETIDOS: $rep");

?>
