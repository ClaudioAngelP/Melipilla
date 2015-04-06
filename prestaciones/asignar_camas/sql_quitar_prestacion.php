<?php

require_once('../../conectar_db.php');
	
	if(isset($_POST['hosphp_id'])) {
	
		$hosphp_id=$_POST['hosphp_id']*1;
		
		pg_query("DELETE FROM hospitalizacion_prestaciones WHERE hospp_id=$hosphp_id");
		
		exit();
	
	}

?>
