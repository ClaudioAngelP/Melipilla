<?php 

	require_once('../../conectar_db.php');
	
	$bod_id=$_POST['bod_id']*1;
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	
	$xls=isset($_POST['xls']);
	
	function number_format2($num, $dig, $c, $p) {
		GLOBAL $xls;
		if(!$xls)
			return number_format($num, $dig, $c, $p);
		else
			return number_format($num, $dig,'.','');
	}

	function number_format3($num, $dig, $c, $p) {
		GLOBAL $xls;
		if(!$xls)
			return '$'.number_format($num, $dig, $c, $p).'.-';
		else
			return number_format($num, $dig,'.','');
	}


	if(isset($_POST['xls'])) {
	
		$b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id");

		if($bod_id==-2) {

			$b['bod_glosa']='Centro de Costo Farmacias';

		}
		
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: filename=\"ConsumoMensual_".$b['bod_glosa'].".xls\";");
		
		print("
		<table>
			<tr>
				<td colspan=4><b>Consumo Mensual de Productos</b></td>
			</tr>
			<tr>
				<td colspan=2>Bodega:</td>
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

	if($bod_id==-2) {
		$bod_id='2,3,4,50,23,16,10';
	}

	$q=pg_query("

select art_codigo, art_glosa, mes, sum(-stock_cant) as cantidad, art_val_ult FROM (
select stock_art_id, to_char(log_fecha, 'MM-YYYY') AS mes, stock_cant from logs
join stock on stock_log_id=log_id
where 
log_fecha::date between '$fecha1' and '$fecha2' AND log_tipo IN (2,9,15,16,17,18) AND stock_bod_id IN ($bod_id) AND stock_cant<0
) AS foo 
JOIN articulo ON stock_art_id=art_id
GROUP BY art_codigo, art_glosa, mes, art_val_ult
ORDER BY art_codigo, mes;		

		
	");
	
	$arts=Array();
	$meses=array();
	$datos=Array();
	
	$total_cant=Array();
	$total_general=0;


	while($r=pg_fetch_assoc($q)) {
		
		$arts[$r['art_codigo']]=$r['art_glosa'].'|'.$r['art_val_ult'];
		$meses[$r['mes']]=1;
		
		if(!isset($datos[$r['art_codigo']])) {
			$datos[$r['art_codigo']]=Array();
		}
		
		if(!isset($datos[$r['art_codigo']][$r['mes']])) {
			$datos[$r['art_codigo']][$r['mes']]=$r['cantidad']*1;
		} else {
			$datos[$r['art_codigo']][$r['mes']]+=$r['cantidad']*1;
		}
		
		if(!isset($total_arts[$r['art_codigo']]))
			$total_arts[$r['art_codigo']]=0;
			
		$total_cant[$r['art_codigo']]+=$r['cantidad'];
		$total_general+=$r['cantidad']*$r['art_val_ult'];

	}
	
	ksort($arts); ksort($meses);

?>

<table style='width:100%;'>
<tr class='tabla_header'>


<td>C&oacute;digo</td>
<td style='width:250px;'>Art&iacute;culo</td>

<?php 

	foreach($meses AS $key => $val) {
		print("<td style='min-width:150px;'>".htmlentities($key)."</td>");
	}

?>

<td>Consumo</td>
<td>Promedio</td>
<td>P. Unit. $</td>
<td style='min-width:150px;'>Total $</td>

</tr>

<?php 

	$c=0;

	foreach($arts AS $cod => $txt) {
		
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
		
		list($glosa, $punit)=explode('|', $txt);
		
		print("<tr class='$clase'>
		<td style='text-align:right;font-weight:bold;'>".$cod."</td>
		<td style='font-size:9px;'>".htmlentities($glosa)."</td>
		");
		
		foreach($meses AS $mes => $nada) {
		
			if(isset($datos[$cod][$mes])) {
					print("<td style='text-align:right;'>".number_format2($datos[$cod][$mes]*1,0,',','.')."</td>");
			} else {
					print("<td style='text-align:right;'>&nbsp;</td>");
			}
			
		}

		print("<td style='text-align:right;font-weight:bold;'>".number_format2($total_cant[$cod]*1,0,',','.')."</td>");
		print("<td style='text-align:right;font-weight:bold;'>".number_format2($total_cant[$cod]/sizeof($meses),2,',','.')."</td>");
	
		print("<td style='text-align:right;font-weight:bold;'>".number_format3($punit*1,0,',','.')."</td>");
		print("<td style='text-align:right;font-weight:bold;'>".number_format3($total_cant[$cod]*$punit,0,',','.')."</td>");
		
		print("</tr>");
		
		$c++;
		
	}

	$colspan=5+sizeof($meses);

	print("<tr class='tabla_header'><td colspan=$colspan style='text-align:right;font-size:14px;'>Total General:</td><td style='font-weight:bold;text-align:right;font-size:14px;'>".number_format3($total_general,0,',','.')."</td></tr>");


?>

</table>
