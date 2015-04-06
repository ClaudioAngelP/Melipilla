<?php

  require_once('../../conectar_db.php');
  
  $convenio_id=$_POST['convenio_id']*1;
  
  $licitacion_conv = pg_escape_string(strtoupper(trim(utf8_decode($_POST['convenio_licitacion']))));
  $nombre_conv = pg_escape_string(strtoupper(trim(utf8_decode($_POST['nombre_convenio']))));
  
  /*   
CREATE TABLE convenio
(
  convenio_id bigserial NOT NULL,
  convenio_nombre character varying DEFAULT 80,
  prov_id bigint,
  convenio_monto bigint,
  convenio_plazo integer,
  convenio_mails text,
  convenio_fecha_inicio date,
  convenio_fecha_final date,
  convenio_licitacion text,
  convenio_nro_res_aprueba text,
  convenio_nro_anio_aprueba character varying(4),
  convenio_nro_res_adjudica text,
  convenio_nro_anio_adjudica character varying(4),
  convenio_nro_res_contrato text,
  convenio_fecha_resolucion date,
  func_id bigint,
  convenio_nro_boleta text,
  convenio_banco_boleta text,
  convenio_fecha_boleta date,
  convenio_monto_boleta bigint,
  convenio_multa text,
  convenio_comentarios text,
  convenio_categoria text,
  convenio_aprueba text,
  convenio_tipo_licitacion smallint,
  CONSTRAINT convenio_id PRIMARY KEY (convenio_id)
)
WITH (
  OIDS=FALSE
);
* 
* 
* 
   */
  
  $prov_id=$_POST['id_proveedor']*1;
  $func_id=$_POST['func_id']*1;
  $monto=$_POST['monto']*1;
  $plazo=$_POST['plazo']*1;
  $monto2=0;
  $plazo2=$_POST['plazo2']*1;
  
  $categoria=$_POST['categoria'];
  $tipo_licitacion=$_POST['tipo_lic']*1;

  $mails=pg_escape_string($_POST['mails']);
  
  $fecha_inicio=pg_escape_string($_POST['inicio']);
  $fecha_final=pg_escape_string($_POST['termino']);
  $fecha_boleta=pg_escape_string($_POST['fecha_boleta']);

  $res_aprueba=pg_escape_string($_POST['res_aprueba']);
  $fecha_aprueba=pg_escape_string($_POST['fecha_aprueba']);
  $sel_aprueba=$_POST['sel_aprueba'];
  

  $res_adjudica=pg_escape_string($_POST['res_adjudica']);
  $fecha_adjudica=pg_escape_string($_POST['fecha_adjudica']);

  $res_prorroga=pg_escape_string($_POST['res_prorroga']);
  $fecha_prorroga=pg_escape_string($_POST['fecha_prorroga']);
  
  $nro_res_prorroga=pg_escape_string($_POST['resp_apr_prorroga']);
  $fecha_resprorroga=pg_escape_string($_POST['fecha_nro_prorroga']);

  $res_aumento=pg_escape_string($_POST['res_aumento']);
  $fecha_aumento=pg_escape_string($_POST['fecha_aumento']);
  $monto_aumento=$_POST['monto_aumento']*1;

  $res_contrato=pg_escape_string($_POST['res_contrato']);
  $fecha_contrato=pg_escape_string($_POST['fecha_contrato']);
  
  $ap_aumento=pg_escape_string($_POST['t_aumento']);
  $f_aumento=pg_escape_string($_POST['b_aumento']);
  $fecha_inicio_convenio = $fecha_inicio; 
  if($fecha_inicio=='') $fecha_inicio='null'; else $fecha_inicio="'$fecha_inicio'";
  if($fecha_final=='') $fecha_final='null'; else $fecha_final="'$fecha_final'";
  if($fecha_contrato=='') $fecha_contrato='null'; else $fecha_contrato="'$fecha_contrato'";
  if($fecha_boleta=='') $fecha_boleta='null'; else $fecha_boleta="'$fecha_boleta'";
  if($fecha_aprueba=='') $fecha_aprueba='null'; else $fecha_aprueba="'$fecha_aprueba'";
  if($fecha_adjudica=='') $fecha_adjudica='null'; else $fecha_adjudica="'$fecha_adjudica'";
  if($fecha_prorroga=='') $fecha_prorroga='null'; else $fecha_prorroga="'$fecha_prorroga'";
  if($fecha_aumento=='') $fecha_aumento='null'; else $fecha_aumento="'$fecha_aumento'";
  if($sel_aprueba=='-1') $sel_aprueba=''; 
  if($f_aumento=='') $f_aumento='null'; else $f_aumento="'$f_aumento'";
  
  $tipo_garantia=$_POST['tipo_garantia']*1;
  $nro_boleta=pg_escape_string($_POST['nro_boleta']);
  $banco_boleta=pg_escape_string($_POST['banco_boleta']);
  $monto_boleta=pg_escape_string($_POST['monto_boleta']*1);
  
  $multa=pg_escape_string($_POST['multa']);
  $comenta=pg_escape_string($_POST['comenta']);
  $nombre_proveedor = pg_escape_string($_POST['nombre_proveedor']);
  

	if($fecha_resprorroga==''){
		$fecha_resprorroga='NULL';
	}else{
		$fecha_resprorroga="'$fecha_resprorroga'";
	}
  pg_query($conn, 'START TRANSACTION;');
  if($convenio_id==0) {
	 
	
	 	
	  pg_query($conn,
	  "
		  INSERT INTO convenio VALUES (
		  DEFAULT,
		  '$nombre_conv',
		  $prov_id, $monto, $plazo, '$mails', $fecha_inicio, $fecha_final,
		  '$licitacion_conv', 
		  '$res_aprueba',
		  '$res_adjudica',
		  '$res_contrato',$fecha_contrato,
		  $func_id,
		  '$nro_boleta',
		  '$banco_boleta',
		  $fecha_boleta,
		  $monto_boleta,
		  '$multa',
		  '$comenta',
		  $fecha_aprueba,
		  $fecha_adjudica,
		  '$res_prorroga', $fecha_prorroga,
		  '$res_aumento', $fecha_aumento,NULL,
		  '$categoria',
		  '$sel_aprueba',
		  $tipo_licitacion, 
		  $monto2, 
		  $plazo2, 
		  $tipo_garantia,
		   $monto_aumento,
		   '$nro_res_prorroga',
		   $fecha_resprorroga,
		   '$ap_aumento',
		   $f_aumento
		  );
	  ");
	  
	  
	  
	  /**
,
  convenio_contrato text,
  convenio_categoria text,
  convenio_aprueba text,
  convenio_tipo_licitacion smallint,
  convenio_monto2 bigint,
  convenio_plazo2 integer,
  convenio_tipo_garantia integer,
  convenio_monto_aumento bigint,
  convenio_nrores_prorroga text,
  convenio_fecha_resprorroga date,
  convenio_aumento_aprueba text,
  convenio_aumento_fecha date*/
  
   //echo $sel_aprueba;
   error_reporting(E_ALL);
   include('Mail.php');
   include('Mail/mime.php');
   
   $detalle_convenio="
		   Estimado/a;<BR/><BR/>
		   Con el objetivo de colaborar en su labor como administrador/a, adjuntamos documentaci&oacute;n del
		   convenio que se indica a continuaci&oacute;n: <br /><br />
		   <table style='width:80%;'>
		  <tr style='text-align:center;font-weight:bold;'>
	      <td bgcolor='#92cddc'>ID. CONVENIO:</td>
		  <td bgcolor='#daeef3'>$licitacion_conv</td>
		  </tr>
		  <tr style='text-align:center;font-weight:bold;'>
	      <td bgcolor='#92cddc'>NOMBRE CONVENIO:</td>
		  <td bgcolor='#daeef3'>$nombre_conv</td>
		  </tr>
		  <tr style='text-align:center;font-weight:bold;'>
		  <td bgcolor='#92cddc'>PROVEEDOR:</td>
		  <td bgcolor='#daeef3'>".strtoupper($nombre_proveedor)."</td>
		  </tr>
		  <tr style='text-align:center;font-weight:bold;'>
		  <td bgcolor='#92cddc'>VIGENTE DESDE:</td>
		  <td bgcolor='#daeef3'>".$fecha_inicio_convenio."</td>
		  </tr>";
		
	$html="	
		<center>
		$detalle_convenio
		<br /><br />
		Ante cualquier duda, comunicarse con el Departamento de Gesti&oacute;n de Convenios
		al anexo 281788 <BR/>
		o al correo electr&oacute;nico krasna.merino@redsalud.gov.cl.
		<BR/><BR/>
		Saludos cordiales;		 
		<BR/><BR/>
		<img 
		src='http://static.wixstatic.com/media/8b1dbc_9ad2884fa86c7a319679de2f52775ccc.jpg_srz_270_160_85_22_0.50_1.20_0.00_jpg_srz'
		style='width: 125px; height: 125px;'> 
		</center>	
		";
		
	  $id_c				=		pg_query($conn, "SELECT currval('convenio_convenio_id_seq1');");
      $id_con			=		pg_fetch_row($id_c);
      $convenio_id		=		$id_con[0];
      
/*
//echo "Convenio_id : ".$convenio_id;
function send_mail($to, $from, $subject, $body, $convenio_id) {
 
 $host = "190.107.177.206";
 $username = "hfbc@sistemasexpertos.cl";
 $password = "solucion1234";
 
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

send_mail('krasna.merino@redsalud.gov.cl,paula.uribe@redsalud.gov.cl,valeria.escobarp@redsalud.gov.cl,'.$mails.',miguel.quininao@redsalud.gov.cl','hfbc@sistemasexpertos.cl',utf8_decode("Ingreso de Nuevo Convenio - $licitacion_conv  ".date('d/m/Y')),$html,$convenio_id);

 */  
   $maxidcon = pg_query($conn,"SELECT MAX(convenio_id) as maxcon FROM convenio") or die (pg_last_error());
   while($row = pg_fetch_array($maxidcon)){ $maxcon = $row['maxcon'];}
   
	  if($sel_aprueba=='prorroga'){//echo $sel_aprueba;
			pg_query("INSERT INTO convenio_modificaciones VALUES(DEFAULT,$maxcon,'$sel_aprueba',CURRENT_TIMESTAMP,'$nro_res_prorroga',$fecha_resprorroga,NULL,$plazo2,$plazo,$func_id);")
					 or die(pg_last_error());
	   }
	  if($sel_aprueba=='aumento'){//echo $sel_aprueba;
	     	pg_query("INSERT INTO convenio_modificaciones VALUES(DEFAULT, $maxcon,'$sel_aprueba',CURRENT_TIMESTAMP,'$ap_aumento',$f_aumento,$monto_aumento,$plazo2,$plazo,$func_id);")
	  				 or die(pg_last_error());
	  }  
	  if($sel_aprueba=='contrato'){//echo $sel_aprueba;
			pg_query("INSERT INTO convenio_modificaciones VALUES(DEFAULT, $maxcon,'$sel_aprueba',CURRENT_TIMESTAMP,'$res_aprueba',$fecha_aprueba,$monto,$plazo2,$plazo,$func_id);")
	 				or die(pg_last_error());
	  }
		
	} else {
		
		pg_query("
		
		UPDATE convenio SET 
		convenio_nombre='$nombre_conv',
		prov_id=$prov_id,
		convenio_monto=$monto,
		convenio_plazo=$plazo,
		convenio_mails='$mails',
		convenio_fecha_inicio=$fecha_inicio,
		convenio_fecha_final=$fecha_final,
		convenio_licitacion='$licitacion_conv',
		convenio_nro_res_aprueba='$res_aprueba',
		convenio_fecha_aprueba=$fecha_aprueba,
		convenio_nro_res_adjudica='$res_adjudica',
		convenio_fecha_adjudica=$fecha_adjudica,
		convenio_nro_res_contrato='$res_contrato',
		convenio_fecha_resolucion=$fecha_contrato,
		func_id=$func_id,
		convenio_nro_boleta='$nro_boleta',
		convenio_banco_boleta='$banco_boleta',
		convenio_fecha_boleta=$fecha_boleta,
		convenio_monto_boleta=$monto_boleta,
		convenio_multa='$multa',
		convenio_comentarios='$comenta',
		convenio_nro_res_prorroga='$res_prorroga',
		convenio_fecha_prorroga=$fecha_prorroga,
		convenio_nro_res_aumento='$res_aumento',
		convenio_fecha_aumento=$fecha_aumento,
		convenio_categoria='$categoria',
		convenio_aprueba='$sel_aprueba',
		convenio_tipo_licitacion=$tipo_licitacion,
		convenio_monto2=$monto2,
		convenio_plazo2=$plazo2,
		convenio_tipo_garantia=$tipo_garantia,
		convenio_monto_aumento=$monto_aumento,
		convenio_nrores_prorroga='$nro_res_prorroga',
		convenio_fecha_resprorroga=$fecha_resprorroga,
		convenio_aumento_aprueba='$ap_aumento',
		convenio_aumento_fecha=$f_aumento
		WHERE convenio_id=$convenio_id		
		
		");
		
		if($sel_aprueba=='prorroga'){
			pg_query("INSERT INTO convenio_modificaciones VALUES(DEFAULT,$convenio_id,'$sel_aprueba',CURRENT_TIMESTAMP,'$nro_res_prorroga',$fecha_resprorroga,NULL,$plazo2,$plazo,$func_id);");
		}elseif($sel_aprueba=='aumento'){
			pg_query("INSERT INTO convenio_modificaciones VALUES(DEFAULT,$convenio_id,'$sel_aprueba',CURRENT_TIMESTAMP,'$ap_aumento',$f_aumento,$monto_aumento,$plazo2,$plazo,$func_id);");
		}elseif($sel_aprueba=='contrato'){
			pg_query("INSERT INTO convenio_modificaciones VALUES(DEFAULT,$convenio_id,'$sel_aprueba',CURRENT_TIMESTAMP,'$res_aprueba',$fecha_aprueba,$monto,$plazo2,$plazo,$func_id);");
		}
	}
  
  if($convenio_id == '0'){
		$id_c= pg_query($conn, "SELECT last_value FROM convenio_convenio_id_seq1;");
	    $id_conv=pg_fetch_row($id_c);
	    $id_convenio=$id_conv[0];
	    print('1,'.$id_convenio);	
	}else{
		print('1,'.$convenio_id);	
	}
	pg_query($conn, 'COMMIT;');

?>
