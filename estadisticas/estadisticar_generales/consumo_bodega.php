<?php
   /*
   Nombre Informe: Consumo por ubicacion
   Entrega informacion el consumo por ubicacion
   de todos los productos despachados, trasladados entre un periodo de tiempo.
   Cinthia Ormazabal C.
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
              Array(  'bodega', 'Ubicaci&oacute;n',           0   ),
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
          item_glosa,
          ABS(SUM(stock_cant)::bigint) as cant_despachada,
           [if %tipo==1]
            ((ABS(SUM(stock_cant))*art_val_min)) as valor [/if]
          [if %tipo==2]
            ((ABS(SUM(stock_cant))*art_val_med)) as valor [/if]
          [if %tipo==3]
            ((ABS(SUM(stock_cant))*art_val_max)) as valor [/if]
          [if %tipo==4]
            ((ABS(SUM(stock_cant))*art_val_ult)) as valor [/if]
          FROM stock
            LEFT JOIN articulo ON art_id=stock_art_id
            LEFT JOIN bodega_forma ON forma_id=art_forma
            LEFT JOIN  logs ON stock_log_id=log_id
            LEFT JOIN item_presupuestario on art_item=item_codigo
          WHERE (log_tipo IN (9,15,16) OR (log_tipo=2 AND stock_cant<0)) AND
            (
              date_trunc('day', log_fecha)>='[%fecha1]' AND
              date_trunc('day', log_fecha)<='[%fecha2]'
            ) AND
            stock_bod_id=[%bodega] AND
            art_activado

          GROUP BY art_codigo,art_glosa,forma_nombre,item_glosa,
             [if %tipo==1] art_val_min [/if]
             [if %tipo==2] art_val_med [/if]
             [if %tipo==3] art_val_max [/if]
             [if %tipo==4] art_val_ult [/if]
          ORDER BY
          [if %tipo==1]
            COALESCE(((ABS(SUM(stock_cant))*art_val_min)),0) [/if]
          [if %tipo==2]
             COALESCE(((ABS(SUM(stock_cant))*art_val_med)),0) [/if]
          [if %tipo==3]
             COALESCE(((ABS(SUM(stock_cant))*art_val_max)),0) [/if]
          [if %tipo==4]
             COALESCE(((ABS(SUM(stock_cant))*art_val_ult)),0) [/if]
          DESC
      ";

    $formato=Array(
                Array('art_codigo',       'C&oacute;digo',          0, 'right'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('forma_nombre',     'Forma',                  0, 'left'),
                Array('item_glosa',       'Item',                  	0, 'left'),
                Array('cant_despachada',  'Consumo UA',                0, 'right'),
                Array('valor',            'Valorizado $',           3, 'right')

              );

    ejecutar_consulta();

     $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=5>Cantidad Total:</td>
      <td>$'.number_format(infoSUM('valor'),0,',','.').'.-</td>
      </tr>
    ';


    procesar_formulario('Consumo de Art&iacute;culos por Bodega(ABC)');

?>
