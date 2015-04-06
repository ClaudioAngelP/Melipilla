<?php

  require_once('../../conectar_db.php');

  $art_codigo = $_GET['art_codigo'];

  $articulos = pg_query($conn, "
  SELECT art_id, art_codigo, art_glosa 
  FROM articulo
  WHERE art_codigo='".pg_escape_string($art_codigo)."'
  ");
  
  if(pg_num_rows($articulos)>0) {
  
    $articulo = pg_fetch_row($articulos);
  
    for($i=0;$i<count($articulo);$i++) {
      $articulo[$i]=htmlentities($articulo[$i]);
    }
    
    $respuesta=Array(true,$articulo);
    
    print(json_encode($respuesta));
    
  } else {
  
    print(json_encode(Array(false,false)));
  
  }

?>