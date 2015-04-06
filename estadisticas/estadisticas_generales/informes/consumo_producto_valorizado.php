<?php
    /*
    Nombre Informe: Consumo valorizado por Articulo
    Entrega informacion del consumo por articulo,
    ya sea por despachos,  recetas o traslados.
    Soluciones Computacionales
    Viña del mar
    */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
    $valor=Array(
    Array(-1,   'Valor minimo'),
    Array(2,    'Valor medio'),
    Array(3,    'Valor maximo'),
    Array(4,    'Ultimo valor',)
    );

    $campos=Array(
    Array(  'bodega', 'Ubicaci&oacute;n',           0   ),
    Array(  'fecha1', 'Fecha de Inicio',            1   ),
    Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   ),
    Array(  'cod',    'Art&iacute;culo',            2   ),
    Array(  'tipo',   'Valorizaci&oacute;n Seg&uacute;n',   10  ,   -1,   $valor)
    );


    $query=
    "
    SELECT
    art_codigo,
    art_glosa,
    forma_nombre,
    ABS(SUM(stock_cant)) as cant_despachada,
    [if %tipo==-1]
        ((sum(ABS(stock_cant))* art_val_min)) as subtotal, art_val_min AS punit [/if]
    [if %tipo==2]
        ((sum(ABS(stock_cant))* art_val_med)) as subtotal, art_val_med AS punit [/if]
    [if %tipo==3]
        ((sum(ABS(stock_cant))* art_val_max)) as subtotal, art_val_max AS punit [/if]
    [if %tipo==4]
        ((sum(ABS(stock_cant))* art_val_ult)) as subtotal, art_val_ult AS punit [/if]
    FROM stock
    left join articulo on art_id=stock_art_id
    left join bodega_forma on forma_id=art_forma
    left join logs on stock_log_id=log_id
    WHERE (log_tipo IN (9,15,16) OR (log_tipo=2 AND stock_cant<0)) AND
    (
        date_trunc('day', log_fecha)>='[%fecha1]' AND
        date_trunc('day', log_fecha)<='[%fecha2]'
    ) AND stock_art_id=[%cod] and
    stock_bod_id=[%bodega] AND art_activado
    GROUP BY articulo.art_codigo,articulo.art_glosa,bodega_forma.forma_nombre,
    [if %tipo==-1]
        art_val_min [/if]
    [if %tipo==2]
        art_val_med [/if]
    [if %tipo==3]
        art_val_max [/if]
    [if %tipo==4]
        art_val_ult [/if]
    ";

    $formato=Array(
    Array('art_codigo',       'C&oacute;digo',           0, 'right'),
    Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
    Array('forma_nombre',     'Forma',                  0, 'left'),
    Array('cant_despachada',  'Consumo',                1, 'right'),
    Array('punit',            'P Unit $',             	2, 'right'),
    Array('subtotal',            'Subtotal',            3, 'right')
    );
    ejecutar_consulta();
    procesar_formulario('Consumo Por Producto Valorizado');
?>
