<?php
    require_once('../../config.php');
    require_once('../../conectores/sigh.php');
    require_once('../../fpdf/fpdf.php');



	function trunc($str,$len) {
		if(strlen($str)>$len)
			return substr($str,0,$len).'...';
		else
			return $str;
	}

    $nomd_id=$_GET['nomd_id']*1;
    $lista = cargar_registros_obj("SELECT 
    nomina_detalle.nomd_id, nom_fecha::date, nomd_hora, 
    doc_rut, doc_paterno, doc_materno, doc_nombres, 
    COALESCE(diag_desc, cancela_desc) AS diag_desc, 
    nomd_diag_cod,
    esp_desc, nomd_tipo, 
    CASE WHEN nom_fecha>=CURRENT_DATE THEN 'P' ELSE 'A' END AS estado,
    nomd_codigo_presta, glosa, esp_lugar, COALESCE(esp_nombre_especialidad, esp_desc) AS esp_nombre_especialidad,
    nomina_detalle.id_sidra,upper(func_nombre) AS asigna_nombre, '260194' AS esp_fono, *
    FROM nomina_detalle
    JOIN nomina USING (nom_id)
    JOIN pacientes USING (pac_id)
    LEFT JOIN comunas USING (ciud_id)
    LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
    LEFT JOIN doctores ON nom_doc_id=doc_id
    LEFT JOIN especialidades ON nom_esp_id=esp_id 
    LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
    LEFT JOIN codigos_prestacion ON nomd_codigo_presta=codigo
    LEFT JOIN cupos_atencion ON nomina.nom_id=cupos_atencion.nom_id
    LEFT JOIN funcionario ON nomd_func_id=func_id
    WHERE nomd_id=$nomd_id
    ORDER BY nomina.nom_fecha ASC, nomd_hora  LIMIT 1", false);
    
    $pac_id=$lista[0]['pac_id']*1;
    $pac = cargar_registro("SELECT *, COALESCE(pac_clave, md5(substr(md5(pac_id::text),1,5))) AS pac_clave FROM pacientes 
    LEFT JOIN comunas USING (ciud_id) LEFT JOIN prevision USING (prev_id) WHERE pac_id=$pac_id ", false);
    if(strlen($pac['prev_desc'])==1)
    {
        $pac['prev_desc']='FONASA - GRUPO '.$pac['prev_desc'];
    }
    class PDF extends FPDF
    {
        function header()
        {
		
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
            $this->SetX(40);
            $this->Cell(150,4,('Admisión Atención Abierta'),0,0,'L');	
            $this->Ln();
            $this->SetFontSize(14);		
            $this->SetY(30);	
	}
    }	
    
    $pdf=new PDF('L','mm','Legal');
    $pdf->AliasNbPages();
    $pdf_margin=0;
    
    //$pdf->SetAutoPageBreak(true,20);
    //$pdf->AddPage();
    
    if($lista)
    {
        
        for($i=0;$i<count($lista);$i++)
        {
            //$lista[$i]['esp_desc']=str_replace('-HDGF', '', $lista[$i]['esp_desc']);
            $prof=strtoupper($lista[$i]['doc_nombres'].' '.$lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno']);
            $prof=str_replace('(AGEN)', '', $prof);
            if($i%2==0)
            {
                $pdf->AddPage();
		$pdf_margin=$pdf->GetX();
		$pdf->SetLeftMargin($pdf_margin);
                
            }
            $pdf->SetLeftMargin($pdf_margin);
            $pdf->SetFont('Arial','', 14);
            $pdf->SetFillColor(200,200,200);	
            $pdf->Cell(140,7,('COMPROBANTE DE CITACIÓN'),1,0,'C',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'Citado para el:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','', 14);
            
            $fec=explode('/',substr($lista[$i]['nom_fecha'],0,10));
            $nombres_dias=Array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
            $dia=$nombres_dias[date('w',mktime(0,0,0,$fec[1],$fec[0],$fec[2]))*1];
	
            if(substr($lista[$i]['nomd_hora'],0,5)=='00:00')
            {
                $lista[$i]['nomd_hora']=$lista[$i]['cupos_horainicio'];
                
            }
            $pdf->Cell(100,6,$dia.' '.substr($lista[$i]['nom_fecha'],0,10).' a las '.substr($lista[$i]['nomd_hora'],0,5).' Hrs.',1,0,'L',1);	
            $pdf->SetFont('Arial','', 14);
            $pdf->Ln();

            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'Programa:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(100,6,$lista[$i]['esp_desc'],1,0,'L',1);	
            $pdf->Ln();

            /*$lugar=$lista[$i]['esp_lugar'];
            if(strlen($lugar)>70) {
                $lugar=substr($lugar,1,67).'...';
            }
            $pdf->SetFillColor(200,200,200);
            $pdf->Cell(40,7,'Lugar:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','', 12);
            $pdf->Cell(160,7,$lugar,1,0,'L',1);	
            $pdf->Ln();*/

            $pdf->SetFont('Arial','', 12);

            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'Profesional:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(100,6,$prof,1,0,'L',1);	
            $pdf->Ln();

            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,7,'Paciente:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(100,7,$pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'],1,0,'L',1);	
            $pdf->Ln();

            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'RUN:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(100,6,$pac['pac_rut'],1,0,'L',1);	
            $pdf->Ln();

            //$pdf->SetFillColor(240,240,240);
            //$pdf->Cell(40,6,'Teléfono(s):',1,0,'R',1);	
            //$pdf->SetFillColor(255,255,255);
	
            if($pac['pac_fono']!='' AND $pac['pac_celular']!='')
                $fonos=$pac['pac_fono'].' / '.$pac['pac_celular'];
            else if($pac['pac_fono']!='')
                $fonos=$pac['pac_fono'];
            else if($pac['pac_celular']!='')
                $fonos=$pac['pac_celular'];
            else
                $fonos='(Sin Información...)';

		if($pac['pac_recados']!='') $fonos.=' / '.$pac['pac_recados'];
            
            //$pdf->Cell(100,6,$fonos,1,0,'L',1);
            $pdf->Ln();
            //$pdf->SetFillColor(240,240,240);
            //$pdf->Cell(40,6,'Dirección:',1,0,'R',1);
            //$pdf->SetFillColor(255,255,255);
            //$pdf->SetFont('Arial','', 9);
            //$pdf->Cell(100,6,strtoupper(trim($pac['pac_direccion']).', '.$pac['ciud_desc']),1,0,'L',1);
            $pdf->SetFont('Arial','', 14);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'Ficha Clínica:',1,0,'R',1);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(40,6,$pac['pac_ficha'],1,0,'L',1);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(30,6,'Previsión:',1,0,'R',1);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(30,6,$pac['prev_desc'],1,0,'L',1);
            $pdf->Ln();
            $glosa=$lista[$i]['glosa'];
            
            if(strlen($glosa)>50)
            {
                $glosa=substr($glosa,1,47).'...';
                
            }
	
            /* $pdf->SetFillColor(200,200,200);
            $pdf->Cell(40,7,'Prestación:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('','B',12);
            $pdf->Cell(30,7,$lista[$i]['nomd_codigo_presta'],1,0,'C',1);	
            $pdf->SetFont('','',10);
            $pdf->Cell(130,7,$glosa,1,0,'L',1);	
            $pdf->Ln(); */

            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'Asignado por:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','', 9);
            $pdf->Cell(100,6,($lista[$i]['asigna_nombre']).' ['.substr($lista[$i]['nomd_fecha_asigna'],0,16).']',1,0,'L',1);	
            $pdf->SetFont('Arial','', 14);
            $pdf->Ln();

		$pr=cargar_registros_obj("SELECT DISTINCT nomdp_id, nomdp_codigo, COALESCE(glosa,presta_desc)as glosa FROM nomina_detalle_prestaciones LEFT JOIN codigos_prestacion ON nomdp_codigo=codigo LEFT JOIN prestaciones_tipo_atencion ON nomdp_codigo=presta_codigo WHERE nomd_id=$nomd_id");
            if($pr) {
		for($ii=0;$ii<sizeof($pr);$ii++) {
                	$pdf->SetFillColor(240,240,240);
			$pdf->SetFont('','B');
        	        $pdf->Cell(40,6,''.($ii+1).') '.$pr[$i]['nomdp_codigo'],1,0,'C',1);
			$pdf->SetFont('','');
	                $pdf->SetFillColor(255,255,255);
			$pdf->SetFont('Arial','', 6);
			$pdf->Cell(100,6,trunc($pr[$ii]['glosa'],75),1,0,'L',1);
			$pdf->SetFont('Arial','', 14);
			$pdf->Ln();
		}

            }


            $pdf->Ln(5);
            $pdf->SetFont('','B',12);
            $pdf->SetFillColor(200,200,200);
            $pdf->Cell(140,6,('Información Importante:'),1,1,'L',1);
            $pdf->SetFont('Arial','',8);
            $pdf->Multicell(140,4,str_replace('<br>',"\n",str_replace("\n",'',("1) Favor verificar que sus datos personales en el siguiente documento sean los correctos,si no es así favor acercarse a ventanillas de Admisión para actualizar sus datos.<br>"
            ."2) Para su atención en el Centro de Atención de Especialidades de nuestro Hospital debe presentar este documento timbrado por Recaudación.<br>"
            ."3) En consultas de Especialidad y procedimientos, los pacientes FONASA A y B tienen gratuidad total en sus prestaciones de salud, a excepción de las atenciones en Odontología, en cuyo caso solo FONASA A tiene gratuidad.<br>"
            ."4) Si por algún motivo no pudiese asistir debe dar aviso a los números 25747622-25747621-25745638  para asignar el cupo a otra persona"))),1);
          
            
            $pdf->Ln(8);
            $pdf->SetFont('Arial','BU', 10);
            $pdf->Cell(160,6,'USO EXCLUSIVO RECAUDACIÓN',0,1,'C');
            $pdf->Ln(1);
            $pdf->SetFont('Arial','', 12);
            $pdf->Cell(40,6,'Previsión:',0,0,'L');
            $pdf->SetFont('Arial','', 16);
            $pdf->Cell(120,6,$pac['prev_desc'],0,1,'L');
            $pdf->Ln(2);
            $pdf->SetFont('Arial','', 12);
            $pdf->Cell(160,6,'___________________________________',0,1,'C');
            $pdf->Cell(160,6,'Firma y Timbre del Recaudador',0,1,'C');
            $pdf->SetFont('','B',14);
            $pdf->SetXY(185,5);

	
            if($lista[$i]['esp_receta']!='t')
            {
                $pdf->SetFont('','BU','16');
		$pdf->Cell(160,10,'Indicaciones',1,0,'C');
		$pdf->SetX(185);
		$pdf->Cell(160,190,'',1,0,'C');
            }
            else
            {
                $pdf->SetLeftMargin(185);
                $pdf->SetFont('','BU','16');
                $pdf->Cell(160,10,'RECETA',1,1,'C');
                $pdf->SetFont('Arial','', 12);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(40,6,'Especialidad:',1,0,'R',1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(120,6,$lista[$i]['esp_desc'],1,0,'L',1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(40,6,'Profesional:',1,0,'R',1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(120,6,$prof,1,0,'L',1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(40,6,'RUN:',1,0,'R',1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(120,6,$pac['pac_rut'],1,0,'L',1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(40,7,'Paciente:',1,0,'R',1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(120,7,$pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'],1,0,'L',1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(40,7,('Diagnóstico:'),1,0,'R',1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(120,7,'',1,0,'L',1);
                $pdf->Ln();
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(40,7,utf8_decode('R.p.:'),0,0,'L');
                $pdf->Ln(105);
                $pdf->SetFont('Arial','', 10);
                $pdf->Cell(80,4,'Validez de la receta [_____] días.',0,0,'C');
                $pdf->Cell(80,4,'___________________________________',0,1,'C');
                $pdf->Cell(80,4,'[_] Agudo  [_] Crónico',0,0,'C');
                $pdf->Cell(80,4,'Nombre y Firma del Médico',0,1,'C');
                $pdf->Cell(80,4,'Fecha: '.substr($lista[$i]['nom_fecha'],0,10),0,0,'C');
                $pdf->Cell(80,4,'Programa: '.$lista[$i]['esp_desc'],0,1,'C');
                $pdf->Ln();
                $pdf->SetFont('Arial','BU', 10);
                $pdf->Cell(160,6,'USO EXCLUSIVO RECAUDACIÓN',0,1,'C');
                $pdf->SetFont('Arial','', 12);
                $pdf->Cell(40,6,'Previsión:',0,0,'R');
                $pdf->SetFont('Arial','', 16);
                $pdf->Cell(120,6,$pac['prev_desc'],0,1,'L');
                $pdf->SetFont('Arial','', 12);
                $pdf->Cell(160,6,'___________________________________',0,1,'C');
                $pdf->Cell(160,6,'Firma y Timbre del Recaudador',0,1,'C');
                
            }
        }
    }
    $pdf->Output('HOJA_ATENCION_'.strtoupper(trim($nomd_id)).'.pdf','I');
?>
