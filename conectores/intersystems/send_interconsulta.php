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

	function fixdatetime($str) {
		
		$dt=explode(' ',$str);
		
		$d=explode('/', $dt[0]);
		
		return ($d[2].'-'.$d[1].'-'.$d[0].'T'.$dt[1]);
		
	}

	$inter_id=$_GET['inter_id'];
	
	$inter=cargar_registro("SELECT *,
	i1.inst_codigo_ifl AS inst_codigo_ifl1,
	i2.inst_codigo_ifl AS inst_codigo_ifl2,
	e1.esp_codigo_ifl_usuario AS esp_codigo_ifl1,
	inter_fecha_salida::date AS fecha_salida	
	FROM interconsulta 
	JOIN instituciones AS i1 ON inter_inst_id1=i1.inst_id 
	JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id
	JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id
	WHERE inter_id=$inter_id");

	$pac_id=$inter['inter_pac_id'];

	$pac=cargar_registro("SELECT *,lpad((reg_id+1)::text,2,'0') AS reg_cod FROM pacientes 
	LEFT JOIN comunas USING (ciud_id)
	LEFT JOIN provincias USING (prov_id)
	LEFT JOIN regiones USING (reg_id)
	WHERE pac_id=$pac_id");
	
	if($pac['sex_id']*1==0) $sex_id='1';
	else if($pac['sex_id']*1==1) $sex_id='2';
	else $sex_id='3';
	
	if($pac['ciud_cod_nacional']*1<10000) {
		$pac['ciud_cod_nacional']='0'.$pac['ciud_cod_nacional'];
	}

	if($pac['prev_id']*1>0 AND $pac['prev_id']*1<5) {
		
		switch($pac['prev_id']*1) {
			case 1: $tramo='A'; break;
			case 2: $tramo='B'; break;
			case 3: $tramo='C'; break;
			case 4: $tramo='D'; break;
		}
		
		$prevision=' 
					<Insurances>
                        <Insurance xmlns:s03="InterSystems" xsi:type="s03:Insurance">
                         <PayorCode xsi:type="s03:String">F</PayorCode>
                         <PayorNationalId xsi:type="s03:NationalId">
                         </PayorNationalId>
                         <PayorDesc xsi:type="s03:String">Fonasa</PayorDesc>
                         <PlanCode xsi:type="s03:String">'.$tramo.'</PlanCode>
                         <PlanDesc xsi:type="s03:String">Grupo '.$tramo.'</PlanDesc>
                        </Insurance>
                     </Insurances>
					';
	} else {
		
		$prevision='';
		
	}

	if($pac['id_sidra']!='') {
		$regnum='<RegistrationNumber xsi:type="s02:String">'.$pac['id_sidra'].'</RegistrationNumber>';
	} else {
		$regnum='';		
	}

	
	if($pac['pac_ficha']!='') {
		$medical_record='
		<MedicalRecords>
		<MedicalRecord xmlns:s03="InterSystems" xsi:type="s03:MedicalRecord">
			<MRN xsi:type="s03:String">'.$pac['pac_ficha'].'</MRN>
			<MRTypeCode xsi:type="s03:String">'.$MRTypeCode.'</MRTypeCode>
			<HomeFacilityCode xsi:type="s03:String">'.$HomeFacilityCode.'</HomeFacilityCode>
		</MedicalRecord>
		</MedicalRecords>
		';
	} else {
		$medical_record='';
	}

	$estadocivil=$pac['estciv_id']*1;
		
	$estciv=0;
		
	switch($estadocivil) {
			case 1: $estciv=1; break; // SOLTERO
			case 2: $estciv=2; break; // CASADO
			case 5: $estciv=3; break; // VIUDO
			case 3: $estciv=4; break; // SEPARADO
			case 6: $estciv=5; break; // CONVIVE
			case 4: $estciv=6; break; // DIVORCIADO
			case 0: $estciv=7; break; // INDETERMINADO
	}

	
	$id_sidra=$inter['id_sidra'];
	$codigo_presta='0101112';
	$codigo_espec=$inter['esp_codigo_ifl1'];

	$prioridad=$inter['inter_prioridad'];
		
	$prior='005';
		
	switch($prioridad) {
		case 1: $prior='001'; break;
		case 2: $prior='002'; break;
		case 3: $prior='003'; break;
		case 6: $prior='004'; break;
		default: $prior='005'; break;
	}

	
	if($inter['inter_motivo_salida']!=0 AND $inter['inter_fecha_salida']!='') {
		
		if($inter['inter_motivo_salida']*1<10) $inter['inter_motivo_salida']='0'.$inter['inter_motivo_salida'];
		
		$salida='
			<ReferralRemovalDate xsi:type="s02:Date">'.fixdatetime($inter['inter_fecha_salida']).'</ReferralRemovalDate>
			<ReferralRemovalReasonCode xsi:type="s02:String">'.$inter['inter_motivo_salida'].'</ReferralRemovalReasonCode>
		';
		
	} else {
		
		$salida='';
		
	}
	
	// <ControlID xsi:type="s01:String">TRAKCARE:44929</ControlID>
	
	// <RequestedProcedureCode xsi:type="s04:String">'.$codigo_presta.'</RequestedProcedureCode>
       
	
	$xml='<?xml version="1.0" encoding="UTF-8" ?>
	<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:s="http://www.w3.org/2001/XMLSchema">
	<SOAP-ENV:Body>
	<SendEvent xmlns="InterSystems">
	<pRequest xmlns:s01="InterSystems" xsi:type="s01:WLEntryEvent" MessageName="WLEntryEvent" SpecificationCode="SDVN" OriginatingSystemCode="SMGESF" EventTime="'.date('Y-m-d\Th:i:s').'" SendingFacilityCode="'.$HomeFacilityCode.'">
	<CorrelatedControlID xsi:type="s01:String">
	</CorrelatedControlID>
    <OperatorCode xsi:type="s01:String">
    </OperatorCode>
    <RecordedDateTime xsi:type="s01:TimeStamp">'.date('Y-m-d\Th:i:s').'</RecordedDateTime>
    <WLEntryCode xsi:type="s01:String">'.$id_sidra.'</WLEntryCode>
    <RNLERegistrationId xsi:type="s01:String">
    </RNLERegistrationId>

       <Patient xmlns:s02="InterSystems" xsi:type="s02:Patient">
		  <NationalId xsi:type="s02:NationalId">'.($pac['pac_rut']).'</NationalId>
		  '.$regnum.'
		  <PRAIS xsi:type="s02:Boolean">'.($pac['pac_prais']=='t'?'true':'false').'</PRAIS>
		  <PatientTypeCode xsi:type="s02:String">ID</PatientTypeCode>
		  <GivenName xsi:type="s02:String">'.$pac['pac_nombres'].'</GivenName>
		  <FamilyName xsi:type="s02:String">'.$pac['pac_appat'].'</FamilyName>
		  <SecondaryName xsi:type="s02:String">'.$pac['pac_apmat'].'</SecondaryName>
		  <SexCode xsi:type="s02:String">'.($sex_id).'</SexCode>
		  <NationalityCode xsi:type="s02:String">1</NationalityCode>
		  <DateOfBirth xsi:type="s02:Date">'.fixdate($pac['pac_fc_nac']).'</DateOfBirth>
		  <HomeAddressStreet xsi:type="s02:String">'.$pac['pac_direccion'].'</HomeAddressStreet>
		  <HomeAddressCityCode xsi:type="s02:String">'.$pac['ciud_cod_nacional'].'</HomeAddressCityCode>
		  <HomeAddressCityDesc xsi:type="s02:String">'.($pac['ciud_desc']).'</HomeAddressCityDesc>
		  <HomePhone xsi:type="s02:String">'.$pac['pac_fono'].'</HomePhone>
		  <MobilePhone xsi:type="s02:String">'.$pac['pac_celular'].'</MobilePhone>
		  <EMail xsi:type="s02:String">'.$pac['pac_mail'].'</EMail>
		  <MaritalStatusCode xsi:type="s02:String">'.$estciv.'</MaritalStatusCode> 
       </Patient>
	   '.$prevision.'
	   '.$medical_record.'
      <WLEntry xmlns:s04="InterSystems" xsi:type="s04:WLEntry">
       
       <DiagnosisCode xsi:type="s04:String">'.$inter['inter_diag_cod'].'</DiagnosisCode>
       <DiagnosisCodingSystem xsi:type="s04:String">ICD10</DiagnosisCodingSystem>
       <Remarks xsi:type="s04:String">'.($inter['inter_fundamentos']).'</Remarks>
       <DiagnosisRemarks xsi:type="s04:String">'.($inter['inter_examenes']).'</DiagnosisRemarks>
       
       <ReferralReasonCode xsi:type="s04:String">1</ReferralReasonCode>
       
       <ReferralDate xsi:type="s04:TimeStamp">'.fixdatetime($inter['inter_fecha_ingreso']).'</ReferralDate>
       <GeneralData xsi:type="s04:String">'.$inter['inter_folio'].'|</GeneralData>
       <SpecialtyCode xsi:type="s04:String">'.$codigo_espec.'</SpecialtyCode>
       <RequestedSpecialtyCode xsi:type="s04:String">'.$codigo_espec.'</RequestedSpecialtyCode>
       <DestinationFacilityCode xsi:type="s04:String">'.$inter['inst_codigo_ifl2'].'</DestinationFacilityCode>
       <OriginatingFacilityCode xsi:type="s04:String">'.$inter['inst_codigo_ifl1'].'</OriginatingFacilityCode>
       
       <EpisodePriorityCode xsi:type="s05:String">'.$prior.'</EpisodePriorityCode>
                                                  
       <WaitingListTypeCode xsi:type="s04:String">1</WaitingListTypeCode>
       
       '.$salida.'
      
      </WLEntry>
  
  </pRequest>
  </SendEvent>
  </SOAP-ENV:Body>
  </SOAP-ENV:Envelope>';
			
	$xml=str_replace("\n",'',trim($xml));
	$xml=str_replace("\t",'',trim($xml));
			
	$xml=preg_replace('/>\s+</','><', $xml);
	
	$xml=utf8_encode($xml);

	$size=strlen($xml);

	//Content-Length: $size

	print("<pre>".htmlentities($xml)."</pre>");
	
	$headers=explode("\n","User-Agent: Mozilla/4.0 (compatible; Cache;)
Connection: Close
SOAPAction: InterSystems/sdvn/sdvnint/SMGES.BS.WSService.SendEvent
Content-Type: text/xml; charset=UTF-8");

	
		
	$ch = curl_init();

	// cURL Setup
	curl_setopt($ch, CURLOPT_URL, "http://10.8.163.80/sdvn/sdvnint/SMGES.BS.WSService.cls?soap_method=SendEvent");
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

	// grab URL and pass it to the browser
	$tmp=curl_exec($ch);
	
	print("<pre>".htmlentities($tmp)."</pre>");
	
	$xmlString = str_replace("<SOAP-ENV:Envelope xmlns:SOAP-ENV=''http://schemas.xmlsoap.org/soap/envelope/'' xmlns:xsi=''http://www.w3.org/2001/XMLSchema-instance'' xmlns:s=''http://www.w3.org/2001/XMLSchema''>",
									'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:s="http://www.w3.org/2001/XMLSchema">',
									$tmp);


	$xmlString = str_replace('SOAP-ENV:', 'SOAP-ENV',  
									$xmlString  );

	$xmlString = str_replace('xsi:', '', 
									$xmlString	);

	$xmlString = str_replace('xmlns:', '', 
									$xmlString	);

	$xml=@simplexml_load_string($xmlString);
				
	if(isset($xml->{'SOAP-ENVBody'}[0]->SendEventResponse[0]->pResponse[0]->WLEntryCode[0]) AND 
		trim($xml->{'SOAP-ENVBody'}[0]->SendEventResponse[0]->pResponse[0]->WLEntryCode[0])!='') {
		
		$id_sidra=$xml->{'SOAP-ENVBody'}[0]->SendEventResponse[0]->pResponse[0]->WLEntryCode[0];

		pg_query("UPDATE interconsulta SET id_sidra='$id_sidra' WHERE inter_id=$inter_id;");
	
	}

	// close cURL resource, and free up system resources
	curl_close($ch); 	

?>
