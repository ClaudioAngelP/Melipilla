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

	$dev_id=$_GET['dev_id']*1;
	$valor=$_GET['nombre'];
	
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
	if($valor=='false')
	{
			$func_w='func_id=func_id_aprueba';
	
				
	}else{
			$func_w='func_id=func_id_ejecuta';
		
	}
	
	$d=cargar_registro("SELECT *, dev_fecha AS devfec,
	func_rut , func_nombre
	 FROM devolucion_boletines 
left join funcionario  on $func_w  
WHERE devol_id=$dev_id");


	$dd=cargar_registro("SELECT * FROM devolucion_boletin_detalle WHERE devol_id=".$dev_id);
	
	
	$b=cargar_registro("SELECT *, bolfec AS bolfec FROM boletines WHERE bolnum=".$d['bolnum']);
	
	if($valor=='false')
	{
			$f=cargar_registro("SELECT * FROM funcionario WHERE func_id=".$d['func_id_aprueba']);
				
	}else{
			$f=cargar_registro("SELECT * FROM funcionario WHERE func_id=".$d['func_id_ejecuta']);
		
	}
	
	$bolnum=$d['bolnum'];
	
	if($b['clirut']=='' OR $b['crecod']!=0) {	
		$cr=cargar_registro("SELECT *, (SELECT SUM(COALESCE(cuopag,'0')::bigint) FROM cuotas WHERE cuotas.crecod=creditos.crecod AND cuonum>0) AS monto_pagado FROM creditos WHERE crecod=".$b['crecod']);
		$tmp=cargar_registro("SELECT * FROM cuotas WHERE crecod=".$b['crecod']." AND cuonum=1");
		list($f_pago)=explode('/',$tmp['cuofec']);
	} else {
		$cr=false; $f_pago='';
	}
	
	if($cr) {

		$bcr=cargar_registros_obj("SELECT * FROM boletines WHERE crecod=".$cr['crecod']." AND bolnum<=$bolnum AND anulacion='' ORDER BY bolnum");
		
		if($bcr[0]['bolnum']!=$bolnum) {
			$paga_cuota=true;
		} else {
			$paga_cuota=false;
		}
		
	} else {

		$paga_cuota=false;

	}

	if(!$cr) {	
		
		$p=cargar_registros_obj("select* from boletin_detalle where bdet_id  in (select bdet_id from devolucion_boletin_detalle where devol_id=$dev_id) and bolnum=$bolnum");
		$pd=cargar_registros_obj("SELECT * FROM devolucion_boletin_detalle WHERE devol_id=".$dev_id);
	} else {
		$crecod=$b['crecod'];
		$ttmp=cargar_registro("SELECT * FROM cuotas WHERE crecod=$crecod AND cuonum=0;");
		$bolnum2=$ttmp['bolnum']*1;
		if($bolnum2) {
			$ttmp2=cargar_registro("SELECT * FROM boletines WHERE bolnum=$bolnum2");
			$b['prvdesc']=$ttmp2['prvdesc'];
			$b['bolmod']=$ttmp2['bolmod'];
			$p=cargar_registros_obj("select* from boletin_detalle where bdet_id not in (select bdet_id from devolucion_boletin_detalle where devol_id=$dev_id) and bolnum=$bolnum2");
			
		}
	}

	
	$c=cargar_registro("SELECT * FROM pacientes
								LEFT JOIN comunas USING (ciud_id) 
								WHERE pac_id=".($cr?$cr['pac_id']:$b['pac_id']));

	$chq=cargar_registros_obj("SELECT * FROM cheques WHERE bolnum=".$bolnum);
	$total_chq=cargar_registros_obj("SELECT SUM(monto) AS total FROM cheques WHERE bolnum=".$bolnum);
	$total_chq=$total_chq[0]['total']*1;

	$pag=cargar_registros_obj("SELECT * FROM forma_pago JOIN tipo_formas_pago ON tipo=fpago_id WHERE bolnum=".$bolnum);
	$total_pag=cargar_registros_obj("SELECT SUM(monto) AS total FROM forma_pago WHERE bolnum=".$bolnum);
	$total_pag=$total_pag[0]['total']*1;


	$l=cargar_registros_obj("
		
		SELECT boletines.*, monto, tipo, estado, 
		func_rut, func_nombre, bolfec::date AS bolfec, desc_id, 
		nombre, descuentos.bolnumx AS dbolnumx
		FROM descuentos 
		JOIN boletines USING (bolnum)	
		JOIN funcionario USING (func_id)	
		WHERE descuentos.bolnum=$bolnum AND NOT descuentos.tipo='i'
		ORDER BY boletines.bolfec
			
	");

	
	$institucion=utf8_decode('CRS PEÑALOLÉN');
	$fecha=$b['bolfec'];

	
	class PDF extends FPDF {
		function header() {

			GLOBAL $bolnum, $b, $d;

			$this->SetFont('Arial','BU', 18);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('../../imagenes/Logo_CRS.jpg',10,15,40,25);

			$this->Ln(10);
			if($b['anulacion']=='') {
				
			} else {
				
				$this->Cell(200,10,utf8_decode('ANULACIÓN'),0,1,'C');
						
			}

			if($b['pagare']=='t'){
				$this->Cell(200,10,utf8_decode('RESTITUCIÓN DE PAGOS #'.number_format($d['bolnum'],0,',','.')),0,0,'C');	
			}else{
				
				if($b['garantia']=='t')
					$this->Cell(200,10,utf8_decode('RESTITUCIÓN DE PAGOS #'.number_format($d['devol_id'],0,',','.')),0,0,'C');	
				else
					$this->Cell(200,7,utf8_decode('RESTITUCIÓN DE PAGOS #'.number_format($d['devol_id'],0,',','.')),0,0,'C');	
			
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

			GLOBAL $conf,$pdf, $b, $f, $c,$pag, $d,$valor;
			
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
				if($b['pagare']=='f') {
					if($valor=='false')
					{
					
					}else{
						$pdf->Cell(150,6,trim($c['pac_nombres'].' '.$c['pac_appat']),0,0,'C');
					}
				}else{
					
				 $pdf->Cell(150,6,strtoupper($t[1]),0,0,'C');
				}
				$pdf->Ln();	
				$pdf->SetFont('','');	
				$pdf->Cell(60,6,'Funcionario Emisor',0,0,'C');	
				if($b['pagare']=='f'){
					if($valor=='false')
					{
					
					}else{
					 $pdf->Cell(150,6,'Paciente',0,0,'C');
					}					
				}else{
					 $pdf->Cell(150,6,'Titular',0,0,'C');
				}
				$pdf->Ln();
				$pdf->Ln();			
				$pdf->SetFontSize(10);
			} else {
				$pdf->SetFontSize(10);
				$pdf->SetFont('','BU');	
				$pdf->Cell(60,6,$f['func_nombre'],0,0,'C');	
				if($b['pagare']=='f') {
					if($valor=='false')
					{
					
					}else{
								$pdf->Cell(80,6,trim($c['pac_nombres'].' '.$c['pac_appat']),0,0,'C');
					}                 
				}else{
                 	 $pdf->Cell(0,6,strtoupper($t[1]),0,0,'C');
				 }
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
	if($valor=='false')
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
	
	if($valor=='false')
	{
	
	}else{
		$pdf->SetFont('Arial','');	
	$pdf->Ln(10);
	$pdf->Cell(130,6,utf8_decode('Declaro recibir conforme el total del monto de la devolucion.'),0,0,'C');	
	}
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

	$pdf->Multicell(200,4,(utf8_decode('En cajas de CRS la cantidad de $ '.number_format($cr['cretot'],0,',','.').' ( '.utf8_decode(num2texto($cr['cretot'])).' PESOS). Por concepto de prestaciones médicas.
La suscripción de este documento no constituye renovación una vez completado el plazo de 30 días.
La cantidad adeudada se pagara el '.texto_fecha($fecha_pagare).' o el día hábil anterior si cae en sábado, domingo y/o festivo.
En caso de regularizar su previsión, debe presentar en CRS CORDILLERA ORIENTE para declarar su situación.
Todas las obligaciones emanadas de este pagare serán solidarias para el o los suscriptores avales, Codeudores, fiadores y demás obligados al pago.
La falta de pago de la deuda, facultara al CRS CORDILLERA ORIENTE para exigir pago total de ella, la que en este evento se considerara de plazo vencido para todos los efectos legales.
Por este acto el suscriptor autorizada al CRS CO o a quien sus derechos, represente para informar de dicha a los boletines comerciales u otros que reciban o contengan información relativa a los deudores morosos, ya sea por sistemas públicos o privados, en caso que exista mora o simple retardo en el pago adecuado.
EL PORTADOR QUEDA LIBERADO DE LA OBLIGACION DE PROTESTAR ESTE PAGARE RESPECTO A TODOS LOS OBLIGADOS A SU PAGO.
Para todos los efectos legales, judiciales y protestos derivados del presente pagare, el suscriptor constituye domicilio especial en esta ciudad en la dirección que se indica y somete a la jurisdicción de los tribunales ordinarios de justicia de Santiago.

En Santiago, a '.texto_fecha(substr($b['bolfec'],0,10)).'.-')),0,'J');

	$pdf->Ln();

	}
		
	

	footer();

	// Adjunta Comprobante de Egreso

	/*if($b['saldof']*1>0) {

		$saldof=$b['saldof']*1;

		$pdf->AddPage();
	
		$pdf->SetFont('','U',18);	

		$fp=explode('/',$b['bolfec']);
		$anio=$fp[2]*1;
		$mes=$vmes[$fp[1]*1];
		$nombre=$c['pac_nombres'].' '.$c['pac_appat'].' '.$c['pac_apmat'];
		$rut=$c['pac_rut'];
		
		$nc=5-strlen($b['coming']);		
		$numero=str_repeat('0',$nc).''.$b['coming'];
		
		$pdf->Cell(200,7,utf8_decode('COMPROBANTE DE EGRESO N° '.$numero.' D.C./'.$anio),0,0,'C');
		$pdf->Ln(30);
		
		$pdf->SetFont('','',12);		
		
		$pdf->Cell(10,5,'',0,0,'L');
		$pdf->Multicell(180,5,str_replace("\n",'',utf8_decode('Con fecha '.$fp[0].' de '.$mes.' del '.$anio.' se procede a cancelar 
la cantidad de $'.number_format($saldof,0,',','.').'.- 
('.num2texto($saldof).' PESOS) al Sr(a). '.$nombre.',  
R.U.T. Nro. '.$rut.', correspondiente al 
saldo a favor remantente durante la emisión del  
Boletín #'.number_format($b['bolnum'],0,',','.').'.')),0,'J');
		
		$pdf->Ln(30);
		
		$pdf->SetFontSize(12);
		$pdf->SetY(270);
		$pdf->SetFont('','B');	
		$pdf->Cell(100,6,'',0,0,'C');	
		$pdf->Cell(100,6,'',0,0,'C');
		$pdf->Ln();	
		$pdf->SetFont('','');	
		$pdf->Cell(100,6,'R.U.T.:'.$rut,0,0,'C');	
		$pdf->Cell(100,6,'RECAUDADOR',0,0,'C');
		$pdf->Ln(20);			
		$pdf->SetFont('','B');	
		$pdf->Cell(200,6,'',0,1,'C');	
		$pdf->SetFont('','');	
		$pdf->Cell(200,6,'',0,0,'C');
		
		
	}*/
	
	// Adjunta Pagaré y Seguro de Desgravamen	
	
	if(false) {



		////////////////////
		// PAGARÉ
		////////////////////

		$pdf->AddPage();
	
		$pdf->SetFont('','U',18);	

		$fp=explode('/',$b['bolfec']);
		$anio=$fp[2]*1;
		$mes=$vmes[$fp[1]*1];
		$nombre=utf8_encode($c['pac_nombres'].' '.$c['pac_appat'].' '.$c['pac_apmat']);
		$rut=$c['pac_rut'];
		$fnac=($c['pac_fc_nac']);
		
		if($c['comdes']!='')
			$dir=utf8_encode($c['pac_direccion'].', '.$c['comdes']);
		else 
			$dir=utf8_encode($c['pac_direccion']);
					
		$pdf->Cell(200,7,utf8_decode('PAGARÉ N° '.$b['crecod']),0,0,'C');
		$pdf->Ln(15);

		$pdf->SetFont('','',8);		

		$pdf->Multicell(200,5,str_replace('<br>',"\n",str_replace("\n",'',utf8_decode('
		
En Valparaíso a '.$fp[0].' días del mes de '.$mes.' del '.$anio.',
<br>

El CLIENTE, '.($nombre).', Cédula Nacional de Identidad '.$rut.', nacionalidad 
chilena, domiciliado en '.($dir).', 
declara que debe y pagará a la orden de Corporación Municipal de Valparaíso, 
en adelante también "el acreedor", R.U.T. 70.859.400-8, domiciliado en 
Pedro Montt 1881 de la ciudad de Valparaíso, 
la cantidad de $'.number_format($cr['cretot']*1,0,',','.').'.- 
('.num2texto($cr['cretot']*1).' PESOS).
<br><br>

1.- CAPITAL:
<br>

El capital adeudado se pagará en '.$cr['cuonro'].' cuotas iguales mensuales 
y sucesivas de $'.number_format(ceil($cr['cretot']/$cr['cuonro']),0,',','.').'.- 
('.num2texto(ceil($cr['cretot']/$cr['cuonro'])).' PESOS) cada una, 
los días 19 de cada mes, a contar del 19 de junio de 2006.

<br><br>

2.- PAGO:
<br>

El pago se efectuará  en la Administración del Cementerio Playa Ancha, 
ubicada en Subida Cementerio S/N, Playa Ancha, Valparaíso.

 
<br><br>

3.- INTERESES PENALES:
<br>

En caso de mora o simple retardo en el pago, se capitalizarán los 
intereses vencidos de conformidad al artículo 9° de la Ley 18.010 y 
el capital adeudado o el saldo insoluto a que éste se halle reducido, 
más los intereses así capitalizados devengará un interés penal igual 
al interés máximo convencional permitido por la ley a la fecha de 
otorgamiento de éste pagaré para operaciones de crédito de dinero no 
reajustables en moneda nacional. El interés penal así calculado correrá 
desde la fecha de la mora o simple retardo y hasta la fecha del pago de lo 
adeudado después de ocurrida la mora o simple retardo, y sin perjuicio de 
los demás derechos del acreedor.

<br><br>

4.- EXIGIBILIDAD ANTICIPADA:
<br>

El no pago oportuno por parte del suscriptor de cualquiera de las cuotas 
pactadas, facultará al acreedor para exigir el total de la deuda, como si 
fuere de plazo vencido.

 
<br><br>

5.- AUTORIZACIÓN:
<br>

El suscriptor autoriza al acreedor para proporcionar información respecto 
del monto del presente pagaré, al Boletín Comercial, a los establecimientos 
de comercio, a las Instituciones Financieras o sociedades vinculadas a éstas.

<br><br>

6.- TRIBUTACIÓN Y GASTOS DEL PAGARÉ:
<br>

Los Impuestos, derechos notariales y demás gastos que afecten a este pagaré 
serán de exclusivo cargo del suscriptor o deudor.
<br><br>

7.- PROTESTO:
<br>

El suscriptor o deudor y futuros tenedores del presente pagaré liberan 
desde ya al acreedor de la obligación de protesto del mismo.

<br><br>

8.- DOMICILIO Y JURISDICCIÓN:
<br>

Para todos los efectos legales derivados de este pagaré incluidas las 
diligencias de su protesto, el deudor o suscriptor constituye domicilio 
especial en la ciudad de Valparaíso, y se somete a la jurisdicción de los 
Tribunales Ordinarios de Justicia.
'))));
	
	$pdf->Ln(15);

	$pdf->SetFont('','B',12);
	$pdf->Cell(200,5,utf8_decode('________________________________'),0,1,'C');	
	$pdf->Cell(200,5,utf8_decode($nombre),0,1,'C');
	$pdf->Cell(200,5,utf8_decode('R.U.T.:'.$rut),0,1,'C');
	
	
	
	
	////////////////////////////
	// SEGURO DE DESGRAVAMEN	
	////////////////////////////
	
	$pdf->AddPage();	
	
	$pdf->SetFont('','U',14);
	$pdf->Cell(200,5,'PROPUESTA DE SEGURO DE DESGRAVAMEN',0,1,'C');	
	$pdf->SetFont('','',12);
	$pdf->Cell(200,5,utf8_decode('Corporación Municipal de Valparaíso para el Desarrollo Social'),0,0,'C');	
	$pdf->Ln(5);

	$pdf->SetFont('','',7);		

	$pdf->Cell(180,5,utf8_decode('Número de Crédito:'),0,0,'R');
	$pdf->SetFont('','U');
	$pdf->Cell(20,5,$cr['crecod'],0,1,'C');
	$pdf->SetFont('','');

	$uc=cargar_registro("SELECT *, cuofec::date AS cuofec FROM cuotas 
		WHERE cuonum=(SELECT MAX(cuonum) FROM cuotas 
							WHERE crecod=".$cr['crecod']." ) 
				AND crecod=".$cr['crecod'] );

	$fecha_ext=$uc['cuofec'];

	$pdf->Multicell(200,5,str_replace('<br>',"\n",str_replace("\n",'',utf8_decode("
Antecedentes Generales:<br>
Póliza Colectiva CV-4172-0000<br>
Contratante de la Póliza Colectiva: CORPORACION MUNICIPAL DE VALPARAISO PARA EL DESARROLLO SOCIAL<br>
RUT: 70.859.400-8<br>
Asegurador: La Interamericana Cia. de Seguros de Vida S.A.<br>
Comision de Recaudacion: 40% IVA Incluido<br>
intermediacion: Intermediacion Directa
<br><br>

Fecha de Propuesta: ".$fp[0]." de $mes del $anio .<br><br>

Inicio de Vigencia de la Cobertura:<br>
la cobertura de la póliza regirá, respecto de cada asegurado, desde la suscripcion de la presente propuesta de seguro, debiendo
la CORPORACION MUNICIPAL DE VALPARAISO PARA EL DESARROLLO SOCIAL cumplir el envío de información e informes de primas.
<br>"))));

$pdf->SetFont('','B');
$pdf->Multicell(200,5,utf8_decode("La presente hará las veces de certificado de cobertura de conformidad a lo dispuesto en la circular N° 1759 de la Superintendencia de Valores y Seguros."));
$pdf->SetFont('','');
$pdf->Ln();

$pdf->Multicell(200,5,str_replace('<br>',"\n",str_replace("\n",'',utf8_decode("
Antecedentes del Asegurado:<br>
Nombre: $nombre  RUT: $rut<br>
Fecha de Nacimiento: $fnac<br>
Dirección: $dir<br>
Monto del Crédito: $ ".number_format($cr['cretot'],0,',','.').".-<br>
Plazo del Crédito: ".$cr['cuonro']." meses.<br>
Fecha de Extinción del Crédito: $fecha_ext <br>
<br><br>

Cobertura de Desgravamen:<br>
Cubre el saldo insoluto del crédito otorgado por la CORPORACION MUNICIPAL DE VALPARAISO PARA EL DESARROLLO SOCIAL para la adquisicion 
de sepulturas en el Cementerio de Playa Ancha, informado por el contratante de la póliza a la Aseguradora, vigente al último día del 
mes inmediatamente anterior a la fecha de fallecimiento del deudor, y basado en un servicio regular de la deuda. El capital asegurado 
sera de un 100% del saldo para cada asegurado, con un Capital Máximo Asegurado de U.F. 100 (100 Unidades de Fomento) por asegurado, basado
en un servicio regular de la deuda.
<br><br>

Requisitos de la Asegurabilidad:<br>
Podrán ser asegurados los informados en calidad de tales a la compañía aseguradora por la CORPORACION MUNICIPAL DE VALPARAISO PARA EL 
DESARROLLO SOCIAL, que sean clientes personas naturales de esta última, titulares de creditos otorgados por la referida empresa para 
la adquisición de sepulturas en el Cementerio de Playa Ancha, que cumplan con los requisitos de ingreso y permanencia que se expresan 
a continuacion.
La edad máxima de incorporación es de 64 años y 364 días, pudiendo permanecer como asegurado de la póliza hasta el día en que cumpla 
70 años de edad.
<br><br>

Beneficiario Cobertura de Desgravamen:<br>
Se designa en calidad de beneficiario irrevocable a la CORPORACION MUNICIPAL DE VALPARAISO PARA EL DESARROLLO SOCIAL de los creditos 
cubiertos a la fecha de fallecimiento.<br>
En caso de renegociación o refinanciamiento, se entendera terminado el seguro asociado al crédito y se origina una nueva operación con
nuevas característcas; con ello, a partir de ese momento comienzan a regir nuevamente las condiciones del seguro, por lo que debe
pagarse una nueva prima y evaluarse de acuerdo a los requisitos establecidos.
<br><br>

Prima Mensual por Asegurado:<br>
"))));


$pdf->SetFont('','B');
$pdf->Cell(80,5,'');                                      
$pdf->MultiCell(70,5,"Prima Mensual por\nRangos de Edades",1,'C');

$pdf->Cell(80,5,'');                                      
$pdf->Cell(30,5,'Cobertura',1,0,'C');
$pdf->Cell(20,5,'18-40',1,0,'C');
$pdf->Cell(20,5,'41-70',1,1,'C');
$pdf->SetFont('','');
$pdf->Cell(80,5,'');
$pdf->Cell(30,5,'Seguro de Desgravamen',1,0,'C');
$pdf->Cell(20,10,'0.29%',1,0,'C');
$pdf->Cell(20,10,'0.93%',1,0,'C'); $pdf->Ln(5);
$pdf->SetFont('','B');
$pdf->Cell(80,5,'');                                      
$pdf->Cell(30,5,'POL 2 05 035',1,1,'C');
$pdf->SetFont('','');
$pdf->Ln(7);

	$pdf->Multicell(200,5,str_replace('<br>',"\n",str_replace("\n",'',utf8_decode("
El proponente autoriza al contratante para contratar y renovar este seguro colectivo en cualquier tiempo y en condiciones semejantes de 
cobertura en la compañía que estime conveniente.<br>
El propuesto asegurado podra solicitar a la CORPORACION MUNICIPAL DE VALPARAISO PARA EL DESARROLLO SOCIAL o la compañía aseguradora una 
copia de la póliza que ha sido contratada en forma colectiva.<br>
En conformidad a la normativa legal vigente, autorizo a todos los médicos y a cualquier otra persona que me haya examinado y/o atendido,
y a todos los hospitales y cualquier otra institución, a entregar información completa, adjuntando copia de sus archivos en relación con
los reclamos de los beneficios, a La Interamericana Compañía de Seguros S.A.<br>
el propuesto asegurado declara haber leído detenidamente el anverso y reverso de la presente propuesta.
<br><br>"))));

$pdf->Cell(100,5,'___________________________',0,0,'C');
$pdf->Cell(100,5,'___________________________',0,1,'C');
$pdf->Cell(100,5,'Firma Asegurado',0,0,'C');
$pdf->Cell(100,5,utf8_decode('Fecha Recepción Cía.'),0,1,'C');
$pdf->Ln();

$pdf->Multicell(200,5,str_replace('<br>',"\n",str_replace("\n",'',utf8_decode("
Excluciones para el Seguro de Desgravamen:<br>
Exclusiones cobertura de Desgravamen, incluídas en el condicionado general POL 205035.
Este seguro no cubre el riesgo de muerte si el fallecimioento del asegurado es consecuencia de alguna de las siguientes situaciones:<br>
1.- Alguna de las circunstancias mencionadas en los numeros 1° y 2° del artículo 575 del Código de Comercio; no obstante, el asegurador 
pagará el capital asegurado al beneficiario, si el fallecimiento ocurriera como consecuencia de suicidio, siempre que el asegurado hubiera 
permanecido, a lo menos, un ño como asegurado vigente.<br>
2.- Participación del asegurado en guerra internacional, sea que Chile tenga o no participacion en ella; en guerra civil, dentro y fuera 
de Chile; o en un motín o conmoción contra el orden público dentro o fuera del pais, siempre que el asegurado tenga participación activa en 
dicho motín o conmoción.<br>
3.- Enfermedades, lesiones o dolencias preexistentes, entendiendose por tales, cualquier lesion, enfermedad o dolencia que afecte al asegurado,
conocida o diagnosticada con anterioridad a la fecha de incorporacion dek asegurado a la póliza.<br>
4.- Una infección oportunísca, o un neoplasma maligno, si al momento de la muerte o enfermedad el asegurado sufría del Sindrome 
de  Inmunodeficiencia Adquirida. Con tal propósito, se entenderá por:<br>
- 'Sindrome de Inmunodeficiencia Adquirida', lo definido para tal efecto por la Organización Mundial de la Salud copia de dicha definicion 
esta archivada en las oficinas principales de la compañía en Santiago, Chile.<br>
- Infección oportunísca incluye, pero no debe limitarse a Neumonia causada por Pneumocystis carinii, organismo de la Enteritis Crónica,
Infección Vírica o Infección Microbacteriana Diseminada.<br>
- Neoplasma Maligno incluye, pero no debe limitarse al Sarcoma de Kaposi, al Linfoma del Sistema Nervioso Central, o a otras afecciones malignas 
ya conocidas o que puedan conocerse como causas inmediatas de muerte en presencia de una inmunodeficiencia adquirida.<br>
- Sindrome de Inmunodeficiencia Adquirida debe incluir encefalopatia (demencia) de V.I.H. (Virus de Inmunodeficiencia Humano) y Sindrome de 
Desgaste por V.I.H. (Virus de Inmunodeficiencia Humano).<br>
En estos casos, el asegurador solo estara obligado a devolver a los herederos del asegurado una cantidad igual al valor de las primas ya pagadas, 
previa deducción de cualquier deuda por concepto del contrato.<br><br>


Que Hacer en Caso de Fallecimiento del Asegurado:<br>
en caso de muerte del asegurado, un familiar o cualquier persona interesada, debe concurrir a la brevedad a las oficinas de la CORPORACION 
MUNICIPAL DE VALPARAISO PARA EL DESARROLLO SOCIAL y presentar, segun la causa del fallecimiento, los siguientes antecedentes:<br>
Muerte Natural: certificado de defunción, fotocopia de cédula de identidad del asegurado y cualquier otro documento que la Cia. de Seguros 
estime necesario.<br>
Muerte accidental: certificado de defunción, fotocopia de cédula de identidad del asegurado, parte policial y cualquier otro documento que 
la Cía. de Seguros estime necesario.<br>
La CORPORACION MUNICIPAL DE VALPARAISO PARA EL DESARROLLO SOCIAL enviará los antecedentes a la Compañia del pago de la indemnización y asesorará 
sin costo adicional a los beneficiarios si estos lo requieren.<br><br>


Informacion sobre presentacion de consultas y reclamos:<br>
En virtud de la circular N° 1487 de julio del 2000, las compañias de seguros deben recibir, registrar y responder todas las presentaciones, consultas 
o reclamos que se les presenten directamente por el contratante, asegurado o beneficiarios o aquellos que la Superintendencia de Valores y Seguros 
derive.<br>
Las presentaciones deben ser efectuadas en la casa matriz y en todas las agencias, oficinas o sucursales de la compañía en que se atienda público, 
personalmente, por correo o fax, sin formalidades, en el horario normal de atención y sin restricción de días u horarios especiales.
el interesado, en caso de disconformidad respecto de lo informado por la compñia de seguros, o bien cuando exista demora injustificada en su respuesta,
podrá recurrir a la Superintendencia de Valores y Seguros, Departamento de Atención al Asegurado, cuyas oficinas se encuentran en Alameda 1449, piso 1.
<br><br>

Información del Sistema de Autorregulacion de Contratos de Seguros y Defensor del Asegurado:<br>
La Interamericana Compañia de Seguros de Vida S.A. se encuentra adherida al código de Autorregulacion de las Compañias de Seguros y esta sujeta al compendio
de Buenas Prácticas Corporativas, que contiene un conjunto de normas destinadas a promover una adecuada relación de las compñias de seguros con sus clientes.
Copia de este Compendio se encuentra en la pagina www.aach.cl<br>
Asimismo, ha aceptado la intervención del Defensor del Asegurado cuando los clientes le presenten reclamos en relación a los contratos celebrados con ella.
Los clientes pueden presentar sus reclamos ante el defensor del asegurado utilizando los formularios disponibles en las oficinas de La Interamericana 
Compañía de Seguros de Vida S.A. o a través de la pagina web www.ddachile.cl<br><br>

En caso de dudas le solicitamos contactar a la mesa de atención al cliente de Seguros Interamericana al fono 600 390 3000. 
"))));		
		
		
	}
	
	$pdf->Output('BOLETIN_'.$bolnum.'.pdf','I');	

?>
