<?php 

	require_once('../../conectar_db.php');

	$cama_id=$_POST['cama_id']*1;

	$q=cargar_registro("SELECT * FROM bloqueo_camas
					JOIN bloqueo_camas_motivos ON bloq_motivo=bmot_id
					JOIN funcionario USING (func_id)
					LEFT JOIN tipo_camas ON
					cama_num_ini<=bloq_numero_cama AND cama_num_fin>=bloq_numero_cama
					LEFT JOIN clasifica_camas ON 
					tcama_num_ini<=bloq_numero_cama AND tcama_num_fin>=bloq_numero_cama	
				  WHERE bloq_numero_cama = $cama_id					
				  AND 
				  bloq_fecha_ini<=CURRENT_DATE AND 
				  (
					bloq_fecha_fin IS NULL OR 
					bloq_fecha_fin>=CURRENT_DATE
				  )
				  ", true);
				
	if($q) {

		exit(json_encode(array(false, $q)));	
		
	}

  
	$q=cargar_registro("SELECT * FROM hospitalizacion 
				  JOIN pacientes ON pac_id=hosp_pac_id					 
				  WHERE hosp_numero_cama = $cama_id					
				 AND hosp_fecha_egr is null", true);
				
	echo json_encode(array(true, $q));	

?>
