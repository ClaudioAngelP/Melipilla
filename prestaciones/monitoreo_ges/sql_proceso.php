<?php 

    require_once('../../conectar_db.php');
    
	 set_time_limit(0);   
	 
	 error_reporting(E_ALL); 
    
    $inicio=trim(file_get_contents("tmp/pointer"))*1;
    $fecha=pg_escape_string(trim(file_get_contents("tmp/date")));
    $final=trim(file_get_contents("tmp/end_pointer"))*1; 

	 $html=''; $registros=array(); $contador=0; $regs=array(); $totalr=0;

	 function extraer_registros($size) {
	 	
	 	GLOBAL $html, $registros, $contador, $totalr;
	 	
	 	$registros=array();
		$contador=0;	 	
	 	
	 	preg_match_all('#<td class="htmltabladetalle">(.*)</td>#', $html, $regtmp);
	 	
		for($i=0;$i<sizeof($regtmp[1]);$i++) {

			$num=floor($i/$size);
			
			$registros[$num][$i%$size]=$regtmp[1][$i];
		
		}		
		
		$totalr=sizeof($registros);
		
	 }
    
    @pg_query("INSERT INTO fechas_monitoreo_ges VALUES ('$fecha');");
    
    $ftam=explode('|', trim(file_get_contents("tmp/file_sizes")));
    
    $f=0;
    
    function tfecha($str) {
    
        $str = explode('/', trim($str));
        
        //return date('d/m/Y', mktime(0,0,0,$str[1]*1,(($str[0]*1)-1),(($str[2]*1)-4)));
        return date('d/m/Y', mktime(0,0,0,$str[1]*1,(($str[0]*1)),(($str[2]*1))));
    
    }
    
    function terminar() {

		GLOBAL $regs;

      //system('rm -rf tmp');
        
      print('
      <table style="width:100%;">
      <tr class="tabla_fila"><td style="text-align:right;">Registros Vigentes:</td>
      <td style="text-align:right;width:50%;">'.number_format($regs[0],0,',','.').'.-</td></tr>
		<tr class="tabla_fila2"><td style="text-align:right;">Registros Vencidos:</td>
		<td style="text-align:right;">'.number_format($regs[1],0,',','.').'.-</td></tr>
		<tr class="tabla_fila"><td style="text-align:right;">Registros Cerrados:</td>
		<td style="text-align:right;">0.-</td></tr>
      <tr class="tabla_fila2" style="font-weight:bolder;"><td style="text-align:right;">Total:</td>
      <td style="text-align:right;">'.number_format($regs[0]+$regs[1],0,',','.').'.-</td>
      </tr></table>');

    }

    //if(file_exists('tmp/vigentes.csv')) 
    //	$csv = fopen('tmp/vigentes.csv', 'r');
    //else {
    
    $html = file_get_contents('tmp/vigentes.html');
    extraer_registros(8);
    	
    $inicio=0;
    $estado=0;

	 $regs[$estado]=0;

    while(1) {

        $inicio++; 
        
		  //if(isset($csv))        
        	//$reg=fgetcsv($csv,1000,",");
        //else
	 	  if($contador>=$totalr) break;
	 	  else $reg=$registros[$contador++]; 
        
        if($reg[6]=='') continue;
            
        $test=explode('-',trim($reg[4]));
        
        //if(count($test)!=2) continue;

        $rut=(trim($reg[1])).'-'.trim(strtoupper($reg[2]));
        
        $pat=trim($reg[0]);
        $nombre=pg_escape_string(trim($reg[3]));
        $finicio=tfecha($reg[4]);
        $flimite=tfecha($reg[5]);
        $dfalta=trim($reg[6]);
        $garantia=trim($reg[7]);

        $ptrad=cargar_registros_obj("
            SELECT * FROM patologias_sigges_traductor 
            WHERE   pst_problema_salud='".pg_escape_string($pat)."' AND
                    pst_garantia='".pg_escape_string($garantia)."'
        ");
        
        if(!$ptrad) $ptrad[0]['pst_id']=-1;

        $chk=cargar_registro("SELECT * FROM monitoreo_ges 
					WHERE mon_rut='$rut' AND 
					mon_fecha_inicio='$finicio' AND
					mon_fecha_limite='$flimite' AND
					mon_pst_id=".$ptrad[0]['pst_id']." AND
					mon_condicion IN (0,1)
					");
					
		if(!$chk) {
   
			pg_query("INSERT INTO monitoreo_ges VALUES (
                    DEFAULT, '$fecha', 
                    0, 0, null, null, 
                    false, 
                    ".$ptrad[0]['pst_id'].",
                    '$rut', '$nombre', $estado, '$finicio', '$flimite',
                    '".pg_escape_string($pat)."', 
                    '".pg_escape_string($garantia)."'
                    )");

		} else {
		
			pg_query("UPDATE monitoreo_ges 
				SET mon_condicion=$estado, mon_fecha_monitoreo=now()
				WHERE mon_id=".$chk['mon_id']); 		
		
		}
                    
	 	$regs[$estado]++;

    }


    $html = file_get_contents('tmp/vencidos.html');
    extraer_registros(8);
    
    $inicio=0;
    $estado=1;

	 $regs[$estado]=0;
        
    while(1) {

        $inicio++; 
			
	 	  if($contador>=$totalr) break;
	 	  else $reg=$registros[$contador++]; 
        
        if($reg[6]=='') continue;
            
        $test=explode('-',trim($reg[4]));
        
        //if(count($test)!=2) continue;

        $rut=pg_escape_string((trim($reg[0])).'-'.trim(strtoupper($reg[1])));

        $pat=trim($reg[3]);
        $nombre=pg_escape_string(trim($reg[2]));
        $finicio=tfecha($reg[4]);
        $flimite=tfecha($reg[5]);
        $dfalta=trim($reg[6]);
        $garantia=trim($reg[7]);

        $ptrad=cargar_registros_obj("
            SELECT * FROM patologias_sigges_traductor 
            WHERE   pst_problema_salud='".pg_escape_string($pat)."' AND
                    pst_garantia='".pg_escape_string($garantia)."'
        ");
        
        if(!$ptrad) $ptrad[0]['pst_id']=-1;
        
        $chk=cargar_registro("SELECT * FROM monitoreo_ges 
					WHERE mon_rut='$rut' AND 
					mon_fecha_inicio='$finicio' AND
					mon_fecha_limite='$flimite' AND
					mon_pst_id=".$ptrad[0]['pst_id']." AND
					mon_condicion IN (0,1)
					");
					
		if(!$chk) {

			pg_query("INSERT INTO monitoreo_ges VALUES (
                    DEFAULT, '$fecha', 
                    0, 0, null, null, 
                    false, 
                    ".$ptrad[0]['pst_id'].",
                    '$rut', '$nombre', $estado, '$finicio', '$flimite',
                    '".pg_escape_string($pat)."', 
                    '".pg_escape_string($garantia)."'
                  )");
                  
        } else {
		
			pg_query("UPDATE monitoreo_ges 
				SET mon_condicion=$estado, mon_fecha_monitoreo=now()
				WHERE mon_id=".$chk['mon_id']); 
			
		}
        
	 	$regs[$estado]++;

    }

    terminar();
    
?>
