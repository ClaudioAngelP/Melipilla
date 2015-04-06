<?php 

	require_once('../../conectar_db.php');
	
	$autf_id=$_POST['autf_id']*1;
	$pac_id=$_POST['pac_id']*1;
	$inicio=pg_escape_string($_POST['finicio']);
	$final=pg_escape_string($_POST['ffinal']);
	
	if($final!='') 
		$final="'$final'";
	else
		$final='null';
		
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	pg_query("INSERT INTO autorizacion_farmacos_pacientes VALUES (DEFAULT, $autf_id, $pac_id, $func_id, 0, '$inicio', $final);");

?>
