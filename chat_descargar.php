<?php 

	require_once('conectar_db.php');
	
	$chat_id=$_GET['chat_id']*1;
	
	$c=cargar_registro("SELECT * FROM chat WHERE chat_id=$chat_id;");
	
	list($nombre,$tipo,$peso,$md5)=explode('|', $c['chat_mensaje']);
	
	header("Pragma: public");
	header('Content-disposition: attachment; filename="'.utf8_encode($nombre).'"');
	header("Content-type: ".$tipo);
	header('Content-Transfer-Encoding: binary');
	ob_clean();
	flush();
	
	readfile('adjuntos_chat/'.$md5);

?>
