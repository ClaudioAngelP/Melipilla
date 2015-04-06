<?php 

	require_once('../../conectar_db.php');
	
	$id_boletin=$_POST['nboletin']*1;
	
	$r=cargar_registro("SELECT * FROM boletines JOIN pacientes USING (pac_id) where bolnum =$id_boletin");
	$p=cargar_registro("SELECT* FROM apertura_cajas JOIN boletines using(func_id) WHERE bolnum=$id_boletin AND bolfec BETWEEN ac_fecha_apertura AND ac_fecha_cierre");
	



	$arr[0]=$r;
	$arr[1]=$p;

	
	print(json_encode($arr));
?>
