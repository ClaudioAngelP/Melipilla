<?php 

	require_once('../../conectar_db.php');
	
	$tipo=$_POST['tipo'];
	
	$nro=$_POST['nro']*1;
	
	if($tipo=='b') {
		$r=cargar_registro("SELECT * FROM boletines WHERE bolnum=$nro");
	} else {
		$r=cargar_registro("
						SELECT *, 
							(SELECT SUM(bolmon) FROM boletines 
							WHERE boletines.crecod=creditos.crecod) AS pagado
						FROM creditos 
						WHERE crecod=$nro");
	}

	echo json_encode($r);

?>