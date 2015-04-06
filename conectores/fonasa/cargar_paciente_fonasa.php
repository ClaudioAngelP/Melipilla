<?php
    function pac_fonasa($rutdv,$pac_ficha)
    {
        $actualizar=false;
        $chk = cargar_registro("SELECT pac_id FROM pacientes WHERE upper(pac_rut)=upper('".$rutdv."');");
        if($chk)
        {
            $encontrado=true;
        }
        else
        {
            $encontrado=false;
        }
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
        //print_r($data);
        //die();
        ob_end_clean();
        preg_match('/<errorM>(.+)<\/errorM>/', $data, $errorm);
        preg_match('/<codcybl>(.+)<\/codcybl>/', $data, $codcybl);
        preg_match('/<nombres>(.+)<\/nombres>/', $data, $pac_nombres);
        preg_match('/<apell1>(.+)<\/apell1>/', $data, $pac_appat);
        preg_match('/<apell2>(.+)<\/apell2>/', $data, $pac_apmat);
        preg_match('/<fechaNacimiento>(.+)<\/fechaNacimiento>/', $data, $pac_fc_nac);
        preg_match('/<genero>(.+)<\/genero>/', $data, $pac_sex);
        preg_match('/<direccion>(.+)<\/direccion>/', $data, $pac_direccion);
        preg_match('/<cdgComuna>(.+)<\/cdgComuna>/', $data, $pac_ciud_id);        
        preg_match('/<telefono>(.+)<\/telefono>/', $data, $pac_telefono);
        preg_match('/<tramo>(.+)<\/tramo>/',$data, $pac_tramo);
        preg_match('/<coddesc>(.+)<\/coddesc>/',$data, $isapre);
        //preg_match('/<folio>(.+)<\/folio>/',$data,$folio');
        //print("<br>");
        //print("<br>");
        $error=false;
        if(count($errorm)>0)
        {
            $errorm = explode('errorM',$errorm[0]);
            $errorm = explode('>',$errorm[1]);
            $errorm = explode('<',$errorm[1]);
            if(trim(strtoupper($errorm[0]))=="RUT NO REGISTRADO")
            {
                //print("ERROR CONTROLADO");			
                //print("RUT ".$rutdv. " NO REGISTRADO");
                //echo "error|1";
                //$tipo_error="RUT NO REGISTRADO";
                //echo "error|RUT NO REGISTRADO EN FONASA";
                $error=false;
            }
            else
            {
                if(trim(strtoupper($errorm[0]))==strtoupper("RUT incorrecto"))
                {
                    echo "error|RUT INCORRECTO";
                    $error=true;
                }
                else
                {
                    //print("RUT ".$rutdv. " PRESENTA OTRO TIPO DE ERROR");
                    //echo "error|2";
                    //$tipo_error="ERROR DESCONOCIDO";
                    //echo "error|ERROR DESCONOCIDO";
                    $error=true;
                }
            }
        }
        else 
        {
            if(trim($codcybl[1])=="01907")
            {
                //print("RUT ".$rutdv. "BLOQUEADOS S/COTIZ. AL DIA");
                //echo "bloqueo|3";
                $error=false;
            }
            if(trim($codcybl[1])=="01903")
            {
                //print("RUT ".$rutdv. "BLOQUEADOS S/COTIZ. AL DIA - PRAIS");
                //echo "bloqueo|4";
                $error=false;
            }
            if(trim($codcybl[1])=="01901")
            {
                //print("RUT ".$rutdv. "BLOQUEADO POR ISAPRE");
                //echo "bloqueo|5";
                $error=false;
            }
        }
        if(!$error)
        {
            $tramo_fonasa="";
            if(count($pac_tramo)>0)
            {
                //$prevision="";
                switch ($pac_tramo[1])
                {
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
            }
            $prais=false;
            if(count($isapre)>0)
            {
                if(strstr($isapre[1], "ISAPRE")!=false)
                    $prevision=5;
                if(strstr($isapre[1], "PRAIS")!=false)
                    $prais=true;
            }
            if(!isset($prevision))
                $prevision=6;

            if(trim(substr($pac_sex[1],0,1))=="M")
                    $pac_sex[1] = 0;
            else
                $pac_sex[1] = 1;

            if(count($pac_ciud_id)>0)
            {
                $ciud_id = cargar_registro("SELECT ciud_id FROM comunas WHERE ciud_cod_nacional=".$pac_ciud_id[1]."", true);
                if($ciud_id)
                {
                    $ciud_id = $ciud_id['ciud_id'];
                }
                else
                {
                    $ciud_id =-1;
                }
            }
            else
            {
                $ciud_id =-1;
            }
	    $originales = '������������������������������������������������������������??';
            $modificadas = 'aaaaaaaceeeeiiiidoooooouuuuybsaaaaaaaceeeeiiiidoooooouuuyybyRr';
            
            $paciente_rut = $rut."-".$dv;
            $paciente_nom = explode("<",$pac_nombres[1]);
            $paciente_nombre = html_entity_decode($paciente_nom[0]);
            //$paciente_nombre = strtr(utf8_decode($paciente_nom[0]), utf8_decode($originales), $modificadas);
            $paciente_app = explode("<",$pac_appat[1]);
            $paciente_appat = html_entity_decode($paciente_app[0]);
            //$paciente_appat = strtr(utf8_decode($paciente_app[0]), utf8_decode($originales), $modificadas);		
            $paciente_apm = explode("<",$pac_apmat[1]);
            $paciente_apmat = html_entity_decode($paciente_apm[0]);
            //$paciente_apmat = strtr(utf8_decode($paciente_apm[0]), utf8_decode($originales), $modificadas);
            $paciente_fc_nac = $pac_fc_nac[1];
            $paciente_sex_id = $pac_sex[1];
            $paciente_prec_id = $prevision;
            $paciente_sector_nombre = 'null';
            $paciente_getn_id = '-1';
            $paciente_sang_id = '-1';
            //$paciente_direccion = trim(utf8_decode($pac_direccion[1]));
            $paciente_direccion = trim(html_entity_decode($pac_direccion[1]));
            $paciente_ciud_id = $ciud_id;
            $paciente_nacion_id = '-1';
            $paciente_estado_civil = 'null';
            if(count($pac_telefono)>0)
            {
                $paciente_fono = $pac_telefono[1];
            }
            else
            {
                $paciente_fono = "0";
            }
            if(!$paciente_nombre=="")
            {
                if($encontrado)
                {
                    if($actualizar)
                    {
                        
                    }
                }
                else
                {
		    //if(count($folio)>0){
		    //	$cert_folio=$folio[1];
		    //	pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('".$rut."-".$dv."'), '$data','$cert_folio',$prevision))");
		    
		    
		    //}
		    //else {
		    	pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('".$rut."-".$dv."'), '$data','',$prevision);");
		    //}

                    $agregar_paciente = "INSERT INTO pacientes VALUES
                    (DEFAULT,upper('$paciente_rut'),
                    trim('$paciente_nombre'),
                    trim('$paciente_appat'),
                    trim('$paciente_apmat'),
                    '$paciente_fc_nac',
                    $paciente_sex_id,
                    $prevision,
                    $paciente_sector_nombre,
                    $paciente_getn_id,
                    $paciente_sang_id,
                    trim('$paciente_direccion'),
                    $paciente_ciud_id,
                    $paciente_nacion_id,
                    $paciente_estado_civil,
                    '$paciente_fono',
                    null,
                    null,
                    '$tramo_fonasa',
                    null,
                    '$pac_ficha',
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null
                    );";
                    pg_query($agregar_paciente);
                    if($prais)
                    	pg_query("UPDATE pacientes SET pac_prais=true WHERE upper(pac_rut)=upper('".$rutdv."')");
                    else
                    	pg_query("UPDATE pacientes SET pac_prais=false WHERE upper(pac_rut)=upper('".$rutdv."')");
                    /*
                    $chk = cargar_registro("SELECT pac_id FROM pacientes WHERE pac_rut='".$rutdv."';");
                    if($chk)
                    {
                        return $chk['pac_id'];	
                    }
                    */           
                }
            }
        }
        
    }
    function pac_fonasa_2($rutdv,$pac_ficha,$nombres,$paterno,$materno,$fc_nac,$sexo,$direccion,$comuna,$telefono)
    {
        $actualizar=false;
        $chk = cargar_registro("SELECT pac_id FROM pacientes WHERE upper(pac_rut)=upper('".$rutdv."');");
        if($chk)
        {
            $encontrado=true;
        }
        else
        {
            $encontrado=false;
        }
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
    preg_match('/<errorM>(.+)<\/errorM>/', $data, $errorm);
    preg_match('/<codcybl>(.+)<\/codcybl>/', $data, $codcybl);
    preg_match('/<nombres>(.+)<\/nombres>/', $data, $pac_nombres);
    preg_match('/<apell1>(.+)<\/apell1>/', $data, $pac_appat);
    preg_match('/<apell2>(.+)<\/apell2>/', $data, $pac_apmat);
    preg_match('/<fechaNacimiento>(.+)<\/fechaNacimiento>/', $data, $pac_fc_nac);
    preg_match('/<genero>(.+)<\/genero>/', $data, $pac_sex);
    preg_match('/<direccion>(.+)<\/direccion>/', $data, $pac_direccion);
    preg_match('/<cdgComuna>(.+)<\/cdgComuna>/', $data, $pac_ciud_id);        
    preg_match('/<telefono>(.+)<\/telefono>/', $data, $pac_telefono);
    preg_match('/<tramo>(.+)<\/tramo>/',$data, $pac_tramo);
    //print($data);
    //print("<br>");
    //print("<br>");
    $error=false;	
    if(count($errorm)>0)
    {
        $errorm = explode('errorM',$errorm[0]);
	$errorm = explode('>',$errorm[1]);
	$errorm = explode('<',$errorm[1]);
	if(trim($errorm[0])=="RUT NO REGISTRADO")
	{
            pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('$rutdv'), '$data');");
            pg_query("
            INSERT INTO pacientes VALUES
            (
                DEFAULT,
                upper('$rutdv'),
                '$nombres',
                '$paterno',
                '$materno',
                '$fc_nac',
                $sexo,
                -1,
                '',
                -1,
                -1,
                '$direccion',
                '$comuna',
                0,
                -1,
                '$telefono',
                null,
                null,
                null,
                null,
                '$pac_ficha'
                );
            ");	
            //print("ERROR CONTROLADO");
            //print("**--**DATOS:".$rutdv."|".$pac_ficha."|".$nombres."|".$paterno."|".$materno."|".$fc_nac."|".$sexo."|".$direccion."|".$comuna."|".$telefono."**--**");
            //print("RUT ".$rutdv. " NO REGISTRADO");
            $error=true;
	}
	else
	{
            pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('".$rutdv."'), '$data');");
            pg_query("
            INSERT INTO pacientes VALUES
            (
                DEFAULT,
                upper('$rutdv'),
                '$nombres',
                '$paterno',
                '$materno',
                '$fc_nac',
                $sexo,
                -1,
                '',
                -1,
                -1,
                '$direccion',
                '$comuna',
                0,
                -1,
                '$telefono',
                null,
                null,
                null,
                null,
                '$pac_ficha'
                );
                ");			
                //print("ERROR CONTROLADO");
		//print("RUT ".$rutdv. " PRESENTA OTRO TIPO DE ERROR");
		//print("**--**DATOS:".$rutdv."|".$pac_ficha."|".$nombres."|".$paterno."|".$materno."|".$fc_nac."|".$sexo."|".$direccion."|".$comuna."|".$telefono."**--**");
		$error=true;
            }
        }
	else
	{
            if(trim($codcybl[1])=="01907")
            {
                pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('$rutdv'), '$data');");
                pg_query("
                INSERT INTO pacientes VALUES
                (
                DEFAULT,
                upper('$rutdv'),
                '$nombres',
                '$paterno',
                '$materno',
                '$fc_nac',
                $sexo,
                -1,
                '',
                -1,
                -1,
                '$direccion',
                '$comuna',
                0,
                -1,
                '$telefono',
                null,
                null,
                null,
                null,
                '$pac_ficha'
                );
                ");	
		//print("ERROR CONTROLADO");
		//print("**--**DATOS:".$rutdv."|".$pac_ficha."|".$nombres."|".$paterno."|".$materno."|".$fc_nac."|".$sexo."|".$direccion."|".$comuna."|".$telefono."**--**");
		//print("RUT ".$rutdv. " BLOQUEADOS S/COTIZ. AL DIA");
		$error=true;
            }
        }
	if(!$error)
	{
   	switch ($pac_tramo[1])
   	{
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
	
	//if(strstr($isapre[1], "isapre")!=false)
	
	//$prevision=5;
	
	if(!isset($prevision))
		$prevision=6;
		
	if(trim(substr($pac_sex[1],0,1))=="M")
		$pac_sex[1] = 0;
	else
   	$pac_sex[1] = 1;
      
	$ciud_id = cargar_registro("SELECT ciud_id FROM comunas WHERE ciud_cod_nacional=".$pac_ciud_id[1]."", true);
	if($ciud_id) {
			$ciud_id = $ciud_id['ciud_id'];
	}
	else {
		$ciud_id =-1;
	}
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
	$paciente_ciud_id = $ciud_id;
	$paciente_nacion_id = '-1';
	$paciente_estado_civil = 'null';
        if(count($pac_telefono)>0)
	{
            $paciente_fono = $pac_telefono[1];
        }
        else
        {
            $paciente_fono = "0";
        }
	if(!$paciente_nombre=="")
	{
		if($encontrado)
		{
      	if($actualizar)
         {
                    
         }
		}
      else
		{
			//print("<br>");
			//print("<br>");
			//print("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, '".$rut."-".$dv."', '$data');");
			/*
         print("INSERT INTO pacientes VALUES
         (DEFAULT,uppper('$paciente_rut'),'$paciente_nombre',
			'$paciente_appat',
         '$paciente_apmat',
         '$paciente_fc_nac',
         $paciente_sex_id,
         $prevision,
         $paciente_sector_nombre,
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
                '$pac_ficha',
                null,
                null,
                null,
                null,
                null,
                null,
                null
                );");
			*/
			//print("<br>");
			//print("<br>");
      	pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, '".$rut."-".$dv."', '$data');");
         $agregar_paciente = "INSERT INTO pacientes VALUES
         (DEFAULT,upper('$paciente_rut'),'$paciente_nombre',
                '$paciente_appat',
                '$paciente_apmat',
                '$paciente_fc_nac',
                $paciente_sex_id,
                $prevision,
                $paciente_sector_nombre,
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
                '$pac_ficha',
                null,
                null,
                null,
                null,
                null,
                null,
                null
                );";
                pg_query($agregar_paciente);
                /*
					$chk = cargar_registro("SELECT pac_id FROM pacientes WHERE pac_rut='".$rutdv."';");
					if($chk)
      			{
      				return $chk['pac_id'];	
      			}  
      			*/           
			}
       }
}
    }



?>
