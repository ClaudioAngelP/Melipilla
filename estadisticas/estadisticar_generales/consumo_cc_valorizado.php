<?php
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
    $valor=Array(
    Array(1,    'Valor M&iacute;nimo'),
    Array(2,    'Valor Medio'),
    Array(3,    'Valor M&aacute;ximo'),
    Array(4,    '&Uacute;ltimo Valor',)
    );

    $campos=Array(
    Array(  'bodega', 'Ubicaci&oacute;n',           0   ),
    Array(  'centro', 'Centro Costo',               20   ),
    Array(  'fecha1', 'Fecha de Inicio',            1   ),
    Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   ),
    Array(  'tipo',   'Valorizaci&oacute;n Seg&uacute;n',   10  ,   -1,   $valor)
    );


           

   
    $query=
    "
      SELECT
          art_codigo,
          art_glosa,
          forma_nombre,
          ABS(SUM(stock_cant)) as cant_despachada,

          [if %tipo==1]
            ((ABS(SUM(stock_cant))* art_val_min)) as valor [/if]
          [if %tipo==2]
            ((ABS(SUM(stock_cant))* art_val_med)) as valor [/if]
          [if %tipo==3]
            ((ABS(SUM(stock_cant))* art_val_max)) as valor [/if]
          [if %tipo==4]
            ((ABS(SUM(stock_cant))* art_val_ult)) as valor [/if]
          ,item_glosa,
            case when cc.centro_ruta is not null then (select upper(centro_nombre) from centro_costo where cc.centro_ruta=centro_costo.centro_ruta)
            else (select upper(centro_nombre) from centro_costo where receta.receta_centro_ruta=centro_costo.centro_ruta) 
            end as centro_detalle,
            cc.centro_ruta,
            receta.receta_centro_ruta
          FROM STOCK
            LEFT JOIN logs on stock_log_id=log_id
            LEFT JOIN articulo on art_id=stock_art_id
            LEFT JOIN item_presupuestario on item_codigo=art_item
            LEFT JOIN bodega_forma on forma_id=art_forma
            LEFT JOIN cargo_centro_costo as cc on logs.log_id=cc.log_id
            LEFT JOIN recetas_detalle on log_recetad_id=recetad_id
            LEFT JOIN receta on recetad_receta_id=receta_id
            WHERE (log_tipo IN (9,15,16,17) OR (log_tipo=2 AND stock_cant<0)) AND
                     (
                       date_trunc('day', log_fecha)>='[%fecha1]' AND
                       date_trunc('day', log_fecha)<='[%fecha2]'
                     ) AND
                     (cc.centro_ruta LIKE '[%centro]%' OR
                      receta.receta_centro_ruta LIKE '[%centro]%' )  AND
                     art_activado AND stock_bod_id=[%bodega]
                GROUP BY art_codigo,art_glosa,forma_nombre,item_glosa,
                [if %tipo==1] art_val_min [/if]
                [if %tipo==2] art_val_med [/if]
                [if %tipo==3] art_val_max [/if]
                [if %tipo==4] art_val_ult [/if]
                ORDER BY art_codigo;
      ";

     
    $formato=Array(
                Array('art_codigo',       'C&oacute;digo',          0, 'right'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('forma_nombre',     'Forma',                  0, 'left'),
                Array('item_glosa',       'Item',                   0, 'left'),
                Array('item_glosa',       'Item',                 0, 'left'),
                Array('cant_despachada',  'Consumo',                1, 'left'),
                Array('valor',            'valorizado',             3, 'right')
              );


    ejecutar_consulta();

    $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=5>Cantidad Total:</td>
      <td>$'.number_format(infoSUM('valor'),2,',','.').'.-</td>
      
      </tr>
      
    ';

    procesar_formulario('Consumo de Art&iacute;culos Valorizado Por Bodega');



?>
