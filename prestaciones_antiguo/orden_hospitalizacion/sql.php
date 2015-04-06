<?php 

	require_once('../../conectar_db.php');

	$centro_ruta=pg_escape_string(utf8_decode($_POST['centro_ruta']));
	$carp_id=$_POST['carp_id']*1;
	$folio=($_POST['nro_folio'])*1;
	$fecha=pg_escape_string(utf8_decode($_POST['fecha']));
	
	$pac_id=$_POST['paciente_id']*1;
	
	if(isset($_POST['ca_id']))
		$ca_id=$_POST['ca_id']*1;
	else 
		$ca_id=0;	

	$doc_id=$_POST['doc_id']*1;
	
	
	$codigo=pg_escape_string(utf8_decode($_POST['cod_presta']));
	
	$ges=$_POST['ges']*1;
	$tipo_atencion=$_POST['tipo_aten']*1;
	$diag_cod=pg_escape_string(utf8_decode($_POST['diag_cod']));
	$diagnostico=pg_escape_string(utf8_decode($_POST['diagnostico']));
	$observacion=pg_escape_string(utf8_decode($_POST['oa_observacion']));
	$oa_prioridad=$_POST['prioridad']*1;

	//$fecha_aten=pg_escape_string(utf8_decode($_POST['fecha_aten']));
	
	pg_query("INSERT INTO orden_atencion VALUES (
		DEFAULT, $folio, '$fecha', $pac_id, 
		$sgh_inst_id, $sgh_inst_id, 0, -1, -1, '', 
		'$codigo', '$diag_cod', -1, $doc_id, DEFAULT, 0, $ca_id, 
		".$_SESSION['sgh_usuario_id'].", 0, DEFAULT, '$centro_ruta', $oa_prioridad, 
		DEFAULT, '$diagnostico', DEFAULT, DEFAULT, DEFAULT, DEFAULT, 
		$ges, $tipo_atencion, $carp_id, '$observacion'); 
	");

	exit('OK');

?>