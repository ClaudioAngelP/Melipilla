<?php 

	require_once('../../conectar_db.php');

	$nomda_id=$_GET['nomda_id']*1;
	
	$f=cargar_registro("SELECT * FROM nomina_detalle_adjuntos WHERE nomda_id=$nomda_id");
	
	$archivo='../../ficha_clinica/adjuntos/'.$f['nomda_archivo'];
	$nombre=$f['nomda_nombre'];

	if (file_exists($archivo)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($nombre).'";');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($archivo));
		ob_clean();
		flush();
		readfile($archivo);
		exit;
	}

?>
