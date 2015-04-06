<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');

	set_time_limit(0);

	$pacs=cargar_registros_obj("
	
	select distinct pac_rut from recetas_detalle
	join logs on log_recetad_id=recetad_id AND log_fecha>=(CURRENT_DATE-('24 hours'::interval))
	join receta on recetad_receta_id=receta_id
	join pacientes on receta_paciente_id=pac_id;

	");


	for($i=0;$i<sizeof($pacs);$i++) {

		$_GET['rut']=$pacs[$i]['pac_rut'];
		$rut=$pacs[$i]['pac_rut'];

		$chk=cargar_registro("SELECT * FROM pacientes_fonasa WHERE pac_rut='$rut' AND cert_fecha>CURRENT_DATE-('1 month'::interval);");
		
		if($chk) continue;
		
		ob_start();
		require('../conectores/fonasa/fonasa_certificador_previsional.php');
		$ret=ob_get_contents();
		ob_end_clean();
		
	}
	
	

?>
