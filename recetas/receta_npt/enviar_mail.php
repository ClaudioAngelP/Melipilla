<?php 

	require_once('../../conectar_db.php');
	
	$f1=pg_escape_string($_POST['fecha1']);
	$f2=pg_escape_string($_POST['fecha2']);
	
	$tipo=$_POST['tipo_inf']*1;
		
	$q=cargar_registros_obj("
		SELECT * FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE rnpt_fecha_emision BETWEEN '$f1 00:00:00' AND '$f2 23:59:00'
		ORDER BY rnpt_id;
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
		
		$f=cargar_registro("SELECT * FROM funcionario WHERE func_id=".$_SESSION['sgh_usuario_id']);

		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'LISTADO DE RECETAS NPT');
		$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Fecha Emisión:');
		$objPHPExcel->getActiveSheet()->setCellValue('C3', date('d/m/Y H:i:s'));
		$objPHPExcel->getActiveSheet()->setCellValue('B4', 'Revisado Por:');
		$objPHPExcel->getActiveSheet()->setCellValue('C4', utf8_encode($f['func_nombre']));

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


		/*
<td>Nro.</td>
<td>Nro. Rp.</td>
<td>Volumen (ml)</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<td>RUT</td>
<td>Ficha</td>
<td>Servicio</td>
<td>Diagn&oacute;stico</td>
<td>Peso (gr)</td>
<td>Tipo Bajada</td>
*/

		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Num.');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Nro. Rp.');
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'Vol. (ml)');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Paterno');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'Materno');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'Nombres');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'Servicio');
		//$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Diagnóstico');
		/*$objPHPExcel->getActiveSheet()->setCellValue('k5', 'Peso (gr)');
		$objPHPExcel->getActiveSheet()->setCellValue('L5', 'Tipo de Bajada');*/
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Peso (gr)');
		$objPHPExcel->getActiveSheet()->setCellValue('K5', 'Tipo de Bajada');
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
		/*$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);*/
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		
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




	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		/*print("
			<tr class='$clase'>
			<td class='tabla_header' style='text-align:right;font-weight:bold;border:1px solid black;'>".($i+1)."</td>
			<td style='text-align:right;'>".$q[$i]['rnpt_id']."</td>
			<td style='text-align:right;'>".($xls?$q[$i]['rnpt_volumen_total']*1:number_format($q[$i]['rnpt_volumen_total']*1,1,',','.'))."</td>
			<td style='text-align:left;'>".$q[$i]['pac_appat']."</td>
			<td style='text-align:left;'>".$q[$i]['pac_apmat']."</td>
			<td style='text-align:left;'>".$q[$i]['pac_nombres']."</td>
			<td style='text-align:right;font-weight:bold;'>".$q[$i]['pac_rut']."</td>
			<td style='text-align:center;font-weight:bold;'>".$q[$i]['pac_ficha']."</td>
			<td style='text-align:left;'>".$q[$i]['centro_nombre']."</td>
			<td style='text-align:left;'>".$q[$i]['rnpt_diagnostico']."</td>
			<td style='text-align:right;'>".($xls?$q[$i]['rnpt_peso_gr']*1:number_format($q[$i]['rnpt_peso_gr'],0,',','.'))."</td>
			<td style='text-align:left;'>".$q[$i]['rnpt_tipo_bajada']."</td>
			");
					
			
		print("</tr>");*/
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+6), ($i+1));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+6), $q[$i]['rnpt_id']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+6), round($q[$i]['rnpt_volumen_total']*1));
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+6), utf8_encode($q[$i]['pac_appat']));
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+6), utf8_encode($q[$i]['pac_apmat']));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+6), utf8_encode($q[$i]['pac_nombres']));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+6), $q[$i]['pac_rut']);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+6), $q[$i]['pac_ficha']);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+6), utf8_encode($q[$i]['centro_nombre']));
		//$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+6), utf8_encode($q[$i]['rnpt_diagnostico']));
		/*$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+6), $q[$i]['rnpt_peso_gr']);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+6), utf8_encode($q[$i]['rnpt_tipo_bajada']));*/
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+6), $q[$i]['rnpt_peso_gr']);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+6), utf8_encode($q[$i]['rnpt_tipo_bajada']));
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($i+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('H'.($i+6))->getFont()->setBold(true);
		/*$objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('L'.($i+6))->getFont()->setBold(true);*/
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getFont()->setBold(true);
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('H'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objPHPExcel->getActiveSheet()->getStyle('K'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
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

		
	}


	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('/tmp/listado_npt.xls');




		$html="
		
		<center>
		<h2><u>Listado de Recetas NPT - G.I.S.<br>Hospital Dr. Gustavo Fricke</u><br>".date('d/m/Y')."</h2><br /><br />

		Adjuntamos a ud. listado de recetas NPT para el d&iacute;a de hoy.
		
		<br /><br />
		Saludos Cordiales.

		</center>	
		
		";


error_reporting(E_ALL);

 include('Mail.php');
 include('Mail/mime.php');

 //$start=microtime();

 function send_mail($to, $from, $subject, $body) {
 
 $host = "10.8.134.40";
 $username = "sistemagis.hgf@redsalud.gov.cl";
 $password = "12345678";
 
 $headers = array ('From' => $from,
   'To' => $to,
   'Subject' => $subject);
   
 $smtp = Mail::factory('smtp',
   array ('host' => $host,
     'auth' => true,
     'username' => $username,
     'password' => $password));
 
 // Creating the Mime message
 $mime = new Mail_mime($crlf);

 // Setting the body of the email
 $mime->setTXTBody(strip_tags($body));
 $mime->setHTMLBody($body);

 $mime->addAttachment('/tmp/listado_npt.xls','application/vnd.ms-excel');

 $body = $mime->get();
 $headers = $mime->headers($headers); 
 
 $mail = $smtp->send($to, $headers, $body);
 
	//print(" ".(microtime()-$start)." msecs...");
	
 } 

 //$mails=trim('pablo.flores@sistemasexpertos.cl,'.$_POST['mails'], ', ');
 $mails=trim('npt@fresenius-kabi.cl,'.$_POST['mails'], ', ');
 //$mails=$_POST['mails'];

 send_mail($mails,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Listado Recetas NPT Hospital Dr. Gustavo Fricke '.date('d/m/Y')),$html);


?>
