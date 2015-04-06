<?php 

		session_start();

		if(!isset($_SESSION['sgh_usuario_id'])) exit('?');

		require_once('conectar_db.php');
		
		$id=$_SESSION['sgh_usuario_id']*1;
      
		$m=cargar_registro("SELECT COALESCE(count(*),0) AS msgs FROM chat WHERE func_id2=$id AND chat_estado=0;");
		
		echo $m['msgs']*1;
      
?>
