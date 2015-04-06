<?php

  require_once('../../conectar_db.php');

  $pat_id=$_POST['pat_id']*1;

  if(isset($_POST['eliminar'])) {

    $patrama_id=$_POST['eliminar']*1;
    
    pg_query("DELETE FROM patologias_auge_ramas WHERE patrama_id=$patrama_id");
  
  } else {

    $nombre=pg_escape_string($_POST['rama_nombre']);  
    
    pg_query("
      INSERT INTO patologias_auge_ramas VALUES (
      DEFAULT, $pat_id, '$nombre'
      );
    ");
  
  }
  
  $ramas=cargar_registros_obj("
    SELECT * FROM patologias_auge_ramas 
    WHERE pat_id=$pat_id ORDER BY rama_nombre
  ");

  exit(json_encode($ramas));

?>
