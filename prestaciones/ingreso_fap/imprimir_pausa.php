<?php

	require_once('../../conectar_db.php');
	require_once('../../fpdf/fpdf.php');

	function cut($str, $len) {
		
		$str=trim($str);		
		
		if(strlen($str)>$len-3) {
			$str=substr($str,0,$len-3).'...';
		} 
		
		return $str;
			
	}		

	function cut2($str) { return cut($str, 30); }
		
	$fap_id=$_GET['fap_id']*1;
	
	$fap=cargar_registro("
		SELECT
		fap_pabellon.*, fappab_pabellones.*,
		th.*, pacientes.*, prevision.*, p1.*,
		ta1.fapta_id AS fapta_id1,
		ta1.fapta_desc AS fapta_desc1,
		ta2.fapta_id AS fapta_id2,
		ta2.fapta_desc AS fapta_desc2,
		COALESCE(d0.diag_desc, fap_diag_cod) AS diag_desc, 		
		COALESCE(d1.diag_desc, fap_diagnostico_1) AS diag_desc_1, 		
		COALESCE(d2.diag_desc, fap_diagnostico_2) AS diag_desc_2, 		
		COALESCE(d3.diag_desc, fap_diagnostico_3) AS diag_desc_3,
		c1.tcama_tipo AS tcama_tipo,
		c2.tcama_tipo AS tcama_tipo2,
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
		'' AS edad,
		COALESCE((SELECT hosp_id FROM hospitalizacion
			WHERE hosp_pac_id=pac_id AND (fap_fecha BETWEEN hosp_fecha_ing AND CASE WHEN hosp_fecha_egr IS NULL THEN fap_fecha ELSE hosp_fecha_egr END) ORDER BY hosp_id DESC LIMIT 1  
			)::text,'(No encontrado...)') AS cta_cte
		FROM fap_pabellon 
		LEFT JOIN pacientes USING (pac_id)		
		LEFT JOIN prevision ON prevision.prev_id=pacientes.prev_id		
		LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id		
		LEFT JOIN fappab_tipo_herida AS th USING (fapth_id)		
		LEFT JOIN fappab_tipo_anestesia AS ta1 ON fapta_id1=ta1.fapta_id		
		LEFT JOIN fappab_tipo_anestesia AS ta2 ON fapta_id2=ta2.fapta_id		
		LEFT JOIN diagnosticos AS d0 ON fap_diag_cod=d0.diag_cod
		LEFT JOIN diagnosticos AS d1 ON fap_diag_cod_1=d1.diag_cod
		LEFT JOIN diagnosticos AS d2 ON fap_diag_cod_2=d2.diag_cod
		LEFT JOIN diagnosticos AS d3 ON fap_diag_cod_3=d3.diag_cod
		LEFT JOIN clasifica_camas AS c1 ON fap_pabellon.centro_ruta=c1.tcama_id::text
		LEFT JOIN clasifica_camas AS c2 ON fap_pabellon.centro_ruta2=c2.tcama_id::text
		LEFT JOIN fap_equipo_quirurgico ON fap_pabellon.fap_id=fap_equipo_quirurgico.fap_id AND fapeq_num=0
		LEFT JOIN personal_pabellon AS p1 ON p1.pp_id=cir1 		
		WHERE fap_pabellon.fap_id=$fap_id	
	");

	$presta=cargar_registros_obj("
                SELECT DISTINCT *, (SELECT glosa FROM codigos_prestacion_recaudacion WHERE fappr_codigo=codigo LIMIT 1) AS glosa FROM fap_prestacion
                WHERE fap_id=$fap_id ORDER BY fappr_id
        ");

	$equipo=cargar_registros_obj("
			SELECT fap_equipo_quirurgico.*,
	
				p1.pp_id AS pp1_id,	
				p1.pp_rut AS pp1_rut,
				p1.pp_paterno || ' ' || p1.pp_materno || ' ' || p1.pp_nombres AS pp1_nombre,
				cir1_t AS pp1_turno,
	
				p2.pp_id AS pp2_id,	
				p2.pp_rut AS pp2_rut,
				p2.pp_paterno || ' ' || p2.pp_materno || ' ' || p2.pp_nombres AS pp2_nombre,
				cir2_t AS pp2_turno,
	
				p3.pp_id AS pp3_id,	
				p3.pp_rut AS pp3_rut,
				p3.pp_paterno || ' ' || p3.pp_materno || ' ' || p3.pp_nombres AS pp3_nombre,
				cir3_t AS pp3_turno,
	
				p4.pp_id AS pp4_id,	
				p4.pp_rut AS pp4_rut,
				p4.pp_paterno || ' ' || p4.pp_materno || ' ' || p4.pp_nombres AS pp4_nombre,
				ane1_t AS pp4_turno,
	
				p5.pp_id AS pp5_id,	
				p5.pp_rut AS pp5_rut,
				p5.pp_paterno || ' ' || p5.pp_materno || ' ' || p5.pp_nombres AS pp5_nombre,
				ane2_t AS pp5_turno,
	
				p6.pp_id AS pp6_id,	
				p6.pp_rut AS pp6_rut,
				p6.pp_paterno || ' ' || p6.pp_materno || ' ' || p6.pp_nombres AS pp6_nombre,
	
				p7.pp_id AS pp7_id,	
				p7.pp_rut AS pp7_rut,
	
				p7.pp_paterno || ' ' || p7.pp_materno || ' ' || p7.pp_nombres AS pp7_nombre,
	
				p8.pp_id AS pp8_id,	
				p8.pp_rut AS pp8_rut,
				p8.pp_paterno || ' ' || p8.pp_materno || ' ' || p8.pp_nombres AS pp8_nombre,
	
				p9.pp_id AS pp9_id,	
				p9.pp_rut AS pp9_rut,
				p9.pp_paterno || ' ' || p9.pp_materno || ' ' || p9.pp_nombres AS pp9_nombre,
	
				p10.pp_id AS pp10_id,	
				p10.pp_rut AS pp10_rut,
				p10.pp_paterno || ' ' || p10.pp_materno || ' ' || p10.pp_nombres AS pp10_nombre,
	
				p11.pp_id AS pp11_id,	
				p11.pp_rut AS pp11_rut,
				p11.pp_paterno || ' ' || p11.pp_materno || ' ' || p11.pp_nombres AS pp11_nombre,
				
				p12.pp_id AS pp12_id,	
				p12.pp_rut AS pp12_rut,
				p12.pp_paterno || ' ' || p12.pp_materno || ' ' || p12.pp_nombres AS pp12_nombre,
				
				p13.pp_id AS pp13_id,	
				p13.pp_rut AS pp13_rut,
				p13.pp_paterno || ' ' || p13.pp_materno || ' ' || p13.pp_nombres AS pp13_nombre,
				
				p14.pp_id AS pp14_id,	
				p14.pp_rut AS pp14_rut,
				p14.pp_paterno || ' ' || p14.pp_materno || ' ' || p14.pp_nombres AS pp14_nombre,
				
				p15.pp_id AS pp15_id,	
				p15.pp_rut AS pp15_rut,
				p15.pp_paterno || ' ' || p15.pp_materno || ' ' || p15.pp_nombres AS pp15_nombre,

				p16.pp_id AS pp16_id,	
				p16.pp_rut AS pp16_rut,
				p16.pp_paterno || ' ' || p16.pp_materno || ' ' || p16.pp_nombres AS pp16_nombre

				
			FROM fap_equipo_quirurgico 
			LEFT JOIN personal_pabellon AS p1 ON p1.pp_id=cir1 		
			LEFT JOIN personal_pabellon AS p2 ON p2.pp_id=cir2 		
			LEFT JOIN personal_pabellon AS p3 ON p3.pp_id=cir3 		
			LEFT JOIN personal_pabellon AS p4 ON p4.pp_id=ane1 		
			LEFT JOIN personal_pabellon AS p5 ON p5.pp_id=ane2 		
			LEFT JOIN personal_pabellon AS p6 ON p6.pp_id=inst 		
			LEFT JOIN personal_pabellon AS p7 ON p7.pp_id=pab 		
			LEFT JOIN personal_pabellon AS p8 ON p8.pp_id=tecane 		
			LEFT JOIN personal_pabellon AS p9 ON p9.pp_id=tecperf 		
			LEFT JOIN personal_pabellon AS p10 ON p10.pp_id=tecrx		
			LEFT JOIN personal_pabellon AS p11 ON p11.pp_id=tecrecu
			LEFT JOIN personal_pabellon AS p12 ON p12.pp_id=cir4
			LEFT JOIN personal_pabellon AS p13 ON p13.pp_id=inst2
			LEFT JOIN personal_pabellon AS p14 ON p14.pp_id=pab2
			LEFT JOIN personal_pabellon AS p15 ON p15.pp_id=tecane2			
			LEFT JOIN personal_pabellon AS p16 ON p16.pp_id=enf
			WHERE fap_id=$fap_id	
			ORDER BY fapeq_num

	");
	

		$edad='';
      
      if($fap['edad_anios']*1>1) $edad.=$fap['edad_anios'].' a ';
		elseif($fap['edad_anios']*1==1) $edad.=$fap['edad_anios'].' a ';

		if($fap['edad_meses']*1>1) $edad.=$fap['edad_meses'].' m ';	
		elseif($fap['edad_meses']*1==1) $edad.=$fap['edad_meses'].' m ';

		if($fap['edad_dias']*1>1) $edad.=$fap['edad_dias'].' d';
		elseif($fap['edad_dias']*1==1) $edad.=$fap['edad_dias'].' d';
	
	
	$pdf=new FPDF('P','mm','Legal');
	
	$pdf->AddPage();
			
	$pdf->SetFillColor(250,250,250);	

	$pdf->SetFont('Arial','', 9);

	$pdf->Image('../../imagenes/logo.png', 160,5,36,22);

	$pdf->Cell(190,4,'Ministerio de Salud',0,1,'L');
	$pdf->Cell(190,4,'Servicio de Salud Metropolitano Occidente',0,1,'L');
	$pdf->Cell(190,4,'Hospital San Jos� de Melipilla',0,1,'L');

	$pdf->SetFont('Arial','BU', 20);

	$pdf->Cell(190,9,utf8_decode('Pausa de Seguridad Quirúrgica'),0,1,'C');
	$pdf->Ln(5);

	$pdf->SetFont('Arial','B',11);

	$pdf->Cell(190,5,'Nro. Folio: '.$fap['fap_fnumero'].' | '.utf8_decode('Fecha Intervención: ').substr($fap['fap_fecha'],0,10),0,1,'C');
	$pdf->Cell(190,5,'RUN: '.formato_rut($fap['pac_rut']).' | FICHA: '.$fap['pac_ficha'].' | Cta. Cte.:'.$fap['cta_cte'],0,1,'C');
	
	if($fap['fap_suspension']!=''){
		$pdf->Cell(190,5,utf8_decode('Suspendido: '.$fap['fap_suspension']),0,1,'C');
	}
	
	$pdf->Ln(5);
	
	// PAUSA DE SEGURIDAD ESTA DIVIDIDA EN TRES CHECKLIST...
	$titulos=Array("Recepción","Ingreso a Pabellón","Salida de Pabellón");
	$fcl_ids=Array(9,10,12); // IDS CHECKLIST PAUSA DE SEGURIDAD 9 RECEPCION 10 INGRESO PAB. 12 SALIDA PAB
	
	$top_y=$pdf->GetY();
	$left_x=$pdf->GetX();
	
	for($c=0;$c<sizeof($fcl_ids);$c++) {

		$fcl_id=$fcl_ids[$c];
	
		$tmp=cargar_registro("SELECT * FROM fap_checklist_detalle WHERE fap_id=$fap_id AND fcl_id=$fcl_id ORDER BY fcld_id DESC LIMIT 1;");
				
		//$pdf->SetY($top_y);
		//$pdf->SetLeftMargin($left_x+(112.5*$c));
		
		$pdf->SetFillColor(230,230,230);	
		$pdf->SetFont('Arial','BU', 12);
		$pdf->Cell(190,5,utf8_decode($titulos[$c]),1,1,'C',1);						
		
		if($tmp) {
		
			$fcld_id=$tmp['fcld_id']*1;
		
			$cl=cargar_registro("SELECT * FROM fap_checklist_detalle JOIN fap_checklist USING (fcl_id) JOIN funcionario USING (func_id) WHERE fcld_id=$fcld_id");
			
			$pdf->SetFont('Arial','I', 8);
			$pdf->Cell(190,5,"Registrado el ".substr($cl['fcld_fecha'],0,16)." por ".$cl['func_nombre'].".",1,1,'C',1);
			
			$campos=explode("\n",$cl['fcld_datos']);;
			$cmp_utl='';
			
			for($i=0;$i<sizeof($campos);$i++) {
				if(trim($campos[$i])=='') continue;
				$cmp=explode('|',$campos[$i]);
				$tipo=$cmp[2]*1;
				
				if($tipo==20) {
					$pdf->SetFillColor(240,240,240);	
					$pdf->SetFont('Arial','BI', 10);
					$pdf->Cell(190,5,$cmp[0],1,1,'C',1);						
				} else if($tipo==6) {
					$pdf->SetFillColor(250,250,250);	
					$pdf->SetFont('Arial','', 8);
					$pdf->Cell(100,4,$cmp[0].':',1,0,'R',1);
					$pdf->SetFont('Arial','', 9);
					$pdf->Cell(90,4,($cmp[1]!=''?' * ':'').str_replace('//',' * ',$cmp[1]),1,1,'L');
				} else if($tipo==0 OR $tipo==1) {
					$pdf->SetFillColor(250,250,250);	
					$pdf->SetFont('Arial','', 8);
					$pdf->Cell(100,4,$cmp[0].':',1,0,'R',1);
					$pdf->SetFont('Arial','', 9);
					
					if($cmp[1]=='S')
						$pdf->Cell(90,4,'SI',1,1,'L');
					elseif($cmp[1]=='N')
						$pdf->Cell(90,4,'NO',1,1,'L');
					else
						$pdf->Cell(90,4,'',1,1,'L');
					//$pdf->Cell(90,4,(($cmp[1]=='S')?'SI':'NO'),1,1,'L');
					$cmp_utl=$cmp[1];
				
				} else if($tipo!=10) {
					if($cmp[1]!=''){
						$pdf->SetFillColor(250,250,250);	
						$pdf->SetFont('Arial','', 8);
						$pdf->Cell(100,4,$cmp[0].':',1,0,'R',1);
						$pdf->SetFont('Arial','', 9);
						$pdf->Cell(90,4,$cmp[1],1,1,'L');
					}
					
					$cmp_utl=$cmp[1];
				
				} else {
				
					$lineas=explode('<br>',$cmp[1]);
				
					for($k=0;$k<sizeof($lineas);$k++) {
						$pdf->SetFillColor(250,250,250);	
						$pdf->SetFont('Arial','', 8);
						
						if($k==0)
							$pdf->Cell(100,4,$cmp[0].':',1,0,'R',1);
						else
							$pdf->Cell(100,4,'',1,0,'R',1);				
							
						$pdf->SetFont('Courier','', 9);
						$pdf->Cell(90,4,$lineas[$k],1,1,'L');
					}			
					$cmp_utl=$cmp[1];
				}
			}
		
		} else {
		
			$pdf->SetFont('Arial','I', 8);
			$pdf->Cell(190,5,"(No ha sido registrado)",1,1,'C',1);

		}
				
	}
	
	$pdf->Ln(15);

	$pdf->SetFont('Arial','',10);
			
	$pdf->Cell(95,5,($equipo[0]['pp4_nombre']),0,0,'C');
	$pdf->Cell(95,5,($equipo[0]['pp1_nombre']),0,1,'C');
	$pdf->Cell(95,5,'ANESTESIOLOGO',0,0,'C');
	$pdf->Cell(95,5,'CIRUJANO',0,1,'C');
	
	$pdf->Output('FAP_PAUSA_SEGUIDAD_'.$fap['fap_fnumero'].'.pdf','I');	
?>
