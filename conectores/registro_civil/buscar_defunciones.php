<?php 

	require_once('registrocivil.php');
	
	regcivil_login();
	
	$p=cargar_registros_obj("SELECT inter_pac_id FROM interconsulta WHERE inter_motivo_salida=0 AND pac_fc_nac<current_date-'50 years';");
	
	regcivil_logout();

?>
