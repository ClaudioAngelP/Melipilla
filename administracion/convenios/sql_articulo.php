<?php

  require_once('../../conectar_db.php');

  $art_id=($_GET['art_id']*1);
  $convenio_id=($_GET['convenio_id']*1);
  $punit=$_GET['conveniod_punit'];
  $conveniod_cant=$_GET['conveniod_cant'];

  $comprobar = pg_query($conn,"
  SELECT * 
  FROM 
  convenio_detalle 
  JOIN convenio USING (convenio_id)
  WHERE art_id=$art_id AND convenio_id=$convenio_id
  ");
  
  if(pg_num_rows($comprobar)==0) {
  
    pg_query($conn, "INSERT INTO convenio_detalle VALUES (".$convenio_id.", ".$art_id.", DEFAULT, $punit,null,null,null,null,$conveniod_cant)");
  
    print(json_encode(Array(true,true)));
  
  } else {
  
    $convenio = pg_fetch_assoc($comprobar);
    
    $conveniod_id=$convenio['conveniod_id'];
    
    pg_query("UPDATE convenio_detalle SET
    conveniod_punit=$punit
    WHERE conveniod_id=$conveniod_id");
    
    print(json_encode(Array(true,true)));
  
  }

?>
