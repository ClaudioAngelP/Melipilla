<?php

   /*
   Nombre Informe: Compras por Item presupuestario
   Este reporte entrega informacion de compras (recepciones)
   por item presupuestario, filtrado por bodega y rango de fechas
   Cinthia Ormazabal C.
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');


    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',        11   ),
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
          (SUM(stock_cant)) as cant_recibida,
          AVG(stock_subtotal/stock_cant)::real AS precio_promedio
          FROM stock
            LEFT JOIN logs on stock_log_id=log_id
            LEFT JOIN articulo on art_id=stock_art_id
            LEFT JOIN bodega_forma on forma_id=art_forma
           INNER JOIN item_presupuestario on item_codigo=art_item
           WHERE log_tipo=1 AND item_codigo='[%item_presu]' AND stock_bod_id=[%bodega] AND
                     (
                       date_trunc('day', log_fecha)>='[%fecha1]' AND
                       date_trunc('day', log_fecha)<='[%fecha2]'
                     ) AND art_activado
          GROUP BY art_codigo,art_glosa,forma_nombre
          ORDER BY (SUM(stock_cant)* AVG(stock_subtotal/stock_cant)::real) DESC

       ";

    $formato=Array(
                Array('art_codigo',       'C&oacute;digo',        0, 'right'),
                Array('art_glosa',        'Art&iacute;culo',      0, 'left'),
                Array('forma_nombre',     'Forma',                0, 'left'),
                Array('cant_recibida',    'Cant.',                1, 'right'),
                Array('precio_promedio',  'P Unit ($)',  3, 'right')

              );

    ejecutar_consulta();

    procesar_formulario('Compras por Item Presupuestario');



?>
