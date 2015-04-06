<?php
 
	require_once('../../config.php');
	require_once('../../conectores/sigh.php');
	require_once('../../fpdf/fpdf.php');

	$nomd_id=$_GET['nomd_id']*1;
		
  $lista = cargar_registros_obj("
  SELECT 
	nomina_detalle.nomd_id, nom_fecha::date, nomd_hora, 
	doc_rut, doc_paterno, doc_materno, doc_nombres, 
	COALESCE(diag_desc, cancela_desc) AS diag_desc, 
	nomd_diag_cod,
	esp_desc, nomd_tipo, 
	CASE WHEN nom_fecha>=CURRENT_DATE THEN 'P' ELSE 'A' END AS estado,
	nomd_codigo_presta, glosa, esp_lugar, COALESCE(esp_nombre_especialidad, esp_desc) AS esp_nombre_especialidad,
	nomina_detalle.id_sidra,*
  FROM nomina_detalle
  JOIN nomina USING (nom_id)
  JOIN pacientes USING (pac_id)
  LEFT JOIN comunas USING (ciud_id)
  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
  LEFT JOIN doctores ON nom_doc_id=doc_id
  LEFT JOIN especialidades ON nom_esp_id=esp_id 
  LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
  LEFT JOIN codigos_prestacion ON nomd_codigo_presta=codigo
  WHERE nomd_id=$nomd_id
  ORDER BY nomina.nom_fecha ASC, nomd_hora 
  ", false);


	$pac_id=$lista[0]['pac_id']*1;

	$pac = cargar_registro("SELECT *, COALESCE(pac_clave, md5(substr(md5(pac_id::text),1,5))) AS pac_clave FROM pacientes 
							LEFT JOIN comunas USING (ciud_id)
							LEFT JOIN prevision USING (prev_id)
							WHERE pac_id=$pac_id ", false);

	
	if(strlen($pac['prev_desc'])==1) {
		$pac['prev_desc']='FONASA - GRUPO '.$pac['prev_desc'];
	}
  
  
  
  
  	class PDF extends FPDF {
		function header() {

			$this->SetFont('Arial','B', 16);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('logo_min.jpg',10,10,45,28);

			//$this->Ln(20);
			$this->SetX(50);
			$this->Cell(150,7,('Ministerio de Salud'),0,0,'L');	
			$this->Ln();
			$this->SetX(50);
			$this->Cell(150,7,('SS Metropolitano Norte'),0,0,'L');	
			$this->Ln();
			$this->SetX(50);
			$this->Cell(150,7,('Instituto Psiquiátrico Dr. José Horwitz Barak'),0,0,'L');	
			$this->Ln();
			$this->SetX(50);
			$this->Cell(150,7,('Admisión Atención Abierta'),0,0,'L');	
			$this->Ln();
		
			$this->SetFontSize(14);		
			$this->SetY(40);	
		
		}

		function footer() {

			$this->SetY(230);
			
			$this->SetFont('','B',14);
			$this->Cell(190,6,('NOTA:'),0,0,'L');
			$this->Ln();
		
			$this->SetFont('','',12);
			$this->Multicell(190,5,str_replace('<br>',"\n",str_replace("\n",'',("
Presentar citación, credencial, y cédula de identidad el día de la atención.<br>
Beneficiarios de FONASA Grupo C y D, deben pasar por recaudación. Si por algún motivo no pudiese asistir dar aviso al número telefónico del establecimiento.<br>
Avise de inmediato cambios de dirección o teléfono.<br><br>"))));

			$this->SetFont('','B',14);

			$this->Cell(190,6,('Página '.$this->PageNo().' de {nb}'),0,0,'C');
			
		}

	}	


	$pdf=new PDF('P','mm','Letter');
	
	$pdf->AliasNbPages();
	
	//$pdf->SetAutoPageBreak(true,20);
	
	//$pdf->AddPage();
		
	if($lista)
	for($i=0;$i<count($lista);$i++) {
		
	//$lista[$i]['esp_desc']=str_replace('-HDGF', '', $lista[$i]['esp_desc']);

	$prof=strtoupper($lista[$i]['doc_nombres'].' '.$lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno']);

	$prof=str_replace('(AGEN)', '', $prof);

	if($lista[$i]['nomd_hora']=='00:00:00') $lista[$i]['nomd_hora']='08:30:00';
	
	if($i%2==0) $pdf->AddPage();

	$pdf->SetFillColor(200,200,200);	

	$pdf->SetFont('Arial','', 14);

	$pdf->SetFillColor(130,130,130);

	$pdf->Cell(200,7,('COMPROBANTE DE CITACIÓN'),1,0,'C',1);
	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Citado para el:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,substr($lista[$i]['nom_fecha'],0,10).' A LAS '.substr($lista[$i]['nomd_hora'],0,5).' HORAS.',1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Profesional:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$prof,1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Programa:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$lista[$i]['esp_desc'],1,0,'L',1);	
	$pdf->Ln();

	$lugar=$lista[$i]['esp_lugar'];
	
	if(strlen($lugar)>70) {
		$lugar=substr($lugar,1,67).'...';
	}

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Lugar:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','', 12);
	$pdf->Cell(160,7,$lugar,1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFont('Arial','', 14);
	
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Paciente:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'],1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'RUN:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$pac['pac_rut'],1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Teléfonos:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$pac['pac_fono'].' / '.$pac['pac_celular'],1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Dirección:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,strtoupper($pac['pac_direccion'].' '.$pac['ciud_desc']),1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Ficha Clínica:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(50,7,$pac['pac_ficha'],1,0,'L',1);	
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(50,7,'Previsión:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(60,7,$pac['prev_desc'],1,0,'L',1);	
	$pdf->Ln();
	
	$glosa=$lista[$i]['glosa'];
	
	if(strlen($glosa)>50) {
		$glosa=substr($glosa,1,47).'...';
	}
	
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Prestación:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('','B',12);
	$pdf->Cell(30,7,$lista[$i]['nomd_codigo_presta'],1,0,'C',1);	
	$pdf->SetFont('','',10);
	$pdf->Cell(130,7,$glosa,1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFont('','',8);

	$pdf->SetFillColor(150,150,150);
	$pdf->Cell(130,5,'Código Autenticidad:',1,0,'R',1);	
	$pdf->SetFillColor(180,180,180);
	$pdf->SetFont('','B',8);
	$pdf->Cell(70,5,md5($lista[$i]['nomd_id'].''.$lista[$i]['id_sidra']),1,0,'C',1);	
	$pdf->Ln();

	$pdf->SetFont('','',14);

	$pdf->Ln();	
		
	}

	$pdf->Output('CITACION_'.strtoupper(trim($nomd_id)).'.pdf','I');	

?>
