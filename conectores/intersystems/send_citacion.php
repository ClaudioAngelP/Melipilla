<?php 

	if(!isset($_GET['connect'])) {
	
		require_once('../../../sghprueba/config.php');
		require_once('../sigh.php');
	
	}
	
	require_once('config_intersystems.php');
	
	error_reporting(E_ALL);

	function fixdate($str) {
		
		$d=explode('/', $str);
		
		return ($d[2].'-'.$d[1].'-'.$d[0]);
		
	}

	if(isset($_GET['nomd_id'])) {

		$nomd_id=$_GET['nomd_id'];
		
		$nomd=cargar_registro("SELECT * FROM nomina_detalle WHERE nomd_id=$nomd_id");
	
	} else {

		$id_sidra=$_GET['id_sidra'];
		
		$nomd=cargar_registro("SELECT * FROM nomina_detalle WHERE id_sidra='$id_sidra'");
		
		$nomd_id=$nomd['nomd_id'];
	
	}

	$pac_id=$nomd['pac_id'];

	$pac=cargar_registro("SELECT * FROM pacientes 
	LEFT JOIN comunas USING (ciud_id)
	WHERE pac_id=$pac_id");
	
	if($pac['sex_id']==0) $sex_id='H';
	else if($pac['sex_id']==1) $sex_id='M';
	else $sex_id='I';
	
	if($pac['ciud_cod_nacional']*1<10000) {
		$pac['ciud_cod_nacional']='0'.$pac['ciud_cod_nacional'];
	}
	
	$cancelar=''; $nover='';
	
	if($nomd['nomd_diag_cod']=='NSP') {
		$estado='No se presenta';
		$estado_cod='N';
		$nover='<ReasonForNotSeenCode xsi:type="s01:String">08</ReasonForNotSeenCode>';
	} elseif($nomd['nomd_diag_cod']=='MA') {
		$estado='No Atendido';
		$estado_cod='N';
		$nover='<ReasonForNotSeenCode xsi:type="s01:String">15</ReasonForNotSeenCode>';
	} elseif($nomd['nomd_diag_cod']=='X') {
		$estado='Cancelado';
		$estado_cod='X';
		$cancela_cod=$nomd['nomd_codigo_cancela'];
		$cancelar='<ReasonForCancelCode xsi:type="s01:String">'.$cancela_cod.'</ReasonForCancelCode>';
	} elseif($nomd['nomd_diag_cod']=='') {
		$estado='Agendado';
		$estado_cod='P';		
	} else {
		$estado='Atendido';
		$estado_cod='A';		
	}
	
	$id_sidra=$nomd['id_sidra'];
	
	$xml='<?xml version="1.0" encoding="UTF-8" ?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:s="http://www.w3.org/2001/XMLSchema">
<SOAP-ENV:Body>
<SendEvent xmlns="InterSystems">
<pRequest xmlns:s01="InterSystems" xsi:type="s01:PatientApptStatusUpdEvent" MessageName="PatientApptStatusUpdEvent" SpecificationCode="SDVN" OriginatingSystemCode="SMGESF" EventTime="'.date('Y-m-d\Th:i:s').'" SendingFacilityCode="'.$HomeFacilityCode.'">
   <ControlID xsi:type="s01:String"></ControlID>
<CorrelatedControlID xsi:type="s01:String"></CorrelatedControlID>
<RecordedDateTime xsi:type="s01:TimeStamp">'.date('Y-m-d\Th:i:s').'</RecordedDateTime>
<AppointmentId xsi:type="s01:String">'.$id_sidra.'</AppointmentId>
<StatusCode xsi:type="s01:String">'.$estado_cod.'</StatusCode>
'.$cancelar.'
'.$nover.'
</pRequest>
</SendEvent>
</SOAP-ENV:Body></SOAP-ENV:Envelope>
       
       ';
			
	$xml=str_replace("\n",'',trim($xml));
	$xml=str_replace("\t",'',trim($xml));
			
	$xml=preg_replace('/>\s+</','><', $xml);
	
	$xml=utf8_encode($xml);

	$size=strlen($xml);

	//Content-Length: $size

	$headers=explode("\n","User-Agent: Mozilla/4.0 (compatible; Cache;)
Connection: Close
SOAPAction: InterSystems/sdvn/sdvnint/SMGES.BS.WSService.SendEvent
Content-Type: text/xml; charset=UTF-8");

		
	$ch = curl_init();

	// cURL Setup
	curl_setopt($ch, CURLOPT_URL, "http://10.8.163.80/sdvn/sdvnint/SMGES.BS.WSService.cls?soap_method=SendEvent");
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

	print('<pre>');

	// grab URL and pass it to the browser
	$tmp=curl_exec($ch);
	
	print('</pre>');

	// close cURL resource, and free up system resources
	curl_close($ch); 	

?>
