<?php

  require_once('../../conectar_db.php');
  
  $data=cargar_registros_obj("
    SELECT * FROM especialidades ORDER BY esp_desc
  ",true);
  
  print(json_encode($data));

?>
