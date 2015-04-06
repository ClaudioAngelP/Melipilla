<?php

  require_once('../../conectar_db.php');

  $bodega_id=$_POST['bodega_id'];
  $observaciones=pg_escape_string(utf8_decode($_POST['observaciones']));
  
  $tipo=$_POST['tipo']*1;
  isset($_POST['urgente']) ? $urgente='true' : $urgente='false';
  
  $arts=json_decode($_POST['articulos']);
  $arcs=json_decode($_POST['archivos']);
  
  if(strstr($bodega_id,'.')) {
    $centro_ruta=pg_escape_string($bodega_id);
    $bodega_id=-1;
  } else {
    $bodega_id=$bodega_id*1;
    $centro_ruta='';
  }
  
  if($tipo==1) {

	  $referente=pg_escape_string(utf8_decode($_POST['referente_tecnico']));
  	  $precio_ref=pg_escape_string(utf8_decode($_POST['precio_referencial']))*1;

    $archivos='ARRAY[';
    for($i=0;$i<count($arcs);$i++) {
      $archivos.="'".pg_escape_string(trim($arcs[$i]->nombre))."'";
      if($i!=(count($arcs)-1)) $archivos.=',';
    }
    $archivos.=']';
    
    $item_codigo=pg_escape_string($_POST['item_codigo']);

  } else {
  
		$referente='';
		$precio_ref='';  
  
    	$archivos='null';

    	$item_codigo='';
  
  }
  
	$fecha_uso=$_POST['fecha_uso'];
	
	if(trim($fecha_uso)=='')
		$fecha_uso='null';
	else 
		$fecha_uso="'$fecha_uso'";  
  
  pg_query($conn, "
    INSERT INTO solicitud_compra VALUES (
    default,
    $bodega_id, '$centro_ruta', now(),
    ".($_SESSION['sgh_usuario_id']*1).",
    null, null, null, null, $urgente, 1, null, 
    '$observaciones', $archivos, $tipo, 0, 
    $precio_ref, '', 0, '$referente', $fecha_uso, null, '$item_codigo'
    )
  ");
  
  if($tipo==0) {
  
    for($i=0;$i<count($arts);$i++) {
    
      $art=cargar_registro("
      SELECT * FROM articulo WHERE art_id=".$arts[$i]->id."
      ");
    
      pg_query($conn, "
      INSERT INTO solcompra_detalle VALUES (
      default,
      ".$arts[$i]->id.",
      ".$arts[$i]->cantidad.",
      ".$arts[$i]->cantidad*$art['art_val_ult'].",
      CURRVAL('solicitud_compra_sol_id_seq')
      ) 
      
      ");
    
    }
  
  }
    
  print(json_encode(true));

?>