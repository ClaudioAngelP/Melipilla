<?php

  require_once('../../conectar_db.php');
  
  $bod = ($_POST['pedido_bodega']*1);
  $p_cant = ($_POST['pedido_cant']*1);
  
  pg_query($conn, "START TRANSACTION;");
  
  $nros_pedidos='';

  if(_func_permitido(29,$bod))
    $autoriza='true'; else $autoriza='false';

  
  for($i=0;$i<$p_cant;$i++) {
    
    $p_detalle = explode('!',$_POST['pedido_'.$i]);
    for($u=0;$u<count($p_detalle)-1;$u++) {
      $temp = $p_detalle[$u];
      $p_detalle[$u]=explode('==',$temp);
    }
    
    $query="
    INSERT INTO pedido VALUES (
    DEFAULT,
    nextval('global_numero_pedido'),
    current_timestamp,
    ".($_SESSION['sgh_usuario_id']*1).",
    0,
    $bod,
    0,
    '',
    0,
    $autoriza
    )
    ";
        
    $pedido = pg_query($conn, $query);

    for($u=0;$u<count($p_detalle)-1;$u++) {
        pg_query($conn, "
        INSERT INTO pedido_detalle VALUES(
        DEFAULT,
        CURRVAL('pedido_pedido_id_seq'),
        ".$p_detalle[$u][0].",
        ".$p_detalle[$u][1]."
        )
        ");
    }
    
    $nro_pedido=cargar_registro("
    SELECT pedido_nro, date_trunc('second', pedido_fecha) AS pedido_fecha 
    FROM pedido 
    WHERE pedido_id=CURRVAL('pedido_pedido_id_seq')");

    $nros_pedidos[$i] = $nro_pedido;

  }
  
  pg_query('COMMIT;');
  
  print(json_encode($nros_pedidos));
  
?>
