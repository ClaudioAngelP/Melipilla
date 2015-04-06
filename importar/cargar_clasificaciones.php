<?php 

	require_once('../conectar_db.php');
	
	$fa=explode("\n",utf8_decode(file_get_contents('clasificaciones.csv')));
	
	//pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($fa);$i++) {
		
		if(trim($fa[$i])=="") continue;
	
		$r=explode('|',pg_escape_string(($fa[$i])));
		
		$cod="ART00".str_pad($r[0], 4, "0", STR_PAD_LEFT);
		
		//$cod=str_replace('-', '', $r[0]);
		
		$nom=trim(strtoupper($r[1]));
		$forma=trim(strtoupper($r[2]));
		$class_cod=$r[3];
		$class_desc=$r[4];
		
		$f=cargar_registro("SELECT * FROM bodega_clasificacion WHERE clasifica_nombre='$class_desc'");
		
		if($f) {
			$class_id=$f['clasifica_id'];
		} else {
			pg_query("INSERT INTO bodega_clasificacion VALUES (DEFAULT, '$class_desc','$class_cod');");
			$class_id="CURRVAL('bodega_clasificacion_clasifica_id_seq')";
		}
		
		$chk=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$cod'");
		
		if($chk) {
			
			pg_query("UPDATE articulo SET art_clasifica_id=$class_id WHERE art_id=".$chk['art_id']);
			
		}
		
	}
	
	//pg_query("COMMIT;");

?>
