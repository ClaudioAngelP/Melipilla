<?php

  require_once('../../graficos/sparklines.php');
  require_once('../../conectar_db.php');
  
  $hosp_id=$_GET['hosp_id']*1;
  $tipo=$_GET['tipo']*1;
  
  $backgr=$_GET['backgr'];
  
  if($tipo!=1) $limite='LIMIT 20'; else $limite='';
  
  $cat = pg_query("
  
	SELECT * FROM (
  
	SELECT DISTINCT * FROM (
  
	SELECT censo_fecha::date, censo_diario FROM censo_diario WHERE hosp_id=$hosp_id AND censo_fecha::time='09:00:00'
	UNION
	SELECT hosp_fecha_ing::date AS censo_fecha, hosp_criticidad AS censo_diario FROM hospitalizacion WHERE hosp_id=$hosp_id
	) AS foo
	
	ORDER BY censo_fecha DESC
	
	$limite
	
	) AS foo2 ORDER BY censo_fecha;
	
	
  ");  
  
  if($tipo==1) {
	$grafico = new Sparkline(400,65);
	$grafico->setPadding(10,15,5,20);
    $grafico->setLineSize(2);
  } else {
	$grafico = new Sparkline(90,40);
	$grafico->setPadding(5,13,4,15);
    $grafico->setLineSize(2);
  }
  
  $grafico->SetBackgroundColor(230,230,230);
 
    
  $min=0;
  $min_n=0;
  $max=0;
  $min_n=0;
  
  for($i=0;$i<pg_num_rows($cat);$i++) {
  
    $v = pg_fetch_result($cat, $i, 'censo_diario');
    
    $letra=$v[0];
    
    switch($letra) {
		case 'A': $val= 13-($v[1]*1); break;
		case 'B': $val= 10-($v[1]*1); break;
		case 'C': $val=  7-($v[1]*1); break;
		case 'D': $val=  4-($v[1]*1); break;
		default:  $val=0;
	}
  
    $grafico->addDataPoint($i, $val);

    if($i==0) { 
		
		$max=$val; $min=$val; $max_n=$i; $min_n=$i; $max_t=$v; $min_t=$v;
		
	} else {
		
		if($val>=$max) { $max=$val; $max_n=$i; $max_t=$v; }
		if($val<$min) { $min=$val; $min_n=$i; $min_t=$v; }
    
	}
  
  	if($i==pg_num_rows($cat)-1) { $ult=$val; $ult_n=$i; $ult_t=$v; }
    
    
  }
  
  if(pg_num_rows($cat)==1) {
	  
	// Si tiene solo un registro agrega otro para que aparezca una línea y no un punto...

    $grafico->addDataPoint($i, $val);
  
  	$ult=$val; $ult_n=$i; $ult_t=$v;

	  
  }
  
  if($ult_t[0]=='A') {
	$color=Array(255,0,0);
  } elseif($ult_t[0]=='B') {
	$color=Array(180,180,0);
  } elseif($ult_t[0]=='C') {
	$color=Array(0,0,255);
  } else {
	$color=Array(0,200,0);	  
  }

  if($max_t[0]=='A') {
	$color2=Array(255,0,0);
  } elseif($max_t[0]=='B') {
	$color2=Array(180,180,0);
  } elseif($max_t[0]=='C') {
	$color2=Array(0,0,255);
  } else {
	$color2=Array(0,200,0);	  
  }
  
  //$grafico->setFeaturePoint($min_n, $min, Array(255,0,0) , 
  //                  $min_t, TEXT_TOP, FONT_2, 5);
  //$grafico->SetFeaturePoint($i+1, $pact, 'blue', 3, 
  //                  '$'.number_format($pact,2,'.',','), TEXT_RIGHT, FONT_1);
  
  if($tipo==1) {
    
    if($max_t!=$ult_t) $grafico->setFeaturePoint($max_n, $max, $color2 , 
								$max_t, TEXT_TOP,  FONT_1, 3);
                    
	$grafico->setFeaturePoint($ult_n, $ult, $color , 
                    $ult_t, TEXT_RIGHT, FONT_2, 6);
  } else {
   
    if($max_t!=$ult_t) $grafico->setFeaturePoint($max_n, $max, $color2 , 
								$max_t, TEXT_TOP, FONT_1, 2);

	$grafico->setFeaturePoint($ult_n, $ult, $color , 
                    $ult_t, TEXT_RIGHT, FONT_1, 3);  
                    
  }
  
  
  if(isset($_GET['inspect'])) {  
	  
	  print_r($cat);
	  
	  $grafico->Inspect(); exit(); }
  
  $grafico->Render();
  
  $grafico->Output();
  
?>
