<?php

  require_once('../../conectar_db.php');
  
  $cadena = pg_escape_string(utf8_decode($_GET['cadena']));
  $rut=$_GET['rut'];

  $items = pg_query($conn, "
    SELECT *
    FROM codigos_prestacion
    WHERE codigo ILIKE '%$cadena%'   OR glosa ILIKE '%$cadena%'
	ORDER BY codigo 
    LIMIT 40
    ");
	
	$items_meds = pg_query($conn, "
    SELECT codigo,glosa,convenios,*
    FROM codigos_prestacion_convenio
    WHERE convenios='$rut' AND (codigo ILIKE '%$cadena%'   OR glosa ILIKE '%$cadena%')
	ORDER BY codigo 
    LIMIT 40
    ");

	 if(pg_num_rows($items)>0 )  {
    if(pg_num_rows($items_meds)>0)  {
    	
	
      for($i=0; $i<pg_num_rows($items_meds); $i++) {
        $array2[$i]=pg_fetch_row($items_meds);
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
		$array[$i][14]=htmlentities($array2[$i][13]);
		$array[$i][15]=htmlentities($array2[$i][14]);
		$array[$i][16]=htmlentities($array2[$i][15]);
		$array[$i][17]=htmlentities($array2[$i][18]);
		$array[$i][18]=htmlentities($array2[$i][19]);
		
		
		
		
       }

    } else {
    		
    	for($i=0; $i<pg_num_rows($items); $i++) {
        $array2[$i]=pg_fetch_row($items);
        $array[$i][0]=htmlentities($array2[$i][0]);
		
        $array[$i][1]=htmlentities($array2[$i][1]);
		$array[$i][2]=htmlentities($array2[$i][2]);
		
		$array[$i][3]='';
		
		$array[$i][4]=htmlentities($array2[$i][0]);
		$array[$i][5]=htmlentities($array2[$i][1]);
		$array[$i][6]=htmlentities($array2[$i][2]);
		$array[$i][7]=htmlentities($array2[$i][3]);
		$array[$i][8]=htmlentities($array2[$i][4]);
		$array[$i][9]=htmlentities($array2[$i][5]);
		$array[$i][10]=htmlentities($array2[$i][6]);
		$array[$i][11]=htmlentities($array2[$i][7]);
		$array[$i][12]=htmlentities($array2[$i][8]);
		$array[$i][13]=htmlentities($array2[$i][9]);
		$array[$i][14]=0;
		$array[$i][15]='';
		$array[$i][16]='';
		$array[$i][17]=0;
		$array[$i][18]=0;
		
       }

    
    }
    }else{
    	$array='';
    }

    print(json_encode($array));


?>
