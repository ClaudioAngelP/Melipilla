<?php 

chdir(dirname(__FILE__));

require_once('../conectar_db.php');

function pac_fonasa($rutdv,$tipo) {
	
	$bool=false;
	switch($tipo) {			
				case 0:
					
					$sw="pac_rut='".$rutdv."'";
					list($rut, $dv)=explode('-',$rutdv);
					$bool=true;
				break;
				case 1:
				 	$sw="pac_pasaporte='".$rutdv."'";
				break;
				case 2:
				 $sw="pac_id=".$rutdv;
				$pac=cargar_registro("SELECT * FROM pacientes WHERE $sw ORDER BY pac_id DESC LIMIT 1;");	
				
				if($pac)
				{
					if($pac['pac_rut']!=''){
					 list($rut, $dv)=explode('-',$pac['pac_rut']);
					$sw="pac_rut='".$rutdv."'";
					$bool=true;
					}
				}
				break;
				case 3:
				 $sw="pac_ficha='".$rutdv."'";
				break;
			}

    
	if($bool==true){
	$chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE $sw AND cert_fecha::date=CURRENT_DATE");
	}
	
	if($chq) {
		if($chq['pac_rut']!=''){
		 list($rut, $dv)=explode('-',$chq['pac_rut']);
		
		}
	
		$prevision=$chq['cert_prev_id']*1;
	
		$prev=cargar_registro("SELECT * FROM prevision WHERE prev_id=$prevision");
		$func=cargar_registro("SELECT * FROM funcionarios_recaudacion WHERE frec_rut='$rutdv'");
		$prev['frec_id']=$func['frec_id']*1;
		$prev['desc']=$chq['cert_desc'];
		$prev['prais']=$chq['cert_prais'];
		$prev['error']='0';
		//print_r($prev);
		switch ($prev['prev_id']) {
                        case "1":
                                $prev['prev_id'] = 1;
                                $prev['prev_desc'] ="FONASA GPO-A";
                                break;
                        case "2":
                                 $prev['prev_id']  = 2;
                                 $prev['prev_desc']="FONASA GPO-B";
                                break;
                        case "3":
                                 $prev['prev_id']  = 3;
                                 $prev['prev_desc']="FONASA GPO-C";
                                break;
                        case "4":
                                 $prev['prev_id']  = 4;
                                 $prev['prev_desc']="FONASA GPO-D";
                                break;
                        default:
                                 $prev['prev_desc']="";
                }
		
		
		return json_encode($prev);
	
	}

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
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        ob_start();
        curl_exec($ch);
        $data=ob_get_contents();
        ob_end_clean();
//print($data);
	  if(!strstr($data,$rut)) {
		
		if($bool==true){
		$chq = cargar_registro("SELECT * FROM pacientes_fonasa WHERE $sw ORDER BY cert_fecha DESC LIMIT 1");
		}
        if($chq) {

                $prevision=$chq['cert_prev_id']*1;

                $prev=cargar_registro("SELECT * FROM prevision WHERE prev_id=$prevision");
                $func=cargar_registro("SELECT * FROM funcionarios_recaudacion WHERE frec_rut='$rutdv'");
                $prev['frec_id']=$func['frec_id']*1;
                $prev['desc']=$chq['cert_desc'];
                $prev['prais']=$chq['cert_prais'];
				$prev['error']='1';
                return json_encode($prev);

        	} else {

			$pac=cargar_registro("SELECT * FROM pacientes WHERE $sw ORDER BY pac_id DESC LIMIT 1;");

			$prevision=$pac['prev_id']*1;

			$prev=cargar_registro("SELECT * FROM prevision WHERE prev_id=$prevision");
               		$func=cargar_registro("SELECT * FROM funcionarios_recaudacion WHERE frec_rut='$rutdv'");

       		        $prev['frec_id']=$func['frec_id']*1;
                	$prev['desc']=$pac['cert_desc'];
	                $prev['prais']='000';
	                $prev['error']='2';
                	return json_encode($prev);
		}
        } 
        preg_match('/<folio>(.+)<\/folio>/',$data,$folio);

        preg_match('/<tramo>(.+)<\/tramo>/',$data, $tramo);

        preg_match('/<coddesc>(.+)<\/coddesc>/',$data, $tdesc);
		$desc=$tdesc[1];

        preg_match('/<codigoprais>(.+)<\/codigoprais>/',$data, $tprais);
		$prais=$tprais[1];
        
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

                if(strstr(strtolower($desc), "isapre")!=false)
                        $prevision=5;

                if(!isset($prevision))
                        $prevision=6;

                $cert_folio = $folio[1];
				
				$data=pg_escape_string($data);
				$desc=pg_escape_string($desc);
				

                pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, '$rutdv', '$data', '$cert_folio', $prevision, '$desc', '$prais');");

				//print("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, '$rutdv', '$data', '$cert_folio');<br>");

                pg_query("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa' WHERE pac_rut='$rutdv'");
                //print("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa' WHERE pac_rut='$rutdv';");
			
				$prev=cargar_registro("SELECT * FROM prevision WHERE prev_id=$prevision");
				$func=cargar_registro("SELECT * FROM funcionarios_recaudacion WHERE frec_rut='$rutdv'");
				$prev['frec_id']=$func['frec_id']*1;
				$prev['desc']=$desc;
				$prev['prais']=$prais;
				$prev['error']='0';
				
				switch ($prev['prev_id']) {
                        case "1":
                                $prev['prev_id'] = 1;
                                $prev['prev_desc'] ="FONASA GPO-A";
                                break;
                        case "2":
                                 $prev['prev_id']  = 2;
                                 $prev['prev_desc']="FONASA GPO-B";
                                break;
                        case "3":
                                 $prev['prev_id']  = 3;
                                 $prev['prev_desc']="FONASA GPO-C";
                                break;
                        case "4":
                                 $prev['prev_id']  = 4;
                                 $prev['prev_desc']="FONASA GPO-D";
                                break;
                        default:
                                 $prev['prev_desc']="";
                }
		
				return json_encode($prev);
				
    }
	
	exit(pac_fonasa($_POST['rut'],$_POST['tipo_rut']*1));

?>
