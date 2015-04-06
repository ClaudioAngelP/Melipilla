<?php 

	require_once('../conectar_db.php');

	$hosp_id=$_GET['hosp_id']*1;

	$d=cargar_registros_obj("SELECT *, cuenta_corriente.codigo AS _codigo FROM cuenta_corriente
	LEFT JOIN codigos_prestacion_item ON cuenta_corriente.codigo=codigos_prestacion_item.codigo AND (cuenta_corriente.modalidad=codigos_prestacion_item.modalidad OR codigos_prestacion_item.modalidad='mixto')
	WHERE hosp_id=$hosp_id ORDER BY item_nombre ASC, fecha, precio DESC, copago DESC",true);

	$h=cargar_registro("SELECT * FROM cuenta_corriente_encabezado 
	JOIN pacientes USING (pac_id)
	LEFT JOIN hospitalizacion USING (hosp_id)
	WHERE hosp_id=$hosp_id;",true);

	header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: filename=\"Cuenta_Corriente_$hosp_id.xls\";");

?>
<html>
<body font-family='Verdana'>
<table style='width:100%;'>
<tr><td colspan=7 align='center'><h1><u>Estado de Cuenta</u></h1></td></tr>
<tr><td bgcolor='#CCCCCC' align='right'>Nro. Cuenta:</td><td colspan=6 align='left'><h2><b><?php echo $hosp_id; ?></b></h2></td></tr>
<tr><td bgcolor='#CCCCCC' align='right'>Fecha Ingreso:</td><td colspan=6><?php echo $h['hosp_fecha_ing']; ?></td></tr>
<tr><td bgcolor='#CCCCCC' align='right'>Fecha Egreso:</td><td colspan=6><?php echo $h['hosp_fecha_egr']; ?></td></tr>
<tr><td bgcolor='#CCCCCC' align='right'>R.U.N.:</td><td colspan=2><?php echo $h['pac_rut']; ?></td><td bgcolor='#CCCCCC' align='right' colspan=2>Ficha:</td><td><?php echo $h['pac_ficha']; ?></td></tr>
<tr><td bgcolor='#CCCCCC' align='right'>Nombre Completo:</td><td colspan=6><?php echo $h['pac_nombres'].' '.$h['pac_appat'].' '.$h['pac_apmat']; ?></td></tr>
<tr><td bgcolor='#CCCCCC' align='right'>Fecha Nacimiento:</td><td colspan=2><?php echo $h['pac_fc_nac']; ?></td><td bgcolor='#CCCCCC' align='right' colspan=2>Previsi&oacute;n:</td><td><?php echo $h['prev_desc']; ?></td></tr>
</table>
<table style='width:100%'>



<?php

	$precio=0; $copago=0; $precio2=0; $copago2=0;
	$item_nombre='-1';

	if($d) {

		for($i=0;$i<sizeof($d);$i++) {

			if($d[$i]['item_nombre']!=$item_nombre) {

				if($i>0) {

					print("<tr><td colspan=5 align='right'><b>Totales $item_nombre:</b></td><td align='right' color='gray'><b>$precio2</b></td><td align='right' color='blue'><b>$copago2</b></td></tr>");	


				}

				$item_nombre=$d[$i]['item_nombre'];

				print("<tr><td colspan=7><h2><b><u>$item_nombre</u></b></h2></td></tr>");

?>

<tr bgcolor='#AAAAAA'>
<td align='center'>Fecha</td>
<td align='center'>C&oacute;digo</td>
<td align='center'>Descripci&oacute;n</td>
<td align='center'>Item</td>
<td align='center'>Cantidad</td>
<td align='center'>Valor</td>
<td align='center'>Copago</td>
</tr>

<?php

				$precio2=0; $copago2=0;

			}

			print("
				<tr>
				<td align='center'>".substr($d[$i]['fecha'],0,10)."</td>
				<td align='center'>'".$d[$i]['_codigo']."</td>
				<td>".$d[$i]['glosa']."</td>
				<td align='center'>'".$d[$i]['item_codigo']."</td>
				<td align='right'>".$d[$i]['cantidad']."</td>
				<td align='right'>".$d[$i]['precio']*$d[$i]['cantidad']."</td>
				<td align='right'>".$d[$i]['copago']*$d[$i]['cantidad']."</td>
				</tr>
			");

			$precio+=$d[$i]['precio']*$d[$i]['cantidad'];
			$precio2+=$d[$i]['precio']*$d[$i]['cantidad'];
			$copago+=$d[$i]['copago']*$d[$i]['cantidad'];
			$copago2+=$d[$i]['copago']*$d[$i]['cantidad'];

		}

	}

	print("<tr><td colspan=5 align='right'><b>Totales $item_nombre:</b></td><td align='right' color='gray'><b>$precio2</b></td><td align='right' color='blue'><b>$copago2</b></td></tr>");


	print("<tr bgcolor='#AAAAAA'><td colspan=5 align='right'><b>Totales Generales:</b></td><td align='right' color='gray'><b>$precio</b></td><td align='right' color='blue'><b>$copago</b></td></tr>");

?>

</table>
</body>
</html>
