<?php 

	require_once('../../conectar_db.php');
	
function sanear_string($string)
{
 
    $string = trim($string);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C',),
        $string
    );
 
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "<", ";", ",", ":",
             ".", " "),
        '',
        $string
    );
 
 
    return $string;
}

	
	$bod_id=$_POST['bod_id']*1;
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	
	$centro=pg_escape_string($_POST['centro_ruta']);
	
	$cen=cargar_registro("SELECT * FROM centro_costo WHERE centro_ruta='$centro'");
	$cen_file=sanear_string(utf8_decode($cen['centro_nombre']));
	
	
		
	$valorizado=isset($_POST['valorizado']);

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
		header("Content-Disposition: filename=\"Gastos_".$b['bod_glosa']."_Centro_costo_$cen_file.xls\";");
		
		print("
		<table>
			<tr>
				<td colspan=4><b>Informe de Gastos por Centro de Costo</b></td>
			</tr>
			<tr>
				<td colspan=2>Bodega:</td>
				<td>".$b['bod_glosa']."</td>
			</tr>
			<tr>
				<td colspan=2>Centro de Costo:</td>
				<td>".htmlentities($cen['centro_nombre'])."</td>
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

	if(!$valorizado)
		$total='SUM(-stock_cant) AS cantidad,SUM((-stock_cant)*art_val_ult)';
	else
		$total='SUM(-stock_cant) AS cantidad,SUM((-stock_cant)*art_val_ult)';
		
	if($centro=='') 
		$centro_w='true';
	else
		$centro_w="(c1.centro_ruta='$centro' OR c2.centro_ruta='$centro' OR c3.centro_ruta='$centro')";
	
	$q=pg_query("

		SELECT mes, anio, centro_ruta, centro_nombre, art_codigo, art_glosa, art_item, art_val_ult, upper(item_glosa) AS item_glosa, $total AS total
		 FROM (
		SELECT 
		COALESCE(c1.centro_ruta, c2.centro_ruta, c3.centro_ruta, '...' || bod_id) AS centro_ruta, 
		COALESCE(c1.centro_nombre, c2.centro_nombre, c3.centro_nombre, bod_glosa) AS centro_nombre, 
		stock_art_id, 
		stock_cant ,extract('month' from log_fecha)AS mes,extract('year' from log_fecha)AS anio
		FROM stock
		JOIN logs ON stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN bodega ON bod_id=origen_bod_id
		LEFT JOIN cargo_centro_costo USING (log_id)
		LEFT JOIN centro_costo AS c1 ON cargo_centro_costo.centro_ruta=c1.centro_ruta
		LEFT JOIN centro_costo AS c2 ON origen_centro_ruta=c2.centro_ruta
		LEFT JOIN recetas_detalle ON log_recetad_id=recetad_id
		LEFT JOIN receta ON recetad_receta_id=receta_id
		LEFT JOIN centro_costo AS c3 ON receta_centro_ruta=c3.centro_ruta
		LEFT JOIN pedido_log_rev USING (log_id)
		WHERE stock_bod_id=$bod_id AND 
		stock_cant<0 AND 
		log_tipo IN (2,9,15,18) AND 
		log_fecha BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59' AND $centro_w
		) AS foo
		JOIN articulo ON stock_art_id=art_id
		LEFT JOIN item_presupuestario ON art_item=item_codigo
		
		GROUP BY centro_ruta, centro_nombre, art_codigo, art_glosa, art_item, art_val_ult, item_glosa,mes,anio
		
		ORDER BY mes,art_glosa, centro_ruta;
		
	");
	
	$datos=Array();
	$datos2=Array();
	$arts=Array();
	$centros=Array();
	$meses=array();
	
	$total_arts=Array();
	$total_centros=Array();
	$total_meses=Array();


	while($r=pg_fetch_assoc($q)) {
		
		$arts[$r['art_codigo']]=$r['art_glosa'].'|'.$r['art_item'].'|'.$r['item_glosa'].'|'.$r['art_val_ult'];
		$centros[$r['centro_ruta']]=$r['centro_nombre'];
		
		if(!isset($datos[$r['art_codigo']])) {
			$datos[$r['art_codigo']]=Array();
		}
		
		if(!isset($datos[$r['art_codigo']][$r['centro_ruta']])) {
			$datos[$r['art_codigo']][$r['centro_ruta']]=$r['cantidad']*1;
		} else {
			$datos[$r['art_codigo']][$r['centro_ruta']]+=$r['cantidad']*1;
		}
		
		if(!isset($datos2[$r['art_codigo']][$r['centro_ruta']][$r['mes'].'|'.$r['anio']])) {
			$datos2[$r['art_codigo']][$r['centro_ruta']][$r['mes'].'|'.$r['anio']]=$r['cantidad']*1;
		}
		
		if(!isset($total_arts[$r['art_codigo']]))
			$total_arts[$r['art_codigo']]=0;
			
		if(!isset($total_centros[$r['centro_ruta']]))
			$total_centros[$r['centro_ruta']]=0;

		if(!isset($total_meses[$r['mes'].'|'.$r['anio']]))
			$total_meses[$r['mes'].'|'.$r['anio']]=0;
		
		$total_arts[$r['art_codigo']]+=$r['total'];
		$total_centros[$r['centro_ruta']]+=$r['total'];
		$total_meses[$r['mes'].'|'.$r['anio']]+=$r['total'];

	}
	
	ksort($centros);

echo $c_nombre;
	
?>

<table style='width:100%;'>
<tr class='tabla_header'>


<td>C&oacute;digo</td>
<td style='width:250px;'>Art&iacute;culo</td>
<td>Item</td>
<td>Descripci&oacute;n</td>
<td>Cantidad</td>

<?php 

	foreach($total_meses AS $key => $val) {
		print("<td style='min-width:150px;'>".htmlentities($key)."</td>");
	}

?>

<td>P. Unit. $</td>
<td style='min-width:150px;'>Total</td>

</tr>

<?php 

	$c=0;

	foreach($arts AS $cod => $txt) {
		
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
		
		list($glosa, $item, $nombre_item, $punit)=explode('|', $txt);
		
		print("<tr class='$clase'>
		<td style='text-align:right;font-weight:bold;'>".$cod."</td>
		<td style='font-size:9px;'>".htmlentities($glosa)."</td>
		<td style='font-size:9px;'>".htmlentities($item)."</td>
		<td style='font-size:9px;'>".htmlentities($nombre_item)."</td>
		");
		
		foreach($centros AS $ruta => $val) {
		
			if(isset($datos[$cod][$ruta])) {
				if(!$valorizado)
					print("<td style='text-align:right;'>".number_format2($datos[$cod][$ruta]*1,0,',','.')."</td>");
				else
					print("<td style='text-align:right;'>".number_format2($datos[$cod][$ruta]*1,0,',','.')."</td>");
						
			} else {
				if(!$valorizado)
					print("<td style='text-align:right;'>".number_format2(0,0,',','.')."</td>");
				else
					print("<td style='text-align:right;'>".number_format2(0,0,',','.')."</td>");
			}
			
			foreach($total_meses AS $mes_anio => $val2) {
			
				if(isset($datos2[$cod][$ruta][$mes_anio])) {
				
					if(!$valorizado)
						print("<td style='text-align:right;'>".number_format2($datos2[$cod][$ruta][$mes_anio]*1,0,',','.')."</td>");
					else
						print("<td style='text-align:right;'>".number_format2($datos2[$cod][$ruta][$mes_anio]*1,0,',','.')."</td>");
					
				} else {
					
					if(!$valorizado)
						print("<td style='text-align:right;'>".number_format2(0,0,',','.')."</td>");
					else
						print("<td style='text-align:right;'>".number_format2(0,0,',','.')."</td>");
				
				}
			
			}			
		
		}
		
	
		print("<td style='text-align:right;font-weight:bold;'>".number_format3($punit*1,0,',','.')."</td>");
		print("<td style='text-align:right;font-weight:bold;'>".number_format3($total_arts[$cod]*1,0,',','.')."</td>");
		
		print("</tr>");
		
		$c++;
		
	}


?>


</table>
	<tr>
		<td> </td>
	</tr>
</table>
