<?php

  require_once('../../conectar_db.php');
  
  $eot_id=$_POST['eot_id']*1;
  $adjuntos=json_decode($_POST['adjuntos']);
  $accion=$_POST['accion'];
  
  if($accion=='evento') {
  
    $estado1=$_POST['estado_actual']*1;
    $estado2=$_POST['estado']*1;
    $observa=pg_escape_string(utf8_decode($_POST['observaciones']));
    $tipodoc=0;

  } else {

    $estado1=0;
    $estado2=0;
    $observa=pg_escape_string(utf8_decode($_POST['observaciones2']));
    $tipodoc=$_POST['tipodoc'];
  
  }

  pg_query("START TRANSACTION;");
  
  pg_query("
    INSERT INTO equipo_orden_evento VALUES (
      DEFAULT, $eot_id, now(), $estado1, $estado2, '$observa', $tipodoc, ".$_SESSION['sgh_usuario_id']."
    );
  ");
  
  for($i=0;$i<count($adjuntos);$i++) {
  
    $archivo=pg_escape_string($adjuntos[$i]);
  
    pg_query("
    INSERT INTO equipo_orden_doc VALUES (
      DEFAULT, CURRVAL('equipo_orden_evento_eevento_id_seq'), '$archivo'
    );
    ");
  
  }

  if($accion=='evento') {
  
    pg_query("
      UPDATE equipo_orden_trabajo SET eot_estado=$estado2 WHERE eot_id=$eot_id;
    ");

    list($eot)=cargar_registros_obj("SELECT * FROM equipo_orden_trabajo WHERE eot_id=$eot_id");

    if($estado2==5) 
      pg_query("
        UPDATE equipos_medicos SET equipo_estado=0 WHERE equipo_id=".$eot['eot_equipo_id']."
      ");
    
  }
  
  pg_query("COMMIT;");
    
  exit('1');

?>
