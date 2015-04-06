<?php

  require_once('../../conectar_db.php');
  require_once('../../num2texto.php');
	require_once('../../fpdf/fpdf.php');

  $orden_id = $_GET['oc_id'];

	$institucion=utf8_decode('CRS PEÑALOLÉN');
	
	
	$d=cargar_registro("SELECT * FROM orden_compra WHERE orden_id=".$orden_id);
	

	
	class PDF extends FPDF {
		function header() {

			GLOBAL  $d;

			$this->SetFont('Arial','BU', 18);

			$this->Image('../../imagenes/Logo_CRS.jpg',10,15,40,25);

			$this->Ln(10);
			if($b['anulacion']=='') {
				
			} else {
				
				$this->Cell(200,10,utf8_decode('ANULACIÓN'),0,1,'C');
						
			}

			if($b['pagare']=='t'){
				$this->Cell(200,10,utf8_decode('RESTITUCIÓN DE DATOS #'.number_format($d['bolnum'],0,',','.')),0,0,'C');	
			}else{
				
				if($b['garantia']=='t')
					$this->Cell(200,10,utf8_decode('RESTITUCIÓN DE DATOS #'.number_format($d['devol_id'],0,',','.')),0,0,'C');	
				else
					$this->Cell(200,7,utf8_decode('RESTITUCIÓN DE DATOS #'.number_format($d['devol_id'],0,',','.')),0,0,'C');	
			
			}
			
			
			
			
					
				$this->SetFont('Arial','',14);
				$this->Ln(7);
				$this->Cell(200,7,utf8_decode('Número de boletín asociado #'.number_format($b['bolnum'],0,',','.')),0,0,'C');	
				
				$this->SetFont('Arial','BU',18);
			
			

			if($b['anulacion']!='')	{
				$this->SetFont('Arial','BU',12);
				$this->Ln(15);
				
				$this->Cell(200,7,utf8_decode('Motivo Anulación: ').$b['anulacion'],0,0,'C');	
			}
			
			$this->SetFontSize(10);	
			if($b['anulacion']!='')	{
				$this->SetY(40);
			}
			else {
			$this->SetY(20);
			}	
				
		
		}

		function footer() {
		
			GLOBAL $b,$pag;
			$offset=(185+10*sizeof($pag));
			if($b['pagare']!='t')
				$this->SetY($offset);
			
			else
				$this->SetY(300);
				
			$this->SetFont('','',10);
			$this->Cell(200,6,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'C');
			
		}

	}	

		function footer() {

			GLOBAL $conf,$pdf, $b, $f, $c,$pag;
			
			$offset=(170+10*sizeof($pag));
			if($b['pagare']!='t')
				$pdf->SetY($offset);
			else
				$pdf->Ln(10);

			$t=explode('|',$b['datos_pagare']);
			
			if($b['anulacion']=='') {
				$pdf->SetFontSize(12);
				$pdf->SetFont('','B');	
				$pdf->Cell(60,6,$f['func_nombre'],0,0,'C');	
				if($b['pagare']=='f') $pdf->Cell(150,6,trim($c['pac_nombres'].' '.$c['pac_appat']),0,0,'C');
				else $pdf->Cell(150,6,strtoupper($t[1]),0,0,'C');
				$pdf->Ln();	
				$pdf->SetFont('','');	
				$pdf->Cell(60,6,'Funcionario Emisor',0,0,'C');	
				if($b['pagare']=='f') $pdf->Cell(150,6,'Paciente',0,0,'C');
				else $pdf->Cell(150,6,'Titular',0,0,'C');
				$pdf->Ln();
				$pdf->Ln();			
				$pdf->SetFontSize(10);
			} else {
				$pdf->SetFontSize(10);
				$pdf->SetFont('','BU');	
				$pdf->Cell(60,6,$f['func_nombre'],0,0,'C');	
				if($b['pagare']=='f') $pdf->Cell(80,6,trim($c['pac_nombres'].' '.$c['pac_appat']),0,0,'C');
                                else $pdf->Cell(0,6,strtoupper($t[1]),0,0,'C');
				$pdf->Cell(55,6,'______________________',0,0,'C');
				$pdf->Ln();	
				$pdf->SetFont('','');	
				$pdf->Cell(60,6,'Funcionario Emisor',0,0,'C');	
				if($b['pagare']=='f') $pdf->Cell(70,6,'Paciente',0,0,'C');
                                else $pdf->Cell(70,6,'Titular',0,0,'C');
				$pdf->Cell(75,6,utf8_decode('Jefe de Recaudación'),0,0,'C');
				$pdf->Ln();
				$pdf->Ln();			
				$pdf->SetFontSize(10);			
			}
				
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
	$pdf->Cell(140,10,'',0,0,'R');
	$pdf->Cell(30,10,utf8_decode('Fecha Emisión:'),0,0,'R');
	if($valor==false)
	{
			$pdf->Cell(30,10,substr($d['devfec'],0,16),0,0,'C');
		
	}else{
			$pdf->Cell(30,10,substr($d['dev_ejecuta'],0,16),0,0,'C');
		
	}
	$pdf->Ln();


	$pdf->SetFillColor(130,130,130);
	
	$trad['mai']='INSTITUCIONAL';
	$trad['mle']='PARTICULAR';

	$pdf->SetFont('Arial','B', 13);
	$pdf->Ln(2);
	
	
	$pdf->SetFont('Arial','B', 10);
	if($b['garantia']=='t'){
	$pdf->Cell(200,5,utf8_decode('Datos del Paciente'),1,0,'C');
	}
	else {
	$pdf->Cell(130,5,utf8_decode('Datos del Paciente'),1,0,'C');
	$pdf->Cell(70,5,utf8_decode('Modalidad Atención: ').$trad[$b['bolmod']],1,0,'C');
	$pdf->SetFont('Arial','', 10);
	 }
	$pdf->SetFont('Arial','', 10);
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	
		
	if($b['garantia']=='t'){
	$pdf->Cell(40,5,'R.U.T. Paciente',1,0,'C');	
	$pdf->Cell(160,5,'Nombre Paciente',1,0,'C');
	}
	else {
	$pdf->Cell(40,5,'R.U.T. Paciente',1,0,'C');	
	$pdf->Cell(120,5,'Nombre Paciente',1,0,'C');
	$pdf->Cell(40,5,utf8_decode('Previsión'),1,0,'C');	
	}
	$pdf->Ln();
	
	$pdf->Cell(40,5,$c['pac_rut'],1,0,'C');
	
	if($b['garantia']=='t'){
	$pdf->Cell(160,5,strtoupper($c['pac_nombres'].' '.$c['pac_appat'].' '.$c['pac_apmat']),1,0,'L');
	$pdf->Ln();
	$pdf->Cell(40,5,'R.U.T. Titular',1,0,'C');	
	$pdf->Cell(160,5,'Nombre Titular',1,0,'C');
	$pdf->Ln();
	$pdf->Cell(40,5,$c['pac_rut'],1,0,'C');
	$pdf->Cell(160,5,strtoupper($c['pac_nombres'].' '.$c['pac_appat'].' '.$c['pac_apmat']),1,0,'L');
	}
	else {
	$pdf->Cell(120,5,strtoupper($c['pac_nombres'].' '.$c['pac_appat'].' '.$c['pac_apmat']),1,0,'L');	
	$pdf->Cell(40,5,$b['prvdesc'],1,0,'C');
	}
	$pdf->Ln();
	
	/*$pdf->Cell(200,5,utf8_decode('Dirección'),1,0,'C');
	$pdf->Ln();
	$pdf->Cell(200,5,utf8_decode($c['pac_direccion']),1,0,'L');
	$pdf->Ln();	
	
	$pdf->Cell(120,5,'Comuna',1,0,'C');
	$pdf->Cell(80,5,utf8_decode('Teléfono'),1,0,'C');
	$pdf->Ln();	
	$pdf->Cell(120,5,($c['ciud_desc']),1,0,'L');
	$pdf->Cell(80,5,($c['pac_fono']),1,0,'L');
	$pdf->Ln();*/


	$pdf->SetFontSize(10);	

	$pdf->SetFont('Arial','');

	if($cr) {

	$pdf->Ln();

	$pdf->SetFillColor(130,130,130);

	$pdf->SetFont('Arial','B', 13);
	
	if($b['pagare']=='t') {
		$pdf->Cell(200,5,utf8_decode('Detalle del Pagaré'),1,0,'C');
		$pdf->Ln();
		$t=explode('|',$b['datos_pagare']);
		$pdf->SetFillColor(200,200,200);
		$pdf->Cell(40,5,'R.U.N. Titular:',1,0,'R');
		$pdf->Cell(160,5,$t[0],1,1,'L');
		$pdf->Cell(40,5,'Nombre Titular:',1,0,'R');
		$pdf->Cell(160,5,strtoupper($t[1]),1,1,'L');
		$pdf->Cell(40,5,utf8_decode('Dirección:'),1,0,'R');
		$pdf->Cell(160,5,$t[2],1,1,'L');
		$pdf->Cell(40,5,utf8_decode('Teléfono(s):'),1,0,'R');
		$pdf->Cell(160,5,$t[3],1,0,'L');
		
	} else
		if($b['garantia']=='t'){

			//$pdf->Cell(200,5,utf8_decode('Detalle de la Garantia'),1,0,'C');	
			//lo de abajo busca fechas para poner que dia vence la garantia
			$ttmp=cargar_registro("SELECT * FROM creditos WHERE crecod=$crecod");
			$fecha_garantia=$ttmp['bolfec'];
		}
		else {
			$pdf->Cell(200,5,utf8_decode('Detalle del Crédito'),1,0,'C');		
		
		
	
	$pdf->Ln();

	$pdf->SetFont('Arial','', 10);
	
	$pdf->SetFillColor(200,200,200);
	
	$pdf->Cell(40,5,utf8_decode('Total a Pagar'),1,0,'C');	
	$pdf->Cell(30,5,utf8_decode('Total Abono'),1,0,'C');	
	
	if($b['pagare']=='t')
		$pdf->Cell(30,5,utf8_decode('Total Pagaré'),1,0,'C');	
	else
		$pdf->Cell(30,5,utf8_decode('Total Crédito'),1,0,'C');	
		
	
	
	//$pdf->Cell(25,5,utf8_decode('Día de Pago'),1,0,'C');	
	if($b['pagare']!='t') {
		$pdf->Cell(25,5,'Cuota',1,0,'C');	
		$pdf->Cell(75,5,'Valor Cuotas',1,0,'C');	
	} else {
		$pdf->Cell(100,5,'Fecha de Vencimiento',1,0,'C');		
	}
	$pdf->Ln();	

	$pdf->Cell(40,5,'$'.number_format(($cr['crepie']*1+$cr['cretot']*1),0,',','.').'.-',1,0,'R');	
	$pdf->Cell(30,5,'$'.number_format($cr['crepie'],0,',','.').'.-',1,0,'R');	
	$pdf->Cell(30,5,'$'.number_format($cr['cretot'],0,',','.').'.-',1,0,'R');	
	//$pdf->Cell(25,5,number_format($f_pago,0,',','.').'',1,0,'C');

	$pdf->SetFont('Arial','B', 12);
	
	if($b['pagare']!='t') {

		$cuota_actual=floor(($cr['monto_pagado']*1)/(($cr['cresal']*1)/($cr['cuonro']*1)));
	
		$pdf->Cell(25,5,$cuota_actual.' / '.$cr['cuonro'].'',1,0,'C');	
		$pdf->Cell(75,5,'$'.number_format($cr['crevalcuo'],0,',','.').'.-',1,0,'R');	
	
	} else {

		$ttmp=cargar_registro("SELECT * FROM cuotas WHERE crecod=$crecod AND cuonum=1");
		
		$fecha_pagare=substr($ttmp['cuofec'],0,10);
	
		$pdf->Cell(100,5,$fecha_pagare,1,0,'C');	
	
	}
	}
	$pdf->SetFont('Arial','', 10);
	$pdf->Ln();
	
	
	}		

	//$pdf->Ln();


	$pdf->SetFontSize(10);	

	$pdf->SetFillColor(130,130,130);

	$pdf->Ln();
	
	if($b['pagare']=='t' AND $b['anulacion']=='') {
	
		$pdf->SetFontSize(10);	

		$pdf->Cell(200,6,utf8_decode('Debo y pagaré a la orden de CENTRO DE REFERENCIA DE SALUD por atención de:'),0,1,'L');

		$pdf->Ln();
		
		$pdf->SetFontSize(10);	
	
	}


	$pdf->SetFont('Arial','B', 12);
	$pdf->Cell(200,5,utf8_decode('Detalle de Devoluciones'),1,1,'C');
	$pdf->SetFont('Arial','', 10);
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(25,5,utf8_decode('Fecha y Hora'),1,0,'C');
	$pdf->Cell(20,5,utf8_decode('Código'),1,0,'C');
	$pdf->Cell(90,5,utf8_decode('Descripción'),1,0,'C');
	$pdf->Cell(5,5,utf8_decode('#'),1,0,'C');
	$pdf->Cell(30,5,'Valor ($)',1,0,'C');
	$pdf->Cell(30,5,'Devolucion ($)',1,0,'C');
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	
	$totalt=0; $totalb=0;	

	//if(!$paga_cuota) {
	
		if($p)
		for($i=0;$i<sizeof($p);$i++) {
	
			$pdf->SetFontSize(8);
			$pdf->SetFont('','');				
			$pdf->Cell(25,5,substr(trim($p[$i]['bdet_fecha']),0,16),1,0,'C');
			$pdf->SetFontSize(8);
			$pdf->SetFont('','B');				
			$pdf->Cell(20,5,trim($p[$i]['bdet_codigo']),1,0,'C');
			$pdf->SetFont('','');				
			$pdf->SetFontSize(8);
			$pdf->Cell(90,5,trunc(trim($p[$i]['bdet_prod_nombre']),45),1,0,'L');
			$pdf->Cell(5,5,trim($p[$i]['bdet_cantidad']),1,0,'C');
			$pdf->SetFontSize(10);
			
			if(!$paga_cuota) {
				$pdf->Cell(30,5,'$ '.number_format($p[$i]['bdet_valor_total']*$p[$i]['bdet_cantidad'],0,',','.').'.-',1,0,'R');
				if($p[$i]['bdet_cobro']=='S') {
					if($b['garantia']=='t'){
						$pdf->Cell(30,5,'$ '.number_format($p[$i]['bdet_valor_total']*$p[$i]['bdet_cantidad'],0,',','.').'.-',1,0,'R');
					}
				else {
					$pdf->Cell(30,5,'$ '.number_format($pd[$i]['monto_dev'],0,',','.').'.-',1,0,'R');
				}
					
				} else {
					$pdf->Cell(30,5,'$ '.number_format(0,0,',','.').'.-',1,0,'R');			
				}
				$pdf->SetFontSize(8);
				$pdf->Ln();
				
				if($p[$i]['bdet_cobro']=='S') {
					
						$totalt+=round($p[$i]['bdet_valor_total']*$p[$i]['bdet_cantidad']);
						$totalb+=round($pd[$i]['monto_dev']);
					
					
				}
			} else {

				$pdf->Cell(30,5,'',1,0,'R');			
				$pdf->Cell(30,5,'',1,0,'R');			
				$pdf->SetFontSize(8);
				$pdf->Ln();
			
			}
			
			if((($i==10 OR (($i-10)%27)==0)) AND $i<sizeof($p)-1) {
				$pdf->AddPage();
			}
	
		}
	
	//} 

	if($paga_cuota AND $b['anulacion']=='') {


				$saldo=$cr['crepie']*1+$cr['cretot']*1;
				
				for($k=0;$k<sizeof($bcr);$k++)
					$saldo-=$bcr[$k]['bolmon'];

				$pdf->Cell(25,5,'*',1,0,'C');

				if($saldo>0) 
					$pdf->Cell(115,5,utf8_decode('Paga Cuota(s) Crédito #'.$cr['crecod']),1,0,'L');
				else
					$pdf->Cell(115,5,utf8_decode('Concluye Pago del Crédito #'.$cr['crecod']),1,0,'L');
				
				$pdf->SetFontSize(12);
				$pdf->Cell(60,5,'$ '.number_format($b['bolmon'],0,',','.').'.-',1,0,'R');
				$totalb+=round($b['bolmon']*1);
				$pdf->Ln();

				$pdf->SetFontSize(10);
				
				$pdf->Cell(25,5,'',1,0,'C');
				$pdf->SetFont('','I');				
				$pdf->Cell(115,5,utf8_decode('Saldo Pendiente $ '.number_format($saldo,0,',','.') ).'.-',1,0,'L');
				$pdf->SetFont('','');				
				$pdf->SetFontSize(12);
				$pdf->Cell(60,5,'',1,0,'R');
				$pdf->Ln();				
						
	}

	if($l) {
	
	$pdf->SetFontSize(10);
				
	for($i=0;$i<sizeof($l);$i++) {
		switch($l[$i]['tipo']) {
			case 'd':
				$texto='Descuento: '.($l[$i]['nombre']);
				break;
			case 'bn':
				$texto='Descuento: Anulación Boletín Nuevo #'.$l[$i]['dbolnumx'];
				break;
			case 'b':
				$texto='Descuento: Anulación Boletín #'.$l[$i]['dbolnumx'];
				break;
			case 'c':
				$texto='Descuento: Anulación Crédito #'.$l[$i]['nombre'].' Boletín #'.$l[$i]['bolnum'];
				break;
			case 'dc':
				$texto='Descuento: Rebaja Crédito: '.htmlentities($l[$i]['nombre']);
				break;
			case 'i':
				$texto='Descuento: Crédito Sin Interés #'.$l[$i]['crecod'].' Boletín #'.$l[$i]['bolnum'];
				break;
		}	

		$pdf->Cell(20,5,'*',1,0,'C');
		$pdf->Cell(130,5,utf8_decode($texto),1,0,'L');
		$pdf->SetFontSize(12);
		$pdf->Cell(50,5,'- $ '.number_format($l[$i]['monto'],0,',','.').'.-',1,0,'R');
		$pdf->SetFontSize(10);
		$pdf->Ln();
		$totalb-=round($l[$i]['monto']*1);		
	
	}	
	
	}
	
	$pdf->SetFont('','');
	
	if($b['anulacion']=='') {
	
		$pdf->SetFontSize(12);
		$pdf->Cell(140,5,'Total:',1,0,'R');
		if($totalb>=0) {
			$pdf->SetFont('','');
			$pdf->Cell(30,5,'$ '.number_format($totalt,0,',','.').'.-',1,0,'R');
			$pdf->SetFont('','B');
			$pdf->Cell(30,5,'$ '.number_format($totalb,0,',','.').'.-',1,0,'R');
			$pdf->SetFont('','');
		} else {
			$pdf->Cell(30,5,'- $ '.number_format(-$totalb,0,',','.').'.-',1,0,'R');
		}
		
		$pdf->Ln();
		
	}
	
	if($b['pagare']=='f') {
	
	$pdf->Ln();
	
	$pdf->SetFillColor(130,130,130);

	$pdf->SetFont('Arial','B', 13);
	
	$pdf->Cell(200,5,'Detalle de pago Devolucion',1,0,'C');
	
	$pdf->SetFont('Arial','', 10);
	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);

	$pdf->SetFontSize(8);	
	
	$pdf->Cell(50,5,'Total Devolucion',1,0,'C');	
	$pdf->Ln();	

	$pdf->SetFontSize(10);	

	$total_efectivo=($d['monto_total']*-1);
	
	
		$pdf->Cell(50,5,'$'.number_format($d['monto_total']*-1,0,',','.').'.-',1,0,'R');	
							
	
	
	$pdf->Ln();



	
	}

	$pdf->Ln();
	

	if($b['anulacion']=='') {
	
	$tmpx=$pdf->GetX();
	$tmpy=$pdf->GetY();
	$pdf->SetX($tmpx+70);
	
	$pdf->SetFontSize(10);	
	$pdf->SetFont('Arial','');	
	if($b['garantia']=='t'){
		$pdf->Multicell(130,5,utf8_decode(num2texto($totalt)).' PESOS.',1,'C');	
	}else {
		$pdf->Multicell(130,5,utf8_decode(num2texto($d['monto_total']*-1)).' PESOS.',1,'C');
	}
	$h=$pdf->GetY()-$tmpy;
	
	$pdf->SetXY($tmpx,$tmpy);
	
	$pdf->Cell(30,$h,'Total devolucion:',1,0,'R');
	
	
	$pdf->SetFontSize(14);	
	$pdf->SetFont('Arial','B');	
	
		$pdf->Cell(40,$h,'$ '.number_format($d['monto_total']*-1,0,',','.').'.-',1,0,'C');
	
	
	$pdf->SetFont('Arial','');	
	$pdf->Ln(10);
	$pdf->Cell(130,6,utf8_decode('Declaro recibir conforme el total del monto de la devolucion.'),0,0,'C');
	}else{
	
	$tmpx=$pdf->GetX();
	$tmpy=$pdf->GetY();
	$pdf->SetX($tmpx+70);
	
	$pdf->SetFontSize(10);	
	$pdf->SetFont('Arial','');	
	$pdf->Multicell(130,5,utf8_decode(num2texto($d['monto_total']*-1)).' PESOS.',1,'C');
	$h=$pdf->GetY()-$tmpy;
	
	$pdf->SetXY($tmpx,$tmpy);
	
	$pdf->Cell(30,$h,'Total Devolucion:',1,0,'R');
	
	
	$pdf->SetFontSize(14);	
	$pdf->SetFont('Arial','B');	
	$pdf->Cell(40,$h,'$ '.number_format($d['monto_total'],0,',','.').'.-',1,0,'C');
	$pdf->SetFont('Arial','');	
		
	$pdf->Ln(10);
	$pdf->Cell(130,6,utf8_decode('Declaro recibir conforme el total del monto de la devolucion.'),0,0,'C');
	}

	if($b['pagare']=='t' AND $b['anulacion']=='') {
	
	$pdf->Ln();
	$pdf->Ln();

	$pdf->SetFontSize(10);	

	$pdf->Ln();

	}
		
	

	footer();
	
	$pdf->Output('BOLETIN_'.$bolnum.'.pdf','I');	

?>