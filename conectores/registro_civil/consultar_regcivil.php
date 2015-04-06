<?php 

	require_once('registrocivil.php');

	regcivil_login();
	
	regcivil_buscar($_GET['rut']);
	
	regcivil_logout();

?>
