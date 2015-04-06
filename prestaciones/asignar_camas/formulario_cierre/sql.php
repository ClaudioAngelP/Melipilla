<?php

  require_once('../../conectar_db.php');

  if($_GET['tipo']=='cc') 
    $tabla='formulario_ccaso'; 
  else 
    $tabla='formulario_excepcion';

  $folio=$_POST['nro_folio']*1;
  if($folio==0) $folio=-1;
  
  $pac_id=$_POST['paciente_id']*1;
  $esp_id=$_POST['esp_id']*1;
  $pat_id=$_POST['pat_id'];
  $patrama_id=$_POST['patrama_id']*1;
  $causal=$_POST['causal'];
  
  $ct=explode('.', $causal);
  
  if(count($ct)>1) {
    $causal=$ct[0]*1; $subcausal=$ct[1]*1;
  } else {
    $causal=$causal*1; $subcausal=0;
  }
  
  $fecha_pa=isset($_POST['fecha_parto'])?"'".pg_escape_string($_POST['fecha_parto'])."'":'null';
  $fecha_def=isset($_POST['fecha_defuncion'])?"'".pg_escape_string($_POST['fecha_defuncion'])."'":'null';
  $semanas=isset($_POST['semanas_gesta'])?($_POST['semanas_gesta']*1):'0';
  
  $diagnostico=pg_escape_string(utf8_decode($_POST['diag_cod']));
  $observaciones=pg_escape_string(utf8_decode($_POST['cierre_observaciones']));
  $docu=isset($_POST['documento'])?$_POST['documento']:'0';
  
	if(stristr($pat_id, 'G')) {
    $garantia_id=substr($pat_id, 1, strlen($pat_id))*1; $pat_id='0';
  } else {
    $pat_id=substr($pat_id, 1, strlen($pat_id))*1; $garantia_id=0;
  }

  
/*
  Tabla formulario_ccaso:
  ---------------------

  ccaso_id bigserial NOT NULL,
  ccaso_folio bigint,
  ccaso_inter_id bigint,
  ccaso_pac_id bigint,
  ccaso_fecha timestamp without time zone,
  ccaso_fecha_ingreso timestamp without time zone,
  ccaso_pat_id bigint,
  ccaso_patrama_id bigint,
  ccaso_causal smallint,
  ccaso_subcausal smallint,
  ccaso_diag_cod character varying(10),
  ccaso_fecha_pa timestamp without time zone,
  ccaso_semanas_gestacion smallint,
  ccaso_fecha_defuncion timestamp without time zone,
  ccaso_observaciones text,
  ccaso_doc_id bigint

*/

  // Busca Interconsulta asociada al Caso

  $query="
    SELECT * FROM interconsulta 
    WHERE 
      inter_pac_id=$pac_id AND 
      inter_estado=1 AND 
      inter_pat_id=$pat_id;
  ";
  
  $ic=cargar_registros_obj($query);
  
  $inter_id=$ic[0]['inter_id']; $esp_id=$ic[0]['inter_especialidad'];
  
  // Busca Caso AUGE asociado a la I.C.
  
  list($ep_id)=cargar_registros_obj("
    SELECT ep_id FROM episodio_clinico WHERE ep_inter_id=$inter_id
  ");

  $ep_id=$ep_id['ep_id'];

  // Ingresa Formulario de Cierre/Excepción

  pg_query($conn, "
    INSERT INTO $tabla VALUES (
      DEFAULT, $folio, $esp_id, $inter_id, $pac_id, now(), now(),
      $pat_id, $patrama_id, $causal, $subcausal, '$diagnostico',
      $fecha_pa, $semanas, $fecha_def, '$observaciones', 0
    );
  ");
  
  // Finaliza I.C.
  
  pg_query($conn, "
    UPDATE interconsulta SET inter_estado=4 WHERE inter_id=$inter_id
  ");
  
  // Finaliza Caso Clínico
  
  pg_query($conn, "
    UPDATE episodio_clinico SET ep_estado=4 WHERE ep_id=$ep_id
  ");
  
  exit('OK');

?>
