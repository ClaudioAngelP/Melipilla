<?php
    require_once('../../conectar_db.php');
    $art_id=$_POST['art_id']*1;
    $bod_id=36;
    $consulta="
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
    ";
    
    $a=pg_query($consulta);
    $d=pg_fetch_assoc($a);
    foreach($d AS $key => $val)
    {
        $d[$key]=htmlentities($val);
    }
    echo(json_encode($d));
?>