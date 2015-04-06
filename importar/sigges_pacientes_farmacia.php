<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');

	set_time_limit(0);

	$pacs=cargar_registros_obj("
	
	select distinct pac_rut from pacientes where pac_id in(
	select distinct pac_id from autorizacion_farmacos_pacientes
	) and not pac_rut='' AND not pac_rut='0-0';

	");


	for($i=0;$i<sizeof($pacs);$i++) {

		$_GET['rut']=$pacs[$i]['pac_rut'];
		$rut=$pacs[$i]['pac_rut'];

		$chk=cargar_registro("SELECT * FROM pacientes_sigges WHERE pac_rut='$rut'");
		
		if($chk) continue;
		
		ob_start();
		require('../conectores/sigges/descargar_paciente.php');
		$json=ob_get_contents();
		ob_end_clean();
	
		print($json."\n\n");
	
	}
	
	

?>
