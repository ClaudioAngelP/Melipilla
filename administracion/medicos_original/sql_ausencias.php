<?php

  require_once('../../conectar_db.php');
  
  if(isset($_POST['ausencia_id'])) {
  
    $doc_id=$_POST['doc_id']*1;
    
    pg_query("DELETE FROM ausencias_medicas 
              WHERE ausencia_id=".($_POST['ausencia_id']*1));
  
  } else {
  
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);
    $motivo=$_POST['motivo']*1;
    $doc_id=$_POST['doc_id']*1;
    
    if($fecha2=='') $fecha2='null'; else $fecha2="'$fecha2'";
    
    pg_query("INSERT INTO ausencias_medicas 
              VALUES (default, '$fecha1', $fecha2, $motivo, $doc_id)");
    
  }
  
  $a = cargar_registros_obj("
    SELECT * FROM ausencias_medicas 
    JOIN ausencias_motivos ON motivo_id=ausencia_motivo 
    WHERE doc_id=$doc_id ORDER BY ausencia_fechainicio  
  ");
  
  print(json_encode($a));

?>
