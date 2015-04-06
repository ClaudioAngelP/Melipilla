<?php 

	require_once('../../conectar_db.php');
	
	$autf_id=$_POST['autf_id']*1;
	$pac_id=$_POST['pac_id']*1;
	
	pg_query("UPDATE autorizacion_farmacos_pacientes SET autfp_vigente=false, autfp_fecha_elimina=CURRENT_TIMESTAMP WHERE autf_id=$autf_id AND pac_id=$pac_id");

?>
