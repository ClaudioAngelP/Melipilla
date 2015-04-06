<?php

  require_once('../conectar_db.php');
  
  $func_rut=pg_escape_string($_GET['func_rut']);
  
  $nombre=pg_query(
  "SELECT doc_id, doc_paterno || ' ' || doc_materno || ' ' || doc_nombres 
  FROM doctores WHERE doc_rut='".$func_rut."'");
  
  if(pg_num_rows($nombre)) {
    $nom = pg_fetch_row($nombre);
    print(json_encode(Array($nom[0], htmlentities($nom[1]))));
  } else {
    print(json_encode(Array(0,htmlentities("No Encontrado"))));
  }

?>
