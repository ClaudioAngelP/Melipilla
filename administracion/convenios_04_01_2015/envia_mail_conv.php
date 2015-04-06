<?php
error_reporting(E_ALL);

 include('Mail.php');
 include('Mail/mime.php');

 //$start=microtime();

 function send_mail($to, $from, $subject, $body) {
 
 $host = "190.107.177.206";
 $username = "hcfb@sistemasexpertos.cl";
 $password = "1234soluciones";
 
 $headers = array ('From' => $from,
   'To' => $to,
   'Subject' => $subject);
   
 $smtp = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
     'username' => $username,
     'password' => $password));
 
 // Creating the Mime message
 $mime = new Mail_mime($crlf);

 // Setting the body of the email
 $mime->setTXTBody(strip_tags($body));
 $mime->setHTMLBody($body);

 $body = $mime->get();
 $headers = $mime->headers($headers); 
 
 $mail = $smtp->send($to, $headers, $body);
 
	//print(" ".(microtime()-$start)." msecs...");
	
 }
 send_mail('claudio.angel@sistemasexpertos.cl,darwin.alvarado@sistemasexpertos.cl','hcfb@sistemasexpertos.cl',utf8_decode('Prueba Correo '.date('d/m/Y')),"Prueba COrreo1");

?>
