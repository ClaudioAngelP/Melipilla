<?php
	require_once('../../conectar_db.php');
	$encontrado=false;
  	$id=($_GET['medico_id']*1);
	$rut=iconv("UTF-8", "ISO-8859-1", $_GET['medico_rut']);
	$paterno=iconv("UTF-8", "ISO-8859-1", $_GET['medico_paterno']);
	$materno=iconv("UTF-8", "ISO-8859-1", $_GET['medico_materno']);
	$nombre=iconv("UTF-8", "ISO-8859-1", $_GET['medico_nombre']);
	$fono=iconv("UTF-8", "ISO-8859-1", $_GET['medico_fono']);
	$mail=iconv("UTF-8", "ISO-8859-1", $_GET['medico_mail']);
	if($id!=0)
	{
		// Edición de Médico
		pg_query($conn, "
		UPDATE doctores
		SET
		doc_rut='".pg_escape_string($rut)."',
		doc_paterno='".pg_escape_string($paterno)."',
		doc_materno='".pg_escape_string($materno)."',
		doc_nombres='".pg_escape_string($nombre)."',
		doc_fono='".pg_escape_string($fono)."',
		doc_mail='".pg_escape_string($mail)."'
		WHERE doc_id=$id");
	}
	else
	{
		$rut=pg_escape_string(str_replace(".","",$rut));
		$consulta="SELECT * FROM doctores WHERE doc_rut='$rut' LIMIT 1";
		
		
		$tmp=cargar_registros_obj($consulta, true);
		if($tmp)
	       {
			$encontrado=true;
		}
		else
		{
			// Ingreso de Médico nuevo
			pg_query($conn, "
			INSERT INTO doctores
			VALUES (
			DEFAULT,
			'".pg_escape_string(str_replace(".","",$rut))."',
			'".pg_escape_string($paterno)."',
			'".pg_escape_string($materno)."',
			'".pg_escape_string($nombre)."',
			'".pg_escape_string($fono)."',
			'".pg_escape_string($mail)."',
			NULL)");
		}
	}
	if($encontrado)
	{
		print('0');
	}
	else
	{
		print('1');
	}
?>
