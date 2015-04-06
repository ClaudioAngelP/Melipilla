<?php require_once('../../conectar_db.php'); 
	
	$pac_id=$_GET['pac_id']*1;
	$fecha1=$_GET['rnpt_fecha'];
	$fecha2=$_GET['rnpt_fecha2'];
	
	$q=cargar_registro("
		SELECT *, rnpt_fecha_emision::date AS rnpt_fecha_emision FROM receta_npt
		JOIN pacientes USING (pac_id)
		JOIN doctores USING (doc_id)
		JOIN centro_costo USING (centro_ruta)
		WHERE pac_id=$pac_id AND rnpt_fecha_emision BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59'
	", true);

	print(json_encode($q));

?>
