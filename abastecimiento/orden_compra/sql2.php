<?php

  require_once('../../conectar_db.php');
  $ordenes = json_decode($_POST['ordenes']);

  if(isset($_POST['ivaincl'])) {
    $iva_incl = true;
  } else {
    $iva_incl = false;
  }
  
  error_reporting(E_ALL);
  
  pg_query('START TRANSACTION;');

  
    for($i=0;$i<count($ordenes);$i++) {
  
    $nro_orden=$ordenes[$i]->numero_orden;
    $nro_orden_esc=pg_escape_string($ordenes[$i]->numero_orden);
    $pord = cargar_registros_obj("SELECT * FROM orden_compra WHERE orden_numero='$nro_orden_esc'");
    
    if($pord){die(json_encode(Array(false,'Orden de Compra '.$nro_orden.' ya fue ingresada.')));}
    
    $prov_id=cargar_registros_obj("
    SELECT prov_id FROM proveedor 
    WHERE prov_rut='".$ordenes[$i]->proveedor->rut."'
    ");
  
    if(!$prov_id) {
    
      // Insertar proveedor si no existe.
      
			pg_query($conn,"
			INSERT INTO proveedor
			VALUES (
			DEFAULT,
			'".pg_escape_string($ordenes[$i]->proveedor->rut)."',
			'".pg_escape_string($ordenes[$i]->proveedor->nombre)."',
			'',
			'',
			'".pg_escape_string($ordenes[$i]->proveedor->telefono)."',
			'',
			'".pg_escape_string($ordenes[$i]->proveedor->mail)."'
			)
			");
			
			$prov_id="CURRVAL('proveedor_prov_id_seq')";
			
    } else {
      
      $prov_id= $prov_id[0]['prov_id']*1;
    
    }
    if($iva_incl==true)
    {
        pg_query($conn,"INSERT INTO orden_compra VALUES (DEFAULT,'$nro_orden_esc', '', now(),
        $prov_id, ".($_SESSION['sgh_usuario_id']*1).", 0, '', 1.0);");
    }
    else
    {
        pg_query($conn,"INSERT INTO orden_compra VALUES (DEFAULT,'$nro_orden_esc', '', now(),
        $prov_id, ".($_SESSION['sgh_usuario_id']*1).", 0, '', $_global_iva);");
    }
    
    foreach($ordenes[$i]->pedidos AS $nro_pedido) {
    
      // Buscar ID del Pedido
      
      $pedido=cargar_registros_obj('
          SELECT pedido_id, pedido_tramite FROM pedido
          WHERE 
          pedido_nro='.($nro_pedido*1).'
      ');
      
      // Inserta IDs asociando orden y pedido
      
      if($pedido) {
      
        $pid = $pedido[0]['pedido_id'];
        
        if($pedido[0]['pedido_tramite']=='t') {
           
          pg_query($conn,"
          INSERT INTO orden_pedido 
          VALUES (CURRVAL('orden_compra_orden_id_seq1'), $pid)
          ");
        
        } else {
        
          die(json_encode(Array(false, '&Oacute;rden de Compra '.$nro_orden.' apunta a pedido que a&uacute;n no se tramita en el portal. (Pedido #'.$nro_pedido.' no est&aacute; debidamente marcado en tr&aacute;mite.)')));
        
        }
      
      } else {
      
        //die(json_encode(Array(false, '&Oacute;rden de Compra '.$nro_orden.' apunta a pedido inexistente. (Pedido #'.$nro_pedido.' no existe.)')));
      
      }
    
    }

    
    $arts = $ordenes[$i]->articulos;
    
    for($n=0;$n<count($arts);$n++) {
    
        // Artículos
        
        list($art_id) = cargar_registros_obj("
          SELECT * FROM articulo 
          WHERE art_codigo='".pg_escape_string(($arts[$n]->codigo))."'
        ");
        
        $cant=str_replace('.','',$arts[$n]->cantidad);
        $cant=str_replace(',','.',$cant);
        
        pg_query($conn, "
        INSERT INTO orden_detalle VALUES (
        DEFAULT,
        CURRVAL('orden_compra_orden_id_seq1'),
        ".$art_id['art_id'].",
        ".pg_escape_string($cant).",
        ".($arts[$n]->subtotal*1)."
        );
        ");
        
    }
    
    $serv = $ordenes[$i]->servicios;
    
    for($n=0;$n<count($serv);$n++) {
    
      // Servicios
      
        $art_id=((string)($serv[$n]->art_id))*1;
        
        $glosa=pg_escape_string(trim(utf8_decode((string)($serv[$n]->glosa))));
		$cantserv=pg_escape_string(utf8_decode((string)($serv[$n]->cantidad)));
			
        if($art_id==0) {
        
			$item=pg_escape_string(utf8_decode((string)($serv[$n]->item)));
			pg_query($conn,"
			INSERT INTO orden_servicios VALUES (
			DEFAULT,
			CURRVAL('orden_compra_orden_id_seq1'),
			'".($glosa)."',
			".($serv[$n]->subtotal*1).",'$item',
			'".$cantserv."'
			);
			");
        
		} else {
			
			
			
			$chk=cargar_registro("SELECT * FROM articulo_nombres WHERE artn_nombre='$glosa';");
			
			if(!$chk) {
				pg_query("INSERT INTO articulo_nombres VALUES (DEFAULT, $art_id, '$glosa');");
			} 
		
			pg_query($conn, "
			INSERT INTO orden_detalle VALUES (
			DEFAULT,
			CURRVAL('orden_compra_orden_id_seq1'),
			".$art_id.",
			".$cantserv.",
			".($serv[$n]->subtotal*1)."
			);
			");		
		
		}

    }
    
  }

  pg_query($conn, "COMMIT;");
  die(json_encode(Array(true,'')));

?>
