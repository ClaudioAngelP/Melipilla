<?php 

	require_once('conectar_db.php');
	
	if(isset($_POST['func_id'])) {
	
		$func_id=$_SESSION['sgh_usuario_id']*1;
		$func_id2=$_POST['func_id']*1;
		
		if ($_FILES["archivo"]["error"] > 0)
		  {
		  
		  //echo "Error: " . $_FILES["archivo"]["error"] . "<br />";

		  print("<script>window.alert('Error al enviar el archivo.');window.close();</script>");
		  
		  }
		else
		  {
			  
		  //echo "Archivo: " . $_FILES["archivo"]["name"] . "<br />";
		  //echo "Tipo: " . $_FILES["archivo"]["type"] . "<br />";
		  //echo "Tama&ntilde;o: " . number_format($_FILES["archivo"]["size"] / 1024,3,',','.') . " Kb<br />";
		  
		  $fname=$_FILES["archivo"]["name"];
		  $ftype=$_FILES["archivo"]["type"];
		  $fsize=$_FILES["archivo"]["size"];
		  
		  $md5=md5_file($_FILES["archivo"]["tmp_name"]);

		  move_uploaded_file($_FILES["archivo"]["tmp_name"], 'adjuntos_chat/'.$md5);
		  		  
		  pg_query("INSERT INTO chat VALUES (DEFAULT, $func_id, $func_id2, CURRENT_TIMESTAMP, '$fname|$ftype|$fsize|$md5', 0, 1);");

		  print("<script>window.alert('Archivo enviado exitosamente.');window.close();</script>");
		  
		  }
		
		
		exit();
	
		
	}
	
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	$func_id2=$_GET['func_id']*1;
	
	$f=cargar_registro("SELECT *, upper(func_nombre) AS func_nombre2 FROM funcionario WHERE func_id=".$func_id2);
	

?>


<html>
<title>Enviar archivo a <?php echo $f['func_nombre2']; ?></title>

<?php cabecera_popup('.'); ?>

<script>


function enviar_archivo() {
	
	$('archivo').submit();
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<form method='post' action='chat_archivo.php' id='archivo' name='archivo' enctype="multipart/form-data" onsubmit='return false;'>

<input type='hidden' id='func_id' name='func_id' value='<?php echo $func_id2; ?>' />

<table style='width:100%;font-size:18px;'>
	<tr>
		<td colspan=3 style='background-color:black;color:white;'>Enviar archivo a <b><u><?php echo $f['func_nombre2']; ?></u></b></td>
	</tr>
	<tr>
		<td style='text-align:right;'>Archivo:</td>
		<td><input type='file' id='archivo' name='archivo' /></td>
		<td><i>(Peso M&aacute;x. 5 Mb)</i></td>
	</tr>
	<tr>
		<td colspan=3 style='background-color:black;'><center><input type='button' id='enviar' name='enviar' value='Enviar Archivo...' onClick='enviar_archivo();' /></center></td>
	</tr>
</table>

</form>


</body>

</html>
