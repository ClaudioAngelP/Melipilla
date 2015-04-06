<?php

  require_once('../../conectar_db.php');
  
  $subclase = pg_escape_string(utf8_decode($_GET['monges_subclase']));
  $cadena = pg_escape_string(utf8_decode($_GET['monges_descripcion']));

  $valida = pg_query($conn, "
    SELECT
		monv_subclase, monv_detalle
	FROM monitoreo_ges_detalle_validaciones
    WHERE 
        monv_detalle ILIKE '%$cadena%'
        AND 
        upper(monv_subclase) = upper('$subclase')
    ORDER BY monv_detalle
	LIMIT 10
    ");

    if(pg_num_rows($valida)>0)  {

      for($i=0; $i<pg_num_rows($valida); $i++) {
        $array[$i]=pg_fetch_row($valida);
        $array[$i][0]=htmlentities($array[$i][0]);
        $array[$i][1]=htmlentities($array[$i][1]);
       }

    } else { $array=''; }

    print(json_encode($array));


?>
