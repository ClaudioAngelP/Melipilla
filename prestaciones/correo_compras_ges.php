<?php 

	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('../conectores/sigh.php');

    function graficar2($vig, $ale, $ven, $total, $borde=0)  {
		
		switch($borde) {
			case 0:	$border='border:0px;'; break;
			case 1:	$border='border:1px solid #999999;'; break;
			case 2:	$border='border:1px solid black;'; break;
		}
		
		if(($vig*1)+($ale*1)+($ven*1)>0) {
		
			$vig=round($vig*100/$total,1);
			$ale=round($ale*100/$total,1);
			$ven=round($ven*100/$total,1);
			$resto=100-($vig+$ale+$ven);
		
		} else {
		
			$html="<table style='width:175px;$border' cellpadding=0 cellspacing=0>
						<tr>";
						
			$html.="<td style='width:100%;background-color:#dddddd;'>&nbsp;</td>";
			
			$html.="</tr></table>";
			
			return $html;
		
		}
			
		$html="<table style='width:175px;$border' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($vig>0) $html.="<td style='width:$vig%;background-color:#218aec;'>&nbsp;</td>";
		if($ale>0) $html.="<td style='width:$ale%;background-color:#f6f90b;'>&nbsp;</td>";
		if($ven>0) $html.="<td style='width:$ven%;background-color:#ff1717;'>&nbsp;</td>";
		if($resto>0) $html.="<td style='width:$resto%;background-color:#bbbbbb;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}


    function graficar($vig, $prox, $ven)  {
		
		$vig=round($vig);
		$prox=round($prox);
		$ven=round($ven);
			
		$html="<table style='width:150px;border:1px solid black;' cellpadding=0 cellspacing=0>
					<tr>";
					
		if($vig>0) $html.="<td style='width:$vig%;background-color:#4444ff;'>&nbsp;</td>";
		if($prox>0) $html.="<td style='width:$prox%;background-color:#BBBB00;'>&nbsp;</td>";
		if($ven>0) $html.="<td style='width:$ven%;background-color:#ff4444;'>&nbsp;</td>";
		
		$html.="</tr></table>";
		
		return $html;
		
	}
	
	$vdetalle=true;
	$vresumen=true;
				
	$lista=cargar_registros_obj("SELECT * FROM (SELECT *, 
										(CURRENT_DATE-monr_fecha::date)::integer AS dias2,
                                        (CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
                                        (mon_fecha_limite::date-monr_fecha_evento::date)::integer AS dias3,
                                        COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS pst_patologia_interna,
                                        COALESCE(trim(pst_garantia_interna), trim(mon_garantia)) AS pst_garantia_interna,
                                        COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS _pst_patologia_interna,
                                        COALESCE(trim(pst_garantia_interna), trim(mon_garantia)) AS _pst_garantia_interna,
                                        monitoreo_ges_registro.monr_id AS id,
                                        (SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut ORDER BY pac_id LIMIT 1) AS pac_id,
										(SELECT monr_valor FROM monitoreo_ges_registro AS mgr2 WHERE mgr2.monr_id=monitoreo_ges_registro.mon_id AND monr_subclase='E' AND mgr2.monr_estado=1 ORDER BY monr_fecha DESC LIMIT 1) AS valores_E
                                        FROM monitoreo_ges_registro 
                                        JOIN monitoreo_ges USING (mon_id)
                                        LEFT JOIN lista_dinamica_bandejas AS lb ON monr_subclase=lb.codigo_bandeja
                                        LEFT JOIN lista_dinamica_condiciones AS lc2 ON monr_clase::bigint=lc2.id_condicion
                                        LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
                                        WHERE NOT mon_estado AND monr_estado=0 AND monr_subclase='N' AND true AND true
                                        ) AS foo 
										LEFT JOIN pacientes USING (pac_id)
                                        LEFT JOIN comunas USING (ciud_id)
                                        LEFT JOIN prevision USING (prev_id)
                                        ORDER BY _pst_patologia_interna, _pst_garantia_interna
                                        ");
					
					
					
	ob_start();
	
	$totales=array();
    $totales_detalle=array();

    $condiciones=array();
    
    $total_pat=0;
    
    if($lista)
    for($i=0;$i<sizeof($lista);$i++) {
	
		if(!isset($condiciones[$lista[$i]['nombre_condicion']])) {
			
			$condiciones[$lista[$i]['nombre_condicion']]['id_condicion']=$lista[$i]['monr_clase'];
			$condiciones[$lista[$i]['nombre_condicion']]['vig']=0;
			$condiciones[$lista[$i]['nombre_condicion']]['ven']=0;
			
		}

		if(!isset($totales[$lista[$i]['pst_patologia_interna']])) {
			
			$totales[$lista[$i]['pst_patologia_interna']]['vig']=0;
			$totales[$lista[$i]['pst_patologia_interna']]['prox']=0;
			$totales[$lista[$i]['pst_patologia_interna']]['ven']=0;
			$totales[$lista[$i]['pst_patologia_interna']]['total']=0;
			
		}

		if(!isset($totales_detalle[$lista[$i]['pst_patologia_interna']]
						  [$lista[$i]['pst_garantia_interna']])) {
							  
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['pst_id']=$lista[$i]['pst_id'];
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['vig']=0;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['prox']=0;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['ven']=0;
			
		}
	
		$totales[$lista[$i]['pst_patologia_interna']]['vig']++;
		$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['vig']++;
	
		$totales[$lista[$i]['pst_patologia_interna']]['total']++;
		
		$_tmp=$totales[$lista[$i]['pst_patologia_interna']]['total'];
		
		if($total_pat<$_tmp) 
			$total_pat=$_tmp;
		
	}
	
	// Ordena los arrays de totales alfabeticamente...
	
	array_multisort(array_keys($condiciones), $condiciones);
	
	foreach($totales AS $key => $val) {
		$ordenar[]=$val['total'];
	}
	
	array_multisort($ordenar, SORT_DESC, $totales);
	
?>


		<center>
		<h2><u>Resumen Compras Autorizadas G.E.S. - GIS<br>Hospital Dr. Gustavo Fricke</u><br><?php echo date('d/m/Y'); ?></h2><br /><br />

		Adjuntamos a usted, n&oacute;mina de compras autorizadas al <?php echo date('d/m/Y'); ?>, para vuestra oportuna gesti&oacute;n.
		
		<br /><br />
				
		<h3><u>Total de Pacientes por Garant&iacute;a/Patolog&iacute;a</u></h3>

<table style='width:100%;font-size:12px;' cellspacing=0>

<tr style='background-color:#BBBBBB;text-align:center;font-weight:bold;' cellspacing=0>
<td>Total</td>
<td>Patolog&iacute;a / Garant&iacute;a</td>
</tr>

<?php 

	$i=0; 
	
	foreach($totales AS $key => $val) {
					
		$class=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$pat=htmlentities($key);	
		$tpat=htmlentities($key);	

		if($tpat=='')
			$tpat="<i>(No especificada...)</i>";	

		array_multisort(array_keys($totales_detalle[$key]), $totales_detalle[$key]);
		
		foreach($totales_detalle[$key] AS $key2 => $val2) {
			
			$i++;

			$gar=htmlentities($key2);
			$tgar=htmlentities($key2);

			if($tgar=='')
				$tgar="<i>(No especificada...)</i>";	

			//$total=$val2['vig']+$val2['ven']+$val2['prox'];
			
			$v1=$val2['vig']*1;
			$v2=$val2['ven']*1;
			$v3=$val2['prox']*1;
			
			$stotal=$v1+$v2+$v3;
			
			$color=($i%2==0)?'DDDDDD':'EEEEEE';

			print("<tr style='background-color:#$color;'>
			<td style='font-size:13px;text-align:right;font-weight:bold;'>$stotal</td>
			<td style='font-size:11px;padding-left:20px;'><i><b>$tgar</b></i> $tpat</td>
			</tr>");
						
		}	
			
	}
		
?>

</table>

</center>	

<?php 

		$html=ob_get_contents();
		
		ob_end_clean();

		require_once('../PHPExcel/Classes/PHPExcel.php');
		require_once '../PHPExcel/Classes/PHPExcel/IOFactory.php';
		
		$archivos=array();
		
		$pat='';
		$gar='';

		$l=0;

		if($lista)
		for($k=0;$k<sizeof($lista);$k++) {		

		if($pat!=$lista[$k]['pst_patologia_interna'] OR $gar!=$lista[$k]['pst_garantia_interna']) {

				$pat=$lista[$k]['pst_patologia_interna'];
				$gar=$lista[$k]['pst_garantia_interna'];
				
				$l=0; // Reinicia planilla en fila 0...

				$objPHPExcel = new PHPExcel();

				// Set document properties
				$objPHPExcel->getProperties()->setCreator("Sistema GIS Hospital Gustavo Fricke")
									 ->setLastModifiedBy("Sistema GIS Hospital Gustavo Fricke")
									 ->setTitle("REPORTE COMPRAS SISTEMA DE MONITOREO GES")
									 ->setSubject("REPORTE COMPRAS SISTEMA DE MONITOREO GES")
									 ->setDescription("REPORTE COMPRAS SISTEMA DE MONITOREO GES")
									 ->setKeywords("pacientes gis fricke hospital ges")
									 ->setCategory("Reportes");

				$objPHPExcel->setActiveSheetIndex(0);

				$objPHPExcel->getActiveSheet()->setCellValue('C1', 'HOSPITAL DR. GUSTAVO FRICKE - SISTEMA GIS');
				$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
				$objPHPExcel->getActiveSheet()->setCellValue('C2', 'Nómina Compras de '.utf8_encode($gar).' '.utf8_encode($pat));
				
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

				$objPHPExcel->getActiveSheet()->setCellValue('A5', ('Nº'));
				$objPHPExcel->getActiveSheet()->setCellValue('B5', 'A. PATERNO A. MATERNO NOMBRE');
				$objPHPExcel->getActiveSheet()->setCellValue('C5', 'RUT');
				$objPHPExcel->getActiveSheet()->setCellValue('D5', 'DV');
				$objPHPExcel->getActiveSheet()->setCellValue('E5', ('Teléfono 1'));
				$objPHPExcel->getActiveSheet()->setCellValue('F5', ('Teléfono 2'));
				$objPHPExcel->getActiveSheet()->setCellValue('G5', ('Dirección'));
				$objPHPExcel->getActiveSheet()->setCellValue('H5', ('Comuna'));
				$objPHPExcel->getActiveSheet()->setCellValue('I5', ('FONASA'));
				$objPHPExcel->getActiveSheet()->setCellValue('J5', 'Código de Prestación');
				$objPHPExcel->getActiveSheet()->setCellValue('K5', 'Patología');
				$objPHPExcel->getActiveSheet()->setCellValue('L5', 'Garantía');
				$objPHPExcel->getActiveSheet()->setCellValue('M5', 'Rama');
				$objPHPExcel->getActiveSheet()->setCellValue('N5', 'Fecha Inicio');
				$objPHPExcel->getActiveSheet()->setCellValue('O5', 'Fecha Límite');
				$objPHPExcel->getActiveSheet()->setCellValue('P5', 'Orden de Compra');
				
				//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
				//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
				
				$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
				$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
				$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
				$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
				$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
				$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
				
				$objPHPExcel->getActiveSheet()->getStyle('A5:P5')->applyFromArray(
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
		
		}

		
		$r=$lista[$k];
				
		$clase=($l%2==0)?'tabla_fila':'tabla_fila2';
		
		//$caso_id=$r['caso_id'];
		
		list($rut, $dv)=explode('-', $r['mon_rut']); 
		
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($l+6), ($l+1));
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($l+6), utf8_encode($r['mon_nombre']));
		$objPHPExcel->getActiveSheet()->setCellValue('C'.($l+6), $rut);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.($l+6), $dv);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.($l+6), utf8_encode($r['pac_fono']));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.($l+6), utf8_encode($r['pac_celular']));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.($l+6), utf8_encode($r['pac_direccion']));
		$objPHPExcel->getActiveSheet()->setCellValue('H'.($l+6), utf8_encode($r['ciud_desc']));
		$objPHPExcel->getActiveSheet()->setCellValue('I'.($l+6), utf8_encode($r['prev_desc']));
		
		$_tmp=false;
		
		preg_match('/\[([0-9]+)\]/',$r['monr_subcondicion'], $_tmp);
		
		if($_tmp)
			$cpresta=($_tmp[1]);
		else
			$cpresta='';

		$objPHPExcel->getActiveSheet()->setCellValue('J'.($l+6), $cpresta);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.($l+6), utf8_encode($r['pst_patologia_interna']));
		$objPHPExcel->getActiveSheet()->setCellValue('L'.($l+6), utf8_encode($r['mon_garantia']));
		$objPHPExcel->getActiveSheet()->setCellValue('M'.($l+6), utf8_encode($r['pst_rama_interna']));
		$objPHPExcel->getActiveSheet()->setCellValue('N'.($l+6), $r['mon_fecha_inicio']);
		$objPHPExcel->getActiveSheet()->setCellValue('O'.($l+6), $r['mon_fecha_limite']);
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.($l+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($l+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.($l+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('P'.($l+6))->getFont()->setBold(true);
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($l+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('C'.($l+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.($l+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('N'.($l+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('O'.($l+6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
		$color=($k%2==0)?'DDDDDD':'EEEEEE';
		
		$color_estado='000000';
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.($l+6).':P'.($l+6))->applyFromArray(
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
		

			$l++;

			if(!isset($lista[$k+1]) OR $pat!=$lista[$k+1]['pst_patologia_interna'] OR $gar!=$lista[$k+1]['pst_garantia_interna']) {
				
					$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER_TRANSVERSE_PAPER);
					
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
					$fname=str_replace(' ','_','/tmp/Sin Oferta '.$pat.' '.$gar.' '.date('d-m-Y').'.xls');
					$objWriter->save($fname);
					
					$archivos[]=$fname;
				
			}
				
	}

error_reporting(E_ALL);

 include('Mail.php');
 include('Mail/mime.php');

 //$start=microtime();

 function send_mail($to, $from, $subject, $body, $files) {
 
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

 for($i=0;$i<sizeof($files);$i++)
	$mime->addAttachment($files[$i],'application/vnd.ms-excel');

 $body = $mime->get();
 $headers = $mime->headers($headers); 
 
 $mail = $smtp->send($to, $headers, $body);
 
	//print(" ".(microtime()-$start)." msecs...");
	
 } 

 //$mails="directora.hgf@redsalud.gov.cl, jefe.sda.hgf@redsalud.gov.cl, coordinador.ges.hgf@redsalud.gov.cl, alealveal@gmail.com, crosales@gmail.com, rodrigo.carvajal@sistemasexpertos.cl";
 //directora.hgf@redsalud.gov.cl, jefe.sda.hgf@redsalud.gov.cl, coordinador.ges.hgf@redsalud.gov.cl, alealveal@gmail.com, crosales@gmail.com, rodrigo.carvajal@sistemasexpertos.cl
//$mails="directora.hgf@redsalud.gov.cl,jefe.sda.hgf@redsalud.gov.cl, coordinador.ges.hgf@redsalud.gov.cl, alealveal@gmail.com, crosales@gmail.com, monitorcompras.hgf@redsalud.gov.cl, monitorges.vigentes@redsalud.gov.cl, monitorges.hgf@redsalud.gov.cl, dadic.hgf@redsalud.gov.cl, veronica.donosov@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl";
 $mails='jefe.sda.hgf@redsalud.gov.cl, coordinador.ges.hgf@redsalud.gov.cl, alealveal@gmail.com, monitorcompras.hgf@redsalud.gov.cl, rodrigo.carvajal@sistemasexpertos.cl';
 
 $mail_array=explode(',', $mails);
 
 for($i=0;$i<sizeof($mail_array);$i++) {
	$email=trim($mail_array[$i]);
	send_mail($email,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Nómina Compras Autorizadas G.E.S. - Hospital Dr. Gustavo Fricke '.date('d/m/Y')),$html,$archivos);
 }


 //send_mail($mails,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Diario G.E.S. - Hospital Dr. Gustavo Fricke '.date('d/m/Y')),$html);


?>
