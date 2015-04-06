<?php
	require_once('../../conectar_db.php');
	
	$paciente_id=$_POST['paciente_id']*1;
	
	$hosp_id=cargar_registro("SELECT hosp_id FROM hospitalizacion 
								WHERE hosp_pac_id=$paciente_id AND hosp_fecha_egr IS NULL 
								AND hosp_anulado=0
								ORDER BY hosp_fecha_egr DESC LIMIT 1");
	
	if($hosp_id){
		print(json_encode($hosp_id['hosp_id']));
	}else{
		print(json_encode(false));
	}
?>
