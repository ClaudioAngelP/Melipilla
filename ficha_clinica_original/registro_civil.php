<?php 

	$script=1;
	require_once('../conectar_db.php');
	require_once('../conectores/registro_civil/registrocivil.php');

?>

<html>
<title>Conexi&oacute;n Registro Civil</title>

<?php cabecera_popup('..'); ?>

<body class='fuente_por_defecto popup_background'>

<?php

	regcivil_login();
	
	ob_start();
	$datos=@regcivil_buscar($_GET['rut']);
	ob_end_clean();
	
	regcivil_logout();
	
	$datos[0]=str_replace('/',' ',$datos[0]);
	$datos[0]=str_replace('=','',$datos[0]);

?>

<table style='width:100%;'>
	
	<tr>
	<td colspan=2 class='sub-content' style='font-weight:bold;text-align:center;font-size:14px;'>Informaci&oacute;n de Registro Civil</td>
	</tr>
	
	<tr>
		<td style='text-align:right;width:30%;' class='tabla_fila2'>RUT:</td>
		<td style='font-size:16px;font-weight:bold;'><?php echo $_GET['rut']; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;width:30%;' class='tabla_fila2'>Nombres:</td>
		<td style='font-size:16px;font-weight:bold;'><?php echo $datos[0]; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;width:30%;' class='tabla_fila2'>Paterno:</td>
		<td style='font-size:16px;font-weight:bold;'><?php echo $datos[1]; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;width:30%;' class='tabla_fila2'>Materno:</td>
		<td style='font-size:16px;font-weight:bold;'><?php echo $datos[2]; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Sexo:</td>
		<td><?php echo $datos[3]; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Fecha de Nacimiento:</td>
		<td><?php echo $datos[4]; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;' class='tabla_fila2'>Fecha de Defunci&oacute;n:</td>
		<td><?php echo $datos[5]; ?></td>
	</tr>
</table>

</body>
</html>

