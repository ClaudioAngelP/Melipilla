<?php 

	require_once('registrocivil.php');

	regcivil_login();
	
	regcivil_buscar('16000469-K');
	sleep(2);
	regcivil_buscar('15817659-9');
	sleep(2);
	regcivil_buscar('4804153-1');
	sleep(2);
	regcivil_buscar('16000469-K');
	sleep(2);
	regcivil_buscar('15817659-9');
	sleep(2);
	regcivil_buscar('4804153-1');
	sleep(2);
	
	regcivil_logout();

?>
