<?php
	error_reporting(E_ALL);
 	require_once('../../config.php');
	require_once('../../conectores/sigh.php');
	require_once('../../fpdf/fpdf.php');
	$tipo=$_GET['tipo_inf'];
	$fecha = pg_escape_string($_GET['fecha']);
	$esp_id = $_GET['esp_id']*1;
	
	if($esp_id!=-1)
		$esp="especialidades.esp_id=$esp_id";
	else
		$esp="true";
	
	$doc_id= $_GET['doc_id']*1;
	if($doc_id!=-1)
		$doc="doctores.doc_id=$doc_id";
	else
		$doc="true";
	
	$agrupar=$_GET['agrupar']*1;
	if($agrupar==0) $agrupar_o='esp_desc,doc_nombre';
	if($agrupar==1) $agrupar_o='esp_desc';
	
	class PDF extends FPDF {
		function header() {

			$this->SetFont('Arial','B', 12);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('logo_min.jpg',5,7,35,20);

			//$this->Ln(20);
			$this->SetX(40);
			$this->Cell(150,4,('Ministerio de Salud'),0,0,'L');	
			$this->Ln();
			$this->SetX(40);
			$this->Cell(150,4,('SS Metropolitano Occidente'),0,0,'L');	
			$this->Ln();
			$this->SetX(40);
			$this->Cell(150,4,('Hospital San José de Melipilla'),0,0,'L');	
			$this->Ln();
		
			$this->SetFontSize(14);		
			$this->SetY(30);	
		
		}

	}
	
	if($tipo==1 OR $tipo==3)
	{
		$nom_ant='';
		$doc_ant='';
		$esp_ant='';
		if($tipo==1)
			$consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
			upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
			pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
			date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,nomd_diag_cod,
			especialidades.esp_id,doctores.doc_id,
			COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
			COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
			COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
			COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
			(SELECT COUNT(*) FROM nomina_detalle AS nd2 JOIN nomina AS n2 USING (nom_id) WHERE nd2.pac_id=pacientes.pac_id AND n2.nom_fecha=nomina.nom_fecha AND nomd_diag_cod NOT IN ('X','T','H')) AS peticiones,
			(SELECT COUNT(*) FROM ficha_espontanea AS fesp WHERE fesp.pac_id=pacientes.pac_id AND fesp.fesp_fecha::date=nomina.nom_fecha::date AND fesp_estado=0) AS peticiones2
			FROM nomina
			LEFT JOIN nomina_detalle USING (nom_id)
			LEFT JOIN especialidades ON nom_esp_id=esp_id
			LEFT JOIN doctores ON nom_doc_id=doc_id
			JOIN pacientes USING (pac_id)
			WHERE esp_ficha AND nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('B','T')
			AND $esp AND $doc
			ORDER BY nom_fecha,$agrupar_o,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
			$salidas = cargar_registros_obj($consulta);
			// LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
	}
	elseif($tipo==2)
	{
		$nom_ant='';
		$doc_ant='';
		$esp_ant='';
  		$salidas = cargar_registros_obj("SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
		upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
		pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,'' AS nomd_diag_cod, 
		date_trunc('second',
		COALESCE((SELECT nomd_fecha_asigna FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomina_detalle.pac_id=pacientes.pac_id AND nom_esp_id=destino_esp_id AND nom_doc_id=destino_doc_id ORDER BY nom_fecha DESC LIMIT 1),
		(SELECT fesp_fecha FROM ficha_espontanea WHERE ficha_espontanea.pac_id=pacientes.pac_id AND ficha_espontanea.esp_id=destino_esp_id AND ficha_espontanea.doc_id=destino_doc_id ORDER BY fesp_fecha DESC LIMIT 1)))AS fecha_asigna,
		especialidades.esp_id,doctores.doc_id,
		COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
		COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
		COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
		(SELECT am_fecha FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id AND am_estado=2 ORDER BY am_fecha DESC LIMIT 1) as fecha_envio
		FROM archivo_movimientos
		LEFT JOIN especialidades ON destino_esp_id=esp_id
		LEFT JOIN doctores ON destino_doc_id=doc_id
		JOIN pacientes USING (pac_id)
		WHERE 
		am_final AND am_estado IN (2,3) AND archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id
		AND $esp AND $doc
		ORDER BY esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint");
		
		// nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('X','T','B')
		// LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
				
	}

	//print_r($salidas);
	$opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
	$opts_color=Array('black','gray','blue','purple','green','red');
	if($tipo==2 AND !$salidas)
	{
		print("<center><h1>(No tiene fichas pendientes por recepcionar...)</h1></center>");
  	}
	if($salidas)
	{
		$pdf=new PDF('L','mm','Letter');
		//$pdf=new PDF('P', 'mm', '200, 300');  
		$pdf->AliasNbPages();
		//$pdf->SetAutoPageBreak(true,20);
		//$pdf->AddPage();
		for($i=0;$i<count($salidas);$i++)
		{
			($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
			$checked='';
			$color='';
			if(($agrupar==0 AND $doc_ant!=$salidas[$i]['doc_id']) OR $esp_ant!=$salidas[$i]['esp_id'])
			{
				$doc_ant=$salidas[$i]['doc_id'];
				$esp_ant=$salidas[$i]['esp_id'];
				$cont=1;
				if($i>0)
					/*
					print("</table>");
					*/
				if(isset($salidas[$i]['amp_nombre']) AND $salidas[$i]['amp_nombre']!='')
				{
					$motivo='<br/>Motivo Solicitud: <b>'.htmlentities($salidas[$i]['amp_nombre']).'</b>';
				}
				else
				{
					$motivo='';
				}
				$pdf->AddPage();

				$pdf->SetFillColor(250,250,250);	
				$pdf->SetFont('Arial','', 10);
				$pdf->SetFillColor(230,230,230);
				$pdf->Cell(200,7,('Programa: '.$salidas[$i]['esp'].'          Fecha Listado: '.$fecha),0,0,'L',1);
				if($agrupar==0)
				{
					$pdf->Ln();
					$pdf->Cell(200,7,('Profesional / Servicio: '.$salidas[$i]['doc_nombre'].''),0,0,'L',1);
				}
														
				
				$pdf->Ln();
				$pdf->SetFillColor(200,200,200);
				$pdf->SetFont('Arial','',10);
				//$pdf->Cell(9,9,'#',1,0,'C',1);
				//$pdf->Cell(12,9,'Hora',1,0,'C',1);
				//$pdf->Cell(21,9,'Solicitado',1,0,'C',1);
				if($tipo==2)
					$pdf->Cell(20,9,'Enviada',1,0,'C',1);
				$pdf->Cell(30,9,'Ficha',1,0,'C',1);
				if($tipo==1 OR $tipo==3)
					$pdf->Cell(9,9,'P*',1,0,'C',1);
				$pdf->Cell(30,9,'RUN',1,0,'C',1);
				$pdf->Cell(130,9,'Nombre Completo',1,0,'C',1);
				if($tipo!=2)
				{
					//$pdf->Cell(30,9,'Ubic. Anterior',1,0,'C',1);
					$pdf->Cell(60,9,'Ubic. Actual',1,0,'C',1);
				}
				//$pdf->Cell(30,9,'Estado Actual',1,0,'C',1);
				
				/*
				print("<table style='width:100%;' class='lista_small' cellspacing=0 cellpadding=1>");
				*/
			}
			$options='';
			for($l=0;$l<sizeof($opts);$l++)
			{
				if($salidas[$i]['am_estado']*1==$l) $sel='SELECTED'; else $sel='';
				$options.='<option value="'.$l.'" '.$sel.'>'.$opts[$l].'</option>';
			}
			if($salidas[$i]['pac_ficha']!='' and $salidas[$i]['pac_ficha']!='0')
                        {
				$ficha=$salidas[$i]['pac_ficha'];
                        }
			else
			{
                            
				//$ficha="<center>";
				//$ficha.="Sin Asignar</center>";
                                $ficha="Sin Asignar";
			}
			if($tipo!=2)
			{
				if($salidas[$i]['nomd_id_sel']*1==$salidas[$i]['nomd_id']*1)
				{
					$color='background-color:#bbbbff;';
				}
				else
				{
					$color='';
				}
			}
			if($salidas[$i]['nomd_diag_cod']=='X' OR $salidas[$i]['nomd_diag_cod']=='T')
			{
				$tachar='text-decoration:line-through;';
        	}
        	else
        	{
         	$tachar='';
        	}
			$pdf->Ln();
			//$pdf->Cell(9,9,$cont,1,0,'C');
			//$pdf->Cell(12,9,substr($salidas[$i]['nomd_hora'],0,5),1,0,'C',1);
			//$pdf->Cell(21,9,substr($salidas[$i]['fecha_asigna'],0,16),1,0,'C');
				if($tipo==2)
					$pdf->Cell(20,9,substr($salidas[$i]['fecha_envio'],0,16),1,0,'C');

				$pdf->SetFont('','B');
				$pdf->SetFont('Arial','',10);
				$pdf->Cell(30,9,$ficha,1,0,'C');
				$pdf->SetFont('Arial','',10);
				$pdf->SetFont('','');
				if($tipo==1 OR $tipo==3)
					$pdf->Cell(9,9,(($salidas[$i]['peticiones']*1)+($salidas[$i]['peticiones2']*1)),1,0,'C');
				$pdf->Cell(30,9,$salidas[$i]['pac_rut'],1,0,'C');
				$pdf->Cell(130,9,$salidas[$i]['pac_nombre'],1,0,'L');
				if($tipo!=2)
				{
					//$pdf->Cell(30,9,htmlentities($salidas[$i]['ubic_anterior']),1,0,'C');
					$pdf->Cell(60,9,htmlentities($salidas[$i]['ubic_actual']),1,0,'C');
				}
				//$pdf->Cell(30,9,$opts[$salidas[$i]['am_estado']*1],1,0,'C');			
        	//$nom_ant=$salidas[$i]['nom_id'];
			$cont++;
  		}
  		/*
  		print("</table>");
  		*/
	} 
	$pdf->Output('fichas_'.date("Ymd").'.pdf','I');
	



	 
	



				
			
	
	
	
	
	/*
	
	
  
  
  	class PDF extends FPDF {
		function header() {

			$this->SetFont('Arial','B', 12);

			//$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
			//$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
			//$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);

			$this->Image('logo_min.jpg',5,7,35,20);

			//$this->Ln(20);
			$this->SetX(40);
			$this->Cell(150,4,('Ministerio de Salud'),0,0,'L');	
			$this->Ln();
			$this->SetX(40);
			$this->Cell(150,4,('SS Metropolitano Occidente'),0,0,'L');	
			$this->Ln();
			$this->SetX(40);
			$this->Cell(150,4,('Hospital Clínico Félix Bulnes Cerda'),0,0,'L');	
			$this->Ln();
			$this->SetX(40);
			$this->Cell(150,4,('Admisión Atención Abierta'),0,0,'L');	
			$this->Ln();
		
			$this->SetFontSize(14);		
			$this->SetY(30);	
		
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

	if($i%2==0) $pdf->AddPage();

	$pdf->SetFillColor(200,200,200);	

	$pdf->SetFont('Arial','', 14);

	$pdf->SetFillColor(130,130,130);

	$pdf->Cell(200,7,('COMPROBANTE DE CITACIÓN'),1,0,'C',1);
	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Citado para el:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','', 16);
	
	$fec=explode('/',substr($lista[$i]['nom_fecha'],0,10));
	$nombres_dias=Array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
	$dia=$nombres_dias[date('w',mktime(0,0,0,$fec[1],$fec[0],$fec[2]))*1];
	
	if(substr($lista[$i]['nomd_hora'],0,5)=='00:00') {
		$lista[$i]['nomd_hora']=$lista[$i]['cupos_horainicio'];
	}
	
	$pdf->Cell(160,6,$dia.' '.substr($lista[$i]['nom_fecha'],0,10).' a las '.substr($lista[$i]['nomd_hora'],0,5).' Hrs.',1,0,'L',1);	
	$pdf->SetFont('Arial','', 14);
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Programa:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,$lista[$i]['esp_desc'],1,0,'L',1);	
	$pdf->Ln();


	

	$pdf->SetFont('Arial','', 14);

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Profesional:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,$prof,1,0,'L',1);	
	$pdf->Ln();

	
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Paciente:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'],1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'RUN:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,$pac['pac_rut'],1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Teléfono(s):',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	
	if($pac['pac_fono']!='' AND $pac['pac_celular']!='')
		$fonos=$pac['pac_fono'].' / '.$pac['pac_celular'];
	else if($pac['pac_fono']!='')
		$fonos=$pac['pac_fono'];
	else if($pac['pac_celular']!='')
		$fonos=$pac['pac_celular'];
	else
		$fonos='(Sin Información...)';
		
	$pdf->Cell(160,6,$fonos,1,0,'L',1);	
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Dirección:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','', 11);
	$pdf->Cell(160,6,strtoupper(trim($pac['pac_direccion']).', '.$pac['ciud_desc']),1,0,'L',1);	
	$pdf->SetFont('Arial','', 14);
	$pdf->Ln();

	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Ficha Clínica:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(50,6,$pac['pac_ficha'],1,0,'L',1);	
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(50,6,'Previsión:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(60,6,$pac['prev_desc'],1,0,'L',1);	
	$pdf->Ln();
	
	$glosa=$lista[$i]['glosa'];
	
	if(strlen($glosa)>50) {
		$glosa=substr($glosa,1,47).'...';
	}
	


	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Asignado por:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','', 11);
	$pdf->Cell(160,6,($lista[$i]['asigna_nombre']).' ['.substr($lista[$i]['nomd_fecha_asigna'],0,16).']',1,0,'L',1);	
	$pdf->SetFont('Arial','', 14);
	$pdf->Ln();


	$pdf->Ln(5);	

	$pdf->SetFont('','B',12);
	$pdf->Cell(190,4,('Información Importante:'),0,0,'L');
	$pdf->Ln();
		
	$pdf->SetFont('','',11);
	$pdf->Multicell(190,4,str_replace('<br>',"\n",str_replace("\n",'',("
1) Debe presentar esta citación, Credencial FONASA o documentación previsional al día, y cédula de identidad el día de la atención.<br>
2) Los pacientes FONASA A y B tienen gratuidad total en sus prestaciones de salud.<br>
3) Si por algún motivo no pudiese asistir debe dar aviso al número ".$lista[$i]['esp_fono']." para asignar el cupo a otra persona.<br>
4) Con el ánimo de agilizar los trámites, los pacientes deben presentarse 15 minutos antes de la hora de atención en las oficinas de Admisión y Recaudación<br>"))));

	$pdf->SetFont('','B',14);

		
	}

	$pdf->Output('CITACION_'.strtoupper(trim($nomd_id)).'.pdf','I');	
	*/

?>
