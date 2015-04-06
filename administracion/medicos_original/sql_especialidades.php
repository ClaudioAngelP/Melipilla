<?php

  require_once('../../conectar_db.php');
  
  $esp_id=$_POST['esp_id']*1;
  $doc_id=$_POST['doc_id']*1;
  $val=$_POST['val'];
  
  if($val=='true') {
  
    $chk = cargar_registro("
      SELECT * FROM especialidad_doctor WHERE esp_id=$esp_id AND doc_id=$doc_id
    ");
    
    if(!$chk)
      pg_query("INSERT INTO especialidad_doctor VALUES ($doc_id, $esp_id)");
  
  } else {
  
    pg_query("DELETE FROM especialidad_doctor WHERE esp_id=$esp_id AND doc_id=$doc_id");
  
  }

?>
