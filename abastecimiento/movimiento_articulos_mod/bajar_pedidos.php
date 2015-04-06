<?php

  require_once("../../conectar_db.php");

  $bodega_origen=pg_escape_string($_GET['bodega_origen']);
  $nro_pedido=($_GET['numero_pedido']*1);
  
  $buscar_nro = pg_query($conn,
  "
  SELECT * FROM pedido WHERE pedido_nro=$nro_pedido
  ");
  
  if(pg_num_rows($buscar_nro)>0) {
  
    $pedidos = pg_query($conn,
    "SELECT 
      pedido_id, 
      pedido_nro, 
      pedido_fecha, 
      bod_glosa, 
      origen_bod_id,
      origen_centro_ruta
    FROM pedido 
    LEFT JOIN bodega ON bod_id=origen_bod_id
    LEFT JOIN centro_costo ON origen_centro_ruta=centro_ruta
    WHERE
    pedido_nro=$nro_pedido
    AND 
    pedido_estado=0
    "
    );
    
    if(pg_num_rows($pedidos)==0) {
      $respuesta[0]=false;
      $respuesta[1]=false;
      
      die(json_encode($respuesta));
    }
    
  } else {

    $respuesta[0]=true;
    $respuesta[1]=false;
    die(json_encode($respuesta));

  }
  
  
    $fila = pg_fetch_row($pedidos);
    
  /*  $detalle = pg_query($conn, "
    SELECT 
      art_codigo, 
      art_glosa, 
      pedidod_cant, 
      art_id, 
      COALESCE(sum(stock_cant),0)
    FROM pedido_detalle 
    JOIN articulo USING (art_id)
    LEFT JOIN stock_precalculado_trans ON 
      stock_art_id=art_id AND stock_bod_id=$bodega_origen
    WHERE pedido_id=".$fila[0]." AND COALESCE(pedidod_estado,false)=false
    GROUP BY
    art_glosa, art_codigo, pedidod_cant, art_id
    ORDER BY art_glosa
    ");*/

  $detalle = cargar_registros_obj("
	
	SELECT *,

	COALESCE((select sum(-stock_cant) from logs  
	join stock on stock_log_id=log_id 
	join pedido_log_rev using (log_id) 
	where log_id_pedido=pd1.pedido_id and stock_art_id=pd1.art_id and stock_cant<0),0) AS recepcionado,

	COALESCE((select sum(-stock_cant) from logs  
	join stock on stock_log_id=log_id 
	left join pedido_log_rev AS plog using (log_id) 
	where log_id_pedido=pd1.pedido_id and stock_art_id=pd1.art_id and stock_cant<0 AND plog.pedidolog_id is null),0) AS no_recepcionado,
	
	calcular_stock_trans(art_id, $bodega_origen) AS stock

	FROM pedido_detalle AS pd1 
	JOIN articulo USING (art_id)
	WHERE pedido_id=".($fila[0]*1)."
	ORDER BY art_glosa
	
  ", true);

    
    for($u=0;$u<sizeof($detalle);$u++) {
    
      $art_fila = ($detalle[$u]);

      $pendiente=($art_fila['pedidod_cant']*1)-($art_fila['recepcionado']*1);
      
      if($pendiente<=0) continue;        
    
     //for($a=0;$a<count($art_fila);$a++)  
        //$art_fila[$a]=htmlentities($art_fila[$a]);

        /*$glosa[$u][0]=$art_fila[0];
        $glosa[$u][1]=$art_fila[1];
        $glosa[$u][2]=$art_fila[2];
        $glosa[$u][3]=$art_fila[3];
        $glosa[$u][4]=$art_fila[4];*/
        
        $num=sizeof($glosa);
        
		$glosa[$num][0]=$art_fila['art_codigo'];
		$glosa[$num][1]=$art_fila['art_glosa'];
		$glosa[$num][2]=$pendiente;
		$glosa[$num][3]=$art_fila['art_id']*1;
		$glosa[$num][4]=$art_fila['stock']*1;
        
      
    }


    $respuesta[0]=true;
    $respuesta[1]=$glosa;
    $respuesta[2]=$fila[4];
    $respuesta[3]=$fila[0];
    $respuesta[4]=$fila[5];
    
    $glosa_cadena = json_encode($respuesta);

    print($glosa_cadena);    

?>
