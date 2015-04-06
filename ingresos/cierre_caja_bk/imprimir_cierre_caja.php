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

	$ac_id=$_GET['ac_id']*1;
	
	$vmes[1]='Enero';
	$vmes[2]='Febrero';
	$vmes[3]='Marzo';
	$vmes[4]='Abril';
	$vmes[5]='Mayo';
	$vmes[6]='Junio';
	$vmes[7]='Julio';
	$vmes[8]='Agosto';
	$vmes[9]='Septiembre';
	$vmes[10]='Octubre';
	$vmes[11]='Noviembre';
	$vmes[12]='Diciembre';
	
	$ac=cargar_registro("SELECT * FROM apertura_cajas JOIN funcionario USING (func_id) WHERE ac_id=".$ac_id);
	
	class PDF extends FPDF {
		function header() {
		
			GLOBAL $ac_id, $ac;

			$this->SetFont('Arial','BU', 18);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('../../imagenes/Logo_CRS.jpg',10,15,40,25);

			$this->SetY(20);	
			$this->Cell(200,7,utf8_decode('CIERRE DE CAJA #'.number_format($ac_id,0,',','.')),0,0,'C');	

			$this->Ln();
			
			$this->SetY(40);	
			$this->SetFont('Arial','B', 16);

        		$this->Cell(50,10,utf8_decode('Fecha Apertura:'),0,0,'R');
		        $this->Cell(50,10,substr($ac['ac_fecha_apertura'],0,16),0,0,'L');
		        $this->Cell(50,10,utf8_decode('Fecha Cierre:'),0,0,'R');
		        $this->Cell(50,10,substr($ac['ac_fecha_cierre'],0,16),0,0,'L');
		        $this->Ln();

			$this->SetFontSize(10);

		
		}

		function footer() {
		
			$this->SetY(300);
				
			$this->SetFont('','',10);
			$this->Cell(200,6,utf8_decode('Página '.$this->PageNo().' de {nb}'),0,0,'C');
			
		}

	}	

		function footer() {

			GLOBAL $pdf, $ac;

				$pdf->Ln(10);
				$pdf->Ln(10);
			
				$pdf->SetFontSize(12);
				$pdf->SetFont('','BU');	
				$pdf->Cell(100,6,formato_rut($ac['func_rut']).' '.$ac['func_nombre'],0,0,'C');	
				$pdf->SetFont('','B');	
				$pdf->Cell(100,6,'__________________________',0,0,'C');
				$pdf->Ln();	
				$pdf->SetFont('','');	
				$pdf->Cell(100,6,'Firma Funcionario',0,0,'C');	
				$pdf->Cell(100,6,utf8_decode('Jefe Recaudación'),0,0,'C');
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
	
	$pdf->SetAutoPageBreak(true,70);
	
	$pdf->AddPage();
	$pdf->SetFillColor(200,200,200);
	
	$pdf->Cell(60,6,'R.U.N.',1,0,'C',1);	
	$pdf->Cell(140,6,'Nombre Recaudador',1,0,'C',1);	
	$pdf->Ln();
	
	$pdf->Cell(60,7,formato_rut($ac['func_rut']),1,0,'R');
	$pdf->Cell(140,7,$ac['func_nombre'],1,0,'L');
	$pdf->Ln();
	
	$pdf->SetFontSize(10);	

	$pdf->SetFont('Arial','');
	
	$pdf->Ln();
	
	$pdf->SetFillColor(130,130,130);

	$pdf->SetFont('Arial','B', 13);
	
	$pdf->Cell(200,7,'Detalle Monto Recaudado',1,0,'C',1);
	
	$pdf->SetFont('Arial','', 10);
	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);

	$pdf->SetFontSize(8);	
	
	$pdf->Cell(50,5,'Total General',1,0,'C',1);	
	$pdf->Cell(50,5,'Total Efectivo',1,0,'C',1);	
	$pdf->Cell(50,5,'Total Cheque(s)',1,0,'C',1);	
	$pdf->Cell(50,5,'Total Otros Medios de Pago',1,0,'C',1);	
	$pdf->Ln();	

	$pdf->SetFontSize(16);	

	$efe3=cargar_registros_obj("SELECT *, boletines.bolnum AS realbolnum, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre, (SELECT SUM(monto) FROM cheques WHERE cheques.bolnum=boletines.bolnum) AS total_cheques, (SELECT SUM(monto) FROM forma_pago WHERE forma_pago.bolnum=boletines.bolnum) AS total_fpago FROM boletines LEFT JOIN pacientes USING (pac_id) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) ORDER BY boletines.bolnum");
	$efe2=cargar_registros_obj("SELECT * FROM caja_detalle WHERE ac_id=$ac_id ORDER BY cd_tipo DESC");
	$efe=cargar_registros_obj("SELECT * FROM caja_detalle WHERE ac_id=$ac_id ORDER BY cd_tipo DESC");
	$tmp=cargar_registro("SELECT SUM(cd_monto) AS total FROM caja_detalle WHERE ac_id=$ac_id;");
	$total_efectivo=$tmp['total']*1;
	
	$chq2=cargar_registros_obj("SELECT COALESCE(COUNT(*),0) AS cnt FROM cheques JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND anulacion=''");
	$chq=cargar_registros_obj("SELECT *, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre FROM cheques JOIN boletines USING (bolnum) JOIN pacientes USING (pac_id) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND anulacion=''");
	$total_chq=cargar_registros_obj("SELECT COALESCE(SUM(monto),0) AS total FROM cheques JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND anulacion=''");
	$total_chq=$total_chq[0]['total']*1;

	$pag2=cargar_registros_obj("SELECT fpago_nombre, COUNT(*) AS cnt, SUM(monto) AS total FROM forma_pago JOIN tipo_formas_pago ON tipo=fpago_id JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND anulacion='' GROUP BY fpago_nombre");
	$pag=cargar_registros_obj("SELECT *, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre FROM forma_pago JOIN tipo_formas_pago ON tipo=fpago_id JOIN boletines USING (bolnum) JOIN pacientes USING (pac_id) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND anulacion=''");
	$total_pag=cargar_registros_obj("SELECT SUM(monto) AS total FROM forma_pago JOIN boletines USING (bolnum) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND anulacion=''");
	$total_pag=$total_pag[0]['total']*1;

	$pagares=cargar_registros_obj("SELECT COUNT(*) AS cnt, SUM(cretot) AS total FROM boletines JOIN creditos USING (crecod) WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND pagare AND anulacion=''");
	$detalle_pagares=cargar_registros_obj("SELECT *, boletines.bolnum AS realbolnum, upper(pac_nombres || ' ' || pac_appat || ' ' || pac_apmat) AS pac_nombre FROM boletines JOIN pacientes USING (pac_id) JOIN creditos USING (crecod) join cuotas on cuotas.crecod=creditos.crecod AND cuonum=1 WHERE boletines.bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id) AND pagare");
	
	$total=$total_efectivo+$total_chq+$total_pag;

	$pdf->Cell(50,7,'$'.number_format($total,0,',','.').'.-',1,0,'R');	
	$pdf->Cell(50,7,'$'.number_format($total_efectivo,0,',','.').'.-',1,0,'R');	
	$pdf->Cell(50,7,'$'.number_format($total_chq,0,',','.').'.-',1,0,'R');	
	$pdf->Cell(50,7,'$'.number_format($total_pag,0,',','.').'.-',1,0,'R');	
	$pdf->Ln();

	
	if($efe) {

		$pdf->SetFontSize(12);	

		$pdf->Cell(40,5,'Efectivo',1,0,'C',1);	
		$pdf->Cell(40,5,'Moneda/Billetes',1,0,'C',1);	
		$pdf->Cell(40,5,'Cantidad',1,0,'C',1);	
		$pdf->Cell(80,5,'Monto ($)',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($efe);$i++) {
		
			$pdf->Cell(40,5,'#'.($i+1),1,0,'C',1);	
			$pdf->Cell(40,5,'$'.number_format($efe[$i]['cd_tipo'],0,',','.').'.-',1,0,'R');	
			$pdf->Cell(40,5,$efe[$i]['cd_monto']/$efe[$i]['cd_tipo'],1,0,'R');	
			$pdf->Cell(80,5,'$'.number_format($efe[$i]['cd_monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Ln();	
		
	}

	// Datos de Cheques	
	
	/*if($chq) {

		$pdf->SetFontSize(10);	

		$pdf->Cell(40,5,'Cheque(s)',1,0,'C',1);	
		$pdf->Cell(40,5,'Banco',1,0,'C',1);	
		$pdf->Cell(40,5,'Serie',1,0,'C',1);	
		$pdf->Cell(40,5,'Fecha',1,0,'C',1);	
		$pdf->Cell(40,5,'Monto',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($chq);$i++) {
		
			$pdf->Cell(40,5,'#'.($i+1),1,0,'C',1);	
			$pdf->Cell(40,5,$chq[$i]['banco'],1,0,'L');	
			$pdf->Cell(40,5,$chq[$i]['serie'],1,0,'L');	
			$pdf->Cell(40,5,$chq[$i]['fecha'],1,0,'C');	
			$pdf->Cell(40,5,'$'.number_format($chq[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 
	
	}*/

	if($chq2) {

		$pdf->SetFontSize(12);	

		$pdf->Cell(100,5,utf8_decode('Número de Cheques'),1,0,'C',1);	
		$pdf->Cell(100,5,'Valor Total Cheques',1,0,'C',1);	
		$pdf->Ln();	

		
		$pdf->Cell(100,5,$chq2[0]['cnt'],1,0,'C',1);	
		$pdf->Cell(100,5,'$'.number_format($total_chq,0,',','.').'.-',1,0,'R');
		$pdf->Ln();	
		$pdf->Ln();	
	
	}

	// Otras Formas de Pago

	/*if($pag) {

		$pdf->SetFontSize(10);	

		$pdf->Cell(40,5,'Otros Medios Pago',1,0,'C',1);	
		$pdf->Cell(40,5,'Tipo Pago',1,0,'C',1);	
		$pdf->Cell(40,5,utf8_decode('Número'),1,0,'C',1);	
		$pdf->Cell(40,5,'Fecha',1,0,'C',1);	
		$pdf->Cell(40,5,'Monto',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pag);$i++) {
		
			$pdf->Cell(40,5,'#'.($i+1),1,0,'C',1);	
			$pdf->Cell(40,5,$pag[$i]['fpago_nombre'],1,0,'L');	
			$pdf->Cell(40,5,$pag[$i]['numero'],1,0,'C');	
			$pdf->Cell(40,5,$pag[$i]['fecha'],1,0,'C');	
			$pdf->Cell(40,5,'$'.number_format($pag[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

	
	}*/

	if($pag2) {

		$pdf->SetFontSize(12);	

		$pdf->Cell(75,5,'Cant. Bonos/Otros',1,0,'C',1);	
		$pdf->Cell(50,5,'Prestador',1,0,'C',1);	
		$pdf->Cell(75,5,'Total',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pag2);$i++) {
		
			$pdf->Cell(75,5,$pag2[$i]['fpago_nombre'],1,0,'L');	
			$pdf->Cell(50,5,$pag2[$i]['cnt'],1,0,'C');	
			$pdf->Cell(75,5,'$'.number_format($pag2[$i]['total'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Ln();	
	
	}
	
	$pdf->SetFontSize(12);	

	$pdf->Ln();	
	
	if($pagares) {

		$pdf->Cell(100,5,utf8_decode('Número de Pagarés'),1,0,'C',1);	
		$pdf->Cell(100,5,utf8_decode('Total Pagarés'),1,0,'C',1);	
		$pdf->Ln();	

		
		$pdf->Cell(100,5,$pagares[0]['cnt'],1,0,'C',1);	
		$pdf->Cell(100,5,'$'.number_format($pagares[0]['total']*1,0,',','.').'.-',1,0,'R');
		$pdf->Ln();	
		$pdf->Ln();	
	
	}

	
	$tmpx=$pdf->GetX();
	$tmpy=$pdf->GetY();
	$pdf->SetX($tmpx+100);
	
	$pdf->SetFontSize(10);	
	$pdf->SetFont('Arial','');	
	$pdf->Multicell(100,5,utf8_decode(num2texto($total)).' PESOS.',1,'C',1);

	$h=$pdf->GetY()-$tmpy;
	
	$pdf->SetXY($tmpx,$tmpy);
	
	$pdf->Cell(50,$h,'Total Recaudado:',1,0,'R',1);
	
	$pdf->SetFontSize(14);	
	$pdf->SetFont('Arial','B');	
	$pdf->Cell(50,$h,'$ '.number_format($total,0,',','.').'.-',1,0,'C');

	$pdf->Ln();


	footer();

	$pdf->AddPage();

	$pdf->SetFillColor(130,130,130);

    $pdf->SetFont('Arial','B', 13);

    $pdf->Cell(200,7,utf8_decode('Detalle Recaudación'),1,1,'C',1);

	$pdf->SetFillColor(200,200,200);

	/*$p=cargar_registros_obj("SELECT * FROM boletin_detalle WHERE bolnum IN (SELECT bolnum FROM apertura_cajas JOIN boletines ON bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre AND boletines.func_id=apertura_cajas.func_id WHERE ac_id=$ac_id AND anulacion='') ORDER BY bolnum, bdet_id");

	$pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(25,5,utf8_decode('Fecha y Hora'),1,0,'C',1);
        $pdf->Cell(20,5,utf8_decode('Código'),1,0,'C',1);
        $pdf->Cell(90,5,utf8_decode('Descripción'),1,0,'C',1);
        $pdf->Cell(5,5,utf8_decode('#'),1,0,'C',1);
        $pdf->Cell(30,5,'Valor ($)',1,0,'C',1);
        $pdf->Cell(30,5,'Copago ($)',1,0,'C',1);
        $pdf->Ln();

        $pdf->SetFillColor(200,200,200);

        $totalt=0; $totalb=0;

        //if(!$paga_cuota) {

                if($p)
                for($i=0;$i<sizeof($p);$i++) {

                        $pdf->SetFontSize(8);
                        $pdf->SetFont('','');
                        $pdf->Cell(25,4,substr(trim($p[$i]['bdet_fecha']),0,16),1,0,'C');
                        $pdf->SetFontSize(8);
                        $pdf->SetFont('','B');
                        $pdf->Cell(20,4,trim($p[$i]['bdet_codigo']),1,0,'C');
                        $pdf->SetFont('','');
                        $pdf->SetFontSize(8);
                        $pdf->Cell(90,4,trunc(trim($p[$i]['bdet_prod_nombre']),45),1,0,'L');
                        $pdf->Cell(5,4,trim($p[$i]['bdet_cantidad']),1,0,'C');
                        $pdf->SetFontSize(10);

                                $pdf->Cell(30,4,'$ '.number_format($p[$i]['bdet_valor_total']*$p[$i]['bdet_cantidad'],0,',','.').'.-',1,0,'R');
                                if($p[$i]['bdet_cobro']=='S') {
                                        $pdf->Cell(30,4,'$ '.number_format($p[$i]['bdet_valor']*$p[$i]['bdet_cantidad'],0,',','.').'.-',1,0,'R');
                                } else {
                                        $pdf->Cell(30,4,'$ '.number_format(0,0,',','.').'.-',1,0,'R');
                                }
                                $pdf->SetFontSize(8);
                                $pdf->Ln();

                                if($p[$i]['bdet_cobro']=='S') {
                                        $totalt+=round($p[$i]['bdet_valor_total']*$p[$i]['bdet_cantidad']);
                                        $totalb+=round($p[$i]['bdet_valor']*$p[$i]['bdet_cantidad']);
                                }
                }

	*/

	
	if($efe3) {

		$pdf->Ln();	

		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,'Efectivo',1,0,'C',1);	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C',1);	
		$pdf->Cell(25,5,'RUT Pac.',1,0,'C',1);	
		$pdf->Cell(90,5,'Nombre Pac.',1,0,'C',1);	
		$pdf->Cell(45,5,'Monto',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($efe3);$i++) {
		
			if($efe3[$i]['anulacion']=='') {
				$anula=''; $fill=0;
			} else {
				$anula=' (A) '; $fill=1;
			}
		
			$pdf->Cell(20,5,'#'.($i+1),1,0,'C',1);	
			$pdf->Cell(20,5,$efe3[$i]['realbolnum'].$anula,1,0,'C',$fill);	
			$pdf->Cell(25,5,formato_rut($efe3[$i]['pac_rut']),1,0,'R',$fill);	
			$pdf->Cell(90,5,trunc($efe3[$i]['pac_nombre'],40),1,0,'L',$fill);
			$pdf->Cell(45,5,$anula.'$'.number_format(($efe3[$i]['bolmon']*1)-($efe3[$i]['total_cheques']*1)-($efe3[$i]['total_fpago']*1),0,',','.').'.-',1,0,'R',$fill);
			$pdf->Ln();	
			
		}
		
		$pdf->Cell(155,5,'Total:',1,0,'R',1);	
		$pdf->Cell(45,5,'$'.number_format($total_efectivo,0,',','.').'.-',1,0,'R',1);
		$pdf->Ln();	

		$pdf->Ln();	
		
	}
	
	if($chq) {

		$pdf->Ln();	

		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,'Cheque(s)',1,0,'C',1);	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C',1);	
		$pdf->Cell(25,5,'RUT Pac.',1,0,'C',1);	
		$pdf->Cell(40,5,'Nombre Pac.',1,0,'C',1);	
		$pdf->Cell(35,5,'Banco',1,0,'C',1);	
		$pdf->Cell(20,5,'Serie',1,0,'C',1);	
		$pdf->Cell(20,5,'Fecha',1,0,'C',1);	
		$pdf->Cell(20,5,'Monto',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($chq);$i++) {
		
			$pdf->Cell(20,5,'#'.($i+1),1,0,'C',1);	
			$pdf->Cell(20,5,$chq[$i]['bolnum'],1,0,'C');	
			$pdf->Cell(25,5,formato_rut($chq[$i]['rut']),1,0,'R');	
			$pdf->Cell(40,5,trunc($chq[$i]['nombre'],20),1,0,'L');	
			$pdf->Cell(35,5,$chq[$i]['banco'],1,0,'L');	
			$pdf->Cell(20,5,$chq[$i]['serie'],1,0,'L');	
			$pdf->Cell(20,5,$chq[$i]['fecha'],1,0,'C');	
			$pdf->Cell(20,5,'$'.number_format($chq[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		}
		
		$pdf->Cell(160,5,'Total:',1,0,'R',1);	
		$pdf->Cell(40,5,'$'.number_format($total_chq,0,',','.').'.-',1,0,'R',1);
		$pdf->Ln();	

		$pdf->Ln();	
		
	}
	
	
	if($pag) {

		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,'Bonos',1,0,'C',1);	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C',1);	
		$pdf->Cell(25,5,utf8_decode('R.U.T.'),1,0,'C',1);	
		$pdf->Cell(55,5,'Nombre',1,0,'C',1);	
		$pdf->Cell(25,5,'Tipo Pago',1,0,'C',1);	
		$pdf->Cell(25,5,utf8_decode('Número'),1,0,'C',1);	
		//$pdf->Cell(30,5,'Fecha',1,0,'C',1);	
		$pdf->Cell(30,5,'Monto',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($pag);$i++) {
		
			$pdf->Cell(20,5,'#'.($i+1),1,0,'C',1);	
			$pdf->Cell(20,5,$pag[$i]['bolnum'],1,0,'C');	
			$pdf->Cell(25,5,formato_rut($pag[$i]['pac_rut']),1,0,'R');	
			$pdf->Cell(55,5,trunc($pag[$i]['pac_nombre'],20),1,0,'L');	
			$pdf->Cell(25,5,$pag[$i]['fpago_nombre'],1,0,'L');	
			$pdf->Cell(25,5,$pag[$i]['numero'],1,0,'C');	
			//$pdf->Cell(30,5,$pag[$i]['fecha'],1,0,'C');	
			$pdf->Cell(30,5,'$'.number_format($pag[$i]['monto'],0,',','.').'.-',1,0,'R');
			$pdf->Ln();	
			
		} 

		$pdf->Cell(160,5,'Total:',1,0,'R',1);	
		$pdf->Cell(40,5,'$'.number_format($total_pag,0,',','.').'.-',1,0,'R',1);
		$pdf->Ln();	

		$pdf->Ln();	

	
	}


	if($detalle_pagares) {

		$pdf->AddPage();
	
		$pdf->SetFontSize(10);	

		$pdf->Cell(20,5,utf8_decode('Pagarés'),1,0,'C',1);	
		$pdf->Cell(20,5,'Nro. Corr.',1,0,'C',1);	
		$pdf->Cell(30,5,utf8_decode('R.U.T.'),1,0,'C',1);	
		$pdf->Cell(60,5,'Nombre',1,0,'C',1);	
		$pdf->Cell(35,5,'Monto',1,0,'C',1);	
		$pdf->Cell(35,5,'Fecha Venc.',1,0,'C',1);	
		$pdf->Ln();	

		for($i=0;$i<sizeof($detalle_pagares);$i++) {

			if($detalle_pagares[$i]['anulacion']=='') {
				$anula=''; $fill=0;
			} else {
				$anula=' (A) '; $fill=1;
			}
		
			$pdf->Cell(20,5,'#'.($i+1),1,0,'C',1);	
			$pdf->Cell(20,5,$detalle_pagares[$i]['realbolnum'].$anula,1,0,'C',$fill);	
			$pdf->Cell(30,5,formato_rut($detalle_pagares[$i]['pac_rut']),1,0,'R',$fill);	
			$pdf->Cell(60,5,trunc($detalle_pagares[$i]['pac_nombre'],20),1,0,'L',$fill);	
			$pdf->Cell(35,5,$anula.'$'.number_format($detalle_pagares[$i]['cretot'],0,',','.').'.-',1,0,'R',$fill);
			$pdf->Cell(35,5,substr($detalle_pagares[$i]['cuofec'],0,10),1,0,'C',$fill);	
			$pdf->Ln();	
			
		} 

		$pdf->Cell(130,5,'Total:',1,0,'R',1);	
		$pdf->Cell(35,5,'$'.number_format($pagares[0]['total'],0,',','.').'.-',1,0,'R',1);
		$pdf->Cell(35,5,'',1,0,'R',1);
		$pdf->Ln();	
		
		$pdf->Ln();	
	
	}
	

	$pdf->Output('CIERRE_CAJA_'.$ac_id.'.pdf','I');	

?>
