<?php
	
	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
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
			((CURRENT_DATE-COALESCE(hosp_fecha_hospitalizacion, hosp_fecha_ing)::date) BETWEEN 0 AND 4)
			
			) AS foo ORDER BY dias_espera DESC
			
		");
		
		require_once('../PHPExcel/Classes/PHPExcel.php');
		require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';

		$objPHPExcel = new PHPExcel();

		// Set document properties
		echo date('H:i:s') , " Set document properties" , PHP_EOL;
		$objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital Gustavo Fricke")
							 ->setLastModifiedBy("Sistema GIS Hospital Gustavo Fricke")
							 ->setTitle("Pacientes Hospitalizados durante menos de 5 días.")
							 ->setSubject("Pacientes Hospitalizados durante menos de 5 días.")
							 ->setDescription("Pacientes Hospitalizados durante menos de 5 días.")
							 ->setKeywords("pacientes gis fricke hospital")
							 ->setCategory("Reportes");

		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Pacientes hospitalizados por menos de 5 días.');
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

		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'RUT');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'Nombre Completo');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', '(Sub)Especialidad');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'Servicio Ingreso');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'Médico Tratante');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'Fecha de Ingreso');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', 'Servicio / Sala');
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'Cama');
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Diagnóstico');
		$objPHPExcel->getActiveSheet()->setCellValue('K5', 'Días Hosp.');
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:K5')->applyFromArray(
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
 				)
			),
			'fill' => array(
	 			'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
	  			'rotation'   => 90,
	 			'startcolor' => array(
	 				'argb' => 'FFA0A0A0'
	 			),
	 			'endcolor'   => array(
	 				'argb' => 'FFFFFFFF'
	 			)
	 		)
		)
		);
		
		$csv=utf8_decode("R.U.T.;Ficha;Nombre Completo;(Sub)Especialidad;Servicio Ingreso;Médico Tratante;Fecha Ingreso;Servicio / Sala;Cama;Diagnóstico;Días Hosp.\n");

		$servicios=Array();
		$especialidades=Array();
		
		$esp_sort=Array();
		$serv_sort=Array();
		
		$total_noesp=0;
		$total_nomed=0;
		$total_pacs=0;
		
		if($q)
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

		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('/tmp/listado_pacientes_hosp_ok.xlsx');
		
		file_put_contents('/tmp/listado_pacientes_hosp_ok.csv',$csv);

		$total=sizeof($q);
		
		$resumen1='<table style="width:80%;">
			<tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
				<td>Servicio</td>
				<td>Sin M&eacute;dico(*)</td>
				<td>Sin Especialidad(**)</td>
				<td>Total Pacientes</td>
				<td>%</td>
			</tr>';

		$j=0;

		array_multisort($serv_sort, SORT_STRING, $servicios);

		foreach($servicios AS $serv => $datos) {

				$color=(($j++)%2==0)?'#dddddd':'#eeeeee';
				
				if($datos['critico']!='t') {
					$nomeds=number_format($datos['nomed'],0,',','.');
					$total_nomed+=$datos['nomed']*1;
				} else {
					$nomeds='';
				}
			
				$resumen1.='<tr bgcolor="'.$color.'">
						<td>'.$serv.'</td>
						<td style="text-align:right;">'.$nomeds.'</td>
						<td style="text-align:right;">'.number_format($datos['noesp'],0,',','.').'</td>
						<td style="text-align:right;font-weight:bold;">'.number_format($datos['total'],0,',','.').'</td>
						<td style="text-align:right;">'.number_format(($datos['total']*100)/$total,2,',','.').'</td>
						</tr>';
			
		}
		
		$resumen1.='<tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
				<td style="font-weight:bold;">Totales</td>
				<td style="text-align:right;">'.number_format($total_nomed).'</td>
				<td style="text-align:right;">'.number_format($total_noesp).'</td>
				<td style="text-align:right;font-weight:bold;">'.number_format($total_pacs).'</td>
				<td>100,00%</td>
			</tr>';
		
		$resumen1.='</table>';

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
				<td style="text-align:right;font-weight:bold;">'.number_format($total_pacs).'</td>
				<td style="text-align:right;">100,00</td>
			</tr>';
		
		$resumen2.='</table>';


	ob_start();
	
?>	

<center>
<h2><u>Resumen Diario Sistema Gesti&oacute;n de Camas G.I.S.<br>Hospital Dr. Gustavo Fricke</u><br><?php echo date('d/m/Y'); ?></h2><br /><br />

<i>Adjuntamos a ustedes, tabla adjunta con detalle de pacientes <b>hospitalizados por menos de 5 d&iacute;as</b> seg&uacute;n registros del Sistema GIS.</i>

<br /><br />

<b><u>Res&uacute;men de Pacientes por SERVICIO</u></b><br /><br />

<?php echo $resumen1; ?><i>(*) Sin M&eacute;dico Tratante Asignado<br />(**) Sin Especialidad Asociada</i><br /><br />

<b><u>Res&uacute;men de Pacientes por ESPECIALIDAD</u></b><br /><br />

<?php echo $resumen2; ?><i>(*) Sin M&eacute;dico Tratante Asignado</i><br /><br />

<br /><br />
Atentamente, EU Luis Contreras A.<br />
Jefe de Gesti&oacute;n de Camas y Traslado de Pacientes<br />
Hospital Dr. Gustavo Fricke - Vi&ntilde;a del Mar.

</center>	
	
<?php 

	$html=ob_get_contents();

	ob_end_clean();
		
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
 
 $mime->addAttachment('/tmp/listado_pacientes_hosp_ok.xlsx','application/xlsx');
 $mime->addAttachment('/tmp/listado_pacientes_hosp_ok.csv','text/csv');

 $body = $mime->get();
 $headers = $mime->headers($headers); 
 
 $mail = $smtp->send($to, $headers, $body);
 
	//print(" ".(microtime()-$start)." msecs...");
	
 } 

 //$mails='gestioncamas.hgf@redsalud.gov.cl, directora.hgf@redsalud.gov.cl, jefe.sdm.hgf@redsalud.gov.cl, jefe.medicina.hgf@redsalud.gov.cl, tatigino@hotmail.com, jefe.pensionado.hgf@redsalud.gov.cl, jefe.cirugia.hgf@redsalud.gov.cl, jefe.urologia.hgf@redsalud.gov.cl, mat.sup.neonatologia.hgf@redsalud.gov.cl, jefe.traumatologia.hgf@redsalud.gov.cl, jefe.pediatria.hgf@redsalud.gov.cl, jefe.sqp.hgf@redsalud.gov.cl, jefe.maternidad.hgf@redsalud.gov.cl, jefe.ccv.hgf@redsalud.gov.cl, jefe.uciped.hgf@redsalud.gov.cl, sergiogalvez@fideco.cl, jefe.sda.hgf@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl';
 $mails='gestioncamas.hgf@redsalud.gov.cl, directora.hgf@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl, edgardo.gonzalez.hgf@redsalud.gov.cl, jefe.informatica.hgf@redsalud.gov.cl';

 $mail_array=explode(',', $mails);

 for($i=0;$i<sizeof($mail_array);$i++) {
	$email=trim($mail_array[$i]);
    send_mail($email,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Pac. Hospitalizados - Sistema Gestión de Camas '.date('d/m/Y')),$html);
 }

 //send_mail('rodrigo.carvajal@sistemasexpertos.cl','sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Sistema Gestión de Camas '.date('d/m/Y')),$html);

?>