<?php

	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	/*
	select hosp_fecha_ing, hosp_esp_id, hosp_doc_id, hosp_diag_cod, pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres, ptras_cama_destino AS cama from paciente_traslado 
			join hospitalizacion using (hosp_id)
			join pacientes on hosp_pac_id=pac_id
			where ptras_fecha::date=CURRENT_DATE-('1 day'::interval)
			UNION
	*/
			
	/*
	
				SELECT *, t1.tcama_critico AS critico FROM (
			
			select hosp_fecha_hospitalizacion, hosp_esp_id, hosp_doc_id, hosp_diag_cod, pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres, hosp_numero_cama AS cama, hosp_procedencia from hospitalizacion
			join pacientes on hosp_pac_id=pac_id
			where hosp_fecha_hospitalizacion>=CURRENT_TIMESTAMP-('24 hours'::interval) AND hosp_fecha_hospitalizacion<=CURRENT_TIMESTAMP
			
			UNION

			select ptras_fecha AS hosp_fecha_hospitalizacion, hosp_esp_id, hosp_doc_id, hosp_diag_cod, pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres, hosp_numero_cama AS cama, hosp_procedencia
			from paciente_traslado 
			join hospitalizacion USING (hosp_id)
			join pacientes on hosp_pac_id=pac_id
			join clasifica_camas AS t1 on t1.tcama_num_ini<=ptras_cama_origen AND t1.tcama_num_fin>=ptras_cama_origen
			join clasifica_camas AS t2 on t2.tcama_num_ini<=ptras_cama_destino AND t2.tcama_num_fin>=ptras_cama_destino
			where 
			ptras_fecha>=CURRENT_TIMESTAMP-('24 hours'::interval) AND ptras_fecha<=CURRENT_TIMESTAMP AND
			t1.tcama_critico AND (NOT t2.tcama_critico AND NOT t2.tcama_ambulatorio)

			) AS foo
			
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod

			LEFT JOIN tipo_camas ON
				cama_num_ini<=cama AND cama_num_fin>=cama
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=cama AND t1.tcama_num_fin>=cama

			WHERE NOT tcama_ambulatorio AND NOT tcama_critico
			ORDER BY tcama_tipo, pac_appat, pac_apmat, pac_nombres;

	
	
	*/

	
	$q=cargar_registros_obj("
		
		SELECT *, foo.hosp_fecha_hospitalizacion AS fecha FROM (

		SELECT hosp_id, hosp_fecha_hospitalizacion, pacientes.*, '' AS
		servicio_origen, tcama_tipo AS servicio_destino, 'NUEVO' AS tipo_ing
		FROM hospitalizacion
		JOIN pacientes ON hosp_pac_id=pac_id
		join clasifica_camas AS t1 on
		t1.tcama_num_ini<=hosp_numero_cama
		AND t1.tcama_num_fin>=hosp_numero_cama

		WHERE hosp_fecha_hospitalizacion BETWEEN CURRENT_TIMESTAMP-('24 hours'::interval) AND CURRENT_TIMESTAMP AND NOT t1.tcama_ambulatorio

		UNION

		SELECT hosp_id, ptras_fecha AS hosp_fecha_hospitalizacion,
		pacientes.*, t1.tcama_tipo AS servicio_origen, t2.tcama_tipo AS
		servicio_destino,'INTERSERVICIO' AS tipo_ing
		FROM paciente_traslado
		JOIN hospitalizacion USING (hosp_id)
		JOIN pacientes ON hosp_pac_id=pac_id
		join clasifica_camas AS t1 on
		t1.tcama_num_ini<=ptras_cama_origen
		AND t1.tcama_num_fin>=ptras_cama_origen
		join clasifica_camas AS t2 on
		t2.tcama_num_ini<=ptras_cama_destino
		AND t2.tcama_num_fin>=ptras_cama_destino
		WHERE ptras_fecha BETWEEN CURRENT_TIMESTAMP-('24 hours'::interval) AND CURRENT_TIMESTAMP
		AND NOT t1.tcama_id=t2.tcama_id AND NOT t2.tcama_ambulatorio
		) AS foo 

		JOIN hospitalizacion USING (hosp_id)
		
		LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
		
		LEFT JOIN doctores ON hosp_doc_id=doc_id
			
		LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod

		LEFT JOIN tipo_camas ON
			cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
		LEFT JOIN clasifica_camas AS t1 ON 
			t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama

		ORDER BY servicio_destino, pac_appat, pac_apmat, pac_nombres;		

			
		");

		
		require_once('../PHPExcel/Classes/PHPExcel.php');
		require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';

		$objPHPExcel = new PHPExcel();

		// Set document properties
		echo date('H:i:s') , " Set document properties" , PHP_EOL;
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
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Pacientes ingresados en las últimas 24 horas.');
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

		$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Servicio');
		$objPHPExcel->getActiveSheet()->setCellValue('B5', 'Sala');
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'Cama');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', 'Procedencia');
		$objPHPExcel->getActiveSheet()->setCellValue('E5', 'R.U.T.');
		$objPHPExcel->getActiveSheet()->setCellValue('F5', 'Ficha');
		$objPHPExcel->getActiveSheet()->setCellValue('G5', 'Nombre Completo');
		$objPHPExcel->getActiveSheet()->setCellValue('H5', '(Sub)Especialidad');
		$objPHPExcel->getActiveSheet()->setCellValue('I5', 'Médico Tratante');
		$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Fecha Asignación de Cama');
		$objPHPExcel->getActiveSheet()->setCellValue('K5', 'Diagnóstico');
		$objPHPExcel->getActiveSheet()->setCellValue('L5', 'Serv. Origen');
		$objPHPExcel->getActiveSheet()->setCellValue('M5', 'Serv. Destino');
		$objPHPExcel->getActiveSheet()->setCellValue('N5', 'Tipo Ingreso');
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:N5')->applyFromArray(
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
		
		
		$csv=utf8_decode("Servicio;Sala;Cama;Procedencia;R.U.T.;Ficha;Nombre Completo;(Sub)Especialidad;Médico Tratante;Fecha Hosp.;Diagnóstico\n");
		
		$servicios=Array();
		$especialidades=Array();
		
		$esp_sort=Array();

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
				
			switch($q[$i]['hosp_procedencia']*1) {
				case 0: $procedencia="UEA"; break;
				case 1: $procedencia="UEI"; break;
				case 2: $procedencia="UEGO"; break;
				case 4: $procedencia="Obst. y Gine."; break;
				case 5: $procedencia=utf8_decode("Hospitalización"); break;
				case 6: $procedencia="At. Ambulatoria"; break;
				case 3: $procedencia="Otro Hospital"; break;
			}
			
			/*
<option value='0'>U. Emergencia Adulto (UEA)</option>
<option value='1'>U. Emergencia Infantil (UEI)</option>
<option value='2'>U. Emergencia Maternal (UEGO)</option>
<option value='4'>Obstetricia y Ginecolog&iacute;a</option>
<option value='5'>Hospitalizaci&oacute;n</option>
<option value='6'>Atenci&oacute;n Ambulatoria</option>
<option value='3'>Otro Hospital</option>
			 * */
			
			$csv.=$servicio.";";	
			$csv.=$q[$i]['tcama_tipo'].' '.$q[$i]['cama_tipo'].";";
            $csv.=(($q[$i]['hosp_numero_cama']*1-$q[$i]['tcama_num_ini']*1)+1).";";
            $csv.=$procedencia.";";
                        
            if(!isset($servicios[$servicio])) {
				$servicios[$servicio]=Array();
				$servicios[$servicio]['critico']=$q[$i]['tcama_critico'];
				$servicios[$servicio]['total']=0;
				$servicios[$servicio]['nomed']=0;
				$servicios[$servicio]['noesp']=0;
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
			$csv.=$med_tratante.";";
			$csv.=$q[$i]['hosp_fecha_hospitalizacion'].";";
			$csv.=$q[$i]['hosp_diag_cod']." ".$q[$i]['diag_desc'].";";
			$csv.="\n";
			
			if($q[$i]['servicio_origen']=='')
				$q[$i]['servicio_origen']="(".$procedencia.")";
			
			$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+6), utf8_encode($servicio));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+6), utf8_encode($q[$i]['cama_tipo']));
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+6), (($q[$i]['hosp_numero_cama']*1-$q[$i]['tcama_num_ini']*1)+1));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($i+6), utf8_encode($procedencia));
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+6), utf8_encode($q[$i]['pac_rut']));
			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+6), $q[$i]['pac_ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.($i+6), utf8_encode($q[$i]['pac_appat'].' '.$q[$i]['pac_apmat'].' '.$q[$i]['pac_nombres']));
			$objPHPExcel->getActiveSheet()->setCellValue('H'.($i+6), utf8_encode($especialidad));
			$objPHPExcel->getActiveSheet()->setCellValue('I'.($i+6), utf8_encode($med_tratante));
			$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+6), substr($q[$i]['fecha'],0,16));
			$objPHPExcel->getActiveSheet()->setCellValue('K'.($i+6), utf8_encode($q[$i]['hosp_diag_cod']." ".$q[$i]['diag_desc']));
			
			$objPHPExcel->getActiveSheet()->setCellValue('L'.($i+6), utf8_encode($q[$i]['servicio_origen']));
			$objPHPExcel->getActiveSheet()->setCellValue('M'.($i+6), utf8_encode($q[$i]['servicio_destino']));
			$objPHPExcel->getActiveSheet()->setCellValue('N'.($i+6), utf8_encode($q[$i]['tipo_ing']));
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('H'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('N'.($i+6))->getFont()->setBold(true);
			
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$color=($i%2==0)?'DDDDDD':'EEEEEE';
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+6).':N'.($i+6))->applyFromArray(
			array(
				'fill' => array(
					'type'       => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array(
						'rgb' => $color
					)
				)
			)
			);


			$servicios[$servicio]['total']++;
			$especialidades[$especialidad]['total']++;
			$total_pacs++;
			
			if($q[$i]['doc_id']*1==0)
				$servicios[$servicio]['nomed']++;
				
			if(trim($q[$i]['esp_desc'])=='') {
				$servicios[$servicio]['noesp']++;
				$total_noesp++;

			}

			if($q[$i]['doc_id']*1==0) {
				$especialidades[$especialidad]['nomed']++;
			}
			
		}
		
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('/tmp/listado_pacientes_ingresados.xlsx');

		file_put_contents('/tmp/listado_pacientes_ingresados.csv',$csv);

		$total=sizeof($q);
		
		$resumen1='<table style="width:80%;">
			<tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
				<td>Servicio</td>
				<td>Sin M&eacute;dico(1)</td>
				<td>Sin Especialidad(2)</td>
				<td>Total Pacientes</td>
				<td>%(3)</td>
			</tr>';

		$j=0;

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
				<td style="text-align:right;">100,00</td>
			</tr>';

		
		$resumen1.='</table>';

		$resumen2='<table style="width:80%;">
			<tr bgcolor="#bbbbbb" style="text-align:center;font-weight:bold;">
				<td>Especialidad</td>
				<td>Sin M&eacute;dico(1)</td>
				<td>Total Pacientes</td>
				<td>%(2)</td>
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
<h2><u>Resumen de Ingresos a HGF &uacute;ltimas 24 hrs.
<br>Hospital Dr. Gustavo Fricke<br></u>
<?php echo date('d/m/Y'); ?></h2>

<br /><br />

<i>Estimados(as), saludos cordiales, desde la Unidad de Gesti&oacute;n de Camas 
de HGF y GIS, se env&iacute;a a ustedes el listado de pacientes 
(ver documento adjunto) que han ingresado a vuestros servicios en las &uacute;ltimas 24 horas. </i>

<br /><br />

<b><u>Res&uacute;men de Pacientes Ingresados por SERVICIO</u></b><br /><br />

<?php echo $resumen1; ?><i>(1) Sin M&eacute;dico Tratante Asignado<br />(2) Sin Especialidad Asociada<br />(3) Porcentaje del Total de Pacientes</i><br /><br />

<b><u>Res&uacute;men de Pacientes Ingresados por ESPECIALIDAD</u></b><br /><br />

<?php echo $resumen2; ?><i>(1) Sin M&eacute;dico Tratante Asignado<br />(2) Porcentaje del Total de Pacientes</i><br /><br />

<br /><br />
Atentamente, EU Luis Contreras A.<br />
Supervisor de Gesti&oacute;n de Camas y Traslado de Pacientes<br />
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

 $mime->addAttachment('/tmp/listado_pacientes_ingresados.xlsx','application/xlsx');
 $mime->addAttachment('/tmp/listado_pacientes_ingresados.csv','text/csv');

 $body = $mime->get();
 $headers = $mime->headers($headers);

 $mail = $smtp->send($to, $headers, $body);

	//print(" ".(microtime()-$start)." msecs...");

 }

 $mails='gestioncamas.hgf@redsalud.gov.cl, directora.hgf@redsalud.gov.cl, jefe.sdm.hgf@redsalud.gov.cl, jefe.medicina.hgf@redsalud.gov.cl, tatigino@hotmail.com, jefe.pensionado.hgf@redsalud.gov.cl, jefe.cirugia.hgf@redsalud.gov.cl, jefe.urologia.hgf@redsalud.gov.cl, mat.sup.neonatologia.hgf@redsalud.gov.cl, jefe.traumatologia.hgf@redsalud.gov.cl, jefe.pediatria.hgf@redsalud.gov.cl, jefe.sqp.hgf@redsalud.gov.cl, jefe.maternidad.hgf@redsalud.gov.cl, jefe.ccv.hgf@redsalud.gov.cl, jefe.uciped.hgf@redsalud.gov.cl, sergiogalvez@fideco.cl, jefe.sda.hgf@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl, enf.ccv.hgf@redsalud.gov.cl, enf.cirugia.hgf@redsalud.gov.cl, enf.dialisis.hgf@redsalud.gov.cl, enf.endos.hgf@redsalud.gov.cl, enf.hemo.hgf@redsalud.gov.cl, enf.iih.hgf@redsalud.gov.cl, matsup.neonatologia.hgf@redsalud.gov.cl, matsup.ginecologia.hgf@redsalud.gov.cl, enf.pediatria.hgf@redsalud.gov.cl, enf.pensionado.hgf@redsalud.gov.cl, enf.trenal.hgf@redsalud.gov.cl, enf.uapq.hgf@redsalud.gov.cl, enf.uci.hgf@redsalud.gov.cl, enf.ucicv.hgf@redsalud.gov.cl, enf.uciped.hgf@redsalud.gov.cl, enf.uea.hgf@redsalud.gov.cl, enf.uei@redsalud.gov.cl, enf.ua.hgf@redsalud.gov.cl, enf.urologia.hgf@redsalud.gov.cl, enf.medicina.hgf@redsalud.gov.cl, enf.sota.hgf@redsalud.gov.cl, enf.uciped.hgf@redsalud.gov.cl, enf.sqp.hgf@redsalud.gov.cl, edgardo.gonzalez.hgf@redsalud.gov.cl';
// enf.ccv.hgf@redsalud.gov.cl, enf.cirugia.hgf@redsalud.gov.cl, enf.dialisis.hgf@redsalud.gov.cl, enf.endos.hgf@redsalud.gov.cl, enf.hemo.hgf@redsalud.gov.cl, enf.iih.hgf@redsalud.gov.cl, matsup.neonatologia.hgf@redsalud.gov.cl, matsup.ginecologia.hgf@redsalud.gov.cl, enf.pediatria.hgf@redsalud.gov.cl, enf.pensionado.hgf@redsalud.gov.cl, enf.trenal.hgf@redsalud.gov.cl, enf.uapq.hgf@redsalud.gov.cl, enf.uci.hgf@redsalud.gov.cl, enf.ucicv.hgf@redsalud.gov.cl, enf.uciped.hgf@redsalud.gov.cl, enf.uea.hgf@redsalud.gov.cl, enf.uei@redsalud.gov.cl, enf.ua.hgf@redsalud.gov.cl, enf.urologia.hgf@redsalud.gov.cl, enf.medicina.hgf@redsalud.gov.cl, enf.sota.hgf@redsalud.gov.cl, enf.uciped.hgf@redsalud.gov.cl
 //$mails='gestioncamas.hgf@redsalud.gov.cl, edgardo.gonzalez.hgf@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl';
 //$mails='rodrigo.carvajal@sistemasexpertos.cl';


 $mail_array=explode(',', $mails);

 for($i=0;$i<sizeof($mail_array);$i++) {
	$email=trim($mail_array[$i]);
	send_mail($email,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Diario de Ingresos, últimas 24 Horas '.date('d/m/Y')),$html);
 }

 //send_mail('rodrigo.carvajal@sistemasexpertos.cl','sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Ingresos Sistema Gestión de Camas '.date('d/m/Y')),$html);

?>
