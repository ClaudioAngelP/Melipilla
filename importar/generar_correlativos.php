<?php 

	require_once('../conectar_db.php');
	
	pg_query("UPDATE logs SET log_folio=NULL WHERE log_folio IS NOT NULL;");
	
	$r=cargar_registros_obj("SELECT * FROM logs WHERE log_tipo=1 ORDER BY log_fecha;");

	for($i=0;$i<sizeof($r);$i++) {
		pg_query("UPDATE logs SET log_folio=max_folio_recep(".$r[$i]['log_bod_id'].") WHERE log_id=".$r[$i]['log_id']);
	}

?>
