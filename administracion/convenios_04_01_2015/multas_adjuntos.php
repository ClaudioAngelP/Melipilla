<?php 

	require_once('../../conectar_db.php');
	
	if(isset($_POST['multa_id'])) {
	
		$func_id=$_SESSION['sgh_usuario_id']*1;
		$multa_id=$_POST['multa_id']*1;
		
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

		  move_uploaded_file($_FILES["archivo"]["tmp_name"], 'adjunto_multas/'.$md5);
		  		  
		  pg_query("INSERT INTO multa_adjuntos VALUES (DEFAULT, $multa_id, $func_id, CURRENT_TIMESTAMP, '$fname|$ftype|$fsize|$md5');");

		  print("<script>window.alert('Archivo enviado exitosamente.');window.close(); 
		  var fn=window.opener.listar_adjuntos($multa_id);
						fn();
						window.close();</script>");
		  
		  }
		  
		exit();
	
		
	}
	
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	$multa_id=$_GET['multa_id']*1;
	
	$f=cargar_registro("SELECT *, upper(convenio_nombre) AS convenio_nombre FROM convenio_multa JOIN convenio ON convm_convenio_id=convenio_id WHERE covnm_id=$multa_id;");
?>


<html>
<title>Adjuntar archivo a <?php echo $f['convenio_nombre']; ?></title>

<?php cabecera_popup('../../'); ?>

<script>


function adjuntar_archivo() {
	
	if($('archivo').value==''){
		alert('Debe seleccionar un archivo para subir');
		return;
	}else{	
		$('archivof').submit();
	}
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<form method='post' action='multas_adjuntos.php' id='archivof' name='archivof' enctype="multipart/form-data" onsubmit='return false;'>

<input type='hidden' id='multa_id' name='multa_id' value='<?php echo $multa_id; ?>' />

<table style='width:100%;font-size:18px;'>
	<tr>
		<td colspan=3 style='background-color:black;color:white;'>Adjuntar archivo a Multa de <b><u><?php echo $f['convenio_nombre']." (".$f['convenio_licitacion'].")"; ?></u></b></td>
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
