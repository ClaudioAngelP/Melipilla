<?php 

	$wsdl='SMGES.BS.WSService.CLS.wsdl';

	if(isset($_GET['wsdl']) OR isset($_GET['WSDL'])) {
		header('Content-type: text/xml');
		readfile($wsdl); exit();
	}
	
	if(isset($_GET['soap_method']) OR sizeof($_POST)>0 OR 
		$HTTP_RAW_POST_DATA!=null) {
		
		$init=microtime(true);
		
		if(isset($_GET['soap_method'])) 
			$method=$_GET['soap_method'];
		elseif(isset($_POST['soap_method'])) 
			$method=$_POST['soap_method'];
		else 
			$method='';
		
		switch($method) {
			
			case 'TestConnection':
				header('Content-type: text/xml');
				print("<SOAP-ENV:Envelope xmlns:SOAP-ENV='http://schemas.xmlsoap.org/soap/envelope/' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:s='http://www.w3.org/2001/XMLSchema'> 
  <SOAP-ENV:Body><TestConnectionResponse xmlns=\"InterSystems\"><TestConnectionResult>1</TestConnectionResult></TestConnectionResponse></SOAP-ENV:Body> 
</SOAP-ENV:Envelope>"); break;
				
			default:
				header('Content-type: text/xml');
				print("<SOAP-ENV:Envelope xmlns:SOAP-ENV='http://schemas.xmlsoap.org/soap/envelope/' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:s='http://www.w3.org/2001/XMLSchema'><SOAP-ENV:Body>");

				foreach($_GET AS $key => $value) {
					$data['get'][$key]=$value;	
				}

				foreach($_POST AS $key => $value) {
					$data['post'][$key]=$value;	
				}
				
				$file_name=date('Y-m-d-H-i-s-').str_replace(' ','_',microtime());				
				
				$data['raw_post_data']=$HTTP_RAW_POST_DATA;
				file_put_contents("data/".$file_name,json_encode($data));
				
				
				if(trim($HTTP_RAW_POST_DATA)=='') {
					exit();
				}

				$xmlString = str_replace("<SOAP-ENV:Envelope xmlns:SOAP-ENV=''http://schemas.xmlsoap.org/soap/envelope/'' xmlns:xsi=''http://www.w3.org/2001/XMLSchema-instance'' xmlns:s=''http://www.w3.org/2001/XMLSchema''>",
									'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:s="http://www.w3.org/2001/XMLSchema">',
									trim($data['raw_post_data']));


				$xmlString = str_replace('SOAP-ENV:', 'SOAP-ENV', 
									$xmlString  );

				$xmlString = str_replace('xsi:', '', 
									$xmlString	);

				$xmlString = str_replace('xmlns:', '', 
									$xmlString	);

				$xml=@simplexml_load_string($xmlString);
				
				$event=$xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0]->asXML();

				print("<SendEventResponse>");
				print("<SendEventResult>1</SendEventResult>");
				print("<pResponse>".$event."</pResponse>");
				print("</SendEventResponse>");

				print("</SOAP-ENV:Body></SOAP-ENV:Envelope>");								
				
				$event_type=$xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0]['MessageName'];
				
				require_once('parse_trakcare.php');
				
				switch($event_type) {
					
					case 'PatientEvent': case 'PatientUpdEvent':
						@procesar_paciente($xml, $file_name);
					break;
					case 'PatientApptEvent': case 'PatientApptUpdEvent':
						@procesar_citacion($xml, $file_name);
					break;
					default:
						@procesar_interconsulta($xml, $file_name);
					break;
						
				}
				
				
				$qdata=pg_escape_string(json_encode($data));
				$deltaT=floor((microtime(true)-$init)*1000);
				
				pg_query("INSERT INTO logs_integraciones VALUES (DEFAULT, CURRENT_TIMESTAMP, $deltaT, '$qdata');");
				
				break;
					
		}
		
		exit();
		
	}

		
?>

<html>
<title>SCV Webservice</title>

<body>

<h2>Bienvenido al webservice de <b>SISTEMAS EXPERTOS LTDA.</b>.</h2><br/><br/>

<div style="background-color:#dddddd;">
Acceder <a href='webservice.php?wsdl'>Descripci&oacute;n del Servicio</a>.<br/><br/>

Acepta los siguientes eventos:<br /><br />
</div>

<ul>
<li>SendEvent</li>
<li>SendQuery</li>
<li><a href='webservice.php?soap_method=TestConnection'>TestConnection</a> [GET]</li>
</ul>

</body>

</html>
