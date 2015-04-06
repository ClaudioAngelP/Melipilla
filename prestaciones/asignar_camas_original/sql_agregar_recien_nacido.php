<?php 

	require_once('../../conectar_db.php');
	
	if(isset($_POST['hospp_id'])) {
		
		$hospp_id=$_POST['hospp_id']*1;
		
		pg_query("DELETE FROM hospitalizacion_partos WHERE hospp_id=$hospp_id;");
		
		exit();
		
	}
	
	
	$hosp_id=$_POST['hosp_id'];

	$cond=$_POST['rn_condicion']*1;
	$sexo=$_POST['rn_sexo']*1;
	$peso=$_POST['rn_peso']*1;
	$apgar=$_POST['rn_apgar']*1;
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	pg_query("INSERT INTO hospitalizacion_partos
	VALUES (DEFAULT, $hosp_id, 0, $cond, $sexo, $peso, $apgar, CURRENT_TIMESTAMP, $func_id);");

?>
