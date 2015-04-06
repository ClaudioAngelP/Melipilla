<?php 

	require_once('../../conectar_db.php');
	
	$doc_id=$_POST['doc_id']*1;
	
	$chk=cargar_registro("SELECT * FROM documento_pagos WHERE doc_id=$doc_id");

	$func_id=$_SESSION['sgh_usuario_id'];
	
	if(!isset($_POST['aut'])) {

		$fecpago=pg_escape_string($_POST['fecpago']);
		$fpago=$_POST['fpago']*1;
		$numpago=$_POST['numpago']*1;
		
		if($chk) {
			pg_query("UPDATE documento_pagos SET docp_fecha='$fecpago', docp_tipo_pago=$fpago, docp_numero=$numpago, docp_func_id=$func_id, docp_fecha_pago=CURRENT_TIMESTAMP WHERE doc_id=$doc_id;");
		} else {
			pg_query("INSERT INTO documento_pagos VALUES ($doc_id, '$fecpago', $fpago, null, $numpago, $func_id, CURRENT_TIMESTAMP);");	
		}
	
	} else {

		$aut=($_POST['aut']*1);
		
		if($aut==0) {
			pg_query("UPDATE documento_pagos SET docp_autorizado=null, docp_func_id2=null, docp_fecha_autorizado=null WHERE doc_id=$doc_id;");
			exit();
		}
		
		if($chk) {
			pg_query("UPDATE documento_pagos SET docp_autorizado=$aut, docp_func_id2=$func_id, docp_fecha_autorizado=CURRENT_TIMESTAMP WHERE doc_id=$doc_id;");
		} else {
			pg_query("INSERT INTO documento_pagos VALUES ($doc_id, null, null, null, null, null, null, $aut, CURRENT_TIMESTAMP, $func_id);");	
		}
		
	}

?>
