<?php
    require_once('../../conectar_db.php');
    function pac_fonasa($rutdv)
    {
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

	print($xml);

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
        
        preg_match('/<nombres>(.+)<\/nombres>/', $data, $pac_nombres);
        preg_match('/<apell1>(.+)<\/apell1>/', $data, $pac_appat);
        preg_match('/<apell2>(.+)<\/apell2>/', $data, $pac_apmat);
        
        preg_match('/<fechaNacimiento>(.+)<\/fechaNacimiento>/', $data, $pac_fc_nac);
        preg_match('/<genero>(.+)<\/genero>/', $data, $pac_sex);
        preg_match('/<direccion>(.+)<\/direccion>/', $data, $pac_direccion);
        preg_match('/<cdgComuna>(.+)<\/cdgComuna>/', $data, $pac_ciud_id);        
        preg_match('/<telefono>(.+)<\/telefono>/', $data, $pac_telefono);
        preg_match('/<tramo>(.+)<\/tramo>/',$data, $pac_tramo);
        
        print_r($data);
        die();
        //print_r($pac_nombres);
	//	die();
        switch ($pac_tramo[1]) {
			case "A":
                        $prevision = 12;
                        $tramo_fonasa="FONASA GPO-A";
                        break;

                    case "B":
                        $prevision = 10;
                        $tramo_fonasa="FONASA GPO-B";
                        break;

                    case "C":
                        $prevision = 11;
                        $tramo_fonasa="FONASA GPO-C";
                        break;

                    case "D":
                        $prevision = 15;
                        $tramo_fonasa="FONASA GPO-D";
                        break;
			default:
				$tramo_fonasa="";
		}
		
		if(strstr($isapre[1], "isapre")!=false)
			$prevision=5;
			
		if(!isset($prevision))
			$prevision=6;
			
		if(trim(substr($pac_sex[1],0,1))=="M")
			$pac_sex[1] = 0;
		else
			$pac_sex[1] = 1;
		
		$ciud_id = cargar_registro("SELECT ciud_id FROM comunas WHERE ciud_cod_nacional=$pac_ciud_id[1]", true);
		
		$paciente_rut = $rut."-".$dv;
		$paciente_nom = explode("<",$pac_nombres[1]);
		$paciente_nombre = utf8_decode($paciente_nom[0]);
		$paciente_app = explode("<",$pac_appat[1]);
		$paciente_appat = utf8_decode($paciente_app[0]);
		$paciente_apm = explode("<",$pac_apmat[1]);
		$paciente_apmat = utf8_decode($paciente_apm[0]);
		$paciente_fc_nac = $pac_fc_nac[1];
		$paciente_sex_id = $pac_sex[1];
		$paciente_prec_id = $prevision;
		$paciente_sector_nombre = 'null';
		$paciente_getn_id = '-1';
		$paciente_sang_id = '-1';
		$paciente_direccion = trim(utf8_decode($pac_direccion[1]));
		$paciente_ciud_id = $ciud_id['ciud_id'];
		$paciente_nacion_id = '-1';
		$paciente_estado_civil = 'null';
		$paciente_fono = $pac_telefono[1];
		
		if ($paciente_nombre=="")
                {
                
                }
                else
                {
                    $chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE pac_rut = '$rutdv' AND cert_fecha::date=CURRENT_DATE");
                    $agregar_paciente = "INSERT INTO pacientes VALUES
                    (DEFAULT,
                    '$paciente_rut',
                    '$paciente_nombre',
                    '$paciente_appat',
                    '$paciente_apmat',
                    '$paciente_fc_nac',
                    $paciente_sex_id,
                    $prevision,
                    '$paciente_sector_nombre',
                    $paciente_getn_id,
                    $paciente_sang_id,
                    '$paciente_direccion',
                    $paciente_ciud_id,
                    $paciente_nacion_id,
                    $paciente_estado_civil,
                    '$paciente_fono',
                    null,
                    null,
                    '$tramo_fonasa',
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null		 
                    );";
                    pg_query($agregar_paciente);
                }		
		//pg_query($agregar_paciente);
    }

    if(isset($_POST['pac_rut']))
    {
        if($_POST['pac_rut']!=="")
        {
            $chk = cargar_registro ("SELECT pac_id FROM pacientes WHERE pac_rut='".$_POST['pac_rut']."';");
            if(!$chk)
            pac_fonasa($_POST['pac_rut']);
        }
    }
    else
    {
        if(isset($_GET['pac_rut']))
        {
            if($_GET['pac_rut']!=="")
            {
                $chk = cargar_registro ("SELECT pac_id FROM pacientes WHERE pac_rut='".$_GET['pac_rut']."';");
                if(!$chk)
                {
                    pac_fonasa($_GET['pac_rut']);
                }
		  else
                {
                    pac_fonasa($_GET['pac_rut']);

                }
            }
            
        }
   }
     
?>
