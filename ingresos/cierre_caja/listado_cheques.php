<?php 

	require_once("../../conectar_db.php");
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	
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
    	header("Content-Disposition: filename=\"listado_cheques.xls\";");
		$xls=1; 
	} else 
		$xls=0;
	
	function dinero($num) {
		GLOBAL $xls;
		if(!$xls) return ('$ '.number_format($num,0,',','.').'.-');
		else			return floor($num*1);
	}	
	
	function numero($num) {
		GLOBAL $xls;
		if(!$xls) return (number_format($num,0,',','.'));
		else			return floor($num*1);
	}	
	
	
	$l=cargar_registros_obj("
		SELECT *  
		FROM cheques 
		WHERE fecha::date >= '$fecha1' AND fecha::date <= '$fecha2'	
	");
	
?>

<table style='width:100%;font-size:12px;'>
<tr class="tabla_header" style='font-weight:bold;'>
<td>Nro. Bolet&iacute;n</td>
<td>Fecha</td>
<td>Banco</td>
<td>RUT</td>
<td>Nombre</td>
<td>Serie</td>
<td>Monto</td>
</tr>

<?php 

	$total=0;

	for($i=0;$i<sizeof($l);$i++) {
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
	
		print("
		<tr class='$clase'>
		<td style='text-align:center;'>".vboletin($l[$i]['bolnum'], $xls)."</td>
		<td style='text-align:center;'>".($l[$i]['fecha'])."</td>
		<td style='text-align:center;'>".($l[$i]['banco'])."</td>
		<td style='text-align:right;'>".($l[$i]['rut'])."</td>
		<td style='text-align:left;'>".($l[$i]['nombre'])."</td>
		<td style='text-align:left;'>".($l[$i]['serie'])."</td>
		<td style='text-align:right;font-weight:bold;'>".dinero($l[$i]['monto'])."</td>
		</tr>		
		");
	
		$total+=$l[$i]['monto']*1;
		
	
	}

	print("
	<tr class='tabla_header' style='font-weight:bold;'>
	<td style='text-align:right;' colspan=6>Total:</td>
	<td style='text-align:right;'>".dinero($total)."</td>	
	</tr>	
	
	");

?>

</table>

