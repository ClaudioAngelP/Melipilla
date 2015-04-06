<?php

  require_once('../../conectar_db.php');
  
  $inter_id=$_POST['inter_id']*1;
  $control_id=$_POST['control_id']*1;

  if(isset($_POST['eliminar']))
    pg_query("
        UPDATE interconsulta SET inter_estado=10 WHERE inter_id=$inter_id;
    ");
  
  if(!isset($_POST['asigna_id'])) {
  
    $cupos_id=$_POST['id']*1;
  
    $hr=$_POST['hr']*1;
    $min=$_POST['min']*1;
  
    $hora="'".$hr.':'.$min.":00'";

  } else {
  
    pg_query("
      DELETE FROM cupos_asigna 
      WHERE asigna_id=".($_POST['asigna_id']*1)."
    ");
    
    exit(json_encode(true));
  
  }
  
  $chk=cargar_registro("
    SELECT * FROM cupos_asigna 
    WHERE inter_id=$inter_id AND control_id=$control_id
  ");
  
  if($chk)
    pg_query("
      DELETE FROM cupos_asigna 
      WHERE inter_id=$inter_id AND control_id=$control_id
    ");
  
  pg_query($conn,
  "
  INSERT INTO cupos_asigna VALUES (
  DEFAULT,
  $inter_id,
  $cupos_id,
  $hora, $control_id, null, null
  )
  ");
  
  $id=cargar_registro("SELECT CURRVAL('cupos_asigna_asigna_id_seq') AS id");
  
  print(json_encode(array(true,$id['id'])));

?>
