<?php 

	require_once('../../conectar_db.php');
	
	$id_boletin=$_POST['str']*1;
	
	$r=cargar_registro("SELECT * FROM boletines JOIN pacientes USING (pac_id) where bolnum =$id_boletin");
	$p=cargar_registro("select sum(monto_total)*-1 as monto_total from devolucion_boletines where bolnum=$id_boletin");
	$g=cargar_registros_obj("select* from boletin_detalle where bdet_id not in (select bdet_id from devolucion_boletin_detalle where bolnum=$id_boletin) and bolnum=$id_boletin");
	
	



	$arr[0]=$r;
	$arr[1]=$p;
	$arr[2]=$g;
	
	print(json_encode($arr));
?>
