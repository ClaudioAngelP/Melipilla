<?php 

	error_reporting(E_ALL);

  	require_once('../conectar_db.php');
  
  	$fi=explode("\n", utf8_decode(file_get_contents('stock_intermedio.csv')));
  
  	pg_query("START TRANSACTION;");
  
  	pg_query("INSERT INTO logs VALUES (
   	DEFAULT, 7, 20, NOW(), 
   	0, 0, 0, 
   	'CARGA AUTOMÁTICA REALIZADA POR EMPRESA SISTEMAS EXPERTOS' 
  		);");
  
  	$log = "CURRVAL('logs_log_id_seq')";
    
  	for($i=1;$i<sizeof($fi);$i++) {
      
   	$r=explode('|',$fi[$i]);
      
     	$art_codigo=trim(strtoupper($r[0]));
     	$art_nombre=trim(strtoupper($r[1]));
     
		$art=cargar_registro("SELECT art_id FROM articulo WHERE art_codigo='$art_codigo' OR art_glosa ILIKE '%$art_nombre%';");
         
     	if(!$art) { 
     	
	   	print("Art&iacute;culo nuevo... <i><b>$art_codigo</b> $art_nombre</i><br>");
	
			
			$forma=trim(strtoupper($r[2]));
			$f=cargar_registro("SELECT * FROM bodega_forma WHERE forma_nombre='$forma';");
						
			if($f) {
				$forma_id=$f['forma_id'];
				print("<i>$forma</i> ya existe.<br>");
			} else {
				//pg_query("INSERT INTO bodega_forma VALUES (DEFAULT, '$forma');");
				$forma_id="CURRVAL('bodega_forma_forma_id_seq')";
				print("<i>$forma</i> forma nueva.<br>");
			}
				
			pg_query("
					INSERT INTO articulo VALUES (
					DEFAULT, '$art_codigo', '0', '$art_nombre', '$art_nombre',
					$forma_id, false, 0, false, '11111111', 0, 0, 0, 0, 1,
					true, 0, false
				);
					
			");
			$art=cargar_registro("SELECT art_id FROM articulo WHERE art_codigo='$art_codigo' OR art_glosa LIKE '$art_nombre';");
		
  		}else{
	 		print("Art&iacute;culo existente... <i><b>$art_codigo</b> $art_nombre</i><br>");
	 	}
     
     	$art_id=$art['art_id']*1;
     	$art_stock=trim($r[5]);
     	$art_pedido=trim($r[3]);
     	$art_critico=trim($r[4]);
     	
     	if($art_stock==0) continue;
      
		if(!$art_stock){  
      	pg_query("INSERT INTO stock VALUES 
            ( DEFAULT, $art_id, 17, 
            $art_stock, CURRVAL('logs_log_id_seq'), 
            '20-12-2020', 0 );
       	");
       	print("Stock cargado <b>$art_stock</b><br>");
       }else{
       	
       }
       
       $critico=cargar_registro("SELECT critico_art_id FROM stock_critico WHERE critico_art_id=$art_id AND critico_bod_id=17;");
       if(!$critico){
       	pg_query("INSERT INTO stock_critico VALUES
       		($art_id,$art_pedido,$art_critico,17,null);
       	");
       	print("Stock cr&iacute;tico ingresado correctamente<br>");
       }else{
       	pg_query("UPDATE stock_critico SET critico_gasto=null WHERE critico_art_id=$art_id AND critico_bod_id=17;");
       	print("Stock cr&iacute;tico actualizado<br>");
       }
       
       pg_query("INSERT INTO articulo_bodega VALUES(DEFAULT,$art_id,17)");
       print("Art&iacute;culo visualizable... Inserci&oacute;n terminada.<br><br>");
            
  	}
  	flush();
	
	pg_query("COMMIT");
	

?>
