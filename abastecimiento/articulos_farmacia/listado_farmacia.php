<?php

	require_once('../../conectar_db.php');

	$filtro=pg_escape_string(utf8_decode($_POST['filtro']));

	if($filtro=='') {
		$filtro_w='true';
	} else {
		$filtro_w="(art_codigo ILIKE '%$filtro%' OR art_glosa ILIKE '%$filtro%')";
	}

	$l=cargar_registros_obj("SELECT *, (autfd_id IS NOT NULL) AS arsenal, articulo.art_id AS real_art_id FROM articulo JOIN bodega_forma ON art_forma=forma_id LEFT JOIN autorizacion_farmacos_detalle ON autf_id=1 AND autorizacion_farmacos_detalle.art_id=articulo.art_id WHERE $filtro_w AND art_item ILIKE '2204004%' ORDER BY art_glosa", true);

?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>#</td>
<td>C&oacute;digo</td>
<td>Descripci&oacute;n</td>
<td>Forma</td>
<td>Arsenal</td>
<td>Vademecum&copy;</td>
<td>Editar</td>
</tr>

<?php

	if($l)
	for($i=0;$i<sizeof($l);$i++) {

		$clase=$i%2==0?'tabla_fila':'tabla_fila2';

		print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\"'>
		<td style='text-align:right;'>".($i+1)."</td>
		<td style='text-align:right;font-weight:bold;'>".$l[$i]['art_codigo']."</td>
		<td style='text-align:left;'>".$l[$i]['art_glosa']."</td>
		<td style='text-align:center;'>".$l[$i]['forma_nombre']."</td>
		<td style='text-align:center;'>".($l[$i]['arsenal']=='t'?'<b>SI</b>':'NO')."</td>");

		if($l[$i]['id_vademecum']!='')
			print("<td><center><img src='iconos/layout.png' onClick='ver_vademecum2(\"".$l[$i]['id_vademecum']."\");' style='cursor:pointer;' /></center></td>");
		else
			print("<td><center><img src='iconos/cross.png' /></center></td>");

		print("<td style='text-align:center;'><center><img src='iconos/pill_go.png' onClick='abrir_art(".$l[$i]['real_art_id'].");' style='cursor:pointer;' /></td>
		</tr>");

	}

?>


</table>
