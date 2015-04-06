<?php

	  require_once('../../conectar_db.php');


	
		$func_id = $_POST['func_id']*1;
		$ac_id = ($_POST['ac_id']*1);
		$fecha='current_timestamp';	
		$devol_id= $_POST['dev_id']*1;
		
		
		pg_query($conn, "
				UPDATE devolucion_boletines
				SET ac_id=$ac_id,dev_ejecuta=$fecha,func_id_ejecuta=$func_id where devol_id=$devol_id
			");
		
		
		
		
			
	print(json_encode(array(true,$devol_id)));
?>
