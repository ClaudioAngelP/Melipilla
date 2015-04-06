<?php

  require_once('../../conectar_db.php');
  
  $pat_id=$_POST['pat_id']*1;
  
  $reg = cargar_registro("
  SELECT 
  pat_id, pat_glosa,
  to_char(pat_fecha_inicio, 'DD/MM/YYYY') AS pat_fecha_inicio,
  to_char(pat_fecha_final, 'DD/MM/YYYY') AS pat_fecha_final,
  pat_ingreso, 
  (SELECT MIN(detpat_etapa) FROM detalle_patauge 
  	WHERE detalle_patauge.pat_id=patologias_auge.pat_id) AS etapa
  FROM patologias_auge WHERE pat_id=$pat_id
  ");
    
  $reg['pat_glosa']=htmlentities($reg['pat_glosa']); 
  
  print(json_encode($reg));

?>
