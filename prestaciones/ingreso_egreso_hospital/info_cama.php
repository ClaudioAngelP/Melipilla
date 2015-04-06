<?php 

	require_once('../../conectar_db.php');

	$n=$_POST['num_cama']*1;
	
	$i=cargar_registros_obj("SELECT cama_tipo, tcama_tipo, hosp_id FROM tipo_camas 
					JOIN clasifica_camas ON tcama_num_ini<=$n AND tcama_num_fin>=$n
					LEFT JOIN hospitalizacion ON 
						hosp_numero_cama=$n AND 
						hosp_fecha_egr IS NULL
					WHERE cama_num_ini<=$n AND cama_num_fin>=$n", true);

	if($i) $i=$i[0];

	echo json_encode($i);

?>