<?php 

	require_once('../../conectar_db.php');
	
	$glosa=pg_escape_string(html_entity_decode(utf8_decode($_POST['glosa'])));
	$art_id=$_POST['art_id']*1;
	
	$chk=pg_query("SELECT * FROM articulo_nombres WHERE artn_nombre='$glosa';");
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	if(pg_fetch_assoc($chk)) {
		
		$artn_id=$chk['artn_id']*1;
		
		pg_query("DELETE FROM articulo_nombres WHERE artn_id=$artn_id;");
		
		pg_query("INSERT INTO articulo_nombres VALUES (DEFAULT, $art_id, '$glosa', $func_id);");
		
	} else {
		
		pg_query("INSERT INTO articulo_nombres VALUES (DEFAULT, $art_id, '$glosa', $func_id);");
		
	}
	
	pg_query("START TRANSACTION;");
	
	pg_query("
		INSERT INTO orden_detalle (ordetalle_orden_id, ordetalle_art_id, ordetalle_cant, ordetalle_subtotal)
		SELECT orserv_orden_id, $art_id, orserv_cant, orserv_subtotal FROM orden_servicios 
		WHERE orserv_glosa='$glosa';
	");
	
	pg_query("DELETE FROM orden_servicios WHERE orserv_glosa='$glosa';");
	
	pg_query("COMMIT;");
	
	

?>
