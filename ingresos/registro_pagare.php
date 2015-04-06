<?php 

	require_once('../conectar_db.php');

	$pac_id=$_GET['pac_id']*1;
	$datos_titular=htmlentities(utf8_decode($_GET['datos_titular']));
	
	$pac=cargar_registro("
		SELECT *, COALESCE(ciud_desc, 'Peñalolén') AS ciud_desc FROM pacientes 
		LEFT JOIN comunas USING (ciud_id)
		WHERE pac_id=$pac_id;
	", true);
	
	if($datos_titular!='') {
		$d=explode('|',$datos_titular);
		$rut=$d[0];
		$nombre=$d[1];
		$direccion=$d[2];
		$telefono=$d[3];
	} else {
		$rut=$pac['pac_rut'];
		$nombre=$pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat'];
		$direccion=$pac['pac_direccion'].', '.$pac['ciud_desc'];
		$telefono=$pac['pac_fono'];
	}
	
?>

<html>
<title>Datos del Titular</title>

<?php cabecera_popup('..'); ?>

<script>

function guardar() {

		window.opener.$('datos_titular').value=$('run').value+'|'+$('nombre').value+'|'+$('direccion').value+'|'+$('telefono').value;
		window.close();

}

</script>

<body class='fuente_por_defecto popup_background'>

<center>
<div class='sub-content'>
<img src='../iconos/user_go.png' />
<b>Datos del Titular del Pagar&eacute;</b>

</div>
<table style='width:100%;font-size:14px;'>

<tr><td style='text-align:right;width:25%;' class='tabla_fila2'>R.U.N.:</td>
<td class='tabla_fila'>
<input type='text' id='run' name='run' value='<?php echo $rut; ?>' />
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>Nombre Completo:</td>
<td class='tabla_fila'>
<input type='text' id='nombre' name='nombre' size=35 value='<?php echo $nombre; ?>' />
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>Direcci&oacute;n:</td>
<td class='tabla_fila'>
<input type='text' id='direccion' name='direccion' size=35 value='<?php echo $direccion; ?>' />
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>Telefono(s):</td>
<td class='tabla_fila'>
<input type='text' id='telefono' name='telefono' value='<?php echo $telefono; ?>' />
</td></tr>

</table>

<input type='button' id='' name='' value='-- [ Guardar Datos... ] --' onClick='guardar();' />

</center>

</body>
</html>