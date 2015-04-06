<?php

  require_once('../../conectar_db.php');

  $bod_id=$_POST['bodega_id'];
  $pac_id=$_POST['pac_id']*1;
  $arts=$_POST['arts'];
  
  $art=json_decode($arts);
  
  if(strstr($bod_id,'.')) {
    
      // Centro de Costo
    
      $tabla_stock='stock_servicios';
      $lotes_vigentes='lotes_vigentes_cc';
      $centro_ruta='\''.pg_escape_string($bod_id).'\'';
      $ubica=$centro_ruta.'::text';
      $bod_id='-1';
      
    } else {
    
      // Bodega
      
      $tabla_stock='stock';
      $lotes_vigentes='lotes_vigentes';
      $bod_id=$bod_id*1;
      $centro_ruta='';
      $ubica=$bod_id.'::bigint';
    
  }

  
  pg_query($conn, "START TRANSACTION;");
  
  pg_query($conn, "
		INSERT INTO 
		logs
		VALUES (
		DEFAULT,
		".($_SESSION['sgh_usuario_id']*1).",
		17,
		current_timestamp,
		0,
		NULL,
		-1,
    '' );
	");	

  for($i=0;$i<count($art);$i++) {
  
  			$lotes = pg_query($conn, "
				SELECT * FROM $lotes_vigentes(".($art[$i]->id).", $ubica);
        ");
				
				
				$art_id=($art[$i]->id)*1;
				$cant=($art[$i]->cantidad*1);
				
				pg_query("INSERT INTO prestacion 
				(presta_id, pac_id, presta_codigo_v, presta_desc, presta_valor, presta_fecha, presta_cant, presta_estado) 
				VALUES (
				DEFAULT, $pac_id, 
				(select art_codigo from articulo where art_id=$art_id),
				(select art_glosa from articulo where art_id=$art_id),
				(select art_val_ult from articulo where art_id=$art_id)*1.2,
				CURRENT_TIMESTAMP, $cant, 1
				);");
				
				$bloquea=0;
				
				while($cant>0) {
				
				  $bloquea++;
				  
				  if($bloquea==300) {
				    // Previene Bucle Infinito y Posibles DoS.
				    
				    $respuesta[0]=false;
				    $respuesta[1]='Error Inesperado.';
            die(json_encode($respuesta));
            
          }
				
					// $dato[0] = Fecha
					// $dato[1] = Cantidad
					
					$registro_lote=pg_fetch_row($lotes);
					
					if($cant>=$registro_lote[0]) {
						$cnt=$registro_lote[0];
					} else {
						$cnt=$cant;
					}
					
					if($registro_lote[1]!='') 	
              $vencimiento="'".$registro_lote[1]."'";
					else 							
              $vencimiento="null";
					
					
					// Rebaja de la ubicacion de origen.
          pg_query($conn, "INSERT INTO $tabla_stock VALUES (
          DEFAULT,
          ".($art[$i]->id).",
          ".($ubica).",
          ".(-$cnt).",
          CURRVAL('logs_log_id_seq'),
          $vencimiento,
          0
          );");
          
          $cant-=$cnt;
          
          }
  
  }
  
  if($centro_ruta!='')
  pg_query($conn, "
      INSERT INTO cargo_centro_costo VALUES (
      CURRVAL('logs_log_id_seq'),
      ".($centro_ruta)."
      )
  ");
  
  pg_query($conn, "
  INSERT INTO cargo_hoja VALUES (
  CURRVAL('logs_log_id_seq'),
  $pac_id
  )
  ");
  
  
  pg_query($conn, "COMMIT;");
  
  print(json_encode(Array(true, true)));

?>


