<?php 

	require_once('../../conectar_db.php');

	$centro_ruta=pg_escape_string(utf8_decode($_POST['centro_ruta']));
	$esp_id=$_POST['esp_id']*1;
	$folio=($_POST['nro_folio'])*1;
	$fecha=pg_escape_string(utf8_decode($_POST['fecha']));
	
	$pac_id=$_POST['paciente_id']*1;
	
	if(isset($_POST['ca_id']))
		$ca_id=$_POST['ca_id']*1;
	else 
		$ca_id=0;	

	$doc_id=$_POST['doc_id']*1;
	$codigo=pg_escape_string(utf8_decode($_POST['cod_presta']));
	$hipotesis=pg_escape_string(utf8_decode($_POST['oa_hipotesis']));
	$fecha2=($_POST['fecha_aten'])*1;
	
	$fecha_tmp=explode('/',$fecha);
	$fecha3=mktime(0,0,0,($fecha_tmp[1]*1)+$fecha2,$fecha_tmp[0]*1,$fecha_tmp[2]*1);
	$fecha3=date('d/m/Y', $fecha3);
	
	pg_query("INSERT INTO orden_atencion VALUES (
		DEFAULT, $folio, '$fecha', $pac_id, 
		$sgh_inst_id, $sgh_inst_id, $esp_id, -1, -1, '$hipotesis', 
		'$codigo', '', -1, $doc_id, '$fecha3', 0, $ca_id, 
		".$_SESSION['sgh_usuario_id'].", 0); 
	");

	exit('OK');

?>