<?php require_once('../../conectar_db.php');
	
	$adjunto_id=$_GET['adjunto_id']*1;
	
	$c=cargar_registro("SELECT * FROM multa_adjuntos WHERE mad_id=$adjunto_id;");
	
	list($nombre,$tipo,$peso,$md5)=explode('|', $c['mad_adjunto']);
	
	header("Pragma: public");
	header('Content-disposition: attachment; filename="'.utf8_encode($nombre).'"');
	header("Content-type: ".$tipo);
	header('Content-Transfer-Encoding: binary');
	ob_clean();
	flush();
	
	readfile('adjunto_multas/'.$md5);

?>
