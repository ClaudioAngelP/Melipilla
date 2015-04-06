<?php
  set_time_limit(0);

  require_once("../conectar_db.php");

  $bodega=$_GET['bodega_origen']*1;

  if(isset($_GET['_buscar_codigo_interno'])) {
  
  $codigo=$_GET['_buscar_codigo_interno'];

  $art=pg_query($conn,"
  SELECT 
  art_id,
  art_codigo,
  art_glosa, 
  coalesce(sum(stock_cant),0) AS stock,
  art_vence,
  forma_nombre,
  art_val_med
  FROM articulo
  LEFT JOIN bodega_forma ON art_forma=forma_id
  LEFT JOIN stock_precalculado_trans ON
    stock_art_id=art_id AND stock_bod_id=$bodega
  WHERE art_codigo='".pg_escape_string($codigo)."'
  GROUP BY art_id, art_codigo, art_glosa, art_vence, forma_nombre, art_val_med
  ;
  ");
  
  } else {
  
  $codigo=$_GET['_buscar_codigo_barra'];

  $art=pg_query($conn,"
  SELECT 
  art_id,
  art_codigo,
  art_glosa,
  artc_codigo,
  artc_cant, 
  coalesce(sum(stock_cant),0) AS stock,
  art_vence,
  forma_nombre,
  art_val_med
  FROM articulo
  JOIN articulo_codigo USING (art_id)
  LEFT JOIN bodega_forma ON art_forma=forma_id
  LEFT JOIN stock_precalculado_trans ON
    stock_art_id=art_id AND stock_bod_id=$bodega
  WHERE artc_codigo='".pg_escape_string($codigo)."'
  GROUP BY art_id, artc_codigo, artc_cant,art_codigo, art_glosa, art_vence, forma_nombre, art_val_med
  ");
  
  }
  
  if(pg_num_rows($art)==1) {
    $datos=pg_fetch_row($art);
    
    for($i=0;$i<count($datos);$i++) {
      $datos[$i]=htmlentities($datos[$i]);
    }
    
    print(json_encode($datos));
    
  } 
  
?>
