<?php 

	require_once('../../conectar_db.php');
	
	$l=cargar_registros_obj("
		SELECT * FROM hospitalizacion_ambulancias 
		JOIN pacientes ON hospa_pac_id=pac_id
		JOIN funcionario ON hospa_func_id=func_id
		ORDER BY hospa_id;
	", true);

?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>Fecha Ingreso</td>
		<td>Funcionario</td>
		<td>RUT</td>
		<td>Nombre Paciente</td>
		<td>Motivo</td>
		<td>Contacto</td>
		<td>Prioridad</td>
		<td>M&oacute;vil</td>
		<td>Detalles</td>
	</tr>
	
	
<?php 

	if($l)
	for($i=0;$i<sizeof($l);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$s_prioridad="<option value='0'>*</option>";
		
		for($j=1;$j<11;$j++) {
			if($l[$i]['hospa_prioridad']==$j) $sel='SELECTED'; else $sel='';
			$s_prioridad.="<option value='$j' $sel>$j</option>";
		}

		$s_movil="<option value=''>(N/A)</option>";

		for($j=1;$j<4;$j++) {
			if($l[$i]['hospa_movil']==$j) $sel='SELECTED'; else $sel='';
			$s_movil.="<option value='$j' $sel>M&oacute;vil $j</option>";
		}
		
		switch($l[$i]['hospa_motivo']) {
			case 0: $mot='ALTA DOMICILIO'; break;
			case 1: $mot='RESCATE DOMICILIO'; break;
			case 2: $mot='RESCATE V.P./CLINICA/HOSP.'; break;
			case 3: $mot='PROCEDIMIENTO'; break;
			case 4: $mot='TRASLADO RED'; break;
		}
		
		print("
		<tr class='$clase'>
		<td style='text-align:center;'>".substr($l[$i]['hospa_fecha_ing'],0,16)."</td>
		<td style='text-align:left;'><i>".($l[$i]['func_nombre'])."</i></td>
		<td style='text-align:right;font-weight:bold;'>".($l[$i]['pac_rut'])."</td>
		<td style='text-align:left;'>".trim($l[$i]['pac_appat'].' '.$l[$i]['pac_apmat'].' '.$l[$i]['pac_nombres'])."</td>
		<td style='text-align:center;font-weight:bold;'>".$mot."</td>
		<td style='text-align:left;'>".trim($l[$i]['hospa_contacto'])."</td>
		<td><center><select id='pr_".$l[$i]['hospa_id']."' name='pr_".$l[$i]['hospa_id']."'>".$s_prioridad."</select></center></td>
		<td><center><select id='mv_".$l[$i]['hospa_id']."' name='mv_".$l[$i]['hospa_id']."'>".$s_movil."</select></center></td>
		<td><center><img src='iconos/magnifier.png' style='cursor:pointer;' /></center></td>
		</tr>
		");
		
		
	}

?>	
	
	
</table>
