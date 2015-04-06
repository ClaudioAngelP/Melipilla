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
	
	
	$pdf=new FPDF('P','mm','Letter');
	
	$pdf->AddPage();
		
	$pdf->SetFillColor(250,250,250);	

	$pdf->SetFont('Arial','', 9);
		
	$pdf->Image('../../imagenes/logo.png', 160,5,36,22);

	$pdf->Cell(190,4,'Ministerio de Salud',0,1,'L');
	$pdf->Cell(190,4,'Servicio de Salud Metropolitano Occidente',0,1,'L');
	$pdf->Cell(190,4,'Hospital San José de Melipilla',0,1,'L');

	$pdf->SetFont('Arial','BU', 18);

	$pdf->Cell(190,9,'Protocolo Operatorio',0,1,'C');
	$pdf->Ln();

	//$pdf->SetFont('Arial','', 14);

	$fecha=explode('.',$fap['fap_fecha']);

	$pdf->SetFont('Arial','B',16);
	$pdf->Cell(55,6,'N° '.$fap['fap_fnumero'],0,0,'L');

	$pdf->SetFont('Arial','I',10);
	$pdf->Cell(40,6,'N° Cta. Cte: '.$fap['cta_cte'],0,0,'C');

	$pdf->SetFont('Arial','',10);

	$pdf->Cell(95,6,'Fecha: '.$fecha[0],0,1,'R');

	$pdf->SetFont('Arial','', 10);

	$pdf->Cell(47,5,'Nombre:',1,0,'R',1);
	$pdf->Cell(141,5,($fap['pac_id']!=0?trim($fap['pac_appat'].' '.$fap['pac_apmat'].' '.$fap['pac_nombres']):''),1,1,'L');

	$pdf->Cell(47,5,'Edad:',1,0,'R',1);
	$pdf->Cell(73,5,($fap['pac_id']!=0?$edad:''),1,0,'L');
	$pdf->Cell(32,5,'R.U.T.:',1,0,'R',1);
	$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['pac_rut']:''),1,1,'L');
			
	
	$tipovacio=0; $tmppab=$fap['fap_tipopab'];
	
	/*switch($fap['fap_tipopab']) {
		case 0: $fap['fap_tipopab']='NORMAL (ELECTIVO)'; break;
		case 1: $fap['fap_tipopab']='URGENCIAS'; break;
		case 2: $fap['fap_tipopab']='EXT. HORARIA'; break;
		default: $tipovacio=1; $fap['fap_tipopab']='[  ]ELECTIVO  /  [  ]URGENCIA'; break;
	}

	$pdf->Cell(47,8,'Modalidad Atención:',1,0,'R');

	if($tipovacio) $pdf->SetFont('Arial','', 10);
	$pdf->Cell(78,8,$fap['fap_tipopab'],1,0,'L');
	if($tipovacio)$pdf->SetFont('Arial','', 12);*/

	switch($fap['fap_subtipopab']) {
		case 0: $fap['fap_subtipopab']='AMBULATORIO'; break;
		case 1: $fap['fap_subtipopab']='HOSPITALIZADO'; break;
		default: $fap['fap_subtipopab']='[ ] HOSPITALIZADO / [ ] AMBULATORIO'; break;
	}
	
	$pdf->Cell(47,5,'Modo de Atención:',1,0,'R',1);
	$pdf->SetFont('Arial','', 10);
	$pdf->Cell(73,5,$fap['fap_subtipopab'],1,0,'L');
	$pdf->SetFont('Arial','', 10);


	$pdf->Cell(32,5,'Ficha Clínica:',1,0,'R',1);
	$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['pac_ficha']:''),1,1,'L');


	switch($fap['fap_tipopab']) {
		case 0: $fap['fap_tipopab']='PROGRAMADA'; break;
		case 1: $fap['fap_tipopab']='NO PROGRAMADA'; break;
		case 2: $fap['fap_tipopab']='URGENCIA'; break;
		case 3: $fap['fap_tipopab']='COMPRA SERVICIOS'; break;
		case 4: $fap['fap_tipopab']='EXTENCIÓN HORARIA'; break;
		case 5: $fap['fap_tipopab']='PRIVADA'; break;
		case 6: $fap['fap_tipopab']='PAB. ELECTIVO/PAB. EXTENDIDO'; break;
		default: $tipovacio=1; $fap['fap_tipopab']=''; break;
	}

	/* EGF */
	/**if (trim($fap['fap_tipopab']) == "") {

        $subtipo1 = "[  ] NORMAL (08:00-17:00) / [  ] EXT. HORARIA / [  ] PRIVADO";
        $subtipo2 = "[  ] INSTITUCIONAL / [  ] PRIVADO";

		$pdf->Cell(47,6,'I.Q. Electiva:',1,0,'R');
		$pdf->SetFont('Arial','', 13);
		$pdf->Cell(141,6,$subtipo1,1,1,'L');
		$pdf->SetFont('Arial','', 14);
		$pdf->Cell(47,6,'I.Q. Urgencia:',1,0,'R');
		$pdf->SetFont('Arial','', 13);
		$pdf->Cell(141,6,$subtipo2,1,1,'L');


    } else {

		$subtipo1 = trim($fap['fap_tipopab']);
        	
		$pdf->Cell(47,6,'Tipo Atención (I.Q.):',1,0,'R',1);
		$pdf->SetFont('Arial','', 14);
		$pdf->Cell(141,6,$subtipo1,1,1,'L');
        
    }*/
    
    $subtipo1 = trim($fap['fap_tipopab']);
        	
	$pdf->Cell(47,5,'Tipo Atención (I.Q.):',1,0,'R',1);
	$pdf->SetFont('Arial','', 10);
	$pdf->Cell(141,5,$subtipo1,1,1,'L');

    $pdf->SetFont('Arial','', 10);
	

	$pdf->Cell(47,5,'Servicio Orígen:',1,0,'R',1);
	$pdf->Cell(73,5,cut($fap['tcama_tipo'],25),1,0,'L');
	$pdf->Cell(32,5,'Previsión:',1,0,'R',1);
	$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['prev_desc']:''),1,1,'L');
	/* EGF *

	if($fap['fap_asa']*1==-1)
		$fap['fap_asa']='';

	/*$pdf->Cell(47,5,'Servicio Destino:',1,0,'R',1);
	$pdf->Cell(78,5,cut($fap['centro_nombre2'],25),1,0,'L');
	$pdf->Cell(27,5,'ASA (1-4):',1,0,'R',1);
	$pdf->Cell(36,5,$fap['fap_asa'],1,1,'L');*/

	/* EGF */
    //$pdf->Cell(47,6,'Especialidad:',1,0,'R',1);
	//$pdf->Cell(73,6,cut($fap['pp_desc'],25),1,0,'L');
    $pdf->Cell(47,5,'N° Pabellón:',1,0,'R',1);
	$pdf->Cell(141,5,$fap['fapp_desc'],1,1,'L');
	
	if($fap['fap_suspension']!=''){
		$pdf->Cell(47,5,'Suspensión:',1,0,'R',1);
		$pdf->Cell(141,5,$fap['fap_suspension'],1,1,'L');
	}

     /* EGF */

	$pdf->SetFillColor(240,240,240);	
	$pdf->SetFont('Arial','B', 10);
	$pdf->Cell(188,7,'DIAGNÓSTICOS PRE./POST. OPERATORIOS',1,1,'C',1);
	$pdf->SetFont('Arial','', 10);
	$pdf->SetFillColor(250,250,250);	


	$pdf->Cell(47,5,'Diag. Pre. Operatorio:',1,0,'R',1);
	$pdf->SetFont('Arial','', 10);
	$pdf->Cell(141,5,cut($fap['fap_diag_cod'],50),1,1,'L');
	$pdf->SetFont('Arial','', 10);
	if($fap['diag_desc_1']!=''){
		$pdf->Cell(47,5,'Diag. Post. Oper. (1):',1,0,'R',1);
		$pdf->SetFont('Arial','', 10);
		if($fap['diag_desc_1']!='')
			$pdf->Cell(141,5,'['.$fap['fap_diag_cod_1'].'] '.cut($fap['diag_desc_1'],45),1,1,'L');
		else 
			$pdf->Cell(141,5,'',1,1,'L');
	}
	
	if($fap['diag_desc_2']!=''){	
		$pdf->Cell(47,5,'Diag. Post. Oper. (2):',1,0,'R',1);
		if($fap['diag_desc_2']!='')
			$pdf->Cell(141,5,'['.$fap['fap_diag_cod_2'].'] '.cut($fap['diag_desc_2'],45),1,1,'L');
		else 
			$pdf->Cell(141,5,'',1,1,'L');
	}

	if($fap['diag_desc_3']!=''){
		$pdf->Cell(47,5,'Diag. Post. Oper. (3):',1,0,'R',1);
		if($fap['diag_desc_3']!='')
			$pdf->Cell(141,5,'['.$fap['fap_diag_cod_3'].'] '.cut($fap['diag_desc_3'],45),1,1,'L');
		else 
			$pdf->Cell(141,5,'',1,1,'L');
	}
		
	$pdf->SetFillColor(240,240,240);	
	$pdf->SetFont('Arial','B', 10);
	$pdf->Cell(188,5,'PRESTACIONES',1,1,'C',1);
	$pdf->SetFont('Arial','', 12);
	$pdf->SetFillColor(250,250,250);	

	for($i=0;$i<sizeof($presta);$i++) {
		if($presta[$i]['fappr_codigo']!=''){
			$pdf->Cell(35,5,$presta[$i]['fappr_codigo'],1,0,'C');
			$pdf->SetFont('Arial','', 10);
			$pdf->Cell(143,5,cut($presta[$i]['glosa'],70),1,0,'L');
			$pdf->Cell(10,5,$presta[$i]['fappr_cantidad'],1,1,'C');
		}
	}
	
	$pdf->SetFont('Arial','B', 10);

	$pdf->SetFillColor(240,240,240);	
	
	$pdf->Cell(46,5,'Horarios Flujo Paciente',1,0,'C',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->Cell(141,5,'EQUIPO QUIRÚRGICO',1,1,'C',1); //(N:Normal T:Turno C:Convenio P:Privado D:Docencia)

	$pdf->SetFont('Arial','', 9);
	$posy=$pdf->GetY();

	$pdf->SetFillColor(250,250,250);	

	$pdf->Cell(31,5,'Ingreso Pabellón:',1,0,'R',1);
	$pdf->Cell(15,5,$fap['fap_pab_hora1'],1,0,'C');
	$posx=$pdf->GetX();
	$pdf->Ln();

	//$pdf->Cell(31,5,'Ingreso Quirófano:',1,0,'R',1);
	//$pdf->Cell(15,5,$fap['fap_pab_hora2'],1,1,'C');

	$pdf->Cell(31,5,'Inicio Anestesia:',1,0,'R',1);
	$pdf->Cell(15,5,$fap['fap_pab_hora3'],1,1,'C');

	$pdf->Cell(31,5,'Inicio Intervención:',1,0,'R',1);
	$pdf->Cell(15,5,$fap['fap_pab_hora4'],1,1,'C');
	$pdf->SetFont('Arial','', 8);
	$pdf->Cell(31,5,'Término Intervención:',1,0,'R',1);
	$pdf->SetFont('Arial','', 9);
	$pdf->Cell(15,5,$fap['fap_pab_hora5'],1,1,'C');

	//$pdf->Cell(31,5,'Término Anestesia:',1,0,'R',1);
	//$pdf->Cell(15,5,$fap['fap_pab_hora6'],1,1,'C');

	$pdf->Cell(31,5,'Salida Pab.:',1,0,'R',1);
	$pdf->Cell(15,5,$fap['fap_pab_hora7'],1,1,'C');

	//$pdf->Cell(31,5,'Fin Aseo:',1,0,'R',1);
	//$pdf->Cell(15,5,$fap['fap_pab_hora8'],1,1,'C');

	/*
	if($fap['fap_eval_pre']=='-1') $fap['fap_eval_pre']='';
	elseif($fap['fap_eval_pre']=='-2') $fap['fap_eval_pre']='(S/D)';
	elseif($fap['fap_eval_pre']=='1') $fap['fap_eval_pre']='S';
	elseif($fap['fap_eval_pre']=='0') $fap['fap_eval_pre']='N';

	if($fap['fap_entrega_ane']=='-1') $fap['fap_entrega_ane']='';
	elseif($fap['fap_entrega_ane']=='1') $fap['fap_entrega_ane']='S';
	elseif($fap['fap_entrega_ane']=='0') $fap['fap_entrega_ane']='N';

	if($fap['fap_eva']=='-1') $fap['fap_eva']='';

	    if ($fap['fap_eval_pre'] == "")
          $fap['fap_eval_pre'] = "[ ]SI / [ ]NO";
        if ($fap['fap_entrega_ane'] == "")
          $fap['fap_entrega_ane'] = "[ ]SI / [ ]NO";
    
    $pdf->Cell(35,5,'Eval. Pre. Anestésica:',1,0,'R',1);
	$pdf->Cell(15,5,$fap['fap_eval_pre'],1,1,'C');
	$pdf->Cell(35,5,'Entrega Anestesista:',1,0,'R',1);
	$pdf->Cell(15,5,$fap['fap_entrega_ane'],1,1,'C');
	$pdf->Cell(35,5,'E.V.A. (1-10):',1,0,'R',1);
	$pdf->Cell(15,5,$fap['fap_eva'],1,1,'C');
	*/
/**
	$ppq=cargar_registros("select * from fap_equipo_quirurgico WHERE fap_id=$fap_id",true);
	
	$cantpp=0;
	$cantss=0;
	for($i=2;$i<=24;$i++){
		if($ppq[0][$i]*1>0){
			$cantpp++;
		}
	}
	
	$agCell = $cantpp-5;
	
	if($agCell>0) $agCell=$agCell-1;

	if($agCell>0)	
		for($i=0;$i<=($agCell);$i++){	
			$pdf->Cell(45,5,'',1,1,'C',0);
		}
	
	$pdf->SetXY($posx, $posy);*/
	$ppq=cargar_registros("select * from fap_equipo_quirurgico WHERE fap_id=$fap_id ORDER BY fapeq_id LIMIT 1",true);
	
	$cantpp=0;
	$cantss=0;
	for($i=2;$i<=24;$i++){
		if($ppq[0][$i]*1>0){
			$cantpp++;
		}
	}
	
	$agCell = $cantpp-5;
	
	//if($agCell==-1) $agCell=$agCell-1;
	
	if($agCell>0)	
		for($i=0;$i<=($agCell-1);$i++){	
			$pdf->Cell(46,5,'',1,1,'C',0);
		}
		
	$pdf->SetXY($posx, $posy);

if($equipo[0]['pp1_nombre']!=''){
	$pdf->Cell(24,5,'Cirujano:',1,0,'R',1);
	
	for($i=0;$i<2;$i++) {
		//$pdf->Cell(4,5,limpiaTipo($equipo[$i]['cir1_t'],$equipo[$i]['pp1_nombre']),1,0,'C');
		$pdf->SetFont('Arial','', 8);
        $pdf->Cell(59,5,cut2($equipo[$i]['pp1_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);
	
	$pdf->SetFont('Arial','', 9);
if($equipo[0]['pp2_nombre']!=''){
	$pdf->Cell(24,5,'Ayudante (1):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		//$pdf->Cell(4,5,limpiaTipo($equipo[$i]['cir2_t'],$equipo[$i]['pp2_nombre']),1,0,'C');
		$pdf->SetFont('Arial','', 8);
        $pdf->Cell(59,5,cut2($equipo[$i]['pp2_nombre']),1,0,'L');
	}
	$pdf->Ln();
}
	
	$pdf->SetX($posx);

	$pdf->SetFont('Arial','', 9);
if($equipo[0]['pp3_nombre']!=''){
	$pdf->Cell(24,5,'Ayudante (2):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		//$pdf->Cell(4,5,limpiaTipo($equipo[$i]['cir3_t'], $equipo[$i]['pp3_nombre']),1,0,'C');
		$pdf->SetFont('Arial','', 8);
		$pdf->Cell(59,5,cut2($equipo[$i]['pp3_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);

	$pdf->SetFont('Arial','', 9);
if($equipo[0]['pp12_nombre']!=''){
	$pdf->Cell(24,5,'Ayudante (3):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		//$pdf->Cell(4,5,limpiaTipo($equipo[$i]['cir4_t'], $equipo[$i]['pp12_nombre']),1,0,'C');
		$pdf->SetFont('Arial','', 8);
        $pdf->Cell(59,5,cut2($equipo[$i]['pp12_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);
	
	$pdf->SetFont('Arial','', 9);
if($equipo[0]['pp4_nombre']!=''){
	$pdf->Cell(24,5,'Anestesista (1):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
        //$pdf->Cell(4,5,limpiaTipo($equipo[$i]['ane1_t'], $equipo[$i]['pp4_nombre']),1,0,'C');
        $pdf->SetFont('Arial','', 8);
		$pdf->Cell(59,5,cut2($equipo[$i]['pp4_nombre']),1,0,'L');
	}
	$pdf->Ln();
}
	
	$pdf->SetX($posx);
	
	$pdf->SetFont('Arial','', 9);
if($equipo[0]['pp5_nombre']!=''){
	$pdf->Cell(24,5,'Anestesista (2):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
        //$pdf->Cell(4,5,limpiaTipo($equipo[$i]['ane2_t'], $equipo[$i]['pp5_nombre']),1,0,'C');
        $pdf->SetFont('Arial','', 8);
		$pdf->Cell(59,5,cut2($equipo[$i]['pp5_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);

	$pdf->SetFont('Arial','', 9);
if($equipo[0]['pp16_nombre']!=''){
	$pdf->Cell(24,5,'Enfer/Matrona:',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp16_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);

if($equipo[0]['pp6_nombre']!=''){
	$pdf->Cell(24,5,'Arsenalera (1):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp6_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);

if($equipo[0]['pp13_nombre']!=''){
	$pdf->Cell(24,5,'Arsenalera (2):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp13_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);

if($equipo[0]['pp7_nombre']!=''){
	$pdf->Cell(24,5,'Pabell. (1):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp7_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);

if($equipo[0]['pp14_nombre']!=''){
	$pdf->Cell(24,5,'Pabell. (2):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp14_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);
	
if($equipo[0]['pp8_nombre']!=''){
	$pdf->Cell(24,5,'Aux. Ane. (1):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp8_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	$pdf->SetX($posx);
	
if($equipo[0]['pp15_nombre']!=''){
	$pdf->Cell(24,5,'Aux. Ane. (2):',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp15_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

	/**
	$pdf->SetX($posx);

if($equipo[0]['pp10_nombre']!=''){
	$pdf->Cell(24,5,'Aux. Rayos:',1,0,'R',1);
	for($i=0;$i<2;$i++) {
		$pdf->Cell(59,5,cut2($equipo[$i]['pp10_nombre']),1,0,'L');
	}
	$pdf->Ln();
}

if($agCell<0){
	for($i=0;$i<abs($agCell);$i++){
		$pdf->SetX($posx);
		$pdf->Cell(142,5,'',1,1,'L');
	}
}*/

	$pdf->SetX($posx);
	
	if($equipo[0]['pp10_nombre']!=''){
		$pdf->Cell(24,5,'Aux. Rayos:',1,0,'R',1);
		for($i=0;$i<2;$i++) {
			$pdf->Cell(59,5,cut2($equipo[$i]['pp10_nombre']),1,0,'L');
		}
		$pdf->Ln();
	}
	
	///
	if($agCell<0){
		for($i=$agCell;$i<0;$i++){
			$pdf->SetX($posx); 
			$pdf->Cell(142,5,'',1,1,'L');
		}
	}
	
	if($agCell>=0) $pdf->Ln();

	if($fap['fap_biopsia']==1) $biopsia='RAPIDA';
	elseif($fap['fap_biopsia']==2) $biopsia='DIFERIDA';
	elseif($fap['fap_biopsia']==3) $biopsia='AMBAS';
	elseif($fap['fap_biopsia']==-2) $biopsia='(SIN DATO)';
	elseif($fap['fap_biopsia']==0) $biopsia='NO';
	else $biopsia='[ ] NO / [ ] RAPIDA / [ ] DIFERIDA / [ ] AMBAS';

	$pdf->Cell(38,5,'Tipo de Herida:',1,0,'R',1);

        /* EGF */
        $pdf->SetFont('Arial','', 10);
        $herida_op = "[ ]1 / [ ]2 / [ ]3 / [ ]4 / [ ]SIN HERIDA / [ ]NO CONSIGNADO ";
        if (trim($fap['fapth_desc']) != "")
	  $herida_op = trim($fap['fapth_desc']);
        $pdf->Cell(150,5,$herida_op,1,1,'L');
        $pdf->SetFont('Arial','', 9);
        /* EGF */

        $pdf->Cell(38,5,'Anestesia Principal:',1,0,'R',1);
	$pdf->Cell(150,5,($fap['fapta_desc1']!=''?$fap['fapta_desc1']:'(SIN DATO)'),1,1,'L');
	$pdf->Cell(38,5,'Anestesia Secundaria:',1,0,'R',1);
	$pdf->Cell(60,5,($fap['fapta_desc2']!=''?$fap['fapta_desc2']:'(SIN DATO)'),1,0,'L');
	$pdf->Cell(50,5,'Recuento Compresas Conforme:',1,0,'R',1);
	if($fap['fap_entrega_ane']==0){ $compresas = $compresas='NO'; }elseif($fap['fap_entrega_ane']==1){ $compresas='SI'; }elseif($fap['fap_entrega_ane']==2){ $compresas='NO CORRESPONDE'; }
	$pdf->Cell(40,5,($compresas),1,1,'L');
	
	
	$pdf->Cell(38,5,'Biopsia:',1,0,'R',1);
	$pdf->Cell(60,5,$biopsia,1,0,'L');  //150


	$pdf->Cell(50,5,'Reintervención:',1,0,'R',1);
	if($fap['fap_reoperado']=='t')
		$pdf->Cell(40,5,'Programada',1,0,'L');
	elseif($fap['fap_reoperado']=='f')
		$pdf->Cell(40,5,'No Programada',1,0,'L');
	else
		$pdf->Cell(40,5,'No',1,0,'L');


	$pdf->Ln();
	/**
	if($fap['fap_hallazgos']!='' OR $fap['fap_protocolo']!=''){
		$pdf->addPage();
			$pdf->SetFillColor(250,250,250);	

			$pdf->SetFont('Arial','', 9);
				
			$pdf->Image('../../imagenes/logo.png', 160,5,40,28);

			$pdf->Cell(190,4,'Ministerio de Salud',0,1,'L');
			$pdf->Cell(190,4,'Servicio de Salud Metropolitano Occidente',0,1,'L');
			$pdf->Cell(190,4,'Hospital San José de Melipilla',0,1,'L');

			$pdf->SetFont('Arial','BU', 23);

			$pdf->Cell(190,9,'Protocolo Operatorio',0,1,'C');
			$pdf->Ln();
	}*/
	
		
	if($fap['fap_hallazgos']!='') {
	$pdf->SetFont('Arial','BU', 10);
	$pdf->Cell(188,5,'Hallazgos Intraoperatorios',1,1,'C',1);
	
	$pdf->SetFont('Arial','', 9);
	$pdf->Multicell(188,4,$fap['fap_hallazgos'],1,'J',0);
	}

	if($fap['fap_protocolo']!='') {
	$pdf->SetFont('Arial','BU', 10);
	$pdf->Cell(188,5,'Descripción Operación',1,1,'C',1);

	$pdf->SetFont('Arial','', 9);
	$pdf->Multicell(188,4,$fap['fap_protocolo'],1,'J',0);
	}	
	
	$pdf->Ln();

	/*$pdf->Cell(95,6,'______________________________',0,0,'C');
	$pdf->Cell(95,6,'______________________________',0,1,'C');
	$pdf->Cell(95,6,'FIRMA CIRUJANO',0,0,'C');
	$pdf->Cell(95,6,'FIRMA ANESTESISTA',0,1,'C');*/
	
	$pdf->Cell(95,4,'',0,0,'C');
	$pdf->Cell(95,4,'______________________________',0,1,'C');
	$pdf->Cell(95,4,'',0,0,'C');
	$pdf->Cell(95,4,($equipo[0]['pp1_nombre']),0,1,'C');
	$pdf->Cell(95,4,'',0,0,'C');
	$pdf->Cell(95,4,'FIRMA CIRUJANO',0,1,'C');
	

	
	if($fap['fap_indicaciones']!='' OR $fap['fap_indicaciones_anestesia']!='') {

		$pdf->AddPage();
			
		$pdf->SetFillColor(250,250,250);	

		$pdf->SetFont('Arial','', 9);

		$pdf->Image('../../imagenes/logo.png', 160,5,36,22);

		$pdf->Cell(190,4,'Ministerio de Salud',0,1,'L');
		$pdf->Cell(190,4,'Servicio de Salud Metropolitano Occidente',0,1,'L');
		$pdf->Cell(190,4,'Hospital San José de Melipilla',0,1,'L');

		$pdf->SetFont('Arial','BU', 20);

		$pdf->Cell(190,9,'Protocolo Operatorio (Indicaciones)',0,1,'C');
		$pdf->Ln();

		$pdf->SetFont('Arial','', 12);

		$fecha=explode('.',$fap['fap_fecha']);

		$pdf->SetFont('Arial','B',18);
		$pdf->Cell(55,7,'N° '.$fap['fap_fnumero'],0,0,'L');

		$pdf->SetFont('Arial','I',12);
		$pdf->Cell(40,7,'N° Cta. Cte: '.$fap['cta_cte'],0,0,'C');

		$pdf->SetFont('Arial','',12);

		$pdf->Cell(95,7,'Fecha: '.$fecha[0],0,1,'R');

		$pdf->SetFont('Arial','', 12);

		$pdf->Cell(47,5,'Nombre:',1,0,'R',1);
		$pdf->Cell(141,5,($fap['pac_id']!=0?trim($fap['pac_appat'].' '.$fap['pac_apmat'].' '.$fap['pac_nombres']):''),1,1,'L');

		$pdf->Cell(47,5,'Edad:',1,0,'R',1);
		$pdf->Cell(78,5,($fap['pac_id']!=0?$edad:''),1,0,'L');
		$pdf->Cell(27,5,'R.U.T.:',1,0,'R',1);
		$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['pac_rut']:''),1,1,'L');
				
		
		$tipovacio=0; $tmppab=$fap['fap_tipopab'];
		
		/*switch($fap['fap_tipopab']) {
			case 0: $fap['fap_tipopab']='NORMAL (ELECTIVO)'; break;
			case 1: $fap['fap_tipopab']='URGENCIAS'; break;
			case 2: $fap['fap_tipopab']='EXT. HORARIA'; break;
			default: $tipovacio=1; $fap['fap_tipopab']='[  ]ELECTIVO  /  [  ]URGENCIA'; break;
		}

		$pdf->Cell(47,8,'Modalidad Atención:',1,0,'R');

		if($tipovacio) $pdf->SetFont('Arial','', 10);
		$pdf->Cell(78,8,$fap['fap_tipopab'],1,0,'L');
		if($tipovacio)$pdf->SetFont('Arial','', 12);*/

		/**switch($fap['fap_subtipopab']) {
			case 0: $fap['fap_subtipopab']='AMBULATORIO'; break;
			case 1: $fap['fap_subtipopab']='HOSPITALIZADO'; break;
			default: $fap['fap_subtipopab']='[ ] HOSPITALIZADO / [ ] AMBULATORIO'; break;
		}*/
		
		$pdf->Cell(47,5,'Modo de Atención:',1,0,'R',1);
		$pdf->SetFont('Arial','', 10);
		$pdf->Cell(78,5,$fap['fap_subtipopab'],1,0,'L');
		$pdf->SetFont('Arial','', 12);


		$pdf->Cell(27,5,'Ficha Clínica:',1,0,'R',1);
		$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['pac_ficha']:''),1,1,'L');


		switch($fap['fap_tipopab']) {
			case 0: $fap['fap_tipopab']='PROGRAMADA'; break;
			case 1: $fap['fap_tipopab']='NO PROGRAMADA'; break;
			case 2: $fap['fap_tipopab']='URGENCIA'; break;
			case 3: $fap['fap_tipopab']='COMPRA SERVICIOS'; break;
			case 4: $fap['fap_tipopab']='EXTENCIÓN HORARIA'; break;
			case 5: $fap['fap_tipopab']='PRIVADA'; break;
			case 6: $fap['fap_tipopab']='PAB. ELECTIVO/PAB. EXTENDIDO'; break;
			default: $tipovacio=1; $fap['fap_tipopab']=''; break;
		}	

		/* EGF */
		/**if (trim($fap['fap_tipopab']) == "") {

			$subtipo1 = "[  ] NORMAL (08:00-17:00) / [  ] EXT. HORARIA / [  ] PRIVADO";
			$subtipo2 = "[  ] INSTITUCIONAL / [  ] PRIVADO";

			$pdf->Cell(47,5,'I.Q. Electiva:',1,0,'R');
			$pdf->SetFont('Arial','', 11);
			$pdf->Cell(141,5,$subtipo1,1,1,'L');
			$pdf->SetFont('Arial','', 12);
			$pdf->Cell(47,5,'I.Q. Urgencia:',1,0,'R');
			$pdf->SetFont('Arial','', 11);
			$pdf->Cell(141,5,$subtipo2,1,1,'L');


		} else {

			$subtipo1 = trim($fap['fap_tipopab']);
				
			$pdf->Cell(47,5,'Tipo Atención (I.Q.):',1,0,'R',1);
			$pdf->SetFont('Arial','', 12);
			$pdf->Cell(141,5,$subtipo1,1,1,'L');
			
		}*/
		
		//$subtipo1 = trim($fap['fap_tipopab']);
        	
		$pdf->Cell(47,6,'Tipo Atención (I.Q.):',1,0,'R',1);
		$pdf->SetFont('Arial','', 12);
		$pdf->Cell(141,6,$subtipo1,1,1,'L');

		$pdf->SetFont('Arial','', 12);
		

		$pdf->Cell(47,5,'Servicio Orígen:',1,0,'R',1);
		$pdf->Cell(78,5,cut($fap['tcama_tipo'],35),1,0,'L');
		$pdf->Cell(27,5,'Previsión:',1,0,'R',1);
		$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['prev_desc']:''),1,1,'L');
		/* EGF */

		if($fap['fap_asa']*1==-1)
			$fap['fap_asa']='';

		/*$pdf->Cell(47,5,'Servicio Destino:',1,0,'R',1);
		$pdf->Cell(78,5,cut($fap['centro_nombre2'],25),1,0,'L');
		$pdf->Cell(27,5,'ASA (1-4):',1,0,'R',1);
		$pdf->Cell(36,5,$fap['fap_asa'],1,1,'L');*/

		/* EGF */
		//$pdf->Cell(47,5,'Especialidad:',1,0,'R',1);
		//$pdf->Cell(78,5,cut($fap['pp_desc'],25),1,0,'L');
		$pdf->Cell(47,5,'N° Pabellón:',1,0,'R',1);
		$pdf->Cell(141,5,$fap['fapp_desc'],1,1,'L');

		 /* EGF */

		$pdf->SetFillColor(240,240,240);	
		$pdf->SetFont('Arial','B', 12);
		$pdf->Cell(188,6,'DIAGNÓSTICOS PRE./POST. OPERATORIOS',1,1,'C',1);
		$pdf->SetFont('Arial','', 12);
		$pdf->SetFillColor(250,250,250);	


		$pdf->Cell(47,5,'Diag. Pre. Operatorio:',1,0,'R',1);
		$pdf->Cell(141,5,cut($fap['fap_diag_cod'],50),1,1,'L');
		
		if($fap['diag_desc_1']!=''){
			$pdf->Cell(47,5,'Diag. Post. Oper. (1):',1,0,'R',1);
			if($fap['diag_desc_1']!='')
				$pdf->Cell(141,5,'['.$fap['fap_diag_cod_1'].'] '.cut($fap['diag_desc_1'],45),1,1,'L');
			else 
				$pdf->Cell(141,5,'',1,1,'L');
		}
		
		if($fap['diag_desc_2']!=''){	
			$pdf->Cell(47,5,'Diag. Post. Oper. (2):',1,0,'R',1);
			if($fap['diag_desc_2']!='')
				$pdf->Cell(141,5,'['.$fap['fap_diag_cod_2'].'] '.cut($fap['diag_desc_2'],45),1,1,'L');
			else 
				$pdf->Cell(141,5,'',1,1,'L');
		}

		if($fap['diag_desc_3']!=''){
			$pdf->Cell(47,5,'Diag. Post. Oper. (3):',1,0,'R',1);
			if($fap['diag_desc_3']!='')
				$pdf->Cell(141,5,'['.$fap['fap_diag_cod_3'].'] '.cut($fap['diag_desc_3'],45),1,1,'L');
			else 
				$pdf->Cell(141,5,'',1,1,'L');
		}
					
		$pdf->SetFillColor(240,240,240);	
		$pdf->SetFont('Arial','B', 12);
		$pdf->Cell(188,6,'PRESTACIONES',1,1,'C',1);
		$pdf->SetFont('Arial','', 12);
		$pdf->SetFillColor(250,250,250);	
				
		for($i=0;$i<sizeof($presta);$i++) {
			if($presta[$i]['fappr_codigo']!=''){
				$pdf->Cell(35,5,$presta[$i]['fappr_codigo'],1,0,'C');
				$pdf->Cell(143,5,cut($presta[$i]['glosa'],50),1,0,'L');
				$pdf->Cell(10,5,$presta[$i]['fappr_cantidad'],1,1,'C');
			}
		}
		
		$pdf->SetFont('Arial','B', 12);
	
		if($fap['fap_indicaciones']!='') {
			$pdf->SetFont('Arial','BU', 12);
			$pdf->Cell(188,6,'Indicaciones',1,1,'C',1);
			
			$tmp_y=$pdf->GetY();
			
			$l_text = explode("\n", $fap['fap_indicaciones']);
			$lineas_indicadas = count($l_text)*1;
			
			for($i=0;$i<$lineas_indicadas;$i++) {
				$pdf->Cell(108,6,'',1,0,'C');
				for($j=0;$j<10;$j++) {
					$pdf->Cell(8,6,'',1,0,'C');
				}
				$pdf->Ln();
			}

			$tmp_y2=$pdf->GetY();
			$pdf->SetY($tmp_y);

			$pdf->SetFont('Arial','', 8);
			$pdf->Multicell(188,6,$fap['fap_indicaciones'],0,'J',0);

			$pdf->SetY($tmp_y2);
			
		}
		
		
		if($fap['fap_indicaciones_anestesia']!='') {
			$pdf->SetFont('Arial','BU', 12);
			$pdf->Cell(188,6,'Indicaciones Anestesia',1,1,'C',1);

			$tmp_y=$pdf->GetY();
			
			$l_text_a = explode("\n", $fap['fap_indicaciones_anestesia']);
			$lineas_indicadas_a = count($l_text_a)*1;
			
			for($i=0;$i<$lineas_indicadas_a;$i++) {
				$pdf->Cell(88,6,'',1,0,'C');
				for($j=0;$j<10;$j++) {
					$pdf->Cell(10,6,'',1,0,'C');
				}
				$pdf->Ln();
			}
			
			$tmp_y2=$pdf->GetY();
			$pdf->SetY($tmp_y);
			
			$pdf->SetFont('Arial','', 10);
			$pdf->Multicell(188,6,$fap['fap_indicaciones_anestesia'],0,'J',0);

			$pdf->SetY($tmp_y2);

		}
		
		

		$pdf->Ln(20);

		$pdf->Cell(95,4,'______________________________',0,0,'C');
		$pdf->Cell(95,4,'______________________________',0,1,'C');
		$pdf->Cell(95,4,($equipo[0]['pp4_nombre']),0,0,'C');
		$pdf->Cell(95,4,($equipo[0]['pp1_nombre']),0,1,'C');
		$pdf->Cell(95,4,'FIRMA ANESTESIOLOGO',0,0,'C');
		$pdf->Cell(95,4,'FIRMA CIRUJANO',0,1,'C');
		
	}
	/**
	$pdf->AddPage();
			
	$pdf->SetFillColor(250,250,250);	

	$pdf->SetFont('Arial','', 9);

	$pdf->Image('../../imagenes/logo.png', 160,5,40,28);

	$pdf->Cell(190,4,'Ministerio de Salud',0,1,'L');
	$pdf->Cell(190,4,'Servicio de Salud Metropolitano Occidente',0,1,'L');
	$pdf->Cell(190,4,'Hospital San José de Melipilla',0,1,'L');

	$pdf->SetFont('Arial','BU', 20);

	$pdf->Cell(190,9,'Pausa de Seguridad Quirúrgica',0,1,'C');
	$pdf->Ln(5);

	$pdf->SetFont('Arial','B',11);

	$pdf->Cell(190,5,'Nro. Folio: '.$fap['fap_fnumero'].' | Fecha Intervención: '.substr($fap['fap_fecha'],0,10),0,1,'C');
	$pdf->Cell(190,5,'RUN: '.formato_rut($fap['pac_rut']).' | FICHA: '.$fap['pac_ficha'].' | Cta. Cte.:'.$fap['cta_cte'],0,1,'C');

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
		$pdf->Cell(190,5,$titulos[$c],1,1,'C',1);						
		
		if($tmp) {
		
			$fcld_id=$tmp['fcld_id']*1;
		
			$cl=cargar_registro("SELECT * FROM fap_checklist_detalle JOIN fap_checklist USING (fcl_id) JOIN funcionario USING (func_id) WHERE fcld_id=$fcld_id");
			
			$pdf->SetFont('Arial','I', 8);
			$pdf->Cell(190,5,"Registrado el ".substr($cl['fcld_fecha'],0,16)." por ".$cl['func_nombre'].".",1,1,'C',1);
			
			$campos=explode("\n",$cl['fcld_datos']);;
			
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
					$pdf->Cell(90,4,(($cmp[1]=='S')?'SI':'NO'),1,1,'L');
				
				} else if($tipo!=10) {
					$pdf->SetFillColor(250,250,250);	
					$pdf->SetFont('Arial','', 8);
					$pdf->Cell(100,4,$cmp[0].':',1,0,'R',1);
					$pdf->SetFont('Arial','', 9);
					$pdf->Cell(90,4,$cmp[1],1,1,'L');
				
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
	*/
	
	$pdf->Output('FAP_PROTOCOLO_PABELLON_'.$fap['fap_fnumero'].'.pdf','I');	


  function limpiaTipo($tipo, $profesional)
  {
    if ($profesional != "")
    {
      if ($tipo == 0)
        return "N";
      else if ($tipo == 1)
        return "T";
      else if ($tipo == 2)
        return "C";
      else if ($tipo == 3)
        return "P";
      else if ($tipo == 4)
        return "D";
    }
    else
      $tipo = "";
    return $tipo;
  }
?>
