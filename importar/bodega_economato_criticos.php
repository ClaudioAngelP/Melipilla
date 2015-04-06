<?php 

	require_once('../conectar_db.php');
	
	$bod_id=13;
	
	$f=explode("\n", file_get_contents('bodega_economato_criticos.csv'));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($f);$i++) {
	
		$r=explode('|',$f[$i]);
		
		$codigo=trim(strtoupper($r[0]));
		
		$art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$codigo'");
		
		if(!$art) {
			print("ERROR: <b>$codigo</b> NO EXISTE<br /><br />");
			continue;
		}
		
		$art_id=$art['art_id']*1;
		$critico=$r[3]*1;
		$pedido=$r[4]*1;
		$gasto=$r[2]*1;
		$prioridad=$r[5]*1;
		$item=trim($r[6]);
		
		pg_query("UPDATE articulo SET art_prioridad_id=$prioridad, art_item='$item' WHERE art_id=$art_id;");
		
		$chk=cargar_registro("SELECT * FROM stock_critico WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
		
		if(!$chk)
			pg_query("INSERT INTO stock_critico VALUES (
				$art_id, $pedido, $critico, $bod_id, $gasto
			);");
		else
			pg_query("UPDATE stock_critico SET
				critico_pedido=$pedido, critico_critico=$critico, critico_gasto=$gasto
				WHERE critico_bod_id=$bod_id AND critico_art_id=$art_id");
	
	}
	
	pg_query("COMMIT;");

?>
