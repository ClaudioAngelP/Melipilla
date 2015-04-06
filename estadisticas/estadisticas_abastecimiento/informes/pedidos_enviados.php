<?php
    /*
    Nombre Informe: Pedidos y Envíos Por Bodega
    */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
    $tipos=Array(
    Array('0','<font size=+3>TOTAL</font>'),
    Array('1','<font size=+3>PARCIAL</font>'),
    Array('2','<font size=+3>SIN DESPACHO</font>'),
    Array('3','<font size=+3>FALTANTES</font>')
    );

    $campos=Array(
    Array('tipo','Filtro',10,-1,$tipos),
    Array('bodega','Ubicaci&oacute;n',11),
    Array('fecha1','Fecha de Inicio',1),
    Array('fecha2','Fecha de T&eacute;rmino',1)
    );
    /*
    [if %tipo==0] (enviado-pedidod_cant) AS pendiente, ((enviado-pedidod_cant)*art_val_ult) AS subtotal [/if]
    [if %tipo==1] (pedidod_cant-enviado) AS pendiente, ((pedidod_cant-enviado)*art_val_ult) AS subtotal [/if]
    [if %tipo==2] (pedidod_cant) AS pendiente, (pedidod_cant*art_val_ult) AS subtotal [/if] 
    [if %tipo==3] (pedidod_cant-enviado) AS pendiente, ((pedidod_cant-enviado)*art_val_ult) AS subtotal [/if] 
     * 
     */
    $query=
    "
    SELECT *,
    [if %tipo==0] (enviado-pedidod_cant) AS pendiente, (enviado*art_val_ult) AS subtotal,((enviado-pedidod_cant)*art_val_ult) AS subtotal_p [/if]
    [if %tipo==1] (pedidod_cant-enviado) AS pendiente, (enviado*art_val_ult) AS subtotal, ((pedidod_cant-enviado)*art_val_ult) AS subtotal_p [/if]
    [if %tipo==2] (pedidod_cant) AS pendiente,(enviado*art_val_ult) AS subtotal, (pedidod_cant*art_val_ult) AS subtotal_p [/if] 
    [if %tipo==3] (pedidod_cant-enviado) AS pendiente,(enviado*art_val_ult) AS subtotal, ((pedidod_cant-enviado)*art_val_ult) AS subtotal_p [/if] 
    FROM
    (
        SELECT * FROM 
        (
            select pedido_nro, 
            COALESCE(log_fecha::date, pedido_fecha::date) AS log_fecha, 
            art_codigo, art_glosa, pedidod_cant::bigint, -(COALESCE(SUM(stock_cant),0)) AS enviado,
            COALESCE(bod_glosa, centro_nombre) AS destino,
            round(art_val_ult::numeric,3) as art_val_ult,
		(
		case when trunc(round(art_val_ult::numeric,3))=art_val_ult then trunc(art_val_ult)::text
		else
		replace(round(art_val_ult::numeric,3)::text,'.',',')
		end
		)as art_val_ult2
            FROM pedido
            join pedido_detalle using (pedido_id)
            join articulo using (art_id)
            left join logs on log_id_pedido=pedido.pedido_id
            left join cargo_centro_costo USING (log_id)
            left join centro_costo ON cargo_centro_costo.centro_ruta=centro_costo.centro_ruta OR origen_centro_ruta=centro_costo.centro_ruta
            left join bodega on origen_bod_id=bod_id
            left join stock on stock_log_id=log_id AND stock_art_id=pedido_detalle.art_id AND stock_cant<0
            where pedido_fecha>='[%fecha1]' AND pedido_fecha<='[%fecha2]' AND destino_bod_id=[%bodega] AND pedido_estado<=2
            group by pedido_nro, log_fecha, pedido_fecha, art_codigo, art_glosa, pedidod_cant, bod_glosa, centro_nombre, art_val_ult
            order by pedido_nro
        ) AS foo 
        WHERE
        [if %tipo==0] pedidod_cant<=enviado [/if]
        [if %tipo==1] pedidod_cant>enviado AND enviado>0 [/if]
        [if %tipo==2] enviado=0 [/if]
        [if %tipo==3] pedidod_cant>enviado[/if]
        )
        AS foo;
    ";
    
    //print($query);
    

    $formato=Array(
    Array('pedido_nro','Numero Pedido',0,'center'),
    Array('log_fecha','Fecha Entrega',0,'center'),
    Array('destino','Ubicaci&oacute;n',0,'center'),
    Array('art_codigo','C&oacute;digo',0,'right'),
    Array('art_glosa','Art&iacute;culo',0,'left'),
    Array('pedidod_cant','Pedido',1,'right'),
    Array('enviado','Enviado',1,'right'),
    Array('pendiente','Pendiente',1,'right'),
    Array('art_val_ult2','P Unit($)',0,'right'),
    Array('subtotal','Subtotal Enviado($)',2,'right'),
    Array('subtotal_p','Subtotal Pendiente($)',2,'right')
    );
    
    ejecutar_consulta();
    
    $pie='
    <tr class="tabla_header" style="text-align:right;font-weight:bold;">
    <td colspan=9>Total:</td>
    <td>'.infoMONEY(infoSUM('subtotal')).'</td>
    <td>'.infoMONEY(infoSUM('subtotal_p')).'</td>
    </tr>
    ';
    procesar_formulario('Resumen de Art&iacute;culos Pedidos y Enviados');
?>