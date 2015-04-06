<?php
    if(isset($_POST['pac_rut']))
    {
        require_once('../../conectar_db.php');
    }
    
    function pac_fonasa($rutdv)
    {
        $chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE pac_rut = '".$rutdv."' AND cert_fecha::date=CURRENT_DATE");
	if($chq){
            return;
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
        /*
        if(($_SESSION['sgh_usuario_id']*1)==7)
        {
            print($data);
            die();
        }   
        */
        ob_end_clean();
        preg_match('/<errorM>(.+)<\/errorM>/', $data, $errorm);
        preg_match('/<folio>(.+)<\/folio>/',$data,$folio);
        preg_match('/<tramo>(.+)<\/tramo>/',$data, $tramo);
        preg_match('/<coddesc>(.+)<\/coddesc>/',$data, $isapre);
        preg_match('/<desIsapre>(.+)<\/desIsapre>/',$data, $detalle_isapre);
        
        
        
        $error=false;
        $en_fonasa=true;
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
                //print("\n");
                //print("error|RUT NO REGISTRADO EN FONASA");
                //print("\n");
                $en_fonasa=false;
            }
            else
            {
                if(trim(strtoupper($errorm[0]))==strtoupper("RUT incorrecto"))
                {
                    //print("\n");
                    //print("error|RUT INCORRECTO");
                    //print("\n");
                    $error=true;
                }
                else
                {
                    //print("\n");
                    //print("RUT ".$rutdv. " PRESENTA OTRO TIPO DE ERROR");
                    //print("\n");
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
            if(count($tramo)>0)
            {
                switch ($tramo[1])
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
            
            
            if(count($folio)>0)
            {
                $cert_folio = $folio[1];
                $chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE upper(pac_rut) = upper('".$rutdv."') AND cert_fecha::date=CURRENT_DATE");
                if(!$chq)
                    pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('".$rutdv."'), '$data', '$cert_folio', $prevision);");
            }
            
            pg_query("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa' WHERE upper(pac_rut)=upper('".$rutdv."')");
            if($prais)
            	pg_query("UPDATE pacientes SET pac_prais=true where upper(pac_rut)=upper('".$rutdv."')");
            else
            	pg_query("UPDATE pacientes SET pac_prais=false WHERE upper(pac_rut)=upper('".$rutdv."')");
        }
    }
	
	
    if(isset($_POST['pac_rut']))
    {
        $pac_rut=$_POST['pac_rut'];
        $recuperar_fonasa=true;
        if($recuperar_fonasa)
            pac_fonasa($pac_rut);
        
	$r=cargar_registro("SELECT *, (SELECT MAX(cert_fecha) FROM pacientes_fonasa WHERE pacientes_fonasa.pac_rut=pacientes.pac_rut) AS fecha_fonasa FROM pacientes WHERE upper(pac_rut)=upper('".$pac_rut."');");
	exit(json_encode($r));
    }
	
	

?>
