<?php

  require_once('../conectar_db.php');
  
  $prof_rut=pg_escape_string($_GET['prof_rut']);
  
  $r = cargar_registro("
    SELECT * FROM profesionales_externos WHERE prof_rut='$prof_rut'
  ");
  
  exit(json_encode($r));

?>