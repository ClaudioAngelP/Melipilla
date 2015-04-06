<?php 

	$sghserver='localhost';
	$sghport=5432;
	
	$sghdbname='temp';
	$sghuser='postgres';
	$sghpass='123soluciones';
	

	$conn = pg_connect("host=$sghserver port=$sghport 
	dbname=$sghdbname user=$sghuser password=$sghpass"); 
	
  if(!$conn) { die('Problemas con la Conexi&oacute;n.'); }

	function cargar_registro($sql, $html=false) {
	
	  GLOBAL $conn;
  
    $registro = array();
    $fila = pg_query($conn, $sql);
    
    if(pg_num_rows($fila)==0) return false;
    
    for($i=0;$i<pg_num_fields($fila);$i++) {
    
      if(!$html)
        $registro[pg_field_name($fila, $i)]=pg_fetch_result($fila, 0, $i);
      else
        $registro[pg_field_name($fila, $i)]=htmlentities(pg_fetch_result($fila, 0, $i));
      
    
    }
    
    return $registro;
  
  }


  function cargar_registros($sql, $html=false) {
	
	  GLOBAL $conn;
  
    $registro = array();
    $filas = pg_query($conn, $sql);
    
    if(pg_num_rows($filas)==0) return false;
    
    for($r=0;$r<pg_num_rows($filas);$r++) {
      $registro[$r]= array();
      
      for($i=0;$i<pg_num_fields($filas);$i++) {
        if(!$html) $registro[$r][$i]=pg_fetch_result($filas, $r, $i);
        else  $registro[$r][$i]=htmlentities(pg_fetch_result($filas, $r, $i));
      }
    
    }
    
    return $registro;
  
  }

  function cargar_registros_obj($sql, $html=false) {
	
	  GLOBAL $conn;
  
    $registro = array();
    $filas = pg_query($conn, $sql);
    
    if(pg_num_rows($filas)==0) return false;
    
    for($r=0;$r<pg_num_rows($filas);$r++) {
      $registro[$r]= array();
      
      for($i=0;$i<pg_num_fields($filas);$i++) {
        if(!$html) $registro[$r][pg_field_name($filas, $i)]=pg_fetch_result($filas, $r, $i);
        else  $registro[$r][pg_field_name($filas, $i)]=htmlentities(pg_fetch_result($filas, $r, $i));
      }
    
    }
    
    return $registro;
  
  }	
	
	$recetas=cargar_registros_obj("
		select * from (select receta_id, count(*) as cnt from receta_dif group by receta_id) as foo where cnt>1;
	");
	
	pg_query("START TRANSACTION;");
	
	for($i=0;$i<sizeof($recetas);$i++) {
	
		$receta_id=$recetas[$i]['receta_id'];
		
		$r=cargar_registros_obj("select * from receta_dif where receta_id=$receta_id order by receta_fecha_emision;");
		
		pg_query("UPDATE receta_dif SET receta_id=receta_id+500000 WHERE
			receta_id=".$receta_id." AND
			receta_fecha_emision='".$r[1]['receta_fecha_emision']."' AND
			receta_paciente_id=".$r[1]['receta_paciente_id']." AND
			receta_doc_id=".$r[1]['receta_doc_id']."
		");
	
		$p=cargar_registros_obj("SELECT * FROM recetas_detalle_dif WHERE recetad_receta_id=$receta_id ORDER BY recetad_id;");
		
		for($j=0;$j<sizeof($p);$j++) {
			
			$pid=$p[$j]['recetad_id'];
			
			pg_query("UPDATE recetas_detalle_dif SET
				recetad_id=recetad_id+500000,
				recetad_receta_id=recetad_receta_id+500000
			WHERE
				recetad_id=$pid AND
				recetad_art_id=".$p[$j]['recetad_art_id']." AND
				recetad_cant=".$p[$j]['recetad_cant']." AND
				recetad_horas=".$p[$j]['recetad_horas']." AND
				recetad_dias=".$p[$j]['recetad_dias']."
			");
			
			if($p[$j+1]['recetad_id']-$pid!=1) break;
			
		}
		
	}
	
	pg_query("COMMIT;");
	

?>
