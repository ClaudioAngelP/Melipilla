<?php 

error_reporting(E_ALL);

  require_once('../conectar_db.php');
  
  
  //$bod_id=4;
  
  //$fi=explode("\n", utf8_decode(file_get_contents('inventario_farmacia_cae.csv')));

  $bod_id=3;
  
  $fi=explode("\n", utf8_decode(file_get_contents('stock_farmacia.csv')));
  
  pg_query("START TRANSACTION;");
  
  //pg_query("INSERT INTO logs VALUES ( DEFAULT, 7, 20, NOW(), 0, 0, 0, 'Carga inicial realizada masivamente por Sistemas Expertos.' );");
  
  
  for($i=1;$i<sizeof($fi);$i++) {
      
     $r=explode('|',$fi[$i]);
     
     if(!isset($r[2]) OR trim($r[2])=='') continue;
      
     //$id=trim($r[0]);
     
     $art_codigo=trim(strtoupper($r[2]));
     $nom=trim(strtoupper($r[3]));
     
     $ff=explode(' ', pg_escape_string($r[4]));

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
					$forma_id, false, 0, false, '2204004001', 0, 0, 0, 0, 1,
					true, 0, false
				);
			");
			
		$art=cargar_registro("SELECT * FROM articulo WHERE art_codigo='$art_codigo'");
	 }
	 
	 
     
     $art_id=$art['art_id']*1;
	 
	 //pg_query("UPDATE articulo SET art_vence='1' WHERE art_id=$art_id;");
     
     /*pg_query("insert into stock (stock_art_id, stock_bod_id, stock_cant, stock_log_id, stock_vence, stock_subtotal)
			select stock_art_id, 1, -cantidad, 295050, stock_vence, 0 from (
			select stock_art_id, stock_vence, SUM(stock_cant) AS cantidad
			FROM stock
			JOIN logs ON stock_log_id=log_id
			      LEFT JOIN pedido ON log_id_pedido=pedido_id
			      LEFT JOIN pedido_detalle
			              ON pedido_detalle.pedido_id=pedido.pedido_id
			              AND pedido_detalle.art_id=stock_art_id
			      LEFT JOIN pedido_log_rev USING (log_id)
			      WHERE stock_bod_id=1 AND stock_art_id=$art_id
			group by stock_art_id, stock_vence
			) AS foo where cantidad<>0;
			");*/
     	
     	
         if(trim($r[11])==''){
         	$cant=0;
         }else{
         	$cant=trim(str_replace('.','',$r[11]))*1;
         } 

         if($cant==0) continue;
         
         //if($art['art_vence']=='true') 
		 $lote="'".$r[9]."'"; 
		 //else $lote='null';
         
		 $valor=trim(str_replace('.','',$r[12]))*1;
		 
         $total=($cant*$valor);
		 
		// pg_query("UPDATE articulo SET art_val_ult=$valor, art_val_min=$valor, art_val_max=$valor, art_val_med=$valor WHERE art_id=$art_id;");
          
         /*pg_query("INSERT INTO stock VALUES 
            ( DEFAULT, $art_id, $bod_id, 
            $cant, 2831542, 
            $lote, $total );");*/
            
        pg_query("INSERT INTO articulo_bodega VALUES (DEFAULT,$art_id,$bod_id);");
            
  }
  
  //$log=cargar_registro("SELECT * FROM logs WHERE log_id=CURRVAL('logs_log_id_seq');");
  $log['log_id']=2831542;
  
  echo "(".sizeof($fi).") OK LOG_ID: <B>".$log['log_id']."</B>";
  
  pg_query("COMMIT");

?>
