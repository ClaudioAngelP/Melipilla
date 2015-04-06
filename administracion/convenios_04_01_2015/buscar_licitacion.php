<?php

  require_once('../../conectar_db.php');

  $licitacion = $_GET['convenio_licitacion'];

  $convenio_q = pg_query($conn, "SELECT 
				convenio_categoria, convenio_tipo_licitacion, convenio_nombre, convenio_nro_res_aprueba, 
				convenio_nro_res_adjudica,convenio_fecha_aprueba, convenio_fecha_adjudica 
				FROM convenio WHERE convenio_licitacion='$licitacion' LIMIT 1
				");
  
  if(pg_num_rows($convenio_q)>0) {
  
    $convenio = pg_fetch_row($convenio_q);
  
    for($i=0;$i<count($convenio);$i++) {
      $convenio[$i]=htmlentities($convenio[$i]);
    }
    
    $respuesta=Array(true,$convenio);
    
    print(json_encode($respuesta));
    
  } else {
  
    print(json_encode(Array(false,false)));
  
  }

?>
