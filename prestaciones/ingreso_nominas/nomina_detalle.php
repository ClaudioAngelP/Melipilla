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

    $nomd_id=$_GET['nomd_id']*1;
    
    
    
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
    
    $regs_examenes=cargar_registros_obj("select *,(select count(*) from solicitud_examen_detalle where sol_examd_solexam_id=sol_exam_id)as cant 
    from solicitud_examen where sol_nomd_id_original=$nomd_id order by sol_esp_id,sol_tipo_examen desc");
    
    $reg_receta=cargar_registro("select * from receta where receta_nomd_id=$nomd_id");
    
     
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
            $this->Cell(150,4,utf8_decode('Hospital San Jose de Melipilla'),0,0,'L');	
            $this->Ln();
            //$this->SetFontSize(14);
            //$this->SetY(30);	
	}
    }	
    
    $pdf=new PDF('P','mm','Letter');
    $pdf->AliasNbPages();
    $pdf_margin=0;
    
    //$pdf->SetAutoPageBreak(true,20);
    //$pdf->AddPage();
    
    
    if($lista)
    {
        for($i=0;$i<count($lista);$i++)
        {
            if($i%2==0)
            {
                $pdf->AddPage();
                $pdf->SetY(8);
                $pdf->SetX(150);
                $pdf->SetFillColor(240,240,240);	
                $pdf->Cell(30,5,utf8_decode('Fecha Atención'),1,0,'C',1);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(20,5,$lista[$i]['fecha_nomina'],1,0,'L',1);	
                $pdf->SetX(30);
                $pdf->SetY(30);
		$pdf_margin=$pdf->GetX();
		$pdf->SetLeftMargin($pdf_margin);
            }
            $pdf->SetFontSize(12);
            $pdf->SetLeftMargin($pdf_margin);
            $pdf->SetFont('Arial','', 12);
            $pdf->SetFillColor(200,200,200);	
            $pdf->Cell(200,5,utf8_decode('REGISTRO DE ATENCIÓN ABIERTA'),1,0,'C',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,7,utf8_decode('N° Folio:'),1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(40,7,$lista[$i]['nom_folio'],1,0,'L',1);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(30,7,'Tipo Consulta:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(90,7,$lista[$i]['nom_motivo'],1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,7,'Medico:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(160,7,$lista[$i]['doc_nombres']." ".$lista[$i]['doc_paterno']." ".$lista[$i]['doc_materno'],1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,7,'Especialidad:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(160,7,$lista[$i]['esp_desc'],1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,7,'Hora de Consulta:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(25,7,substr($lista[$i]['nomd_hora'],0,5).' Hrs.',1,0,'L',1);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(25,7,'Estado:',1,0,'R',1);
            $pdf->SetFont('Arial','', 10);
            $pdf->SetFillColor(255,255,255);
            if($lista[$i]['nomd_diag_cod']=="")
                $estado="AGENDADO";
            else if($lista[$i]['nomd_diag_cod']=="OK")
                $estado="ATENDIDO";
            else if($lista[$i]['nomd_diag_cod']=="ALTA")
                $estado="ALTA DE ESPECIALIDAD";
            else if($lista[$i]['nomd_diag_cod']=="N")
                $estado="NO ATENDIDO";
            else if($lista[$i]['nomd_diag_cod']=="T")
                $estado="SUSPENDIDO";
            else if($lista[$i]['nomd_diag_cod']=="X")
                $estado="BLOQUEADO";
            
            $pdf->Cell(50,7,$estado,1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','', 12);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,7,'Interconsulta:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(160,7,"",1,0,'L',1);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFont('Arial','', 12);
            $pdf->SetFillColor(200,200,200);	
            $pdf->Cell(200,5,('DATOS DEL PACIENTE'),1,0,'C',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(40,6,'Nombre:',1,0,'R',1);
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(160,6,strtoupper($pac['pac_nombres']).' '.strtoupper($pac['pac_appat']).' '.strtoupper($pac['pac_apmat']),1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(35,6,'RUN:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(35,6,$pac['pac_rut'],1,0,'L',1);
            $pdf->SetFillColor(240,240,240);
            $pdf->Cell(15,6,'SEXO:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(30,6,$pac['sex_desc'],1,0,'L',1);
            $pdf->Cell(15,6,'Edad:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(70,6,utf8_decode($pac['edad_anios']." AÑOS ".$pac['edad_meses']." Meses ".$pac['edad_dias']." días"),1,0,'L',1);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFillColor(200,200,200);
            $pdf->Cell(40,6,'DIAGNOSTICO:',1,0,'R',1);	
            $pdf->SetFillColor(255,255,255);
            $pdf->Cell(160,6,str_replace("|"," : ",$lista[$i]['nomd_diag']),1,0,'L',1);
            $pdf->Ln();
            $pdf->Ln();
            $pdf->SetFont('Arial','', 12);
            $pdf->SetFillColor(200,200,200);	
            $pdf->Cell(200,5,('OBSERVACIONES'),1,0,'L',1);
            $pdf->Ln();
            $pdf->SetFont('Arial','', 10);
            $pdf->SetFillColor(255,255,255);
            $pdf->MultiCell(200, 7,$lista[$i]['nomd_observaciones'], 1);
			
			 $cargar_prestacion = 
	    			cargar_registros_obj(
		    			"
							SELECT pc_desc, pc_codigo  FROM nomina_detalle
							JOIN solicitud_examen_detalle ON sol_examd_nomd_id = nomd_id
							LEFT JOIN procedimiento_codigo ON pc_id = sol_examd_cod_presta
							LEFT JOIN examen_kits ON kit_codigo = sol_examd_cod_presta::TEXT
							WHERE  nomd_id=$nomd_id
		    			");	
			if($cargar_prestacion)
			{	$pdf->SetFont('Arial','', 12);
                $pdf->SetFillColor(200,200,200);
                $pdf->Cell(200,5,('Examenes Solicitados'),1,0,'C',1);
                $pdf->Ln();
				for($i=0;$i<count($cargar_prestacion);$i++)
        		{
							
                            $pdf->Cell(25,5,$cargar_prestacion[$i]['pc_codigo'],1,0,'C',1);
                            $pdf->SetFillColor(255,255,255);
							$pdf->Cell(175,5,$cargar_prestacion[$i]['pc_desc'],1,0,'L',1);
				}
			}
			
			
			
            if($regs_examenes)
            {
                $pdf->SetFont('Arial','', 12);
                $pdf->SetFillColor(200,200,200);
                $pdf->Cell(200,5,('Examenes Solicitados'),1,0,'C',1);
                $pdf->Ln();
                for($x=0;$x<count($regs_examenes);$x++)
                {
                    $tipo_exam=$regs_examenes[$x]['sol_tipo_examen'];
                    $regs_exam_detalle=cargar_registros_obj("SELECT pc_codigo,pc_desc FROM solicitud_examen_detalle LEFT JOIN procedimiento_codigo on pc_id=sol_examd_cod_presta where sol_examd_solexam_id=".$regs_examenes[$x]['sol_exam_id']."");
                    if($regs_exam_detalle)
                    {
                        for($y=0;$y<count($regs_exam_detalle);$y++)
                        {
                            $pdf->SetFont('Arial','', 9);
                            $pdf->SetFillColor(240,240,240);
                            $pdf->Cell(25,5,$regs_exam_detalle[$y]['pc_codigo'],1,0,'C',1);
                            $pdf->SetFillColor(255,255,255);
                            $desc_examen=$regs_exam_detalle[$y]['pc_desc'];
                            
                            if(strlen($desc_examen)>45)
                            {
                                $desc_examen=substr($desc_examen,1,40).'...';
                            }
                            if($tipo_exam!="")
                                $pdf->Cell(175,5,$desc_examen." -- [ ".$tipo_exam." ]",1,0,'L',1);
                            else
                                $pdf->Cell(175,5,$desc_examen,1,0,'L',1);
                            $pdf->Ln();
                        }
                    }
                }
                $pdf->Ln();
            }
            if($reg_receta)
            {
                $pdf->SetFont('Arial','', 12);
                $pdf->SetFillColor(200,200,200);
                $pdf->Cell(200,5,('Medicamentos Indicados'),1,0,'C',1);
                $pdf->Ln();
                $pdf->SetFont('Arial','', 10);
                $pdf->Cell(95,5,('Medicamento'),1,0,'C',1);
                $pdf->Cell(75,5,('Dosis'),1,0,'C',1);
                $pdf->Cell(30,5,('Recetado'),1,0,'C',1);
                $pdf->Ln();
                $regs_receta_detalle=cargar_registros_obj("SELECT *, 
                (
                    case when art_unidad_cantidad is null then 
                        (((recetad_dias*24)/recetad_horas)*recetad_cant)
                    else
                        ceil((((recetad_dias*24)/recetad_horas)*recetad_cant)/art_unidad_cantidad)
                    end
                )as total    
                FROM recetas_detalle
                LEFT JOIN articulo on art_id=recetad_art_id 
                LEFT JOIN bodega_forma ON art_forma=forma_id
                where recetad_receta_id in (select receta_id from receta where receta_nomd_id=$nomd_id)");
                if($regs_receta_detalle)
                {
                    for($y=0;$y<count($regs_receta_detalle);$y++)
                    {
                        $pdf->SetFillColor(255,255,255);
                        $pdf->SetFont('Arial','', 9);
                        $pdf->Cell(95,5,$regs_receta_detalle[$y]['art_glosa'],1,0,'L',1);
                        if($rec[$j]['recetad_horas']*1<=24)
                        {
                            $div_h=1;
                            $txt_horas='horas';
                        }
                        else
                        {
                            if(($regs_receta_detalle[$y]['recetad_horas'])%24==0)
                            {
                                $div_h=24;
                                $txt_horas='día(s)';
                            }
                            else
                            {
                                $div_h=1;
                                $txt_horas='horas';
                            }
                        }
                        if($regs_receta_detalle[$y]['recetad_dias']*1<=30)
                        {
                            $div_d=1;
                            $txt_dias='día(s).';
                        }
                        else
                        {
                            if(($regs_receta_detalle[$y]['recetad_dias'])%30==0)
                            {
                                $div_d=30;
                                $txt_dias='mes(es).';
                            }
                            else
                            {
                                $div_d=1;
                                $txt_dias='día(s).';
                            }
                        }
                        $pdf->SetFont('Arial','', 10);
                        $pdf->Cell(75,5,$regs_receta_detalle[$y]['recetad_cant'].' '.strtoupper($regs_receta_detalle[$y]['forma_nombre']).' cada '.($regs_receta_detalle[$y]['recetad_horas']/$div_h).' '.$txt_horas.' durante '.($regs_receta_detalle[$y]['recetad_dias']/$div_d).' '.$txt_dias,1,0,'L',1);
                        $pdf->Cell(30,5,number_format($regs_receta_detalle[$y]['total'],2,',',''),1,0,'C',1);
                        $pdf->Ln();
                    }
                }
            }
            $reg_inter=cargar_registro("select * from interconsulta where inter_origen_nomd_id=$nomd_id");
            if($reg_inter)
            {
                $pdf->AddPage();
                
                $id=$reg_inter['inter_id'];
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
                if($inter[0]=='-1')
                    $inter[0]='INT-'.$id;
                $pdf->Ln();
                $pdf->Ln();
                $pdf->SetFillColor(200,200,200);	
                $pdf->SetFont('Arial','', 14);
                $pdf->SetFillColor(130,130,130);
                $pdf->Cell(200,7,('INTERCONSULTA SOLICITADA'),1,0,'C',1);
                $pdf->Ln();
                $pdf->SetFillColor(200,200,200);
                $pdf->Cell(40,6,utf8_decode('N° Interconsulta:'),1,0,'R',1);
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
				
                $pdf->Cell(160,6,utf8_decode($dia.' '.substr($inter2[16],0,10)).'',1,0,'L',1);	
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
                $pdf->Cell(200,6,utf8_decode('Datos del Clínicos'),1,0,'L');
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
                $pdf->Cell(200,7,utf8_decode('Hipótesis Diagnostica:'),1,0,'L',1);	
                $pdf->Ln();
                $pdf->SetFont('Arial','', 10);
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(200,7,"[".$inter2[10]."] - ".htmlentities($inter2[11]),1,0,'L',1);
                $dic=cargar_registro("SELECT * FROM interconsulta WHERE inter_id=$id");
                $caso=cargar_registro("SELECT * FROM casos_auge WHERE id_sigges=".$dic['id_caso']);
                $pdf->Ln();
                $pdf->SetFont('Arial','', 14);
                $pdf->SetFillColor(200,200,200);
                $pdf->Cell(40,7,utf8_decode('Patología GES:'),1,0,'R',1);
                $pdf->SetFillColor(255,255,255);
                if($caso)
                    $pdf->Cell(160,7,$caso['ca_patologia'],1,0,'L',1);
                else
                    $pdf->Cell(160,7,'No hay sospecha.',1,0,'L',1);
                $pdf->Ln();
                $pdf->SetFont('Arial','', 12);
                $pdf->SetFillColor(200,200,200);
                $pdf->Cell(200,7,utf8_decode('Fundamentos Cl�nicos:'),1,0,'L',1);
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
                    $pdf->Cell(200,7,'Ex�menes Comp.:',1,0,'L',1);
                    $pdf->Ln();
                    $pdf->SetFont('Arial','', 8);
                    $pdf->SetFillColor(255,255,255);
                    $pdf->MultiCell(200, 6, $inter2[2], 1);
                }
                
                
            }
            $pdf->Ln(15);
            $pdf->SetFont('Arial','', 12);
            $pdf->Cell(200,6,'___________________________________',0,1,'C');
            $pdf->Cell(200,6,utf8_decode('Nombre y Firma del Médico'),0,1,'C');
            
           
                       
            
            
            
        }
    }
    $pdf->Output('registro_atencion_'.strtoupper(trim($nomd_id)).'.pdf','I');
?>
