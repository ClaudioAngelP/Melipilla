<?php

  require_once('../../conectar_db.php');

  $pedidod=$_POST['pedidod_id']*1;
  $pedido=$_POST['pedido_id']*1;
  $valor=$_POST['val']*1;
  
  
  if($valor) $tramite='true';
  else       $tramite='false';
  
  pg_query($conn, "
  UPDATE pedido_detalle
  SET 
  pedidod_tramite=$tramite
  WHERE 
  pedidod_id=$pedidod
  ");

  $chk=cargar_registro("SELECT * FROM pedido_detalle WHERE pedido_id=$pedido AND pedidod_tramite");
  
  if($tramite=='true' OR ($tramite=='false' AND !$chk))
  	pg_query($conn, "UPDATE pedido
  				SET pedido_tramite=$tramite
  			WHERE pedido_id=$pedido");

?>
