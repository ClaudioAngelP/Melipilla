<?php 

	require_once('../../conectar_db.php');
	
	$r=cargar_registros_obj("SELECT 
	*, (
		bloq_fecha_ini<=CURRENT_DATE AND 
		(
			bloq_fecha_fin IS NULL OR 
			bloq_fecha_fin>=CURRENT_DATE
		)
	) AS vigencia
	FROM bloqueo_camas
	JOIN bloqueo_camas_motivos ON bloq_motivo=bmot_id
	JOIN funcionario USING (func_id)
	LEFT JOIN tipo_camas ON
	cama_num_ini<=bloq_numero_cama AND cama_num_fin>=bloq_numero_cama
	LEFT JOIN clasifica_camas ON 
	tcama_num_ini<=bloq_numero_cama AND tcama_num_fin>=bloq_numero_cama
	ORDER BY bloq_fecha_ini, bloq_numero_cama");
	
	$tcama_ids=explode(',',_cav(254));

?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>Sector/Sala</td>
		<td>Nro. Cama</td>
		<td>Motivo</td>
		<td>Fecha Inicio</td>
		<td>Fecha T&eacute;rmino</td>
		<td>Funcionario/Observaciones</td>
		<td>Eliminar</td>
	</tr>

<?php 

	if($r)
	for($i=0;$i<sizeof($r);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
		<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'>
		<td style='text-align:center;'>".htmlentities($r[$i]['tcama_tipo']." / ".$r[$i]['cama_tipo'])."</td>
		<td style='text-align:center;font-size:18px;'>".($r[$i]['bloq_numero_cama']*1-$r[$i]['tcama_num_ini']*1+1)."</td>
		<td style='text-align:center;font-weight:bold;'>".htmlentities($r[$i]['bmot_desc'])."</td>
		<td style='text-align:center;font-size:16px;'>".$r[$i]['bloq_fecha_ini']."</td>
		<td style='text-align:center;font-size:16px;'>".($r[$i]['bloq_fecha_fin']!=''?$r[$i]['bloq_fecha_fin']:'<i>(Indefinido...)</i>')."</td>
		<td style='text-align:justify;'><i>[".htmlentities($r[$i]['func_nombre'])."]</i><br>".htmlentities($r[$i]['bloq_observaciones'])."</td>
		");
	
		if($r[$i]['vigencia']=='t') {
			$checked='CHECKED=""';
		} else {
			$checked='DISABLED=""';
		}
		
		/*print("
		<td style='text-align:center;'>
		<input type='checkbox' ".$checked."
		id='bloq_chq_".$r[$i]['bloq_id']."' name='bloq_chq_".$r[$i]['bloq_id']."'  />
		</td>
		");*/
				
		if(in_array($r[$i]['tcama_id'], $tcama_ids)) {
			print("<td>
			<center>
			<img src='iconos/delete.png' style='cursor:pointer;' onClick='eliminar_bloqueo(".$r[$i]['bloq_id'].");' />
			</center>
			</td>");
		} else {
			print("<td>&nbsp;</td>");
		}
		
		print("</tr>");
		
	}

?>

</table>

