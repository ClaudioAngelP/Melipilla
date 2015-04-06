<?php 

	require_once('../../conectar_db.php');
	
	$cert_id=$_GET['cert_id']*1;
	
	$cert=cargar_registro("SELECT * FROM item_presupuestario_certificados 
	WHERE cert_id=$cert_id;");

	$d=cargar_registros_obj("SELECT *, (0) AS certd_disponible FROM item_presupuestario_certificados_detalle
								LEFT JOIN item_presupuestario_sigfe ON certd_item=item_codigo
								WHERE cert_id=$cert_id
								ORDER BY certd_item;");

	
	require_once('../../fpdf/fpdf.php');

	$pdf=new FPDF('P','mm','Legal');
	
	$pdf->AddPage();
		
	$pdf->SetFillColor(200,200,200);	

	$pdf->Image('logo_minsal.jpg',180,5,25,25);

	$pdf->SetFont('Arial','', 10);

	$pdf->Cell(190,5,'Ministerio de Salud',0,1,'L');
	$pdf->Cell(190,5,utf8_decode('Servicio de Salud Metropolitano Norte'),0,1,'L');
	$pdf->Cell(190,5,utf8_decode('Instituto Psiquiátrico José Horwitz Barak'),0,1,'L');
	$pdf->Ln();
	$pdf->Ln();

	$pdf->SetFont('Arial','', 18);

	$pdf->Cell(190,7,'Certificado de Compromiso Presupuestario',0,1,'C');
	$pdf->Ln();

	$pdf->SetFont('Arial','', 14);
	
	list($fecha)=explode(' ',$cert['cert_fecha']);	
	
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(95,7,utf8_decode('Nº FOLIO: ').$cert['cert_folio'],0,0,'L');

	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(95,7,utf8_decode('Fecha Emisión: ').$fecha,0,0,'R');
	$pdf->Ln();
	
	$monto='$'.number_format($cert['cert_monto'],0,',','.').'.-';
	$item_codigo=$cert['cert_item'];
	$item_nombre=$cert['item_glosa'];
	
	$pdf->SetFont('Arial','',10);
	
	$pdf->Multicell(190,7,utf8_decode("Mediante el presente documento, el Instituto Psiquiátrico José Horwitz Barak certifica con fecha $fecha que dispone del presupuesto de $monto para los siguientes items:"), 0, 'J');

	$pdf->Ln(5);

	$pdf->SetFont('Arial','B',12);

	$pdf->Cell(40,7,utf8_decode('Código'),1,0,'C');
	$pdf->Cell(110,7,utf8_decode('Descripción'),1,0,'C');
	$pdf->Cell(40,7,utf8_decode('Subtotal'),1,1,'C');

	$pdf->SetFont('Arial','',10);

	for($i=0;$i<sizeof($d);$i++) {
		
		$pdf->Cell(40,6,$d[$i]['certd_item'],1,0,'R');
		$pdf->Cell(110,6,$d[$i]['item_nombre'],1,0,'L');
		$pdf->Cell(40,6,'$ '.number_format($d[$i]['certd_monto'],0,',','.').'.-',1,1,'R');
		
	}
	

	$pdf->SetFont('Arial','B',14);

	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();

	$pdf->Cell(95,8,'______________________________',0,0,'C');
	$pdf->Cell(95,8,'______________________________',0,1,'C');
	$pdf->Cell(95,8,'FIRMA JEFE FINANZAS',0,0,'C');
	$pdf->Cell(95,8,'FIRMA S.D.A.',0,1,'C');



	
	if(false AND $cert['cert_resolucion']!='') {
		
	$pdf->AddPage();
		
	$pdf->SetFillColor(200,200,200);	

	$pdf->Image('logo_minsal.jpg',180,5,25,25);

	$pdf->SetFont('Arial','', 10);

	$pdf->Cell(190,5,'Ministerio de Salud',0,1,'L');
	$pdf->Cell(190,5,utf8_decode('Servicio de Salud Metropolitano Norte'),0,1,'L');
	$pdf->Cell(190,5,utf8_decode('Instituto Psiquiátrico José Horwitz Barak'),0,1,'L');
	$pdf->Ln();
	$pdf->Ln();

	$pdf->SetFont('Arial','', 18);

	$pdf->Cell(190,7,utf8_decode('RESOLUCIÓN EXENTA'),0,1,'C');
	$pdf->Ln();

	$pdf->SetFont('Arial','', 14);
	
	list($fecha)=explode(' ',$cert['cert_fecha_resolucion']);	
	
	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(95,7,utf8_decode('Nº FOLIO: ').$cert['cert_resolucion'],0,0,'L');

	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(95,7,utf8_decode('Fecha Emisión: ').$fecha,0,0,'R');
	$pdf->Ln();

	$pdf->Ln();

	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(95,7,utf8_decode('VISTOS: '),0,1,'L');
	$pdf->Ln();
	$pdf->SetFont('Arial','',11);
	
	$pdf->Multicell(190,7,utf8_decode("Lo dispuesto en el DFL Nº 1/19.653, del Ministerio Secretaría General de la Presidencia que fija el texto refundido, coordinado y sistematizado de la Ley Nº 18.575 Orgánica Constitucional de Bases Generales de la Administración del Estado; la Ley Nº 19.886 de Bases sobre Contratos Administrativos de Suministros y Prestaciones de Servicios y su Reglamento contenido en el Decreto Supremo de Hacienda Nº 250/2004 y sus modificaciones; el DFL Nº 1/2005 del Ministerio de Salud, que fija el texto refundido, coordinado y sistematizado del DL. Nº 2763 de 1979 y de las Leyes Nº 18.933 y Nº 18.469; el Decreto 38/2005 del Ministerio de Salud; Reglamento Orgánico de los Establecimientos de Salud de Menor Complejidad y de los Establecimientos de Autogestión en Red; la Resolución Exenta Bº 1172/2007 del Ministerio de Salud y Ministerio de Hacienda que otorga la calidad de Establecimiento de Autogestión en Red al Instituto Psiquiátrico 'Dr. José Horwitz Barak'; la Resolución Nº 117 de 2011 del Servicio de Salud Metropolitano Norte, que designa cargo de Director del Instituto Psiquiátrico a D. Enrique Cancec Iturra; ").$cert['cert_vistos'].utf8_decode(" , emitido por el Jefe del Departamento de Finanzas del Instituto; y, la Resolución Nº 1600/2008 de la Contraloría General de la República que establece normas sobre extensión de Trámite de Toma de Razón;"), 0, 'J');
	$pdf->Ln();

	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(95,7,utf8_decode('CONSIDERANDO: '),0,1,'L');
	$pdf->Ln();
	$pdf->SetFont('Arial','',11);
	
	$pdf->Multicell(190,7,$cert['cert_considerando'], 0, 'J');
	$pdf->Ln();

	$pdf->SetFont('Arial','B',14);
	$pdf->Cell(95,7,utf8_decode('RESUELVO: '),0,1,'L');
	$pdf->Ln();
	$pdf->SetFont('Arial','',11);

	$pdf->Multicell(190,7,utf8_decode(str_replace("<br>","\n","
1. APRUEBASE el trato directo con la Empresa Sistemas Expertos e Ingeniería de Software Limitada RUT Nº 76.132.093-9 para la contratación de los servicios de 'ARRIENDO DE SISTEMA INFORMATICO AREAS DE ABASTECIMIENTO, FARMACIAS, CONTROL PRESUPUESTARIO, CONTROL DE CONTRATOS Y RECAUDACION Y MONITOREO  GES DEL INSTITUTO PSIQUIATRICO 'DR. JOSE HORWITZ BARAK'.<br>
2. AUTORIZASE el pago por concepto de contratación de los servicios de arriendo de sistema informático de farmacias del Instituto Psiquiátrico 'Dr. José Horwitz Barak' a la Empresa Sistemas Expertos e Ingeniería de Software Limitada, RUT Nº 76.132.093-9 por la suma de $ 28.000.000.- más IVA, cantidad que será pagada en 6 cuotas y se pagará 30 días después de la presentación de la Factura en la Oficina de Partes del Instituto Psiquiátrico, Avda. La Paz 841, Recoleta, adjuntando la Orden de Compra correspondiente.<br>
3. IMPUTESE el gasto que irrogue la presente contratación al Item 22-12-999-014 del Presupuesto del Instituto Psiquiátrico vigente.<br>
4. PUBLIQUESE la presente Resolución en el Sistema de Información de Compras y Contrataciones Públicas, dentro del plazo de 24 horas contadas desde la fecha del presente instrumento, en el portal de www.mercadopublico.cl, de conformidad a lo dispuestos en el Art. 50 de la Ley Nº 19.886.<br>
")), 0, 'J');
	$pdf->Ln();

	$pdf->SetFont('Arial','B',12);

	$pdf->Multicell(190,7,utf8_decode("
ANOTESE, COMUNIQUESE Y PUBLIQUESE EN EL SISTEMA DE
INFORMACION DE CONTRATACION PUBLICA


DR. ENRIQUE CANCEC ITURRA
DIRECTOR
INSTITUTO PSIQUIATRICO 'DR. JOSE HORWITZ BARAK'


TRANSCRITO FIELMENTE
MINISTRO DE FE
"), 0, 'C');

	$pdf->SetFont('Arial','',10);

	$pdf->Multicell(190,7,utf8_decode("DISTRIBUCION:
1.DIRECCION
2.SUBDIRECCION ADMINISTRATIVA
3.DEPTO. ABASTECIMIENTO
4.DEPTO. FINANZAS
5.ABOGADO ASESOR
6.OF. DE PARTES.
"), 0, 'L');


	$pdf->Ln();
	
		
	}






	$pdf->Output('COMPROMISO_PRESUP_'.$cert['cert_folio'].'.pdf','I');	


?>
