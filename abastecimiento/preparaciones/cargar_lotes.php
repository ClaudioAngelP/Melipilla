<?php

  require_once('../../conectar_db.php');

  $art_id = $_GET['art_id']*1;
  $bod_id = $_GET['bodega_id']*1;

	$q_view="

    CREATE TEMP VIEW stock_precalc_temp AS
    	 SELECT 
        stock_bod_id, stock_art_id, stock_vence, 
        SUM(stock_cant) AS stock_cant, 0 AS stock_cant_trans
	     FROM stock 
	     JOIN logs ON stock_log_id=log_id
	     LEFT JOIN pedido ON log_id_pedido=pedido_id
	     LEFT JOIN pedido_detalle 
		    ON pedido_detalle.pedido_id=pedido.pedido_id 
		    AND pedido_detalle.art_id=stock_art_id
	     WHERE 
       (NOT logs.log_tipo = 2 OR 
       (logs.log_tipo = 2 AND pedido_detalle.pedidod_estado))
	     AND stock_art_id=$art_id
	     AND stock_bod_id=$bod_id
	     GROUP BY stock_bod_id, stock_art_id, stock_vence;

  ";

  pg_query($conn, $q_view);
  
  $lotes = cargar_registros("
    SELECT stock_cant, 0, stock_vence, 
    (SELECT art_vence FROM articulo WHERE art_id=$art_id) AS art_vence
    FROM stock_precalc_temp WHERE
  	stock_cant>0
  	ORDER BY stock_vence
  ", false);  
  
  if(!$lotes) {
  	$art=cargar_registro("SELECT * FROM articulo WHERE art_id=$art_id");
  	$art_vence=$art['art_vence'];
  	$lotes=array(array(0,0,null,$art_vence));
  }
  
  print(json_encode($lotes));

?>
