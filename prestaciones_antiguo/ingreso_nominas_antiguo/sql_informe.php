<?php 

	require_once('../../conectar_db.php');

	function fix($str) {
		$test=explode('/',$str);
		if(checkdate($test[1],$test[0],$test[2]))
			return "'".$str."'";
		else 
			return 'null';
	}		

	$nomd_id=$_POST['nomd_id']*1;
	$html=pg_escape_string($_POST['html']);
	$fecha1=fix($_POST['fecha1']);
	$fecha2=fix($_POST['fecha2']);
	$fecha3=fix($_POST['fecha3']);
	$doc_id=$_POST['doc_id'];
	
	pg_query("DELETE FROM nomina_detalle_informe WHERE nomd_id=$nomd_id");	
	
	pg_query("INSERT INTO nomina_detalle_informe VALUES (
		$nomd_id, $fecha1, $fecha2, $fecha3, '$html', DEFAULT, $doc_id,
		".$_SESSION['sgh_usuario_id'].", 0	
	);");

?>