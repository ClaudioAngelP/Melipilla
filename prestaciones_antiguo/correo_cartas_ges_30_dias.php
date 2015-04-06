<?php 
	
	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	/*

	BUSCA LOS REGISTROS EN LAS SIGUIENTES CONDICIONES:
	
	* NSP1(8), 
	* NSP2(9), 
	X * EXCEPTUADA TEMPORALMENTE(15), 
	X * CASO ELIMINADO(26), 
	X * CASO CERRADO(27), 
	* RECHAZA TEMPORALMENTE(34), 
	* RECHAZA DEFINITIVO(35), 
	* ATENDIDO PARTICULAR(36), 
	* ATENDIDO INSTITUCIONAL(37), 
	X * FALLECIDO(38), 
	* INUBICABLE(39)(6??!!), 
	X * NO CONTACTADO(40), 
	* PROBLEMA PREVISIONAL(48)
	

	Y LAS RECLASIFICA EN: CORREO ENVIADO(11)

	*/
	
	$lista=cargar_registros_obj("
		SELECT * FROM (SELECT
                *,
                (mon_fecha_limite::date-monr_fecha_evento::date)::integer AS dias3,
                (CASE
                WHEN ((mon_fecha_limite-CURRENT_DATE)<0) THEN 2
                WHEN ((mon_fecha_limite-CURRENT_DATE)<=7 OR
                          (mon_fecha_limite-CURRENT_DATE)<=floor((mon_fecha_limite-mon_fecha_inicio)*0.3)) THEN 1
                ELSE 0 END) AS estado, upper(monr_observaciones) AS observa,
                (SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut LIMIT 1) AS pac_id
                FROM monitoreo_ges_registro
                JOIN monitoreo_ges USING (mon_id)
                JOIN lista_dinamica_condiciones ON monr_clase=id_condicion::text
                JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
                WHERE 
                monr_estado=0 AND monr_clase='11' AND (monr_fecha_evento+'30 days'::interval)<CURRENT_DATE
                ORDER BY mon_id) AS foo
                LEFT JOIN pacientes USING (pac_id)
                left join comunas using (ciud_id)
                ORDER BY monr_fecha_evento;
	");
		
		
		require_once('../PHPExcel/Classes/PHPExcel.php');
		require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital Gustavo Fricke")
							 ->setLastModifiedBy("Sistema GIS Hospital Gustavo Fricke")
							 ->setTitle("Pacientes ingresados en las últimas 24 horas.")
							 ->setSubject("Pacientes ingresados en las últimas 24 horas.")
							 ->setDescription("Pacientes ingresados en las últimas 24 horas.")
							 ->setKeywords("pacientes gis fricke hospital")
							 ->setCategory("Reportes");

		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'NOMINA DE CORREOS GES SIN RESPUESTA');
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(30);
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:O5')->applyFromArray(
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

	$condiciones=Array();

	if($lista) {
	
	for($k=0;$k<sizeof($lista);$k++) {
		
		$r=$lista[$k];
		
		$clase=($k%2==0)?'tabla_fila':'tabla_fila2';
		
		//$caso_id=$r['caso_id'];
		
		$key=htmlentities($r['nombre_condicion']);
		
		if(!isset($condiciones[$key])) {
			$condiciones[$key]=Array();
			$condiciones[$key]['total']=0;
		}

		$condiciones[$key]['total']++;
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($k+6), utf8_encode($r['mon_fecha']));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($k+6), $r['mon_rut']);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($k+6), ''); //$r['pac_ficha']
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

		
		$objPHPExcel->getActiveSheet()->getStyle('B'.($k+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($k+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($k+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('L'.($k+6))->getFont()->setBold(true);
		//$objPHPExcel->getActiveSheet()->getStyle('M'.($k+6))->getFont()->setBold(true);
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H'.($k+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			
		$color=($k%2==0)?'DDDDDD':'EEEEEE';
		
		switch($r['estado']*1){
			case 2: $color_estado='FF0000'; break;
			case 1: $color_estado='BEA30A'; break;
			default: $color_estado='000000'; break;
		}
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($k+6).':O'.($k+6))->applyFromArray(
			array(
				'font'    => array(
					'color'		=> array ( 'rgb' => $color_estado )
				),
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
	$objWriter->save('/tmp/nomina_cartas_sin_respuesta.xls');
	
	




		$total=sizeof($lista);
		
		$resumen1='<table style="width:80%;">
			<tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
				<td>Clasificaci&oacute;n</td>
				<td>Cantidad</td>
				<td>%</td>
			</tr>';

		$j=0;

		array_multisort(array_keys($condiciones), $condiciones);

		foreach($condiciones AS $cond => $datos) {
				
				$color=(($j++)%2==0)?'#dddddd':'#eeeeee';
				
				$resumen1.='<tr bgcolor="'.$color.'">
						<td>'.$cond.'</td>
						<td style="text-align:right;font-weight:bold;">'.number_format($datos['total'],0,',','.').'</td>
						<td style="text-align:right;">'.number_format(($datos['total']*100)/$total,2,',','.').'</td>
						</tr>';
			
		}
		
		$resumen1.='<tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
				<td style="font-weight:bold;">Totales</td>
				<td style="text-align:right;font-weight:bold;">'.number_format($total,0,',','.').'</td>
				<td style="text-align:right;">100,00</td>
			</tr>';

		
		$resumen1.='</table>';
			

	ob_start();
	
?>	

<center>
<h2><u>Excepciones y Correos GES<br>Sistema de Monitoreo GES - G.I.S.<br>
Hospital Dr. Gustavo Fricke</u><br>
<?php echo date('d/m/Y H:i'); ?></h2>

<br /><br />

<i>Estimados, se adjunta n&oacute;mina (XLS) de cartas sin respuesta en Monitoreo GES.</i>

<br /><br />

<b><u>Pacientes clasificados como CARTA SIN RESPUESTA</u></b><br /><br />

<?php echo $resumen1; ?>

<br /><br />

<i>Con esta acci&oacute;n, todos estos pacientes han sido reclasificados como "Carta sin respuesta".<br /><br />Saludos cordiales.</i>


</center>	
	
<?php 

	$html=ob_get_contents();

	ob_end_clean();
	
	
	
	
	
	
} else {







	ob_start();
	
?>	

<center>
<h2><u>Excepciones y Correos GES<br>Sistema de Monitoreo GES - G.I.S.<br>
Hospital Dr. Gustavo Fricke</u><br>
<?php echo date('d/m/Y H:i'); ?></h2>

<br /><br />

<i>Estimados, no se detectaron cartas sin respuesta pendientes. Se notifica como aviso de que el proceso si est&aacute; siendo ejecutado.<br /><br />Saludos cordiales.</i>

</center>	
	
<?php 

	$html=ob_get_contents();

	ob_end_clean();
	
}

		
error_reporting(E_ALL);

 include('Mail.php');
 include('Mail/mime.php');

 //$start=microtime();

 function send_mail($to, $from, $subject, $body, $attach) {
 
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
 $mime = new Mail_mime();

 // Setting the body of the email
 $mime->setTXTBody(strip_tags($body));
 $mime->setHTMLBody($body);
 
 if($attach) {
	$mime->addAttachment('/tmp/nomina_cartas_sin_respuesta.xls','application/xlsx');
 }

 $body = $mime->get();
 $headers = $mime->headers($headers); 
 
 $mail = $smtp->send($to, $headers, $body);
 
	//print(" ".(microtime()-$start)." msecs...");
	
 } 
 
 //alealveal@gmail.com, monitorcompras.hgf@redsalud.gov.cl, monitorges.vigentes@redsalud.gov.cl,
 
 $mails='alealveal@gmail.com, monitorcompras.hgf@redsalud.gov.cl, monitorges.vigentes@redsalud.gov.cl,rodrigo.carvajal@sistemasexpertos.cl';
//$mails='rodrigo.carvajal@sistemasexpertos.cl';
 
 $mail_array=explode(',', $mails);
 
 for($i=0;$i<sizeof($mail_array);$i++) {
	$email=trim($mail_array[$i]);
	send_mail($email,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Monitoreo GES - Correos Sin Respuesta '.date('d/m/Y H:i')),$html,$lista);
 }
 
 $id_condicion=50;  // CORREO ENVIADO
 $id_bandeja_o='I';	// BANDEJA EXCEPCIÓN Y CORREOS
 $id_bandeja='AF';	// BANDEJA CORREOS ENVIADOS
 $fevento=date('d/m/Y');
 
 pg_query("START TRANSACTION;");


 if($lista) 
 for($i=0;$i<sizeof($lista);$i++) {
	 	 		
    	$mon_id=$lista[$i]['mon_id']*1;
    	$monr_id=$lista[$i]['monr_id']*1;
    	
	// Actualizar Monitoreo...
	$tmp=cargar_registro("SELECT * FROM monitoreo_ges_registro WHERE mon_id=$mon_id AND monr_estado=0;");

	pg_query("UPDATE monitoreo_ges_registro SET monr_estado=2 WHERE mon_id=$mon_id AND monr_estado=0;");

	$comentarios=utf8_decode('SIN RESPUESTA A CORREO ENVIADO EL '.$lista[$i]['monr_fecha_evento']);

        pg_query("INSERT INTO monitoreo_ges_registro VALUES (
                                DEFAULT, $mon_id, 7, now(), '$id_condicion', '$id_bandeja', '$comentarios', null, 'AUTOMATIZACION CORREOS', '', '$fevento'
                        );");

	pg_query("UPDATE lista_dinamica_caso SET monr_id=CURRVAL('monitoreo_ges_registro_monr_id_seq') WHERE monr_id=".$tmp['monr_id']);

		
    }
 
	pg_query("COMMIT;");

	if($lista)
	echo 'Modificadas '.sizeof($lista).' cartas sin respuesta por email.';
	else	echo 'No hay cartas sin respuesta pendientes.';

	unlink('/tmp/nomina_cartas_sin_respuesta.xls');

?>
