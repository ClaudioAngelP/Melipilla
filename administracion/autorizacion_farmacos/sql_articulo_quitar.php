<?php

  require_once('../../conectar_db.php');

  $art_id=($_GET['art_id']*1);
  $autf_id=($_GET['autf_id']*1);
  
  pg_query($conn, "
  DELETE FROM autorizacion_farmacos_detalle WHERE
  art_id=".$art_id." AND autf_id=".$autf_id."
  ");
  
  print json_encode(true);
  
?>
