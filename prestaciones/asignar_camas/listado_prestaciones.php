<?php 

	require_once('../../conectar_db.php');
	
	$hosp_id=$_POST['hosp_id']*1;

	$hp=cargar_registros_obj("
		SELECT *
		FROM hospitalizacion_prestaciones
		WHERE hosp_id=$hosp_id
		ORDER BY hospp_fecha_digitacion DESC
	", true);
		
	print("
	<table style='width:100%;'>
		<tr class='tabla_header'>
			<td>Fecha/Hora</td>
			<td style='width:40%'>Prestaci&oacute;n</td>
			<td>Cantidad</td>
			<td>Realizado</td>
			<td>Fecha/Hora Realizado</td>
			<td>Acci&oacute;n</td>
		</tr>
	");
	
	if($hp)
	for($i=0;$i<sizeof($hp);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$checked=' CHECKED ';
		
		if($hp[$i]['hospp_fecha_realizado']=='') {
			$hp[$i]['hospp_fecha_realizado']='<i>(Pendiente...)</i>';
			$checked='';
		} else {
			$hp[$i]['hospp_fecha_realizado']='<b>'.substr($hp[$i]['hospp_fecha_realizado'],0,16).'</b>';
			$checked=' CHECKED ';
		}
			
		print("<tr class='$clase'>
		<td style='text-align:center;'>".substr($hp[$i]['hospp_fecha_digitacion'],0,16)."</td>
		<td style='text-align:left;'><b>[".$hp[$i]['hospp_codigo']."]</b> ".trim($hp[$i]['hospp_nombre'])."</td>
		<td style='text-align:right;'>".$hp[$i]['hospp_cantidad']."</td>
		<td>
		<center>
			<input type='checkbox' 
			id='hospp_".$hp[$i]['hospp_id']."' 
			name='hospp_".$hp[$i]['hospp_id']."' 
			value='' $checked onClick='realizar_hospp(".$hp[$i]['hospp_id'].");' /></center>
		</td>
		<td style='text-align:center;'>".$hp[$i]['hospp_fecha_realizado']."</td>
		<td style='text-align:center;'><img src='../../iconos/delete.png' style='cursor:pointer;' 
			onClick='eliminar_hp(".$hp[$i]['hospp_id'].");'></td>
		</tr>");
		
	}
		
	print("	
	</table>
	");

?>
