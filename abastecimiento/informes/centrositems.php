<?php

  require_once('../../conectar_db.php');
  
  if(!isset($_GET['submit'])) {
  
  } else {
  
    $tmp_inicio = microtime(true);
    
    if(isset($_GET['xls'])) {
      header("Content-type: application/vnd.ms-excel");
      header("Content-Disposition: filename=\"Tabla de Gastos por Centros de Responsabilidad vs Item Presupuestarios.XLS\";");
      $xlsborder="border=1";
    } else {
      $xlsborder="";
    }
  
    // Crea tabla temporal con los gastos de Recetas
    // y la une con los gastos por despacho a servicios.
  
    pg_query($conn, "
    
    CREATE TEMP TABLE recetas_centros AS 
    
    SELECT 
      obtener_centro_costo(receta_centro_ruta) AS centro_ruta, 
      art_item, 
      -(stock_cant) AS stock_cant, 
      art_val_ult 
    FROM stock
	   JOIN articulo ON stock_art_id=art_id
	   JOIN logs ON stock_log_id=log_id
	   JOIN recetas_detalle ON log_recetad_id=recetad_id
	   JOIN receta ON recetad_receta_id=receta_id
	  WHERE
	   log_fecha BETWEEN '2007-12-01' AND '2007-12-31'
	   
	  UNION

    SELECT 
      obtener_centro_costo(centro_ruta) AS centro_ruta, 
      articulo.art_item, 
      -(stock.stock_cant) AS stock_cant,
      articulo.art_val_ult
    FROM stock
	     JOIN articulo ON stock_art_id=art_id
	     JOIN logs ON stock_log_id=log_id 
	     JOIN cargo_centro_costo USING (log_id)
    WHERE log_tipo=15 AND log_fecha BETWEEN '2007-12-01' AND '2007-12-31'
    
    ;
    ");
    
    $centros = pg_query($conn, "
    SELECT DISTINCT centro_ruta, centro_nombre FROM recetas_centros
    JOIN centro_costo USING (centro_ruta);
    ");
    
    $items = pg_query($conn, "
    SELECT DISTINCT art_item, item_glosa FROM recetas_centros
    JOIN item_presupuestario ON art_item=item_codigo;
    ");
  
    // Cabecera del Informe
  
    print("<table $xlsborder><tr style='text-align: center; font-weight:bold;'><td>&nbsp;</td>");
    
    for($i=0;$i<pg_num_rows($centros);$i++) {
    
      $centro_arr = pg_fetch_row($centros, $i);
      
      print("<td>".$centro_arr[1]."</td>");
    
    }
    
    print("</tr>");
    
    // Filas del Informe; una por cada item presupuestario.
  
  
    for($a=0;$a<pg_num_rows($items);$a++) {
    
      $item_arr = pg_fetch_row($items, $a);
    
      print("<tr><td>".$item_arr[1]."</td>");
      
      for($i=0;$i<pg_num_rows($centros);$i++) {
    
        $centro_arr = pg_fetch_row($centros, $i);
      
        $dato_q = pg_query($conn, "
        SELECT SUM(gasto) FROM (
        SELECT stock_cant*art_val_ult AS gasto FROM recetas_centros
        WHERE 
        centro_ruta='".pg_escape_string($centro_arr[0])."' AND 
        art_item='".pg_escape_string($item_arr[0])."')
        AS foo
        ");
      
        $dato = pg_fetch_row($dato_q);
      
        print("
        <td style='text-align: right;'>
        ".number_formats($dato[0])."
        </td>");
    
      }
      
      print("</tr>");
    
    }
    
    print("</table>");
    
    $tmp_final = microtime(true);
    
    $tmp = $tmp_final-$tmp_inicio;
    
    print("<center>Obtenido en [".$tmp."] msecs.</center>");
  
  }

?>
