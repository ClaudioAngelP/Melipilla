<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",pg_escape_string(utf8_decode(file_get_contents("bandejas_condiciones.csv"))));
	
	for($i=4;$i<sizeof($f);$i++) {
		
		if(trim($f[$i])=='||||||||' OR trim($f[$i])=='') continue;
		
		$r=explode('|',$f[$i]);
		
		for($j=0;$j<sizeof($r);$j++) {
			$r[$j]=trim($r[$j]);
		}
		
		if($r[0]!='')
			$id_condicion=$r[0]*1;
			
		if($id_condicion==100) {
			
			$id_condicion=0;
			
		} 


		if($r[3]!='')
			$codigo_bandeja=$r[5];

		$id_condicion_nueva=$r[3]*1;

		$codigo_bandeja_nueva=$r[7];
		
		pg_query("
		INSERT INTO lista_dinamica_proceso VALUES (
			DEFAULT,
			$id_condicion,
			$id_condicion_nueva,
			'$codigo_bandeja',
			'$codigo_bandeja_nueva'
		);");
		
	}


?>
