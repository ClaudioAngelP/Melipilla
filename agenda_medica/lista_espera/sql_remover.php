<?php 

	require_once('../../conectar_db.php');

	if(isset($_POST['inter_id']))	
		$inter_id=$_POST['inter_id']*1;
	else 
		$oa_id=$_POST['oa_id']*1;
		
	$inter_salida=$_POST['inter_salida']*1;
	$inter_fecha_salida=pg_escape_string($_POST['inter_fecha_salida']);
	
	if(isset($inter_id))
		pg_query("
			UPDATE interconsulta SET
			inter_estado=2,
			inter_motivo_salida=$inter_salida, 
			inter_fecha_salida='$inter_fecha_salida',
			func_id3=".$_SESSION['sgh_usuario_id']."
			WHERE inter_id=$inter_id	
		");
	else 
		pg_query("
			UPDATE orden_atencion SET
			oa_estado=2,
			oa_motivo_salida=$inter_salida, 
			oa_fecha_salida='$inter_fecha_salida',
			func_id2=".$_SESSION['sgh_usuario_id']."
			WHERE oa_id=$oa_id	
		");
	

?>
