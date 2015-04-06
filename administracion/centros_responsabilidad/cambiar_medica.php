<?php

  require_once('../../conectar_db.php');
  
  $nodo = $_GET['nodo'];
  $estado = $_GET['estado'];
  
  ($estado=='1')?$estado='true':$estado='false';
  
  pg_query($conn, "
  UPDATE centro_costo SET centro_medica=".$estado." 
  WHERE centro_ruta='".pg_escape_string($nodo)."';
  ");

  print('OK');
  
?>

