<?php 

	set_time_limit(0);

	require_once('../../conectar_db.php');

    function graficar($vig, $prox, $ven)  {
		
		$vig=round($vig);
		$prox=round($prox);
		$ven=round($ven);
			
		$html="<table style='width:250px;border:1px solid black;' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($vig>0) $html.="<td style='width:$vig%;background-color:#4444ff;'>&nbsp;</td>";
		if($prox>0) $html.="<td style='width:$prox%;background-color:#BBBB00;'>&nbsp;</td>";
		if($ven>0) $html.="<td style='width:$ven%;background-color:#ff4444;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}

    $pat=pg_escape_string(trim(($_POST['pat'])));
    $tmppat=$pat;
    $estado=$_POST['estado']*1;
    $filtro=pg_escape_string(trim($_POST['filtro2']));
    $filtrogar=pg_escape_string(trim(($_POST['filtrogar'])));
    $filtrocond=pg_escape_string(trim(($_POST['filtrocond'])));
    
    if($pat=="-1") {
		$pat_w="true";
	} else {
		$pat_w="trim(pst_patologia_interna)='".$pat."'";
	}

    if($estado==-2) {
		$estado_w='true';
	} elseif($estado==-1) {
		$estado_w='NOT mon_estado';
	} elseif($estado==0) {
		$estado_w='NOT mon_estado AND mon_fecha_limite>=CURRENT_DATE';
	} elseif($estado==1) {
		$estado_w='NOT mon_estado AND mon_fecha_limite<CURRENT_DATE';
	} elseif($estado==2) {
		$estado_w='mon_estado';
	} elseif($estado==3) {
		$estado_w="mon_estado AND mon_estado_sigges='Exceptuada'";
	}
		
	if($filtro!='') {
		$filtro_w="mon_rut ilike '%$filtro%' OR mon_nombre ilike '%$filtro%'";
		$estado_w='true';
	} else {
		$filtro_w='true';
	}

	if($filtrogar!='') {
		$filtrogar_w="trim(pst_garantia_interna)='".$filtrogar."'";
	} else {
		$filtrogar_w='true';
	}

	if($filtrocond!='0') {
		if($filtrocond!='')
			$filtrocond_w="nombre_condicion='".$filtrocond."'";
		else
			$filtrocond_w="nombre_condicion is null";
	} else {
		$filtrocond_w='true';
	}

	$ver=$_POST['tipo_ver']*1;


    $lista=cargar_registros_obj("
    
		SELECT * FROM (
    
        SELECT 

        *, 

    		(CURRENT_DATE)-mon_fecha_limite AS dias,
    		
    		trim(pst_patologia_interna) AS pst_patologia_interna,
    
    		trim(pst_garantia_interna) AS pst_garantia_interna,
    		
    		mg.mon_id AS real_mon_id,
        
			(monr_fecha_evento IS NOT NULL AND monr_fecha_evento<CURRENT_DATE) AS reclasificar,
			
			(CASE 
			WHEN ((mon_fecha_limite-CURRENT_DATE)<0) THEN 'VENCIDA' 
			WHEN ((mon_fecha_limite-CURRENT_DATE)<=7 OR 
				  (mon_fecha_limite-CURRENT_DATE)<=floor((mon_fecha_limite-mon_fecha_inicio)*0.3)) THEN 'ALERTA'
			ELSE 'VIGENTE' END) AS estado        


        FROM monitoreo_ges AS mg

        JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
        
        LEFT JOIN monitoreo_ges_registro AS mgr ON mgr.mon_id=mg.mon_id AND mgr.monr_estado=0
        LEFT JOIN lista_dinamica_condiciones ON id_condicion=COALESCE(monr_clase,'0')::integer
        LEFT JOIN lista_dinamica_bandejas ON codigo_bandeja=monr_subclase
		LEFT JOIN funcionario ON monr_func_id=func_id
		LEFT JOIN lista_dinamica_especialidades ON mon_cod_especialidad=esp_codigo

        WHERE $estado_w AND $pat_w AND $filtrogar_w AND $filtro_w AND $filtrocond_w
        
        ) AS foo
        
        ORDER BY dias DESC

    ");


		require_once('../../PHPExcel/Classes/PHPExcel.php');
		require_once '../../PHPExcel/Classes/PHPExcel/IOFactory.php';

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital Gustavo Fricke")
							 ->setLastModifiedBy("Sistema GIS Hospital Gustavo Fricke")
							 ->setTitle("REPORTE SISTEMA DE MONITOREO GES")
							 ->setSubject("REPORTE SISTEMA DE MONITOREO GES")
							 ->setDescription("REPORTE SISTEMA DE MONITOREO GES")
							 ->setKeywords("pacientes gis fricke hospital ges")
							 ->setCategory("Reportes");

		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Monitoreo GES');
		
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Fecha Emisión:');
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

		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Fecha Ingreso');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Nombre Completo');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'Fecha Inicio');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'Fecha Límite');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'Fecha Evento');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'Dif.F.Evento');
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'Patología');
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Garantía');
		$objPHPExcel->getActiveSheet()->setCellValue('K5', 'Rama');
		$objPHPExcel->getActiveSheet()->setCellValue('L5', 'Condición Actual');
		$objPHPExcel->getActiveSheet()->setCellValue('M5', 'Cual');
		$objPHPExcel->getActiveSheet()->setCellValue('N5', 'Especialidad');
		$objPHPExcel->getActiveSheet()->setCellValue('O5', 'Observación Monitor');
		$objPHPExcel->getActiveSheet()->setCellValue('P5', 'Comentarios Directorio');
		$objPHPExcel->getActiveSheet()->setCellValue('Q5', 'Tipo');
		$objPHPExcel->getActiveSheet()->setCellValue('R5', 'Fecha Revisión');
		$objPHPExcel->getActiveSheet()->setCellValue('S5', 'Monitor GES');
		$objPHPExcel->getActiveSheet()->setCellValue('T5', 'Bandeja Proceso');
		
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:T5')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'top'     => array(
 					'style' => PHPExcel_Style_Border::BORDER_THIN
 				),
 				'bottom'     => array(
 					'style' => PHPExcel_Style_Border::BORDER_THIN
 				)
			),
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)
		)
		);


	if($lista)
	for($k=0;$k<sizeof($lista);$k++) {
		
		$r=$lista[$k];
		
		$clase=($k%2==0)?'tabla_fila':'tabla_fila2';
		
		$caso_id=$r['caso_id'];
		
		if($r['nombre_bandeja']=='') {
			if($r['monr_fecha_evento']!='') {
				$r['nombre_bandeja']='(Monitorear Post Fecha de Evento...)';
			} else {
				$r['nombre_bandeja']='(Fuera de Fecha de Corte...)';
			}
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($k+6), utf8_encode($r['mon_fecha']));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($k+6), $r['mon_rut']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($k+6), $r['pac_ficha']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($k+6), utf8_encode($r['mon_nombre']));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($k+6), $r['mon_fecha_inicio']);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($k+6), $r['mon_fecha_limite']);

		if($r['mon_estado']!='t' OR $r['mon_fecha_sigges']=='')
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($k+6), $r['monr_fecha_evento']);
		else
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($k+6), $r['mon_fecha_sigges']);

		$objPHPExcel->getActiveSheet()->setCellValue('H'.($k+6), $r['dias3']);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($k+6), utf8_encode($r['pst_patologia_interna']));
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($k+6), utf8_encode($r['mon_garantia']));
		$objPHPExcel->getActiveSheet()->setCellValue('K'.($k+6), utf8_encode($r['pst_rama_interna']));
		
		if($r['mon_estado']!='t' OR $r['mon_estado_sigges']=='') {
			$objPHPExcel->getActiveSheet()->setCellValue('L'.($k+6), utf8_encode($r['nombre_condicion']));
			$objPHPExcel->getActiveSheet()->setCellValue('M'.($k+6), utf8_encode($r['monr_subcondicion']));
		} else {
			$objPHPExcel->getActiveSheet()->setCellValue('L'.($k+6), utf8_encode($r['mon_estado_sigges']));
			$objPHPExcel->getActiveSheet()->setCellValue('M'.($k+6), utf8_encode($r['mon_causal_sigges']));
		}
			
		$objPHPExcel->getActiveSheet()->setCellValue('N'.($k+6), utf8_encode(strtoupper($r['esp_nombre'])));
		$objPHPExcel->getActiveSheet()->setCellValue('O'.($k+6), utf8_encode($r['monr_observaciones']));
		$objPHPExcel->getActiveSheet()->setCellValue('P'.($k+6), utf8_encode($r['monr_comentarios']));
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.($k+6), utf8_encode($r['estado']));
		$objPHPExcel->getActiveSheet()->setCellValue('R'.($k+6), ($r['monr_fecha']==''?'':substr($r['monr_fecha'],0,10)));
		$objPHPExcel->getActiveSheet()->setCellValue('S'.($k+6), utf8_encode($r['func_nombre']));
		$objPHPExcel->getActiveSheet()->setCellValue('T'.($k+6), utf8_encode($r['nombre_bandeja']));
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.($k+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($k+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($k+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('L'.($k+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('M'.($k+6))->getFont()->setBold(true);
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('P'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('Q'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
		$color=($k%2==0)?'DDDDDD':'EEEEEE';
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($k+6).':S'.($k+6))->applyFromArray(
			array(
				'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => $color
	 			)
				)
			)
			);
		
				
	}
	
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
	
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: filename=\"LISTADO_MONITOREO---".date('d-m-Y').".xls\";");

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	/*

		$id=$r['monr_id']*1;

		if($lista_id!='-1' AND $lista_id!='-2') {

			if($li['lista_campos_tabla']!='') {
			
				$campos=explode('|', $li['lista_campos_tabla']);
				$valores=explode('|', $r['in_valor_tabla']);
				
				for($i=0;$i<sizeof($campos);$i++) {
				
					if(strstr($campos[$i],'>>>')) {
						$cmp=explode('>>>',$campos[$i]);
						$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
					} else {
						$cmp=$campos[$i]; $tipo=2;
					}
					
					print("<td>");
					
					if($tipo==0) {

						if(isset($valores[$i])) 
							$vact=($valores[$i]=='true')?'CHECKED':'';
						else 
							$vact='';

						print("<input type='checkbox' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' $vact />");	

					} elseif($tipo==1) {

						if(isset($valores[$i])) 
							$vact=($valores[$i]=='true')?'CHECKED':'';
						else 
							$vact='CHECKED';

						print("<input type='checkbox' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' $vact />");
										
					} elseif($tipo==2) {

						print("<input type='text' size=5 style='text-align:center;' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' value='$vact' />");
										
					} elseif($tipo==5) {
					
						$opts=explode('//', $cmp[2]);
									
						if(isset($valores[$i])) 
							$vact=$valores[$i];
						else 
							$vact='';

						print("<select id='campo_".$i."_".$id."' name='campo_".$i."_".$id."'>");
						
						for($j=0;$j<sizeof($opts);$j++) {
							
							$opts[$j]=trim($opts[$j]);
							
							if($vact==$opts[$j]) $sel='SELECTED'; else $sel='';
							
							print("<option value='".$opts[$j]."' $sel>".$opts[$j]."</option>");	
						}			
						
						print("</select>");		
						
					} elseif($tipo==10) {

						if(isset($valores[$i])) 
							$vact=$valores[$i];
						else 
							$vact='';
						
						print("<textarea id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' style='width:100%;height:20px;'>$vact</textarea>");
										
					} else {

						if(isset($valores[$i])) 
							$vact=$valores[$i];
						else 
							$vact='';
						
						print("<input type='text' id='campo_".$i."_".$id."' name='campo_".$i."_".$id."' size=10 value='$vact' />");
										
					}	
					
					print("</td>");	
					
				}	
			
			}
			
		} 
*/
	
?>
