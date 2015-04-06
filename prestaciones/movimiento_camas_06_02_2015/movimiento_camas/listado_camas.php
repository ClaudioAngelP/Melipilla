<?php 

	require_once('../../conectar_db.php');
	   
	$ucamas=cargar_registros_obj("SELECT * FROM hospitalizacion 
											JOIN pacientes ON pac_id=hosp_pac_id
											LEFT JOIN doctores ON doc_id=hosp_doc_id
											LEFT JOIN comunas USING (ciud_id)
											WHERE hosp_fecha_egr IS NULL", true);

	print(json_encode($ucamas));

?>