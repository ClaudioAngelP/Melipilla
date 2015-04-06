<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');

  $bod_id=2;
  
  $fi=explode("\n", utf8_decode(file_get_contents('inventario_psiq.csv')));
  
  pg_query("START TRANSACTION;");
  
  pg_query("INSERT INTO logs VALUES (
    DEFAULT, 7, 20, '31/12/2012', 
    0, 0, 0, 
    'CARGA AUTOMÁTICA REALIZADA POR EMPRESA SISTEMAS EXPERTOS' 
  );");
  
  $ignorados=0;
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
     
     if(!isset($r[1]) OR trim($r[1])=='') continue;
           
           
      $artcod=explode('-',$r[1]);
      
     $art_codigo='ART0'.$artcod[1];
     
     $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
     
     if(!$art) { 
		 $ignorados++;
		continue;		 
	 }
     
     $art_id=$art['art_id']*1; 	
     	
         if(trim($r[4])==''){
         	$cant=0;
         }else{
         	$cant=trim(str_replace('.','',$r[4]))*1;
         } 

         if($cant==0){
			  $ignorados++;
			  continue;
		}
         
         if($art['art_vence']=='true') $lote="'01/01/2020'"; else $lote='null';
         
         $punit=trim($r[5]);
         
         $subtotal=($cant*$punit);
                   
         pg_query("INSERT INTO stock VALUES 
            ( DEFAULT, $art_id, $bod_id, 
            $cant, CURRVAL('logs_log_id_seq'), 
            $lote, $subtotal );");
            
  }
  
  $log=cargar_registro("SELECT * FROM logs WHERE log_id=CURRVAL('logs_log_id_seq');");
  
  echo "(".sizeof($fi).") OK <b>$ignorados</b> ARTICULOS IGNORADOS EN LA CARGA... LOG_ID: <B>".$log['log_id']."</B>";
  
  pg_query("COMMIT");

?>
