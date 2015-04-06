<?php 

	require_once('../conectar_db.php');
	
	$f=explode("\n",file_get_contents('codigos_medicos.csv'));
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode(';',pg_escape_string(utf8_decode($f[$i])));
		
		$rut=(str_replace('.','',$r[0])*1).'-'.strtoupper($r[1]);		
		$cod=trim($r[4]);
		
		if(strstr($r[2], '  ')) {
			$r[2]=str_replace('  ', ' ', $r[2]);
		}
		
		$nombre=explode(' ',trim($r[2]));
		
		if(sizeof($nombre)==2) {
			$pat=$nombre[0]; $mat=''; $nom=$nombre[1];
		} elseif(sizeof($nombre)==3) {
			$pat=$nombre[0]; $mat=$nombre[1]; $nom=$nombre[2];			
		} elseif(sizeof($nombre)>3) {
			$pat=$nombre[0]; $mat=$nombre[1]; 
			$nom=$nombre[2];					
			for($j=3;$j<sizeof($nombre);$j++) $nom.=' '.$nombre[$j];	
			$nom=trim($nom);
		}
		
		$chk=cargar_registro("SELECT * FROM doctores WHERE doc_rut='$rut'");
		
		if($chk) {		

			pg_query("UPDATE doctores SET doc_codigo='$cod' WHERE doc_rut='$rut'");
		
		} else {
		
			pg_query("INSERT INTO doctores VALUES (DEFAULT, '$rut', '$pat', '$mat', '$nom');");
			pg_query("UPDATE doctores SET doc_codigo='$cod' WHERE doc_id=CURRVAL('doctores_doc_id_seq');");
		
			
		}
		
	}

?>
