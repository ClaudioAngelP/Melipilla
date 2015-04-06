<?php 
    //print("<img src='logo.png'>");
   // die();
 require_once('../../conectar_db.php');

$convenio_id = $_POST['convenio_id'];


   error_reporting(E_ALL);
   include('Mail.php');
   include('Mail/mime.php');
   
   $convenio = cargar_registro("SELECT * FROM convenio WHERE convenio_id = $convenio_id");	
   
   
   $detalle_convenio="
   Estimado/a;<BR/><BR/>
   Con el objetivo de colaborar en su labor como administrador/a, adjuntamos documentaci&oacute;n del
   convenio que se indica a continuaci&oacute;n: <br /><br />
   <table style='width:80%;'>
		  <tr style='text-align:center;font-weight:bold;'>
	      <td bgcolor='#92cddc'>ID. CONVENIO:</td>
		  <td bgcolor='#daeef3'>".$convenio['convenio_licitacion']."</td>
		  </tr>
		  <tr style='text-align:center;font-weight:bold;'>
	      <td bgcolor='#92cddc'>CONVENIO NOMBRE:</td>
		  <td bgcolor='#daeef3'>".$convenio['convenio_nombre']."</td>
		  </tr>
		  <tr style='text-align:center;font-weight:bold;'>
		  <td bgcolor='#92cddc'>Fecha:</td>
		  <td bgcolor='#daeef3'>".date('d/m/Y')."</td>
		  </tr>";
		
	$html="	
		<center>
		$detalle_convenio
		<br /><br />
		Ante cualquier duda, comunicarse con el Departamento de Gesti&oacute;n de Convenios
		al anexo FONO DE ANEXO <BR/>
		o al correo electr&oacute;nico NOMBRE Y EMAIL DE RESPONZABLE DE CONVENIOS.
		<BR/><BR/>
		Saludos cordiales;		 
		<BR/><BR/>
		<img src='http://www.hospitaltalagante.cl/images/imagenes-template/logo_hospital%282%29.jpg'> 
		</center>	
		";


		function send_mail($to, $from, $subject, $body, $convenio_id) {
		 
		 $host = "190.107.177.206";
		 $username = "hfbc@sistemasexpertos.cl";
		 $password = "solucion1234";
		 $convenio_id = $convenio_id;
		 
		 $headers = array ('From' => $from,
		   'To' => $to,
		   'Subject' => $subject);
		   
		   
		   
		 $smtp = Mail::factory('smtp',
		   array ('host' => $host,
		     'auth' => true,
		     'username' => $username,
		     'password' => $password));
		 
		// Creating the Mime message
		$mime = new Mail_mime();
		
		// Setting the body of the email
		$mime->setTXTBody(strip_tags($body));
		$mime->setHTMLBody($body);
		 
		$l=pg_query("SELECT * FROM convenio_adjuntos WHERE convenio_id=$convenio_id");
	    
	    for($i=0;$i<pg_num_rows($l);$i++)
	    {
	        $adjunto = pg_fetch_assoc($l);	
			list($nombre,$tipo,$peso,$md5)=explode('|',$adjunto['cad_adjunto']);
		 	$file = './adjuntos_convenio/'.$md5;
		 	$mime->addAttachment($file,'',$nombre);
		 }
		
		 $body = $mime->get();
		 $headers = $mime->headers($headers); 
		 
		 $mail = $smtp->send($to, $headers, $body);
		 
		 } 
		
		//send_mail('rguerracortes@gmail.com,goarancibi@gmail.com','goarancibi@gmail.com',utf8_decode("Ingreso de Nuevo Convenio -".$convenio['convenio_licitacion']."".date('d/m/Y')),$html, $convenio_id);
		
		send_mail("claudio.angel@sistemasexpertos.cl,darwin.alvarado@sistemasexpertos.cl,".$convenio['convenio_mails']."",'hfbc@sistemasexpertos.cl',utf8_decode("Ingreso de Nuevo Convenio - $licitacion_conv  ".date('d/m/Y')),$html,$convenio_id);
		
?>