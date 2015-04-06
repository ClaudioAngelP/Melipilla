<?php 

	require_once('../../conectar_db.php');
	require_once('../../num2texto.php');
	require_once('../../fpdf/fpdf.php');
	
	function trunc($str, $len) {
		if(strlen($str)>$len)
			return substr($str,0,$len).'...';
		else 
			return $str;
	}

	$cerrado_id=$_GET['id']*1;
	 $func_id=$_SESSION['sgh_usuario_id']*1;
	
	
	$b=cargar_registro("SELECT * FROM cierre_prestaciones WHERE cierre_id=".$cerrado_id);
	$t=str_replace('|',',',$b['cierre_nomd_ids']);
	
	
	
	$f=cargar_registro("SELECT * FROM funcionario WHERE func_id=".$b['cierre_func_id']);
	$p=cargar_registros_obj("SELECT * FROM nomina_detalle join nomina using (nom_id) join pacientes using (pac_id) WHERE nomd_id in ($t)");
		
	
	
	$institucion=utf8_decode('CRS');
	$fecha=$b['cierre_fecha'];

	
	class PDF extends FPDF {
		function header() {

			GLOBAL $cerrado_id, $b,$f;

				
			$this->SetFont('Arial','BU', 18);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('../../imagenes/Logo_CRS.jpg',10,15,40,25);

			$this->Ln(10);
			
			$this->Cell(200,7,utf8_decode('Cierre de Prestaciones #'.number_format($cerrado_id,0,',','.')),0,0,'C');	
			$this->Ln(10);
			$this->SetFont('Arial','B', 10);
			
			
			$this->Cell(100,10,utf8_decode('Fecha Cierre:'),0,0,'R');
			$this->Cell(30,10,substr($b['cierre_fecha'],0,16),0,0,'C');
			$this->Ln(5);
			$this->Cell(140,10,'',0,0,'R');
			$this->Cell(50,10,$srt_corto,0,0,'R');	
			$this->Ln(10);
			$this->SetFont('Arial','BU', 18);

			
				
		$this->SetY(40);
		}


		function footer() {
		
			GLOBAL $b;

			$this->SetY(300);
				
			$this->SetFont('','',10);
			$this->Cell(200,6,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'C');
			
		}

	}	

		function footer() {

			GLOBAL $conf,$pdf, $b, $f, $c;

			
				$pdf->Ln(10);
			
				$pdf->SetFontSize(10);
				
				$pdf->Ln();
				$pdf->Ln();			
				$pdf->SetFontSize(10);			
			
				
		}
		
		function texto_fecha($str) {
		
			GLOBAL $vmes;
		
			$ff=explode('/',$str);
			
			return $ff[0].' de '.$vmes[$ff[1]*1].' del '.$ff[2];
		}
		
			
	
	$pdf=new PDF('P','mm','Legal');
	$pdf->AliasNbPages();
	
	$pdf->SetAutoPageBreak(true,60);
	
	$pdf->AddPage();
		
	$pdf->SetFillColor(200,200,200);	

	$pdf->SetFont('Arial','', 8);
	
	//$pdf->Cell(90,10,'Emitido por ',0,0,'R');
	//$pdf->Cell(50,10,$institucion,0,0,'C');
	$pdf->Ln(10);
	/*$pdf->Cell(140,10,'',0,0,'R');
	$pdf->Cell(30,10,utf8_decode('Fecha Emisión:'),0,0,'R');
	$pdf->Cell(30,10,substr($b['bolfec'],0,16),0,0,'C');
	$pdf->Ln();*/
	if($p)
		for($i=0;$i<sizeof($p);$i++) {
			
		
		$pdf->Cell(20,5,'Nomina ID:',1,0,'R');
		$pdf->Cell(20,5,$p[$i]['nom_folio'],1,0,'C');
		$pdf->Cell(20,5,'Paciente:',1,0,'R');
		$pdf->Cell(100,5,strtoupper($p[$i]['pac_nombres'].' '.$p[$i]['pac_appat'].' '.$p[$i]['pac_apmat']),1,0,'L');
		$pdf->Cell(20,5,utf8_decode('Prestacion:'),1,0,'R');
		$pdf->Cell(20,5,$p[$i]['nomd_codigo_presta'],1,0,'L');
		$pdf->Ln();
		}
	
		
	

	footer();

	
	$pdf->Output('BOLETIN_'.$cerrado_id.'.pdf','I');	

?>
