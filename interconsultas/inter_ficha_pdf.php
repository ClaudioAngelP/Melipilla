<?php
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    require_once('../fpdf/fpdf.php');
    //--------------------------------------------------------------------------
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
    //--------------------------------------------------------------------------
    
    if($_GET['tipo']=='inter_ficha' OR $_GET['tipo']=='revisar_inter_ficha')
    {
        $id=$_GET['inter_id'];
        $consulta="SELECT 
	inter_folio, 
        inter_ingreso, 
        pac_rut, 
        pac_appat, 
        pac_apmat, 
        pac_nombres,
        pac_fc_nac,
        pac_direccion,
        ciud_desc,
        prov_desc,
        reg_desc,
        sex_desc,
        prev_desc,
        sang_desc,
        getn_desc,
        prof_rut, 
        prof_paterno, 
        prof_materno, 
        prof_nombres,
	pac_fono,
	pac_celular,
	pac_mail,
        date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,
        date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,
        date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
        upper(doc_rut)as doc_rut,
        (doc_nombres || ' ' || doc_paterno || ' ' || doc_materno)as doc_nombre
        FROM interconsulta 
        LEFT JOIN pacientes ON inter_pac_id=pac_id
        LEFT JOIN comunas ON pacientes.ciud_id=comunas.ciud_id
        LEFT JOIN provincias ON comunas.prov_id=provincias.prov_id
        LEFT JOIN regiones ON provincias.reg_id=regiones.reg_id
        LEFT JOIN sexo ON pacientes.sex_id=sexo.sex_id
        LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
        LEFT JOIN grupo_sanguineo ON pacientes.sang_id=grupo_sanguineo.sang_id
        LEFT JOIN grupos_etnicos ON pacientes.getn_id=grupos_etnicos.getn_id
        LEFT JOIN profesionales_externos ON prof_id=inter_prof_id
        LEFT JOIN doctores on doc_id=inter_prof_id
        WHERE inter_id=$id";
        
        $datos=pg_query($conn, $consulta);
        
        
        $consulta="
        SELECT
        e1.esp_desc,
        inter_fundamentos,
        inter_examenes,
        inter_comentarios,
        inter_estado,
        inter_rev_med,
        inter_prioridad,
        i1.inst_nombre,
        inter_inst_id1,
        inter_motivo,
        inter_diag_cod,
        COALESCE(inter_diagnostico,diag_desc),
        COALESCE(garantia_nombre, ''),
        COALESCE(garantia_id, 0),
        i2.inst_nombre AS inst_nombre2,
        inter_inst_id2,
        inter_ingreso, ice_icono, ice_desc,
	unidad.esp_desc AS unidad_desc, inter_unidad,
	inter_motivo_salida,
	icc_desc,
	inter_fecha_salida
        FROM interconsulta
        LEFT JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id
        LEFT JOIN instituciones AS i1 ON inter_inst_id1=inst_id
        LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id
        LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id
        LEFT JOIN interconsulta_estado ON inter_estado=ice_id		
        LEFT JOIN especialidades AS unidad ON inter_unidad=unidad.esp_id		
	LEFT JOIN interconsulta_cierre ON inter_motivo_salida=icc_id
        LEFT JOIN diagnosticos ON inter_diag_cod=diag_cod
        WHERE inter_id=$id
        ";
        $datos2 = pg_query($consulta);
        $inter = pg_fetch_row($datos);
        $inter2 = pg_fetch_row($datos2);
        $institucion=$inter2[8];
        switch($inter2[9])
        {
            case 0: $inter2[9]='Confirmación Diagnóstica'; break;
            case 1: $inter2[9]='Realizar Tratamiento'; break;
            case 2: $inter2[9]='Seguimiento'; break;
            default: $inter2[9]='Otro Motivo'; break;
        }
        
        //for($a=0;$a<count($inter);$a++)
            //$inter[$a] = htmlentities($inter[$a]);

        if($inter[0]=='-1')
            $inter[0]='INT-'.$id;

    
        
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
        $pdf=new PDF('P','mm','Letter');
        $pdf->AliasNbPages();
        //$pdf->SetAutoPageBreak(true,20);
        //$pdf->AddPage();
		
        $pdf->AddPage();
	$pdf->SetFillColor(200,200,200);	
	$pdf->SetFont('Arial','', 14);
	$pdf->SetFillColor(130,130,130);
	$pdf->Cell(200,7,('INTERCONSULTA'),1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Número de Folio:',1,0,'R',1);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,$inter[0],1,0,'L',1);	
        $pdf->Ln();
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Fecha Solicitud:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->SetFont('Arial','', 16);
	
	$fec=explode('/',substr($inter2[16],0,10));
	$nombres_dias=Array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
	$dia=$nombres_dias[date('w',mktime(0,0,0,$fec[1],$fec[0],$fec[2]))*1];
	$pdf->Cell(160,6,$dia.' '.substr($inter2[16],0,10).'',1,0,'L',1);	
	$pdf->SetFont('Arial','', 14);
	$pdf->Ln();
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Procedencia:',1,0,'R',1);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,$inter2[7],1,0,'L',1);	
        $pdf->Ln();
	$pdf->SetFillColor(200,200,200);
        $pdf->Cell(40,6,'Especialidad:',1,0,'R',1);
	$pdf->SetFillColor(255,255,255);
        $pdf->Cell(160,6,$inter2[0],1,0,'L',1);
        if($inter2[8]==$inter2[15])
        {
            $rut_solicitante=$inter[25];
            $nom_solicitante=$inter[26];
        }
        else
        {
            $rut_solicitante=$inter[15];
            $nom_solicitante="".($inter[16])." ".($inter[17])." ".($inter[18])."";
        }
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'RUN Solicitante:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(30,6,$rut_solicitante,1,0,'L',1);	
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(33,6,'Nombre Solicitante:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(97,6,$nom_solicitante,1,0,'L',1);
        
        
        
        
	$pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('','B','16');
        $pdf->Cell(200,6,'Datos del Paciente',1,0,'L');
        $pdf->SetFont('Arial','', 14);
        $pdf->Ln();
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Nombre:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$inter[5].' '.$inter[3].' '.$inter[4],1,0,'L',1);	
        $pdf->Ln();
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'RUN:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,$inter[2],1,0,'L',1);
        $pdf->Ln();
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Sexo:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(30,6,$inter[11],1,0,'L',1);	
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(45,6,'Fecha Nacimiento:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(35,6,$inter[6],1,0,'L',1);
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(15,6,'Edad:',1,0,'L',1);
        $pdf->SetFillColor(255,255,255);
	$pdf->Cell(35,6,$inter[22]." A,".$inter[23]." M,".$inter[24]." D",1,0,'L',1);
        $pdf->Ln();
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Domicilio:',1,0,'R',1);
        $pdf->SetFont('Arial','', 8);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,$inter[7],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 14);
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Comuna:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,6,"[".$inter[8]."] ".$inter[9]." ".$inter[10],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 14);
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,6,'Teléfonos:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
        if($inter[20]!="")
            $pdf->Cell(160,6,"[".$inter[19]."] [".$inter[20]."]",1,0,'L',1);
        else
            $pdf->Cell(160,6,"[".$inter[19]."]",1,0,'L',1);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('','B','16');
        $pdf->Cell(200,6,'Datos del Clínicos',1,0,'L');
        $pdf->SetFont('Arial','', 14);
        $pdf->Ln();
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Destino:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(160,7,$inter2[14],1,0,'L',1);
        $pdf->Ln();
	$pdf->SetFillColor(200,200,200);
	$pdf->Cell(60,7,'Especialidad Receptora:',1,0,'R',1);	
        $pdf->SetFont('Arial','', 10);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(140,7,htmlentities($inter2[19]),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 14);
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(50,7,'Se envia para:',1,0,'R',1);	
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(150,7,$inter2[9],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(60,7,'Hipótesis Diagnostica:',1,0,'R',1);	
        $pdf->SetFont('Arial','', 10);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(140,7,"[".$inter2[10]."] - ".htmlentities($inter2[11]),1,0,'L',1);
        $dic=cargar_registro("SELECT * FROM interconsulta WHERE inter_id=$id");
        $caso=cargar_registro("SELECT * FROM casos_auge WHERE id_sigges=".$dic['id_caso']);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 14);
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(40,7,'Patología GES:',1,0,'R',1);
        $pdf->SetFillColor(255,255,255);
        if($caso)
            $pdf->Cell(160,7,$caso['ca_patologia'],1,0,'L',1);
  	else
            $pdf->Cell(160,7,'No hay sospecha.',1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 12);
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(200,7,'Fundamentos Clínicos:',1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 11);
	$pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 6, $inter2[1], 1);
      
        $pdf->SetFont('Arial','', 12);
        $pdf->SetFillColor(200,200,200);
	$pdf->Cell(200,7,'Comentarios:',1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 11);
	$pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 6, $inter2[3], 1);
        
        
        if($inter2[2]!="")
        {
            $pdf->SetFont('Arial','', 14);
            $pdf->SetFillColor(200,200,200);
            $pdf->Cell(200,7,'Exámenes Comp.:',1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','', 8);
            $pdf->SetFillColor(255,255,255);
            $pdf->MultiCell(200, 6, $inter2[2], 1);
        }
        
	
	
        
        
         
        
        
        
    
	 
        
	
	$pdf->Ln();
        
        
        
        $pdf->Output('INTERCONSULTA_'.strtoupper(trim($id)).'.pdf','I');	
        
        
        
        
    }
    
    
    
    
    
    /*
    $nomd_id=$_GET['nomd_id']*1;
    $lista = cargar_registros_obj("
    SELECT 
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
  ORDER BY nomina.nom_fecha ASC, nomd_hora  LIMIT 1
  ", false);


	$pac_id=$lista[0]['pac_id']*1;

	$pac = cargar_registro("SELECT *, COALESCE(pac_clave, md5(substr(md5(pac_id::text),1,5))) AS pac_clave FROM pacientes 
							LEFT JOIN comunas USING (ciud_id)
							LEFT JOIN prevision USING (prev_id)
							WHERE pac_id=$pac_id ", false);

	
	if(strlen($pac['prev_desc'])==1) {
		$pac['prev_desc']='FONASA - GRUPO '.$pac['prev_desc'];
	}
  
  
  
  
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

	//$pdf->SetFillColor(200,200,200);
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
		
	//$pdf->Cell(160,6,$fonos,1,0,'L',1);	
	$pdf->Ln();
 
	//$pdf->SetFillColor(200,200,200);
	//$pdf->Cell(40,6,'Dirección:',1,0,'R',1);	
	//$pdf->SetFillColor(255,255,255);
	//$pdf->SetFont('Arial','', 11);
	//$pdf->Cell(160,6,strtoupper(trim($pac['pac_direccion']).', '.$pac['ciud_desc']),1,0,'L',1);	
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
3) Si por algún motivo no pudiese asistir debe dar aviso al número 25747622-25747621-25745638 para asignar el cupo a otra persona.<br>
4) Con el ánimo de agilizar los trámites, los pacientes deben presentarse 15 minutos antes de la hora de atención en las oficinas de Admisión y Recaudación<br>"))));

	$pdf->SetFont('','B',14);

		
	}

	$pdf->Output('CITACION_'.strtoupper(trim($nomd_id)).'.pdf','I');	
*/
?>
