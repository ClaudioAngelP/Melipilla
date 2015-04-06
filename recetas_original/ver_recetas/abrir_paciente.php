<?php

  require_once('../../conectar_db.php');
  
  $pac_rut = $_GET['pac_rut'];
  
  $pacientes = pg_query($conn, "
  SELECT pac_id, pac_appat || ' ' || pac_apmat || ' ' || pac_nombres
  FROM pacientes
  WHERE pac_rut='".pg_escape_string($pac_rut)."'
  ");
  
  if(pg_num_rows($pacientes)>=1) {
  
    $paciente = pg_fetch_row($pacientes);
  
    for($i=0;$i<count($paciente);$i++) {
      $paciente[$i]=htmlentities($paciente[$i]);
    }
  
    print(json_encode(Array(true, $paciente)));
  
  } else {
  
    print(json_encode(false));
  
  }

?>
