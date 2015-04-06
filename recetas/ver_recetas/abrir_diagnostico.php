<?php

  require_once('../../conectar_db.php');
  
  $diag_cod = $_GET['diag_cod'];
  
  $diagnosticos = pg_query($conn, "
  SELECT diag_cod, diag_desc
  FROM diagnosticos
  WHERE diag_cod='".pg_escape_string($diag_cod)."'
  ");
  
  if(pg_num_rows($diagnosticos)==1) {
  
    $diagnostico = pg_fetch_row($diagnosticos);
  
    for($i=0;$i<count($diagnostico);$i++) {
      $diagnostico[$i]=htmlentities($diagnostico[$i]);
    }
  
    print(json_encode(Array(true, $diagnostico)));
  
  } else {
  
    print(json_encode(false));
  
  }

?>
