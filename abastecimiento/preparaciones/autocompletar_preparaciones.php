<?php

  require_once('../../conectar_db.php');
  
    $cadena = pg_escape_string(utf8_decode($_GET['codigo_prep']));

    $centros = pg_query($conn, "
    SELECT
    art_id,
    art_codigo,
    art_glosa
    FROM articulo
    WHERE 
    art_codigo ILIKE '300%' AND (art_codigo ILIKE '%$cadena%' OR art_glosa ILIKE '%$cadena%') 
    LIMIT 10
    ");

    if(pg_num_rows($centros)>0)  {

      for($i=0; $i<pg_num_rows($centros); $i++) {
        $array[$i]=pg_fetch_row($centros);
        $array[$i][1]=htmlentities($array[$i][1]);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

?>
