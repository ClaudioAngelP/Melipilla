<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');

	$pedidos=cargar_registros_obj("SELECT * FROM pedido WHERE pedido_fecha BETWEEN '01/06/2011' AND '15/07/2011';");
	
	//$pedidos=cargar_registros_obj("SELECT * FROM pedido WHERE pedido_nro=24999;");
	
	pg_query("START TRANSACTION;");
	
	for($i=0;$i<sizeof($pedidos);$i++) {
		
		$pedido_nro=$pedidos[$i]['pedido_nro']*1;
		
		$ok=0; $borrados=0;
		
		$log=cargar_registro("SELECT * FROM logs WHERE log_id_pedido=".$pedidos[$i]['pedido_id']);
		
		if(!$log) {

			print("PEDIDO: $pedido_nro SIN MOVIMIENTOS <br /><br />");
			
			continue;
			
		}
		
		$stock=cargar_registros_obj("SELECT * FROM stock WHERE stock_log_id=".$log['log_id']);
		
		$tmp=array();
		
		for($j=0;$j<sizeof($stock);$j++) {
			
			$fnd=false;
			
			for($k=0;$k<sizeof($tmp);$k++) {
				
				if(
					$stock[$j]['stock_bod_id']==$tmp[$k]['stock_bod_id'] AND
					$stock[$j]['stock_art_id']==$tmp[$k]['stock_art_id'] AND
					$stock[$j]['stock_cant']==$tmp[$k]['stock_cant'] AND
					$stock[$j]['stock_log_id']==$tmp[$k]['stock_log_id'] AND
					$stock[$j]['stock_vence']==$tmp[$k]['stock_vence']
				) {
					$fnd=true; break;
				} 
				
			}
			
			if($fnd) {
				pg_query("DELETE FROM stock WHERE stock_id=".$stock[$j]['stock_id']);
				$borrados++;
			} else {
				$tmp[]=$stock[$j];
				$ok++;
			}
			
			
		}
		
		print("PEDIDO: $pedido_nro OK: $ok BORRADOS: $borrados <br /><br />");
		
	}
	
	pg_query("COMMIT;");

?>
