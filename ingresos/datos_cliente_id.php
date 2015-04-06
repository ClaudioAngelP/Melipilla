<?php 

	require_once('../conectar_db.php');
	
	$id_sidra=$_POST['id_sidra']*1;
	
	$r=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$id_sidra");

?>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:30%;'>ID.:</td>
<td style='font-weight:bold;'><?php echo htmlentities($r['id_sidra']); ?></td>
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
