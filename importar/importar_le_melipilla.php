<?php
    set_time_limit(0);
    require_once('/var/www/produccion/config.php');
    require_once('/var/www/produccion/conectores/sigh.php');
    
    $f=explode("\n",(file_get_contents('lista de Espera_20141201_20150106.csv')));
    $fnd=0;
    $nfnd=0;
    $ndoc=0;
    $poli_malo=0;
    
    
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
                    pg_query("INSERT INTO pacientes_fonasa VALUES (DEFAULT, CURRENT_TIMESTAMP, upper('".$rutdv."'), '".pg_escape_string($data)."', '$cert_folio', $prevision);");
            }
            pg_query("UPDATE pacientes SET prev_id=$prevision, pac_tramo='$tramo_fonasa' WHERE upper(pac_rut)=upper('".$rutdv."')");
            if($prais)
            	pg_query("UPDATE pacientes SET pac_prais=true where upper(pac_rut)=upper('".$rutdv."')");
            else
            	pg_query("UPDATE pacientes SET pac_prais=false WHERE upper(pac_rut)=upper('".$rutdv."')");
        }
    }
    
    pg_query("START TRANSACTION;");
    for($i=1;$i<sizeof($f);$i++)
    {
        
        $r=explode(';',($f[$i]));
        //----------------------------------------------------------------------
        //print_r($r);
        $prut=explode('-',trim(strtoupper($r[1])."-".strtoupper($r[2])));
	$pac_rut=($prut[0]*1).'-'.$prut[1];
        
        $folio=trim($r[0]);
        print("<br>");
        print("Linea :".$i." FOLIO:".$folio." RUT: ".$pac_rut."<br>");
        if(!isset($r[1]) OR trim($r[1])==''){
            print("<br>");
            print("Salto: Linea :".$i." FOLIO:".$folio."");
            print("<br>");
            continue;
        }
        //----------------------------------------------------------------------
        
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
	if(trim($r[29])!='') {
            $motivo_salida=(trim($r[29])*1);
	} else {
            $motivo_salida='0';
        }
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $fecha_ingreso=str_replace('-','/',trim($r[35]));
        $fecha_entrada=str_replace('-','/',trim($r[19]));
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        if(trim($r[39])!='')
            $fecha_salida="'".str_replace('-','/',trim($r[39]))."'";
	else
            $fecha_salida='null';
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
	$unidad_desc=str_replace("&yen;", "&Ntilde;", htmlentities(trim($r[27])));
        $unidad_desc=pg_escape_string($unidad_desc);
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
	$poli=cargar_registro("SELECT * FROM especialidades WHERE upper(esp_desc)=upper('".html_entity_decode($unidad_desc)."')");
        if(!$poli)
        {
            $poli=cargar_registro("SELECT * FROM especialidades WHERE upper(esp_nombre_especialidad)=upper('".html_entity_decode($unidad_desc)."')");
            if(!$poli)
            {
                $poli_malo++;
                print("<br>");
                print("SELECT * FROM especialidades WHERE upper(esp_desc)=upper('".$unidad_desc."')");
                print("<br>");
                echo "ESP [".($i+1)."]: $unidad_desc (POLI) NO EXISTE.<BR />";
                $esp_id=-1;
                //continue;
            }
            else
            {
                $esp_id=$poli['esp_id'];
                $unidad_id=0;
            }
	}
        else
        {
            //$unidad_id=$poli['esp_id'];
            $esp_id=$poli['esp_id'];
            $unidad_id=0;
	}
        
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        //list($inst_cod)=explode(' ',$r[8]);
        //$inst_cod=trim(strtoupper($inst_cod));
        $inst_cod=trim(strtoupper($r[16]));
        //if($inst_cod=='08-090')
        //    $inst_cod='08-101';
        
        
        //if($inst_cod=='CONSULTORIO DR. EDELBERTO ELGUETA')
        //    $inst_cod='Dr. Edelberto Elgueta Consultorio';
        
        
	//$inst=cargar_registro("SELECT * FROM instituciones WHERE inst_codigo_ifl='$inst_cod'");
        $inst=cargar_registro("SELECT * FROM instituciones WHERE upper(inst_nombre)=upper('$inst_cod')");
	if(!$inst)
        {
            pg_query("INSERT INTO instituciones VALUES (DEFAULT,'$inst_cod',0,'',0);");
            echo "INST [".($i+1)."]: $inst_cod NO ENCONTRADA.<BR/>";
            $inst=cargar_registro("SELECT * FROM instituciones WHERE upper(inst_nombre)=upper('$inst_cod')");
            if(!$inst)
            {
                $inst_id=$sgh_inst_id;
                
            }
            else
            {
                $inst_id=$inst['inst_id'];
            }
        }
        else
        {
            $inst_id=$inst['inst_id'];
            
        }
        
        
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        
        
        if(trim($r[24])!='')
        {
            $sospecha_diag=trim($r[24]);
        }
        else
            $sospecha_diag='';
        
        if(trim($r[30])=='' OR trim($r[30])=='0000-00-00')
        {
            $estado_inter=0;
            $fecha_egreso='null';
        }
        else
        {
            $estado_inter=1;
            $fecha_egreso="'".str_replace('-','/',trim($r[30]))."'";
        }
        $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$pac_rut'");
	if($pac)
        {
            $pac_id=$pac['pac_id'];
            $fnd++;
            $folio=0;
            //print("<br>");
            if($esp_id==-1){
                print("SELECT * FROM interconsulta WHERE inter_pac_id=$pac_id and inter_especialidad=$esp_id and inter_ingreso::date='$fecha_ingreso' and inter_fecha_entrada::date='$fecha_entrada' and inter_inst_id1=$inst_id and upper(inter_espdesc)=upper('$unidad_desc')");
                print("<br>");
                $reg_pac=cargar_registro("SELECT * FROM interconsulta WHERE inter_pac_id=$pac_id and inter_especialidad=$esp_id and inter_ingreso::date='$fecha_ingreso' and inter_fecha_entrada::date='$fecha_entrada' and inter_inst_id1=$inst_id and upper(inter_espdesc)=upper('$unidad_desc')");
            } else {
                print("SELECT * FROM interconsulta WHERE inter_pac_id=$pac_id and inter_especialidad=$esp_id and inter_ingreso::date='$fecha_ingreso' and inter_fecha_entrada::date='$fecha_entrada' and inter_inst_id1=$inst_id");
                print("<br>");
                $reg_pac=cargar_registro("SELECT * FROM interconsulta WHERE inter_pac_id=$pac_id and inter_especialidad=$esp_id and inter_ingreso::date='$fecha_ingreso' and inter_fecha_entrada::date='$fecha_entrada' and inter_inst_id1=$inst_id");
            }
            if(!$reg_pac)
            {
                print("InterConsulta No Encontrada Linea ".($i+1));
                print("<br>");
                
                
                
                pg_query("INSERT INTO interconsulta VALUES (
                DEFAULT,
                $folio,
                $inst_id,
                $esp_id,
                $unidad_id,
                $estado_inter,
                '$sospecha_diag',
                '',
                '',
                $pac_id,
                $sgh_inst_id,
                0,
                '$fecha_ingreso'::date, 
                '',
                -1,
                0,
                '',
                0,
                'CARGADO AUTOMATICAMENTE L.E. 2014',
                0,
                $motivo_salida,
                0,
                0,
                now(),
                '$fecha_ingreso',
                -1,
                0,
                7,
                7,
                7,
                '',
                $fecha_salida,
                null,
                null,
                '$fecha_entrada'::date,
                null,
                0,
                $fecha_egreso,
                '$unidad_desc');");
            }
            else
            {
                print("Inter Consulta Encontrada primera seccion Linea ".($i+1));
                print("<br>");
            }
        }
        else
        {
            print("Paciente No encontrado Linea ".($i+1));
            print("<br>");
            
            $nombres=trim(strtoupper($r[3]));
            $paterno=trim(strtoupper($r[4]));
            $materno=trim(strtoupper($r[5]));
            $fc_nac="'".str_replace('-','/',trim($r[6]))."'";
            $sexo=trim($r[7]*1);
            if($sexo==1)
                $pac_sexo=0;
            if($sexo==2)
                $pac_sexo=1;
            $direccion=trim(strtoupper($r[11]));
            $comuna=trim(strtoupper($r[13]));
            $telefono=trim(strtoupper($r[14]));
            
            $ciud_id = cargar_registro("SELECT ciud_id FROM comunas WHERE upper(ciud_desc)=upper('$comuna');", true);
            if($ciud_id)
            {
                $ciud_id = $ciud_id['ciud_id'];
            }
            else
            {
                $ciud_id =-1;
            }
            
            pg_query("
            INSERT INTO pacientes VALUES
            (
                DEFAULT,
                upper('$pac_rut'),
                '$nombres',
                '$paterno',
                '$materno',
                $fc_nac,
                $pac_sexo,
                -1,
                '',
                -1,
                -1,
                '$direccion',
                $ciud_id,
                0,
                -1,
                '$telefono',
                null,
                null,
                null,
                null,
                0
                );
            ");
            
            
            
            $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$pac_rut'");
            if($pac)
            {
                $pac_id=$pac['pac_id'];
                $folio=0;
                $reg_pac=cargar_registro("SELECT * FROM interconsulta WHERE inter_pac_id=$pac_id and inter_especialidad=$esp_id and inter_ingreso::date='$fecha_ingreso' and inter_fecha_entrada::date='$fecha_entrada' and inter_inst_id1=$inst_id");
                if(!$reg_pac)
                {
                    print("InterConsulta No Encontrada Linea ".($i+1));
                    print("<br>");
                    
                    pg_query("INSERT INTO interconsulta VALUES (
                    DEFAULT,
                    $folio,
                    $inst_id,
                    $esp_id,
                    $unidad_id,
                    $estado_inter,
                    '$sospecha_diag',
                    '',
                    '',
                    $pac_id,
                    $sgh_inst_id,
                    0,
                    '$fecha_ingreso'::date, 
                    '',
                    -1,
                    0,
                    '',
                    0,
                    'CARGADO AUTOMATICAMENTE L.E. 2014',
                    0,
                    $motivo_salida,
                    0,
                    0,
                    now(),
                    '$fecha_ingreso',
                    -1,
                    0,
                    7,
                    7,
                    7,
                    '',
                    $fecha_salida,
                    null,
                    null,
                    '$fecha_entrada'::date,
                    null,
                    0,
                    $fecha_egreso,
                    '$unidad_desc');");
                }
                else
                {
                    print("Inter Consulta Encontrada Segunda seccion Linea: ".($i+1));
                    print("<br>");
                }
            }
            $nfnd++;
            print("<br>");
            echo "PAC: $pac_rut NO ENCONTRADO.<br />";
            print("<br>");
            pac_fonasa(strtoupper(trim($pac_rut)));
            //continue;
            
        }
    }
    pg_query("COMMIT;");
    echo "TERMINADO! FND OK: $fnd NOT FND: $nfnd POLIS MALOS: $poli_malo<BR>";
?>