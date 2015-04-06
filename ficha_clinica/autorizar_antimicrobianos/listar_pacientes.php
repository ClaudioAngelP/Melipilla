<?php require_once('../../conectar_db.php');
	
	$tipo_pac=$_POST['tipo_pac'];
	
	if($tipo_pac==1) $edad='AND edad<15'; else if ($tipo_pac==2) $edad=' AND edad>15'; else $edad='';

	$tipo_val=$_POST['tipo_val']*1;
	
	if($tipo_val!=4)$estado='hospam_estado='.$tipo_val; else	$estado='true';
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$rango="hospam_fecha_digitacion BETWEEN '".$fecha1." 00:00:00' AND '".$fecha2." 23:59:59' ";
	
	//$servicio=$_POST['centro_ruta'];

	$paciente=$_POST['pac_id'];
	if($paciente!=-1) $pac='AND pac_id='.$paciente; else $pac='';
	
	$medicamento=$_POST['art_id'];
	if($medicamento!=-1) $med=' AND art_id='.$medicamento; else $med='';
	
	$tipo_cama=$_POST['tcama_id'];
	if($tipo_cama!='') $tcama=' AND tcama_id='.$tipo_cama; else $tcama=' AND true';
	
	$visador=$_POST['func_id'];
	if($visador!=-1) $vis=' AND hospam_func_id2='.$visador; else $vis='';
	
	$tipo_inf=$_POST['tipo_inf'];
	
if($tipo_inf==0){
	$lista=cargar_registros_obj("

		SELECT (now()::date-hospam_fecha_digitacion::date) AS transcurrido,* FROM ( 
			SELECT *,extract(YEAR FROM age(current_timestamp,pac_fc_nac))AS edad,
			upper(func1.func_nombre) AS generador, upper(func2.func_nombre) AS visador,
			(hospam_fecha_digitacion+(hospam_dias||' days')::interval)::date AS fecha_fin,
			hosp_fecha_egr::date,(tcama_tipo||'/'||cama_tipo)AS desc_cama,((hosp_numero_cama-tcama_num_ini)+1)AS cama_nro
			FROM hospitalizacion_autorizacion_meds
				JOIN hospitalizacion USING (hosp_id)
			JOIN pacientes ON hosp_pac_id=pac_id
			JOIN articulo USING (art_id)
			JOIN funcionario AS func1 ON hospam_func_id=func1.func_id
			LEFT JOIN funcionario AS func2 ON hospam_func_id2=func2.func_id 
			JOIN doctores ON hospam_doc_id=doc_id
			LEFT JOIN bodega_forma ON art_forma=forma_id
			LEFT JOIN tipo_camas ON
                               cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
            LEFT JOIN clasifica_camas AS t1 ON 
                               t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama
			 WHERE $estado $tcama
			ORDER BY hospam_fecha_digitacion ASC
		)AS foo
			WHERE $rango $pac $edad $med $vis $tcama
			ORDER BY desc_cama,cama_nro,hospam_fecha_digitacion;

	");
		
	$xls=isset($_POST['xls']);
	if($xls)
		$xls=($_POST['xls']*1)==1;
	
    if($xls){
		require_once('../../PHPExcel/Classes/PHPExcel.php');
		require_once '../../PHPExcel/Classes/PHPExcel/IOFactory.php';
		
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');

		// It will be called file.xls
		header('Content-Disposition: attachment; filename="listado_antimicrobianos.xls"');

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital Gustavo Fricke")
							 ->setLastModifiedBy("Sistema GIS Hospital Gustavo Fricke")
							 ->setTitle("LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS")
							 ->setSubject("LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS")
							 ->setDescription("LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS")
							 ->setKeywords("pacientes gis fricke hospital antimicrobianos")
							 ->setCategory("Reportes");

		$objPHPExcel->setActiveSheetIndex(0);
		
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS');
		//$objPHPExcel->getActiveSheet()->setCellValue('D2', 'LISTADO DE RECETAS NPT');
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Fecha Emisión:');
		$objPHPExcel->getActiveSheet()->setCellValue('C3', date('d/m/Y H:i:s'));
		
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(16);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

		$objPHPExcel->getActiveSheet()->getStyle('C1:C4')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			)
		)
		);

		$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->applyFromArray(
		array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			)
		)
		);
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Nombre'); //Fecha Solicitud
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'R.U.T.');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Edad'); //Nombre
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'Servicio'); //Edad
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'Cama'); //Servicio
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'Medicamento'); //Cama
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'Fecha Solicitud'); //Medicamento
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'Fecha Termino');
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Diagnostico');
		$objPHPExcel->getActiveSheet()->setCellValue('K5', 'Estado');
		$objPHPExcel->getActiveSheet()->setCellValue('L5', 'Observaciones Visador');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getStyle('A5:L5')->applyFromArray(
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
	}else{
		
		print("
			<table style='width:100%;'>
			<tr class='tabla_header'>
			<td>#</td>
			<td>Fecha Solicitud</td>
			<td>R.U.T</td>
			<td>Nombre</td>
			<td>Edad</td>
			<td>Servicio</td>
			<td>Cama</td>
			<td>Medicamento</td>
			<td>Estado</td>
			<td>Detalle</td>
			</tr>");
	}
	
	if($lista)
	for($i=0;$i<sizeof($lista);$i++) {
	
		$class=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($lista[$i]['hosp_numero_cama']!=0) {
			$lista[$i]['desc_cama']='<b>'.htmlentities($lista[$i]['tcama_tipo']).'</b> / '.htmlentities($lista[$i]['cama_tipo']).'';
			$lista[$i]['cama_nro']=(($lista[$i]['hosp_numero_cama']*1-$lista[$i]['tcama_num_ini']*1)+1);	
		} else {
			$lista[$i]['desc_cama']='<i>(n/a)</i>';
			$lista[$i]['cama_nro']=0;	
		}
		
		
		
		
		if($lista[$i]['hosp_numero_cama']==-1) {
			$lista[$i]['desc_cama']='Alta / Fecha: '.$lista[$i]['hosp_fecha_egr'];
			$lista[$i]['cama_nro']=0;	
		}
		
		
		
		if($xls){
			if($lista[$i]['hospam_estado']==0) $estado='Pendiente';
			else if($lista[$i]['hospam_estado']==1) $estado='Aceptado';
			elseif($lista[$i]['hospam_estado']==2) $estado='Modificado';
			else $estado='Rechazado';
			
				
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+6), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+6), utf8_encode(trim($lista[$i]['pac_appat']." ".$lista[$i]['pac_apmat']." ".$lista[$i]['pac_nombres']))); 
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+6), ($lista[$i]['pac_rut']." / ".$lista[$i]['pac_ficha']));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+6), $lista[$i]['edad']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+6), utf8_encode($lista[$i]['desc_cama']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+6), htmlentities($lista[$i]['cama_nro']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+6), utf8_encode($lista[$i]['art_glosa']));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+6), substr($lista[$i]['hospam_fecha_digitacion'],0,16));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+6), $lista[$i]['fecha_fin']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+6), utf8_encode($lista[$i]['hospam_diagnostico']));
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+6), $estado);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+6), '');
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('L'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6).':L'.($i+6))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);
		
		}else{
		
		
		if($lista[$i]['hospam_estado']==1){
			$estado='<b>Aceptado</b>';
		}else if($lista[$i]['hospam_estado']==2){
			$estado='<b>Modificado</b>';
		}else if($lista[$i]['hospam_estado']==3){
			$estado='<b>Rechazado</b>';
		}else{
			$estado="<select id='hospam_".$lista[$i]['hospam_id']."' name='hospam_".$lista[$i]['hospam_id']."' onChange='guardar_aut(".$lista[$i]['hospam_id'].");'>
			<option value='0' ".($lista[$i]['hospam_estado']==0?'SELECTED':'').">Pendiente</option>
			<option value='1' ".($lista[$i]['hospam_estado']==1?'SELECTED':'').">Aceptado</option>
			<option value='2' ".($lista[$i]['hospam_estado']==2?'SELECTED':'').">Modificado</option>
			<option value='3' ".($lista[$i]['hospam_estado']==3?'SELECTED':'').">Rechazado</option>
			</select>";
		}
		
		print("<tr class='$class'>
			<td style='text-align:right;'>".($i+1)."</td>
			<td style='text-align:center;font-weight:bold;'>".substr($lista[$i]['hospam_fecha_digitacion'],0,16)."</td>
			<td style='text-align:right;font-weight:bold;'>".$lista[$i]['pac_rut']." / ".$lista[$i]['pac_ficha']."</td>
			<td style='font-size:14px;font-weight:bold;'>".trim(htmlentities($lista[$i]['pac_appat']." ".$lista[$i]['pac_apmat']." ".$lista[$i]['pac_nombres']))."</td>
			<td style='font-size:14px;font-weight:bold;'>".$lista[$i]['edad']."</td>
			<td style='text-align:center;font-weight:bold;'>".$lista[$i]['desc_cama']."</td>
			<td style='text-align:center;font-weight:bold;'>".$lista[$i]['cama_nro']."</td>
			<td style='text-align:left;font-weight:bold;font-size:14px;'>".$lista[$i]['art_glosa']."</td>
			<td><center>".$estado."</center></td>
			<td style='text-align: center;' class='no_printer'>
                   <img src='iconos/magnifier.png' style='cursor: pointer;'
              		onClick='ver_detalle(".$lista[$i]['hospam_id'].")'     
                   alt='Ver Detalle...'
                   title='Ver Detalle...'>
                   </td>
			</tr>");
		}
	}
	
	if($xls){
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save(_DIR_.'/listado_npt.xls');
		// Write file to the browser
		$objWriter->save('php://output');
	}else{
		print("</table>");	
	}
	
}else if($tipo_inf==1){
	
	$query="
		SELECT (now()::date-hospam_fecha_digitacion::date) AS transcurrido,* FROM ( 
			SELECT *,extract(YEAR FROM age(current_timestamp,pac_fc_nac))AS edad,
			upper(func1.func_nombre) AS generador, upper(func2.func_nombre) AS visador,
			(hospam_fecha_digitacion+(hospam_dias||' days')::interval)::date AS fecha_fin,
			hosp_fecha_egr::date,(tcama_tipo||'/'||cama_tipo)AS desc_cama,((hosp_numero_cama-tcama_num_ini)+1)AS cama_nro
			FROM hospitalizacion_autorizacion_meds
				JOIN hospitalizacion USING (hosp_id)
			JOIN pacientes ON hosp_pac_id=pac_id
			JOIN articulo USING (art_id)
			JOIN funcionario AS func1 ON hospam_func_id=func1.func_id
			LEFT JOIN funcionario AS func2 ON hospam_func_id2=func2.func_id 
			JOIN doctores ON hospam_doc_id=doc_id
			LEFT JOIN bodega_forma ON art_forma=forma_id
			LEFT JOIN tipo_camas ON
                               cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
            LEFT JOIN clasifica_camas AS t1 ON 
                               t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama
			ORDER BY hospam_fecha_digitacion ASC
		)AS foo
			WHERE $rango
			ORDER BY desc_cama,cama_nro,hospam_fecha_digitacion
	";
	$totales_visador=cargar_registros_obj("SELECT hospam_func_id2,visador,hospam_estado,count(*) AS cnt FROM (".$query.")AS foo5
									GROUP BY visador,hospam_func_id2,hospam_estado
									ORDER BY visador,hospam_estado");
	
	$totales_servicio=cargar_registros_obj("SELECT tcama_tipo,hospam_estado,count(*) AS cnt FROM (".$query.")AS foo5
									GROUP BY tcama_tipo,hospam_estado
									ORDER BY tcama_tipo,hospam_estado");
	
	$totales_generador=cargar_registros_obj("SELECT hospam_func_id,generador, hospam_estado, count(*) AS cnt FROM (".$query.")AS foo5
									GROUP BY generador,hospam_func_id,hospam_estado
									ORDER BY generador,hospam_estado");

									
	$lista=cargar_registros_obj($query);
		
	$xls=isset($_POST['xls']);
	if($xls)
		$xls=($_POST['xls']*1)==1;
	
    if($xls){
		require_once('../../PHPExcel/Classes/PHPExcel.php');
		require_once '../../PHPExcel/Classes/PHPExcel/IOFactory.php';
		
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');

		// It will be called file.xls
		header('Content-Disposition: attachment; filename="listado_antimicrobianos.xls"');

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital Gustavo Fricke")
							 ->setLastModifiedBy("Sistema GIS Hospital Gustavo Fricke")
							 ->setTitle("LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS")
							 ->setSubject("LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS")
							 ->setDescription("LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS")
							 ->setKeywords("pacientes gis fricke hospital antimicrobianos")
							 ->setCategory("Reportes");

		$objPHPExcel->setActiveSheetIndex(0);
		
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'LISTADO AUTORIZACION FARMACOS ANTIMICROBIANOS');
		//$objPHPExcel->getActiveSheet()->setCellValue('D2', 'LISTADO DE RECETAS NPT');
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Fecha Emisión:');
		$objPHPExcel->getActiveSheet()->setCellValue('C3', date('d/m/Y H:i:s'));
		
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(16);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

		$objPHPExcel->getActiveSheet()->getStyle('C1:C4')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			)
		)
		);

		$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->applyFromArray(
		array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			)
		)
		);
		
		
		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Nombre'); //Fecha Solicitud
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'R.U.T.');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Edad'); //Nombre
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'Servicio'); //Edad
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'Cama'); //Servicio
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'Medicamento'); //Cama
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'Fecha Solicitud'); //Medicamento
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'Fecha Termino');
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Diagnostico');
		$objPHPExcel->getActiveSheet()->setCellValue('K5', 'Estado');
		$objPHPExcel->getActiveSheet()->setCellValue('L5', 'Observaciones Visador');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getStyle('A5:L5')->applyFromArray(
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
	}else{
		
		print("<center><table style='width:80%;'>
				   <tr class='tabla_header'>
					   <td colspan=3>Totales por Visador</td>
				   </tr>
				   <tr class='tabla_header'>
						<td width=45%;>Nombre Visador</td><td>Estado Solicitud</td><td>Cantidad</td>
				   </tr>");
		for($v=0;$v<sizeof($totales_visador);$v++){
			if($totales_visador[$v]['hospam_estado']==0){ $estado='Pendiente';
			}else if($totales_visador[$v]['hospam_estado']==1){ $estado='Aceptado';
			}else if($totales_visador[$v]['hospam_estado']==2){ $estado='Modificado';
			}else if($totales_visador[$v]['hospam_estado']==3){ $estado='Rechazado';
			}
			print("<tr>
				<td class='tabla_fila2'>".htmlentities($totales_visador[$v]['visador'])."</td>
				<td style='text-align:center;' class='tabla_fila'>".$estado."</td>
				<td class='tabla_fila' style='text-align:right;'>".$totales_visador[$v]['cnt']."</td>
			</tr>");
			
		}
		print("<tr class='tabla_header'><td colspan=3>&nbsp;</td></tr></table>");
		
		print("<br>");
		
		print("<table style='width:80%;'>
				   <tr class='tabla_header'>
					   <td colspan=3>Totales por Servicio</td>
				   </tr>
				   <tr class='tabla_header'>
						<td width=45%;>Servicio</td><td>Estado Solicitud</td><td>Cantidad</td>
				   </tr>");
		for($s=0;$s<sizeof($totales_servicio);$s++){
			if($totales_servicio[$s]['hospam_estado']==0){ $estado='Pendiente';
			}else if($totales_servicio[$s]['hospam_estado']==1){ $estado='Aceptado';
			}else if($totales_servicio[$s]['hospam_estado']==2){ $estado='Modificado';
			}else if($totales_servicio[$s]['hospam_estado']==3){ $estado='Rechazado';
			}
			print("<tr>
				<td class='tabla_fila2'>".htmlentities($totales_servicio[$s]['tcama_tipo'])."</td>
				<td style='text-align:center;' class='tabla_fila'>".$estado."</td>
				<td class='tabla_fila' style='text-align:right;'>".$totales_servicio[$s]['cnt']."</td>
			</tr>");
			
		}
		print("<tr class='tabla_header'><td colspan=3>&nbsp;</td></tr></table>");
		
		print("<br>");
		
		print("<table style='width:80%;'>
				   <tr class='tabla_header'>
					   <td colspan=3>Totales por Generador</td>
				   </tr>
				   <tr class='tabla_header'>
						<td width=45%;>Nombre Generador</td><td>Estado Solicitud</td><td>Cantidad</td>
				   </tr>");
		for($g=0;$g<sizeof($totales_generador);$g++){
			if($totales_generador[$g]['hospam_estado']==0){ $estado='Pendiente';
			}else if($totales_generador[$g]['hospam_estado']==1){ $estado='Aceptado';
			}else if($totales_generador[$g]['hospam_estado']==2){ $estado='Modificado';
			}else if($totales_generador[$g]['hospam_estado']==3){ $estado='Rechazado';
			}
			print("<tr>
				<td class='tabla_fila2'>".htmlentities($totales_generador[$g]['generador'])."</td>
				<td style='text-align:center;' class='tabla_fila'>".$estado."</td>
				<td class='tabla_fila' style='text-align:right;'>".$totales_generador[$g]['cnt']."</td>
			</tr>");
			
		}
		print("<tr class='tabla_header'><td colspan=3>&nbsp;</td></tr></table></center>");
		print("<br><br>");
		
		print("
			<table style='width:100%;'>
			<tr class='tabla_header'><td colspan=10>Detalle Solicitudes</td></tr>
			<tr class='tabla_header'>
			<td>#</td>
			<td>Fecha Solicitud</td>
			<td>R.U.T</td>
			<td>Nombre</td>
			<td>Edad</td>
			<td>Servicio</td>
			<td>Cama</td>
			<td>Medicamento</td>
			<td>Estado</td>
			<td>Detalle</td>
			</tr>");
	}
	
	if($lista)
	for($i=0;$i<sizeof($lista);$i++) {
	
		$class=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($lista[$i]['hosp_numero_cama']!=0) {
			$lista[$i]['desc_cama']='<b>'.htmlentities($lista[$i]['tcama_tipo']).'</b> / '.htmlentities($lista[$i]['cama_tipo']).'';
			$lista[$i]['cama_nro']=(($lista[$i]['hosp_numero_cama']*1-$lista[$i]['tcama_num_ini']*1)+1);	
		} else {
			$lista[$i]['desc_cama']='<i>(n/a)</i>';
			$lista[$i]['cama_nro']=0;	
		}
		
		
		
		
		if($lista[$i]['hosp_numero_cama']==-1) {
			$lista[$i]['desc_cama']='Alta / Fecha: '.$lista[$i]['hosp_fecha_egr'];
			$lista[$i]['cama_nro']=0;	
		}
		
		
		
		if($xls){
			if($lista[$i]['hospam_estado']==0) $estado='Pendiente';
			else if($lista[$i]['hospam_estado']==1) $estado='Aceptado';
			elseif($lista[$i]['hospam_estado']==2) $estado='Modificado';
			else $estado='Rechazado';
			
				
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+6), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+6), utf8_encode(trim($lista[$i]['pac_appat']." ".$lista[$i]['pac_apmat']." ".$lista[$i]['pac_nombres']))); 
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+6), ($lista[$i]['pac_rut']." / ".$lista[$i]['pac_ficha']));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+6), $lista[$i]['edad']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+6), utf8_encode($lista[$i]['desc_cama']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+6), htmlentities($lista[$i]['cama_nro']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+6), utf8_encode($lista[$i]['art_glosa']));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+6), substr($lista[$i]['hospam_fecha_digitacion'],0,16));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+6), $lista[$i]['fecha_fin']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+6), utf8_encode($lista[$i]['hospam_diagnostico']));
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+6), $estado);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+6), '');
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('L'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('L'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6).':L'.($i+6))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);
		
		}else{
		
		
		if($lista[$i]['hospam_estado']==0){
			$estado='<b>Pendiente</b>';
		}else if($lista[$i]['hospam_estado']==1){
			$estado='<b>Aceptado</b>';
		}else if($lista[$i]['hospam_estado']==2){
			$estado='<b>Modificado</b>';
		}else if($lista[$i]['hospam_estado']==3){
			$estado='<b>Rechazado</b>';
		}
		
		print("<tr class='$class'>
			<td style='text-align:right;'>".($i+1)."</td>
			<td style='text-align:center;font-weight:bold;'>".substr($lista[$i]['hospam_fecha_digitacion'],0,16)."</td>
			<td style='text-align:right;font-weight:bold;'>".$lista[$i]['pac_rut']." / ".$lista[$i]['pac_ficha']."</td>
			<td style='font-size:14px;font-weight:bold;'>".trim(htmlentities($lista[$i]['pac_appat']." ".$lista[$i]['pac_apmat']." ".$lista[$i]['pac_nombres']))."</td>
			<td style='font-size:14px;font-weight:bold;'>".$lista[$i]['edad']."</td>
			<td style='text-align:center;font-weight:bold;'>".$lista[$i]['desc_cama']."</td>
			<td style='text-align:center;font-weight:bold;'>".$lista[$i]['cama_nro']."</td>
			<td style='text-align:left;font-weight:bold;font-size:14px;'>".$lista[$i]['art_glosa']."</td>
			<td><center>".$estado."</center></td>
			<td style='text-align: center;' class='no_printer'>
                   <img src='iconos/magnifier.png' style='cursor: pointer;'
              		onClick='ver_detalle(".$lista[$i]['hospam_id'].")'     
                   alt='Ver Detalle...'
                   title='Ver Detalle...'>
                   </td>
			</tr>");
		}
	}
	
	if($xls){
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save(_DIR_.'/listado_npt.xls');
		// Write file to the browser
		$objWriter->save('php://output');
	}else{
		print("</table>");	
	}
	
	
}

?>	
