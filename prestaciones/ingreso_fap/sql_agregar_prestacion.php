<?php 

	require_once('../../conectar_db.php');
	
	if(isset($_POST['hospp_id'])) {
		
		$hospp_id=$_POST['hospp_id']*1;
		
		$func_id=$_SESSION['sgh_usuario_id']*1;
		
		$chk=cargar_registro("SELECT hospp_fecha_realizado FROM hospitalizacion_prestaciones WHERE hospp_id=$hospp_id;");
		
		if(isset($_POST['remover'])) {
			pg_query("DELETE FROM hospitalizacion_prestaciones WHERE hospp_id=$hospp_id;");
			
			exit();
		}
		
		if($chk['hospp_fecha_realizado']=='') {
	
			pg_query("UPDATE hospitalizacion_prestaciones SET 
				hospp_fecha_realizado=CURRENT_TIMESTAMP, 
				hospp_func_id2=$func_id 
				WHERE hospp_id=$hospp_id
			");
			
		} else {

			pg_query("UPDATE hospitalizacion_prestaciones SET 
				hospp_fecha_realizado=null, 
				hospp_func_id2=$func_id 
				WHERE hospp_id=$hospp_id
			");
			
		}
		
		exit();
		
	}
	
	$hosp_id=$_POST['hosp_id']*1;
	$fap_id=$_POST['fap_id']*1;

	$codigo=pg_escape_string(utf8_decode($_POST['codigo_presta']));
	$nombre=pg_escape_string(utf8_decode($_POST['nombre_presta']));
	$cantidad=$_POST['cant_presta']*1;
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	pg_query("INSERT INTO hospitalizacion_prestaciones 
	VALUES (DEFAULT, $hosp_id, CURRENT_TIMESTAMP, $func_id, '$codigo', '$nombre', $cantidad, null, null, $fap_id);");

?>
