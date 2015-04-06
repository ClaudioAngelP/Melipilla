<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  
  
  //$bod_id=4;
  
  //$fi=explode("\n", utf8_decode(file_get_contents('inventario_farmacia_cae.csv')));

  $bod_id=4;
  
  $fi=explode("\n", utf8_decode(file_get_contents('stock_insumos_clinicos.csv')));
  
  pg_query("START TRANSACTION;");
  
  pg_query("INSERT INTO logs VALUES ( DEFAULT, 7, 20, '04/11/2013 17:34:39', 0, 0, 0, 'Carga inicial realizada masivamente por Sistemas Expertos.' );");
  
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
     
     if(!isset($r[3]) OR trim($r[3])=='') continue;
      
     //$id=trim($r[0]);
     
     $art_codigo=trim(strtoupper($r[3]));
     $nom=trim(strtoupper($r[4]));
		
	$ff=explode(' ', pg_escape_string($r[6]));

	$forma=trim(strtoupper($ff[0]));
	
	$f=cargar_registro("SELECT * FROM bodega_forma WHERE forma_nombre ILIKE '%$forma%';");
	
	if($f) {
		$forma_id=$f['forma_id'];
	} else {
		pg_query("INSERT INTO bodega_forma VALUES (DEFAULT, '$forma');");
		$forma_id="";
		
	}
     
     $art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
     
     if(!$art) { 

		pg_query("INSERT INTO articulo VALUES (
					DEFAULT, '$art_codigo', '0', '$nom', '$nom',
					$forma_id, false, 0, false, '2204001', 0, 0, 0, 0, 1,
					true, 0, false
				);
			");
			
		$art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
	 }
	 
	 //pg_query("update articulo SET art_nombre='$nom', art_glosa='$nom' where art_codigo='$art_codigo';");
     
     $art_id=$art['art_id']*1;

     	
         if(trim($r[8])==''){
         	$cant=0;
         }else{
         	$cant=trim(str_replace('.','',$r[8]))*1;
         } 

         if($cant==0) continue;
         
         if($art['art_vence']=='1') $lote="'30/12/2013'"; else $lote='null';
         
          $total=($cant*$r[9]);
          
         pg_query("INSERT INTO stock VALUES 
            ( DEFAULT, $art_id, $bod_id, 
            $cant, CURRVAL('logs_log_id_seq'), 
            $lote, $total );");
            
       pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bod_id);");
            
  }
  
  $log=cargar_registro("SELECT * FROM logs WHERE log_id=CURRVAL('logs_log_id_seq');");
  
  echo "(".sizeof($fi).") OK LOG_ID: <B>".$log['log_id']."</B>";
  
  pg_query("COMMIT");

?>
