<?php

  require_once("../../conectar_db.php");

  $bodega_origen=pg_escape_string($_GET['bodega_origen']);
  $nro_pedido=($_GET['numero_pedido']*1);
  
  $buscar_nro = pg_query($conn,
  "
  SELECT * FROM pedido WHERE pedido_nro=$nro_pedido
  ");
  
  if(pg_num_rows($buscar_nro)>0) {
  
	  if(strstr($bodega_origen,'.'))
	  	 $centro=cargar_registro("SELECT * FROM centro_costo 
	  	 									WHERE centro_ruta='$bodega_origen'");
	
	  if(strstr($bodega_origen,'.') AND $centro['centro_stock']=='t') {
	
	    $cond="origen_centro_ruta='$bodega_origen'";
	    $cond2="stock_centro_ruta='$bodega_origen'";
	    $tabla_stock="stock_servicios";
	
	  } else {
	  
	    if(strstr($bodega_origen,'.')) {
	  	$cond="origen_centro_ruta='$bodega_origen'";
	  	$destino = cargar_registro("SELECT * FROM pedido WHERE pedido_nro=".$nro_pedido);
	    	$cond2="stock_bod_id=".$destino['destino_bod_id'];
	    } else {
	  		$cond="origen_bod_id=$bodega_origen";
	    	$cond2="stock_bod_id=$bodega_origen";
	    }
	
	    $tabla_stock="stock";  		
	  		
	  }

  
    $pedidos = pg_query($conn,
    "SELECT 
    pedido.pedido_id, 
    pedido_nro, 
    pedido_fecha, 
    bod_glosa, 
    origen_bod_id, 
    logs.log_id
    FROM pedido 
    JOIN bodega ON bod_id=destino_bod_id
    JOIN logs ON log_id_pedido=pedido.pedido_id
    LEFT JOIN pedido_log_rev ON logs.log_id=pedido_log_rev.log_id
    WHERE
    pedido_nro=$nro_pedido
    AND
    pedidolog_id IS NULL 
    AND
    pedido_estado=1
    "
    );
    
    if(pg_num_rows($pedidos)==0) {
      $respuesta[0]=false;
      $respuesta[1]=false;
      
      exit(json_encode($respuesta));
    }
    
  } else {

    $respuesta[0]=true;
    $respuesta[1]=false;
    exit(json_encode($respuesta));

  }
  
  
    $fila = pg_fetch_row($pedidos);
    
  /*$detalle = pg_query($conn, "
  SELECT *, (cantidad-pedidod_cant) FROM (
  SELECT art_codigo, art_glosa, COALESCE(pedidod_cant,0) AS pedidod_cant,
  art_id, ABS(COALESCE(SUM(stock_cant),0)) AS cantidad, pedidod_estado
  FROM logs 
  LEFT JOIN pedido_detalle ON log_id_pedido=pedido_id 
  JOIN articulo USING (art_id)
  LEFT JOIN $tabla_stock ON stock_log_id=log_id
  AND stock_art_id=art_id
  AND $cond2
  WHERE log_id_pedido=".$fila[0]."
  GROUP BY art_codigo, art_glosa, pedidod_cant, art_id, pedidod_estado
  ) AS v1
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
	where log_id_pedido=pd1.pedido_id and stock_art_id=pd1.art_id and stock_cant<0 AND plog.pedidolog_id is null),0) AS no_recepcionado

	FROM pedido_detalle AS pd1 
	JOIN articulo USING (art_id)
	WHERE pedido_id=".($fila[0]*1)."
	ORDER BY art_glosa;

  ", true);

    
	$glosa=array();    
    
    for($u=0;$u<sizeof($detalle);$u++) {
    
      $art_fila = $detalle[$u];
      
     if($art_fila['pedidod_cant']==0) 
	$pendiente=$art_fila['no_recepcionado']*1;
     else
	$pendiente=($art_fila['pedidod_cant']*1)-($art_fila['recepcionado']*1);
      
      if($pendiente<=0) continue;        
           
      //for($a=0;$a<count($art_fila);$a++)  
	  //  $art_fila[$a]=htmlentities($art_fila[$a]);

	  $num=sizeof($glosa);

      /*$glosa[$num][0]=$art_fila[0];
      $glosa[$num][1]=$art_fila[1];
      $glosa[$num][2]=$art_fila[2];
      $glosa[$num][3]=$art_fila[3];
      $glosa[$num][4]=$art_fila[4];*/

      $glosa[$num][0]=$art_fila['art_codigo'];
      $glosa[$num][1]=$art_fila['art_glosa'];
      $glosa[$num][2]=$pendiente*1;
      $glosa[$num][3]=$art_fila['art_id']*1;
      $glosa[$num][4]=$art_fila['no_recepcionado']*1;
      
    }


    $respuesta[0]=true;
    $respuesta[1]=$glosa;
    $respuesta[2]=$fila[4];
    $respuesta[3]=$fila[0];
    $respuesta[4]=$fila[5];
    
    $glosa_cadena = json_encode($respuesta);

    print($glosa_cadena);    

?>