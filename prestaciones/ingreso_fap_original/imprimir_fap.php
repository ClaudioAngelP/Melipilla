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

	function cut2($str) { return cut($str, 20); }
	
	if(isset($_GET['fap_id'])) $fap_id=$_GET['fap_id']*1;
	elseif(isset($_GET['fappr_id'])) { 
		$q=pg_query("SELECT * FROM fap_prestacion WHERE fappr_id=".($_GET['fappr_id']*1));
		$r=pg_fetch_assoc($q);
		$fap_id=$r['fap_id']*1;
	}
	
	$fap=cargar_registro("
		SELECT
		fap_pabellon.*, fappab_pabellones.*,
		th.*, pacientes.*, prevision.*, p1.*,
		ta1.fapta_id AS fapta_id1,
		ta1.fapta_desc AS fapta_desc1,
		ta2.fapta_id AS fapta_id2,
		ta2.fapta_desc AS fapta_desc2,
		d0.diag_desc AS diag_desc, 		
		d1.diag_desc AS diag_desc_1, 		
		d2.diag_desc AS diag_desc_2, 		
		d3.diag_desc AS diag_desc_3,
		c1.centro_nombre AS centro_nombre,
		c2.centro_nombre AS centro_nombre2,
   	date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
		'' AS edad 		 
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
		LEFT JOIN centro_costo AS c1 ON fap_pabellon.centro_ruta=c1.centro_ruta
		LEFT JOIN centro_costo AS c2 ON fap_pabellon.centro_ruta2=c2.centro_ruta
		LEFT JOIN fap_equipo_quirurgico ON fap_pabellon.fap_id=fap_equipo_quirurgico.fap_id AND fapeq_num=0
		LEFT JOIN personal_pabellon AS p1 ON p1.pp_id=cir1 		
		WHERE fap_pabellon.fap_id=$fap_id	
	");

	$presta=cargar_registros_obj("
		SELECT * FROM fap_prestacion 
		LEFT JOIN codigos_prestacion ON fappr_codigo=codigo
		WHERE fap_id=$fap_id	
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
			p11.pp_paterno || ' ' || p11.pp_materno || ' ' || p11.pp_nombres AS pp11_nombre
			  
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
		
	$pdf->SetFillColor(200,200,200);	

	$pdf->SetFont('Arial','', 9);

	$pdf->Cell(190,4,'Ministerio de Salud',0,1,'L');
	$pdf->Cell(190,4,'Serv. de Salud Vi�a del Mar - Quillota',0,1,'L');
	$pdf->Cell(190,4,'Hospital Dr. Gustavo Fricke',0,1,'L');

	$pdf->SetFont('Arial','', 14);

	$pdf->Cell(190,7,'FAP - UAPQ',0,1,'C');
	$pdf->Ln();

	$pdf->SetFont('Arial','', 14);

	$fecha=explode('.',$fap['fap_fecha']);

	$pdf->SetFont('Arial','B',18);
	$pdf->Cell(55,7,'N� '.$fap['fap_fnumero'],0,0,'L');

	$pdf->SetFont('Arial','I',12);
	$pdf->Cell(40,7,'N� Hoja Cargo: '.$fap['fap_hoja_cargo'],0,0,'C');

	$pdf->SetFont('Arial','',14);

	$pdf->Cell(95,7,'Fecha: '.$fecha[0],0,1,'R');

	$pdf->SetFont('Arial','', 12);

	$pdf->Cell(47,7,'Nombre:',1,0,'R');
	$pdf->Cell(141,7,($fap['pac_id']!=0?trim($fap['pac_appat'].' '.$fap['pac_apmat'].' '.$fap['pac_nombres']):''),1,1,'L');

	$pdf->Cell(47,7,'Edad:',1,0,'R');
	$pdf->Cell(78,7,($fap['pac_id']!=0?$edad:''),1,0,'L');
	$pdf->Cell(27,7,'R.U.T.:',1,0,'R');
	$pdf->Cell(36,7,($fap['pac_id']!=0?$fap['pac_rut']:''),1,1,'L');
			
	
	$tipovacio=0; $tmppab=$fap['fap_tipopab'];
	
	/*switch($fap['fap_tipopab']) {
		case 0: $fap['fap_tipopab']='NORMAL (ELECTIVO)'; break;
		case 1: $fap['fap_tipopab']='URGENCIAS'; break;
		case 2: $fap['fap_tipopab']='EXT. HORARIA'; break;
		default: $tipovacio=1; $fap['fap_tipopab']='[  ]ELECTIVO  /  [  ]URGENCIA'; break;
	}

	$pdf->Cell(47,8,'Modalidad Atenci�n:',1,0,'R');

	if($tipovacio) $pdf->SetFont('Arial','', 10);
	$pdf->Cell(78,8,$fap['fap_tipopab'],1,0,'L');
	if($tipovacio)$pdf->SetFont('Arial','', 12);*/

	switch($fap['fap_subtipopab']) {
		case 0: $fap['fap_subtipopab']='AMBULATORIO'; break;
		case 1: $fap['fap_subtipopab']='HOSPITALIZADO'; break;
		default: $fap['fap_subtipopab']='[ ] HOSPITALIZADO / [ ] AMBULATORIO'; break;
	}
	
	$pdf->Cell(47,8,'Modo de Atenci�n:',1,0,'R');
	$pdf->SetFont('Arial','', 10);
	$pdf->Cell(78,8,$fap['fap_subtipopab'],1,0,'L');
	$pdf->SetFont('Arial','', 12);


	$pdf->Cell(27,8,'Ficha Cl�nica:',1,0,'R');
	$pdf->Cell(36,8,($fap['pac_id']!=0?$fap['pac_ficha']:''),1,1,'L');


	switch($fap['fap_tipopab']) {
		case 0: $fap['fap_tipopab']='NORMAL'; break;
		case 1: $fap['fap_tipopab']='URGENCIAS'; break;
		case 2: $fap['fap_tipopab']='EXT. HORARIA'; break;
		case 3: $fap['fap_tipopab']='PRIVADO ELECTIVO'; break;
		case 4: $fap['fap_tipopab']='PRIVADO URGENCIA'; break;
		default: $tipovacio=1; $fap['fap_tipopab']=''; break;
	}	

	/* EGF */
	if (trim($fap['fap_tipopab']) == "") {

        $subtipo1 = "[  ] NORMAL (08:00-17:00) / [  ] EXT. HORARIA / [  ] PRIVADO";
        $subtipo2 = "[  ] INSTITUCIONAL / [  ] PRIVADO";

		$pdf->Cell(47,8,'I.Q. Electiva:',1,0,'R');
		$pdf->SetFont('Arial','', 11);
		$pdf->Cell(141,8,$subtipo1,1,1,'L');
		$pdf->SetFont('Arial','', 12);
		$pdf->Cell(47,8,'I.Q. Urgencia:',1,0,'R');
		$pdf->SetFont('Arial','', 11);
		$pdf->Cell(141,8,$subtipo2,1,1,'L');


    } else {

		$subtipo1 = trim($fap['fap_tipopab']);
        	
		$pdf->Cell(47,8,'Tipo Atenci�n (I.Q.):',1,0,'R');
		$pdf->SetFont('Arial','', 12);
		$pdf->Cell(141,8,$subtipo1,1,1,'L');
        
    }

    $pdf->SetFont('Arial','', 12);
	

	$pdf->Cell(47,8,'Servicio Or�gen:',1,0,'R');
	$pdf->Cell(78,8,cut($fap['centro_nombre'],25),1,0,'L');
	$pdf->Cell(27,8,'Previsi�n:',1,0,'R');
	$pdf->Cell(36,8,($fap['pac_id']!=0?$fap['prev_desc']:''),1,1,'L');
	/* EGF */

	if($fap['fap_asa']*1==-1)
		$fap['fap_asa']='';

	$pdf->Cell(47,8,'Servicio Destino:',1,0,'R');
	$pdf->Cell(78,8,cut($fap['centro_nombre2'],25),1,0,'L');
	$pdf->Cell(27,8,'ASA (1-4):',1,0,'R');
	$pdf->Cell(36,8,$fap['fap_asa'],1,1,'L');

	/* EGF */
    $pdf->Cell(47,7,'Especialidad:',1,0,'R');
	$pdf->Cell(78,7,cut($fap['pp_desc'],25),1,0,'L');
    $pdf->Cell(27,7,'N� Pabell�n:',1,0,'R');
	$pdf->Cell(36,7,$fap['fapp_desc'],1,1,'L');

	$pdf->Cell(47,7,'Suspensi�n de FAP:',1,0,'R');
	$pdf->Cell(141,7,cut($fap['fap_suspension'],25),1,1,'L');
    
    
        /* EGF */

	$pdf->SetFont('Arial','B', 12);
	$pdf->Cell(188,8,'DIAGN�STICOS PRE./POST. OPERATORIOS',1,1,'C');
	$pdf->SetFont('Arial','', 12);


	$pdf->Cell(47,8,'Diag. Pre. Operatorio:',1,0,'R');
	$pdf->Cell(141,8,cut($fap['fap_diag_cod'],50),1,1,'L');
	
	$pdf->Cell(47,8,'Diag. Post. Oper. (1):',1,0,'R');
	if($fap['fap_diag_cod_1']!='')
		$pdf->Cell(141,8,'['.$fap['fap_diag_cod_1'].'] '.cut($fap['diag_desc_1'],45),1,1,'L');
	else 
		$pdf->Cell(141,8,'',1,1,'L');
		
	$pdf->Cell(47,8,'Diag. Post. Oper. (2):',1,0,'R');
	if($fap['fap_diag_cod_2']!='')
		$pdf->Cell(141,8,'['.$fap['fap_diag_cod_2'].'] '.cut($fap['diag_desc_2'],45),1,1,'L');
	else 
		$pdf->Cell(141,8,'',1,1,'L');

	$pdf->Cell(47,8,'Diag. Post. Oper. (3):',1,0,'R');
	if($fap['fap_diag_cod_3']!='')
		$pdf->Cell(141,8,'['.$fap['fap_diag_cod_3'].'] '.cut($fap['diag_desc_3'],45),1,1,'L');
	else 
		$pdf->Cell(141,8,'',1,1,'L');

	$pdf->SetFont('Arial','B', 12);
	$pdf->Cell(188,8,'PRESTACIONES',1,1,'C');
	$pdf->SetFont('Arial','', 12);

	for($i=0;$i<3;$i++) {
		$pdf->Cell(35,8,$presta[$i]['fappr_codigo'],1,0,'C');
		$pdf->Cell(143,8,cut($presta[$i]['glosa'],50),1,0,'L');
		$pdf->Cell(10,8,$presta[$i]['fappr_cantidad'],1,1,'C');
	}

	//$pdf->Ln();
	
	$pdf->SetFont('Arial','B', 12);

	$pdf->Cell(50,8,'Horarios Flujo Paciente',1,0,'C');
        $pdf->SetFont('Arial','B', 10);
        $pdf->Cell(138,8,'EQUIPO QUIR�RGICO    (N:Normal T:Turno C:Convenio P:Privado D:Docencia)',1,1,'C');

	$pdf->SetFont('Arial','', 8);
	$posy=$pdf->GetY();

	$pdf->Cell(35,8,'Ingreso Pabell�n:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora1'],1,0,'C');
	$posx=$pdf->GetX();
	$pdf->Ln();

	$pdf->Cell(35,8,'Ingreso Quir�fano:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora2'],1,1,'c');

	$pdf->Cell(35,8,'Inicio Anestesia:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora3'],1,1,'C');

	$pdf->Cell(35,8,'Inicio Intervenci�n:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora4'],1,1,'C');

	$pdf->Cell(35,8,'T�rmino Intervenci�n:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora5'],1,1,'C');

	$pdf->Cell(35,8,'T�rmino Anestesia:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora6'],1,1,'C');

	$pdf->Cell(35,8,'Salida Pab./Ing. Recup.:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora7'],1,1,'C');

	$pdf->Cell(35,8,'Salida Recuperaci�n:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_pab_hora8'],1,1,'C');

	if($fap['fap_eval_pre']=='-1') $fap['fap_eval_pre']='';
	elseif($fap['fap_eval_pre']=='-2') $fap['fap_eval_pre']='(S/D)';
	elseif($fap['fap_eval_pre']=='1') $fap['fap_eval_pre']='S';
	elseif($fap['fap_eval_pre']=='0') $fap['fap_eval_pre']='N';

	if($fap['fap_entrega_ane']=='-1') $fap['fap_entrega_ane']='';
	elseif($fap['fap_entrega_ane']=='1') $fap['fap_entrega_ane']='S';
	elseif($fap['fap_entrega_ane']=='0') $fap['fap_entrega_ane']='N';

	if($fap['fap_eva']=='-1') $fap['fap_eva']='';

	/* EGF */
        if ($fap['fap_eval_pre'] == "")
          $fap['fap_eval_pre'] = "[ ]SI / [ ]NO";
        if ($fap['fap_entrega_ane'] == "")
          $fap['fap_entrega_ane'] = "[ ]SI / [ ]NO";
    /* EGF */

        $pdf->Cell(35,8,'Eval. Pre. Anest�sica:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_eval_pre'],1,1,'C');
	$pdf->Cell(35,8,'Entrega Anestesista:',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_entrega_ane'],1,1,'C');
	$pdf->Cell(35,8,'E.V.A. (1-10):',1,0,'R');
	$pdf->Cell(15,8,$fap['fap_eva'],1,1,'C');

	$pdf->SetXY($posx, $posy);

	$pdf->Cell(18,8,'Cirujano (1):',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(6,8,limpiaTipo($equipo[$i]['cir1_t'],$equipo[$i]['pp1_nombre']),1,0,'C');
                $pdf->Cell(34,8,cut2($equipo[$i]['pp1_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Cirujano (2):',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(6,8,limpiaTipo($equipo[$i]['cir2_t'],$equipo[$i]['pp2_nombre']),1,0,'C');
                $pdf->Cell(34,8,cut2($equipo[$i]['pp2_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Cirujano (3):',1,0,'R');
	for($i=0;$i<3;$i++) {
                $pdf->Cell(6,8,limpiaTipo($equipo[$i]['cir3_t'], $equipo[$i]['pp3_nombre']),1,0,'C');
		$pdf->Cell(34,8,cut2($equipo[$i]['pp3_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Anest. (1):',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(6,8,limpiaTipo($equipo[$i]['ane1_t'], $equipo[$i]['pp4_nombre']),1,0,'C');
                $pdf->Cell(34,8,cut2($equipo[$i]['pp4_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Anest. (2):',1,0,'R');
	for($i=0;$i<3;$i++) {
                $pdf->Cell(6,8,limpiaTipo($equipo[$i]['ane2_t'], $equipo[$i]['pp5_nombre']),1,0,'C');
		$pdf->Cell(34,8,cut2($equipo[$i]['pp5_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Instrument.:',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(40,8,cut2($equipo[$i]['pp6_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Pabellonero:',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(40,8,cut2($equipo[$i]['pp7_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Tec. Anes.:',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(40,8,cut2($equipo[$i]['pp8_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Tec. Perf.:',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(40,8,cut2($equipo[$i]['pp9_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Tec. Rayos:',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(40,8,cut2($equipo[$i]['pp10_nombre']),1,0,'L');
	}
	$pdf->Ln();

	$pdf->SetX($posx);

	$pdf->Cell(18,8,'Tec. Recup.:',1,0,'R');
	for($i=0;$i<3;$i++) {
		$pdf->Cell(40,8,cut2($equipo[$i]['pp11_nombre']),1,0,'L');
	}
	$pdf->Ln();

	if($fap['fap_biopsia']==1) $biopsia='RAPIDA';
	elseif($fap['fap_biopsia']==2) $biopsia='DIFERIDA';
	elseif($fap['fap_biopsia']==3) $biopsia='AMBAS';
	elseif($fap['fap_biopsia']==-2) $biopsia='(SIN DATO)';
	elseif($fap['fap_biopsia']==0) $biopsia='NO';
	else $biopsia='[ ] NO / [ ] RAPIDA / [ ] DIFERIDA / [ ] AMBAS';

	$pdf->Cell(38,7,'Tipo de Herida:',1,0,'R');

        /* EGF */
        $pdf->SetFont('Arial','', 10);
        $herida_op = "[ ]1 / [ ]2 / [ ]3 / [ ]4 / [ ]SIN HERIDA / [ ]NO CONSIGNADO ";
        if (trim($fap['fapth_desc']) != "")
	  $herida_op = trim($fap['fapth_desc']);
        $pdf->Cell(150,7,$herida_op,1,1,'L');
        $pdf->SetFont('Arial','', 8);
        /* EGF */

        $pdf->Cell(38,7,'Anestesia Principal:',1,0,'R');
	$pdf->Cell(150,7,($fap['fapta_desc1']!=''?$fap['fapta_desc1']:'(SIN DATO)'),1,1,'L');
	$pdf->Cell(38,7,'Anestesia Secundaria:',1,0,'R');
	$pdf->Cell(150,7,($fap['fapta_desc2']!=''?$fap['fapta_desc2']:'(SIN DATO)'),1,1,'L');
	$pdf->Cell(38,7,'Biopsia:',1,0,'R');
	$pdf->Cell(100,7,$biopsia,1,0,'L');  //150

/*	$pdf->Cell(23,7,'Sospecha GES:',1,0,'R');
	if($fap['fap_sospecha_ges']=='t')
		$pdf->Cell(17,7,'S',1,0,'L');
	else if($fap['fap_sospecha_ges']=='f')
		$pdf->Cell(17,7,'N',1,0,'L');
	else
		$pdf->Cell(27,7,'[ ] SI  /  [ ] NO',1,0,'L');

	$pdf->Cell(40,7,'',1,0,'L');
*/

	$pdf->Cell(30,7,'Reoperado:',1,0,'R');
	if($fap['fap_reoperado']=='t')
		$pdf->Cell(20,7,'S',1,0,'L');
	if($fap['fap_reoperado']=='f')
		$pdf->Cell(20,7,'N',1,0,'L');
	else
		$pdf->Cell(20,7,'',1,0,'L');

//	$pdf->Ln();
//        $pdf->Cell(38,7,'Observaci�n:',1,0,'R');
//	$pdf->Cell(150,7,$fap['fap_observaciones'],1,1,'L');

        $pdf->Ln();
	$pdf->Ln();
	$pdf->SetFont('Arial','',10);

	$pdf->Cell(95,6,'______________________________',0,0,'C');
	$pdf->Cell(95,6,'______________________________',0,1,'C');
	$pdf->Cell(95,6,'FIRMA CIRUJANO',0,0,'C');
	$pdf->Cell(95,6,'FIRMA ANESTESISTA',0,1,'C');
	//$pdf->Ln();
	/*$pdf->Cell(188,6,'______________________________',0,1,'C');
	$pdf->Cell(188,6,'V�B� RECAUDACI�N',0,1,'C');*/

	$pdf->Output('FAP_PABELLON_'.$fap['fap_fnumero'].'.pdf','I');	


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