<?php

  require_once('../../conectar_db.php');

  $art_id=($_GET['art_id']*1);
  $autf_id=($_GET['autf_id']*1);
  $presta=pg_escape_string($_GET['presta']);
  
  $comprobar = pg_query($conn,"
  SELECT * 
  FROM 
  autorizacion_farmacos_detalle 
  JOIN autorizacion_farmacos USING (autf_id)
  WHERE art_id=$art_id AND autf_id=$autf_id
  ");
  
  if(pg_num_rows($comprobar)==0) {
  
    pg_query($conn, "
    INSERT INTO autorizacion_farmacos_detalle
    VALUES (DEFAULT, ".$autf_id.", ".$art_id.", '$presta')
    ");
  
    print(json_encode(Array(true,true)));
  
  } 

?>
