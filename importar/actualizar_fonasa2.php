<?php
    error_reporting(E_ALL);
    //require_once('../conectar_db.php');
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    set_time_limit(0);
    
    
    function pac_fonasa($rutdv)
    {
        list($rut, $dv)=explode('-',$rutdv);

        $xml='<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <tns:getCertificadoPrevisional xmlns:tns="http://ws.fonasa.cl:8080/Certificados/Previsional">
      <!-- Element must appear exactly once -->
      <tns:query>
        <!-- Element must appear exactly once -->
        <tns:queryTO>
          <!-- Element must appear exactly once -->
          <tns:tipoEmisor>1</tns:tipoEmisor>
          <!-- Element must appear exactly once -->
          <tns:tipoUsuario>1</tns:tipoUsuario>
        </tns:queryTO>
        <!-- Element must appear exactly once -->
        <tns:entidad>61606608</tns:entidad>
        <!-- XXXXXXXX corresponde a cod establecimiento DEIS entidad que consulta sin guion -->
        <tns:claveEntidad>6160</tns:claveEntidad>
        <!-- XXXX corresponde a Clave entidad que consulta 4 digitos numericos -->
        <tns:rutBeneficiario>15999794</tns:rutBeneficiario>
        <!-- XXXXXXXX corresponde a RUT de la Persona que se consulta sin DV -->
        <tns:dgvBeneficiario>4</tns:dgvBeneficiario>
        <!-- DV corresponde a digito verificador de RUT -->
        <tns:canal>1</tns:canal>
      </tns:query>
    </tns:getCertificadoPrevisional>
  </soap:Body>
</soap:Envelope>
';
  
/*
$xml='<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<tns:getCertificadoPrevisional xmlns:tns="http://certificadorprevisional.fonasa.gov.cl.ws/">
<tns:query>
<tns:queryTO>
<tns:tipoEmisor>1</tns:tipoEmisor>
<tns:tipoUsuario>1</tns:tipoUsuario>
</tns:queryTO>
<tns:entidad>61608000</tns:entidad>
<tns:claveEntidad>6160</tns:claveEntidad>
<tns:rutBeneficiario>'.$rut.'</tns:rutBeneficiario>
<tns:dgvBeneficiario>'.$dv.'</tns:dgvBeneficiario>
<tns:canal>1</tns:canal>
</tns:query>
</tns:getCertificadoPrevisional>
</soap:Body>
</soap:Envelope>';
*/
        

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
        preg_match('/<errorM>(.+)<\/errorM>/', $data, $errorm);
        preg_match('/<folio>(.+)<\/folio>/',$data,$folio);
        preg_match('/<tramo>(.+)<\/tramo>/',$data, $tramo);
        preg_match('/<coddesc>(.+)<\/coddesc>/',$data, $isapre);
        preg_match('/<desIsapre>(.+)<\/desIsapre>/',$data, $detalle_isapre);
        preg_match('/<genero>(.+)<\/genero>/', $data, $pac_sex);
        
        print_r($data);
     die();
        
        
        
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
                print("\n");
                print("error|RUT NO REGISTRADO EN FONASA");
                print("\n");
                //$error=true;
                $en_fonasa=false;
		
            }
            else
            {
                if(trim(strtoupper($errorm[0]))==strtoupper("RUT incorrecto"))
                {
                    print("\n");
                    print("error|RUT INCORRECTO");
                    print("\n");
                    $error=true;
                }
                else
                {
                    print("\n");
                    print("RUT ".$rutdv. " PRESENTA OTRO TIPO DE ERROR");
                    print("\n");
                    $error=true;
                }
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
	    
            if($en_fonasa)
            {
		if(count($pac_sex)>0)
		{
	        	if(trim(substr($pac_sex[1],0,1))=="M")
				$pac_sex[1] = 0;
		        else
				$pac_sex[1] = 1;
			$sexo=1;
		}
		else
		{
			$sexo=0;
		}
            }
      
			
            $cert_folio = $folio[1];
        
            $chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE upper(pac_rut) = upper('$rutdv') AND cert_fecha::date=CURRENT_DATE");
		
		//if(!$chq)
            pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('$rutdv'), '$data', '$cert_folio', $prevision);");
        
            if($en_fonasa)
            {
		 print("\n");
		 print("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa',sex_id=$pac_sex[1] WHERE upper(pac_rut)=upper('$rutdv')");
                 print("\n");
		 if($sexo==0)
               		pg_query("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa',sex_id=2 WHERE upper(pac_rut)=upper('$rutdv')");
		 else
			pg_query("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa',sex_id=$pac_sex[1] WHERE upper(pac_rut)=upper('$rutdv')");
		if($prais)
			pg_query("UPDATE pacientes SET pac_prais=true WHERE upper(pac_rut)=upper('$rutdv')");
		else
			pg_query("UPDATE pacientes SET pac_prais=false WHERE upper(pac_rut)=upper('$rutdv')");
	     }
	     else
	     {
               print("\n");
		 print("UPDATE pacientes SET prev_id=$prevision, pac_tramo='' WHERE upper(pac_rut)=upper('$rutdv')");
               print("\n");

               pg_query("UPDATE pacientes SET prev_id=$prevision, pac_tramo='' WHERE upper(pac_rut)=upper('$rutdv')");
               if($prais)
               		pg_query("UPDATE pacientes SET pac_prais=true WHERE upper(pac_rut)=upper('$rutdv')");
               else
               		pg_query("UPDATE pacientes SET pac_prais=false WHERE upper(pac_rut)=upper('$rutdv')");
	     }
            //print("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa' WHERE pac_rut='$rutdv';");
        }
    }
    
    
    
    $fi=explode("\n", utf8_decode(file_get_contents('ruts.csv')));
    //for($i=0;$i<sizeof($fi);$i++)
    for($i=0;$i<sizeof($fi);$i++)
    {
        /*
        if(strstr($fi[$i],":"))
        {
            $arr=explode(":",$fi[$i]);
            $r=trim($arr[2]);
        }
        else
        {
            $r=trim($fi[$i]);
            //$r="";
        }
         * 
         */
        $r=trim($fi[$i]);
        $string_rut=$r;
        if($string_rut=="")
            continue;
        
        //print(trim($string_rut));
        //sleep(120);
	print("\n");
	print("Linea:".$i." Rut:".trim($string_rut));
	
        pac_fonasa(trim($string_rut));
        
        //sleep(120);
    }
?>
