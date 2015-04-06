<?php

    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');


   $campos=Array(

              Array(  'item_presu', 'Item Presupuestario',    7   ),
              Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   ),

            );

    $query=
    "
    SELECT art_codigo, art_glosa,
      (SUM((stock_subtotal/stock_cant))/(count(*)))::real as total
       FROM articulo
       INNER JOIN stock on stock_art_id=articulo.art_id
       INNER JOIN logs on stock_log_id=log_id
       INNER JOIN item_presupuestario on item_codigo=art_item
       WHERE  art_activado AND item_codigo='[%item_presu]'
              AND date_trunc('day',log_fecha) BETWEEN '[%fecha1]' AND '[%fecha2]' AND log_tipo=1
       GROUP BY articulo.art_codigo,articulo.art_glosa
       ORDER BY art_codigo;

      ";

    $formato=Array(
                Array('art_codigo',       'C&oacute;digo',          0, 'left'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('total',            '$total',                 0, 'left')
              );


    ejecutar_consulta();


    procesar_formulario('Variaci&oacute;n de Precios por Item Presupuestario');



?>
