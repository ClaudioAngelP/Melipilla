<?php 

	require_once('../conectar_db.php');

	$receta_id=$_GET['receta_id']*1;

	$rec=cargar_registro("SELECT * FROM receta WHERE receta_id=$receta_id");

	$bod_id=$rec['receta_bod_id'];
	$fecha=$rec['receta_fecha_emision'];
	$func=$rec['receta_func_id'];

	pg_query("START TRANSACTION;");

	$d=cargar_registros_obj("
		SELECT *, (total + despacho) AS dif FROM (
			SELECT recetad_receta_id, recetad_id, recetad_art_id, recetad_cant, 
			recetad_horas, recetad_dias, (((recetad_dias*24)/recetad_horas)*recetad_cant) AS total,
			COALESCE(stock_art_id,recetad_art_id), COALESCE(SUM(stock_cant),0) AS despacho FROM recetas_detalle 
			LEFT JOIN logs ON log_recetad_id=recetad_id
			LEFT JOIN stock ON stock_log_id=log_id
			WHERE recetad_receta_id = $receta_id
			GROUP BY recetad_receta_id, recetad_id, recetad_art_id, recetad_cant, recetad_horas, recetad_dias, stock_art_id
		) AS foo;
	");

	for($i=0;$i<sizeof($d);$i++) {

		$art_id=$d[$i]['recetad_art_id']*1;
		
		$recetad_id=$d[$i]['recetad_id']*1;
		
		$dif=$d[$i]['dif']*1;
		
		if($dif==0) {
			print("ARTICULO $art_id ESTA OK.<br><br>");
			continue;
		}

		$dif=$d[$i]['total']*1;
		
		$stock=cargar_registros_obj("
			SELECT stock_art_id, stock_vence, SUM(stock_cant) AS saldo FROM stock 
			JOIN logs ON stock_log_id=log_id
			WHERE stock_art_id=$art_id AND stock_bod_id=$bod_id AND log_fecha<='$fecha'
			GROUP BY stock_art_id, stock_vence
			ORDER BY stock_vence
		");
		
		pg_query("DELETE FROM stock WHERE stock_log_id IN (SELECT log_id FROM logs WHERE log_recetad_id=$recetad_id)");
		pg_query("DELETE FROM logs WHERE log_recetad_id=$recetad_id");
		
		pg_query("INSERT INTO logs VALUES (DEFAULT, $func, 9, '$fecha', $recetad_id, null, 0);");
		
		$csum=0;
		
		$tdif=$d[$i]['total']*1;
		
		for($j=0;$j<sizeof($stock);$j++) {
			
			$cvence=$stock[$j]['stock_vence'];
			$cdisp=$stock[$j]['saldo']*1;
			
			if($cdisp==0) continue;
			
			print("CDISP $cdisp VENCE $cvence TDIF $tdif <br><br>");
			
			if($cdisp<$tdif) $cantidad=-($cdisp); else $cantidad=-($tdif);
			
			pg_query("INSERT INTO stock VALUES (DEFAULT, $art_id, $bod_id, $cantidad, CURRVAL('logs_log_id_seq'),'$cvence', 0);");
			
			$tdif+=$cantidad;
			
			$csum+=$cantidad;
			
			if($tdif==0) break;
			
		}
		
		print("DESCONTADOS DE ART $art_id CANTIDAD $csum DE STOCK. <BR />");
		
		if(($csum+$dif)==0) print("OK!<br><br>"); 
		else print("ERROR!!!<br><br>");
	
	}

	pg_query("COMMIT;");

?>
