<?php 

	require_once('../../conectar_db.php');
	
	$hosp_id=$_POST['hosp_id']*1;

	$h=cargar_registro("SELECT * FROM hospitalizacion WHERE hosp_id=$hosp_id;");
	
	$pac_id=$h['hosp_pac_id']*1;
	
	$hc=cargar_registros_obj("

		SELECT * FROM (

		SELECT * FROM (
			SELECT 
			fecha AS log_fecha, codigo AS art_codigo, art_glosa, 
			cantidad, forma_nombre,
			art_val_ult AS punit, 
			(cantidad*art_val_ult) AS subtotal, 0 AS hosphc_id
			FROM cargo_pyxis
			JOIN articulo ON codigo=art_codigo 
			LEFT JOIN bodega_forma ON art_forma=forma_id
			WHERE pac_id=$pac_id 
			ORDER BY log_fecha DESC
		) AS foo 
		
		UNION ALL
		
		(
			SELECT hosphc_fecha_digitacion AS log_fecha, art_codigo, art_glosa, 
			hosphc_cantidad AS cantidad, forma_nombre,
			art_val_ult AS punit, 
			(hosphc_cantidad*art_val_ult) AS subtotal, hosphc_id
			FROM hospitalizacion_hoja_cargo
			JOIN articulo USING (art_id)
			LEFT JOIN bodega_forma ON art_forma=forma_id
			WHERE hosp_id=$hosp_id

		)
		
		) AS foo3
		
		ORDER BY log_fecha DESC

	", true);
		
	print("
	<table style='width:100%;'>
		<tr class='tabla_header'>
			<td>Fecha/Hora</td>
			<td>C&oacute;digo</td>
			<td>Art&iacute;culo</td>
			<td>Cantidad</td>
			<td>Unidad</td>
			<td>P Unit. $</td>
			<td>Subtotal $</td>
			<td>Eliminar</td>
		</tr>
	");
	
	if($hc)
	for($i=0;$i<sizeof($hc);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("<tr class='$clase'>
		<td style='text-align:center;'>".substr($hc[$i]['log_fecha'],0,16)."</td>
		<td style='text-align:right;font-weight:bold;'>".$hc[$i]['art_codigo']."</td>
		<td style='text-align:left;'>".$hc[$i]['art_glosa']."</td>
		<td style='text-align:right;'>".$hc[$i]['cantidad']."</td>
		<td style='text-align:left;'>".$hc[$i]['forma_nombre']."</td>
		<td style='text-align:right;'>$".number_format($hc[$i]['punit'],0,',','.').".-</td>
		<td style='text-align:right;'>$".number_format($hc[$i]['subtotal'],0,',','.').".-</td>
		<td><center>
		");
		
		if($hc[$i]['hosphc_id']!=0)
			print("<img src='../../iconos/delete.png' style='cursor:pointer;' 
			onClick='eliminar_hc(".$hc[$i]['hosphc_id'].");'>");
		else
			print("<img src='../../iconos/stop.png'>");
		
		print("</center></td>
		</tr>");
		
	}
		
	print("	
	</table>
	");

?>
