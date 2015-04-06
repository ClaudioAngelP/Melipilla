<?php 

	require_once('../../conectar_db.php');

	$cert_id=$_GET['cert_id']*1;
	
	$c=cargar_registro("
		SELECT * FROM item_presupuestario_certificados WHERE cert_id=$cert_id;
	");
	
	$tmp=cargar_registros_obj("
		SELECT * FROM item_presupuestario_certificados_detalle AS d1
		JOIN item_presupuestario_sigfe ON certd_item=item_codigo
		WHERE cert_id=$cert_id;
	");
	
	$items=array();
	
	for($i=0;$i<sizeof($tmp);$i++) {
		$items[$i][0]=$tmp[$i]['certd_item'];
		$items[$i][1]=$tmp[$i]['item_nombre'];
		$items[$i][2]=$tmp[$i]['certd_monto_lic'];
		$items[$i][3]=$tmp[$i]['certd_monto_adj'];
	}
	
	$c['items']=$items;
	
	echo json_encode($c);

?>
