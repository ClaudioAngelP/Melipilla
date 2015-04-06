<?php
 
	require_once('../../config.php');
	require_once('../../conectores/sigh.php');
	require_once('../../fpdf/fpdf.php');

	$busca=pg_escape_string(utf8_decode($_GET['rut']));

	$busca=str_replace('.','',trim($busca));
	$pac_w="pac_rut='$busca'";	

    $pac = cargar_registro("SELECT *, substr(md5(pac_id::text),1,5) AS _clave FROM pacientes 
							LEFT JOIN comunas USING (ciud_id)
							LEFT JOIN prevision USING (prev_id)
							WHERE $pac_w ", false);

	if(!$pac) {
		exit("
			<script>
				alert('Paciente no encontrado.');
			</script>		
		");	
	}
	
	pg_query("UPDATE pacientes SET pac_clave=md5(substr(md5(pac_id::text),1,5)) WHERE pac_id=".$pac['pac_id']);
	
	if(strlen($pac['prev_desc'])==1) {
		$pac['prev_desc']='FONASA - GRUPO '.$pac['prev_desc'];
	}

	if(trim($pac['pac_mail'])=='') {
		$pac['pac_mail']='<i>(No Especificado...)</i>';
	}
	  
  	class PDF extends FPDF {
		function header() {

			$this->SetFont('Arial','B', 14);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('logo_min.jpg',10,8,45,28);

			//$this->Ln(20);
			$this->SetX(50);
			$this->Cell(150,6,('Ministerio de Salud'),0,0,'L');	
			$this->Ln();
			$this->SetX(50);
			$this->Cell(150,6,('SS Viña del Mar Quillota'),0,0,'L');	
			$this->Ln();
			$this->SetX(50);
			$this->Cell(150,6,('Hospital Dr. Gustavo Fricke Viña del Mar'),0,0,'L');	
			$this->Ln();
			$this->SetX(50);
			$this->Cell(150,6,('Admisión Atención Abierta'),0,0,'L');	
			$this->Ln();
		
			$this->SetFontSize(14);		
			$this->SetY(55);	
		
		}

	}	


	$pdf=new PDF('P','mm','Letter');
	
	$pdf->AliasNbPages();
	
	$pdf->SetAutoPageBreak(true,20);
	
	$pdf->AddPage();

	$pdf->SetFont('','B',14);
	
	$pdf->Multicell(190,7,str_replace('<br>',"\n",str_replace("\n",'',("Le damos la bienvenida a nuestro Sistema de Consulta de Horas por Internet, para ingresar, debe acceder al sitio web del Hospital Dr. Gustavo Fricke (www.hospitalfricke.cl), hacer click en el banner \"Consulte su Hora en Línea\", donde deberá ingresar los siguientes datos de acceso:"))));
	
	$pdf->Ln(20);
	
	$pdf->SetFont('','B',18);
	$pdf->Cell(80,8,'RUT:',0,0,'R');
	$pdf->SetFont('','BU',18);
	$pdf->Cell(110,8,$pac['pac_rut'],0,0,'L');
	$pdf->Ln();
	$pdf->SetFont('','B',18);
	$pdf->Cell(80,8,'Clave:',0,0,'R');
	$pdf->SetFont('','BU',18);
	$pdf->Cell(110,8,$pac['_clave'],0,0,'L');
	$pdf->Ln(20);

	$pdf->SetFont('','B',14);
	
	$pdf->Multicell(190,7,str_replace('<br>',"\n",str_replace("\n",'',("Luego, haga click en \"Consultar\", se le mostraran (sí existen) sus futuras Horas Médicas para asistir al Consultorio de Atención de Especialidades (CAE) del hospital."))));
	
	$pdf->Ln(60);

	$pdf->SetFont('','B',10);

	$pdf->Cell(190,6,'CLAUSULA DE RESPONSABILIDAD',0,0,'C');

	$pdf->Ln();

	$pdf->Multicell(190,6,str_replace('<br>',"\n",str_replace("\n",'',("Para garantizar la identidad del paciente que accede al sitio Web y posibilitar el uso de la consulta de Citaciones Médicas en Internet, existe el uso clave de acceso.<br>El uso de la información obtenida a través de Citaciones Médicas del sitio Web del hospital, es de exclusiva responsabilidad del Paciente que solicitó la clave, y su mal uso no imputable al Hospital."))));
	
	$pdf->Output('CARTA_CLAVE_'.strtoupper(trim($rut)).'.pdf','I');	

?>
