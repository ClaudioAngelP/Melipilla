<?php
    require_once('../conectar_db.php');
    $orden = $_GET['orden_numero'];
    //$oid = $_GET['orden_numero']*1;
    $orden=str_replace('*','%', $orden);
    $orden = pg_escape_string($orden);
    //$oid = pg_escape_string($oid);
    $obj = new stdClass();
  
    $cabecera = cargar_registros_obj("SELECT *,
    (SELECT COUNT(*) FROM orden_detalle WHERE ordetalle_orden_id=orden_id) AS articulos, 
    (SELECT COUNT(*) FROM orden_servicios WHERE orserv_orden_id=orden_id) AS servicios 
    FROM orden_compra WHERE orden_numero='$orden'");
    
    //print("SELECT * FROM orden_compra WHERE orden_numero='$orden'");
    //$cabecera = cargar_registros_obj("select * from orden_compra where orden_numero='".pg_escape_string($orden)."'$woid");
    //if(!$cabecera) die(json_encode(false));
    if($cabecera!=0)
    {
        $proveedor = cargar_registros_obj("SELECT * FROM proveedor WHERE prov_id=".($cabecera[0]['orden_prov_id']*1)."");
        if(!$proveedor)
        {
            $obj->proveedor=false;
        }
        else
        {
            $obj->proveedor=$proveedor; 
        }
        $recepcionados=false;
        $consulta="
        SELECT count(*) FROM (
        select art_id, COALESCE(ordetalle_cant, 1) AS ordetalle_cant,
        ordetalle_subtotal, art_codigo, art_glosa, art_vence, item_glosa,
        COALESCE(SUM(stock_cant),0) AS recepcionado,
        forma_nombre
        from orden_detalle 
        JOIN articulo ON ordetalle_art_id=art_id
        JOIN orden_compra ON ordetalle_orden_id=orden_id
        LEFT JOIN bodega_forma ON forma_id = art_forma
        LEFT JOIN item_presupuestario ON art_item=item_codigo
        LEFT JOIN documento ON doc_orden_id=orden_id
        LEFT JOIN logs on log_doc_id=doc_id
        LEFT JOIN stock on stock_log_id=log_id AND stock_art_id=ordetalle_art_id
        where ordetalle_orden_id=".$cabecera[0]['orden_id']." and stock_art_id=ordetalle_art_id
        GROUP BY articulo.art_id,orden_detalle.ordetalle_cant,orden_detalle.ordetalle_subtotal,item_presupuestario.item_glosa,bodega_forma.forma_nombre
        ) AS foo WHERE recepcionado=ordetalle_cant";
        
        $detalle = cargar_registros_obj($consulta,true);
        if($detalle)
        {
            if($detalle[0]['count']>0)
            {
                $recepcionados=$detalle;		
		$obj->recepcionado=$recepcionados;
		$consulta="select doc_id from documento where doc_orden_id=".$cabecera[0]['orden_id'];
                $reg_docs= cargar_registros_obj($consulta,true);
                if($reg_docs) {
                    $obj->documentos=$reg_docs;
                }
                else {
                    $obj->documentos=false;
                }
            } else {
                $obj->recepcionado=false;
            }
	} else {
            $obj->recepcionado=false;
	}
        
        $detalle = cargar_registros_obj("SELECT * FROM (
        SELECT art_id,  ordetalle_cant, ordetalle_subtotal,
        art_codigo, art_glosa, art_vence, item_glosa, COALESCE(SUM(stock_cant),0) AS recepcionado, forma_nombre FROM orden_detalle
        JOIN orden_compra ON ordetalle_orden_id=orden_id
        JOIN articulo ON ordetalle_art_id=art_id
        LEFT JOIN bodega_forma ON forma_id = art_forma
        LEFT JOIN item_presupuestario ON art_item=item_codigo
        LEFT JOIN documento ON doc_orden_id=orden_id
        LEFT JOIN logs on log_doc_id=doc_id
        LEFT JOIN stock on stock_log_id=log_id AND stock_art_id=ordetalle_art_id
        WHERE ordetalle_orden_id=".$cabecera[0]['orden_id']." AND ordetalle_art_id=art_id
        GROUP BY art_codigo, art_glosa, art_vence, item_glosa, art_id, forma_nombre, ordetalle_cant,ordetalle_subtotal) AS foo WHERE recepcionado<ordetalle_cant",true);
        if(!$detalle)
        {
            $obj->detalle=false;
        }
        else
        {
            $obj->detalle=$detalle; 
        }
        //$pedido= cargar_registros_obj("select * from orden_pedido where orden_id=".$cabecera[0]['orden_id']."");
        $pedido= cargar_registros_obj("select orden_pedido.*, pedido_nro from orden_pedido join pedido on orden_pedido.pedido_id=pedido.pedido_id where orden_pedido.orden_id=".$cabecera[0]['orden_id']." and orden_pedido.pedido_id<>0");
        if(!$pedido)
        {
            $obj->pedido=false;
        }
        else
        {
            $obj->pedido=$pedido;

        }
        $pedido_detalle=cargar_registros_obj("select orden_pedido.*, pedido_detalle.*, pedido_nro from orden_pedido join pedido_detalle on orden_pedido.pedido_id=pedido_detalle.pedido_id join pedido on pedido_detalle.pedido_id=pedido.pedido_id where orden_pedido.orden_id=".$cabecera[0]['orden_id']."");
            if(!$pedido_detalle)
            {
               $obj->detallep=false;
            }
            else
            {
                $obj->detallep=$pedido_detalle;
            }
            $obj->cabecera=$cabecera;
            echo(json_encode($obj));
    }
    else
    {
        $orden=$orden;
        $cabecera = cargar_registros_obj("select *,
        (SELECT COUNT(*) FROM orden_detalle WHERE ordetalle_orden_id=orden_id) AS articulos, 
        (SELECT COUNT(*) FROM orden_servicios WHERE orserv_orden_id=orden_id) AS servicios 
        from orden_compra where orden_numero='$orden'");
        if($cabecera!=0)
        {
            $proveedor = cargar_registros_obj("SELECT * FROM proveedor WHERE prov_id=".($cabecera[0]['orden_prov_id']*1)."");
            if(!$proveedor)
            {
                $obj->proveedor=false;
            }
            else
            {
                $obj->proveedor=$proveedor;
            }
            $detalle = cargar_registros_obj("
            SELECT * FROM (
            SELECT art_id, ordetalle_cant, ordetalle_subtotal,
            art_codigo, art_glosa, art_vence, item_glosa, COALESCE(SUM(stock_cant),0) AS recepcionado FROM orden_detalle
            JOIN orden_compra ON ordetalle_orden_id=orden_id
            JOIN articulo ON ordetalle_art_id=art_id
            LEFT JOIN item_presupuestario ON art_item=item_codigo
            LEFT JOIN documento ON doc_orden_id=orden_id
            LEFT JOIN logs on log_doc_id=doc_id
            LEFT JOIN stock on stock_log_id=log_id AND stock_art_id=ordetalle_art_id
            WHERE ordetalle_orden_id=".$cabecera[0]['orden_id']." AND ordetalle_art_id=art_id
            GROUP BY  art_codigo, art_glosa, art_vence, item_glosa, art_id, ordetalle_cant,ordetalle_subtotal) AS foo WHERE recepcionado<ordetalle_cant",true);   
            
            if(!$detalle)
            {
                $obj->detalle=false;
            }
            else
            {
                $obj->detalle=$detalle;
            }
            $pedido= cargar_registros_obj("select orden_pedido.*, pedido_nro from orden_pedido join pedido on orden_pedido.pedido_id=pedido.pedido_id where orden_pedido.orden_id=".$cabecera[0]['orden_id']." and orden_pedido.pedido_id<>0");
            if(!$pedido)
            {
                $obj->pedido=false;
            }
            else
            {
                $obj->pedido=$pedido;
            }
            $pedido_detalle=cargar_registros_obj("select orden_pedido.*, pedido_detalle.*, pedido_nro from orden_pedido join pedido_detalle on orden_pedido.pedido_id=pedido_detalle.pedido_id join pedido on pedido_detalle.pedido_id=pedido.pedido_id where orden_pedido.orden_id=".$cabecera[0]['orden_id']."");
            if(!$pedido_detalle)
            {
               $obj->detallep=false;
            }
            else
            {
                $obj->detallep=$pedido_detalle;
            }
            $obj->cabecera=$cabecera;
            echo(json_encode($obj));
        }
        else
        {
            die(json_encode(false));
        }

    }
?>
