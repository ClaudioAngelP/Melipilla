<?php 

	require_once('../../conectar_db.php');
	
	$centro_ruta=pg_escape_string($_POST['centro_ruta']);
	$ppto_item=pg_escape_string($_POST['ppto_item']);
	$monto=$_POST['ppto_monto']*1;
	
	list($mes, $anio) = explode('/', $_POST['mesanio']);
	
	$f1=date('d/m/Y', mktime(0,0,0,$mes*1,1,$anio*1));
	$f2=date('d/m/Y', mktime(0,0,0,($mes*1)+1,1,$anio*1));

	$chk=cargar_registro("SELECT * FROM centro_costo_presupuesto WHERE centro_ruta='$centro_ruta' AND ppto_item='$ppto_item' AND ppto_fecha='$f1'");
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	if($chk) {
	
		pg_query("
			UPDATE centro_costo_presupuesto SET
			ppto_monto=$monto,
			ppto_func_id=$func_id,
			ppto_fecha_asigna=CURRENT_TIMESTAMP
			WHERE centro_ruta='$centro_ruta' AND ppto_item='$ppto_item' AND ppto_fecha='$f1'
		");
	
	} else {

		pg_query("
			INSERT INTO centro_Costo_presupuesto VALUES (
				'$centro_ruta', '$f1', '$ppto_item', $monto, $func_id, CURRENT_TIMESTAMP
			);
		");
	
	}
	
	
?>
