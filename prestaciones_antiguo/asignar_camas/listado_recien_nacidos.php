<?php 

	require_once('../../conectar_db.php');
	
	$hosp_id=$_POST['hosp_id']*1;

	$hp=cargar_registros_obj("
		SELECT *
		FROM hospitalizacion_partos
		WHERE hosp_id=$hosp_id
		ORDER BY hospp_fecha_digitacion DESC
	", true);
		
	print("
	<table style='width:100%;'>
		<tr class='tabla_header'>
			<td>Fecha/Hora Digitaci&oacute;n</td>
			<td style='width:40%'>Condici&oacute;n</td>
			<td>Sexo</td>
			<td>Peso (Gramos)</td>
			<td>APGAR</td>
			<td>Remover</td>
		</tr>
	");
	
	if($hp)
	for($i=0;$i<sizeof($hp);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$checked=' CHECKED ';

		switch($hp[$i]['hospp_condicion']*1) {
			case 0: $cond='VIVO'; break;
			case 1: $cond='FALLECIDO'; break;
		} 
		
		switch($hp[$i]['hospp_sexo']*1) {
			case 0: $sexo='MASCULINO'; break;
			case 1: $sexo='FEMENINO'; break;
			case 2: $sexo='INDEFINIDO'; break;
		} 
					
		print("<tr class='$clase'>
		<td style='text-align:center;'>".substr($hp[$i]['hospp_fecha_digitacion'],0,16)."</td>
		<td style='text-align:center;'>".$cond."</td>
		<td style='text-align:center;'>".$sexo."</td>
		<td style='text-align:right;'>".number_format($hp[$i]['hospp_peso_gramos'],0,',','.')."</td>		
		<td style='text-align:center;'>".$hp[$i]['hospp_apgar']."</td>		
		<td>
		<center>
		<img src='../../iconos/delete.png' style='cursor:pointer;' 
		onClick='eliminar_parto(".$hp[$i]['hospp_id'].");'>
		</center></td>
		</tr>");
		
	}
		
	print("	
	</table>
	");

?>
