<?php
  require_once('../conectar_db.php');
  $pac_id=$_POST['pac_id']*1;
  $c=cargar_registros_obj("
    ca_pac_id=$pac_id
    ORDER BY ca_patologia
  print(json_encode($c));
?>