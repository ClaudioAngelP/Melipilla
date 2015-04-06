<?php 

	error_reporting(E_NONE);

	$script=1;
	require_once('../../conectar_db.php');
	require_once('../../conectores/registro_civil/registrocivil.php');
	
	regcivil_login();
	
	ob_start();
	$datos=@regcivil_buscar($_GET['rut']);
	ob_end_clean();
	
	regcivil_logout();
	
	for($i=0;$i<4;$i++) {
		$datos[$i]=str_replace('/',' ',$datos[$i]);
		$datos[$i]=str_replace('=','',$datos[$i]);
	}
	
	for($i=0;$i<sizeof($datos);$i++) 
		$datos[$i]=htmlentities($datos[$i]);
		
	echo json_encode($datos);
	
?>
