<?php

	require_once('../conectar_db.php');
	
	$pac_id=$_POST['pac_id']*1;
	$bod_id=$_POST['bodega_id']*1;
	
	$r=cargar_registro("
		SELECT * FROM receta 
		JOIN doctores ON receta_doc_id=doc_id
		WHERE receta_paciente_id=$pac_id
		AND receta_tipotalonario_id = 0
		ORDER BY receta_fecha_emision DESC;
	", true);
	
	if($r)
	$r['detalle']=cargar_registros_obj("
		SELECT *,
		COALESCE((
		SELECT SUM(stock_cant) FROM stock
		JOIN logs ON stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
		WHERE 
			stock_art_id=recetad_art_id AND 
			(pedido.pedido_id IS NULL OR pedidod_estado OR origen_bod_id=0) AND 
			stock_bod_id=$bod_id
		),0) AS stock,
		upper(forma_nombre) AS forma_nombre,
		upper(COALESCE(art_unidad_adm, forma_nombre)) AS art_unidad_adm,
		COALESCE(art_unidad_cantidad, 1) AS art_unidad_cantidad_adm 
		FROM recetas_detalle 
		JOIN articulo ON recetad_art_id=art_id
		LEFT JOIN bodega_forma ON art_forma=forma_id
		WHERE recetad_receta_id=".$r['receta_id']."
	", true);
	
	exit(json_encode($r));

?>
