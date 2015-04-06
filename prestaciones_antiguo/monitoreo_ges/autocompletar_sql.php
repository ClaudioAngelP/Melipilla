<?php

  require_once('../../conectar_db.php');

  if($_GET['tipo']=='bandejas') { 
  
  $cadena = pg_escape_string(utf8_decode($_GET['monges_subclase']));

  $valida = pg_query($conn, "
    SELECT * FROM (
		SELECT DISTINCT ldp.id_condicion, nombre_condicion, nombre_bandeja, ldp.codigo_bandeja, subcondiciones
		FROM lista_dinamica_proceso AS ldp
		LEFT JOIN lista_dinamica_condiciones USING (id_condicion)
		LEFT JOIN lista_dinamica_bandejas AS ldb ON ldp.codigo_bandeja_n=ldb.codigo_bandeja
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
        $array[$i][5]=htmlentities($array2[$i][4]);
        $array[$i][2]='<img src="../../iconos/arrow_right.png" />';
       }

    } else { $array=''; }

    print(json_encode($array));
    
  }


  if($_GET['tipo']=='condiciones') { 
  
  $cadena = pg_escape_string(utf8_decode($_GET['monges_subclase']));

  $valida = pg_query($conn, "
    SELECT DISTINCT id_condicion, nombre_condicion, subcondiciones
	FROM lista_dinamica_condiciones
	WHERE id_condicion>0 AND	nombre_condicion ILIKE '%$cadena%'
	ORDER BY nombre_condicion
    LIMIT 20
    ");

    if(pg_num_rows($valida)>0)  {

      for($i=0; $i<pg_num_rows($valida); $i++) {
        $array2[$i]=pg_fetch_row($valida);
        $array[$i][0]=htmlentities($array2[$i][0]);
        $array[$i][1]=htmlentities($array2[$i][1]);
        $array[$i][2]=htmlentities($array2[$i][2]);
       }

    } else { $array=''; }

    print(json_encode($array));
    
  }


?>
