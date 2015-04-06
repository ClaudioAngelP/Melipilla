<?php 

	require_once('../../conectar_db.php');
	
	
 function pac_fonasa($rutdv) {
        
		
		$chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE pac_rut = '$rutdv' AND cert_fecha::date=CURRENT_DATE");
		
		if($chq) return;
		
        list($rut, $dv)=explode('-',$rutdv);
    
	$xml='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cer="http://certificadorprevisional.fonasa.gov.cl.ws/">
   <soapenv:Header/>
   <soapenv:Body>
      <cer:getCertificadoPrevisional>
         <cer:query>
            <cer:queryTO>
               <cer:tipoEmisor>10</cer:tipoEmisor>
               <cer:tipoUsuario>1</cer:tipoUsuario>
            </cer:queryTO>
            <cer:entidad>61608000</cer:entidad>
            <cer:claveEntidad>6160</cer:claveEntidad>
            <cer:rutBeneficiario>'.$rut.'</cer:rutBeneficiario>
            <!--Optional:-->
            <cer:dgvBeneficiario>'.$dv.'</cer:dgvBeneficiario>
            <cer:canal>10</cer:canal>
         </cer:query>
      </cer:getCertificadoPrevisional>
   </soapenv:Body>
</soapenv:Envelope>';

		// print($xml);

		$headers=explode("\n","User-Agent: Mozilla/4.0 (compatible; Cache;)
Connection: Keep-Alive
Accept-Encoding: gzip,deflate
Content-Type: text/xml;charset=UTF-8
Content-Length: ".strlen($xml)."
SOAPAction: \"http://certificadorprevisional.fonasa.gov.cl.ws/getCertificadoPrevisional\"
User-Agent: Apache-HttpClient/4.1.1 (java 1.5)");

            
        $ch = curl_init();

        // cURL Setup
        curl_setopt($ch, CURLOPT_URL, "http://ws.fonasa.cl:8080/Certificados/Previsional");
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);

        ob_start();
        curl_exec($ch);
        $data=ob_get_contents();
        ob_end_clean();
        
        preg_match('/<folio>(.+)<\/folio>/',$data,$folio);

        preg_match('/<tramo>(.+)<\/tramo>/',$data, $tramo);

        preg_match('/<coddesc>(.+)<\/coddesc>/',$data, $isapre);
        
        preg_match('/<desIsapre>(.+)<\/desIsapre>/',$data, $detalle_isapre);
        
        //print_r($data);
        
        switch ($tramo[1]) {
			case "A":
				$prevision = 1;
				$tramo_fonasa="A";
				break;
			case "B":
				$prevision = 2;
				$tramo_fonasa="B";
				break;
			case "C":
				$prevision = 3;
				$tramo_fonasa="C";
				break;
			case "D":
				$prevision = 4;
				$tramo_fonasa="D";
				break;
			default:
				$tramo_fonasa="";
		}
		
		if(strstr($isapre[1], "isapre")!=false)
			$prevision=5;
			
		if(!isset($prevision))
			$prevision=6;
			
		$cert_folio = $folio[1];
		
		$chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE pac_rut = '$rutdv' AND cert_fecha::date=CURRENT_DATE");
		
		//if(!$chq)
        pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, '$rutdv', '$data', '$cert_folio', $prevision);");
        
        //print("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, '$rutdv', '$data', '$cert_folio');<br>");
        
		pg_query("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa' WHERE pac_rut='$rutdv'");
		//print("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa' WHERE pac_rut='$rutdv';");
    }	
	
	
	
	
	
	$pac_rut=$_POST['pac_rut'];

	pac_fonasa($pac_rut);
	
	$r=cargar_registro("SELECT *, (SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE pac_rut='$pac_rut';");
	
	exit(json_encode($r));

?>