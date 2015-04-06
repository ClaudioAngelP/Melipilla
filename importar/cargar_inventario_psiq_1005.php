<?php require_once('../conectar_db.php'); error_reporting(E_ALL);

  $bod_id=1;
  
  $fi=explode("\n", utf8_decode(file_get_contents('inventario_psiq_1005.csv')));
  
  pg_query("START TRANSACTION;");
  
  pg_query("INSERT INTO logs VALUES (
    DEFAULT, 7, 20, '10/05/2013', 
    0, 0, 0, 
    'CARGA AUTOMÁTICA REALIZADA POR EMPRESA SISTEMAS EXPERTOS' 
  );");
  
  $ignorados=0;
  $ign='';
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
     
     if(!isset($r[0]) OR trim($r[0])=='') continue;    
           
      $artcod=str_pad($r[0],5,0,STR_PAD_LEFT);
      
     $art_codigo='ART0'.$artcod;
     
     $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
     
     if(!$art) { 
		 $ignorados++;
		 $ign.="$ignorados.- [$i][".$r[0]."|".$r[1]."|".$r[3]."|".$r[5]."|".$r[6]."][$art_codigo]<br>";
		continue;		 
	 }
     
     $art_id=$art['art_id']*1; 	
     	
         if(trim($r[6])==''){
         	$cant=0;
         }else{
         	$cant=trim(str_replace('.','',$r[6]))*1;
         } 

         if($cant==0){
			  $ignorados++;
			  $ign.="$ignorados.- [$i][".$r[0]."|".$r[1]."|".$r[3]."|".$r[5]."|".$r[6]."][$art_codigo]<br>";
			  continue;
		}
         
         if($art['art_vence']=='true') $lote="'01/01/2020'"; else $lote='null';
         
         $punit=trim($r[3]);
         
         $subtotal=($cant*$punit);
                   
         pg_query("INSERT INTO stock VALUES 
            ( DEFAULT, $art_id, $bod_id, 
            $cant, CURRVAL('logs_log_id_seq'), 
            $lote, $subtotal );");
            
  }
  
  $log=cargar_registro("SELECT * FROM logs WHERE log_id=CURRVAL('logs_log_id_seq');");
  
  echo "(".sizeof($fi).") OK <b>$ignorados</b> ARTICULOS IGNORADOS EN LA CARGA... LOG_ID: <B>".$log['log_id']."</B><br>";
  echo $ign;
  
  pg_query("COMMIT");

?>
