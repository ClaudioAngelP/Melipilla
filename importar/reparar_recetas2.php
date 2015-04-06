<?php 

	error_reporting(E_ALL);

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
	
	$logs=cargar_registros_obj("
		select * 
		from recetas_detalle_dif 
		join receta_dif on receta_id=recetad_receta_id
		join logs_2 on log_recetad_id=recetad_id
		join stock_2 on stock_log_id=log_id 
		where stock_art_id=recetad_art_id;
	");
	
	pg_query("START TRANSACTION;");
	
	for($i=0;$i<sizeof($logs);$i++) {
	
		$func_id=$logs[$i]['receta_func_id'];
		$fecha=$logs[$i]['receta_fecha_emision'];
		$recetad_id=$logs[$i]['recetad_id'];
		
		pg_query("INSERT INTO logs_dif VALUES (DEFAULT, $func_id, 9, '$fecha', $recetad_id, null, 0, null);");
		
		$art_id=$logs[$i]['stock_art_id'];
		$bod_id=$logs[$i]['stock_bod_id'];
		$cant=$logs[$i]['stock_cant'];
		$vence=$logs[$i]['stock_vence'];
		$log_id="CURRVAL('logs_dif_log_id_seq')";
		$tlog_id=$logs[$i]['stock_log_id'];
		
		if($vence=='')
			$vence='null';
		else
			$vence="'$vence'";
			
		$stock_id=$logs[$i]['stock_id'];
			
		pg_query("
			DELETE FROM stock_dif WHERE
			stock_id=$stock_id AND
			stock_art_id=$art_id AND
			stock_bod_id=$bod_id AND
			stock_cant=$cant AND
			stock_log_id=$tlog_id 
		");
		
		pg_query("INSERT INTO stock_dif VALUES (DEFAULT, $art_id, $bod_id, $cant, $log_id, $vence, 0);");
		
	}
	
	pg_query("COMMIT;");
	

?>
