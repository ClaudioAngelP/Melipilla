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
	$f=explode("\n",file_get_contents('logs_erroneos2.csv'));
	
	for($i=1;$i<sizeof($f);$i++) {
		
		$log=false;
		
		$r = explode('|',$f[$i]);
	
		$log_id = $r[0]*1;
		$log_fecha = $r[1];
		
		$tmp = explode(' ',$log_fecha);
		list($tmp1)=explode('.',$tmp[1]);
		$fec = explode('-',$tmp[0]);
		
		$log_fecha=$fec[2].'/'.$fec[1].'/'.$fec[0].' '.$tmp[1];
		
		print(' '.$log_fecha.' ');
		
		$log=cargar_registro("SELECT * FROM logs WHERE log_fecha='$log_fecha'");
		
		if(!$log) { echo 'NO'; continue; }
		
		pg_query("UPDATE logs SET log_id=$log_id WHERE log_fecha='$log_fecha'");
	
	}

?>
