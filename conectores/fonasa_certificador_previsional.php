<?php 

	require_once('../../config.php');
	require_once('../sigh.php');
	
	error_reporting(E_ALL);

	// RUT de la Institucion autorizada por FONASA para uso del webservice...
	$inst_rut='61606608';

	// RUT del Paciente...
	$rut=$_GET['rut'];
	
	$trut=explode('-',$rut);
	
	$rut=$trut[0]*1;
	$dv=trim(strtoupper($trut[1]));
	
	// URL de acceso a WEBSERVICE FONASA
	$url="http://200.51.172.210/trade_service/web_services/Certificado.asp?ID=".$inst_rut."&RUT=".$rut."&DGV=".$dv;
	
	$data=file_get_contents($url);
	
	$data2=pg_escape_string($data);
	pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, '".$rut."-".$dv."', '$data2');");

	$xml=simplexml_load_string($data);
	
	// Preparacion de los datos para actualizar datos...
	
	$pat=pg_escape_string(trim(utf8_decode($xml->APELL1)));
	$mat=pg_escape_string(trim(utf8_decode($xml->APELL2)));
	$nom=pg_escape_string(trim(utf8_decode($xml->NOMBRES)));
	$sex=$xml->SEX;
	$fnac=$xml->FEC_NAC;
	
	$cod=$xml->COD_CYBL;
	$cert=pg_escape_string($xml->COD_DESC);
	
	$isapre='';

	if(isset($xml->TRAMO))
		$tramo=$xml->TRAMO;
	else
		$tramo='';
	
	switch($sex) {
		case 'M': $sex=1; break;
		case 'F': $sex=2; break;
	}
	
	$ftmp=explode('-',$fnac);
	
	$fnac=$ftmp[2].'/'.$ftmp[1].'/'.$ftmp[0];
	
	// QUERY FINAL
	
	/*
	pg_query("UPDATE pacientes SET 
	pac_appat='$pat', pac_apmat='$mat', pac_nombres='$nom',
	sex_id=$sex, pac_fc_nac='$fnac', pac_tramo='$tramo'
	WHERE pac_rut='".$rut."-".$dv."'");
	
	pg_query("INSERT INTO pacientes_fonasa VALUES (
		DEFAULT, now(), '".$rut."-".$dv."',
		'$pat', '$mat', '$nom', '$fnac', 
		'$cod', '$cert', '$tramo', '$isapre'
	);");

	*/
	
?>
