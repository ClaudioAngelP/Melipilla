<?php

  require_once('conectar_db.php');
  
  if(isset($_POST['pedido_id'])) {

    $pedido_id=$_POST['pedido_id']*1;
    $bod_id=$_POST['bodega_destino']*1;
  
    if(isset($_POST['anular'])) {
      pg_query("
        UPDATE pedido SET pedido_estado=3 WHERE pedido_id=$pedido_id
      ");
      die(json_encode(true));  
    }
    
    /*pg_query("
      UPDATE pedido SET destino_bod_id=$bod_id WHERE pedido_id=$pedido_id
    ");*/
    
    $detalle=cargar_registros_obj("
      SELECT * FROM pedido_detalle WHERE pedido_id=$pedido_id
    ");
    
    $ndet = json_decode($_POST['listado']);
    
    if($detalle)
    for($i=0;$i<count($detalle);$i++) {
    
      $encontrado=false;
    
      for($j=0;$j<count($ndet);$j++) {
        if($detalle[$i]['art_id']==$ndet[$j]->art_id) { 
          if($detalle[$i]['pedidod_cant']!=$ndet[$j]->pedidod_cant) {
                pg_query("UPDATE pedido_detalle 
                          SET 
                          pedidod_cant=".($ndet[$j]->pedidod_cant*1)."
                          WHERE pedido_id=$pedido_id AND art_id=
                          ".$ndet[$j]->art_id);
                $encontrado=true; break;
          } else {
                $encontrado=true; break;
          }
        }
      }
      
      if(!$encontrado)
                pg_query("DELETE FROM pedido_detalle 
                          WHERE pedido_id=$pedido_id AND 
                          art_id=".$detalle[$i]['art_id']);

    }
    
    for($i=0;$i<count($ndet);$i++) {
    
      $encontrado=false;
    
      for($j=0;$j<count($detalle);$j++) {
        if($ndet[$i]->art_id==$detalle[$j]['art_id']) {
          $encontrado=true; break;
        }
      }
      
      if(!$encontrado)
        pg_query("
        INSERT INTO pedido_detalle VALUES (
        DEFAULT,
        $pedido_id,
        ".$ndet[$i]->art_id.",
        ".$ndet[$i]->pedidod_cant.",
        false
        )
        ");    
      
    }
    
  
    die(json_encode(true));
  
  }

?>
