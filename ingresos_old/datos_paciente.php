<?php 

	require_once('../conectar_db.php');
	
	function xmldate($str) {
		$tmp=explode('-',$str);
		return $tmp[2].'/'.$tmp[1].'/'.$tmp[0];
	}

	
	function pac_fonasa($rutdv) {
		
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

		
		preg_match('/<codcybl>(.+)<\/codcybl>/',$data,$tmp);
					
		$prev_cod=($tmp[1]);

		preg_match('/<coddesc>(.+)<\/coddesc>/',$data,$tmp);
					
		$prev_desc=utf8_decode($tmp[1]);

		preg_match('/<desIsapre>(.+)<\/desIsapre>/',$data,$tmp);
					
		$prev_isapre=utf8_decode($tmp[1]);

		preg_match('/<direccion>(.+)<\/direccion>/',$data,$tmp);
					
		$direccion=utf8_decode(trim($tmp[1]));

		preg_match('/<desRegion>(.+)<\/desRegion>/',$data,$tmp);
					
		$region=utf8_decode($tmp[1]);

		preg_match('/<desComuna>(.+)<\/desComuna>/',$data,$tmp);
					
		$comuna=utf8_decode(trim($tmp[1]));

		preg_match('/<cdgComuna>(.+)<\/cdgComuna>/',$data,$tmp);
					
		$cod_comuna=($tmp[1]*1);
		
		preg_match('/<telefono>(.+)<\/telefono>/',$data,$tmp);
					
		$telefono=($tmp[1]);
		
		preg_match('/<beneficiarioTO>(.+)<\/beneficiarioTO>/',$data,$tmp);
		
		$tmpdata=($tmp[1]);

		preg_match('/<nombres>(.+)<\/nombres>/',$tmpdata,$tmp);
					
		$nombres=utf8_decode(trim($tmp[1]));

		preg_match('/<apell1>(.+)<\/apell1>/',$tmpdata,$tmp);
					
		$paterno=utf8_decode(trim($tmp[1]));

		preg_match('/<apell2>(.+)<\/apell2>/',$tmpdata,$tmp);
					
		$materno=utf8_decode(trim($tmp[1]));

		preg_match('/<genero>(.+)<\/genero>/',$tmpdata,$tmp);
					
		$sexo=trim($tmp[1]);

		preg_match('/<fechaNacimiento>(.+)<\/fechaNacimiento>/',$tmpdata,$tmp);
					
		$fc_nac=xmldate($tmp[1]);

		preg_match('/<fechaFallecimiento>(.+)<\/fechaFallecimiento>/',$tmpdata,$tmp);
					
		$fc_def=xmldate($tmp[1]);
		
		if($fc_def=='00/00/0000') $fc_def='';


		preg_match('/<afiliadoTO>(.+)<\/afiliadoTO>/',$data,$tmp);
		
		$tmpdata=($tmp[1]);

		preg_match('/<tramo>(.+)<\/tramo>/',$tmpdata,$tmp);
					
		$tramo=utf8_decode(trim($tmp[1]));



		if($prev_isapre!='')
			return $prev_desc.' '.$prev_isapre;
		else if($tramo!='')
			return 'FONASA '.$tramo;
		else
			return $prev_desc.' '.$prev_isapre.' ('.$prev_cod.')';

	}
	
	
	
	$pac_id=$_POST['pac_id']*1;
	
	$p=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id", true);
	
	$pac_rut=$p['pac_rut'];
	
	$prev_desc=pac_fonasa($pac_rut);
	
	$ges=cargar_registros_obj("SELECT * FROM monitoreo_ges WHERE mon_rut='$pac_rut'", true);
	
	print(json_encode(array($prev_desc, $ges)));
	
?>
