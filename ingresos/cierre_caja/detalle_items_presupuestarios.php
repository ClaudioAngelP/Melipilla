<?php 

	require_once("../../conectar_db.php");
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$funcs=$_POST['funcionarios']*1;

	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}

	
	if(isset($_POST['xls'])) {
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"detalle_items.xls\";");
		$xls=1; 
	} else 
		$xls=0;
	
	function dinero($num) {
		GLOBAL $xls;
		if(!$xls) return ('$'.number_format($num,0,',','.').'.-');
		else			return floor($num*1);
	}	
	
	function numero($num) {
		GLOBAL $xls;
		if(!$xls) return (number_format($num,0,',','.'));
		else			return floor($num*1);
	}	
	
	
	if($funcs!=-1) {
		$func_w='func_id='.$funcs;
	} else {
		$func_w='true';
	}	
	
	$l=cargar_registros_obj("
		SELECT * FROM boletin_detalle
		JOIN boletines USING (bolnum)
		JOIN pacientes USING (pac_id)
		LEFT JOIN codigos_prestacion_item ON bdet_codigo=codigo AND (bolmod=modalidad OR modalidad='mixto')
		WHERE bolfec::date >= '$fecha1' AND 
		bolfec::date <= '$fecha2' AND $func_w
		ORDER BY bolnum
	", true);


?>


<table style='width:100%;'>
<tr class='tabla_header'>
<td>Nro.</td>
<td>Fecha</td>
<td>RUN</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<td>C&oacute;digo</td>
<td>Item Presupuestario</td>
<td>Cantidad</td>
<td>Precio $</td>
<td>Copago $</td>
</tr>

<?php 

	$total=0; $copago=0;

	if($l)
	for($i=0;$i<sizeof($l);$i++) {

		$clase=$i%2==0?'tabla_fila':'tabla_fila2';

		print("<tr class='$clase'>
		<td style='text-align:center;font-weight:bold;'>".$l[$i]['bolnum']."</td>
		<td style='text-align:center;'>".substr($l[$i]['bolfec'],0,10)."</td>
		<td style='text-align:right;'>".$l[$i]['pac_rut']."</td>
		<td style='text-align:left;'>".$l[$i]['pac_appat']."</td>
		<td style='text-align:left;'>".$l[$i]['pac_apmat']."</td>
		<td style='text-align:left;'>".$l[$i]['pac_nombres']."</td>
		<td style='text-align:center;'>".$l[$i]['item_codigo']."</td>
		<td style='text-align:left;'>".$l[$i]['item_nombre']."</td>
		<td style='text-align:right;'>".numero($l[$i]['bdet_cantidad'])."</td>
		<td style='text-align:right;'>".numero($l[$i]['bdet_valor_total'])."</td>
		<td style='text-align:right;'>".numero($l[$i]['bdet_valor'])."</td>
		</tr>");

		$total+=$l[$i]['bdet_valor_total']*1;
		$copago+=$l[$i]['bdet_valor']*1;

	}

	print("<tr class='tabla_header'><td colspan=9 style='text-align:right;'>Totales:</td><td style='text-align:right;'>".numero($total)."</td><td style='text-align:right;'>".numero($copago)."</td></tr>");


?>

</table>
