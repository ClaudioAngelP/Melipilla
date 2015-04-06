<?php

  require_once('../../conectar_db.php');
  
  $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

  $items = pg_query($conn, "
    SELECT item_codigo, item_nombre
    FROM item_presupuestario_sigfe
    WHERE item_codigo ILIKE '%$cadena%' OR  item_nombre ILIKE '%$cadena%'
	ORDER BY item_codigo
    LIMIT 20
    ");

    if(pg_num_rows($items)>0)  {

      for($i=0; $i<pg_num_rows($items); $i++) {
        $array2[$i]=pg_fetch_row($items);
        $array[$i][0]=htmlentities($array2[$i][0]);
        $array[$i][1]=htmlentities($array2[$i][1]);
       }

    } else { $array=''; }

    print(json_encode($array));


?>
