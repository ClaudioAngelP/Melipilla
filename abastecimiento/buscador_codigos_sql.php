<?php

  require_once("../conectar_db.php");


  $codigo_interno = $_GET['_buscar_codigo_interno_asociar'];
  $codigo_barras = $_GET['_buscar_codigo_barras_asociar'];
  $cantidad = $_GET['_buscar_cantidad_asociar']*1;
  $formato = $_GET['_buscar_formato_asociar']*1;
  
  // Verifica que codigo de barras no exista en db.
  
  $ver_barras = pg_query($conn, "
  SELECT artc_codigo, art_glosa, art_codigo FROM articulo
  JOIN articulo_codigo USING (art_id)
  WHERE artc_codigo='".pg_escape_string($codigo_barras)."'
  ");
  
  if(pg_num_rows($ver_barras)>0) {
    $datos = pg_fetch_row($ver_barras);
    die('C&oacute;digo de Barras ['.$datos[0].'] est&aacute; actualmente siendo utilizado para art&iacute;culo ['.$datos[1].' - Cod. Int: '.$datos[2].'].
    ');
  }
  
  // Busca art_id en tabla de articulos.
  
  $buscar_art = pg_query("
  SELECT art_id FROM articulo WHERE art_codigo='".pg_escape_string($codigo_interno)."'
  ");
  
  if(pg_num_rows($buscar_art)==0) {
    die('C&oacute;digo Interno inexistente.');
  }
  
  $arts=pg_fetch_row($buscar_art);
  
  $art_id=$arts[0];
  
  // Ingresa asociación a la db.
  
  pg_query("INSERT INTO articulo_codigo VALUES(
  DEFAULT,
  $art_id,
  '".pg_escape_string($codigo_barras)."',
  $cantidad,
  $formato,
  0
  )");
  
  print('OK');


?>
