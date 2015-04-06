<?php 

	require_once('../conectar_db.php');

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

	
	$bolnum=$_GET['bolnum']*1;
	
	$r=cargar_registro("SELECT *, date_trunc('second',bolfec) AS bolfec FROM boletines 
	LEFT JOIN funcionario USING (func_id) 	
	LEFT JOIN pacientes USING (pac_id)
	WHERE bolnum=".$bolnum, true);

	$a=cargar_registros_obj("SELECT * FROM boletines WHERE bolnumx=".$bolnum);

	$det=cargar_registros_obj("SELECT * FROM boletin_detalle
									JOIN prestacion ON bdet_presta_id=presta_id
									LEFT JOIN codigos_prestacion ON presta_codigo_v=codigo
		 								WHERE bolnum=".$bolnum);

	$chq=cargar_registros_obj("SELECT * FROM cheques WHERE bolnum=".$bolnum);
	$pag=cargar_registros_obj("SELECT * FROM forma_pago 
		JOIN tipo_formas_pago ON fpago_id=tipo	
	WHERE bolnum=".$bolnum);

?>

<html>
<title>Visualizar Comprobante de Pago</title>

<?php cabecera_popup('..'); ?>

<script>

abrir_credito=function(crecod) {
	window.open('visualizar_credito.php?crecod='+crecod,'_self');
}

eliminar_boletin=function(bolnum) {
	window.open('eliminar_credito.php?bolnum='+bolnum,'_self');
	//window.open('../ingresos/imprimir_boletin.php?bolnum='+bolnum,'_blank');
}

imprimir_boletin=function(bolnum) {
	window.open('../ingresos/imprimir_boletin.php?bolnum='+bolnum,'_blank');
}

</script>

<body class='fuente_por_defecto popup_background'>

<div class="sub-content">
<img src="../iconos/table.png">
<b>Datos del Comprobante de Pago <u>#<?php echo number_format($bolnum, 0,',','.'); ?></u></b>
</div>

<div class='sub-content'>

<table style='width:100%;font-size:14px;'>
<tr>
<td style='text-align:right;width:150px;'>Fecha Emisi&oacute;n:</td>
<td><?php echo $r['bolfec']; ?></td>
</tr>
<tr>
<td style='text-align:right;width:150px;'>R.U.N. Paciente:</td>
<td style='font-weight:bold;'><?php echo formato_rut($r['pac_rut']); ?></td>
</tr>
<tr>
<td style='text-align:right;width:150px;'>Nombre Completo:</td>
<td style='font-weight:bold;'><?php echo ucwords($r['pac_nombres'].' '. $r['pac_appat'].' '. $r['pac_apmat']); ?></td>
</tr>
<tr>
<td style='text-align:right;'>Monto:</td>
<td style='font-weight:bold;'>$ <?php echo number_format($r['bolmon'],0,',','.'); ?>.-</td>
</tr>

<?php if($r['crecod']*1!=0) { ?>
<tr>
<td style='text-align:right;'>Cr&eacute;dito:</td>
<td>#<?php echo $r['crecod']; ?>
<img src="../iconos/magnifier.png" style="width:12px;height:12px;cursor:pointer;" onClick="abrir_credito(<?php echo $r['crecod']; ?>,'../');" /></td>
</tr>
<?php } ?>

<tr>
<td style='text-align:right;'>Funcionario Emisor:</td>
<td style=''><i><?php echo htmlentities($r['func_rut']).' <b>'.htmlentities($r['func_nombre']).'</b>'; ?></i></td>
</tr>

<?php if($r['anulacion']!='') { ?>
<tr>
<td style='text-align:right;'>Anulada:</td>
<td style='font-weight:bold;color:red;'><?php echo $r['anulacion']; ?></td>
</tr>
<?php } ?>

<?php if($a) { ?>
<tr>
<td style='text-align:right;' valign='top'>Anula los Boletines:</td>
<td>
	<?php 
		for($i=0;$i<sizeof($a);$i++)
			echo vboletin($a[$i]['bolnum'],0,'../').'<br />';	
	?>
</td>
</tr>
<?php } ?>

<?php if($r['saldof']*1>0) { ?>
<tr>
<td style='text-align:right;'>Saldo a Favor Cliente:</td>
<td style='font-weight:bold;'>$ <?php echo number_format($r['saldof'],0,',','.'); ?>.-</td>
</tr>
<?php } ?>

</table>
</div>

<?php if($chq) { ?>

<div class='sub-content'>
<img src='../iconos/vcard.png'>
<b>Pago con Cheque(s)</b>
</div>

<div class='sub-content2'>

<table style='width:100%;font-size:11px;'>
<tr class="tabla_header" style='font-weight:bold;'>
<td>Fecha</td>
<td>Banco</td>
<td>RUT</td>
<td>Nombre</td>
<td>Serie</td>
<td>Monto</td>
</tr>


<?php 

	$total=0;

	for($i=0;$i<sizeof($chq);$i++) {
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
	
		print("
		<tr class='$clase'>
		<td style='text-align:center;'>".($chq[$i]['fecha'])."</td>
		<td style='text-align:center;'>".($chq[$i]['banco'])."</td>
		<td style='text-align:right;'>".($chq[$i]['rut'])."</td>
		<td style='text-align:left;'>".($chq[$i]['nombre'])."</td>
		<td style='text-align:left;'>".($chq[$i]['serie'])."</td>
		<td style='text-align:right;font-weight:bold;'>".dinero($chq[$i]['monto'])."</td>
		</tr>		
		");
	
		$total+=$chq[$i]['monto']*1;

	}

	echo "<tr class='tabla_header'><td colspan=5 style='text-align:right;'>Total Cheques:</td>
			<td style='text-align:right;font-weight:bold;'>$ ".number_format($total,0,',','.').".-</td></tr>";
	
?>



</table>

</div>

<?php } ?>


<?php if($pag) { ?>

<div class='sub-content'>
<img src='../iconos/vcard.png'>
<b>Otras Formas de Pago</b>
</div>

<div class='sub-content2'>

<table style='width:100%;font-size:11px;'>
<tr class="tabla_header" style='font-weight:bold;'>
<td>Tipo</td>
<td>N&uacute;mero</td>
<td>Fecha</td>
<td>Monto</td>
</tr>


<?php 

	$total=0;

	for($i=0;$i<sizeof($pag);$i++) {
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
	
		print("
		<tr class='$clase'>
		<td style='text-align:center;'>".($pag[$i]['fpago_nombre'])."</td>
		<td style='text-align:center;'>".($pag[$i]['numero'])."</td>
		<td style='text-align:center;'>".($pag[$i]['fecha'])."</td>
		<td style='text-align:right;font-weight:bold;'>".dinero($pag[$i]['monto'])."</td>
		</tr>		
		");
	
		$total+=$pag[$i]['monto']*1;

	}

	echo "<tr class='tabla_header'><td colspan=3 style='text-align:right;'>Total Otras Formas de Pago:</td>
			<td style='text-align:right;font-weight:bold;'>$ ".number_format($total,0,',','.').".-</td></tr>";
	
?>

</table>

</div>

<?php } ?>

<center>
<?php if(_cax(302) AND $r['anulacion']=='') { ?>
<input type='button' value='--- Anulaci&oacute;n de Comprobante ... ---' 
onClick='eliminar_boletin(<?php echo $bolnum; ?>);' />
<?php } ?>
<input type='button' value='--- Imprimir Bolet&iacute;n... ---' 
onClick='imprimir_boletin(<?php echo $bolnum; ?>);' />
<br/><br/>
</center>

</body>
</html>
