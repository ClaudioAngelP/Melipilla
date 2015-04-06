<?php 
    require_once('../../conectar_db.php');
    if($_GET['tipo']=='prestacion') {
        $cadena = pg_escape_string(utf8_decode($_GET['cod_presta']));
        $procmed = pg_query($conn, "
        SELECT
        codigo,
        codigo,
        glosa
        FROM codigos_prestacion
        WHERE 
        (codigo ILIKE '%$cadena%' OR glosa ILIKE '%$cadena%') 
        LIMIT 10
        ");

        if(pg_num_rows($procmed)>0)  {
            for($i=0; $i<pg_num_rows($procmed); $i++) {
                $array[$i]=pg_fetch_row($procmed);
                $array[$i][2]=htmlentities($array[$i][2]);
            }
        } else { $array=''; }
        print(json_encode($array));
    }

    if($_GET['tipo']=='articulo') {
        $cadena = pg_escape_string(utf8_decode($_GET['art_codigo']));
        $procmed = pg_query($conn, "
        SELECT
        art_id,
        art_codigo,
        art_glosa,
        forma_nombre
        FROM articulo
        LEFT JOIN bodega_forma ON art_forma=forma_id
        WHERE 
        (art_codigo ILIKE '%$cadena%' OR art_glosa ILIKE '%$cadena%') 
        AND art_item IN ('2204004001', '2204005001','2204005')
        LIMIT 10
        ");

    if(pg_num_rows($procmed)>0)  {

      for($i=0; $i<pg_num_rows($procmed); $i++) {
        $array[$i]=pg_fetch_row($procmed);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }



	if($_GET['tipo']=='medicamento_restringido') {

    $cadena = pg_escape_string(utf8_decode($_GET['art_codigo']));
    
    $procmed = pg_query($conn, "
    SELECT
    articulo.art_id,
    art_codigo,
    art_glosa,
    forma_nombre
    FROM articulo
    LEFT JOIN bodega_forma ON art_forma=forma_id
    JOIN autorizacion_farmacos_detalle AS autf ON autf_id=92 AND autf.art_id=articulo.art_id
    WHERE 
    (art_codigo ILIKE '%$cadena%' OR art_glosa ILIKE '%$cadena%') 
    LIMIT 10
    ");

    if(pg_num_rows($procmed)>0)  {

      for($i=0; $i<pg_num_rows($procmed); $i++) {
        $array[$i]=pg_fetch_row($procmed);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }



?>
