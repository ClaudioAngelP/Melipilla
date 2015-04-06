<?php

	require_once('../../conectar_db.php');
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	$ac=cargar_registro("SELECT * FORM apertura_cajas WHERE func_id=$func_id AND ac_fecha_apertura::date=CURRENT_DATE AND ac_fecha_cierre IS NULL;");

	if(!$ac) {
		pg_query("INSERT INTO apertura_cajas VALUES (DEFAULT, $func_id, CURRENT_TIMESTAMP, null);");
	}

?>