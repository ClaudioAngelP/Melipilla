<?php
  require_once('../conectar_db.php');
  $prof_rut=pg_escape_string($_GET['prof_rut']);
  $r = cargar_registro("
  exit(json_encode($r));
?>