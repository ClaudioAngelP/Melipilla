<?php require_once('../../conectar_db.php');

	$bodega=$_POST['bod_id'];
	
	$ids=explode('&&&' , $_POST['ids']);
	
	$comentarios = pg_escape_string($_POST['comentarios']);
	  
	pg_query("INSERT INTO logs VALUES (
		DEFAULT, ".($_SESSION['sgh_usuario_id']*1).", 
		30, now(), 0, 0, 0, '$comentarios' 
		)");
  
	$log = "CURRVAL('logs_log_id_seq')";

	for($i=0;$i<sizeof($ids);$i++){

		list($art, $lote, $cant)=explode('!!',$ids[$i]);

		$cant*=1;

		if($lote!='')
			$fec = "'".$lote."'";
		else
			$fec = 'null';
			
			if($art){
					pg_query("insert into stock (stock_art_id, stock_bod_id, stock_cant, stock_log_id, stock_vence, stock_subtotal)
					select stock_art_id, $bodega, -cantidad, $log, stock_vence, 0 from (
					select stock_art_id, stock_vence, SUM(stock_cant) AS cantidad
					FROM stock
						  WHERE stock_bod_id=$bodega AND stock_art_id=".$art."
					group by stock_art_id, stock_vence
					) AS foo where cantidad<>0;");
			}
			
			
		if($cant!=0){
			pg_query("INSERT INTO stock VALUES (DEFAULT, $art, $bodega, $cant, $log, $fec, 0 )");
			//print("INSERT INTO stock VALUES (DEFAULT, $art, $bodega, $cant, $log, $fec, 0 );\n\n");
		}
	
	}
	
	list(list($log_id)) = cargar_registros("SELECT CURRVAL('logs_log_id_seq');",false);
  
	print(json_encode($log_id));
  	
?>
