<?php 

	require_once('../../conectar_db.php');

	$pp_id=$_POST['pp_id']*1;
	
	pg_query("DELETE FROM personal_pabellon WHERE pp_id=$pp_id");
	
	$r=cargar_registros_obj("SELECT * FROM personal_pabellon ORDER BY pp_id",true);
	
	echo json_encode($r);

?>