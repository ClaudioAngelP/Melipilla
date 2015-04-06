<?php 

	require_once('../conectar_db.php');

	$c=cargar_registros_obj("SELECT * FROM monitoreo_ges_registro 
	JOIN monitoreo_Ges USING (mon_id) 
	JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
	WHERE monr_subclase IN ('E', 'N') AND monr_estado=0 ORDER BY pst_patologia_interna, mon_fecha_limite DESC;", true);
 
	
	
	print("<table style='width:100%'><tr class='tabla_header'><td>RUT</td><td>Nombre</td><td>Patolog&iacute;a</td><td>Garant&iacute;a</td></tr>");

	for($i=0;$i<sizeof($c);$i++) {
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';

		print("<tr class='$clase'>
		<td style='text-align:right;'>".$c[$i]['mon_rut']."</td>
		<td style='text-align:left;'>".$c[$i]['mon_nombre']."</td>
		<td style='text-align:left;'>".$c[$i]['pst_patologia_interna']."</td>
		<td style='text-align:left;'>".$c[$i]['mon_garantia']."</td>
		</tr>");

	}
	
	print("</table>");



?>
