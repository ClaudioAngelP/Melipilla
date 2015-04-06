<?php

  require_once('conectar_db.php');
  
  $cadena = pg_escape_string(utf8_decode($_GET['cadena']));

  /*$items = pg_query($conn, "
    SELECT *
    FROM codigos_prestacion
    WHERE codigo ILIKE '%$cadena%'   OR glosa ILIKE '%$cadena%'
	ORDER BY codigo 
    LIMIT 40
    ");*/
	
	 $items = pg_query($conn, "select* from (SELECT *
    FROM codigos_prestacion
    WHERE codigo ILIKE '%$cadena%'    OR glosa ILIKE '%$cadena%' 
   
    union
 SELECT art_codigo as codigo,art_glosa as glosa, 'GIS'::text as tipo,null::smallint as anio, CEIL(CEIL(art_val_ult)*1.19*1.2) as precio, CEIL(CEIL(art_val_ult)*1.19*1.2) as transferencia,0 as copago_a,0 as copago_b,CEIL(CEIL(art_val_ult)*1.19*1.2*0.1) as copago_c,CEIL(CEIL(art_val_ult)*1.19*1.2*0.2) as copago_d,''::text as pab,
    ''::text as canasta,''::text as convenios, false as pago_fijo   
   
    FROM articulo
    WHERE art_codigo  ILIKE '%$cadena%'    OR art_glosa ILIKE '%$cadena%' 
   )as fooo ORDER BY codigo 
    LIMIT 40 ");


    if(pg_num_rows($items)>0)  {
    	
	
      for($i=0; $i<pg_num_rows($items); $i++) {
        $array2[$i]=pg_fetch_row($items);
        $array[$i][0]=htmlentities($array2[$i][0]);
		
        $array[$i][1]=htmlentities($array2[$i][1]);
		$array[$i][2]=htmlentities($array2[$i][2]);
		
		$array[$i][3]='';
		
		$array[$i][4]=htmlentities($array2[$i][3]);
		$array[$i][5]=htmlentities($array2[$i][4]);
		$array[$i][6]=htmlentities($array2[$i][5]);
		$array[$i][7]=htmlentities($array2[$i][6]);
		$array[$i][8]=htmlentities($array2[$i][7]);
		$array[$i][9]=htmlentities($array2[$i][8]);
		$array[$i][10]=htmlentities($array2[$i][9]);
		$array[$i][11]=htmlentities($array2[$i][10]);
		$array[$i][12]=htmlentities($array2[$i][11]);
		$array[$i][13]=htmlentities($array2[$i][12]);
		
		
       }

    } else { $array=''; }

    print(json_encode($array));


?>
