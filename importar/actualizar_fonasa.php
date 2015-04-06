<?php
    error_reporting(E_ALL);
    //require_once('../conectar_db.php');
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    set_time_limit(0);
    
    
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
        preg_match('/<codcybl>(.+)<\/codcybl>/', $data, $codcybl);
        preg_match('/<folio>(.+)<\/folio>/',$data,$folio);
        preg_match('/<tramo>(.+)<\/tramo>/',$data, $tramo);
        preg_match('/<coddesc>(.+)<\/coddesc>/',$data, $isapre);
        preg_match('/<desIsapre>(.+)<\/desIsapre>/',$data, $detalle_isapre);
        preg_match('/<genero>(.+)<\/genero>/', $data, $pac_sex);
        
        //print_r($data);
     
                
        
        $error=false;
		$en_fonasa=true;
        if(count($errorm)>0)
        {
            $errorm = explode('errorM',$errorm[0]);
            $errorm = explode('>',$errorm[1]);
            $errorm = explode('<',$errorm[1]);
            
            if((trim(strtoupper($errorm[0]))==strtoupper("RUT NO REGISTRADO")) or ((strtoupper($errorm[0]))==strtoupper("Rut no existe en la Base Datos.")))
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
                    die();
                }
                else
                {
                    print("\n");
                    print("RUT ".$rutdv. " PRESENTA OTRO TIPO DE ERROR");
                    print("\n");
                    $error=true;
                    print_r($data);
                    die();
                }
            }
        } 
        
        /*
        00131 FUNCIONARIO DE SALUD. CUENTA CON BENEFICIOS B EN MAI
		00110 CERTIFICADO EN FONASA
		01901 BLOQUEADO POR ISAPRE
		01902 BLOQUEADOS POR AFILIADO FALLECIDO
		01911 BLOQUEADO CAPREDENA
		01914 BLOQUEADO POR AUDITORIA
		01903 BLOQUEADOS S/COTIZ. AL DIA
		*/ 
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
        
            if(!isset($prevision)){
				if(count($codcybl)>0) {
					if(trim($codcybl[1])=="01903") {
						print("\n\nRUT ".$rutdv. " BLOQUEADOS S/COTIZ. AL DIA o PRAIS");
						$prevision=6;
					}
					elseif(trim($codcybl[1])=="01902") {
						print("\n\nRUT ".$rutdv. " BLOQUEADOS POR AFILIADO FALLECIDO-");
						$prevision=6;
						
					}
					elseif(trim($codcybl[1])=="01972") {
						print("\n\nRUT ".$rutdv. " DIPRECA-PRAIS");
						$prevision=6;
					} else {
						if(!$en_fonasa){
							print("\n\nRUT ".$rutdv. " NO ENCONTRADO EN FONASA");
							$prevision=6;
						} else {
							print("\n CODIGO NO RECONOCIDO");
							print("\n\n\n");
							print_r($data);
							print("\n\n\n");
							die();
						}
					}
					
				} else {
					print("\n CODIGO NO ENCONTRADO");
					print("\n\n\n");
					print_r($data);
					print("\n\n\n");
					die();
				}
			}
			if(!isset($prevision)){
				print("\n\n PREVISION NO ASIGANADA");
				die();
			} else {
				if($prevision==""){
					print("\n\n PREVISION NO ASIGANADA");
					die();
				}
				
			}
            
            if($en_fonasa)
            {
				if(count($pac_sex)>0){
					if(trim(substr($pac_sex[1],0,1))=="M")
						$pac_sex[1] = 0;
					else
						$pac_sex[1] = 1;
					$sexo=1;
				} else {
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
			} else {
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
    
    
    
    $fi=explode("\n", utf8_decode(file_get_contents('ruts2.csv')));
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
