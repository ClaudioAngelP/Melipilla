<?php
    /*
    Nombre Informe: Devoluciones desde servicios por bodega
    Entrega informacion de las devoluciones recibidas desde un determinado servicio
    entre un periodo de tiempo.
    Soluciones Computacionales
    Viña del mar
    */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
    
    $valor=Array(
    Array(1,    'Valor M&iacute;nimo'),
    Array(2,    'Valor Medio'),
    Array(3,    'Valor M&aacute;ximo'),
    Array(4,    '&Uacute;ltimo Valor',)
    );

    $campos=Array(
    Array('bodega','Ubicaci&oacute;n',0),
    Array('fecha1','Fecha de Inicio',1),
    Array('fecha2','Fecha de T&eacute;rmino',1),
    Array('tipo','Valorizaci&oacute;n Seg&uacute;n',10,-1,$valor)
    );

    $query=
    "
    SELECT
    count(*) as conta,
    DATE_TRUNC('day',log_fecha)::date as fecha,
    art_codigo,
    art_glosa,
    forma_nombre,
    ABS(SUM(stock_cant)) as cant_entregada,
    centro_nombre,
    log_comentario as comentario,
    [if %tipo==1]
        ((ABS(SUM(stock_cant))*art_val_min)) as valor [/if]
    [if %tipo==2]
        ((ABS(SUM(stock_cant))*art_val_med)) as valor [/if]
    [if %tipo==3]
        ((ABS(SUM(stock_cant))*art_val_max)) as valor [/if]
    [if %tipo==4]
        ((ABS(SUM(stock_cant))*art_val_ult)) as valor [/if]
    ,
    item_glosa
    FROM logs
    INNER JOIN stock on stock_log_id=log_id
    LEFT JOIN articulo on art_id=stock_art_id
    LEFT JOIN bodega_forma on forma_id=art_forma
    LEFT JOIN item_presupuestario on item_codigo=art_item
    INNER JOIN cargo_centro_costo as cc on cc.log_id=logs.log_id
    INNER JOIN centro_costo on centro_costo.centro_ruta=cc.centro_ruta
    WHERE
    (
        DATE_TRUNC('day', log_fecha)>='[%fecha1]' AND
        DATE_TRUNC('day', log_fecha)<='[%fecha2]'
    ) AND
    stock_bod_id=[%bodega] AND
    log_tipo=16
    GROUP BY art_codigo,art_glosa,forma_nombre,logs.log_fecha,centro_nombre,logs.log_comentario,
    [if %tipo==1] art_val_min [/if]
             [if %tipo==2] art_val_med [/if]
             [if %tipo==3] art_val_max [/if]
             [if %tipo==4] art_val_ult [/if],
    item_glosa
    ORDER BY log_fecha ;
    ";
    
    

    $formato=Array(
    Array('fecha',            'Fecha',                  0, 'center'),
    Array('art_codigo',       'C&oacute;digo',          0, 'center'),
    Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
    Array('item_glosa',       'Item Presupuestario',    0, 'left'),
    Array('forma_nombre',     'Forma',                  0, 'left'),
    Array('cant_entregada',   'Cant.',                  1, 'right'),
    Array('valor',            'Valorizado $',           3, 'right'),
    Array('centro_nombre',    'Servicio',               0, 'left'),
    Array('comentario',       'Comentario',             0, 'left')
    );
    
    ejecutar_consulta();
    $pie='
    <tr class="tabla_header" style="text-align:right;font-weight:bold;">
    <td colspan=7>Total Devoluciones:</td>
    <td>'.number_format(contador('conta'),0,',','.').'</td>
    </tr>
    <tr class="tabla_header" style="text-align:right;font-weight:bold;">
    <td colspan=7>Valor Total:</td>
    <td>$'.number_format(infoSUM('valor'),0,',','.').'.-</td>
    </tr>
    ';
    
    
    
    
    procesar_formulario('Devoluciones desde Servicios');
?>