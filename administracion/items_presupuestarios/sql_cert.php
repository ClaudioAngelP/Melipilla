<?php 

	require_once('../../conectar_db.php');
	
	$cert_id=$_POST['cert_id']*1;
	
	$des=pg_escape_string(utf8_decode($_POST['cert_descripcion']));
	$noc=pg_escape_string(utf8_decode($_POST['cert_orden_compra']));
	$sig=pg_escape_string(utf8_decode($_POST['cert_cod_sigfe']));
	$obs=pg_escape_string(utf8_decode($_POST['cert_observaciones']));
	$res=pg_escape_string(utf8_decode($_POST['cert_resolucion']));
	$vis=pg_escape_string(utf8_decode($_POST['cert_vistos']));
	$con=pg_escape_string(utf8_decode($_POST['cert_considerando']));

	$detalle=json_decode($_POST['detalle'], true);
	
	$func_id=$_SESSION['sgh_usuario_id'];

	$tmonto=0;
	
	for($i=0;$i<sizeof($detalle);$i++) {
		
		$tmonto+=($detalle[$i]['certd_monto'])*1;
		
	}

	
	/*
	
	CREATE TABLE item_presupuestario_certificados
	(
	  cert_id bigserial NOT NULL,
	  cert_folio bigint,
	  func_id bigint,
	  cert_fecha timestamp without time zone,
	  cert_descripcion text,
	  cert_monto bigint,
	  cert_resolucion text,
	  cert_visto text,
	  cert_considerando text,
	  cert_observaciones text,
	  CONSTRAINT item_presupuestario_certificados_cert_id_key PRIMARY KEY (cert_id)
	)
	WITH (
	  OIDS=FALSE
	);
	
	*/ 
	 
	
	if($cert_id==0) {
	
		pg_query("INSERT INTO item_presupuestario_certificados VALUES (
			DEFAULT, COALESCE((SELECT MAX(cert_folio) FROM item_presupuestario_certificados)+1,1),
			$func_id, CURRENT_TIMESTAMP, '$des', $tmonto, '$res', '$vis', '$con', '$obs', '$noc', '$sig'
		);");

		$id=cargar_registro("SELECT CURRVAL('item_presupuestario_certificados_cert_id_seq') AS id;");
		
		$cert_id=$id['id']*1;
	
	} else {
		
		pg_query("
			UPDATE item_presupuestario_certificados SET
			cert_descripcion='$des',
			cert_orden_compra='$noc',
			cert_cod_sigfe='$sig',
			cert_monto=$tmonto,
			cert_resolucion='$res',
			cert_visto='$vis',
			cert_considerando='$con',
			cert_observaciones='$obs'
			WHERE cert_id=$cert_id;
		");
		
	}
	
	pg_query("DELETE FROM item_presupuestario_certificados_detalle WHERE cert_id=$cert_id;");
	
	for($i=0;$i<sizeof($detalle);$i++) {
		
		$certd_id=pg_escape_string($detalle[$i]['certd_id']*1);
		$item=pg_escape_string($detalle[$i]['certd_item']);
		$monto=($detalle[$i]['certd_monto'])*1;
		
			pg_query("
				INSERT INTO item_presupuestario_certificados_detalle VALUES (
					DEFAULT, $cert_id, '$item', $monto
				);
			");
		
	}

	print($cert_id);
?>
