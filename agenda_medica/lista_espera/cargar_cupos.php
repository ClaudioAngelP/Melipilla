<?php

  require_once('../../conectar_db.php');
  
  $esp_id=$_GET['esp_id']*1;
  $fecha=pg_escape_string($_GET['fecha']);
  $doc_id=$_GET['doc_id']*1;
  
  $cupos= cargar_registros_obj("
  SELECT asigna_hora, pac_appat, pac_apmat, pac_nombres, control_id, asigna_id
  FROM cupos_asigna
  JOIN cupos_atencion ON cupos_asigna.cupos_id=cupos_atencion.cupos_id
  JOIN interconsulta ON cupos_asigna.inter_id=interconsulta.inter_id
  JOIN pacientes ON inter_pac_id=pacientes.pac_id
  WHERE 
      inter_especialidad=$esp_id AND 
      date_trunc('day', cupos_fecha)='$fecha' AND
      cupos_doc_id=$doc_id
  ", true);
  
  print(json_encode($cupos));

?>
