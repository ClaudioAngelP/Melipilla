<?php 

	require_once("../../conectar_db.php");
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$funcs=$_POST['funcionarios']*1;

	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;white-space:nowrap;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}

	
	if(isset($_POST['xls'])) {
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"informe_caja.xls\";");
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
		$func_w='boletines.func_id='.$funcs;
	} else {
		$func_w='true';
	}	
	
	$l=cargar_registros_obj("
		SELECT *, bolfec::date AS bolfec, (
			SELECT SUM(monto) FROM cheques
			WHERE cheques.bolnum=boletines.bolnum		
		) AS cheques, 
		array(
			SELECT (
			SELECT COALESCE(SUM(monto),0) AS monto FROM forma_pago
			WHERE forma_pago.bolnum=boletines.bolnum
			AND forma_pago.tipo=fpago_id
			) FROM tipo_formas_pago ORDER BY fpago_id
		) AS formas_pago,
		(SELECT count(*) FROM seguros WHERE seguros.bolnum=boletines.bolnum) AS nro_seguros
		FROM boletines 
		LEFT JOIN apertura_cajas ON bolfec BETWEEN ac_fecha_apertura AND COALESCE(ac_fecha_cierre, CURRENT_TIMESTAMP) AND apertura_cajas.func_id=boletines.func_id
		WHERE bolfec::date >= '$fecha1' AND 
		bolfec::date <= '$fecha2' AND $func_w
		ORDER BY bolnum	
	");
	
?>

<table style='width:100%;'>
<tr class='tabla_header'><td><b><u>Totales Generales</u></b></td></tr>
</table>

<table style='width:100%;font-size:12px;'>
<tr class="tabla_header" style='font-weight:bold;'>
<td>Fecha</td>
<td>CC</td>
<td>Nro. Doc.</td>
<td>Efectivo</td>
<td>Cheques</td>

<?php 
	$fp=cargar_registros_obj("SELECT * FROM tipo_formas_pago ORDER BY fpago_id");

	$tpag=array();
	
	for($i=0;$i<sizeof($fp);$i++) {
		echo '<td>'.htmlentities($fp[$i]['fpago_nombre']).'</td>';
		$tpag[$i]=0;
	}
?>

<td>Seguros</td>
<td>Sub Total</td>
</tr>

<?php 

	$efectivo=0;$cheques=0;$total=0;$seguros=0;

	if($l)
	for($i=0;$i<sizeof($l);$i++) {

		$fpag=explode( ',', substr($l[$i]['formas_pago'],1,-1) );

		$ofp=0;
		for($k=0;$k<sizeof($fp);$k++) $ofp+=$fpag[$k]*1;
			
		$befectivo=$l[$i]['bolmon']-$l[$i]['cheques']-$ofp;	
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
		
		if($l[$i]['anulacion']=='')
			$color='';
		else
			$color='red;text-decoration:line-through';
	
		print("
			<tr class='$clase' style='color:$color'>
			<td style='text-align:center;'>".$l[$i]['bolfec']."</td>
			<td style='text-align:center;'>".$l[$i]['ac_id']."</td>
			<td style='text-align:center;'>".vboletin($l[$i]['bolnum'], $xls)."</td>
			<td style='text-align:right;'>".dinero($befectivo,0,',','.')."</td>
			<td style='text-align:right;'>".dinero($l[$i]['cheques'])."</td>
		");

		for($k=0;$k<sizeof($fp);$k++) {
			print("<td style='text-align:right;'>".dinero($fpag[$k])."</td>");
			if($l[$i]['anulacion']=='')
				$tpag[$k]+=$fpag[$k]*1;
		}
		
		print("
			<td style='text-align:center;'>".($l[$i]['nro_seguros'])."</td>
			<td style='text-align:right;font-weight:bold;'>".dinero($l[$i]['bolmon'])."</td>
			</tr>		
		");
	
		if($l[$i]['anulacion']=='') {
		
			$seguros+=$l[$i]['nro_seguros'];
			$efectivo+=$befectivo;
			$cheques+=$l[$i]['cheques']*1;
			$total+=$l[$i]['bolmon']*1;
		
		}
		
	
	}

	print("
	<tr class='tabla_header' style='font-weight:bold;'>
	<td style='text-align:right;' colspan=3>Totales:</td>
	<td style='text-align:right;'>".dinero($efectivo)."</td>	
	<td style='text-align:right;'>".dinero($cheques)."</td>	
	");

	for($i=0;$i<sizeof($fp);$i++)
		print("<td style='text-align:right;'>".dinero($tpag[$i])."</td>");


	print("
		<td style='text-align:center;'>".($seguros)."</td>	
		<td style='text-align:right;'>".dinero($total)."</td>	
	</tr>	
	
	");

?>

</table>

<table style='width:100%;'>
<tr class='tabla_header'><td><b><u>Cr&eacute;ditos Nuevos</u></b></td></tr>
</table>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>Bolet&iacute;n</td>
<td>RUT Cliente</td>
<td>Nombre Cliente</td>
<td>Monto Pi&eacute;</td>
<td>Monto Cr&eacute;dito</td>
</tr>

<?php 

	$cn=cargar_registros_obj("
		SELECT * FROM boletines
		JOIN creditos USING (crecod)
		JOIN pacientes ON creditos.pac_id=pacientes.pac_id 
		WHERE bolfec::date >= '$fecha1' AND 
		bolfec::date <= '$fecha2' AND $func_w AND
		bolnum=(SELECT bolnum FROM cuotas 
		WHERE cuotas.crecod=creditos.crecod AND cuonum=0)
		ORDER BY bolnum
	");

	$total=0;

	if($cn)
		for($i=0;$i<sizeof($cn);$i++) {
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
	
			print("
				<tr class='$clase'>
				<td style='text-align:center;' align='center'>".vboletin($cn[$i]['bolnum'])."</td>
				<td style='text-align:right;' align='right'>".$cn[$i]['pac_rut']."</td>
				<td style='text-align:left;' align='left'>".htmlentities(($cn[$i]['pac_appat'].' '.$cn[$i]['pac_apmat'].' '.$cn[$i]['pac_nombres']))."</td>
				<td style='text-align:right;' align='right'>".dinero($cn[$i]['crepie'])."</td>
				<td style='text-align:right;' align='right'>".dinero($cn[$i]['cretot'])."</td>
				</tr>
			");
			
			$total+=$cn[$i]['cretot'];			
			
		}
		
	echo "<tr class='tabla_header'><td colspan=4 align='right'>Total:</td><td align='right'>".dinero($total)."</td></tr>";

?>

</table>

<table style='width:100%;'>
<tr class='tabla_header'><td><b><u>Recuperaci&oacute;n de Cr&eacute;ditos</u></b></td></tr>
</table>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>Bolet&iacute;n</td>
<td>RUT Cliente</td>
<td>Nombre Cliente</td>
<td>Total Cr&eacute;dito</td>
<td>Monto Pag. Cr&eacute;dito</td>
<td>%</td>
<td>Monto Pagado</td>
</tr>

<?php 

	if($funcs!=-1) {
                $func_w='func_id='.$funcs;
        } else {
                $func_w='true';
        }

	$cn=cargar_registros_obj("
		SELECT *,
		(SELECT SUM(bolmon) FROM boletines 
		WHERE boletines.crecod=b1.crecod AND boletines.bolfec<=b1.bolfec) AS pagado
		FROM boletines AS b1
		JOIN creditos USING (crecod)
		JOIN pacientes ON creditos.pac_id=pacientes.pac_id 
		WHERE bolfec::date >= '$fecha1' AND 
		bolfec::date <= '$fecha2' AND $func_w AND
		NOT bolnum=(SELECT bolnum FROM cuotas 
		WHERE cuotas.crecod=creditos.crecod AND cuonum=0)
		ORDER BY bolnum
	");

	$total=0;

	if($cn)
		for($i=0;$i<sizeof($cn);$i++) {
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
	
			print("
			<tr class='$clase'>
				<td style='text-align:center;' align='center'>".vboletin($cn[$i]['bolnum'])."</td>
				<td style='text-align:right;' align='right'>".$cn[$i]['pac_rut']."</td>
				<td style='text-align:left;' align='left'>".htmlentities(($cn[$i]['pac_appat'].' '.$cn[$i]['pac_apmat'].' '.$cn[$i]['pac_nombres']))."</td>
				<td style='text-align:right;' align='right'>".dinero($cn[$i]['cretot'])."</td>
				<td style='text-align:right;' align='right'>".dinero($cn[$i]['pagado']-$cn[$i]['crepie'])."</td>
				<td style='text-align:center;' align='center'>".number_format(($cn[$i]['pagado']-$cn[$i]['crepie'])*100/$cn[$i]['cretot'],0,',','.')." %</td>
				<td style='text-align:right;' align='right'>".dinero($cn[$i]['bolmon'])."</td>
			</tr>
			");
			
			$total+=$cn[$i]['bolmon'];			
			
		}
		
	echo "<tr class='tabla_header'><td colspan=6 align='right'>Total:</td><td align='right'>".dinero($total)."</td></tr>";

?>


</table>

