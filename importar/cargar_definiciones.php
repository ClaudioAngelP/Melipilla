<?php 

	error_reporting(E_ALL);

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	/*$f=explode("\n",pg_escape_string(utf8_decode(file_get_contents("definicion_proceso.csv"))));
	
	for($i=0;$i<sizeof($f);$i++) {
		
		if(trim($f[$i])=='||||||||' OR trim($f[$i])=='') continue;
		
		$r=explode('|',$f[$i]);
		
		for($j=0;$j<sizeof($r);$j++) {
			$r[$j]=trim($r[$j]);
		}
		
		if($r[0]!='')
			$id_condicion=$r[0]*1;
			
		if($id_condicion==33) {
			
			$id_condicion=0;
			$nombre_condicion='';
			
		} else {

			if($r[1]!='')
				$nombre_condicion=$r[1];
				
		}

		if($r[2]!='')
			$nombre_bandeja=$r[2];

		if($r[3]!='')
			$codigo_bandeja=$r[3];

		$id_condicion_nueva=$r[5]*1;

		if($id_condicion_nueva!=0)
			$nombre_condicion_nueva=$r[4];
		else
			$nombre_condicion_nueva='';

		$codigo_bandeja_nueva=$r[6];
		
		pg_query("
		INSERT INTO lista_dinamica_proceso VALUES (
			DEFAULT,
			$id_condicion,
			'$nombre_condicion',
			'$codigo_bandeja',
			'$nombre_bandeja',
			$id_condicion_nueva,
			'$nombre_condicion_nueva',
			'$codigo_bandeja_nueva'
		);");
		
	}

	$f=explode("\n",pg_escape_string(utf8_decode(file_get_contents("definicion_proceso2.csv"))));
	
	for($i=1;$i<sizeof($f);$i++) {
		
		if($f[$i]=='||||||||||||' OR $f[$i]=='') continue;
		
		$r=explode('|',$f[$i]);
		
		for($j=0;$j<sizeof($r);$j++) {
			$r[$j]=trim($r[$j]);
		}
		
		if($r[0]!='')
			$id_condicion=$r[0]*1;
			
		if($id_condicion==33) {
			
			$id_condicion=0;
			$nombre_condicion='';
			
		} else {

			if($r[1]!='')
				$nombre_condicion=$r[1];
				
		}

		if($r[5]!='')
			$nombre_bandeja=$r[5];

		if($r[6]!='')
			$codigo_bandeja=$r[6];

		$id_condicion_nueva=0;
		$nombre_condicion_nueva='';

		pg_query("
		INSERT INTO lista_dinamica_proceso VALUES (
			DEFAULT,
			$id_condicion,
			'$nombre_condicion',
			'$codigo_bandeja',
			'$nombre_bandeja',
			$id_condicion_nueva,
			'$nombre_condicion_nueva',
			'$codigo_bandeja_nueva'
		);");
		
	}*/

	$f=explode("\n",pg_escape_string(utf8_decode(file_get_contents("definicion_proceso2.csv"))));
	
	for($i=1;$i<sizeof($f);$i++) {
		
		if($f[$i]=='||||||||||||' OR $f[$i]=='') continue;
		
		$r=explode('|',$f[$i]);
		
		for($j=0;$j<sizeof($r);$j++) {
			$r[$j]=trim($r[$j]);
		}
		
		if($r[0]!='')
			$id_condicion=$r[0]*1;

		$nombre_subcondicion=$r[9];		
		
		if($nombre_subcondicion!='') {
			pg_query("
				UPDATE lista_dinamica_condiciones SET subcondiciones=COALESCE(subcondiciones,'') || '|' || '$nombre_subcondicion'
				WHERE id_condicion=$id_condicion
			;");
		}
	}

?>
