<?php require_once('../../conectar_db.php'); 
	
	$rnpt_id=$_GET['npt_id']*1;
	
	$tipo=$_GET['tipo'];
	
	$nro_busca=$_GET['nro_busca'];
	
	if($tipo==0){
		$q=cargar_registro("
			SELECT *, rnpt_fecha_emision::date AS rnpt_fecha_emision FROM receta_npt
			JOIN pacientes USING (pac_id)
			JOIN doctores USING (doc_id)
			JOIN centro_costo USING (centro_ruta)
			WHERE rnpt_id=$rnpt_id;
		", true);

		print(json_encode($q));
	}
	
	if($tipo==1){
		
		$q=cargar_registro("
			SELECT *, rnpt_fecha_emision::date AS rnpt_fecha_emision,
			func2.func_nombre As func2_nombre,func2.func_rut AS func2_rut
			FROM receta_npt
			JOIN pacientes USING (pac_id)
			JOIN doctores USING (doc_id)
			JOIN centro_costo USING (centro_ruta)
			JOIN funcionario AS func2 ON func_id=rnpt_func_id2
			WHERE  rnpt_estado>0 and rnpt_fecha_recep IS NOT NULL AND rnpt_doc_num=$nro_busca
		", true);
		
		print(json_encode($q));
		
	}
?>
