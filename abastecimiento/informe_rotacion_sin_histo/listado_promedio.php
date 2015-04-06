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
				<td colspan=4><b>Informe Promedio de Saldos de Art&iacute;culos</b></td>
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
		JOIN articulo_bodega ON artb_art_id=stock_art_id AND artb_bod_id=stock_bod_id
		WHERE stock_bod_id=$bod_id --(pedido.pedido_id IS NULL OR pedido_log_rev.pedidolog_id IS NOT NULL) AND stock_bod_id=$bod_id
		GROUP BY stock_art_id
		) AS foo
		JOIN articulo ON stock_art_id=art_id
		JOIN articulo_bodega ON artb_art_id=art_id AND artb_bod_id=$bod_id
		WHERE art_activado
		ORDER BY art_glosa;
	");

	$sinit=array();

	while($r=pg_fetch_assoc($inicial)) {
		$sinit[$r['stock_art_id']*1]['art_codigo']=$r['art_codigo'];
		$sinit[$r['stock_art_id']*1]['art_glosa']=$r['art_glosa'];
		$sinit[$r['stock_art_id']*1]['stock_inicial']=$r['stock_inicial']*1;
		$sinit[$r['stock_art_id']*1]['valor']=$r['art_val_ult'];
		$sinit[$r['stock_art_id']*1]['stock_inicial']=$r['_stock_inicial']*1;
		$sinit[$r['stock_art_id']*1]['_stock_inicial']=$r['stock_inicial']*1;
	}

	$salidas=pg_query("
		SELECT *, (_total_salidas*art_val_ult) AS total_salidas, _total_salidas AS cant_salidas FROM (
		SELECT stock_art_id, SUM(stock_cant) AS _total_salidas FROM stock
		JOIN logs ON log_fecha >= '$fecha1' AND log_fecha <= '$fecha2' AND
		log_tipo IN (2,9,15,17,18) AND stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
		WHERE stock_cant<0 AND stock_bod_id=$bod_id
		GROUP BY stock_art_id
		) AS foo JOIN articulo ON stock_art_id=art_id;
	");

	$out=array();

	while($r=pg_fetch_assoc($salidas)) {
		$out[$r['stock_art_id']*1][0]=-($r['total_salidas']*1);
		$out[$r['stock_art_id']*1][1]=-($r['cant_salidas']*1);
	}


	$final=pg_query("
		SELECT *, (_stock_final*art_val_ult) AS stock_final FROM (
		SELECT stock_art_id, SUM(stock_cant) AS _stock_final FROM stock
		JOIN logs ON log_fecha < '$fecha2' AND stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
		JOIN articulo_bodega ON artb_art_id=stock_art_id AND artb_bod_id=stock_bod_id
		WHERE stock_bod_id=$bod_id --(pedido.pedido_id IS NULL OR pedido_log_rev.pedidolog_id IS NOT NULL) AND stock_bod_id=$bod_id
		GROUP BY stock_art_id
		) AS foo 
		JOIN articulo ON stock_art_id=art_id
		JOIN articulo_bodega ON artb_art_id=art_id AND artb_bod_id=$bod_id
		WHERE art_activado
		ORDER BY art_glosa;
	");
	
	while($r=pg_fetch_assoc($final)) {
		
		if(!isset($sinit[$r['stock_art_id']*1])) {
			
			$sinit[$r['stock_art_id']*1]['art_codigo']=$r['art_codigo'];
			$sinit[$r['stock_art_id']*1]['art_glosa']=$r['art_glosa'];
			$sinit[$r['stock_art_id']*1]['valor']=$r['art_val_ult'];
			$sinit[$r['stock_art_id']*1]['stock_inicial']=0;
			$sinit[$r['stock_art_id']*1]['_stock_inicial']=0;
		
		}

		$sinit[$r['stock_art_id']*1]['stock_final']=$r['_stock_final']*1;
		$sinit[$r['stock_art_id']*1]['_stock_final']=$r['stock_final']*1;

	}

	
	
?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>C&oacute;digo Art.</td>
		<td>Descripci&oacute;n</td>
		<td>Saldo Inicial</td>
		<td>$ Inicial</td>
		<td>Saldo Final</td>
		<td>$ Final</td>
		<td>Salidas</td>
		<td>Promedio</td>
		<td>$ Promedio</td>
	</tr>

<?php

	$c=0; $total_inicial=0; $total_promedio=0; $total_final=0; $_total_inicial=0; $_total_promedio=0; $_total_final=0; $total_salidas=0; $num=0;

	foreach($sinit AS $art_id => $val) {
		
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
		
		if(isset($out[$art_id])) {
			$salidas=$out[$art_id][1];
		} else {
			$salidas=0;
		}

		print("
			<tr class='$clase'>
			<td style='text-align:right;font-weight:bold;font-size:11px;'>".$val['art_codigo']."</td>
			<td style='font-size:10px;'>".htmlentities($val['art_glosa'])."</td>
			<td style='text-align:right;'>".number_format2($val['stock_inicial'],0,',','.')."</td>
			<td style='text-align:right;'>".number_format3($val['_stock_inicial'],0,',','.')."</td>

			<td style='text-align:right;'>".number_format2($val['stock_final'],0,',','.')."</td>
			<td style='text-align:right;'>".number_format3($val['_stock_final'],0,',','.')."</td>

			<td style='text-align:right;'>".number_format2($salidas,0,',','.')."</td>

			<td style='text-align:right;font-weight:bold;'>".number_format2((($val['stock_inicial']+$val['stock_final'])/2),0,',','.')."</td>
			<td style='text-align:right;'>".number_format3((($val['stock_inicial']+$val['stock_final'])/2)*$val['valor'],0,',','.')."</td>

		");

		print("</tr>");
                $total_inicial += ($val['stock_inicial']);
                $total_final += ($val['stock_final']);
                $total_promedio += (($val['stock_inicial']+$val['stock_final'])/2);
                $total_salidas += $salidas;
                $_total_inicial += ($val['_stock_inicial']);
                $_total_final += ($val['_stock_final']);
                $_total_promedio += ((($val['stock_inicial']+$val['stock_final'])/2)*$val['valor']);
		$c++;

	}
   $clase=($c%2==0)?'tabla_fila':'tabla_fila2';
?>
<!-- EGF -->
<tr class='<?php echo $clase?>'>
  <td style='text-align:right;font-weight:bold;' colspan='2'>TOTAL</td>
  <td style='text-align:right;font-weight:bold;'><?php echo number_format2($total_inicial,0,',','.')?></td>
  <td style='text-align:right;font-weight:bold;'><?php echo number_format3($_total_inicial,0,',','.')?></td>
  <td style='text-align:right;font-weight:bold;'><?php echo number_format2($total_final,0,',','.')?></td>
  <td style='text-align:right;font-weight:bold;'><?php echo number_format3($_total_final,0,',','.')?></td>
  <td style='text-align:right;font-weight:bold;'><?php echo number_format3($total_salidas,0,',','.')?></td>
  <td style='text-align:right;font-weight:bold;'><?php echo number_format2($total_promedio,0,',','.')?></td>
  <td style='text-align:right;font-weight:bold;'><?php echo number_format3($_total_promedio,0,',','.')?></td>
</tr>
<?php
  $c++;
  $clase=($c%2==0)?'tabla_fila':'tabla_fila2';
?>
<tr class='<?php echo $clase?>'>
  <td style='text-align:right;font-weight:bold;font-size:15px' colspan='2'>INDICADOR</td>
  <td style='text-align:right;font-weight:bold;font-size:15px'><?php echo round(($total_salidas/$total_promedio),4)?></td>
</tr>
<!-- EGF -->

</table>
