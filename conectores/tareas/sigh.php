<?php 

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

  
  function pg_array_parse($s) {
  
    if($s!='{""}' AND $s!=NULL) {
      $s = str_replace("{", "Array('", $s);
      $s = str_replace("}", "')", $s);
      $s = str_replace(",", "','", $s);   
    } else {
      $s = 'Array("")';
    } 
    
    $s = "\$retval = $s;";
    
    eval($s);
    return $retval;
    
  }


?>