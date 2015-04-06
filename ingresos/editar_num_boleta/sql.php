<?php

	  require_once('../../conectar_db.php');


	
		$func_id = $_POST['func_id']*1;
		$ac_id = ($_POST['ac_id']*1);
		$fecha='current_timestamp';	
		$bolnum_id= $_POST['bolnum_id']*1;
		$boleta= $_POST['boleta_numero']*1;
		
		
		pg_query($conn, "
				UPDATE boletines
				SET   numboleta=$boleta where bolnum=$bolnum_id
			");
		
		
		
		
			
	print(json_encode(array(true,$bolnum_id)));
?>
