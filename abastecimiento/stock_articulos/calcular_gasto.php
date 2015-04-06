<?php

  require_once('../../graficos/sparklines.php');
  require_once('../../conectar_db.php');
  
  $art_id=$_GET['art_id']*1;
  $bod_id=$_GET['bod_id']*1;
  $g=$_GET['g']*1;

  if($g==0)
  $gasto=cargar_registros_obj("
  	SELECT SUM(gasto) AS gasto, year, month 
    FROM (
      SELECT 
        abs(stock_cant) AS gasto, 
        extract(YEAR FROM log_fecha) AS year, 
        extract(MONTH FROM log_fecha) AS month
  	 FROM stock
  	 JOIN logs ON log_id=stock_log_id AND log_tipo IN (2,9,15,18)
  	 WHERE 
  	   (stock_art_id=$art_id
  	   AND 
  	   log_fecha>=(now()-('12 month'::interval)) AND log_fecha<=now()
  	   AND
  	   stock_bod_id=$bod_id) AND NOT (log_tipo=2 AND stock_cant>0)
    ORDER BY year, month) AS foo 
    GROUP BY year, month; 
    
  ");
  else
  $gasto=cargar_registros_obj("
  	SELECT SUM(gasto) AS gasto, year, week 
    FROM (
      SELECT 
        abs(stock_cant) AS gasto, 
        extract(YEAR FROM log_fecha) AS year, 
        extract(WEEK FROM log_fecha) AS week 
  	 FROM stock
  	 JOIN logs ON log_id=stock_log_id AND log_tipo IN (2,9,15,18)
  	 WHERE 
  	   (stock_art_id=$art_id
  	   AND 
  	   log_fecha>=(now()-('3 month'::interval)) AND log_fecha<=now()
  	   AND
  	   stock_bod_id=$bod_id) AND NOT (log_tipo=2 AND stock_cant>0)
    ORDER BY year, week) AS foo 
    GROUP BY year, week; 
    
  ");
  
  $grafico=new Sparkline();
  $grafico->setLineSize(1);
  $grafico->setSize(600,60);
  $grafico->setPadding(20,12,18,20);
  
  $min=0;
  $min_n=0;
  $max=0;
  $min_n=0;
  
  $val=0;
  
  for($i=0;$i<count($gasto);$i++) {

    if($g==0)
      $fec=$gasto[$i]['month'].'/'.$gasto[$i]['year'];
    else
      $fec=$gasto[$i]['week'].'/'.$gasto[$i]['year'];
      
    $val=$gasto[$i]['gasto'];
    
    $grafico->AddDataPoint($i, $val);
  
    $grafico->SetFeaturePoint($i, $val, Array(0,180,0), 
                    $fec, 
                    TEXT_TOP, FONT_1, 7);
    
    $grafico->SetFeaturePoint($i, $val, array(255,0,0), 
                    number_format($val,0,',','.'), 
                    TEXT_BOTTOM, FONT_2, 5);
  
    /*
    $grafico->SetFeaturePoint($i, $val, 'green', 3, 
                    $fec, 
                    TEXT_TOP, FONT_1);
    $grafico->SetFeaturePoint($i, $val, 'red', 3, 
                    number_format($val,0,',','.'), 
                    TEXT_BOTTOM, FONT_1);
     */
     
  }
  
  //$grafico->SetFeaturePoint($min_n, $min, 'red', 3, 
  //                  number_format($min,2,'.',','), TEXT_TOP, FONT_1);
  
  $grafico->Render();
  
  $grafico->Output();
  
  
?>