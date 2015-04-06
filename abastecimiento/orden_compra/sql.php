<?php

  require_once('../../conectar_db.php');
  
  $orden_nro = $_POST['orden_numero'];
  $orden_prov_id = $_POST['orden_prov_id']*1;
  $orden_pedido_id = $_POST['pedido_id']*1;
  
  if(isset($_POST['ivaincl'])) {
    $iva_incl = true;
  } else {
    $iva_incl = false;
  }
  
  $orden_obs = pg_escape_string($_POST['orden_observacion']);
  
  if(trim($orden_nro)=='') {
    
    $orden_nro="null";
    
  } else {
  
    $chequear = cargar_registros_obj("
    SELECT * FROM orden_compra 
    WHERE orden_numero='".pg_escape_string($orden_nro)."'
    ");
    
    if($chequear) {
    
      die('Orden de Compra ya fue ingresada.');
    
    }
    
    $orden_nro="'".pg_escape_string($orden_nro)."'";
  
  }
  
  $det=json_decode($_POST['det']);
  
  pg_query($conn, "START TRANSACTION;");
  
  pg_query($conn, "
  INSERT INTO orden_compra VALUES (
  DEFAULT,
  $orden_nro,
  '',
  now(),
  $orden_prov_id,
  ".($_SESSION['sgh_usuario_id']*1).",
	0,
  '$orden_obs',
  $_global_iva
  );
  ");
  
  for($i=0;$i<count($det);$i++) {
  
    if($det[$i]->id!=0) {

      if($iva_incl) $subtotal=($det[$i]->cantidad*$det[$i]->valor)/$_global_iva;
      else          $subtotal=($det[$i]->cantidad*$det[$i]->valor);
    
      pg_query($conn, "
      INSERT INTO orden_detalle VALUES (
      DEFAULT,
      CURRVAL('orden_compra_orden_id_seq1'),
      ".$det[$i]->id.",
      ".$det[$i]->cantidad.",
      ".$subtotal."
      );
      ");

    } else {

      if($iva_incl) $subtotal=($det[$i]->cantidad*$det[$i]->valor)/$_global_iva;
      else          $subtotal=($det[$i]->cantidad*$det[$i]->valor);

      pg_query($conn, "
      INSERT INTO orden_servicios VALUES (
      DEFAULT,
      CURRVAL('orden_compra_orden_id_seq1'),
      '".pg_escape_string(utf8_decode($det[$i]->glosa))."',
      ".$subtotal.",
      '".pg_escape_string($det[$i]->item_codigo)."',
      ".$det[$i]->cantidad."
      );
      ");

    
    }
    
  }
  
  pg_query($conn,"
    INSERT INTO orden_pedido 
    VALUES (CURRVAL('orden_compra_orden_id_seq1'), $orden_pedido_id)
  ");

  pg_query($conn, "COMMIT;");
  
  $id=cargar_registro("SELECT CURRVAL('orden_compra_orden_id_seq1') AS id;");

  die(json_encode(Array(true,$id['id'])));

?>
