<?php 

	require_once('../../conectar_db.php');
	
	$ids=explode('|',$_POST['ids']);
	
	for($i=0;$i<sizeof($ids);$i++) {
	
		$id=$ids[$i];
		
		$nro_cama=$_POST['nro_cama_'.$id]*1;
		
		if($nro_cama!=0) {
			pg_query("
				UPDATE hospitalizacion SET
				hosp_numero_cama=$nro_cama 
				WHERE hosp_id=$id;			
			");
		}	
	
	}

?>