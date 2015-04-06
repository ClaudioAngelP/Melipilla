<?php 

	require_once('../../conectar_db.php');
	
	$us_id=$_POST['us_id'];
	
	if(isset($_POST['fosa'])) {	
		$fosa=pg_escape_string(utf8_decode($_POST['fosa']));
		$f=explode('|',$fosa);
		$clase=$f[0];
		$codigo=$f[1];
		$numero=$f[2]*1;
		$letra=trim($f[3]);
		$ubicacion=$f[4];
	} else { 
		$fosa=false;
	}
		
	if($fosa) {
		
		if($numero==0)
			$us_vence='null';
		else 
			$us_vence="(current_date + ('3 year'::interval))";
			
		pg_query("
			UPDATE uso_sepultura SET
				sep_clase='$clase',
				sep_codigo='$codigo',
				sep_numero=$numero,
				sep_letra='$letra',
				us_vence=$us_vence,
				us_ubicacion='$ubicacion'
			WHERE us_id=$us_id		
		");		
			
	} else {
	
		pg_query("
			UPDATE uso_sepultura SET
				us_vence=(current_date + ('1 year'::interval))
			WHERE us_id=$us_id		
		");								

	}

?>