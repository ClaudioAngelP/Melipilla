<?php 

	require_once('../../conectar_db.php');

	error_reporting(E_ALL);

	$bod_id=$_POST['bod_id']*1;
	
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
	
	if(isset($_POST['xls'])) {
		
		$b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id");
		
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: filename=\"InformeRotacion_".$b['bod_glosa'].".xls\";");
		
		print("
		<table>
			<tr>
				<td colspan=4><b>Informe de Rotaci&oacute;n de Art&iacute;culos</b></td>
			</tr>
			<tr>
				<td colspan=2>Ubicaci&oacute;n:</td>
				<td>".$b['bod_glosa']."</td>
			</tr>
			<tr>
				<td colspan=2>Fecha Inicial:</td>
				<td>".$fecha1."</td>
			</tr>
			<tr>
				<td colspan=2>Fecha Final:</td>
				<td>".$fecha2."</td>
			</tr>
		</table>
		");
		
		
	}
	
	$inicial=pg_query("
		SELECT *, (_stock_inicial*art_val_ult) AS stock_inicial FROM (
		SELECT stock_art_id, SUM(stock_cant) AS _stock_inicial FROM stock
		JOIN logs ON log_fecha < '$fecha1' AND stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
		LEFT JOIN pedido_log_rev USING (log_id)
		WHERE (pedido.pedido_id IS NULL OR pedido_log_rev.pedidolog_id IS NOT NULL) AND stock_bod_id=$bod_id
		GROUP BY stock_art_id
		) AS foo JOIN articulo ON stock_art_id=art_id
		WHERE art_activado
		ORDER BY art_glosa;
	");
	
	$sinit=array();
	
	while($r=pg_fetch_assoc($inicial)) {
		$sinit[$r['stock_art_id']*1]['art_codigo']=$r['art_codigo'];
		$sinit[$r['stock_art_id']*1]['art_glosa']=$r['art_glosa'];
		$sinit[$r['stock_art_id']*1]['stock_inicial']=$r['stock_inicial']*1;
		$sinit[$r['stock_art_id']*1]['valor']=$r['art_val_ult'];
		$sinit[$r['stock_art_id']*1]['stock']=$r['stock_inicial']*1;
		$sinit[$r['stock_art_id']*1]['stock2']=$r['_stock_inicial']*1;
		$sinit[$r['stock_art_id']*1]['sumatoria']=0;
		$sinit[$r['stock_art_id']*1]['sumatoria2']=0;
	}

	$salidas=pg_query("
		SELECT *, (_total_salidas*art_val_ult) AS total_salidas, _total_salidas AS cant_salidas FROM (
		SELECT stock_art_id, SUM(stock_cant) AS _total_salidas FROM stock
		JOIN logs ON log_fecha >= '$fecha1' AND log_fecha <= '$fecha2' AND 
		log_tipo IN (2,9,15,18) AND stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
		LEFT JOIN pedido_log_rev USING (log_id)
		WHERE stock_cant<0 AND stock_bod_id=$bod_id
		GROUP BY stock_art_id
		) AS foo JOIN articulo ON stock_art_id=art_id;
	");
	
	$out=array();
	
	while($r=pg_fetch_assoc($salidas)) {
		$out[$r['stock_art_id']*1][0]=-($r['total_salidas']*1);
		$out[$r['stock_art_id']*1][1]=-($r['cant_salidas']*1);
	}

	$mov=pg_query("
		SELECT *, (_mov_dia*art_val_ult) AS mov_dia FROM (
		SELECT log_fecha::date AS dia, stock_art_id, SUM(stock_cant) AS _mov_dia FROM stock
		JOIN logs ON log_fecha >= '$fecha1' AND log_fecha <= '$fecha2' AND stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_log_rev USING (log_id)
		WHERE stock_bod_id=$bod_id AND (pedido.pedido_id IS NULL OR pedido_log_rev.pedidolog_id IS NOT NULL)
		GROUP BY log_fecha::date, stock_art_id
		) AS foo JOIN articulo ON stock_art_id=art_id
		WHERE art_activado
		ORDER BY art_glosa;
	");

	$movs=array();
	$_movs=array();
	
	while($r=pg_fetch_assoc($mov)) {
		
		$art_id=$r['stock_art_id']*1;

		$movs[$art_id][$r['dia']]=$r['mov_dia'];
		$_movs[$art_id][$r['dia']]=$r['_mov_dia'];
		
		if(!isset($sinit[$art_id])) {
			$sinit[$r['stock_art_id']*1]['art_codigo']=$r['art_codigo'];
			$sinit[$r['stock_art_id']*1]['art_glosa']=$r['art_glosa'];
			$sinit[$r['stock_art_id']*1]['valor']=$r['art_val_ult'];
			$sinit[$r['stock_art_id']*1]['stock_inicial']=0;
			$sinit[$r['stock_art_id']*1]['stock']=0;
			$sinit[$r['stock_art_id']*1]['stock2']=0;
			$sinit[$r['stock_art_id']*1]['sumatoria']=0;
			$sinit[$r['stock_art_id']*1]['sumatoria2']=0;
		}
		
	}
	
	$f1=explode('/',$fecha1);
	$f2=explode('/',$fecha2);

	$t1=mktime(0,0,0,$f1[1],$f1[0],$f1[2]);
	$t2=mktime(0,0,0,$f2[1],$f2[0],$f2[2]);
	
	$dif_dias=(($t2-$t1)/86400)+1;
		
	for($i=0;$i<$dif_dias;$i++) {
		
		$dia=$t1+(86400*$i);
		$fec=date('d/m/Y', $dia);
		
		//print("DIA $fec <br>");
		
		foreach($sinit AS $art_id => $val) {
			
			if(isset($movs[$art_id][$fec]))
				$sinit[$art_id]['stock']+=$movs[$art_id][$fec];

			if(isset($_movs[$art_id][$fec]))
				$sinit[$art_id]['stock2']+=$_movs[$art_id][$fec];
				
			//print("MOVS ".$movs[$art_id][$fec]." <br>");
				
			$sinit[$art_id]['sumatoria']+=$sinit[$art_id]['stock'];
			$sinit[$art_id]['sumatoria2']+=$sinit[$art_id]['stock2'];

			//print("SUMA ".$sinit[$art_id]['sumatoria']." <br>");
			
		}
		
	}
	
	
?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>C&oacute;digo Art.</td>
		<td>Descripci&oacute;n</td>
		<td>Saldo Inicial</td>
		<td>Cant. Promedio</td>
		<td>Promedio $</td>
		<td>Cant. Salidas</td>
		<td>Salidas $</td>
		<td>P. Unit. $</td>
		<td>Indice Rot.</td>
	</tr>

<?php

	$c=0; $total_promedio=0; $total_salidas=0; $num=0;

	foreach($sinit AS $art_id => $val) {
		
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
		
		if($val['sumatoria']==0 AND $val['sumatoria2']==0) continue;
		
		print("
			<tr class='$clase'>
			<td style='text-align:right;font-weight:bold;font-size:11px;'>".$val['art_codigo']."</td>
			<td style='font-size:10px;'>".htmlentities($val['art_glosa'])."</td>
			<td style='text-align:right;'>".number_format3($val['stock_inicial'],0,',','.')."</td>
			<td style='text-align:right;font-weight:bold;'>".number_format2($val['sumatoria2']/$dif_dias,0,',','.')."</td>
			<td style='text-align:right;'>".number_format3($val['sumatoria']*1/$dif_dias,0,',','.')."</td>
		");
		
		$total_promedio+=($val['sumatoria']*1/$dif_dias);
			
		if(isset($out[$art_id])) {
			print("<td style='text-align:right;font-weight:bold;'>".number_format($out[$art_id][1],0,',','.')."</td>");
			print("<td style='text-align:right;'>".number_format3($out[$art_id][0],0,',','.')."</td>");
			$total_salidas+=$out[$art_id][0];
		} else {
			print("<td style='text-align:right;font-weight:bold;'>".number_format(0,0,',','.')."</td>");			
			print("<td style='text-align:right;'>".number_format3(0,0,',','.')."</td>");			
			$out[$art_id][0]=0;
			$out[$art_id][1]=0;
		}

		print("<td style='text-align:right;'>".number_format3($val['valor'],0,',','.')."</td>");
		
		if($val['sumatoria']!=0 OR $val['sumatoria2']!=0) {
			
			if($val['sumatoria']!=0)
				$indicador=($out[$art_id][0]/($val['sumatoria']/$dif_dias));
			else
				$indicador=($out[$art_id][1]/($val['sumatoria2']/$dif_dias));
			
			print("<td style='text-align:right;font-weight:bold;'>".number_format2($indicador,3,',','.')."</td>");
			$num++;
			
		} else {
			print("<td style='text-align:right;font-weight:bold;'>-1,000</td>");	
		}
		
		
		print("</tr>");
		
		$c++;
		
	}

?>

<tr class='tabla_header'><td colspan='8' style='text-align:right;'>
Indicador de Bodega:
</td><td style='text-align:right;font-weight:bold;'><?php echo number_format2($total_salidas/$total_promedio,6,',','.'); ?></td>
</tr>

</table>


