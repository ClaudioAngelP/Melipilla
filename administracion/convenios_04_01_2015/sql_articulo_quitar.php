<?php

  require_once('../../conectar_db.php');

  $art_id=($_GET['art_id']*1);
  $convenio_id=($_GET['convenio_id']*1);
  
  pg_query($conn, "
  DELETE FROM convenio_detalle WHERE
  art_id=".$art_id." AND convenio_id=".$convenio_id."
  ");
  
  print json_encode(true);
  
?>
