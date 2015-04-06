<?php

  require_once('../../conectar_db.php');
  
  $detpat_padre_id=$_POST['detpat_padre_id']*1;
  $detpat_id=$_POST['detpat_id']*1;
  $etapa=$_POST['etapa']*1;
  
  // Traslado de Nodos...
  
  if(isset($_POST['accion'])) {
  
    function propagar_etapa($di, $e) {
      pg_query($conn, "
        UPDATE detalle_patauge SET detpat_etapa=$e 
        WHERE detpat_id=$di");
        
      $childs=cargar_registros_obj("
        SELECT detpat_id FROM detalle_patauge 
        WHERE detpat_padre_id=$di");
      
      if($childs) {
        for($i=0;$i<count($childs);$i++)
          propagar_etapa($childs[$i]['detpat_id'], $e);
      }
      
    }
  
    if($_POST['accion']=='mover_todo') { 
      pg_query($conn, "UPDATE detalle_patauge 
                  SET detpat_padre_id=$detpat_padre_id, detpat_etapa=$etapa
                WHERE detpat_id=$detpat_id;");
    
    } else {
      pg_query($conn, "UPDATE detalle_patauge 
                  SET detpat_padre_id=$detpat_padre_id, detpat_etapa=$etapa
                WHERE detpat_padre_id=$detpat_id;");

    }
    
    list($p)=cargar_registros_obj("
        SELECT pat_id FROM detalle_patauge WHERE detpat_id=$detpat_id
    ");
    
    $pat_id=$p['pat_id'];
  
    $d=cargar_registros_obj("
      SELECT detpat_id FROM detalle_patauge 
      WHERE pat_id=$pat_id AND detpat_padre_id=0 AND detpat_etapa=$etapa");
      
    for($i=0;$i<count($d);$i++) 
      propagar_etapa($d[$i]['detpat_id'], $etapa);
        
    exit('');
  
  }
  
  // Ingreso y/o modificación del nodo...
  
  $canasta=json_decode($_POST['canasta']);
  $presta_codigo = pg_escape_string($_POST['codigo_prestacion']);
  $pat_id=$_POST['pat_id']*1;
  
  
  if(isset($_POST['cant']))
    $cant = $_POST['cant']*1;
  else
    $cant = 0;
 
  $unidad = $_POST['unidad']*1;
  $ipd = (isset($_POST['ipd'])) ? 'true':'false';
  $sigges = (isset($_POST['sigges'])) ? 'true':'false';
  
  if(isset($_POST['edad'])) 
    $edad=$_POST['edad']*1;
  else
    $edad=0;  

  if(isset($_POST['edad2'])) 
    $edad2=$_POST['edad2']*1;
  else
    $edad2=0;  

  $redad=$_POST['redad']*1;

  if(isset($_POST['frec'])) 
    $frec=$_POST['frec']*1;
  else
    $frec=0;  

  $ufrec=pg_escape_string($_POST['ufrec']);

  $sexo=$_POST['sexo']*1;
  $excluyentes=pg_escape_string($_POST['excluyentes']);

  if($detpat_id==0) {
  
    pg_query($conn, "
    INSERT INTO detalle_patauge VALUES (
      DEFAULT,
      $pat_id,
      '$presta_codigo',
      $ipd, $sigges,
      $detpat_padre_id,
      $cant, $unidad,
      $edad, $redad,
      $frec, '$ufrec',
      $sexo, '$excluyentes', $edad2, 
      $etapa
    )
    ");
    
    for($i=0;$i<count($canasta);$i++) {
      pg_query("INSERT INTO detalle_patcanasta VALUES (
      DEFAULT, CURRVAL('detalle_patauge_detpat_id_seq'),
      '".pg_escape_string($canasta[$i]->codigo)."', ''
      );");
    }
  
  } else {
    
    pg_query($conn, "
    UPDATE detalle_patauge SET
    presta_codigo='$presta_codigo',
    detpat_ipd=$ipd,
    detpat_sigges=$sigges,
    detpat_plazo=$cant,
    detpat_uplazo=$unidad,
    detpat_edad=$edad,
    detpat_edad2=$edad2,
    detpat_redad=$redad,
    detpat_frec=$frec,
    detpat_ufrec='$ufrec',
    detpat_sexo=$sexo,
    detpat_excluyentes='$excluyentes'
    WHERE detpat_id=$detpat_id
    ");
    
    $can=cargar_registros_obj("
      SELECT * FROM detalle_patcanasta WHERE detpat_id=$detpat_id
    ");
    
    for($i=0;$i<count($canasta);$i++) {
      $fnd=false;
      for($j=0;$j<count($can);$j++)
        if($can[$j]['codigo']==$canasta[$i]->codigo) $fnd=true;
      
      
      if(!$can OR !$fnd) 
        pg_query("INSERT INTO detalle_patcanasta VALUES (
        DEFAULT, $detpat_id,
        '".pg_escape_string($canasta[$i]->codigo)."', ''
        );");
    }
    
    for($j=0;$j<count($can);$j++) {
      $fnd=false;
      for($i=0;$i<count($canasta);$i++)
        if($can[$j]['codigo']==$canasta[$i]->codigo) $fnd=true;
      
      if(!$fnd)
        pg_query("DELETE FROM detalle_patcanasta 
        WHERE detpat_id=$detpat_id AND 
        codigo='".pg_escape_string($can[$j]['codigo'])."'");
        
    }
      
  
  }
  
  
?>
