<?php
    require_once('../../config.php');
    require_once('../../conectores/sigh.php');
    require_once('../../fpdf/fpdf.php');
    $nomd_id=($_GET['nomd_id']*1);
    $esp_examen=($_GET['esp_examen']*1);
    $sol_id=($_GET['sol_id']*1);
    $examenes_sol=pg_escape_string($_GET['txt_examenes']);
    $examenes_sol=str_replace("|",",",$examenes_sol);
    /*
    {
        $where_estado="where total_examenes!=total_agendados and sol_examd_nomd_id=0";
    }
    else
    {
        $where_estado="where total_examenes=total_agendados and sol_examd_nomd_id!=0";
    }
    */
    //$pac_id=($_GET['pac_id']*1);
    //if($pac_id!="0")
    //    $pac_string="and sol_pac_id=".$pac_id."";
    //else
    
    $pac_string="";
    $esp_string="and sol_esp_id=".$esp_examen."";
    $where_estado="";
    
    $consulta="
        SELECT * from (
        SELECT foo.*,
        (SELECT count(*) FROM solicitud_examen_detalle left join solicitud_examen on sol_examd_solexam_id=sol_exam_id WHERE sol_nomd_id_original=foo.sol_nomd_id_original)as total_examenes,
        (SELECT count(*) FROM solicitud_examen_detalle left join solicitud_examen on sol_examd_solexam_id=sol_exam_id WHERE sol_nomd_id_original=foo.sol_nomd_id_original and sol_examd_nomd_id!=0)as total_agendados
        from (
        SELECT sol_nomd_id_original,date_trunc('second',sol_fecha)as fecha_solicitud,sol_tipo_examen,solicitud_examen_detalle.*,
        procedimiento_codigo.*,nomina_detalle.*,nomina.*,pac_appat || ' ' || ' ' || pac_apmat || ' '|| pac_nombres as nombre_paciente,pac_rut,func_nombre,sol_esp_id,
        sol_exam_id,sol_estado,
        especialidades.esp_desc,
        prevision.*,
        pac_fc_nac,
        esp1.esp_desc as esp_solicita
        FROM solicitud_examen_detalle 
        LEFT JOIN solicitud_examen on sol_examd_solexam_id=sol_exam_id
        LEFT JOIN pacientes on sol_pac_id=pac_id
        LEFT JOIN funcionario on sol_func_id=func_id
        LEFT JOIN procedimiento_codigo on sol_examd_cod_presta=pc_id
        LEFT JOIN nomina_detalle on sol_nomd_id_original=nomd_id
        LEFT JOIN nomina using (nom_id)
        left join prevision using (prev_id)
        left join especialidades on sol_esp_id=especialidades.esp_id
        left join especialidades esp1 on esp1.esp_id=nom_esp_id
        WHERE sol_examd_id in ($examenes_sol) $pac_string $esp_string
        order by sol_fecha desc, sol_nomd_id_original,sol_tipo_examen,sol_examd_solexam_id
        )as foo
        )as foo2
        $where_estado
        ";
        //print($consulta);
    
        $reg_examenes = cargar_registros_obj($consulta);
        if(!$reg_examenes)
        {
            $reg_examenes=false;
        }
    
    function calculaedad($fechanacimiento)
    {
        list($dia,$mes,$ano) = explode("/",$fechanacimiento);
        $ano_diferencia  = date("Y") - $ano;
        $mes_diferencia = date("m") - $mes;
        $dia_diferencia   = date("d") - $dia;
        if ($dia_diferencia < 0 || $mes_diferencia < 0)
            $ano_diferencia--;
        return
        $ano_diferencia;
    }
    
    //$estado_solicitudes=($_POST['estado_solicitudes']*1);
    //if($estado_solicitudes==0)
    //{
    //$where_estado="where total_examenes!=total_agendados and sol_examd_nomd_id=0";
    //}
    //else
    //{
    //$where_estado="where total_examenes=total_agendados and sol_examd_nomd_id!=0";
    //}
    
    /*
    



	function trunc($str,$len) {
		if(strlen($str)>$len)
			return substr($str,0,$len).'...';
		else
			return $str;
	}
    */
    
    /*
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
    */ 
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
    
    $pdf=new PDF('P','mm','Legal');
    
    
    $pdf->AliasNbPages();
    $pdf_margin=5;
    if($reg_examenes)
    {
        if(strlen($reg_examenes[0]['prev_desc'])==1)
        {
            $reg_examenes[0]['prev_desc']='FONASA - GRUPO '.$reg_examenes[0]['prev_desc'];
        }
        $pdf->AddPage();  
        $pdf->SetFont('Arial','', 14);
        $pdf->SetFillColor(200,200,200);	
        $pdf->Cell(180,7,('ORDEN MEDICA'),1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Citado para el:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->SetFont('Arial','', 12);
        $pdf->Cell(130,6,'___/___/_______',1,0,'L',1);	
        $pdf->SetFont('Arial','', 12);
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Fecha Solicitud:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,$reg_examenes[0]['fecha_solicitud'],1,0,'L',1);	
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Especialidad:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,$reg_examenes[0]['esp_desc'],1,0,'L',1);	
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Rut:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,$reg_examenes[0]['pac_rut'],1,0,'L',1);	
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Nombre Paciente:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,$reg_examenes[0]['nombre_paciente'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Edad:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,calculaedad($reg_examenes[0]['pac_fc_nac'])." AÑOS",1,0,'L',1);	
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Previsión:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,$reg_examenes[0]['prev_desc'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Solicitado por:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,$reg_examenes[0]['func_nombre'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Especialidad Solicitante:',1,0,'R',1);	
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(130,6,$reg_examenes[0]['esp_solicita'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(180,6,'Diagnostico:',1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFillColor(255,255,255);
        if($reg_examenes[0]['nomd_diag']!="")
        {
            if($reg_examenes[0]['nomd_diag_cod']!="OK" and $reg_examenes[0]['nomd_diag']!="ALTA" and $reg_examenes[0]['nomd_diag']!="N" and $reg_examenes[0]['nomd_diag']!="T")
            {
                //$pdf->Cell(140,6,"[".$reg_examenes[0]['nomd_diag_cod']."] : ".$reg_examenes[0]['nomd_diag'],1,0,'L',1);
                $pdf->Multicell(180,25,"",1,1,0);
                $pdf->SetXY(11, 100);
                $pdf->Write(5,"[".$reg_examenes[0]['nomd_diag_cod']."] : ".$reg_examenes[0]['nomd_diag'],1);
                
            }
            else
            {
                //$pdf->Cell(140,6,$reg_examenes[0]['nomd_diag'],1,0,'L',1);
                $pdf->Multicell(180,25,"",1,1,0);
                $pdf->SetXY(11, 100);
                $pdf->Write(5,$reg_examenes[0]['nomd_diag'],1);
            }
        }
        else
        {
            //$pdf->Cell(140,6,"Sin Asignar",1,0,'L',1);
            $pdf->Multicell(180,20,"Sin Asignar",1);
        }
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(200,200,200);	
        $pdf->Cell(180,7,('EXAMENES SOLICITADOS'),1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 9);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(40,6,'Tipo Examén',1,0,'C',1);
        $pdf->Cell(40,6,'Código',1,0,'C',1);
        $pdf->Cell(100,6,'Detalle',1,0,'C',1);
        for($i=0;$i<count($reg_examenes);$i++)
        {
            $pdf->Ln();
            $pdf->SetFont('Arial','', 8);
            $pdf->SetFillColor(255,255,255);
            if($reg_examenes[$i]['sol_tipo_examen']!="")
                $pdf->Cell(40,6,$reg_examenes[$i]['sol_tipo_examen'],1,0,'L',1);
            else
                $pdf->Cell(40,6,$reg_examenes[$i]['pc_grupo'],1,0,'L',1);
            
            $pdf->Cell(40,6,$reg_examenes[$i]['pc_codigo'],1,0,'L',1);
            if($reg_examenes[$i]['pc_grupo']!='' and $reg_examenes[$i]['pc_grupo']!=null)
            {
                $pdf->Cell(100,6,$reg_examenes[$i]['pc_desc']."-".$reg_examenes[$i]['pc_grupo'],1,0,'L',1);
                if($reg_examenes[$i]['sol_examd_obs']!="")
                {
                    $pdf->Ln();
                    $pdf->SetFillColor(240,240,240);
                    $pdf->Cell(40,6,'Observ : ',1,0,'C',1);
                    $pdf->Cell(140,6,$reg_examenes[$i]['sol_examd_obs'],1,0,'left',1);
                }
            }
            else
            {
                $pdf->Cell(100,6,$reg_examenes[$i]['pc_desc'],1,0,'L',1);
                if($reg_examenes[$i]['sol_examd_obs']!="")
                {
                    $pdf->Ln();
                    $pdf->SetFillColor(240,240,240);
                    $pdf->Cell(40,6,'Observ : ',1,0,'C',1);
                    $pdf->Cell(140,6,$reg_examenes[$i]['sol_examd_obs'],1,0,'left',1);
                }
            }
        }
        
        
        
        
    }
    
    $pdf->Output('ATENCION_EXAMEN_'.strtoupper(trim($nomd_id)).'.pdf','I');
    
    //$pdf->SetAutoPageBreak(true,20);
    //$pdf->AddPage();
    /*
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
            //$pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'Edad:',1,0,'R',1);
            $pdf->SetFillColor(255,255,255);
            $pdf->SetFont('Arial','', 10);
            $pdf->Cell(100,6,calculaedad($pac['pac_fc_nac'])." AÑOS",1,0,'L',1);
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
     */ 
    $pdf->Output('HOJA_ATENCION_'.strtoupper(trim($nomd_id)).'.pdf','I');
     
?>
