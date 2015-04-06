<?php

  require_once('../../conectar_db.php');

	   $receta_id=$_GET['receta_id'];
	   $bodega_id=$_GET['bodega_id'];
	   $receta_detalle=$_GET['detalle_receta'];
	   
	   pg_query($conn,"START TRANSACTION;");
	   
	   $detalle = split('!', $receta_detalle);
	   
	   for($i=0;$i<count($detalle)-1;$i++) {
     
          $fila = split('/',$detalle[$i]);
          
          if($fila[2]!='') 
            $vence = "'".pg_escape_string(str_replace('$', '/', $fila[2]))."'";
          else
            $vence = 'null';
          
          // Ingresa Log
		
      		pg_query($conn, "
      		INSERT INTO 
      		logs
      		VALUES (
      		DEFAULT,
      		1,
      		9,
      		current_timestamp,
      		".$fila[0].",
      		NULL,
      		0 )
      		");	
          
          pg_query($conn, "
          INSERT INTO 
					stock
					VALUES (
					DEFAULT,
					".$fila[1].",
					$bodega_id,
					-(".$fila[3]."),
					CURRVAL('logs_log_id_seq'),
					".$vence.",
					0
          )
					");
     
     }
     
      pg_query("
      
		update receta set 
			receta_vigente=false, 
			receta_fecha_cierre=CURRENT_TIMESTAMP, 
			receta_func_id2=".$_SESSION['sgh_usuario_id']." 
		where receta_id=$receta_id AND receta_id not in (

		select distinct recetad_receta_id FROM (
		select recetad_id, recetad_receta_id, ((((recetad_dias*24)/recetad_horas)*recetad_cant)/COALESCE( art_unidad_cantidad, 1 )) AS total, sum(-stock_cant) AS cantidad
		from recetas_detalle
		join articulo on recetad_art_id=art_id
		left join logs on log_recetad_id=recetad_id
		left join stock on stock_log_id=log_id AND stock_art_id=recetad_art_id
		WHERE recetad_receta_id=$receta_id
		group by recetad_id, recetad_receta_id, recetad_dias, recetad_horas, recetad_cant, art_unidad_cantidad
		) AS foo WHERE COALESCE(cantidad,0)<total

		);
		
      ");
	
	   pg_query($conn,"COMMIT;");
	   
	   print('OK');


?>
