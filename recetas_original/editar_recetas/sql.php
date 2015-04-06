<?php

  require_once('../../conectar_db.php');
  
  $bodega_id = ($_GET['bodega_id']*1);
  $id_receta = ($_GET['__receta_id']*1);
  
  $id_paciente = ($_GET['__id_paciente']*1);
  $id_diagnostico = pg_escape_string($_GET['__id_diag']);
  $rut_medico = pg_escape_string($_GET['__nombre_medico']);
  $observaciones = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['observaciones']));
 
  $medico_q = pg_query($conn, "
  SELECT doc_id FROM doctores WHERE doc_rut='".$rut_medico."'
  ");
  
  if(pg_num_rows($medico_q)!=1) {
    die('Error Inesperado.');
  } else {
    $medico_a = pg_fetch_row($medico_q);
  }

  pg_query($conn, "START TRANSACTION;");


  pg_query($conn, "
  UPDATE receta SET
  receta_paciente_id=".$id_paciente.",
  receta_doc_id=".$medico_a[0].",
  receta_diag_cod='".$id_diagnostico."',
  receta_comentarios='".$observaciones."'
  WHERE receta_id=".$id_receta."
  ");
  
  $detalle_receta = pg_query($conn, "
  SELECT recetad_id, recetad_art_id FROM recetas_detalle
  WHERE recetad_receta_id=".$id_receta."
  ");
  
  for($i=0;$i<pg_num_rows($detalle_receta);$i++) {
  
    $recetad_id = pg_fetch_row($detalle_receta);
    
    $cant = ($_GET['__cant_n_'.$recetad_id[0]]*1);
    $horas = ($_GET['__horas_n_'.$recetad_id[0]]*1);
    $dias = ($_GET['__dias_n_'.$recetad_id[0]]*1);
    
    pg_query("
    UPDATE recetas_detalle
    SET
    recetad_cant=".$cant.",
    recetad_horas=".$horas.",
    recetad_dias=".$dias."
    WHERE
    recetad_id=".$recetad_id[0]."
    ");
    
    $meds = split('|', $_GET['logs_recetad_'.$recetad_id[0]]);
  
    foreach($meds as $log_id) {
      
      $val_anterior = $_GET['valor_log_'.$log_id];
      $val_nuevo = $_GET['n_valor_log'.$log_id];
      
      $diferencia = $val_anterior-$val_nuevo;
      
      if($diferencia!=0) {
        
        pg_query($conn, "
        INSERT INTO 
		    logs
		    VALUES (
		    DEFAULT,
		    ".($_SESSION['sgh_usuario_id']*1).",
		    25,
		    current_timestamp,
		    ".$recetad_id[0].",
		    NULL,
		    0,
        '' )
		    ");
		  
		    $lotes = pg_query($conn, "
				SELECT * FROM lotes_vigentes(".$recetad_id[1].", ".$bodega_id.");
        ");
      }
        
		  
      if($diferencia>0) {
        
        $primer_lote = pg_fetch_row($lotes);
        
        // Ingresa cantidad al lote más próximo para cuadrar stock
        //////////////////////////////////////////////////////////
        
        if($primer_lote[1]=='null') {
          $primer_lote[1]='null';
        } else {
          $primer_lote[1]="'".$primer_lote[1]."'";
        }
        
        pg_query($conn, "
        INSERT INTO 
				stock
				VALUES (
				DEFAULT,
				".$recetad_id[1].",
				".$bodega_id.",
				".$diferencia.",
				CURRVAL('logs_log_id_seq'),
				".$primer_lote[1].",
				0
				)
        ");
        
      } else if($diferencia<0) {
      
        $cant=-($diferencia);
        
        while($cant>0) {
        
          $registro_lote = pg_fetch_row($lotes);
					
				  if($cant >= $registro_lote[0]) {
					 $cnt = $registro_lote[0];
				  } else {
					 $cnt = $cant;
				  }
				  
				  if($registro_lote[1]!='null') 	
              $vencimiento="'".$registro_lote[1]."'";
					else 							
              $vencimiento="null";
					
					pg_query($conn, "
          INSERT INTO 
				  stock
				  VALUES (
				  DEFAULT,
				  ".$recetad_id[1].",
				  ".$bodega_id.",
				  ".$diferencia.",
				  CURRVAL('logs_log_id_seq'),
				  ".$vencimiento.",
				  0
				  )
          ");
				  
				  $cant-=$cnt;
				
				}
					      
      }
      
    
    }
  
  }
  
  pg_query($conn, "COMMIT;");
  
  print(json_encode(true));
  
  
?>
