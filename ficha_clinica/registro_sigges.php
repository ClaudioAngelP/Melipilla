<?php 

	require_once('../conectar_db.php');

?>

<html>
<title>Conexi&oacute;n SIGGES</title>

<?php cabecera_popup('..'); ?>

<body class='fuente_por_defecto popup_background'>

<?php

	ob_start();
		require_once('../conectores/sigges/descargar_paciente.php');
		$json=ob_get_contents();
	ob_end_clean();
	
	$datos=json_decode($json, true);
	
?>

<table style='width:100%;'>
	
	<tr>
	<td colspan=2 class='sub-content' style='font-weight:bold;text-align:center;font-size:14px;'>Informaci&oacute;n de Registro SIGGES</td>
	</tr>
	
	<tr>
		<td style='text-align:right;width:30%;' class='tabla_fila2'>RUT:</td>
		<td style='font-size:16px;font-weight:bold;'><?php echo $datos['rut']; ?></td>
	</tr>
	<tr>
		<td style='text-align:right;width:30%;' class='tabla_fila2'>Nombre:</td>
		<td style='font-size:16px;font-weight:bold;'><?php echo $datos['nombre']; ?></td>
	</tr>
</table>

<table style='width:100%;font-size:14px;'>
	<tr class='tabla_header'>
		<td>#</td>
		<td>Problema de Salud GES</td>
	</tr>

<?php 

	$casos=$datos['casos'];

	for($i=0;$i<sizeof($casos);$i++) {
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		print("<tr class='$clase'><td style='text-align:right;font-weight:bold;font-size:24px;' rowspan=3>".($i+1)."</td>
		<td style='font-size:16px;'><b>".$casos[$i]['xproblema']."</b></td></tr>
		<tr class='$clase'><td><b>".$casos[$i]['estado']."</b></td></tr>
		<tr class='$clase'><td><i>".$casos[$i]['nombre']."</i></td></tr>
		");
	}
	
?>

</table>

</body>
</html>
