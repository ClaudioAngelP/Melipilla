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
	
	$r=cargar_registros_obj("
		
		SELECT * FROM (
		SELECT nombre_condicion, 
		SUM(CASE WHEN estado=0 THEN 1 ELSE 0 END) AS vigentes,
		SUM(CASE WHEN estado=1 THEN 1 ELSE 0 END) AS alerta,
		SUM(CASE WHEN estado=2 THEN 1 ELSE 0 END) AS vencidas,
		COUNT(*) AS subtotal FROM (
        SELECT 
        *, 
		(CURRENT_DATE)-mon_fecha_limite AS dias,
		trim(pst_patologia_interna) AS pst_patologia_interna,
		trim(pst_garantia_interna) AS pst_garantia_interna,
		(CASE 
		WHEN ((mon_fecha_limite-CURRENT_DATE)<0) THEN 2 
		WHEN ((mon_fecha_limite-CURRENT_DATE)<=7 OR 
			  (mon_fecha_limite-CURRENT_DATE)<=floor((mon_fecha_limite-mon_fecha_inicio)*0.3)) THEN 1 
		ELSE 0 END) AS estado        

        FROM monitoreo_ges 
        JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
        LEFT JOIN monitoreo_ges_registro USING (mon_id)
        LEFT JOIN lista_dinamica_condiciones ON id_condicion=monr_clase::integer
        WHERE NOT mon_estado AND (monr_estado=0 OR monr_estado IS NULL)
        ) AS foo
        GROUP BY nombre_condicion
        ) AS foo2
        ORDER BY (vencidas+alerta+vigentes) DESC, nombre_condicion;
        
		", true);
		
		/*
		
		SELECT * FROM (
		SELECT id_condicion, nombre_condicion,
		(select COUNT(*) FROM monitoreo_ges_registro 
		join monitoreo_ges using (mon_id)
		WHERE monr_estado=0 AND monr_clase::bigint=id_condicion AND mon_fecha_limite<CURRENT_DATE) AS vencidas,
		(select COUNT(*) FROM monitoreo_ges_registro 
		join monitoreo_ges using (mon_id)
		WHERE monr_estado=0 AND monr_clase::bigint=id_condicion AND mon_fecha_limite>=CURRENT_DATE) AS vigentes
		from lista_dinamica_condiciones
		) AS foo 
		WHERE (vencidas>0 OR vigentes>0) 
		ORDER BY (vencidas+vigentes) DESC, nombre_condicion;
		", true);
		* 
		*/
		
	$lista=cargar_registros_obj("SELECT * FROM (SELECT *, 
		(CURRENT_DATE-in_fecha::date)::integer AS dias2,
		(CURRENT_DATE-mon_fecha_limite::date)::integer AS dias,
		(mon_fecha_limite::date-monr_fecha_evento::date)::integer AS dias3,
		COALESCE(trim(pst_patologia_interna), trim(mon_patologia)) AS pst_patologia_interna,
		COALESCE(trim(pst_garantia_interna), trim(mon_garantia)) AS pst_garantia_interna,
		monitoreo_ges_registro.monr_id AS id,
		(CASE 
		WHEN ((mon_fecha_limite-CURRENT_DATE)<0) THEN 2 
		WHEN ((mon_fecha_limite-CURRENT_DATE)<=7 OR 
			  (mon_fecha_limite-CURRENT_DATE)<=floor((mon_fecha_limite-mon_fecha_inicio)*0.3)) THEN 1 
		ELSE 0 END) AS estado
		FROM monitoreo_ges 
		LEFT JOIN monitoreo_ges_registro USING (mon_id)
		LEFT JOIN lista_dinamica_caso AS lc ON lc.monr_id=monitoreo_ges_registro.monr_id
		LEFT JOIN lista_dinamica_instancia AS li ON li.caso_id=lc.caso_id AND in_estado=0
		LEFT JOIN lista_dinamica_bandejas AS lb ON monr_subclase=lb.codigo_bandeja
		LEFT JOIN lista_dinamica_condiciones AS lc2 ON monr_clase::bigint=lc2.id_condicion
		LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
		LEFT JOIN pacientes USING (pac_id)
		WHERE NOT mon_estado AND (monr_estado=0 OR monr_estado IS NULL)) AS foo 
		ORDER BY dias3 ASC, dias DESC;");
					
					
					
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
		
		if($lista[$i]['estado']*1<=1) {
			$condiciones[$lista[$i]['nombre_condicion']]['vig']++;
		} else {
			$condiciones[$lista[$i]['nombre_condicion']]['ven']++;
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
	
		if($lista[$i]['estado']*1==0) {
			$totales[$lista[$i]['pst_patologia_interna']]['vig']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['vig']++;
		} elseif($lista[$i]['estado']*1==1) {
			$totales[$lista[$i]['pst_patologia_interna']]['prox']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['prox']++;
		} else {
			$totales[$lista[$i]['pst_patologia_interna']]['ven']++;
			$totales_detalle[$lista[$i]['pst_patologia_interna']][$lista[$i]['pst_garantia_interna']]['ven']++;
		}
	
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
	
	$total=($r[0]['vigentes']*1)+($r[0]['vencidas']*1)+($r[0]['alerta']*1);

?>


		<center>
		<h2><u>Resumen Diario Monitoreo G.E.S. - GIS<br>Instituto Psiqui&aacute;trico Dr. Jos&eacute; Horwitz Barak</u><br><?php date('d/m/Y'); ?></h2><br /><br />

		Adjuntamos a usted resumen diario de Sistema de Monitoreo G.E.S., para vuestro conocimiento y oportuna gesti&oacute;n.
		
		<br /><br />
		
		<h3><u>Resumen Total de Pacientes por Condici&oacute;n</u></h3>

<table style='width:100%;font-size:12px;' cellspacing=0>

<tr style='background-color:#BBBBBB;text-align:center;font-weight:bold;'>
<td style='width:40%;'>Condici&oacute;n</td>
<td>
Vigentes</td>
<td>
Alerta</td>
<td>
Vencidos</td>
<td>Gr&aacute;fico</td>
</tr>

<?php 

	$totalvig=0;
	$totalven=0;
	$totalale=0;

	for($i=0;$i<sizeof($r);$i++) {
		
		$clase=($i%2==0)?'DDDDDD':'EEEEEE';
		
		if($r[$i]['vencidas']==0) $color='#999999'; else $color='#ff4444';
		if($r[$i]['alerta']==0) $color2='#999999'; else $color2='#ff811b';
		
		if($r[$i]['nombre_condicion']=='')
			$r[$i]['nombre_condicion']='<i>(Sin Clasificar...)</i>';

		
		print("
			<tr style='background-color:#$clase'>
			<td style='text-align:right;font-weight:bold;width:40%;'>".$r[$i]['nombre_condicion']."</td>
			<td style='font-weight:bold;text-align:right;color:#4b5cc3;'>".$r[$i]['vigentes']."</td>
			<td style='font-weight:bold;text-align:right;color:$color2;'>".$r[$i]['alerta']."</td>
			<td style='font-weight:bold;text-align:right;color:$color;'>".$r[$i]['vencidas']."</td>
			<td><center>".graficar2($r[$i]['vigentes']*1,$r[$i]['alerta']*1,$r[$i]['vencidas']*1, $total)."</center></td>
			</tr>
		");
		
		$totalvig+=$r[$i]['vigentes']*1;
		$totalale+=$r[$i]['alerta']*1;
		$totalven+=$r[$i]['vencidas']*1;

	}


		print("
			<tr style='background-color:#BBBBBB;font-weight:bold;'>
			<td style='font-weight:bold;width:40%;text-align:right;'>Totales:</td>
			<td style='font-weight:bold;text-align:right;color:#4b5cc3;'>".$totalvig."</td>
			<td style='font-weight:bold;text-align:right;color:#ff811b;'>".$totalale."</td>
			<td style='font-weight:bold;text-align:right;color:red;'>".$totalven."</td>
			<td style='font-size:16px;'><center><b>".($totalvig+$totalale+$totalven)."</b></center></td>
			</tr>
		");

?>

</table>

		<br /><br />
		
		<h3><u>Resumen de Pacientes por Patolog&iacute;a/Garant&iacute;a</u></h3>


<table style='width:100%;font-size:12px;' cellspacing=0>

<tr style='background-color:#BBBBBB;text-align:center;font-weight:bold;' cellspacing=0>
<td>Total</td>
<td>Vigentes</td>
<td>Vencidas</td>
<td>Gr&aacute;fico</td>
<td colspan=2>Patolog&iacute;a</td>
</tr>

<?php 

	$i=0; 
	
	foreach($totales AS $key => $val) {
			
		$i++;
		
		$class=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$pat=htmlentities($key);	
		$tpat=htmlentities($key);	

		if($tpat=='')
			$tpat="<i>(No especificada...)</i>";	

		//$total=$val['vig']+$val['prox']+$val['ven'];
		
		$v1=$val['vig'];
		$v2=$val['ven'];
		$v3=$val['prox'];

			
		print("<tr style='background-color:#CCCCCC;'>");
		
		// ARREGLAR, SELECCIONA O QUITA LAS GARANTIAS DE LA PATOLOGIA...		
		
		print("
		<td style='text-align:right;color:black;font-size:16px;'>".($val['vig']+$val['prox']+$val['ven'])."</td>
		<td style='text-align:right;color:blue;font-size:14px;'>".($val['vig']+$val['prox'])."</td>
		<td style='text-align:right;color:red;font-size:14px;'>".($val['ven'])."</td>
		<td><center>".graficar2($v1, $v3, $v2, $total_pat, 2)."</center></td>
		<td style='font-weight:bold;font-size:14px;' colspan=2>$tpat</td>
		");
				
		
		print("</tr>");

		array_multisort(array_keys($totales_detalle[$key]), $totales_detalle[$key]);
		
		foreach($totales_detalle[$key] AS $key2 => $val2) {

			$gar=htmlentities($key2);
			$tgar=htmlentities($key2);

			if($tgar=='')
				$tgar="<i>(No especificada...)</i>";	

			//$total=$val2['vig']+$val2['ven']+$val2['prox'];
			
			$v1=$val2['vig'];
			$v2=$val2['ven'];
			$v3=$val2['prox'];

			print("<tr style='background-color:#EEEEEE;'>");

			print("
			<td style='text-align:right;color:black;font-size:12px;'>".($val2['vig']+$val2['prox']+$val2['ven'])."</td>
			<td style='text-align:right;color:blue;font-size:11px;'>".($val2['vig']+$val2['prox'])."</td>
			<td style='text-align:right;color:red;font-size:11px;'>".$val2['ven']."</td>
			<td><center>".graficar2($v1, $v3, $v2, $total_pat, 0)."</center></td>
			<td style='font-weight:bold;font-size:12px;width:20px;'>
			<center>&ordm;</center>
			</td>
			<td style='font-size:11px;padding-left:20px;'><span style='cursor:pointer;'><i>$tgar</i></span></td>
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

		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Sistema GIS Instituto Psiquatrico")
							 ->setLastModifiedBy("Sistema GIS Instituto Psiquiatrico")
							 ->setTitle("REPORTE DIARIO SISTEMA DE MONITOREO GES")
							 ->setSubject("REPORTE DIARIO SISTEMA DE MONITOREO GES")
							 ->setDescription("REPORTE DIARIO SISTEMA DE MONITOREO GES")
							 ->setKeywords("pacientes gis hospital ges")
							 ->setCategory("Reportes");

		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->setCellValue('C1', 'INSTITUTO PSIQUATRICO DR. JOSE HORWITZ BARAK - SISTEMA GIS');
		$objPHPExcel->getActiveSheet()->setCellValue('B2', 'Reporte:');
		$objPHPExcel->getActiveSheet()->setCellValue('C2', 'LISTADO DIARIO DE MONITOREO GES, PACIENTES EN ALERTA AMARILLA/ROJA');
		
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
	$objWriter->save('/tmp/listado_monitoreo_ges.xls');

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

 $mime->addAttachment('/tmp/listado_monitoreo_ges.xls','application/vnd.ms-excel');

 $body = $mime->get();
 $headers = $mime->headers($headers); 
 
 $mail = $smtp->send($to, $headers, $body);
 
	//print(" ".(microtime()-$start)." msecs...");
	
 } 

 $mails="enzo.barrueto@redsalud.gov.cl, david.reichberg@redsalud.gov.cl, misael.sandoval@redsalud.gov.cl, rodrigo.corvalan@redsalud.gov.cl, karen.zapata@redsalud.gov.cl, pablo.flores@sistemasexpertos.cl, rodrigo.carvajal@sistemasexpertos.cl";
 //directora.hgf@redsalud.gov.cl, jefe.sda.hgf@redsalud.gov.cl, coordinador.ges.hgf@redsalud.gov.cl, alealveal@gmail.com, crosales@gmail.com, rodrigo.carvajal@sistemasexpertos.cl
 
 $mail_array=explode(',', $mails);
 
 for($i=0;$i<sizeof($mail_array);$i++) {
	$email=trim($mail_array[$i]);
	send_mail($email,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Diario G.E.S. - Instituto Psiq. Dr. JOSE HORWITZ BARAK '.date('d/m/Y')),$html);
 }


 //send_mail($mails,'sistemagis.hgf@redsalud.gov.cl',utf8_decode('Resumen Diario G.E.S. - Hospital Dr. Gustavo Fricke '.date('d/m/Y')),$html);


?>
