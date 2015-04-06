<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",file_get_contents('sicoa.csv'));
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode('|',utf8_decode($f[$i]));
		
		if(trim($r[2])=='' AND trim($r[3])==''){ 
			print(pg_escape_string($r[0])." - ".pg_escape_string($r[1])." : NO ENCONTRADO.<br>");			
			continue;
		}

		$chk=cargar_registros_obj("
			SELECT * FROM patologias_sigges_traductor
			WHERE pst_patologia_interna='".pg_escape_string($r[0])."' AND pst_garantia_interna='".pg_escape_string($r[1])."' 		
		");

		if(!$chk)
			print(pg_escape_string($r[0])." - ".pg_escape_string($r[1])." : NO EXISTE.<br>");
		else{
		
			
			pg_query("UPDATE patologias_sigges_traductor SET 
						codigos_sic='".pg_escape_string($r[2])."',
						codigos_oa='".pg_escape_string($r[3])."'
					WHERE pst_patologia_interna='".pg_escape_string($r[0])."' AND 
							pst_garantia_interna='".pg_escape_string($r[1])."'
					");		
			print(pg_escape_string($r[0])." - ".pg_escape_string($r[1])." : SI<br>");
		}	
	}

?>