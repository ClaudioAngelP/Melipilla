<?php

  require_once('../../conectar_db.php');
  
  $r=json_decode($_POST['p']);
  $esp_id=$_POST['esp_id']*1;
  
  if($r[11]!=0) {
    $doc_id=$r[12]; $campo='cupos_cantidad_c';
    //$wdoc="cupos_doc_id=$doc_id AND";
    $wdoc='';
    $twhere='AND NOT cupos_asigna.control_id=0';
  } else {
    $doc_id=0; $campo='cupos_cantidad_n';
    $wdoc="";
    $twhere='AND cupos_asigna.control_id=0';

  }
  
  $q="
    SELECT *, cupos_fecha::date AS cupos_fecha FROM (
    SELECT 
    *,
    (
      SELECT COUNT(*) FROM cupos_asigna 
      WHERE cupos_atencion.cupos_id=cupos_asigna.cupos_id
        $twhere
    ) AS utilizados     
    FROM cupos_atencion 
    WHERE $wdoc cupos_esp_id=$esp_id
    ) AS foo WHERE ($campo::bigint-(utilizados+cupos_cant_r))>0 
  ";
  
  //print($q);
  
  $cupo=cargar_registros_obj($q);        

  if(!$cupo)
    die('No hay mas cupos.');

  $cupos_id=$cupo[0]['cupos_id'];
  $inter_id=$r[6];
  $control_id=$r[11];

  $total=($cupo[0]['cupos_cantidad_n']*1)+($cupo[0]['cupos_cantidad_c']*1);
  
  $hr=explode(':',$cupo[0]['cupos_horainicio']);
  $hi=mktime($hr[0]*1, $hr[1]*1, 0)/60;
  $hr=explode(':',$cupo[0]['cupos_horafinal']);
  $hf=mktime($hr[0]*1, $hr[1]*1, 0)/60;
    
  $step=floor(($hf-$hi)/$total);

  $stop=0;

  for(;$hi<=$hf;$hi+=$step) {
  
    $stop++; if($stop>30) break;
  
    $hr=date('H:i', $hi*60);
  
    $q="SELECT * FROM cupos_asigna 
    WHERE cupos_id=$cupos_id AND asigna_hora='$hr:00'";
    
    $chk=pg_query($q);
    
    if(pg_num_rows($chk)==0) 
      break;
  
  }
  
  $q="
    INSERT INTO cupos_asigna VALUES (
    default, $inter_id, $cupos_id, '$hr', $control_id
    );
  ";
  
  pg_query($q);
  
  print($cupo[0]['cupos_fecha'].' '.$hr);
  
  exit();

?>
