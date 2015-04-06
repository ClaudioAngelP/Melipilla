<?php 

	require_once('../conectar_db.php');
	
	$art_id=$_POST['art_id']*1;
	$bod_id=$_POST['bod_id']*1;
	$pac_id=$_POST['pac_id']*1;
	
	$a=pg_query("
		SELECT *,
		COALESCE((
		SELECT SUM(stock_cant) FROM stock
		JOIN logs ON stock_log_id=log_id
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
		WHERE 
			stock_art_id=$art_id AND 
			(pedido.pedido_id IS NULL OR pedidod_estado OR origen_bod_id=0) AND 
			stock_bod_id=$bod_id
		),0) AS stock,
		upper(COALESCE(art_unidad_adm, forma_nombre)) AS art_unidad_administracion,
		COALESCE(art_unidad_cantidad, 1) AS art_unidad_cantidad_adm
		FROM articulo 
		LEFT JOIN bodega_forma ON art_forma=forma_id
		WHERE art_id=$art_id
	");
	
	$b=pg_query("
	
		select *, pow((((24/recetad_horas::real)*recetad_cant::real)/unidad::real)/despacho::real,-1) AS dias from (
		select log_fecha::date, stock_art_id, SUM(-stock_cant) AS despacho, 
		receta_cronica, recetad_horas, recetad_dias, recetad_cant, 
		(CURRENT_DATE-log_fecha::date) AS dias_trans, coalesce(art_unidad_cantidad,1) AS unidad
		from receta
		join recetas_detalle on recetad_receta_id=receta_id AND recetad_art_id=$art_id
		join articulo on recetad_art_id=art_id
		join logs on log_recetad_id=recetad_id AND log_fecha::date between (CURRENT_DATE-'1 month'::interval)::date and CURRENT_DATE
		join stock on log_id=stock_log_id AND stock_art_id=recetad_art_id
		WHERE receta_paciente_id=$pac_id
		GROUP BY log_fecha, stock_art_id, recetad_horas, recetad_dias, recetad_cant, receta_cronica, art_unidad_cantidad
		ORDER BY log_fecha::date DESC
		) as foo;

	
	");
	
	$d=pg_fetch_assoc($a);
	$d2=pg_fetch_assoc($b);
	
	foreach($d AS $key => $val) {
		$d[$key]=htmlentities($val);
	}
	
	if($d2) {
		foreach($d2 AS $key => $val) {
			$d[$key]=htmlentities($val);		
		}
	} 
	
	echo(json_encode($d));

?>
