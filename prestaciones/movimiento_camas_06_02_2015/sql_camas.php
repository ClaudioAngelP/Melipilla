<?php 

	require_once('../../conectar_db.php');

	error_reporting(E_ALL);
	
	//$ucamas=json_decode($_POST['ucamas']);
	$datos=json_decode($_POST['movs'], true);
	
	//print_r($ucamas);
	//print_r($datos);
	
	pg_query('START TRANSACTION;');
	
	for($i=0;$i<sizeof($datos);$i++) {
	
		$r=$datos[$i];
		
		if($r['mov_id']==0) {
			
			pg_query("UPDATE hospitalizacion SET 
							hosp_numero_cama=".($r['mov_id_dst']*1)."
						 WHERE hosp_id=".($r['ucama_id']*1));
						 
			pg_query("UPDATE hospitalizacion SET 
							hosp_fecha_hospitalizacion=CURRENT_TIMESTAMP
						WHERE hosp_id=".($r['ucama_id']*1));
						 	
		} else {
			
			pg_query("UPDATE hospitalizacion SET 
							hosp_numero_cama=".($r['mov_id_dst']*1)."
						 WHERE hosp_id=".($r['ucama_id']*1));
						 			
			if($r['mov_id_dst']==-1) {
				pg_query("UPDATE hospitalizacion SET
							hosp_fecha_egr=current_timestamp
						 WHERE hosp_id=".($r['ucama_id']*1));
			} else {
				pg_query("INSERT INTO paciente_traslado 
					VALUES (DEFAULT, current_timestamp, 
					'', 
					".($r['ucama_id']*1).", 
					".($r['mov_id']*1).", 
					".($r['mov_id_dst']*1).");");
			}			
			
		}

	}
	
	pg_query('COMMIT;');

	echo(json_encode(true));

?>
