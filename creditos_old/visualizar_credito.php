<?php 

	require_once('../conectar_db.php');

	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}

	
	$crecod=$_GET['crecod']*1;
	
	$c=cargar_registro("SELECT *, crefec::date AS crefec FROM creditos WHERE crecod=".$crecod);

	$r=cargar_registro("SELECT * FROM pacientes WHERE pac_id=".$c['pac_id']);
	
	//print_r($c);

?>

<html>
<title>Visualizar Informaci&oacute;n de Cr&eacute;dito</title>

<?php cabecera_popup('..'); ?>

<script>

pago='';

forma_pago=function() {

	var params='total='+($('monto').value*1);

	 l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-250;
        
    win = window.open('../ingresos/forma_pago.php?'+params, 
                    '_forma_pago',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=490');
                    
    win.focus();


}


function pagar() {

	var params='crecod=<?php echo $crecod*1; ?>&'+$('pago').serialize();
	
	if($('saldo_credito').value*1<$('monto').value*1) {
		alert('El monto ingresado es mayor que el saldo pendiente.');
		return;
	}	

	$('boton_pagar').disabled=true;	
	
	var myAjax=new Ajax.Request(
	'sql_pago_credito.php',
	{
		method:'post',
		parameters: params,
		onComplete: function(resp) {
		
			try {
			
				d=resp.responseText.evalJSON(true);
			
				window.open('../ingresos/imprimir_boletin.php?bolnum='+d[1],'_blank');

				location.reload(true);
				
			} catch(err) {
			
				alert(resp.responseText);
				
			}
		
		
		}	
	}	
	);

}

function cambiar_vista() {

	if($('cambia_vista').value=='- Ver Boletines -') {
		$('cambia_vista').value='-- Ver  Cuotas --';
		$('lista_cuotas').style.display='none';
		$('lista_boletines').style.display='';
	} else {
		$('cambia_vista').value='- Ver Boletines -';
		$('lista_cuotas').style.display='';
		$('lista_boletines').style.display='none';		
	}
	
}

function ver_cobranza() {

	window.open('cobranzas_credito.php?crecod=<?php echo $crecod; ?>','_self');
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<div class="sub-content">
<table cellpadding=0 cellspacing=0 style='width:100%;'><tr><td>
<img src="../iconos/table.png"></td><td>
<b>Datos del Cr&eacute;dito</b></td><td style='width:40%;text-align:right;'>
<input type='button' id='cambia_vista' name='cambia_vista' 
onClick='cambiar_vista();' value='- Ver Boletines -' />
</td><td style='width:20%;text-align:right;'>
<input type='button' id='cobranza' name='cobranza' 
onClick='ver_cobranza();' value='- Gesti&oacute;n Cobranza -' />
</td></tr></table>
</div>

<div class="sub-content">

<table>

<tr>
<td style='text-align:right;'>R.U.T.:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['pac_rut']); ?></td>
<td style='text-align:right;'>Nombre Completo:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['pac_appat']); ?>&nbsp;
<?php echo htmlentities($r['pac_apmat']); ?>&nbsp;
<?php echo htmlentities($r['pac_nombres']); ?></td>
</tr>

</table>
</div>

<table style='width:100%;' cellpadding=0 cellspacing=0>

<tr><td valign="top">

<div class='sub-content2'> 

<?php 
	
		$q="
			SELECT 
			*,
			cuofec::date AS cuofec,
			bolfec::date AS bolfec,
			cuofecpag::date AS cuofecpag,
			(cuofecpag::date-cuofec::date) AS atraso,
			(now()::date-cuofec::date) AS atraso2 
			FROM cuotas 
			LEFT JOIN boletines USING (bolnum)
			WHERE cuotas.crecod=".$crecod."
			ORDER BY cuotas.cuofec, cuotas.cuofecpag	
		";
		
		$cuotas=cargar_registros_obj($q);

		ob_start();

		$total=0; $deuda=0; $saldo=($c['cretot']+$c['crepie']);		
		
		for($j=0;$j<sizeof($cuotas);$j++) {
		
			$clase=($j%2==0)?'tabla_fila':'tabla_fila2';		
		
			echo '<tr class="'.$clase.'">';
			
			if($cuotas[$j]['cuofecpag']!='') {			

			echo '<td style="text-align:center;">'.$cuotas[$j]['cuofec'].'</td>
					<td style="text-align:center;">'.$cuotas[$j]['bolfec'].'</td>
					<td style="text-align:center;">'.$cuotas[$j]['atraso'].'</td>
					<td style="text-align:center;">
					'.vboletin($cuotas[$j]['bolnum'],false,'../').'</td>
					<td style="text-align:right;color:green;">
					$'.number_format($cuotas[$j]['cuopag']*1,0,',','.').'.-</td>
					<td style="text-align:center;">
					<img src="../iconos/tick.png"> 
					</td>';

			$total+=$cuotas[$j]['cuopag']*1;
			$saldo-=$cuotas[$j]['cuopag']*1;

			} else {

			$f=explode('/',$cuotas[$j]['cuofec']);
			$fp=mktime(0,0,0,$f[1],$f[0],$f[2]);
			$fn=mktime(0,0,0);
			
			if($fp<=$fn) {
				$color='red';
				$deuda+=$cuotas[$j]['cuomon']*1; $icono='cross';
			} else {
				$color='blue'; $icono='clock';
			}

			echo '<td style="text-align:center;">'.$cuotas[$j]['cuofec'].'</td>
					<td style="text-align:center;">'.$cuotas[$j]['bolfec'].'</td>
					<td style="text-align:center;">'.$cuotas[$j]['atraso2'].'</td>
					<td style="text-align:center;">
					(n/a)</td>
					<td style="text-align:right;color:'.$color.'">
					$'.number_format($cuotas[$j]['cuomon'],0,',','.').'.-</td>
					<td style="text-align:center;">
					<img src="../iconos/'.$icono.'.png">					
					</td>';
			
			}
					
			echo	'</tr>';		

		}
		
		$htmlcuotas=ob_get_contents();
		ob_end_clean();





		$q="
			SELECT 
			*,
			bolfec::date AS bolfec
			FROM boletines 
			WHERE boletines.crecod=".$crecod."
			ORDER BY boletines.bolfec	
		";
		
		$boletines=cargar_registros_obj($q);

		ob_start();

		for($j=0;$j<sizeof($boletines);$j++) {
		
			$clase=($j%2==0)?'tabla_fila':'tabla_fila2';		
		
			echo '<tr class="'.$clase.'">';
			
			echo '<td style="text-align:center;">
					'.vboletin($boletines[$j]['bolnum'],false,'../').'</td>
					<td style="text-align:center;">'.$boletines[$j]['bolfec'].'</td>
					<td style="text-align:right;font-weight:bold;">$'.number_format($boletines[$j]['bolmon']*1,0,',','.').'.-</td>
					';
		
			echo	'</tr>';		

		}
		
		$htmlboletines=ob_get_contents();
		ob_end_clean();



		$htmltotales='<tr class="tabla_fila" 
				style="font-weight:bold;">
				<td style="text-align:right;">Total Pagado:</td>
				<td style="text-align:right;color:green;">
				$'.number_format($total,0,',','.').'.-</td></tr>
				<tr class="tabla_fila2" 
				style="font-weight:bold;">
				<td style="text-align:right;">Morosidad:</td>
				<td style="text-align:right;color:red;">
				$'.number_format($deuda,0,',','.').'.-</td></tr>
				<tr class="tabla_fila" 
				style="font-weight:bold;">
				<td style="text-align:right;">Saldo Pendiente:</td>
				<td style="text-align:right;color:blue;">
				$'.number_format($saldo,0,',','.').'.-</td></tr>';




		
		print("
		<table cellpadding=3 style='width:100%;font-size:12px;height:150px;'>
		<tr class='tabla_fila'>
		<td style='text-align:right;'>
		Fecha de Apertura:		
		</td>
		<td style='text-align:center;'>
		".$c['crefec']."
		</td></tr>
		
		<tr class='tabla_fila2'>
		<td style='text-align:right;'>
		Valor Producto:		
		</td>
		<td style='text-align:right;font-weight:bold;'>
		$".number_format( ($c['cretot']+$c['crepie']) ,0,',','.').".-</td>
		</tr>
		
		<tr class='tabla_fila'>
		<td style='text-align:right;'>
		Pie:		
		</td>
		<td style='text-align:right;'>
		$".number_format($c['crepie'],0,',','.').".-</td>
		</tr>
		
		<tr class='tabla_fila2'>	
		<td style='text-align:right;'>
		Monto Cr&eacute;dito:		
		</td>
		<td style='text-align:right;font-weight:bold;'>
		$".number_format($c['cresal'],0,',','.').".-</td>
		</tr>

		<tr class='tabla_fila'>	
		<td style='text-align:right;'>
		N&uacute;mero de Cuotas:		
		</td>
		<td style='text-align:right'>
		".number_format($c['cuonro'],0,',','.').".-</td>
		</tr>

		<tr class='tabla_fila2'>	
		<td style='text-align:right;'>
		Valor Cuotas:		
		</td>
		<td style='text-align:right'>
		$".number_format($c['crevalcuo'],0,',','.').".-</td>
		</tr>

		$htmltotales

		</table>
		");

?>

</div>

</td><td>

<div class='sub-content2' style="width:350px;height:200px;overflow:auto;">

<table style="width:100%;font-size:12px;" id='lista_cuotas'>

<tr class="tabla_header">
<td>Fecha Pago</td>
<td>Fecha Bolet&iacute;n</td>
<td>Atraso</td>
<td>Bolet&iacute;n</td>
<td>Monto</td>
<td>&nbsp;</td>
</tr>

<?php 

	echo $htmlcuotas;

?>

</table>

<table style="width:100%;font-size:12px;display:none;" id='lista_boletines'>

<tr class="tabla_header">
<td>Bolet&iacute;n</td>
<td>Fecha</td>
<td>Monto</td>
</tr>

<?php echo $htmlboletines; ?>

</table>

</div>

</td></tr></table>

<?php if($c['cretip']=='N') { ?>

<div class="sub-content">
<img src="../iconos/money.png">
<b>Realizar Pago</b>
</div>

<div class="sub-content">

<form id='pago' name='pago' onSubmit='return false;'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:30%;'>Nro. de Bolet&iacute;n/Fecha:</td>
<td style='font-weight:bold;' colspan=2>
<input type='text' id='nbolnum' name='nbolnum' size=10
value='' style='text-align:center;' />
<input type='text' id='nbolfec' name='nbolfec' size=10
value='<?php echo date('d/m/Y'); ?>' style='text-align:center;' />
</td>
</tr>

<tr>
<td style='text-align:right;'>Monto a Pagar:</td>
<td style='width:10%;'>
<input type='text' id='monto' name='monto' size=10 style='text-align:right;'></td>
<td>
<input type='hidden' id='pago_efectivo' name='pago_efectivo' value=''>
<input type='hidden' id='pago_cheques' name='pago_cheques' value=''>
<input type='hidden' id='pago_otras' name='pago_otras' value=''>
<input type='button' style='font-size:11px;' 
value='Forma de Pago...' onClick='forma_pago();'></td>
</tr>

<tr><td style='text-align:right;'>Vigencia:</td><td colspan=2>
<select id='vigencia' name='vigencia'>
<option value='N' SELECTED >Vigente</option>
<option value='CD' >Cerrado por Descuento</option>
<option value='A' >Anulado</option>
</select>
</td></tr>


<tr>
<td style='text-align:right;'>Observaciones:</td>
<td colspan=2><input type='text' id='observaciones' name='observaciones' size=25></td>
</tr>
<tr><td style='text-align:center;' colspan=3>
<center>
<input type='button' onClick='pagar();' id='boton_pagar' 
value='Ingresar Pago del Cr&eacute;dito...'>
</center>
</td></tr>

</table>

</div>

<input type='hidden' id='saldo_credito' name='saldo_credito' value='<?php echo $saldo*1; ?>' />

</form>

</div>

<?php } ?>


</body>
</html>