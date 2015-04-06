<?php 

	require_once('../../conectar_db.php');
	
	$doc_id=$_POST['doc_id']*1;
	
	if(isset($_POST['numero'])) {
		
		$num=$_POST['numero']*1;
		
		$chk=cargar_registro("SELECT * FROM documento_pagos WHERE doc_id=$doc_id");
		
		if($chk) {
			pg_query("UPDATE documento_pagos SET docp_nro_factura=$numero WHERE doc_id=$doc_id;");
		} else {
			pg_query("INSERT INTO documento_pagos VALUES ($doc_id, (SELECT doc_fecha_recepcion::date+'30 days'::interval FROM documento WHERE doc_id=$doc_id), 0, $num, null, null, null);");	
		}
	
	}

	if(isset($_POST['orden_compra'])) {
		
		$num=pg_escape_string($_POST['orden_compra']);
		
		pg_query("UPDATE documento SET doc_orden_desc='$num' WHERE doc_id=$doc_id;");
		
	
	}

?>
