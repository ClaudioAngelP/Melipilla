<?php

  require_once('sparklines.php');
  require_once('../conectar_db.php');
  
  $art_id=$_GET['art_id']*1;
  $bod_id=$_GET['bod_id']*1;
  $backgr=$_GET['backgr'];
  $pact=$_GET['pact']*1;
  
  $precios = pg_query("
	SELECT (stock_subtotal/stock_cant) FROM stock
	JOIN logs ON stock_log_id=log_id
	WHERE stock_art_id=$art_id AND log_tipo=1 ORDER BY log_fecha;
  ");  
  
  $grafico = new Sparkline();
  $grafico->setSize(350,60);
  $grafico->setPadding(30,20,20,70);
  $grafico->setLineSize(2);
    
  $min=0;
  $min_n=0;
  $max=0;
  $min_n=0;
  
  $ult=0;
  $ult_n=0;
  
  for($i=0;$i<pg_num_rows($precios);$i++) {
  
    $val = round(pg_fetch_result($precios, $i, 0),2);
  
    $grafico->addDataPoint($i, $val);
  
    if($val>=$max) { $max=$val; $max_n=$i; }
    if($val<=$min) { $min=$val; $min_n=$i; }
    
    if($i==0) { $max=$val; $min=$val; }

    if($i==pg_num_rows($precios)-1) { $ult=$val; $ult_n=$i; }
    
  }
  
  $grafico->setFeaturePoint($min_n, $min, Array(0,200,0), 
                    '$'.number_format($min,2,',','.'), TEXT_BOTTOM, FONT_2, 5);
  $grafico->setFeaturePoint($max_n, $max, Array(255,0,0), 
                    '$'.number_format($max,2,',','.'), TEXT_TOP, FONT_2, 5);
                    
  $grafico->SetFeaturePoint($ult_n, $ult, Array(0,0,255),
                    '$'.number_format($ult,2,',','.'), TEXT_RIGHT, FONT_2, 5);
  
  $grafico->Render();
  
  $grafico->Output();
  
?>
