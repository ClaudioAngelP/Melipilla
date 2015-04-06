<?php

  require_once('../../conectar_db.php');
  
  $codigo_bandeja = pg_escape_string(utf8_decode($_GET['codigo_bandeja']));
  $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

  $valida = pg_query($conn, "
    SELECT * FROM (
		SELECT DISTINCT id_condicion, nombre_condicion, nombre_bandeja, codigo_bandeja
		FROM lista_dinamica_proceso
	) AS foo WHERE id_condicion>0 AND
	nombre_condicion ILIKE '%$cadena%'
	ORDER BY nombre_condicion
    LIMIT 20
    ");

    if(pg_num_rows($valida)>0)  {

      for($i=0; $i<pg_num_rows($valida); $i++) {
        $array2[$i]=pg_fetch_row($valida);
        $array[$i][0]=htmlentities($array2[$i][0]);
        $array[$i][1]=htmlentities($array2[$i][1]);
        $array[$i][3]=htmlentities($array2[$i][2]);
        $array[$i][4]=htmlentities($array2[$i][3]);
        $array[$i][2]='<img src="../../iconos/arrow_right.png" />';
       }

    } else { $array=''; }

    print(json_encode($array));


?>
