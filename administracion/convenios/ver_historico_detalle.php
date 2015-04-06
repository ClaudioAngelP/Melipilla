<?php 

	require_once('../../conectar_db.php');
	
	$convenio_id=$_GET['convenio_id']*1;
		
		
	$q = cargar_registros_obj("SELECT to_char(conv_fecha_digitacion, 'DD/MM/YYYY HH24:MI:SS') as conv_fecha_digitacion, 
							  convd_tipo, conv_resp_aprueba, conv_fecha_aprueba, convd_monto,convd_plazo, convd_plazo2, func_nombre
							  FROM convenio_modificaciones 
							  LEFT JOIN funcionario ON func_id=convd_func_adm
							  WHERE convenio_id=$convenio_id");
							  
	$convenio_licitacion = cargar_registro("SELECT convenio_licitacion FROM convenio WHERE convenio_id=$convenio_id");
		
?>
<script>

listado_multas=function() {
	
		var params=$('convenio_id').serialize();		
	
		var myAjax=new Ajax.Updater(
			'listado_convenio_mod',
			'listado_convenio_modificaciones.php',
			{  method:'post', parameters:params 	}	
			
		);
	
}
</script>

<html>
<title>Visualizar Multas</title>

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>
<div class='sub-content'>
<img src='../../iconos/book.png'> <b>Convenio - Modificaciones</b>
</div>
<div class='sub-content'>
	<center>
		<table>
			<tr><td>Licitaci&oacute;n:</td>
				<td><input type='hidden' name='convenio_id' id='convenio_id' value='<?php echo $convenio_id; ?>'>
					<b><?php echo $convenio_licitacion['convenio_licitacion']; ?></b></td></tr>
		</table>
	</center>
<div class='sub-content2' style='height:300px; overflow:auto;' id='listado_convenio_mod' name='listado_convenio_mod'>
<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>Fecha</td>
		<td>Tipo</td>
		<td>N&deg; Aprueba</td>
		<td>Fecha Aprueba</td>
		<td>Plazo Entrega (Dias)</td>
		<td>Monto</td>
		<td>Adm. Contrato</td>
	</tr>

<?php if($q)
	for($i=0;$i<sizeof($q);$i++) {
					
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
		print("<tr style='height:20px;' class='$clase'
					onMouseOver='this.className=\"mouse_over\";'
					onMouseOut='this.className=\"$clase\";'>");
					
		print("
				<td style='text-align:center; font-weight:bold;'>".$q[$i]['conv_fecha_digitacion']."</td>
				<td style='text-align:center;'>".strtoupper($q[$i]['convd_tipo'])."</td>
				<td style='text-align:center;'>".$q[$i]['conv_resp_aprueba']."</td>
				<td style='text-align:center;'>".$q[$i]['conv_fecha_aprueba']."</td>
				<td style='text-align:center;'>".$q[$i]['convd_plazo2']."</td>
				<td style='text-align:center;'>$ ".number_format($q[$i]['convd_monto'],0,',','.').".-</td>
				<td style='text-align:left;'>".$q[$i]['func_nombre']."</td>");
		
		print("</tr>");
	} ?>
	</table></div>
<br /><br />
<center>
<a href='ver_convenio.php?convenio_id=<?php echo $convenio_id; ?>'>Volver Atr&aacute;s...</a>

</center>
</div>
</body>
</html>
