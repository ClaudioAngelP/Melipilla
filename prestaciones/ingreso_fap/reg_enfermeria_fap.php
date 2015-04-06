<?php

	require_once('../../conectar_db.php');
	
	$fap_id=$_POST['fap_id']*1;
	
	if(isset($_POST['faprr_hora'])) {
		$hora=$_POST['faprr_hora'];
		$desc=$_POST['faprr_descripcion'];
		$func_id=$_SESSION['sgh_usuario_id']*1;
		
		pg_query("INSERT INTO fap_registro_recuperacion VALUES (DEFAULT, $fap_id, '$hora', '$desc', CURRENT_TIMESTAMP, $func_id);");
	}
	
	$reg=cargar_registros_obj("SELECT * FROM fap_registro_recuperacion JOIN funcionario USING (func_id) WHERE fap_id=$fap_id ORDER BY faprr_hora DESC;", true);
	
?>

<table style='width:100%;font-size:10px;'>
<tr class='tabla_header'><td style='width:20%;'>Hora</td><td>Descripci&oacute;n</td><td>&nbsp;</td></tr>
<tr class='tabla_fila2'><td><input type='text' id='faprr_hora' name='faprr_hora' value='<?php echo date('H:i'); ?>' style='text-align:center;font-weight:bold;width:100%;background-color:transparent;' /></td>
<td>
<input type='text' id='faprr_descripcion' name='faprr_descripcion' value='' style='width:100%;background-color:transparent;' onKeyUp='if(event.which==13) guardar_registro();' />
</td>
<td style='width:10%;'><center><img src='../../iconos/add.png' onClick='guardar_registro();' style='cursor:pointer;' /></center></td>
</tr>
<?php

	if($reg)
	for($i=0;$i<sizeof($reg);$i++) {
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("<tr class='$clase'>
		<td style='text-align:center;font-weight:bold;'>".substr($reg[$i]['faprr_hora'],0,5)."</td>
		<td>".$reg[$i]['faprr_descripcion']."</td>
		<td><center><img src='../../iconos/user.png' onClick='alert(\"Registrado el ".substr($reg[$i]['faprr_digitacion'],0,19)." por ".$reg[$i]['func_nombre']."\".unescapeHTML());' style='cursor:pointer;' /></center></td>
		</tr>");
	
	}

?>
</table>
