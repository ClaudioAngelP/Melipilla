<?php
    
    chdir(dirname(__FILE__));
    require_once('../config.php');
    require_once('../conectores/sigh.php');
    
    
    function formato_rut($rut) {
		$r=explode('-',$rut);
		return number_formats($r[0]*1).'-'.$r[1];	
    } 
    function number_formats($numero) {
        return number_format($numero, 0, ',', '.');
    }
    
    
    $doc_id="";
    if($argv[1]!='correo_nomina_atencion.php')
        $doc_id=$argv[1]*1;
    else
        $doc_id=$argv[2]*1;
    
    if($doc_id!="")
        $w_doc='nom_doc_id='.$doc_id.'';
    else
        $w_doc='true';
    
    $q="
    SELECT 
    nom_id, nom_folio, esp_desc, doc_rut, doc_paterno, doc_materno, doc_nombres,
    nom_digitar, nom_motivo,
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND pac_id=0 AND nomd_diag_cod NOT IN ('X','T','B')) AS libres,
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_diag_cod NOT IN ('T')) AS ocupados,
    nom_tipo_contrato,
    nom_estado,
    (SELECT MIN(nomd_hora) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id limit 1)as min_hora,
    (select sum(cupos_cantidad_c) from cupos_atencion where cupos_atencion.nom_id=nomina.nom_id group by cupos_atencion.nom_id)as extras,
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_extra='S' AND nomd_diag_cod NOT IN ('T'))as extras_ocupados,
    (
    (select sum(cupos_cantidad_c) from cupos_atencion where cupos_atencion.nom_id=nomina.nom_id group by cupos_atencion.nom_id)
    -
    (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_extra='S' AND nomd_diag_cod NOT IN ('T'))
    )as extras_disponibles,
    (select (substr(cupos_horainicio::text,1,5)  || ' - ' || substr(cupos_horafinal::text,1,5)) from cupos_atencion where cupos_atencion.nom_id=nomina.nom_id )as horario_atencion,
    nom_tipo
    FROM nomina
    JOIN especialidades ON nom_esp_id=esp_id
    JOIN doctores ON nom_doc_id=doc_id
    WHERE nom_fecha::date='".date('d/m/Y')."' AND $w_doc 
    ORDER BY esp_desc, doc_paterno, doc_materno, doc_nombres,min_hora
    ";
    
    
    $lista = cargar_registros_obj($q,true);
    
    /*
    $q=cargar_registros_obj("
    SELECT * FROM (
        SELECT *,
        upper(pac_nombres) as pac_nombres, upper(pac_appat) as pac_appat, upper(pac_apmat) as pac_apmat,
	hosp_fecha_ing::date AS hosp_fecha_ing,
	hosp_fecha_ing::time AS hosp_hora_ing,
	hosp_fecha_egr::date,
	(CURRENT_DATE-COALESCE(hosp_fecha_hospitalizacion, hosp_fecha_ing)::date) AS dias_espera,
	t1.tcama_tipo AS tcama_tipo, t1.tcama_critico AS critico, t1.tcama_num_ini AS tcama_num_ini,
	t2.tcama_tipo AS servicio

			FROM hospitalizacion
			JOIN pacientes ON hosp_pac_id=pac_id

			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id

			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod

			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
			LEFT JOIN clasifica_camas AS t1 ON
				t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama

			LEFT JOIN clasifica_camas AS t2 ON
				t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio

			WHERE hosp_numero_cama>0 AND hosp_fecha_egr IS NULL AND
			((CURRENT_DATE-COALESCE(hosp_fecha_hospitalizacion, hosp_fecha_ing)::date) BETWEEN 5 AND 10000)

			) AS foo ORDER BY dias_espera DESC

		");
            */
    require_once('../PHPExcel/Classes/PHPExcel.php');
    require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';

    $objPHPExcel = new PHPExcel();

		// Set document properties
    echo date('H:i:s') , " Set document properties" , PHP_EOL;
    $objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital San José de Melipilla")
    ->setLastModifiedBy(utf8_encode("Sistema GIS Hospital San José de Melipilla"))
    ->setTitle(utf8_encode("Pacientes Agendados para el día ".date('d/m/Y H:i:s').""))
    ->setSubject(utf8_encode("Pacientes Agendados para el día ".date('d/m/Y H:i:s').""))
    ->setDescription(utf8_encode("Pacientes Agendados para el día ".date('d/m/Y H:i:s').""))
    ->setKeywords("pacientes gis melipilla hospital")
    ->setCategory("Reportes");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('C1', utf8_encode('HOSPITAL SAN JOSÉ DE MELIPILLA - SISTEMA GIS'));
    $objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', utf8_encode('Pacientes Agendados para El Día: - Nomina de Atención: '));
    $objPHPExcel->getActiveSheet()->setCellValue('B3', utf8_encode('Fecha Emisión:'));
    $objPHPExcel->getActiveSheet()->setCellValue('C3', date('d/m/Y H:i:s'));
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(16);
    $objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
    $objPHPExcel->getActiveSheet()->getStyle('C1:C3')->applyFromArray(
    array(
    'font'    => array(
    'bold'      => true
    )
    )
    );
    
    $objPHPExcel->getActiveSheet()->getStyle('B2:B3')->applyFromArray(
    array(
    'alignment' => array(
    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
    )
    )
    );

    $objPHPExcel->getActiveSheet()->setCellValue('A5', 'RUT');
    $objPHPExcel->getActiveSheet()->setCellValue('B5', 'Ficha');
    $objPHPExcel->getActiveSheet()->setCellValue('C5', 'Nombre Completo');
    $objPHPExcel->getActiveSheet()->setCellValue('D5', '(Sub)Especialidad');
    $objPHPExcel->getActiveSheet()->setCellValue('E5', 'Servicio Ingreso');
    $objPHPExcel->getActiveSheet()->setCellValue('F5', 'Médico Tratante');
    $objPHPExcel->getActiveSheet()->setCellValue('G5', 'Fecha de Ingreso');
    $objPHPExcel->getActiveSheet()->setCellValue('H5', 'Servicio / Sala');
    $objPHPExcel->getActiveSheet()->setCellValue('I5', 'Cama');
    $objPHPExcel->getActiveSheet()->setCellValue('J5', utf8_encode('Diagnóstico'));
    $objPHPExcel->getActiveSheet()->setCellValue('K5', utf8_encode('Días Hosp.'));
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray(
    array(
        'font'  =>  array('bold'            =>  true),
        'alignment' => array('horizontal'   =>  PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
        'borders' => array('top'            => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
        'fill' => array('type'     =>  PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                        'rotation' =>   90,
                        'startcolor' => array('argb' => 'FFA0A0A0'),
                        'endcolor'   => array('argb' => 'FFFFFFFF'))
    )
    );
		
    $csv=utf8_decode("R.U.T.;Ficha;Nombre Completo;(Sub)Especialidad;Servicio Ingreso;M&eacute;dico Tratante;Fecha Ingreso;Servicio / Sala;Cama;Diagnóstico;Días Hosp.\n");

    $servicios=Array();
    $especialidades=Array();
		
    $esp_sort=Array();
    $serv_sort=Array();
		
    $total_noesp=0;
    $total_nomed=0;
    $total_pacs=0;

    //if($q)
        /*
        for($i=0;$i<sizeof($q);$i++) {
            if($q[$i]['esp_desc']!='')
                $especialidad=$q[$i]['esp_desc'];
            else
                $especialidad='(Sin Asignar...)';

            if($q[$i]['tcama_tipo']!='')
                $servicio=$q[$i]['tcama_tipo'];
            else
                $servicio='(Sin Asignar...)';

            if($q[$i]['doc_rut']!='')
                $med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
            else
                $med_tratante='(Sin Asignar...)';
				
            if(!isset($servicios[$servicio])) {
                $servicios[$servicio]=Array();
		$servicios[$servicio]['critico']=$q[$i]['critico'];
		$servicios[$servicio]['total']=0;
		$servicios[$servicio]['nomed']=0;
		$servicios[$servicio]['noesp']=0;
		$serv_sort[]=$servicio;
            }

            if(!isset($especialidades[$especialidad])) {
                $especialidades[$especialidad]=Array();
		$especialidades[$especialidad]['nombre']=$especialidad;
		$especialidades[$especialidad]['total']=0;
		$especialidades[$especialidad]['nomed']=0;
		$esp_sort[]=$especialidad;
            }	
			
            $csv.=$q[$i]['pac_rut'].";";
            $csv.=$q[$i]['pac_ficha'].";";
            $csv.=($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres']).";";
            $csv.=$especialidad.";";
            $csv.=$servicio.";";
            $csv.=$med_tratante.";";
            $csv.=$q[$i]['hosp_fecha_ing'].";";
            $csv.=$q[$i]['tcama_tipo'].' '.$q[$i]['cama_tipo'].";";
            $csv.=(($q[$i]['hosp_numero_cama']*1-$q[$i]['tcama_num_ini']*1)+1).";";
            $csv.=$q[$i]['hosp_diag_cod']." ".$q[$i]['diag_desc'].";";
            $csv.=$q[$i]['dias_espera'].";";
            $csv.="\n";
			
			
            $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+6), utf8_encode($q[$i]['pac_rut']));
            $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+6), $q[$i]['pac_ficha']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+6), utf8_encode($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres']));
            $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+6), utf8_encode($especialidad));
            $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+6), utf8_encode($servicio));
            $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+6), utf8_encode($med_tratante));
            $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+6), $q[$i]['hosp_fecha_ing']);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+6), utf8_encode($q[$i]['tcama_tipo'].' '.$q[$i]['cama_tipo']));
            $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+6), (($q[$i]['hosp_numero_cama']*1-$q[$i]['tcama_num_ini']*1)+1));
            $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+6), utf8_encode($q[$i]['hosp_diag_cod']." ".$q[$i]['diag_desc']));
            $objPHPExcel->getActiveSheet()->setCellValue('K'.($i+6), $q[$i]['dias_espera']);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('D'.($i+6))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('G'.($i+6))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getFont()->setBold(true);
			
            $objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('G'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
            $color=($i%2==0)?'DDDDDD':'EEEEEE';
			
            $objPHPExcel->getActiveSheet()->getStyle('A'.($i+6).':K'.($i+6))->applyFromArray(
            array(
                'fill' => array(
                'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
		'rotation'   => 90,
		'startcolor' => array(
                    'argb' => 'FF'.$color
                ),
		'endcolor'   => array(
                    'argb' => 'FFFFFFFF'
		)
		)
            )
            );

            $servicios[$servicio]['total']++;
            $especialidades[$especialidad]['total']++;
            $total_pacs++;
			
            if($q[$i]['doc_id']*1==0) {
                $servicios[$servicio]['nomed']++;
            }
            
            if(trim($q[$i]['esp_desc'])=='') {
                $servicios[$servicio]['noesp']++;
		$total_noesp++;
            }

            if($q[$i]['doc_id']*1==0)
                $especialidades[$especialidad]['nomed']++;
        }
        **/
    
        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('/tmp/listado_pacientes_hosp_excedidos.xlsx');
        

        file_put_contents('/tmp/listado_pacientes_hosp_excedidos.csv',$csv);

        //$total=sizeof($q);
        $total=100;

        $resumen1='<table style="width:80%;">
        <tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
        <td style="text-align:center;font-weight:bold;">Nro. Folio</td>
        <td style="text-align:center;font-weight:bold;">Horario Atenci&oacute;n</td>
        <td style="text-align:center;font-weight:bold;">Especialidad</td>
        <td style="text-align:center;font-weight:bold;">R.U.T.</td>
        <td style="text-align:center;font-weight:bold;">Nombre</td>
        <td style="text-align:center;font-weight:bold;">Tipo</td>
        <td style="text-align:center;font-weight:bold;">Tipo Contrato</td>
        <td style="text-align:center;font-weight:bold;">Cupos Libres</td>
        <td style="text-align:center;font-weight:bold;">Cupos Ocupados</td>
        <td style="text-align:center;font-weight:bold;">Extras Disp.</td>
        </tr>';
        $cont=0;
        if($lista)
        {
            for($i=0;$i<count($lista);$i++)
            {
                if($lista[$i]['nom_estado']=='10' or $lista[$i]['nom_estado']=='11')
                    continue;

                $color=(($cont++)%2==0)?'#dddddd':'#eeeeee';
                if($lista[$i]['nom_estado']=='-1')
                {
                   $color='background-color:#ffff80;';
                }
                if($i==0)
                {
                    $resumen1.='<tr  style="text-align:center;font-size:14px;background-color:SkyBlue;"">';
                        $resumen1.='<td style="text-align:center;font-size:14px;background-color:SkyBlue;" colspan="3">'.((formato_rut($lista[$i]['doc_rut']))).'</td>';
                    $resumen1.='</tr>';
                }
                else
                {
                    $resumen1.='<tr bgcolor="'.$color.'">';
                }
                
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.$lista[$i]['nom_folio'].'</td>';
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.$lista[$i]['horario_atencion'].'</td>';
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.$lista[$i]['esp_desc'].'</td>';
                    /*
                    if($lista[$i]['doc_rut']!='(n/a)' AND $lista[$i]['doc_rut']!='')
                    {
                        $resumen1.='<td style="text-align:center;font-size:12px;">'.((formato_rut($lista[$i]['doc_rut']))).'</td>';
                    }
                    else
                    {
                        $resumen1.='<td style="text-align:center;font-size:12px;">&nbsp;</td>';
                        
                    }
                     * 
                     */
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.($lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno'].' '.$lista[$i]['doc_nombres']).'</td>';
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.utf8_encode($lista[$i]['nom_motivo']).'</td>';
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.utf8_encode($lista[$i]['nom_tipo_contrato']).'</td>';
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.utf8_encode($lista[$i]['libres']).'</td>';
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.utf8_encode($lista[$i]['ocupados']).'</td>';
                    $resumen1.='<td style="text-align:center;font-size:12px;">'.utf8_encode($lista[$i]['extras_disponibles']).'</td>';
                $resumen1.='</tr>';
            }
            $resumen1.='</table>';
            $pacientes_resumen1="";
            if($doc_id!="")
            {
                
                $q="SELECT DISTINCT 
                esp_id,esp_desc
                FROM nomina
                JOIN especialidades ON nom_esp_id=esp_id
                JOIN doctores ON nom_doc_id=doc_id
                WHERE nom_fecha::date='".date('d/m/Y')."' AND doc_id=$doc_id and nom_tipo!=1
                ORDER BY esp_desc";
                
                $nominas_esp= cargar_registros_obj($q,true);
                
                if($nominas_esp)
                {
                    for($x=0;$x<count($nominas_esp);$x++)
                    {
                        $consulta="
                        SELECT 
                        pacientes.*, nomina_detalle.*, diag_desc,nom_motivo,esp_recurso, 
                        date_part('year',age(pac_fc_nac)) as edad, cancela_desc,nom_esp_id,nom_doc_id,prev_desc
                        FROM nomina_detalle
                        JOIN nomina USING (nom_id)
                        JOIN especialidades ON nom_esp_id=esp_id
                        LEFT JOIN pacientes USING (pac_id)
                        LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
                        LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
                        LEFT JOIN prevision on pacientes.prev_id=prevision.prev_id
                        WHERE nom_fecha='".date('d/m/Y')."' and nom_doc_id=$doc_id and nom_esp_id=".$nominas_esp[$x]['esp_id']."
                        and (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL)
                        and nom_tipo!=1
                        order by nomd_hora asc";
                        
                        $nomina_detalle= cargar_registros_obj($consulta);
                        
                        if($nomina_detalle)
                        {
                            if($x>0)
                                $pacientes_resumen1.='<br /><br /><br />';
                                
                                
                            $pacientes_resumen1.='<table style="width:80%;">
                            <tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
                                <td colspan="4" style="text-align:center;font-weight:bold;">ESPECIALIDAD: '.$nominas_esp[$x]['esp_desc'].'</td>
                            </tr>
                            <tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
                                <td style="text-align:center;font-weight:bold;font-size:12px;">#</td>
                                <td style="text-align:center;font-weight:bold;font-size:12px;">Hora</td>
                                <td style="text-align:center;font-weight:bold;font-size:12px;">RUT/Ficha</td>
                                <td style="text-align:center;font-weight:bold;font-size:12px;">Paciente</td>
                            </tr>';
                            $cont_pac=0;
                            for($y=0;$y<count($nomina_detalle);$y++)
                            {
                                ($cont_pac%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                                $cont_pac++;
                                if($nomina_detalle[$y]['sex_id']==0) $sexo='M';
                                elseif($nomina_detalle[$y]['sex_id']==1) $sexo='F';
                                else $sexo='I';
                                
                                if($nomina_detalle[$y]['nomd_diag_cod']=='B')
                                    continue;
                                
                                if($nomina_detalle[$y]['pac_id']==0)
                                {
                                    //$completo=false;
                                    ($cont_pac%2==0) ? $color='#BBDDBB' : $color='#BBEEBB';
                                    if($nomina_detalle[$y]['nomd_diag_cod']=='X')
                                    {
                                        ($cont_pac%2==0) ? $color='#ff8888' : $color='#ee8888';
                                        $cestado='BLOQUEADO ('.$nomina_detalle[$y]['cancela_desc'].')';
                                    }
                                    else
                                    {
                                        if($nomina_detalle[$y]['nomd_extra']=="S")
                                        {
                                            //$sobrecupos_nomina=$sobrecupos_nomina+1;
                                            $color='#ff9933';
                                        }
                                        $ntipo=$nomina_detalle[$y]['nom_motivo'];
                                        if($ntipo!='')
                                            $cestado='DISPONIBLE ('.$ntipo.')';
                                        else
                                            $cestado='DISPONIBLE';
                                    }
                                    //$nombrecupo=$nom_recurso?'BLOQUE':'CUPO';
                                    $nombrecupo='CUPO';
                                    $hora_arr=str_replace(":",".",substr($nomina_detalle[$y]['nomd_hora'],0,5));
                                    $colspan=4;
                                    $pacientes_resumen1.="<tr style='height:30px;background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";' onClick=''>";
                                        $pacientes_resumen1.="<td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($y+1)."</td>";
                                        $pacientes_resumen1.="<td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($nomina_detalle[$y]['nomd_hora'],0,5)."</td>";
                                        $pacientes_resumen1.="<td style='text-align:center;font-weight:bold;font-size:16px;' colspan=$colspan><i>$nombrecupo $cestado</i></td>";
                                    $pacientes_resumen1.="</tr>";
                                    continue;
                                }
                                if($nomina_detalle[$y]['nomd_diag_cod']!='X' AND $nomina_detalle[$y]['nomd_diag_cod']!='T')
                                {
                                    if($nomina_detalle[$y]['nomd_extra']=="S")
                                    {
                                        //$sobrecupos_nomina=$sobrecupos_nomina+1;
                                        $color='#ff9933';
                                    }
                                    else
                                    {
                                        $color='';
                                        ($cont_pac%2==0) ? $color='#dddddd' : $color='#eeeeee';
                                    }
                                    $pacientes_resumen1.="<tr class='$clase' style='height:30px;background-color:$color' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";' onClick=''>";
                                }
                                else
                                {
                                    ($cont_pac%2==0) ? $color='#ff8888' : $color='#ee8888';
                                    $pacientes_resumen1.="<tr style='height:30px;background-color:$color' onMouseOver='this.style.background=\"#dddddd\";' onMouseOut='this.style.background=\"".$color."\";'>";
                                }
                                
                                if($nomina_detalle[$y]['nomd_diag_cod']=='X' OR $nomina_detalle[$y]['nomd_diag_cod']=='T' OR $nomina_detalle[$y]['nomd_diag_cod']=='N')
                                {
                                    if($nomina_detalle[$y]['nomd_diag_cod']=='X')
                                    {
                                        ($cont_pac%2==0) ? $color='#ff8888' : $color='#ee8888';
                                        $cestado='BLOQUEADO ('.$nomina_detalle[$y]['cancela_desc'].')';
                                        $colspan=4;
                                        $pacientes_resumen1.="<td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($y+1)."</td>";
                                        $pacientes_resumen1.="<td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($nomina_detalle[$y]['nomd_hora'],0,5)."</td>";
                                        $pacientes_resumen1.="<td style='text-align:center;font-weight:bold;font-size:16px;' colspan=$colspan><i>".$nombrecupo." ".$cestado."<font size='1'> [".$nomina_detalle[$y]['pac_rut']." ".htmlentities(strtoupper($nomina_detalle[$y]['pac_appat'].' '.$nomina_detalle[$y]['pac_apmat'].' '.$nomina_detalle[$y]['pac_nombres']))."]</font></i></td>";
                                        $pacientes_resumen1.="</tr>";
                                        continue;
                                    }
                                }
                                $pacientes_resumen1.="<td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($y+1)."</td>";
                                $pacientes_resumen1.="<td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($nomina_detalle[$y]['nomd_hora'],0,5)."</td>";
                                $pacientes_resumen1.="<td style='text-align:center;font-weight:bold;'>".($nomina_detalle[$y]['pac_rut']!=''?$nomina_detalle[$y]['pac_rut']:$nomina_detalle[$y]['pac_ficha'])."</td>";
                                $pacientes_resumen1.="<td>".htmlentities(strtoupper($nomina_detalle[$y]['pac_nombres'].' '.$nomina_detalle[$y]['pac_appat'].' '.$nomina_detalle[$y]['pac_apmat']))."</td>";
                                
                                //------------------------------------------------------------------
                                                        
                                
                                
                                
                                
                                
                                
                                
                            }
                            
                            
                            
                            
                            
                            
                           $pacientes_resumen1.="</table>" ;
                        }
                    }
                }
            }
            
            
            
            /*
            $resumen2='<table style="width:80%;">
            <tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
            <td>Especialidad</td>
            <td>Sin M&eacute;dico(*)</td>
            <td>Total Pacientes</td>
            <td>%</td>
            </tr>';
                
            array_multisort($esp_sort, SORT_STRING, $especialidades);

            foreach($especialidades AS $esp => $datos) {
                $color=(($j++)%2==0)?'#dddddd':'#eeeeee';
                $resumen2.='<tr bgcolor="'.$color.'">
                <td>'.$esp.'</td>
                <td style="text-align:right;">'.number_format($datos['nomed'],0,',','.').'</td>
                <td style="text-align:right;font-weight:bold;">'.number_format($datos['total'],0,',','.').'</td>
                <td style="text-align:right;">'.number_format(($datos['total']*100)/$total,2,',','.').'</td>
                </tr>';
            }

            $resumen2.='
            <tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
            <td style="font-weight:bold;">Totales</td>
            <td style="text-align:right;">'.number_format($total_nomed).'</td>
            <td style="text-align:right;font-weight;bold;">'.number_format($total_pacs).'</td>
            <td style="text-align:right;">100,00</td>
            </tr>';

            $resumen2.='</table>';
             * 
             */
        
        }
         
        ob_start();

?>
<center>
    <h2><u>Reporte Diario Sistema de Nominas de Atenci&oacute;n - G.I.S.<br>Hospital San Jos&eacute; De Melipilla</u><br><?php echo date('d/m/Y'); ?></h2>
    <br /><br />
    <i>Adjuntamos a usted, tabla adjunta con detalle de pacientes Agendados para hoy <b><?php echo date('d/m/Y'); ?></b> seg&uacute;n registros del Sistema GIS.</i>
    <br /><br />
    <b><u>Res&uacute;men de Nominas de Atenci&oacute;n por ESPECIALIDAD</u></b>
    <br /><br />
    <?php echo $resumen1; ?><!--<i>(*) Sin M&eacute;dico Tratante Asignado<br />(**) Sin Especialidad Asociada</i>-->
    <br /><br />
    <b><u>Res&uacute;men de Pacientes Agendados por ESPECIALIDAD</u></b>
    <br /><br />
    <?php echo $pacientes_resumen1; ?><!--<i>(*) Sin M&eacute;dico Tratante Asignado</i>-->
    <br /><br />
    <br /><br />
    Atentamente, Sistema Nominas de Atenci&oacute;n - GIS.<br />
    Hospital San Jos&eacute; de Melipilla - Melipilla.
</center>
<?php
    $html=ob_get_contents();
    ob_end_clean();
    error_reporting(E_ALL);


    
    include('Mail.php');
    include('Mail/mime.php');

    $start=microtime();

    function send_mail($to, $from, $subject, $body) {
        //$host = "10.8.134.40";
        //$username = "sistemagis.hgf@redsalud.gov.cl";
        //$password = "12345678";
        
        $host = "190.107.177.206";
        $username = "hfbc@sistemasexpertos.cl";
        $password = "solucion1234";
        

        $headers = array ('From' => $from,
        'To' => $to,
        'Subject' => $subject);

        $smtp = Mail::factory('smtp',
        array ('host' => $host,
        'auth' => true,
        'username' => $username,
        'password' => $password));

        // Creating the Mime message
        $crlf="";
        $mime = new Mail_mime($crlf);

        // Setting the body of the email
        $mime->setTXTBody(strip_tags($body));
        $mime->setHTMLBody($body);

        //$mime->addAttachment('/tmp/listado_pacientes_hosp_excedidos.xlsx','application/xlsx');
        //$mime->addAttachment('/tmp/listado_pacientes_hosp_excedidos.csv','text/csv');

        $body = $mime->get();
        $headers = $mime->headers($headers);

        $mail = $smtp->send($to, $headers, $body);

        //print(" ".(microtime()-$start)." msecs...");
    }
     

    //$mails='gestioncamas.hgf@redsalud.gov.cl, directora.hgf@redsalud.gov.cl, jefe.sdm.hgf@redsalud.gov.cl, jefe.medicina.hgf@redsalud.gov.cl, tatigino@hotmail.com, jefe.pensionado.hgf@redsalud.gov.cl, jefe.cirugia.hgf@redsalud.gov.cl, jefe.urologia.hgf@redsalud.gov.cl, mat.sup.neonatologia.hgf@redsalud.gov.cl, jefe.traumatologia.hgf@redsalud.gov.cl, jefe.pediatria.hgf@redsalud.gov.cl, jefe.sqp.hgf@redsalud.gov.cl, jefe.maternidad.hgf@redsalud.gov.cl, jefe.ccv.hgf@redsalud.gov.cl, jefe.uciped.hgf@redsalud.gov.cl, sergiogalvez@fideco.cl, jefe.sda.hgf@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl, edgardo.gonzalez.hgf@redsalud.gov.cl';
    //$mails='gestioncamas.hgf@redsalud.gov.cl, directora.hgf@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl, edgardo.gonzalez.hgf@redsalud.gov.cl, jefe.informatica.hgf@redsalud.gov.cl';
    //$mails='claudio.angel@sistemasexpertos.cl,carolina.rojase@redsalud.gov.cl,sergio.abarca@redsalud.gob.cl,alejandro.munoz@redsalud.gob.cl';
    $mails='claudio.angel@sistemasexpertos.cl';

    $mail_array=explode(',', $mails);

    for($z=0;$z<sizeof($mail_array);$z++) {
        $email=trim($mail_array[$z]);
        send_mail($email,'sistemagis.hgf@redsalud.gov.cl','Pacientes Agendados Nomina Fecha Atención "'.date('d/m/Y').'" - Sistema GIS Nominas de Atención',$html);
    }
    
    //send_mail('rodrigo.carvajal@sistemasexpertos.cl','sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Sistema GestiÃ³n de Camas '.date('d/m/Y')),$html);
?>
