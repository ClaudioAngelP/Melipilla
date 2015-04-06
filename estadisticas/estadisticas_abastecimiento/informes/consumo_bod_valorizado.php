<?php

      /*
   Nombre Informe: Consumo Por Bodega Valorizado
   Entrega informacion por paciente, especificando
   dosis entregada según fecha de receta
   Cinthia Ormazabal C.
   */

    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');


   $campos=Array(

              Array(  'item_presu', 'Item Presupuestario',    7   ),
              Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   ),

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

          FROM STOCK
            LEFT JOIN logs on stock_log_id=log_id
            LEFT JOIN articulo on art_id=stock_art_id
            LEFT JOIN bodega_forma on forma_id=art_forma
            LEFT JOIN cargo_centro_costo as cc on logs.log_id=cc.log_id
            LEFT JOIN recetas_detalle on log_recetad_id=recetad_id
            LEFT JOIN receta on recetad_receta_id=receta_id
              WHERE (log_tipo IN (9,15,16) OR (log_tipo=2 AND stock_cant<0)) AND
                     (
                       date_trunc('day', log_fecha)>='[%fecha1]' AND
                       date_trunc('day', log_fecha)<='[%fecha2]'
                     ) AND
                     (cc.centro_ruta LIKE '[%centro]' || '%' OR
                      receta.receta_centro_ruta LIKE '[%centro]' || '%' )  AND
                     art_activado 
                GROUP BY art_codigo,art_glosa,forma_nombre,
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
                Array('cant_despachada',  'Consumo',                1, 'right'),
                Array('valor',            'valorizado',             3, 'right')
              );


    ejecutar_consulta();

    $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=4>Cantidad Total:</td>
      <td>'.number_format(infoSUM('valor'),2,',','.').'</td>
      </tr>
    ';

    procesar_formulario('Variaci&oacute;n de Precios por Item Presupuestario');



?>
