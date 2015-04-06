<?php 

	require_once('../../conectar_db.php');

	error_reporting(E_ALL);
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	
	$xls=isset($_POST['xls']);
	
	function number_format2($num, $dig, $c, $p) {
		GLOBAL $xls;
		if(!$xls)
			return number_format($num, $dig, $c, $p);
		else
			return number_format($num, $dig, '', '.');
	}

	function number_format3($num, $dig, $c, $p) {
		GLOBAL $xls;
		if(!$xls)
			return '$'.number_format($num, $dig, $c, $p).'.-';
		else
			return number_format($num, $dig, '', '.');
	}

	
	$detalle= cargar_registros_obj("SELECT DISTINCT art_codigo,art_glosa,forma_nombre FROM articulo
		JOIN stock ON stock_art_id=art_id
		JOIN logs ON log_id=stock_log_id
		LEFT JOIN bodega_forma ON forma_id=art_forma
		WHERE log_fecha BETWEEN '".$fecha1."' AND '".$fecha2."'
		AND art_arsenal=true AND art_activado=true
		AND log_tipo IN (9,15,18) 
		AND stock_cant<0
		ORDER BY art_glosa,forma_nombre
		--and stock_bod_id in ()
	");
	
	$movs=sizeof($detalle);
	
	$arsenal=pg_query("SELECT * FROM articulo WHERE art_arsenal=true AND art_activado=true");
	
	$total=pg_num_rows($arsenal);
	
	$porcentaje=number_format((($movs*100)/$total),1,',','.');
	
	if($porcentaje<80)
		$puntuacion=0;
	else if ($porcentaje>=80 AND $porcentaje<(83.3))
		$puntuacion=1;
	else if ($porcentaje>=(83.3) AND $porcentaje<(86.6))
		$puntuacion=2;
	else if ($porcentaje>=(86.6) AND $porcentaje<90)
		$puntuacion=3;
	else if ($porcentaje>=90)
		$puntuacion=4;
		
	if(isset($_POST['xls'])) {
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: filename=\"Porcentaje_Uso_Arsenal.xls\";");
	}
	
?>
<table style='width:100%;'>
<tr class='tabla_header'>
<td colspan=4><center><b>Porcentaje de Uso del Arsenal Farmacol&oacute;gico</b></center>
</td>
</tr>
<tr>
<td style='width:10%;' class='tabla_header'>Fecha inicial:</td>
<td style='text-align:left;font-weight:bold;width:10%;'><?php echo $fecha1; ?></td>
<td style='width:10%;' class='tabla_header'>Fecha final:</td>
<td style='text-align:left;font-weight:bold;width:10%;'><?php echo $fecha2; ?></td>
</tr>
<tr>
<td class='tabla_header'>Utilizados / Total</td><td><?php echo '<b>'.$movs.' /</b> '.$total; ?></td>
<td class='tabla_header'>No Utilizados</td><td><?php echo '<b>'.($total-$movs).'</b>'?></td>
</tr>
<tr>
<td class='tabla_header'>Porcentaje</td><td><?php echo '<b>'.$porcentaje.'%</b>'; ?></td>
<td class='tabla_header'>Puntuaci&oacute;n</td><td><?php echo '<b>'.$puntuacion.'</b>'; ?></td>
</tr>
</table>

<?php

	print("<table style='width:100%;'>
			<tr class='tabla_header'>
				<td colspan=4>Detalle Art&iacute;culos <b>Utilizados</b></td>
			</tr>
			<tr class='tabla_header'>
				<td>N &ordm;</td>
				<td>C&oacute;digo</td>
				<td>Medicamento</td>
				<td>Formato</td>
			</tr>"); 
	
	for($i=0;$i<sizeof($detalle);$i++){
	
		($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
	
		print("
			<tr class=".$clase.">
				<td>".($i+1)."</td>
				<td>".$detalle[$i]['art_codigo']."</td>
				<td>".htmlentities($detalle[$i]['art_glosa'])."</td>
				<td>".$detalle[$i]['forma_nombre']."</td>							
			</tr>
		");		
		
	}
	
	$nousados=cargar_registros_obj("SELECT art_codigo,art_glosa,forma_nombre FROM articulo
				LEFT JOIN bodega_forma ON forma_id=art_forma
				WHERE art_id NOT IN(SELECT DISTINCT art_id FROM articulo
		JOIN stock ON stock_art_id=art_id
		JOIN logs ON log_id=stock_log_id
		LEFT JOIN bodega_forma ON forma_id=art_forma
		WHERE log_fecha BETWEEN '".$fecha1."' AND '".$fecha2."'
		AND art_arsenal=true 
		AND log_tipo IN (9,15,18) 
		AND stock_cant<0) AND art_arsenal=true AND art_activado=true
		ORDER BY art_glosa,forma_nombre
		");
		
		
		print("
			<tr class='tabla_header'>
				<td colspan=4>Detalle Art&iacute;culos <b>NO Utilizados</b></td>
			</tr>
			<tr class='tabla_header'>
				<td>N &ordm;</td>
				<td>C&oacute;digo</td>
				<td>Medicamento</td>
				<td>Formato</td>
			</tr>"); 
	
	for($i=0;$i<sizeof($nousados);$i++){
	
		($i%2==1)   ?   $clase='tabla_fila'   : $clase='tabla_fila2';
	
		print("
			<tr class=".$clase.">
				<td>".($i+1)."</td>
				<td>".$nousados[$i]['art_codigo']."</td>
				<td>".htmlentities($nousados[$i]['art_glosa'])."</td>
				<td>".$nousados[$i]['forma_nombre']."</td>							
			</tr>
		");		
		
	}
	
	print("</table>");
?>