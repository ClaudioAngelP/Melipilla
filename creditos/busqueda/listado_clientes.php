<?php 

	require_once('../../conectar_db.php');
	
	$nombre=pg_escape_string(trim(utf8_decode($_POST['nombre'])));


	while(strstr($nombre, '  ')) {
		$nombre=str_replace('  ',' ', $nombre);
	}
	
	$query=str_replace(' ', ' & ', $nombre);

	$lista=cargar_registros_obj("
	
		SELECT 
		*,
		(SELECT COUNT(*) FROM creditos 
		LEFT JOIN boletines ON boletines.crecod=creditos.crecod AND pagare AND bolmon=0
		WHERE creditos.pac_id=pacientes.pac_id AND COALESCE(anulacion,'')='') AS creditos,
		(SELECT SUM(cretot) FROM creditos 
		LEFT JOIN boletines ON boletines.crecod=creditos.crecod AND pagare AND bolmon=0
		WHERE creditos.pac_id=pacientes.pac_id AND COALESCE(anulacion,'')='') AS total_creditos		 
		FROM pacientes
		WHERE 
		to_tsvector(pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) @@ to_tsquery('$query')
		OR
		(pac_rut) ILIKE '%$nombre%'
		ORDER BY pac_appat, pac_apmat, pac_nombres
			
	");

?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>R.U.T.</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<td>Cr&eacute;ditos</td>
<td>Total Cr&eacute;ditos</td>
</tr>

<?php 

	if($lista)
	for($i=0;$i<sizeof($lista);$i++) {
	
		($i%2==0)?$clase='tabla_fila':$clase='tabla_fila2';	
	
		echo "<tr class='$clase' style='height:30px;cursor:pointer;'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'
			onClick='abrir_cliente(".$lista[$i]['pac_id'].");'>
			<td style='text-align:right;font-weight:bold;'>
			".$lista[$i]['pac_rut']."</td>		
			<td style='font-weight:bold;'>".htmlentities($lista[$i]['pac_appat'])."</td>		
			<td>".htmlentities($lista[$i]['pac_apmat'])."</td>		
			<td style='font-weight:bold;'>".htmlentities($lista[$i]['pac_nombres'])."</td>		
			<td style='text-align:right;'>".$lista[$i]['creditos']."</td>		
			<td style='text-align:right;'>
			$".number_format($lista[$i]['total_creditos'],0,',','.').".-</td>		
		</tr>";	
	
	}

?>

</table>
