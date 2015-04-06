<?php

  require_once('conectar_db.php');

  if($_GET['tipo']=='especialidad_subespecialidad') {

    $cadena = pg_escape_string(utf8_decode($_GET['esp_desc']));
   
    $query = "SELECT
    esp_id,
    esp_id,
    esp_desc
    FROM especialidades_gestion_camas
    WHERE esp_desc ILIKE '%$cadena%'
    ORDER BY esp_desc";
	 
	 $esp=pg_query($conn, $query);	    

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }
  
  if($_GET['tipo']=='especialidad') {

    $cadena = pg_escape_string(utf8_decode($_GET['esp_desc']));
   
    $query = "SELECT
    esp_id,
    esp_id,
    esp_desc
    FROM especialidades_gestion_camas
    WHERE esp_tipo=0 AND esp_desc ILIKE '%$cadena%'
    ORDER BY esp_desc";
	 
	 $esp=pg_query($conn, $query);	    

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='subespecialidad') {

    $cadena = pg_escape_string(utf8_decode($_GET['esp_desc']));

    $esp = pg_query($conn, "
    SELECT
    esp_id,
    esp_id,
    esp_desc
    FROM especialidades_gestion_camas
    WHERE esp_tipo=1 AND esp_desc ILIKE '%$cadena%'
    ORDER BY esp_desc
    ");

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }

  if($_GET['tipo']=='instituciones') {

    $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

    $esp = pg_query($conn, "
    SELECT
    inst_id,
    inst_id,
    inst_nombre
    FROM instituciones_gestion_camas
    WHERE 
    inst_nombre ILIKE '%$cadena%'
    ORDER BY inst_nombre
    ");

    if(pg_num_rows($esp)>0)  {

      for($i=0; $i<pg_num_rows($esp); $i++) {
        $array[$i]=pg_fetch_row($esp);
        $array[$i][2]=htmlentities($array[$i][2]);
      }

    } else { $array=''; }

    print(json_encode($array));

  }


?>
