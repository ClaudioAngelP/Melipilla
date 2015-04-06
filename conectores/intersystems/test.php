<?php 

	require_once('parse_trakcare2.php');
	
	error_reporting(E_ALL);
	
	$files=scandir('interc');
	
	for($i=2;$i<sizeof($files);$i++) {
		
		$name=$files[$i];
		
		print("PROCESANDO: $name <BR /><BR /><BR /><BR />");
		
		$data=json_decode(file_get_contents('interc/'.$files[$i]), true);
		
		if(trim($data['raw_post_data'])!='') {
			
			print("PROCESANDO XML <br>");

			$xmlString = str_replace("<SOAP-ENV:Envelope xmlns:SOAP-ENV=''http://schemas.xmlsoap.org/soap/envelope/'' xmlns:xsi=''http://www.w3.org/2001/XMLSchema-instance'' xmlns:s=''http://www.w3.org/2001/XMLSchema''>",
									'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:s="http://www.w3.org/2001/XMLSchema">',
									trim($data['raw_post_data']));

			//$xmlString = str_replace("<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:s=\"http://www.w3.org/2001/XMLSchema\">",
			//						'<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:s="http://www.w3.org/2001/XMLSchema">',
			//						$xmlString);
			
			$xmlString = str_replace('SOAP-ENV:', 'SOAP-ENV', 
									$xmlString
									);

			$xmlString = str_replace('xsi:', '', 
									$xmlString	);

			$xmlString = str_replace('xmlns:', '', 
									$xmlString	);
									

			$xml=@simplexml_load_string($xmlString);
			
			print("<pre>");
			print_r($xml);
			print("</pre>");
				
			$event=$xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0]->asXML();
								
			$event_type=$xml->{'SOAP-ENVBody'}[0]->SendEvent[0]->pRequest[0]['MessageName'];
				
			switch($event_type) {
					
				case 'PatientEvent': case 'PatientUpdEvent':
					procesar_paciente($xml, $name);
				break;
				case 'PatientApptEvent': case 'PatientApptUpdEvent':
					procesar_citacion($xml, $name);
				break;
				default:
					procesar_interconsulta($xml, $name);
				break;
						
			}
				
		}
	}


?>
