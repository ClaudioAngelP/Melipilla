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

	$orden_id=$_GET['orden_id']*1;
	 $func_id=$_SESSION['sgh_usuario_id']*1;
	
	
	$b=cargar_registro("SELECT * FROM orden_compra WHERE orden_id=".$orden_id);
	$f=cargar_registro("SELECT * FROM funcionario WHERE func_id=".$func_id);
		
	
	$p=cargar_registros_obj("SELECT * FROM orden_detalle WHERE ordetalle_orden_id=$orden_id");

	
	$institucion=utf8_decode('INSTITUTO PSIQUIATRICO');
	$fecha=$b['orden_fecha'];

	
	class PDF extends FPDF {
		function header() {

			GLOBAL $orden_id, $b,$f;

			$this->SetFont('Arial','B', 10);

			//$this->Image('../../imagenes/psi.jpg',10,15,25);
			
			$srt_corto='';
			
			
			$this->Cell(140,10,'',0,0,'R');
			$this->Cell(30,10,utf8_decode('Fecha Emisión:'),0,0,'R');
			$this->Cell(30,10,substr($b['bolfec'],0,16),0,0,'C');
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
				$pdf->SetFont('','BU');	
				$pdf->Cell(80,6,$srt_corto,0,0,'C');	
				$pdf->Cell(60,6,'ss',0,0,'C');
                                //trim()$c['pac_nombres'].' '.$c['pac_appat']
				$pdf->Cell(55,6,'______________________',0,0,'C');
				$pdf->Ln();	
				$pdf->SetFont('','');	
				$pdf->Cell(80,6,'Funcionario Emisor',0,0,'C');	
				
				$pdf->Cell(65,6,utf8_decode('Jefe de Recaudación'),0,0,'C');
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
	
	$pdf->SetAutoPageBreak(true,20);
	
	$pdf->AddPage();
		
	$pdf->SetFillColor(200,200,200);	

	$pdf->SetFont('Arial','', 10);
	
	//$pdf->Cell(90,10,'Emitido por ',0,0,'R');
	//$pdf->Cell(50,10,$institucion,0,0,'C');
	$pdf->Ln(10);
	/*$pdf->Cell(140,10,'',0,0,'R');
	$pdf->Cell(30,10,utf8_decode('Fecha Emisión:'),0,0,'R');
	$pdf->Cell(30,10,substr($b['bolfec'],0,16),0,0,'C');
	$pdf->Ln();*/


	$pdf->SetFillColor(130,130,130);
	
	
	$pdf->SetFillColor(200,200,200);
	
		
	
		
	

	footer();

	
	$pdf->Output('BOLETIN_'.$orden_id.'.pdf','I');	

?>
