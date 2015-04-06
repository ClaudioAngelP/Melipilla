<?php

  require_once("../../conectar_db.php");
  
  $equipo_id=$_POST['equipo_id'];
  $observaciones=pg_escape_string(utf8_decode($_POST['observaciones']));
  
  list($equipo)=cargar_registros_obj("
    SELECT * FROM equipos_medicos
    JOIN equipo_medico_clase ON equipo_eclase_id=eclase_id
    WHERE equipo_id=$equipo_id
  ");


  $estados=cargar_registros_obj("
    SELECT * FROM eot_estado_equipo
  ");
  
  $est='';
  
  for($i=0;$i<count($estados);$i++) {
    if(isset($_POST['estado_'.$estados[$i]['eoteseq_id']]))
      $est.=$estados[$i]['eoteseq_id'].',';
  }
  
  if($est!='') $est=substr($est,0,strlen($est)-1);

  if($equipo['equipo_accesorios']!='') {
    $a=explode(',', $equipo['equipo_accesorios']);
  
    $accesorios='';
  
    for($i=0;$i<count($a);$i++) {
      if(isset($_POST['a_'.$i])) $accesorios.=$a[$i].',';
    }
    
    if($accesorios!='')
      $accesorios=substr($accesorios, 0, strlen($accesorios)-1);
  
    $accesorios=pg_escape_string($accesorios);
  } else $accesorios='';

  
  pg_query("
  INSERT INTO equipo_orden_trabajo VALUES (
  DEFAULT,
  current_timestamp,
  '$observaciones', $equipo_id,
  null, null, null, null, null, 0, -1,
  ".($_SESSION['sgh_usuario_id']*1).", 0, 0, '$accesorios', '$est' 
  );
  ");
  
  pg_query("UPDATE equipos_medicos SET equipo_estado=2 WHERE equipo_id=".$equipo_id);
  
  list($eot) = cargar_registros_obj("SELECT CURRVAL('equipo_orden_trabajo_eot_id_seq') AS id;");
  
  exit($eot['id']);

?>
