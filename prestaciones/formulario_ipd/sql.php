<?php

  require_once('../../conectar_db.php');

  $folio=$_POST['nro_folio']*1;
  if($folio==0) $folio=-1;
  
  $pac_id=$_POST['paciente_id']*1;
  $esp_id=$_POST['esp_id']*1;
  $pat_id=($_POST['pat_id']);
  $patrama_id=$_POST['patrama_id']*1;
  $confirma=($_POST['confirma']=='t')?'true':'false';
  $diagnostico=pg_escape_string(utf8_decode($_POST['diag_cod']));
  $fundamentos=pg_escape_string(utf8_decode($_POST['ipd_fundamentos']));
  $tratamiento=pg_escape_string(utf8_decode($_POST['ipd_tratamiento']));
  $fecha_t=pg_escape_string($_POST['ipd_fecha_tratamiento']);
  
	if(stristr($pat_id, 'G')) {
    $garantia_id=substr($pat_id, 1, strlen($pat_id))*1; $pat_id='0';
  } else {
    $pat_id=substr($pat_id, 1, strlen($pat_id))*1; $garantia_id=0;
  }

  
/*
  Tabla formulario_ipd:
  ---------------------

  ipd_id bigserial NOT NULL,
  ipd_folio bigint,
  ipd_fecha timestamp without time zone,
  ipd_fecha_ingreso timestamp without time zone,
  ipd_esp_id bigint,
  ipd_pac_id bigint,
  ipd_pat_id bigint,
  ipd_patrama_id bigint,
  ipd_inter_id bigint,
  ipd_confirma boolean DEFAULT false,
  ipd_diagnostico text,
  ipd_fundamentos text,
  ipd_tratamiento text,
  ipd_fecha_tratamiento date,
  ipd_doc_id bigint,
*/

  $ic=cargar_registros_obj("
    SELECT * FROM interconsulta 
    WHERE 
      inter_pac_id=$pac_id AND 
      inter_estado=1 AND 
      inter_pat_id=$pat_id;
  ");
  
  if($ic) $inter_id=$ic[0]['inter_id']; else $inter_id=0;
  
  if($inter_id!=0) {
    if($confirma=='true') $ep_estado=1; else $ep_estado=2;
    pg_query($conn, "
      UPDATE episodio_clinico       SET ep_patrama_id=$patrama_id, ep_estado=$ep_estado 
      WHERE ep_inter_id=$inter_id  
    ");
    
  }

  pg_query($conn, "
  INSERT INTO formulario_ipd VALUES (
    DEFAULT, $folio, now(), now(), $esp_id,
    $pac_id, $pat_id, $patrama_id, $inter_id, $confirma,
    '$diagnostico','$fundamentos','$tratamiento', 
    '$fecha_t', 0
  );
  ");
  
  if(!$confirma) {
  
    // Si descarta Caso AUGE termina I.C. y Caso
    
    // Finaliza I.C.
  
    pg_query($conn, "
      UPDATE interconsulta SET inter_estado=4 WHERE inter_id=$inter_id
    ");

    // Finaliza Caso Cl�nico
    
    pg_query($conn, "
      UPDATE episodio_clinico SET ep_estado=2 WHERE ep_inter_id=$inter_id
    ");
  }
  exit('OK');
?>