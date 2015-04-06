<?php 

	require_once('../conectar_db.php');
	
	$l=explode("\n",utf8_decode(file_get_contents("camas.csv")));
	
	$servicios=array();
	
	for($i=1;$i<sizeof($l);$i++) {
	
		if(trim($l[$i])=='') continue;
	
		$r=explode('|',$l[$i]);
				
		$serv=trim($r[0]);
		$sala=trim($r[1]);
		
		if(!isset($servicios[$serv])) {
			$servicios[$serv]=Array();
			$servicios[$serv]['nombre']=pg_escape_string($serv);
			$servicios[$serv]['salas']=array();
			$servicios[$serv]['camas']=0;			
		}

		if(!isset($servicios[$serv]['salas'][$sala])) {
			$servicios[$serv]['salas'][$sala]=array();
			$servicios[$serv]['salas'][$sala]['nombre']=pg_escape_string($sala);						
			$servicios[$serv]['salas'][$sala]['camas']=$r[2]*1;						
		}
		
		$servicios[$serv]['camas']+=$r[2]*1;			
	
	}
	
	foreach($servicios AS $key=>$val) {
		
		pg_query("INSERT INTO clasifica_camas 
					VALUES (DEFAULT, '".$val['nombre']."', 
					COALESCE((SELECT tcama_num_fin+1 FROM clasifica_camas ORDER BY tcama_num_fin DESC LIMIT 1),1), 
					COALESCE((SELECT tcama_num_fin+1 FROM clasifica_camas ORDER BY tcama_num_fin DESC LIMIT 1),1)+".$val['camas']."-1,
					false, '', false, false
					);");
		
		foreach($val['salas'] AS $k=>$v) {
			
			pg_query("INSERT INTO tipo_camas 
					VALUES (DEFAULT, '".$v['nombre']."', 
					COALESCE((SELECT cama_num_fin+1 FROM tipo_camas ORDER BY cama_num_fin DESC LIMIT 1),1), 
					COALESCE((SELECT cama_num_fin+1 FROM tipo_camas ORDER BY cama_num_fin DESC LIMIT 1),1)+".$v['camas']."-1,
					null
					);");
			
		}
		
	}


?>
