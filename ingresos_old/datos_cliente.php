<?php 

	require_once('../conectar_db.php');
	
	$pac_id=$_POST['pac_id']*1;
	
	$r=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id");

?>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:30%;'>R.U.T.:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['pac_rut']); ?></td>
</tr>

<tr>
<td style='text-align:right;width:30%;'>Paterno:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['pac_appat']); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Materno:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['pac_apmat']); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Nombres:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['pac_nombres']); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Fecha de Nac.:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['pac_fc_nac']); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Direcci&oacute;n:</td>
<td><?php echo htmlentities($r['pac_direccion']); ?></td>
</tr>
<tr>
<td style='text-align:right;'>Tel&eacute;fono Fijo:</td>
<td><?php echo htmlentities($r['pac_fono']); ?></td>
</tr>

<tr>
<td style='text-align:right;'>Tel&eacute;fono M&oacute;vil:</td>
<td><?php echo htmlentities($r['pac_celular']); ?></td>
</tr>

</table>
