<?php 

	require_once('../../conectar_db.php');

	$oa_id=$_POST['oa_id'];
	$centro_ruta=pg_escape_string(utf8_decode($_POST['centro_ruta']));
	$carp_id=$_POST['carp_id']*1;
	$fecha_aten=pg_escape_string(utf8_decode($_POST['fecha_aten']));
	$fecha_hosp=pg_escape_string(utf8_decode($_POST['fecha_hosp']));
	
	if($fecha_aten=='') $fecha_aten='null'; else $fecha_aten="'$fecha_aten'";
	if($fecha_hosp=='') $fecha_hosp='null'; else $fecha_hosp="'$fecha_hosp'";
	
	$doc_id=$_POST['doc_id']*1;
	
	$codigo=pg_escape_string(utf8_decode($_POST['codigo_prestacion']));
	
	$ges=$_POST['ges']*1;
	$tipo_atencion=$_POST['tipo_aten']*1;
	$diag_cod=pg_escape_string(utf8_decode($_POST['diag_cod']));
	$diagnostico=pg_escape_string(utf8_decode($_POST['diagnostico']));
	$observacion=pg_escape_string(utf8_decode($_POST['oa_observacion']));
	$oa_prioridad=$_POST['prioridad']*1;

	//$fecha_aten=pg_escape_string(utf8_decode($_POST['fecha_aten']));
	
	pg_query("UPDATE orden_atencion SET 
			oa_fecha_aten=$fecha_aten,
			oa_fecha_ingreso_hosp=$fecha_hosp,
			oa_carpeta_id=$carp_id,
			oa_centro_ruta='$centro_ruta',
			oa_doc_id=$doc_id,
			oa_codigo='$codigo',
			oa_ges=$ges,
			oa_tipo_aten=$tipo_atencion,
			oa_diag_cod='$diag_cod',
			oa_diagnostico='$diagnostico',
			oa_observacion='$oa_observacion',
			oa_prioridad=$oa_prioridad
		WHERE oa_id=$oa_id
	");

	exit('OK');

?>
