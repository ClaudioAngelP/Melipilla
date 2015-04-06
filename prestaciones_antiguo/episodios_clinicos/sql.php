<?php

  require_once('../../conectar_db.php');
  
  $ep_id=$_POST['ep_id']*1;
  $pac_id=$_POST['pac_id']*1;
  $fecha1=pg_escape_string($_POST['fecha1']);
  
  $indef=isset($_POST['epvigente']);
  if(!$indef) {
    $fecha2="'".pg_escape_string($_POST['fecha2'])."'";
  } else {
    $fecha2='null';
  }
  
  $pat_id=$_POST['pat_id']*1;
  
  if($pat_id!=0) $detpat_id=$_POST['detpat_id']*1;
  else $detpat_id=0;
  
  if($ep_id==0) {
    pg_query($conn, "
    INSERT INTO episodio_clinico VALUES (
    DEFAULT, $pac_id, now(), '$fecha1', $fecha2, $pat_id, $detpat_id, 0
    );
    ");
  } else {
    pg_query($conn, "
    UPDATE episodio_clinico SET
    ep_pac_id=$pac_id,
    ep_fecha_inicio='$fecha1',
    ep_fecha_alta=$fecha2,
    ep_pat_id=$pat_id,
    ep_detpat_id=$detpat_id
    WHERE ep_id=$ep_id
    ");  
  }
  
  print(json_encode(true));
  

?>
