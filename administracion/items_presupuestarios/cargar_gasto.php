<?php

  require_once('../../conectar_db.php');
  
  $id_gasto = $_GET['gasto_id']*1;
  
  $gasto = cargar_registro('
  SELECT * FROM gasto_externo WHERE gastoext_id='.$id_gasto.'
  ');
  
  if($gasto) {
    $gasto['gastoext_nombre']=htmlentities($gasto['gastoext_nombre']);
    $gasto['gastoext_unidad']=htmlentities($gasto['gastoext_unidad']);
  } else {
    $gasto['gastoext_nombre']='';
    $gasto['gastoext_unidad']='%';
  }
  
  $gasto['detalle'] = cargar_registros("
  SELECT 
  centro_nombre,
  COALESCE(gastoextd_valor,0),
  gastoext_unidad,
  centro_ruta,
  length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel
	FROM centro_costo
  LEFT JOIN gastoext_detalle ON gastoextd_gastoext_id=$id_gasto AND
            gastoextd_centro_ruta=centro_costo.centro_ruta
  LEFT JOIN gasto_externo ON gastoextd_gastoext_id=gastoext_id
  ORDER BY centro_ruta
  ", true);

  
  print(json_encode($gasto));

?>

