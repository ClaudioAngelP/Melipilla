<?php 

	require_once('../conectar_db.php');
	
	$fa=explode("\n",utf8_decode(file_get_contents('ordenes_psiq.csv')));
	
	//pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($fa);$i++) {
		
		if(trim($fa[$i])=="") continue;
	
		$r=explode('|',pg_escape_string(($fa[$i])));
		
		$nro=$r[2];
		$orden=$r[3];
			
		$f=cargar_registro("SELECT * FROM documento WHERE doc_num=$nro;");
			
		if($f) {
		
			$o=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='$orden';");
			if($o){
					pg_query("UPDATE documento SET doc_orden_id=".$o['orden_id'].",doc_orden_desc='".$o['orden_numero']."' WHERE doc_id=".$f['doc_id'].";");
			}else{
					print("ORDEN $orden NO EXISTE.<br>");
			}
				
		} else {
			print("DOCUMENTO $nro NO EXISTE.<br>");
		}
		
		
	}
	
	//pg_query("COMMIT;");

?>
