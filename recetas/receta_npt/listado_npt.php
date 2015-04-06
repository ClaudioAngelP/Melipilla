<?php require_once('../../conectar_db.php');

	error_reporting(E_ALL);
	
	$f1=pg_escape_string($_POST['fecha1']);
	$f2=pg_escape_string($_POST['fecha2']);
	$func_id=$_POST['func_id']*1;
	$centro_ruta=pg_escape_string($_POST['centro_costo']);
	$nro_busca=($_POST['nro_busca']*1);
	
	$xls=isset($_POST['xls']);
	if($xls)
		$xls=($_POST['xls']*1)==1;
	
	$tipo=$_POST['tipo_inf']*1;
	
	if($tipo==1)
		$reporte='DETALLE COMPLETO';
	if($tipo==2)
		$reporte='TOTALES POR SERVICIO';
	if($tipo==3)
		$reporte='TOTALES POR PACIENTE';
	if($tipo==4)
		$reporte='TOTALES POR FUNCIONARIO';
	if($tipo==5)
		$reporte='RECEPCIÓN NPT';
	if($tipo==6)
		$reporte='DESPACHO NPT';
	if($tipo==7){
		$reporte='RESUMEN RECEPCIÓN';
		$docs=cargar_registro("SELECT array_to_string(array(SELECT distinct rnpt_doc_num
		 FROM receta_npt
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		AND rnpt_estado!=0),'|') AS docs");
		$nrs=explode('|',$docs['docs']);
		$guias='';
		for($i=0;$i<sizeof($nrs);$i++){
			$guias.=$nrs[$i].', ';
		}
		$guias=trim($guias,', ');
		//print_r($docs);
	}
	if($tipo==8)
		$reporte='INDICADOR';
	
	if($tipo==9)
		$reporte='CONSULTA RECEPCI&Oacute;N';
	
	if($xls){
		require_once('../../PHPExcel/Classes/PHPExcel.php');
		require_once '../../PHPExcel/Classes/PHPExcel/IOFactory.php';
		
		// We'll be outputting an excel file
		header('Content-type: application/vnd.ms-excel');

		// It will be called file.xls
		header('Content-Disposition: attachment; filename="listado_npt.xls"');

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital Gustavo Fricke")
							 ->setLastModifiedBy("Sistema GIS Hospital Gustavo Fricke")
							 ->setTitle("LISTADO DE RECETAS NPT")
							 ->setSubject("LISTADO DE RECETAS NPT")
							 ->setDescription("LISTADO DE RECETAS NPT")
							 ->setKeywords("pacientes gis fricke hospital nutricion parenteral npt")
							 ->setCategory("Reportes");

		$objPHPExcel->setActiveSheetIndex(0);
		
		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', $reporte);
		//$objPHPExcel->getActiveSheet()->setCellValue('D2', 'LISTADO DE RECETAS NPT');
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Fecha Emisión:');
		$objPHPExcel->getActiveSheet()->setCellValue('C3', date('d/m/Y H:i:s'));
		
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'Rango Fechas:');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', $f1.' - '.$f2);
		
		if($tipo==7){
			$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Docs. Recepcionados:');
			$objPHPExcel->getActiveSheet()->setCellValue('C5', $guias);
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setSize(16);
		$objPHPExcel->getActiveSheet()->getStyle('C1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

		$objPHPExcel->getActiveSheet()->getStyle('C1:C5')->applyFromArray(
		array(
			'font'    => array(
				'bold'      => true
			)
		)
		);

		$objPHPExcel->getActiveSheet()->getStyle('B2:B5')->applyFromArray(
		array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
			)
		)
		);
		
		
	}

if($tipo==1) {
	
	$q=cargar_registros_obj("
		SELECT *,CASE WHEN rnpt_detalle ilike '%soluci%' 
			THEN lower(rnpt_detalle) ELSE rnpt_volumen_total::text END 
			AS rnpt_volumen_total
		FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:50'
		ORDER BY rnpt_id;
	");
	
	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Nro. Rp.');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Vol. (ml)');
		$objPHPExcel->getActiveSheet()->setCellValue('D7', 'Paterno');
		$objPHPExcel->getActiveSheet()->setCellValue('E7', 'Materno');
		$objPHPExcel->getActiveSheet()->setCellValue('F7', 'Nombres');
		$objPHPExcel->getActiveSheet()->setCellValue('G7', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('H7', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('I7', 'Servicio');
		$objPHPExcel->getActiveSheet()->setCellValue('J7', 'Peso (gr)');
		$objPHPExcel->getActiveSheet()->setCellValue('K7', 'Tipo de Bajada');
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('A7:L7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
		print("
			<table style='width:100%;'>
			<tr class='tabla_header'>
			<td>Nro.</td>
			<td>Nro. Rp.</td>
			<td>Volumen (ml)</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombres</td>
			<td>RUT</td>
			<td>Ficha</td>
			<td>Servicio</td>
			<td>Peso (gr)</td>
			<td>Tipo Bajada</td>
			<td>Ver</td>
			</tr>
			");
	} 
	
	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($q[$i]['rnpt_volumen_total']*1){
			$rnpt_volumen=round($q[$i]['rnpt_volumen_total']*1);
		}else{
			$vol=explode('|',$q[$i]['rnpt_volumen_total']);
			$rnpt_volumen=htmlentities($vol[0]);
		}
		
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+8), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+8), $q[$i]['rnpt_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+8), $rnpt_volumen);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+8), utf8_encode($q[$i]['pac_appat']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+8), utf8_encode($q[$i]['pac_apmat']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+8), utf8_encode($q[$i]['pac_nombres']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+8), $q[$i]['pac_rut']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+8), $q[$i]['pac_ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+8), utf8_encode($q[$i]['centro_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+8), $q[$i]['rnpt_peso_gr']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+8), utf8_encode($q[$i]['rnpt_tipo_bajada']));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':L'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			if($rnpt_volumen*1){
					$rnpt_volumen=number_format($q[$i]['rnpt_volumen_total']*1,1,',','.');
			}
			
			print("
				<tr class='$clase'>
				<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
				<td style='text-align:right;'>".$q[$i]['rnpt_id']."</td>
				<td style='text-align:right;'>".$rnpt_volumen."</td>
				<td style='text-align:left;'>".$q[$i]['pac_appat']."</td>
				<td style='text-align:left;'>".$q[$i]['pac_apmat']."</td>
				<td style='text-align:left;'>".$q[$i]['pac_nombres']."</td>
				<td style='text-align:right;font-weight:bold;'>".$q[$i]['pac_rut']."</td>
				<td style='text-align:center;font-weight:bold;'>".$q[$i]['pac_ficha']."</td>
				<td style='text-align:left;'>".htmlentities($q[$i]['centro_nombre'])."</td>
				<td style='text-align:right;'>".number_format($q[$i]['rnpt_peso_gr'],0,',','.')."</td>
				<td style='text-align:left;'>".$q[$i]['rnpt_tipo_bajada']."</td>
				<td style='text-align:left;'>
					<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='visualizar_rnpt(".$q[$i]['rnpt_id'].");' /></center>
				</td>
				</tr>
				");
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

if($tipo==2) {

	$q=cargar_registros_obj("
		SELECT centro_nombre, COUNT(*) AS total FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		GROUP BY centro_nombre
		ORDER BY centro_nombre;
	");
	
	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Nro. Rp.');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Vol. (ml)');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getStyle('A7:C7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
		print("<table style='width:100%;'>
		<tr class='tabla_header'>
		<td>Nro.</td>
		<td>Servicio/Unidad</td>
		<td>Total</td>
		</tr>");
	}
		
	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
				
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+8), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+8), utf8_encode($q[$i]['centro_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+8), round($q[$i]['total']*1));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':C'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			print("
				<tr class='$clase'>
				<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
				<td style='text-align:left;'>".htmlentities($q[$i]['centro_nombre'])."</td>
				<td style='text-align:right;'>".($xls?$q[$i]['total']*1:number_format($q[$i]['total']*1,1,',','.'))."</td>
			");
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

if($tipo==3) {

	$q=cargar_registros_obj("
		SELECT centro_nombre, pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres, COUNT(*) AS total FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		GROUP BY centro_nombre, pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres
		ORDER BY centro_nombre, pac_appat, pac_apmat, pac_nombres;
	");
	
	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Paterno');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Materno');
		$objPHPExcel->getActiveSheet()->setCellValue('D7', 'Nombres');
		$objPHPExcel->getActiveSheet()->setCellValue('E7', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('F7', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('G7', 'Unidad/Servicio');
		$objPHPExcel->getActiveSheet()->setCellValue('H7', 'Total');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getStyle('A7:H7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
	
		print("<table style='width:100%;'>
				<tr class='tabla_header'>
				<td>Nro.</td>
				<td>Paterno</td>
				<td>Materno</td>
				<td>Nombres</td>
				<td>RUT</td>
				<td>Ficha</td>
				<td>Unidad/Servicio</td>
				<td>Total</td>
				</tr>");
	}
	
	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+8), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+8), utf8_encode($q[$i]['pac_appat']));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+8), utf8_encode($q[$i]['pac_apmat']));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+8), utf8_encode($q[$i]['pac_nombres']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+8), $q[$i]['pac_rut']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+8), $q[$i]['pac_ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+8), utf8_encode($q[$i]['centro_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+8), round($q[$i]['total']*1));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':H'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			print("
				<tr class='$clase'>
				<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
				<td style='text-align:left;font-weight:bold;'>".htmlentities($q[$i]['pac_appat'])."</td>
				<td style='text-align:right;font-weight:bold;'>".htmlentities($q[$i]['pac_apmat'])."</td>
				<td style='text-align:center;font-weight:bold;'>".htmlentities($q[$i]['pac_nombres'])."</td>
				<td style='text-align:left;'>".$q[$i]['pac_rut']."</td>
				<td style='text-align:left;'>".$q[$i]['pac_ficha']."</td>
				<td style='text-align:left;'>".htmlentities($q[$i]['centro_nombre'])."</td>
				<td style='text-align:right;'>".number_format($q[$i]['total']*1,1,',','.')."</td>
				
			");
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

} if($tipo==4) {

	$total=0;

	if($func_id) $wr='AND func_id='.$func_id; else $wr='';
	
	

	$q=cargar_registros_obj("
		SELECT func_rut,func_nombre,COUNT(*) AS total FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN funcionario ON func_id=rnpt_func_id
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		$wr
		GROUP BY func_rut,func_nombre
		ORDER BY func_rut,func_nombre;
	");

	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Nombre');
		$objPHPExcel->getActiveSheet()->setCellValue('D7', 'Total');
		$objPHPExcel->getActiveSheet()->mergeCells('D7:D8');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getStyle('A7:E7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
		
	}else{
		print("<table style='width:100%;'>
				<tr class='tabla_header'>
				<td>Nro.</td>
				<td style='width:10%;'>RUT</td><td style='width:40%;'>Nombre</td>
				<td >Total</td>
				</tr>
");
	}
	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+9), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+9), $q[$i]['func_rut']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+9), utf8_encode($q[$i]['func_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+9), round($q[$i]['total']*1));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+9))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+9))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+9))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+9))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+9).':D'.($i+9))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			print("
				<tr class='$clase'>
				<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
				<td style='text-align:left;'>".$q[$i]['func_rut']."</td>
				<td style='text-align:left;'>".htmlentities($q[$i]['func_nombre'])."</td>
				<td style='text-align:right;'>".number_format($q[$i]['total']*1,1,',','.')."</td>
			");
		}
		
		$total+=$q[$i]['total'];
	
	}
	
	
	if($xls){
	
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+9), 'Total:');
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i+9).':C'.($i+9));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+9), round($total));
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+9).':D'.($i+9))->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save(_DIR_.'/listado_npt.xls');
		// Write file to the browser
		$objWriter->save('php://output');
	}else{
		print("<tr class='tabla_header'>
			<td colspan=3 style='text-align:right;'>Total:</td>
			<td style='text-align:right;'>".($xls?$total*1:number_format($total*1,1,',','.'))."</td>
			</tr></table>");	
	}

} if($tipo==5) {
	
	$q=cargar_registros_obj("
		SELECT *,CASE WHEN rnpt_detalle ilike '%soluci%' 
			THEN lower(rnpt_detalle) ELSE rnpt_volumen_total::text END 
			AS rnpt_volumen_total 
		FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		AND rnpt_estado=0
		ORDER BY rnpt_detalle,rnpt_id;
	");

	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Nro. Rp.');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Vol. (ml)');
		$objPHPExcel->getActiveSheet()->setCellValue('D7', 'Paterno');
		$objPHPExcel->getActiveSheet()->setCellValue('E7', 'Materno');
		$objPHPExcel->getActiveSheet()->setCellValue('F7', 'Nombres');
		$objPHPExcel->getActiveSheet()->setCellValue('G7', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('H7', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('I7', 'Servicio');
		$objPHPExcel->getActiveSheet()->setCellValue('J7', 'Peso (gr)');
		$objPHPExcel->getActiveSheet()->setCellValue('K7', 'Tipo de Bajada');
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('A7:L7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
		print("<table style='width:100%;'>
			<tr class='tabla_header'>
			<td>Nro.</td>
			<td>Nro. Rp.</td>
			<td>Volumen (ml)</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombres</td>
			<td>RUT</td>
			<td>Ficha</td>
			<td>Servicio</td>
			<td>Peso (gr)</td>
			<td>Tipo Bajada</td>
			<td>Marcar</td><td>Ver</td>
			</tr>");
	}
		
	$ids='';
	if($q)
	
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($q[$i]['rnpt_volumen_total']*1){
			$rnpt_volumen=round($q[$i]['rnpt_volumen_total']*1);
		}else{
			$vol=explode('|',$q[$i]['rnpt_volumen_total']);
			$rnpt_volumen=htmlentities($vol[0]);
		}
		
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+8), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+8), $q[$i]['rnpt_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+8), $rnpt_volumen);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+8), utf8_encode($q[$i]['pac_appat']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+8), utf8_encode($q[$i]['pac_apmat']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+8), utf8_encode($q[$i]['pac_nombres']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+8), $q[$i]['pac_rut']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+8), $q[$i]['pac_ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+8), utf8_encode($q[$i]['centro_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+8), $q[$i]['rnpt_peso_gr']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+8), utf8_encode($q[$i]['rnpt_tipo_bajada']));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':L'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			
			if($rnpt_volumen*1){
					$rnpt_volumen=number_format($q[$i]['rnpt_volumen_total']*1,1,',','.');
			}
			print("
			<tr class='$clase'>
			<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
			<td style='text-align:right;'>".$q[$i]['rnpt_id']."</td>
			<td style='text-align:right;'>".$rnpt_volumen."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_appat'])."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_apmat'])."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_nombres'])."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['pac_ficha']."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['centro_nombre'])."</td>
			<td style='text-align:right;'>".number_format($q[$i]['rnpt_peso_gr'],0,',','.')."</td>
			<td style='text-align:left;'>".$q[$i]['rnpt_tipo_bajada']."</td>
			<td style='text-align:center;'>
			<input type='checkbox' id='chk_".$q[$i]['rnpt_id']."' name='chk_".$q[$i]['rnpt_id']."' CHECKED />
			</td>
			<td style='text-align:left;'>
				<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='visualizar_rnpt(".$q[$i]['rnpt_id'].");' /></center>
			</td>
			</tr>
			");
		}
			$ids.=$q[$i]['rnpt_id'].'|';
	}
	if($xls){
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save(_DIR_.'/listado_npt.xls');
		// Write file to the browser
		$objWriter->save('php://output');
	}else{
	
		$ids=trim($ids,'| ');
		print("<tr style='display:none;'><td>
				<input type='hidden' id='ids' name='ids' value='$ids'>
				</td></tr>
			</table>");	
	}
} if($tipo==6) {
	
	$q=cargar_registros_obj("
		SELECT *,CASE WHEN rnpt_detalle ilike '%soluci%' 
			THEN lower(rnpt_detalle) ELSE rnpt_volumen_total::text END 
			AS rnpt_volumen_total,
			rnpt_fecha_emision::time AS rnpt_hora 
		FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		AND rnpt_estado=1 AND centro_ruta='$centro_ruta'
		ORDER BY rnpt_detalle,rnpt_id;
	");

	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Nro. Rp.');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Vol. (ml)');
		$objPHPExcel->getActiveSheet()->setCellValue('D7', 'Paterno');
		$objPHPExcel->getActiveSheet()->setCellValue('E7', 'Materno');
		$objPHPExcel->getActiveSheet()->setCellValue('F7', 'Nombres');
		$objPHPExcel->getActiveSheet()->setCellValue('G7', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('H7', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('I7', 'Servicio');
		$objPHPExcel->getActiveSheet()->setCellValue('J7', 'Peso (gr)');
		$objPHPExcel->getActiveSheet()->setCellValue('K7', 'Tipo de Bajada');
		$objPHPExcel->getActiveSheet()->setCellValue('L7', 'Hora Recep.');
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('A7:L7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
		print("<table style='width:100%;'>
			<tr class='tabla_header'>
			<td>Nro.</td>
			<td>Nro. Rp.</td>
			<td>Volumen (ml)</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombres</td>
			<td>RUT</td>
			<td>Ficha</td>
			<td>Servicio</td>
			<td>Peso (gr)</td>
			<td>Tipo Bajada</td>
			<td>Hora Recep.</td>
			<td>Marcar</td><td>Ver</td>
			</tr>");
	}
		
	$ids='';
	if($q)
	
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($q[$i]['rnpt_volumen_total']*1){
			$rnpt_volumen=round($q[$i]['rnpt_volumen_total']*1);
		}else{
			$vol=explode('|',$q[$i]['rnpt_volumen_total']);
			$rnpt_volumen=htmlentities($vol[0]);
		}
		
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+8), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+8), $q[$i]['rnpt_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+8), $rnpt_volumen);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+8), utf8_encode($q[$i]['pac_appat']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+8), utf8_encode($q[$i]['pac_apmat']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+8), utf8_encode($q[$i]['pac_nombres']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+8), $q[$i]['pac_rut']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+8), $q[$i]['pac_ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+8), utf8_encode($q[$i]['centro_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+8), $q[$i]['rnpt_peso_gr']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+8), utf8_encode($q[$i]['rnpt_tipo_bajada']));
			$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+8), utf8_encode($q[$i]['rnpt_hora']));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('L'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':L'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			
			if($rnpt_volumen){
				$rnpt_volumen=number_format($q[$i]['rnpt_volumen_total']*1,1,',','.');
			}
			print("
			<tr class='$clase'>
			<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
			<td style='text-align:right;'>".$q[$i]['rnpt_id']."</td>
			<td style='text-align:right;'>".$rnpt_volumen."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_appat'])."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_apmat'])."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_nombres'])."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['pac_ficha']."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['centro_nombre'])."</td>
			<td style='text-align:right;'>".number_format($q[$i]['rnpt_peso_gr'],0,',','.')."</td>
			<td style='text-align:left;'>".$q[$i]['rnpt_tipo_bajada']."</td>
			<td style='text-align:left;'>".$q[$i]['rnpt_hora']."</td>
			<td style='text-align:center;'>
			<input type='checkbox' id='chk_".$q[$i]['rnpt_id']."' name='chk_".$q[$i]['rnpt_id']."' CHECKED />
			</td>
			<td style='text-align:left;'>
				<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='visualizar_rnpt(".$q[$i]['rnpt_id'].");' /></center>
			</td>
			</tr>
			");
		}
			$ids.=$q[$i]['rnpt_id'].'|';
	}
	
	if($xls){
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save(_DIR_.'/listado_npt.xls');
		// Write file to the browser
		$objWriter->save('php://output');
	}else{
	
		$ids=trim($ids,'| ');
		print("<tr style='display:none;'><td>
				<input type='hidden' id='ids' name='ids' value='$ids'>
				</td></tr>
			</table>");	
	}
} if($tipo==7) {

	$q=cargar_registros_obj("
		select art_id,art_codigo,art_glosa,sum(total)AS total,array_agg(rnpt_doc_num)AS guias FROM(
	SELECT distinct art_id,art_codigo,art_glosa,COUNT(*)AS total,rnpt_doc_num
		FROM (SELECT (CASE WHEN rnpt_detalle ilike '%SOLUCI%' THEN 
			(CASE WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 1%' THEN 11076
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 2%' THEN 11077
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 3%' THEN 11078
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 4%' THEN 11079
			WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 5%' THEN 15059
			END) 
		ELSE (CASE 
			WHEN rnpt_volumen_total<=250 THEN 11071
			WHEN rnpt_volumen_total<=500 THEN 11074
			WHEN rnpt_volumen_total<=1000 THEN 11075
			WHEN rnpt_volumen_total<=2000 THEN 11072
			ELSE 11073 END
			)
		END) AS art_id,rnpt_doc_num  FROM receta_npt
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		AND rnpt_estado!=0)AS foo
		LEFT JOIN articulo USING (art_id)
		GROUP BY art_id,art_codigo,art_glosa,rnpt_doc_num)AS foo2
		group by art_id,art_codigo,art_glosa
		order by art_glosa
	");
	
	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Código');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Artículo');
		$objPHPExcel->getActiveSheet()->setCellValue('D7', 'Cantidad');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getStyle('A7:D7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
	
		print("<table style='width:100%;'>
		<tr class='tabla_header'>
		<td>N#</td>
		<td>C&oacute;digo Int.</td>
		<td>Art&iacute;culo</td>
		<td>Cantidad</td>
		</tr>");
	}
		
	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
				
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+8), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+8), $q[$i]['art_codigo']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+8), utf8_encode($q[$i]['art_glosa']));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+8), round($q[$i]['total']*1));
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':D'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			print("
				<tr class='$clase'>
				<td style='text-align:center;'>".($i+1)."</td>
				<td style='text-align:right;font-weight:bold;'>".$q[$i]['art_codigo']."</td>
				<td style='text-align:left;'>".htmlentities($q[$i]['art_glosa'])."</td>
				<td style='text-align:right;'>".(number_format($q[$i]['total']*1,1,',','.'))."</td>
				</tr>
			");
		}
		
	}
	
	$r=cargar_registros_obj("
		SELECT *,CASE WHEN rnpt_detalle ilike '%soluci%' 
			THEN lower(rnpt_detalle) ELSE rnpt_volumen_total::text END 
			AS rnpt_volumen_total,
			rnpt_fecha_emision::date
		FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:50' AND rnpt_estado=0
		ORDER BY receta_npt.rnpt_fecha_emision;
	");
	
	if($r){
		if($xls){
			
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+10), 'Detalle Recetas NO Recepcionadas');
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+10))->applyFromArray(
			array(
				'font'    => array(
					'bold'      => true
				)
			)
			);
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+11), 'Num.');
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+11), 'Nro. Rp.');
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+11), 'Vol. (ml)');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+11), 'Paterno');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+11), 'Materno');
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+11), 'Nombres');
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+11), 'RUT');
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+11), 'Ficha');
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+11), 'Servicio');
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+11), 'Peso (gr)');
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+11), 'Tipo de Bajada');
			$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+11), 'Fecha Emisi&oacute;n');
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
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+11).':L'.($i+11))->applyFromArray(
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
				)/*,
				'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => 'A0A0A0'
		 			)
		 		)*/
			)
			);
		}else{
			print("
				<table style='width:100%;'>
				<tr class='tabla_header'><td colspan=12><center>Detalle NO Recepcionadas</center></td></tr>
				<tr class='tabla_header'>
				<td>Nro.</td>
				<td>Nro. Rp.</td>
				<td>Volumen (ml)</td>
				<td>Paterno</td>
				<td>Materno</td>
				<td>Nombres</td>
				<td>RUT</td>
				<td>Ficha</td>
				<td>Servicio</td>
				<td>Peso (gr)</td>
				<td>Tipo Bajada</td>
				<td>Fecha Emisi&oacute;n</td>
				<td>Ver</td>
				</tr>
				");
		} 
	
		for($u=0;$u<sizeof($r);$u++) {
		
			$clase=($u%2==0)?'tabla_fila':'tabla_fila2';
			
			if($r[$u]['rnpt_volumen_total']*1){
				$rnpt_volumen=round($r[$u]['rnpt_volumen_total']*1);
			}else{
				$vol=explode('|',$r[$u]['rnpt_volumen_total']);
				$rnpt_volumen=htmlentities($vol[0]);
			}
			
			if($xls){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.($u+($i+12)), ($u+1));
				$objPHPExcel->getActiveSheet()->setCellValue('B'.($u+($i+12)), $r[$u]['rnpt_id']);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.($u+($i+12)), $rnpt_volumen);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.($u+($i+12)), utf8_encode($r[$u]['pac_appat']));
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($u+($i+12)), utf8_encode($r[$u]['pac_apmat']));
				$objPHPExcel->getActiveSheet()->setCellValue('F'.($u+($i+12)), utf8_encode($r[$u]['pac_nombres']));
				$objPHPExcel->getActiveSheet()->setCellValue('G'.($u+($i+12)), $r[$u]['pac_rut']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.($u+($i+12)), $r[$u]['pac_ficha']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.($u+($i+12)), utf8_encode($r[$u]['centro_nombre']));
				$objPHPExcel->getActiveSheet()->setCellValue('J'.($u+($i+12)), $r[$u]['rnpt_peso_gr']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.($u+($i+12)), utf8_encode($r[$u]['rnpt_tipo_bajada']));
				$objPHPExcel->getActiveSheet()->setCellValue('L'.($u+($i+12)), utf8_encode($r[$u]['rnpt_fecha_emision']));
				$objPHPExcel->getActiveSheet()->getStyle('A'.($u+($i+12)))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('B'.($u+($i+12)))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('C'.($u+($i+12)))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('H'.($u+($i+12)))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('J'.($u+($i+12)))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('K'.($u+($i+12)))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('L'.($u+($i+12)))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.($u+($i+12)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('B'.($u+($i+12)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('C'.($u+($i+12)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('G'.($u+($i+12)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('H'.($u+($i+12)))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
				$color=($u%2==0)?'DDDDDD':'EEEEEE';
					
				/*$objPHPExcel->getActiveSheet()->getStyle('A'.($u+($i+12)).':L'.($u+($i+12)))->applyFromArray(
					array(
						'fill' => array(
			 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
			  			'color' => array(
			 				'rgb' => $color
			 			)
						)
					)
					);*/
			}else{
				if($rnpt_volumen*1){
					$rnpt_volumen=number_format($r[$u]['rnpt_volumen_total']*1,1,',','.');
				}
				print("
					<tr class='$clase'>
					<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($u+1)."</td>
					<td style='text-align:right;'>".$r[$u]['rnpt_id']."</td>
					<td style='text-align:right;'>".$rnpt_volumen."</td>
					<td style='text-align:left;'>".htmlentities($r[$u]['pac_appat'])."</td>
					<td style='text-align:left;'>".htmlentities($r[$u]['pac_apmat'])."</td>
					<td style='text-align:left;'>".htmlentities($r[$u]['pac_nombres'])."</td>
					<td style='text-align:right;font-weight:bold;'>".$r[$u]['pac_rut']."</td>
					<td style='text-align:center;font-weight:bold;'>".$r[$u]['pac_ficha']."</td>
					<td style='text-align:left;'>".htmlentities($r[$u]['centro_nombre'])."</td>
					<td style='text-align:right;'>".number_format($r[$u]['rnpt_peso_gr'],0,',','.')."</td>
					<td style='text-align:left;'>".$r[$u]['rnpt_tipo_bajada']."</td>
					<td style='text-align:left;'>".$r[$u]['rnpt_fecha_emision']."</td>
					<td style='text-align:left;'>
					<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='visualizar_rnpt(".$q[$i]['rnpt_id'].");' /></center>
				</td>
					</tr>
					");
			}
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

} if($tipo==8) {

	$q=cargar_registros_obj("
			SELECT *,art_glosa FROM(SELECT *,(CASE WHEN rnpt_detalle ilike '%SOLUCI%' THEN 
				(CASE WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 1%' THEN 11076
				WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 2%' THEN 11077
				WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 3%' THEN 11078
				WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 4%' THEN 11079
				WHEN rnpt_detalle ilike 'SOLUCION NPT MAGISTRAL TIPO 5%' THEN 15059
				END) 
			ELSE (CASE 
				WHEN rnpt_volumen_total<=250 THEN 11071
				WHEN rnpt_volumen_total<=500 THEN 11074
				WHEN rnpt_volumen_total<=1000 THEN 11075
				WHEN rnpt_volumen_total<=2000 THEN 11072
				ELSE 11073 END
				)
			END) AS art_id,COALESCE(date_trunc('second',rnpt_fecha_recep)::text,'(Sin Recepción...)')AS fecha_recep,
		COALESCE((date_trunc('second',rnpt_fecha_desp))::text,'(Sin Despacho...)')AS fecha_desp,
		date_trunc('second',(COALESCE(rnpt_fecha_desp::time,'23:59:59') - rnpt_fecha_recep::time))AS diff,
		CASE WHEN rnpt_detalle ilike '%soluci%' 
			THEN lower(rnpt_detalle) ELSE rnpt_volumen_total::text END 
			AS rnpt_volumen_total 
		FROM receta_npt
		LEFT JOIN pacientes USING (pac_id)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:59' AND rnpt_estado!=0)AS foo
		LEFT JOIN articulo USING (art_id)
		LEFT JOIN centro_costo USING (centro_ruta)
		");
		
	$total=sizeof($q);
	$movs=0;
	
	for($x=0;$x<$total;$x++){
			$hr=explode(':',$q[$x]['diff']);
			if($hr[0]<02){
				$movs++;
			}
	}
	
	$porcentaje=number_format((($movs*100)/$total),1,',','.');
	
		
	if($xls){
		
		$objPHPExcel->getActiveSheet()->setCellValue('A10', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B10', 'Nro. Rp.');
		$objPHPExcel->getActiveSheet()->setCellValue('C10', 'Volumen (ml)');
		$objPHPExcel->getActiveSheet()->setCellValue('D10', 'Paciente');
		$objPHPExcel->getActiveSheet()->setCellValue('E10', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('F10', 'Servicio');
		$objPHPExcel->getActiveSheet()->setCellValue('G10', 'Fecha/Hora Recepci&oacute;n');
		$objPHPExcel->getActiveSheet()->setCellValue('H10', 'Fecha/Hora Despacho');
		$objPHPExcel->getActiveSheet()->setCellValue('I10', 'Diferencia hora');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getStyle('A10:I10')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
		
		print("<table style='width:100%;'>
		<tr class='tabla_header'>
		<td colspan=4>Resumen Indicador</td>
		</tr>
		<tr class='tabla_fila'>
			<td style='text-align:right;width:60%;'> Total Recetas</td>
			<td>$total <i><b>(100%)</b></i></td>
		</tr><tr class='tabla_fila2'>
			<td style='text-align:right;width:60%;'> <b>%</b> Cumplimiento del tiempo de almacenamiento en Farmacia</td>
			<td>$movs <i><b>($porcentaje%)</b></i></td>
		</tr>");
		
		print("<table style='width:100%;'>
		<tr class='tabla_header'>
		<td>Num.</td>
		<td>Nro. Rp.</td>
		<td>Volumen (ml)</td>
		<td>Paciente</td>
		<td>RUT</td>
		<td>Servicio</td>
		<td>Fecha/Hora Recep.</td>
		<td>Fecha/Hora Desp.</td>
		<td>Diferencia Horas</td>
		<td>Ver</td>
		</tr>");
	}
		
	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($q[$i]['rnpt_volumen_total']*1){
				$rnpt_volumen=round($q[$i]['rnpt_volumen_total']*1);
			}else{
				$vol=explode('|',$q[$i]['rnpt_volumen_total']);
				$rnpt_volumen=htmlentities($vol[0]);
			}
				
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+11), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+11), $q[$i]['rnpt_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+11), $rnpt_volumen);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+11), utf8_encode($q[$i]['pac_nombres'].' '.$q[$i]['pac_appat'].' '.$q[$i]['pac_apmat']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+11), $q[$i]['pac_rut']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+11), utf8_encode($q[$i]['centro_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+11), $q[$i]['fecha_recep']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+11), $q[$i]['fecha_desp']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+11), $q[$i]['diff']);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i+11))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':I'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			
			if($rnpt_volumen*1){
				$rnpt_volumen=number_format($q[$i]['rnpt_volumen_total']*1,1,',','.');
			}
			print("
				<tr class='$clase'>
				<td style='text-align:left;'>".($i+1)."</td>
				<td style='text-align:right;'>".$q[$i]['rnpt_id']."</td>
				<td style='text-align:right;'>".$rnpt_volumen."</td>
				<td>".htmlentities(strtoupper($q[$i]['pac_nombres']." ".$q[$i]['pac_appat']." ".$q[$i]['pac_apmat']))."</td>
				<td>".$q[$i]['pac_rut']."</td>
				<td>".htmlentities($q[$i]['centro_nombre'])."</td>
				<td>".$q[$i]['fecha_recep']."</td>
				<td>".$q[$i]['fecha_desp']."</td>
				<td>".$q[$i]['diff']."</td>
				<td style='text-align:left;'>
					<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='visualizar_rnpt(".$q[$i]['rnpt_id'].");' /></center>
				</td>
				</tr>
			");
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

}  if($tipo==9) {
	
	$q=cargar_registros_obj("
		SELECT *,CASE WHEN rnpt_detalle ilike '%soluci%' 
			THEN lower(rnpt_detalle) ELSE rnpt_volumen_total::text END 
			AS rnpt_volumen_total 
		FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_estado>0 and rnpt_fecha_recep IS NOT NULL AND rnpt_doc_num=$nro_busca
		ORDER BY rnpt_detalle,rnpt_id;
	");

	if($xls){
		$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Nro. Rp.');
		$objPHPExcel->getActiveSheet()->setCellValue('C7', 'Vol. (ml)');
		$objPHPExcel->getActiveSheet()->setCellValue('D7', 'Paterno');
		$objPHPExcel->getActiveSheet()->setCellValue('E7', 'Materno');
		$objPHPExcel->getActiveSheet()->setCellValue('F7', 'Nombres');
		$objPHPExcel->getActiveSheet()->setCellValue('G7', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('H7', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('I7', 'Servicio');
		$objPHPExcel->getActiveSheet()->setCellValue('J7', 'Peso (gr)');
		$objPHPExcel->getActiveSheet()->setCellValue('K7', 'Tipo de Bajada');
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('A7:L7')->applyFromArray(
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
			)/*,
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
	  			'color' => array(
	 				'rgb' => 'A0A0A0'
	 			)
	 		)*/
		)
		);
	}else{
		print("<table style='width:100%;'>
			<tr class='tabla_header'>
			<td>Nro.</td>
			<td>Nro. Rp.</td>
			<td>Volumen (ml)</td>
			<td>Paterno</td>
			<td>Materno</td>
			<td>Nombres</td>
			<td>RUT</td>
			<td>Ficha</td>
			<td>Servicio</td>
			<td>Peso (gr)</td>
			<td>Tipo Bajada</td>
			<td>Marcar</td><td>Ver</td>
			</tr>");
	}
		
	$ids='';
	if($q)
	
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($q[$i]['rnpt_volumen_total']*1){
			$rnpt_volumen=round($q[$i]['rnpt_volumen_total']*1);
		}else{
			$vol=explode('|',$q[$i]['rnpt_volumen_total']);
			$rnpt_volumen=htmlentities($vol[0]);
		}
		
		if($xls){
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+8), ($i+1));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+8), $q[$i]['rnpt_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+8), $rnpt_volumen);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+8), utf8_encode($q[$i]['pac_appat']));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+8), utf8_encode($q[$i]['pac_apmat']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+8), utf8_encode($q[$i]['pac_nombres']));
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+8), $q[$i]['pac_rut']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+8), $q[$i]['pac_ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+8), utf8_encode($q[$i]['centro_nombre']));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+8), $q[$i]['rnpt_peso_gr']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+8), utf8_encode($q[$i]['rnpt_tipo_bajada']));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+8))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+8))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
				
			/*$objPHPExcel->getActiveSheet()->getStyle('A'.($i+8).':L'.($i+8))->applyFromArray(
				array(
					'fill' => array(
		 			'type'       => PHPExcel_Style_Fill::FILL_SOLID,
		  			'color' => array(
		 				'rgb' => $color
		 			)
					)
				)
				);*/
		}else{
			
			if($rnpt_volumen*1){
					$rnpt_volumen=number_format($q[$i]['rnpt_volumen_total']*1,1,',','.');
			}
			print("
			<tr class='$clase'>
			<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
			<td style='text-align:right;'>".$q[$i]['rnpt_id']."</td>
			<td style='text-align:right;'>".$rnpt_volumen."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_appat'])."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_apmat'])."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['pac_nombres'])."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['pac_ficha']."</td>
			<td style='text-align:left;'>".htmlentities($q[$i]['centro_nombre'])."</td>
			<td style='text-align:right;'>".number_format($q[$i]['rnpt_peso_gr'],0,',','.')."</td>
			<td style='text-align:left;'>".$q[$i]['rnpt_tipo_bajada']."</td>
			<td style='text-align:center;'>
			<input type='checkbox' id='chk_".$q[$i]['rnpt_id']."' name='chk_".$q[$i]['rnpt_id']."' CHECKED />
			</td>
			<td style='text-align:left;'>
				<center><img src='iconos/magnifier.png' style='cursor:pointer;' onClick='visualizar_rnpt(".$q[$i]['rnpt_id'].");' /></center>
			</td>
			</tr>
			");
		}
			$ids.=$q[$i]['rnpt_id'].'|';
	}
	if($xls){
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		//$objWriter->save(_DIR_.'/listado_npt.xls');
		// Write file to the browser
		$objWriter->save('php://output');
	}else{
	
		$ids=trim($ids,'| ');
		print("<tr style='display:none;'><td>
				<input type='hidden' id='ids' name='ids' value='$ids'>
				</td></tr>
			</table>");	
	}
}

?>
