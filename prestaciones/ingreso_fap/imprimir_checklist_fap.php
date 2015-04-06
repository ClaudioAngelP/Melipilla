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
	
	$fcld_id=$_GET['fcld_id']*1;
	
	$cl=cargar_registro("SELECT * FROM fap_checklist_detalle JOIN fap_checklist USING (fcl_id) WHERE fcld_id=$fcld_id");
	
	$fap_id=$cl['fap_id']*1;
	
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
		
	$pdf->SetFillColor(250,250,250);	

	$pdf->SetFont('Arial','', 9);

	$pdf->Cell(190,4,'Ministerio de Salud',0,1,'L');
	$pdf->Cell(190,4,'Servicio de Salud Metropolitano Occidente',0,1,'L');
	$pdf->Cell(190,4,'Hospital San José de Melipilla',0,1,'L');

	$pdf->SetFont('Arial','BU', 20);

	$pdf->Cell(190,9,$cl['fcl_nombre'],0,1,'C');
	$pdf->Ln();

	$pdf->SetFont('Arial','', 14);

	$fecha=explode('.',$fap['fap_fecha']);

	$pdf->SetFont('Arial','B',18);
	$pdf->Cell(55,7,'N° '.$fap['fap_fnumero'],0,0,'L');

	$pdf->SetFont('Arial','I',12);
	$pdf->Cell(40,7,'N° Cta. Cte: '.$fap['hosp_id'],0,0,'C');

	$pdf->SetFont('Arial','',14);

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

	switch($fap['fap_subtipopab']) {
		case 0: $fap['fap_subtipopab']='AMBULATORIO'; break;
		case 1: $fap['fap_subtipopab']='HOSPITALIZADO'; break;
		default: $fap['fap_subtipopab']='[ ] HOSPITALIZADO / [ ] AMBULATORIO'; break;
	}
	
	$pdf->Cell(47,5,'Modo de Atención:',1,0,'R',1);
	$pdf->SetFont('Arial','', 10);
	$pdf->Cell(78,5,$fap['fap_subtipopab'],1,0,'L');
	$pdf->SetFont('Arial','', 12);


	$pdf->Cell(27,5,'Ficha Clínica:',1,0,'R',1);
	$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['pac_ficha']:''),1,1,'L');


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
        
    }

    $pdf->SetFont('Arial','', 12);
	

	$pdf->Cell(47,5,'Servicio Orígen:',1,0,'R',1);
	$pdf->Cell(78,5,cut($fap['centro_nombre'],25),1,0,'L');
	$pdf->Cell(27,5,'Previsión:',1,0,'R',1);
	$pdf->Cell(36,5,($fap['pac_id']!=0?$fap['prev_desc']:''),1,1,'L');
	/* EGF */

	if($fap['fap_asa']*1==-1)
		$fap['fap_asa']='';

	$pdf->Cell(47,5,'Servicio Destino:',1,0,'R',1);
	$pdf->Cell(78,5,cut($fap['centro_nombre2'],25),1,0,'L');
	$pdf->Cell(27,5,'ASA (1-4):',1,0,'R',1);
	$pdf->Cell(36,5,$fap['fap_asa'],1,1,'L');

	/* EGF */
    //$pdf->Cell(47,5,'Especialidad:',1,0,'R',1);
	//$pdf->Cell(78,5,cut($fap['pp_desc'],25),1,0,'L');
    $pdf->Cell(47,5,'N° Pabellón:',1,0,'R',1);
	$pdf->Cell(141,5,$fap['fapp_desc'],1,1,'L');
	
	if($fap['fap_suspension']!=''){
		$pdf->Cell(47,5,'Suspensión:',1,0,'R',1);
		$pdf->Cell(141,5,$fap['fap_suspension'],1,1,'L');
	}

     /* EGF */

	$pdf->SetFillColor(240,240,240);	
	$pdf->SetFont('Arial','B', 12);
	$pdf->Cell(188,6,'DIAGNÓSTICOS PRE./POST. OPERATORIOS',1,1,'C',1);
	$pdf->SetFont('Arial','', 12);
	$pdf->SetFillColor(250,250,250);	


	$pdf->Cell(47,5,'Diag. Pre. Operatorio:',1,0,'R',1);
	$pdf->Cell(141,5,cut($fap['fap_diag_cod'],50),1,1,'L');
	
	$pdf->Cell(47,5,'Diag. Post. Oper. (1):',1,0,'R',1);
	if($fap['diag_desc_1']!='')
		$pdf->Cell(141,5,'['.$fap['fap_diag_cod_1'].'] '.cut($fap['diag_desc_1'],45),1,1,'L');
	else 
		$pdf->Cell(141,5,'',1,1,'L');
		
	$pdf->Cell(47,5,'Diag. Post. Oper. (2):',1,0,'R',1);
	if($fap['diag_desc_2']!='')
		$pdf->Cell(141,5,'['.$fap['fap_diag_cod_2'].'] '.cut($fap['diag_desc_2'],45),1,1,'L');
	else 
		$pdf->Cell(141,5,'',1,1,'L');

	$pdf->Cell(47,5,'Diag. Post. Oper. (3):',1,0,'R',1);
	if($fap['diag_desc_3']!='')
		$pdf->Cell(141,5,'['.$fap['fap_diag_cod_3'].'] '.cut($fap['diag_desc_3'],45),1,1,'L');
	else 
		$pdf->Cell(141,5,'',1,1,'L');
		
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

	//$pdf->Ln();
	
	$campos=explode("\n",$cl['fcld_datos']);;
	$cmp_utl='';
	for($i=0;$i<sizeof($campos);$i++) {
		if(trim($campos[$i])=='') continue;
		$cmp=explode('|',$campos[$i]);
		$tipo=$cmp[2]*1;
		
		if($tipo==20) {
			$pdf->SetFillColor(240,240,240);	
			$pdf->SetFont('Arial','BI', 11);
			$pdf->Cell(188,7,$cmp[0],1,1,'C',1);					
		} else if($tipo==6) {
			$pdf->SetFillColor(250,250,250);	
			$pdf->SetFont('Arial','', 7);
			$pdf->Cell(90,5,$cmp[0].':',1,0,'R',1);
			$pdf->SetFont('Arial','', 10);
			$pdf->Cell(98,5,($cmp[1]!=''?' * ':'').str_replace('//',' * ',$cmp[1]),1,1,'L');
		} else if($tipo==0 OR $tipo==1) {
			$pdf->SetFillColor(250,250,250);	
			$pdf->SetFont('Arial','', 7);
			$pdf->Cell(90,5,$cmp[0].':',1,0,'R',1);
			$pdf->SetFont('Arial','', 10);
			$pdf->Cell(98,5,(($cmp[1]=='S')?'SI':'NO'),1,1,'L');
			$cmp_utl=$cmp[1];
		
		} else if($tipo!=10) {
			if($cmp[1]!=''){
				$pdf->SetFillColor(250,250,250);	
				$pdf->SetFont('Arial','', 7);
				$pdf->Cell(90,5,$cmp[0].':',1,0,'R',1);
				$pdf->SetFont('Arial','', 10);
				$pdf->Cell(98,5,$cmp[1],1,1,'L');
			}
			$cmp_utl=$cmp[1];
		
		} else {
		
			$lineas=explode('<br>',$cmp[1]);
			
				for($k=0;$k<sizeof($lineas);$k++) {
					$pdf->SetFillColor(250,250,250);	
					$pdf->SetFont('Arial','', 7);
					
					if($k==0)
						$pdf->Cell(90,5,$cmp[0].':',1,0,'R',1);
					else
						$pdf->Cell(90,5,'',1,0,'R',1);				
						
					$pdf->SetFont('Courier','', 9);
					$pdf->Cell(98,5,$lineas[$k],1,1,'L');
				}
			$cmp_utl=$cmp[1];			
		
		}
	}
	
	$pdf->Ln(20);

	$pdf->Cell(95,6,'______________________________',0,0,'C');
	$pdf->Cell(95,6,'______________________________',0,1,'C');
	$pdf->Cell(95,6,'FIRMA CIRUJANO',0,0,'C');
	$pdf->Cell(95,6,'FIRMA ANESTESISTA',0,1,'C');
	//$pdf->Ln();
	/*$pdf->Cell(188,6,'______________________________',0,1,'C');
	$pdf->Cell(188,6,'V°B° RECAUDACIÓN',0,1,'C');*/

	$pdf->Output('FAP_CHECKLIST_PABELLON_'.$fap['fap_fnumero'].'.pdf','I');	


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
