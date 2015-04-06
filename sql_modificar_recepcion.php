<?php 
    require_once('conectar_db.php');
    $doc_id=$_POST['doc_id']*1;
    $prov_id=$_POST['id_proveedor']*1;
    $doc_tipo=$_POST['doc_tipo']*1;
    $doc_num=$_POST['doc_num']*1;
    $doc_descuento=$_POST['doc_descuento']*1;
    $doc_observacion=pg_escape_string(trim(utf8_decode($_POST['doc_observacion'])));
    $orden_numero=pg_escape_string(trim(strtoupper($_POST['orden_numero'])));
    $log_id=$_POST['log_id']*1;
    
    $doc=cargar_registro("
    SELECT *, COALESCE(orden_numero, doc_orden_desc) AS _orden_numero, COALESCE(orden_id,doc_orden_id) AS _orden_id FROM documento 
    LEFT JOIN proveedor ON doc_prov_id=prov_id
    LEFT JOIN orden_compra ON doc_orden_id=orden_id
    WHERE doc_id=$doc_id
    ", true);
	
    $doc['detalle']=cargar_registros_obj("
    SELECT stock_id, art_codigo, art_glosa, art_vence, 
    forma_nombre, stock_cant, stock_vence, stock_subtotal, 0 as tipo
    FROM logs
    JOIN stock ON stock_log_id=log_id
    JOIN articulo ON stock_art_id=art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE log_doc_id=$doc_id
    UNION
    SELECT serv_id AS stock_id, '' AS art_codigo, serv_glosa AS art_glosa, 
    '0' AS art_vence, serv_unidad AS forma_nombre, serv_cant AS stock_cant, 
    null as stock_vence, serv_subtotal AS stock_subtotal, 1 as tipo
    FROM logs
    JOIN servicios ON serv_log_id=log_id
    WHERE log_doc_id=$doc_id
    ", true);
	
    $datos=pg_escape_string(json_encode($doc));
    pg_query("START TRANSACTION;");
    $chk=cargar_registro("SELECT * FROM orden_compra WHERE orden_numero='$orden_numero'");
    if($chk) {
        $orden_id=$chk['orden_id']*1;
        $orden_desc=$chk['orden_numero'];
    } else {
        $orden_id=0;
        $orden_desc=$orden_numero;	
    }
    $orden_1=$orden_desc;
    $orden_2=$doc['_orden_numero'];
    pg_query("UPDATE documento SET
    doc_prov_id=$prov_id,
    doc_tipo=$doc_tipo,
    doc_num=$doc_num,
    doc_descuento=$doc_descuento,
    doc_observacion='$doc_observacion',
    doc_orden_id=$orden_id,
    doc_orden_desc='$orden_desc'
    WHERE doc_id=$doc_id");
	
    $l=json_decode($_POST['listado'], true);
    $detalle_pedido = cargar_registros_obj("
    SELECT stock_id, art_codigo, art_glosa, art_vence, forma_nombre, stock_cant, stock_vence, stock_subtotal, 0 as tipo
    FROM logs
    JOIN stock ON stock_log_id=log_id
    JOIN articulo ON stock_art_id=art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    WHERE log_doc_id=$doc_id
    UNION
    SELECT serv_id AS stock_id, '' AS art_codigo, serv_glosa AS art_glosa, '0' AS art_vence, '' AS forma_nombre, serv_cant AS stock_cant, null as stock_vence, serv_subtotal AS stock_subtotal, 1 as tipo
    FROM logs
    JOIN servicios ON serv_log_id=log_id
    WHERE log_doc_id=$doc_id
    ");
    
    for($i=0;$i<sizeof($detalle_pedido);$i++) {
        $stock_id=$detalle_pedido[$i]['stock_id']*1;
	$tipo=$detalle_pedido[$i]['tipo']*1;
        if($stock_id==0)
            continue;
	$fnd=false;
        for($j=0;$j<sizeof($l);$j++) {
            if($l[$j]['stock_id']*1==$stock_id AND $l[$j]['tipo']*1==$tipo) {
                $fnd=true; break;
            }
        }
        if(!$fnd) {
            if($tipo==0)
                pg_query("DELETE FROM stock WHERE stock_id=$stock_id;");
            else
                pg_query("DELETE FROM servicios WHERE serv_id=$stock_id;");
        }
    }
    for($i=0;$i<sizeof($l);$i++) 
    {
        $stock_id=$l[$i]['stock_id']*1;
	$art_id=$l[$i]['art_id']*1;
	$art_glosa=pg_escape_string(utf8_decode($l[$i]['art_glosa']));
	$stock_cant=str_replace(',','.',$l[$i]['stock_cant'])*1;
	$stock_vence=$l[$i]['stock_vence'];
	$stock_subtotal=str_replace(',','.',$l[$i]['stock_subtotal'])*1;
		
	if($stock_vence!='')
            $stock_vence="'$stock_vence'";
        else
            $stock_vence='null';
		
        if($stock_id!=0) 
	{
            if($tipo==0)
                pg_query("UPDATE stock SET 
		stock_cant=$stock_cant,
		stock_vence=$stock_vence, 
		stock_subtotal=$stock_subtotal 
		WHERE stock_id=$stock_id;");
            else
                pg_query("UPDATE servicios SET 
		serv_glosa='$art_glosa',
		serv_cant=$stock_cant,
		serv_subtotal=$stock_subtotal 
		WHERE serv_id=$stock_id;");
        }
	else
	{
            if($tipo==0)
                pg_query("
		INSERT INTO stock VALUES (
		DEFAULT,
		$art_id,
		(SELECT log_bod_id FROM logs WHERE log_id=$log_id),
		$stock_cant,
		$log_id,
		$stock_vence,
		$stock_subtotal
		);
		");
        }
    }
    $mod=cargar_registro("SELECT docm_id FROM documento_modificaciones WHERE doc_id=$doc_id AND docm_fecha_realiza IS NULL;");
    $docm_id=$mod['docm_id'];
    pg_query("
    UPDATE documento_modificaciones SET 
    func_id2=".$_SESSION['sgh_usuario_id'].", 
    docm_fecha_realiza=CURRENT_TIMESTAMP, 
    docm_datos_previos='$datos'
    WHERE docm_id=$docm_id;
    ");
    
    pg_query("update orden_compra set orden_estado=foo2.estado_nuevo 
    from(
    select *,case when subtotal_recep=0 then 0
    when subtotal_orden>subtotal_recep then 1 
    else  2 end AS estado_nuevo  from
    (select orden_id, orden_numero,
    sum(COALESCE(ordetalle_subtotal,orserv_subtotal))AS subtotal_orden,
    (select COALESCE(sum(COALESCE(stock_subtotal,serv_subtotal)),0) from documento 
    left join logs on log_doc_id=doc_id
    left join stock on stock_log_id=log_id
    left join servicios on serv_log_id=log_id
    where doc_orden_id=orden_id
    )AS subtotal_recep,
    orden_estado
    from orden_compra
    left join orden_detalle on ordetalle_orden_id=orden_id
    left join orden_servicios on orserv_orden_id=orden_id
    WHERE orden_numero in ('$orden_1','$orden_2')
    group by orden_compra.orden_estado,orden_compra.orden_id,orden_compra.orden_numero
    )AS foo
    )AS foo2
    WHERE orden_compra.orden_id=foo2.orden_id
    "); 
    
    
    //RECALCULA LOS PRECIOS
    $arts_update=cargar_registros_obj("SELECT art_id
    FROM stock
    JOIN articulo ON stock_art_id=art_id
    LEFT JOIN bodega_forma ON art_forma=forma_id
    JOIN logs ON stock_log_id=log_id
    WHERE log_doc_id=$doc_id");
										
    for($i=0;$i<sizeof($arts_update);$i++) {
        $art_id1=$arts_update[$i]['art_id']*1;
	pg_query("UPDATE articulo SET art_val_min=foo.art_val_min, art_val_med=foo.art_val_med, art_val_max=foo.art_val_max 
        FROM (
            select art_id, art_codigo, art_glosa, 
            (select max(stock_subtotal/stock_cant) AS art_val_ult from stock where stock_art_id=art_id AND stock_subtotal>0) AS art_val_max, 
            (select min(stock_subtotal/stock_cant) AS art_val_min from stock where stock_art_id=art_id AND stock_subtotal>0) AS art_val_min, 
            (select avg(stock_subtotal/stock_cant) AS art_val_med from stock where stock_art_id=art_id AND stock_subtotal>0) AS art_val_med
            from articulo AS a1 WHERE art_id=$art_id1
	) AS foo where articulo.art_id=foo.art_id AND articulo.art_id=$art_id1;");

        pg_query("UPDATE articulo set art_val_ult=foo.art_val_ult FROM (
        select stock.stock_art_id, stock_subtotal/stock_cant AS art_val_ult from stock 
        join logs on stock_log_id=log_id
	join (
	select stock_art_id, max(log_fecha) AS max_log_fecha from stock 
	join logs on stock_log_id=log_id
	where stock_subtotal>0 AND stock_art_id=$art_id1 group by stock_art_id) AS buffer 
	ON buffer.stock_art_id=stock.stock_art_id AND max_log_fecha=log_fecha) AS foo
	WHERE art_id=$art_id1;");
    }				
    pg_query("COMMIT;");
    print(json_encode(true));
?>



