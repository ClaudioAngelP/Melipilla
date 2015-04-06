<?php
    require_once('../../config.php');
    require_once('../../conectores/sigh.php');
    require_once('../../fpdf/fpdf.php');
    
    function trunc($str,$len)
    {
        if(strlen($str)>$len)
            return substr($str,0,$len).'...';
        else
            return $str;
    }
    if(isset($_GET['nomd_id'])) {
        $nomd_id=$_GET['nomd_id']*1;
        $ref=cargar_registro("SELECT nomdr_id FROM nomina_detalle_referencia WHERE nomd_id=$nomd_id");
        if($ref){
            $nomdr_id=($ref['nomdr_id']*1);
        }else {
            print("Contrarreferencia no encontrada o no realizada");
            exit();
        }
    }
    else {
        $nomdr_id=$_GET['nomdr_id']*1;
    }
    
    $ref=cargar_registro("SELECT nomina_detalle_referencia.*,
    inst1.inst_nombre as inst_nombre,inst2.inst_nombre as inst_nombre2,date_trunc('Second',nomdr_fecha) as fecha_referencia ,
    funcionario.*,
    pacientes.*,
    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,
    date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,
    date_part('day',age(now()::date, pac_fc_nac)) as edad_dias ,
    esp_desc,
    doc_nombres,
    doc_paterno,
    doc_materno
    FROM nomina_detalle_referencia 
    LEFT JOIN instituciones inst1 on inst1.inst_id=nomdr_est_id 
    LEFT JOIN instituciones inst2 on inst2.inst_id=nomdr_est_id2 
    LEFT JOIN funcionario on func_id=nomdr_func_id
    LEFT JOIN nomina_detalle as nd on nd.nomd_id=nomina_detalle_referencia.nomd_id
    LEFT JOIN nomina on nomina.nom_id=nd.nom_id
    LEFT JOIN doctores on doc_id=nomina.nom_doc_id
    LEFT JOIN especialidades on esp_id=nomina.nom_esp_id
    LEFT JOIN pacientes on pacientes.pac_id=nd.pac_id
    WHERE nomdr_id=$nomdr_id");
    
    
    $reg_inter=cargar_registro("select * from interconsulta where inter_nomd_id=".$ref['nomd_id']."",true);
    if($reg_inter){
        $nomdr_diagnostico1=$reg_inter['inter_fundamentos'];
    }
    
    
    class PDF extends FPDF
    {
        function header()
        {
		
            $this->SetFont('Arial','B', 10);
            //$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
            //$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
            //$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);
            $this->Image('logo_min.jpg',5,7,30,15);
            //$this->Ln(20);
            $this->SetY(8);
            $this->SetX(30);
            $this->Cell(150,4,('Ministerio de Salud'),0,0,'L');	
            $this->Ln();
            $this->SetX(30);
            $this->Cell(150,4,('SS Metropolitano Occidente'),0,0,'L');	
            $this->Ln();
            $this->SetX(30);
            $this->Cell(150,4,('Hospital San José de Melipilla'),0,0,'L');	
            $this->Ln();
            //$this->SetFontSize(14);
            //$this->SetY(30);	
	}
    }	
    
    $pdf=new PDF('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf_margin=0;
    
    if($ref)
    {
        $pdf->AddPage();
        $pdf->SetX(23);
        $pdf->SetY(23);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(255,255,255);	
        $pdf->Cell(200,5,('HOJA CONTRARREFERENCIA DE ESPECIALIDADES'),0,0,'C',1);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,6,'Nro Contrarreferencia:',1,0,'C',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(25,6,$ref['nomdr_id'],1,0,'C',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(60,6,'Fecha Contrarreferencia:',1,0,'C',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(65,6,$ref['fecha_referencia'],1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,7,'Registrado por:',1,0,'C',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(150,7,$ref['func_nombre'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,7,'Especialidad de Atención:',1,0,'C',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(150,7,$ref['esp_desc'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(50,7,'Médico Asignado:',1,0,'C',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(150,7,$ref['doc_nombres']." ".$ref['doc_paterno']." ".$ref['doc_materno'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(200,200,200);	
        $pdf->Cell(200,5,('DATOS DEL PACIENTE'),1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(30,6,'Apellidos:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(65,6,$ref['pac_appat']." ".$ref['pac_apmat'],1,0,'L',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(30,6,'Nombres:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(75,6,$ref['pac_nombres'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(30,6,'Edad:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(50,6,$ref['edad_anios']." AÑOS ".$ref['edad_meses']." Meses ".$ref['edad_dias']." días",1,0,'L',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(30,6,'Ficha Clínica:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(30,6,$ref['pac_ficha'],1,0,'C',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(20,6,'RUT:',1,0,'C',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(40,6,$ref['pac_rut'],1,0,'C',1);
        if($reg_inter) {
            $consulta="SELECT e1.esp_desc, inter_fundamentos, inter_examenes,inter_comentarios, 
            inter_estado, inter_rev_med,inter_prioridad,i1.inst_nombre,
            inter_inst_id1,inter_motivo,inter_diag_cod,inter_diagnostico,COALESCE(garantia_nombre, ''),COALESCE(garantia_id, 0),
            i2.inst_nombre AS inst_nombre2, inter_inst_id2,inter_ingreso, ice_icono, ice_desc,unidad.esp_desc AS unidad_desc, 
            inter_unidad,inter_motivo_salida,icc_desc,
            inter_fecha_salida ,inter_id,casos_auge.ca_patologia,inter_pat_id
            FROM interconsulta 
            LEFT JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id 
            LEFT JOIN instituciones AS i1 ON inter_inst_id1=inst_id
            LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id 
            LEFT JOIN garantias_atencion ON inter_garantia_id=garantia_id
            LEFT JOIN interconsulta_estado ON inter_estado=ice_id 
            LEFT JOIN especialidades AS unidad ON inter_unidad=unidad.esp_id		
            LEFT JOIN interconsulta_cierre ON inter_motivo_salida=icc_id
            LEFT JOIN casos_auge ON casos_auge.id_sigges=id_caso
            WHERE inter_id=".$reg_inter['inter_id']."";
            
            $datos2 = pg_query($consulta);
            $inter2 = pg_fetch_row($datos2);
            $pdf->Ln();
            $pdf->SetFont('Arial','B', 10);
            $pdf->SetFillColor(200,200,200);	
            $pdf->Cell(200,5,('DATOS DE INTERCONSULTA DE ORIGEN'),1,0,'C',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','B', 10);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(60,6,'Especialidad Origen:',1,0,'L',1);
            $pdf->SetFont('Arial','', 10);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(140,6,$inter2[0],1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','B', 10);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(60,6,'Especialidad Receptora:',1,0,'L',1);
            $pdf->SetFont('Arial','', 10);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(140,6,$inter2[19],1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','B', 10);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(60,6,'Motivo Derivación:',1,0,'L',1);
            switch($inter2[9])
            {
                case 0: $motivo='Confirmación Diagnóstica'; break;	
                case 1: $motivo='Realizar Tratamiento'; break;	
                case 2: $motivo='Seguimiento'; break;	
                case 3: $motivo='Control Especialidad'; break;	
                case 4: $motivo='Otro Motivo...'; break;
            }
            $pdf->SetFont('Arial','', 10);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(140,6,$motivo,1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','B', 10);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(60,6,'Diagnóstico (Pres.):',1,0,'L',1);
            $pdf->SetFont('Arial','', 10);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(140,6,"[".$inter2[10]."] ".$inter2[11],1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(60,6,'Patología GES:',1,0,'L',1);
            $pdf->SetFont('Arial','', 10);
            $pdf->SetFillColor(255,255,255);
            $dic=cargar_registro("SELECT * FROM interconsulta WHERE inter_id=".$reg_inter['inter_id']."");
            $caso=cargar_registro("SELECT * FROM casos_auge WHERE id_sigges=".$dic['id_caso']);
            if($inter2[25]!=''){
                $pdf->Cell(140,6,$inter2[25],1,0,'L',1);
            } else {
                if(($inter2[26]*1)!=0) {
                    $reg_pat=cargar_registro("SELECT * FROM patologias_auge WHERE pat_id=".($inter2[26]*1)."");
                    if($reg_pat) {
                        $pdf->Cell(140,6,$reg_pat['pat_glosa'],1,0,'L',1);
                    } else {
                        print("<td class='tabla_fila'><b>Error al Encontrar Patologia.</b></td>");
                        $pdf->Cell(140,6,"Error al Encontrar Patologia.",1,0,'L',1);
                    }
                } else {
                    $pdf->Cell(140,6,"No hay sospecha.",1,0,'L',1);
                }
            }
        }
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(200,200,200);	
        $pdf->Cell(200,5,('DATOS DE CONTRARREFERENCIA'),1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(60,6,'Establecimiento Derivador:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(140,6,$ref['inst_nombre2'],1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(60,6,'Fecha Ingreso Especialidad:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(40,6,$ref['nomdr_fecha_ingreso'],1,0,'C',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(60,6,'Fecha Alta Especialidad:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(40,6,$ref['nomdr_fecha_alta'],1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(200,5,('Diagnóstico De Derivación:'),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 7,$ref['nomdr_diagnostico1'], 1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(200,5,('Diagnóstico Especialidad:'),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 7,$ref['nomdr_diagnostico2'], 1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(100,7,('EXÁMENES DE APOYO:'),1,0,'L',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(30,6,'Biopsia Nº:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(25,6,$ref['nomdr_biopsia_nro'],1,0,'C',1);
        /*
        $pdf->SetFont('Arial','B', 11);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(20,6,'Establec.:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(80,6,trunc($ref['inst_nombre'],42),1,0,'L',1);
         * 
         */
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(20,6,'Fecha:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->Cell(25,6,$ref['nomdr_fecha2'],1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(200,7,('Detalle:'),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 7,$ref['nomdr_detalle'], 1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(200,7,('TRATAMIENTO: Médico o Quirúrgico (señalar tipo y fecha de intervención):'),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 7,$ref['nomdr_tratamiento'], 1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(200,7,('INDICACIONES:'),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 7,$ref['nomdr_indicaciones'], 1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(120,6,'SE SUGIERE CONTROL POR ESPECIALISTA:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        if($ref['nomdr_control']=='' or $ref['nomdr_control']=='0') 
            $pdf->Cell(80,6,'',1,0,'C',1);
        else if($ref['nomdr_control']=='1')
            $pdf->Cell(80,6,'Sin Control',1,0,'C',1);
        else if($ref['nomdr_control']=='2')
            $pdf->Cell(80,6,'6 Meses',1,0,'C',1);
        else if($ref['nomdr_control']=='3')
            $pdf->Cell(80,6,'1 Año',1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(200,7,('(Derivar con nueva Interconsulta desde APS):'),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(40,6,'CONTROL APS:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        if($ref['nomdr_control_aps']=='' or $ref['nomdr_control_aps']=='0') 
            $pdf->Cell(40,6,'',1,0,'C',1);
        else if($ref['nomdr_control_aps']=='1')
            $pdf->Cell(40,6,'1 Mes',1,0,'C',1);
        else if($ref['nomdr_control_aps']=='2')
            $pdf->Cell(40,6,'3 Meses',1,0,'C',1);
        else if($ref['nomdr_control_aps']=='3')
            $pdf->Cell(40,6,'6 Meses',1,0,'C',1);
        else if($ref['nomdr_control_aps']=='4')
            $pdf->Cell(40,6,'1 Año',1,0,'C',1);
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(240,240,240);
        $pdf->Cell(60,6,'Pertinecia de la derivación:',1,0,'L',1);
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        if($ref['nomdr_pertinencia']=='' or $ref['nomdr_pertinencia']=='0')
            $pdf->Cell(60,6,'',1,0,'C',1);
        else if($ref['nomdr_pertinencia']=='1')
            $pdf->Cell(60,6,'SI',1,0,'C',1);
        else if($ref['nomdr_pertinencia']=='2')
            $pdf->Cell(60,6,'NO',1,0,'C',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','B', 10);
        $pdf->SetFillColor(200,200,200);
        $pdf->Cell(200,7,('(si la derivación no fué pertinente, señalar ¿por que?)'),1,0,'L',1);
        $pdf->Ln();
        $pdf->SetFont('Arial','', 10);
        $pdf->SetFillColor(255,255,255);
        $pdf->MultiCell(200, 7,$ref['nomdr_porque'], 1);
        $pdf->Ln(10);
        $pdf->SetFont('Arial','', 10);
        $pdf->Cell(200,6,'___________________________________',0,1,'C');
        $pdf->Cell(200,6,'Nombre y Firma del Médico',0,1,'C');
        
    }
    
    
    
    
    
    $pdf->Output('contrareferencia_'.strtoupper(trim($nomdr_id)).'.pdf','I');
    
    /*
    function trunc($str,$len)
    {
        if(strlen($str)>$len)
            return substr($str,0,$len).'...';
        else
            return $str;
    }
     * 
     */

    
    
    
    /*
    $lista = cargar_registros_obj("SELECT 
    nomina_detalle.nomd_id, 
    nom_fecha::date as fecha_nomina, 
    nomd_hora, 
    doc_rut, 
    doc_paterno, 
    doc_materno, 
    doc_nombres, 
    COALESCE(diag_desc, cancela_desc) AS diag_desc, 
    nomd_diag_cod,
    esp_desc, 
    nomd_tipo, 
    CASE WHEN nom_fecha>=CURRENT_DATE THEN 'P' ELSE 'A' END AS estado,
    nomd_codigo_presta, 
    glosa, 
    esp_lugar, 
    COALESCE(esp_nombre_especialidad, esp_desc) AS esp_nombre_especialidad,
    nomina_detalle.id_sidra,
    upper(func_nombre) AS asigna_nombre, 
    '260194' AS esp_fono,
    *
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
    
    */
    
    
    /*
    $pac_id=$lista[0]['pac_id']*1;
    $pac = cargar_registro("SELECT *,
    date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,
    date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,
    date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
    COALESCE(pac_clave, md5(substr(md5(pac_id::text),1,5))) AS pac_clave FROM pacientes 
    LEFT JOIN comunas USING (ciud_id) 
    LEFT JOIN prevision USING (prev_id) 
    LEFT JOIN sexo USING (sex_id)
    WHERE pac_id=$pac_id ", false);
    if(strlen($pac['prev_desc'])==1)
    {
        $pac['prev_desc']='FONASA - GRUPO '.$pac['prev_desc'];
    }
     * 
     */
    /*
    $regs_examenes=cargar_registros_obj("select *,(select count(*) from solicitud_examen_detalle where sol_examd_solexam_id=sol_exam_id)as cant 
    from solicitud_examen where sol_nomd_id_original=$nomd_id order by sol_esp_id,sol_tipo_examen desc");
    
    $reg_receta=cargar_registro("select * from receta where receta_nomd_id=$nomd_id");
    */
     
    
    
    
    
    
    
?>
