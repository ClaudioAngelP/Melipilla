<?php 

	require_once('../../conectar_db.php');
	
	if(isset($_POST['convenio_id'])) {
	
		$func_id=$_SESSION['sgh_usuario_id']*1;
		$convenio_id=$_POST['convenio_id']*1;
		
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

		  move_uploaded_file($_FILES["archivo"]["tmp_name"], 'adjuntos_convenio/'.$md5);
		  		  
		  pg_query("INSERT INTO convenio_adjuntos VALUES (DEFAULT, $convenio_id, $func_id, CURRENT_TIMESTAMP, '$fname|$ftype|$fsize|$md5');");

		  print("<script>window.alert('Archivo enviado exitosamente.');window.close(); </script>");
		  
		  }
		
		
		exit();
	
		
	}
	
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	$convenio_id=$_GET['convenio_id']*1;
	
	$f=cargar_registro("SELECT *, upper(convenio_nombre) AS convenio_nombre FROM convenio WHERE convenio_id=".$convenio_id);
	

?>


<html>
<title>Adjuntar archivo a <?php echo $f['convenio_nombre']; ?></title>

<?php cabecera_popup('../../'); ?>

<script>


function adjuntar_archivo() {
	
	$('archivo').submit();
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<form method='post' action='convenio_archivo.php' id='archivo' name='archivo' enctype="multipart/form-data" onsubmit='return false;'>

<input type='hidden' id='convenio_id' name='convenio_id' value='<?php echo $convenio_id; ?>' />

<table style='width:100%;font-size:18px;'>
	<tr>
		<td colspan=3 style='background-color:black;color:white;'>Adjuntar archivo a <b><u><?php echo $f['convenio_nombre']; ?></u></b></td>
	</tr>
	<tr>
		<td style='text-align:right;'>Archivo:</td>
		<td><input type='file' id='archivo' name='archivo' /></td>
		<td><i>(Peso M&aacute;x. 5 Mb)</i></td>
	</tr>
	<tr>
		<td colspan=3 style='background-color:black;'><center><input type='button' id='enviar' name='enviar' value='Adjuntar Archivo...' onClick='adjuntar_archivo();' /></center></td>
	</tr>
</table>

</form>


</body>

</html>
