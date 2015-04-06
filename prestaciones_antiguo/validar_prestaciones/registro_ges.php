<?php 

	require_once('../../conectar_db.php');
	
	$ca_id=$_GET['ca_id']*1;
	
	$caso=cargar_registro("SELECT * FROM casos_auge WHERE ca_id=$ca_id", true);
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=".$caso['ca_pac_id'],true);

?>
<html>
<title>Registro de Monitoreo GES</title>
<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/chart_organisation.png' />
<b>Registro de Monitoreo GES</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr><td style='width:150px;text-align:right;' class='tabla_fila2'>
R.U.T.:
</td><td class='tabla_fila' style='text-align:left;font-size:16px;font-weight:bold;'>
<?php echo $pac['pac_rut']; ?>
</td><td style='width:150px;text-align:right;' class='tabla_fila2'>
Nro. Ficha:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;font-weight:bold;'>
<?php echo $pac['pac_ficha']; ?>
</td></tr>

<tr><td style='width:100px;text-align:right;' class='tabla_fila2'>
Nombre Paciente:
</td><td colspan=3 class='tabla_fila' style='text-align:left;font-weight:bold;font-size:16px;'>
<?php echo $pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']; ?>
</td></tr>

<tr><td style='width:150px;text-align:right;' class='tabla_fila2'>
Fecha de Nac.:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;'>
<?php echo $pac['pac_fc_nac']; ?>
</td><td style='width:150px;text-align:right;' class='tabla_fila2'>
Edad:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;font-weight:bold;'>
<?php echo $edad; ?>
</td></tr>

<tr>
<td class='tabla_fila2' style='text-align:right;'>Caso GES:</td>
<td class='tabla_fila' colspan=3><?php echo $caso['ca_patologia']; ?></td>
</tr>

</table>

</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td class='tabla_fila2' style='text-align:right;'>Clasificaci&oacute;n:</td>
<td class='tabla_fila'><select id='clasificacion' name='clasificacion'>
<option value='1'>Brecha Real - Atendido, Documento no Confeccionado</option>
<option value='2'>Brecha Real - Atendido, Documento no Registrado</option>
<option value='3'>Brecha Real - Atendido en Extrasistema</option>
<option value='4'>Brecha Real - Atendido Localmente</option>
<option value='5'>Brecha Real - Atendido Particular</option>
<option value='6'>Brecha Real - Correo</option>
<option value='7'>Brecha Real - Citado</option>
<option value='8'>Brecha Real - Correo, Compras</option>
</select></td>
</tr>

<tr>
<td class='tabla_fila2' style='text-align:right;'>Categorizaci&oacute;n:</td>
<td class='tabla_fila'><select id='categorizacion' name='categorizacion'>
<option value='1'>1 Brecha Real</option>
<option value='2'>2 Brecha Real - NSP 1</option>
<option value='3'>3 En proceso de correo</option>
<option value='4'>4 En proceso de compra</option>
<option value='5'>5 Otros</option>
<option value='6.1'>6.1 Exceptuado</option>
<option value='7'>7 Correos</option>
<option value='8'>8 Brecha Real - Citado</option>
<option value='9'>9 Gesti&oacute;n Propia</option>
</select></td>
</tr>

<tr>
<td class='tabla_fila2' style='text-align:right;'>Historial:</td>
<td class='tabla_fila'><textarea id='historial' name='historial' cols=40 rows=4>

</textarea></td>
</tr>


</table>

<center>
<input type='button' id='' name='' onClick='guardar();' 
value='--- Guardar Informaci&oacute;n Monitoreo... ---' />
</center>

</div>

</body>
</html>