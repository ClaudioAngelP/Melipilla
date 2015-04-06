<?php 

	error_reporting(E_ALL);

	$sghserver='localhost';
	$sghport=5432;
	
	$sghdbname='temp';
	$sghuser='postgres';
	$sghpass='123soluciones';
	

	$conn = pg_connect("host=$sghserver port=$sghport 
	dbname=$sghdbname user=$sghuser password=$sghpass"); 

	$sghserver='localhost';
	$sghport=5432;
	
	$sghdbname='hgf';
	$sghuser='postgres';
	$sghpass='123soluciones';
	

	$conn2 = pg_connect("host=$sghserver port=$sghport 
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
	
	$logs=pg_query($conn2,"
	SELECT * FROM pedido 
	WHERE pedido_nro IN 
	(".$_GET['pedido_nro'].");
	");
	
	pg_query($conn2, "START TRANSACTION;");
	
	while($r=pg_fetch_assoc($logs)) {
		
		$log=pg_query($conn, "SELECT * FROM logs_2 WHERE log_id_pedido=".$r['pedido_id']);
		
		if(pg_num_rows($log)==0) {
			print("ERROR ".$r['pedido_nro']." NO ENCONTRADO <br><br>");
			continue;
		}
		
		$l=pg_fetch_assoc($log);
		
		$pedido_id=$r['pedido_id'];
		
		$origen_bod_id=$r['origen_bod_id'];
		$destino_bod_id=$r['destino_bod_id'];
		
		$log_func_id=$l['log_func_if'];
		$log_tipo=$l['log_tipo'];
		$fecha=$l['log_fecha'];
		
		$chq=pg_query($conn2, "SELECT * FROM logs WHERE log_id_pedido=".$pedido_id);
		
		if(pg_num_rows($chq)==0) {
			pg_query($conn2, "INSERT INTO logs VALUES (DEFAULT, $log_func_id, $log_tipo, '$fecha', 0, null, $pedido_id);");
			$log_id="CURRVAL('logs_log_id_seq')";
		} else {
			$l=pg_fetch_assoc($chq);
			$log_id=$l['log_id'];
		}

		$items=pg_query($conn, "SELECT * FROM stock_2 
									WHERE stock_log_id=".$log_id." AND 
									((stock_bod_id=$origen_bod_id AND stock_cant>0)
									OR
									(stock_bod_id=$destino_bod_id AND stock_cant<0))
									");
		
		while($logs2=pg_fetch_assoc($items)) {
			
			$art_id=$logs2['stock_art_id'];
			$bod_id=$logs2['stock_bod_id'];
			$cant=$logs2['stock_cant'];
			$vence=$logs2['stock_vence'];
			$tlog_id=$logs2['stock_log_id'];
			$subtotal=$logs2['stock_subtotal'];
			
			if($vence=='')
				$vence='null';
			else
				$vence="'$vence'";
				
			//$stock_id=$logs['stock_id'];
			
			$chq=pg_query($conn2, "SELECT * FROM stock 
									WHERE stock_art_id=$art_id AND 
									stock_bod_id=$bod_id AND 
									stock_cant=$cant AND
									stock_vence=$vence AND
									stock_log_id=$log_id");
			
			if(pg_num_rows($chq)==0) {		
				
				pg_query($conn2, "
					INSERT INTO stock VALUES 
					(DEFAULT, $art_id, $bod_id, $cant, $log_id, $vence, $subtotal);
				");

				print("OK LOG_ID: $log_id $art_id $cant $subtotal<br><br>");
				
			} else {
			
				print("REPETIDO LOG_ID: $log_id $art_id $cant $subtotal<br><br>");
				
			}
			
			flush();
		
		}
		
	}
	
	pg_query($conn2, "COMMIT;");
	

?>
