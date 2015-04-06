<?php
    if(isset($_GET['f1']) AND isset($_GET['f2']))
    {
        $fecha_inicio=$_GET['f1'];
	$fecha_final=$_GET['f2'];
    }
    else
    {
        if($argv[1]!='mercadopublico_v2.php')
            $dias=$argv[1]*1;
        else
            $dias=$argv[2]*1;
        $fecha_inicio=date('Y-m-d', mktime(0,0,0,date('m'),(date('d')*1)-$dias));
        $fecha_final=date('Y-m-d', mktime(0,0,0,date('m'),(date('d')*1)));
    }
    //$username='carorojase';
    //$password='ca1234';
    $username='elyevenes';
    $password='28nacha2012';
    
    $xml='<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GenerateXMLOCByParam xmlns="wsGetOC">
      <dteDateBegin>'.$fecha_inicio.'</dteDateBegin>
      <dteDateEnd>'.$fecha_final.'</dteDateEnd>
      <strState>0</strState>
      <intEnterprise>0</intEnterprise>
    </GenerateXMLOCByParam>
  </soap:Body>
</soap:Envelope>';

		

		$headers=explode("\n","Host: www.mercadopublico.cl
Content-Type: text/xml;charset=utf-8
Content-Length: ".strlen($xml)."
SOAPAction: \"wsGetOC/GenerateXMLOCByParam\"");

            
    $ch = curl_init();
    // cURL Setup
    curl_setopt($ch, CURLOPT_URL, "http://www.mercadopublico.cl/wsoc/wsGetOc.asmx");
    curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    //curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    ob_start();
    curl_exec($ch);
    $data=ob_get_contents();
    ob_end_clean();
		
    $data=str_replace(' xmlns="wsGetOC"','',$data);
    $data=str_replace('soap:','SOAP',$data);
        
    print("<pre>$data</pre>");
    //die();

    if(!$soap_obj=new SimpleXMLElement($data))
    {
        print("Error procesando SOAP de respuesta.<br>");	
	exit();
    }
		
    //print_r($soap_obj);
    
    $ordenes_xml = $soap_obj->SOAPBody->GenerateXMLOCByParamResponse->GenerateXMLOCByParamResult;

    //print("<pre>".htmlentities($ordenes_xml)."</pre>");

    if(!$xml_obj=new SimpleXMLElement($ordenes_xml))
    {
        print("Error procesando XML de respuesta.<br>");	
        exit();
    }
		
    //$ordenes_obj = $xml_obj->OrdersResults->OrdersList->Order;
    $ordenes_obj = $xml_obj->OrdersList->Order;
    include('procesar_xml_oc.php');
    foreach($ordenes_obj AS $orden_obj)
    {
        $nro_orden = pg_escape_string($orden_obj->OrderHeader->OrderNumber->BuyerOrderNumber);
	$xml=$orden_obj->asXML();
	file_put_contents('xml/Orden_'.$nro_orden.'.xml','<?xml version="1.0" ?>'.$xml);
	//echo $xml;
	procesar_xml($xml);
	print($nro_orden.'<br>');
	//print($xml.'<br>');
    }
?>
