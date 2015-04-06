<?php 

	require_once('../../conectar_db.php');
	
	if(isset($_POST['hosphc_id'])) {
		
		$hosphc_id=$_POST['hosphc_id']*1;
		
		pg_query("DELETE FROM hospitalizacion_hoja_cargo WHERE hosphc_id=$hosphc_id;");
		
		exit();
		
	}
	
	
	$hosp_id=$_POST['hosp_id'];

	$art_id=pg_escape_string(utf8_decode($_POST['art_id']));
	$cantidad=$_POST['art_cantidad']*1;
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	pg_query("INSERT INTO hospitalizacion_hoja_cargo
	VALUES (DEFAULT, $hosp_id, CURRENT_TIMESTAMP, $func_id, $art_id, $cantidad);");

?>
