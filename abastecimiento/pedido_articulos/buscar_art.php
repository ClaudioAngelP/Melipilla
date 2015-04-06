<?php
    require_once('../../conectar_db.php');
    $origen=$_GET['origen'];
    $destino=$_GET['destino'];
    $art_id=$_GET['art_id']*1;
    //--------------------------------------------------------------------------
    if(!$destino*1)
        $destino='null';
    //--------------------------------------------------------------------------
    if(!strstr($origen,'.'))
    {
        $b=cargar_registro("SELECT bod_id,'' AS centro_ruta FROM bodega WHERE bod_id=$origen");
    }
    else
    {
        $b=cargar_registro("SELECT 0 AS bod_id,centro_ruta FROM centro_costo WHERE centro_ruta='$origen'");
    }
    //--------------------------------------------------------------------------
    if($b['centro_ruta']!='')
        $centro_ruta="origen_centro_ruta='".$b['centro_ruta']."'";
    else
        $centro_ruta=" (origen_centro_ruta='".$b['centro_ruta']."' OR origen_centro_ruta IS NULL)";
    //--------------------------------------------------------------------------
    $consulta="SELECT * 
    FROM (
    SELECT *,(pedidod_cant-enviado) AS pendiente FROM (select pedido_id As pedido_nro, 
    art_id,art_codigo, art_glosa, forma_nombre AS art_forma, pedidod_cant::bigint AS pedidod_cant, (COALESCE(SUM(-stock_cant),0)) AS enviado,
    COALESCE(bod_glosa, centro_nombre) AS origen 
    FROM pedido
    join pedido_detalle using (pedido_id)
    join articulo using (art_id)
    left join logs on log_id_pedido=pedido.pedido_id
    left join cargo_centro_costo USING (log_id)
    left join centro_costo ON cargo_centro_costo.centro_ruta=centro_costo.centro_ruta OR origen_centro_ruta=centro_costo.centro_ruta
    left join bodega on origen_bod_id=bod_id
    left join stock on stock_log_id=log_id AND stock_art_id=pedido_detalle.art_id AND stock_cant<0
    left join bodega_forma ON forma_id=art_forma
    where destino_bod_id=$destino AND pedido_estado=0
    AND origen_bod_id=".$b['bod_id']." AND $centro_ruta
    group by pedido_id,art_codigo, art_glosa,art_id,forma_nombre,pedidod_cant, bod_glosa, centro_nombre, art_val_ult
    order by art_glosa DESC) AS foo 
    )
    AS foo2 WHERE pendiente>0 and art_id=$art_id";
    $l=cargar_registros_obj($consulta);
    if(!$l)
    {
       $l=false;
    }
    print(json_encode(Array($l)));
    //--------------------------------------------------------------------------
?>