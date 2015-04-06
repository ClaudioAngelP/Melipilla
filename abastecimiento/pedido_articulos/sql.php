<?php

    require_once("../../conectar_db.php");

    $bodega=pg_escape_string($_GET['bodega']);
    $bodegas_pedidos=$_GET['bodegas_pedidos'];
    $articulos_pedidos=$_GET['articulos_pedidos'];
    $comentarios=iconv("UTF-8", "ISO-8859-1", $_GET['comentarios']);
    
    $bod_ids=split(',',$bodegas_pedidos);
    $art_ids=split('!',$articulos_pedidos);
    
    
    if(count($bod_ids)>1) {
      die(json_encode(Array(false, 'Est&aacute; solicitando art&iacute;culos de distintos destinos en un mismo pedido.')));
    }
    
     if(strstr($bodega,'.')) {
      $bodega_origen='0';
      $centro_ruta=$bodega;
     } else {
      $bodega_origen=$bodega*1;
      $centro_ruta='';
     }
    
    
    pg_query($conn, 'START TRANSACTION;');
    
    foreach($bod_ids as $bod_id) {
    
    if(_func_permitido(29,$bodega) OR 
        _func_permitido_cc(29,$centro_ruta))
      $autoriza='true'; else $autoriza='false';
    
    pg_query($conn, "
    INSERT INTO pedido VALUES(
    DEFAULT,
    nextval('global_numero_pedido'),
    current_timestamp,
    ".($_SESSION['sgh_usuario_id']*1).",
    0,
    $bodega_origen,
    $bod_id,
    '$comentarios',
    0,
    $autoriza, false, '$centro_ruta'
    )
    ");
    
    for($i=0;$i<count($art_ids);$i++) {
      
      $art = split('-',$art_ids[$i]);
      
      $art_bod_id=$art[0]*1;
      $art_id=$art[1]*1;
      $art_cant=$art[2]*1;
      
      if($art_bod_id==$bod_id) {
        pg_query($conn, "
        INSERT INTO pedido_detalle VALUES(
        DEFAULT,
        CURRVAL('pedido_pedido_id_seq'),
        $art_id,
        $art_cant
        )
        ");
      }
    }
    
    }
    
    $id_p=pg_query($conn, "SELECT currval('pedido_pedido_id_seq');");
    $id_parr=pg_fetch_row($id_p);
    $id_pedido=$id_parr[0];
    
    pg_query($conn, 'COMMIT;');
    
    print(json_encode(Array(true, $id_pedido)));
    
?>
