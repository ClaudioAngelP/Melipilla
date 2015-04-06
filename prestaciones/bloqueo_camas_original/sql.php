<?php 

	require_once("../../conectar_db.php");
	
	$ncama=$_POST['cama_id']*1;
	
	$fecha_ini=pg_escape_string($_POST['bloq_fecha_ini']);
	$fecha_fin=pg_escape_string($_POST['bloq_fecha_fin']);

	if($fecha_fin!='') $fecha_fin="'$fecha_fin'"; else $fecha_fin='null';

	$mot=explode('|',$_POST['bloq_motivo']);

	$motivo=($mot[0]*1);
	$observaciones=pg_escape_string($_POST['bloq_observaciones']);
	
	pg_query("INSERT INTO bloqueo_camas VALUES (
		DEFAULT,
		$ncama,
		".$_SESSION['sgh_usuario_id'].",
		'$fecha_ini',
		$fecha_fin,
		$motivo,
		'$observaciones'
	);");

?>
