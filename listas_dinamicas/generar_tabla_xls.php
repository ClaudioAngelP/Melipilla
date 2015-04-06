<?php 

	require_once('../conectar_db.php');
	
	$colnames=array('P','Q','R','S','T','U','V','W','X','Y','Z');

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

    $pat=pg_escape_string(trim(utf8_decode($_POST['pat'])));
    $filtrogar=pg_escape_string(trim(utf8_decode($_POST['filtrogar'])));
    
    $filtro=false;
    
    if(isset($_POST['filtro_cond'])) {
    
		$cond=cargar_registros_obj("SELECT * FROM lista_dinamica_condiciones;");
		
		$conds='';
		
		for($i=0;$i<sizeof($cond);$i++) {
		
			if(isset($_POST['chk_cnd_'.$cond[$i]['id_condicion']])) {
				$conds.="'".$cond[$i]['id_condicion']."',";
			}
			
		}
		
		$conds=trim($conds,',');
    
		if($conds!='')
			$cond_w='(monr_clase IN ('.$conds.'))';		// CONDICIONES SELECCIONADAS...
		else
			$cond_w='true';	// SI NO SELECCIONA NINGUNA LAS TOMA TODAS...
    
	} else {
		
		$cond_w='true';
		
	}

	
	
	if(isset($_POST['filtro_cual'])) {
    
		$chks=$_POST['filtro_cual']*1;
    
		for($i=0;$i<=$chks;$i++) {
		
			if(isset($_POST['chk_cual_'.$i])) {
				$cuales.="'".pg_escape_string(utf8_decode($_POST['chk_cual_'.$i]))."',";
			}
			
		}
		
		$cuales=trim($cuales,',');
    
		if($cuales!='')
			$cual_w='(trim(monr_subcondicion) IN ('.$cuales.'))';		// CONDICIONES SELECCIONADAS...
		else
			$cual_w='true';	// SI NO SELECCIONA NINGUNA LAS TOMA TODAS...
    
	} else {
		
		$cual_w='true';
		
	}

    
    if(isset($_POST['filtro_cond']) AND $_POST['filtro_cond']=='3') {
    
		$pat=cargar_registros_obj("SELECT * FROM patologias_sigges_traductor;");
		
		$pats='';
		
		for($i=0;$i<sizeof($pat);$i++) {
		
			if(isset($_POST['chk_pst_'.$pat[$i]['pst_id']])) {
				
				// Existen multiples IDS por cada x problema x garantia...
				// TODO: ¿¿¿Deben unificarse los ID de x problema x garantia???...
				$tmp=cargar_registros_obj("SELECT * FROM patologias_sigges_traductor 
						WHERE pst_patologia_interna='".$pat[$i]['pst_patologia_interna']."' AND 
							  pst_garantia_interna='".$pat[$i]['pst_garantia_interna']."'");
							  
				for($k=0;$k<sizeof($tmp);$k++)
					$pats.=$tmp[$k]['pst_id'].',';
					
			}
			
		}
		
		$pats=trim($pats,',');
		
		if($pats!='')
			$filtropat_w='(pst_id IN ('.$pats.'))';		// SI HAY PATOLOGIAS TOMA LAS CHEQUEADAS...
		else
			$filtropat_w='true';	// SI NO HAY CHEQUEADAS LAS TOMA TODAS...
    
	} else {
		
		$filtropat_w='true';
		
	}
	
	$tipo_gar=$_POST['tipo_gar']*1;
	
	if($tipo_gar==0) {
		$tipo_w='true';
	} else if($tipo_gar==1) {
		$tipo_w='dias>0';
	} else {
		$tipo_w='dias<=0';
	}

	$fecha_lim=pg_escape_string($_POST['fecha_limite']);

	if($fecha_lim=='') {
		$fecha_w='true';
	} else {
		$fecha_w="mon_fecha_limite<='$fecha_lim'";
	}

	$directorio=isset($_POST['directorio']);

	if(!$directorio) {
		$dir_w="lb.codigo_bandeja IS NOT NULL";
	} else {
		$dir_w="lb.codigo_bandeja IS NULL";
		//$dir_w="true";
	}
	
	if($_POST['fecha_dif']!='') {
		$dif_w='dias3<='.($fecha_dif*1);
	} else {
		$dif_w='true';
	}


	//$vdetalle=isset($_POST['ver_detalle']);
	//$vresumen=isset($_POST['ver_resumen']);
	
	$vdetalle=true;
	$vresumen=true;
	
	$lista_id=pg_escape_string(utf8_decode($_POST['lista_id']));
	
	if($lista_id!='-1' AND $lista_id!='-2') {

		$li=cargar_registro("SELECT * FROM lista_dinamica_bandejas WHERE codigo_bandeja='$lista_id'");	
		
		$lista_w="monr_subclase='$lista_id'";
		
		$query_campos='';
		
		if($li['codigo_bandeja_campos']!='') {
			
			if($li['codigo_bandeja_campos']!='')
				$query_cods=explode('|', $li['codigo_bandeja_campos']);
			else 
				$query_cods=array();
			
			for($c=0;$c<sizeof($query_cods);$c++) {
				
				$tmp_bandeja=cargar_registro("SELECT * FROM lista_dinamica_bandejas WHERE codigo_bandeja='".$query_cods[$c]."'");
				$campos_cods[$query_cods[$c]]=explode('|',$tmp_bandeja['lista_campos_tabla']);
				
				$query_campos.=",(SELECT monr_valor FROM monitoreo_ges_registro AS mgr2 WHERE mgr2.mon_id=monitoreo_ges_registro.mon_id AND monr_subclase='".$query_cods[$c]."' AND monr_estado=1 ORDER BY monr_fecha DESC LIMIT 1) AS valores_".$query_cods[$c];
				
			}
		}

	} else {
		
		$lista_w='true';
		
	}


	/*
	$lista=cargar_registros_obj("SELECT * FROM (SELECT *, 
					(CURRENT_DATE-in_fecha::date)::integer AS dias2,
					(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
					(mon_fecha_limite::date-monr_fecha_evento::date)::integer AS dias3,
					COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS pst_patologia_interna,
					COALESCE(trim(pst_garantia_interna), trim(mon_garantia)) AS pst_garantia_interna,
					monitoreo_ges_registro.monr_id AS id
					$query_campos
					FROM monitoreo_ges_registro 
					JOIN monitoreo_ges USING (mon_id)
					LEFT JOIN lista_dinamica_caso AS lc ON lc.monr_id=monitoreo_ges_registro.monr_id
					LEFT JOIN lista_dinamica_instancia AS li ON li.caso_id=lc.caso_id AND in_estado=0
					LEFT JOIN lista_dinamica_bandejas AS lb ON COALESCE(monr_subclase,'')=lb.codigo_bandeja
					LEFT JOIN lista_dinamica_condiciones AS lc2 ON monr_clase::bigint=lc2.id_condicion
					LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
					LEFT JOIN pacientes USING (pac_id)
					WHERE NOT mon_estado AND monr_estado=0 AND $lista_w AND $cond_w AND $filtropat_w) AS foo 
					WHERE $tipo_w AND $fecha_w
					ORDER BY dias3 ASC, dias DESC;");
		*/
		
			$lista=cargar_registros_obj("SELECT * FROM (SELECT *, 
					(CURRENT_DATE-monr_fecha::date)::integer AS dias2,
					(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
					(mon_fecha_limite::date-monr_fecha_evento::date)::integer AS dias3,
					COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS pst_patologia_interna,
					COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS _pst_patologia_interna,
					COALESCE(trim(pst_garantia_interna), trim(mon_garantia)) AS pst_garantia_interna,
					monitoreo_ges_registro.monr_id AS id, monitoreo_ges_registro.mon_id AS id2,
					(CASE 
					WHEN ((mon_fecha_limite-CURRENT_DATE)<0) THEN 2 
					WHEN ((mon_fecha_limite-CURRENT_DATE)<=7 OR 
						  (mon_fecha_limite-CURRENT_DATE)<=floor((mon_fecha_limite-mon_fecha_inicio)*0.3)) THEN 1 
					ELSE 0 END) AS estado,
					trim(monr_subcondicion) AS monr_subcondicion,
					trim(upper(monr_subcondicion)) AS _monr_subcondicion,
					(SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut LIMIT 1) AS pac_id,
					monitoreo_ges_registro.monr_id AS id
					$query_campos
					FROM monitoreo_ges_registro 
					JOIN monitoreo_ges USING (mon_id)
					LEFT JOIN lista_dinamica_bandejas AS lb ON COALESCE(monr_subclase,'')=lb.codigo_bandeja
					LEFT JOIN lista_dinamica_condiciones AS lc2 ON COALESCE(monr_clase,'0')::bigint=lc2.id_condicion
					LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
					WHERE NOT mon_estado AND monr_estado=0 AND $lista_w AND $cond_w AND $cual_w AND $filtropat_w AND $dir_w) AS foo 
					LEFT JOIN pacientes USING (pac_id)
					WHERE $tipo_w AND $fecha_w AND $dif_w
					$orden;");


		require_once('../PHPExcel/Classes/PHPExcel.php');
		require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';

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
		if(!isset($li))
			$objPHPExcel->getActiveSheet()->setCellValue('C2', 'LISTADO DE MONITOREO GES');
		else
			$objPHPExcel->getActiveSheet()->setCellValue('C2', utf8_encode($li['nombre_bandeja']));
		
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
		$objPHPExcel->getActiveSheet()->setCellValue('N5', 'Observación Monitor');
		$objPHPExcel->getActiveSheet()->setCellValue('O5', 'Comentarios Directorio');
		
		$cnt=0;
		for($c=0;$c<sizeof($query_cods);$c++) {
			$campos=$campos_cods[$query_cods[$c]];
			for($d=0;$d<sizeof($campos);$d++) {
				$datos_campo=explode('>>>', $campos[$d]);
				$objPHPExcel->getActiveSheet()->setCellValue($colnames[$cnt].'5', $datos_campo[0]);			
				$objPHPExcel->getActiveSheet()->getColumnDimension($colnames[$cnt])->setAutoSize(true);
				$cnt++;
			}
		}
		
		//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:'.$colnames[$cnt>0?$cnt-1:0].'5')->applyFromArray(
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
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($k+6), utf8_encode($r['mon_fecha']));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($k+6), $r['mon_rut']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($k+6), $r['pac_ficha']);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($k+6), utf8_encode($r['mon_nombre']));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($k+6), $r['mon_fecha_inicio']);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($k+6), $r['mon_fecha_limite']);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($k+6), $r['monr_fecha_evento']);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.($k+6), $r['dias3']);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($k+6), utf8_encode($r['pst_patologia_interna']));
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($k+6), utf8_encode($r['mon_garantia']));
		$objPHPExcel->getActiveSheet()->setCellValue('K'.($k+6), utf8_encode($r['pst_rama_interna']));
		$objPHPExcel->getActiveSheet()->setCellValue('L'.($k+6), utf8_encode($r['nombre_condicion']));
		$objPHPExcel->getActiveSheet()->setCellValue('M'.($k+6), utf8_encode($r['monr_subcondicion']));
		$objPHPExcel->getActiveSheet()->setCellValue('N'.($k+6), utf8_encode($r['monr_observaciones']));
		$objPHPExcel->getActiveSheet()->setCellValue('O'.($k+6), utf8_encode($r['monr_comentarios']));


		$cnt=0;
		for($c=0;$c<sizeof($query_cods);$c++) {
			
			$campos=$campos_cods[$query_cods[$c]];
			$valores=explode('|', utf8_encode($r[strtolower('valores_'.$query_cods[$c])]));
			
			for($d=0;$d<sizeof($campos);$d++) {
				
				$datos_campo=explode('>>>', $campos[$d]);
				
				//if(isset($valores[$d]))
					$objPHPExcel->getActiveSheet()->setCellValue($colnames[$cnt].''.($k+6), $valores[$d]);			
				//else
					//$objPHPExcel->getActiveSheet()->setCellValue($colnames[$cnt].''.($k+6), '');			
				
				$cnt++;
				
			}
			
		}


		
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
			
		$color=($k%2==0)?'DDDDDD':'EEEEEE';
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($k+6).':'.$colnames[$cnt>0?$cnt-1:0].''.($k+6))->applyFromArray(
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
