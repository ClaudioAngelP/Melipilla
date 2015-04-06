<?php 

	require_once('../conectar_db.php');
	
	$q=cargar_registros_obj("
		select log_id, log_fecha, log_tipo from logs WHERE log_id in (
		select log_id FROM (select log_id, count(*) AS cnt from logs group by log_id) AS foo WHERE cnt>1
		) order by log_id");
		
	for($i=0;$i<100;$i++) {
	
		print('<br><br>log_id: '.$q[$i]['log_id'].' log_fecha '.$q[$i]['log_fecha'].' log_tipo '.$q[$i]['log_tipo'].'<br><br>');
		
		$log_id=$q[$i]['log_id'];
		
		$stock=cargar_registros_obj("SELECT * FROM stock WHERE stock_log_id=$log_id;");
		
		for($j=0;$j<sizeof($stock);$j++) {
			print("stock_id ".$stock[$j]['stock_id']." stock_art_id ".$stock[$j]['stock_art_id']." stock_cant ".$stock[$j]['stock_cant'].'<br>');
		}
	
	}
	
?>
