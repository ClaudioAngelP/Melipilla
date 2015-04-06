<?php

      /*
   Nombre Informe: Numero serie por articulo
   
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */

   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');


   $campos=Array(
              Array(  'cod',    'Art&iacute;culo',            2   ),
              Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   )

            );

    $query=
    "

            select log_fecha::date,log_doc_id,stock_serie,stock_cant, stock_vence, log_doc_id from stock
            join logs on log_id=stock_log_id
            left join stock_refserie on stock_refserie.stock_id=stock.stock_id
            where stock_art_id=[%cod] AND stock_cant>0 and date_trunc('day',log_fecha) BETWEEN '[%fecha1]' AND '[%fecha2]'
            order by log_fecha
      ";

    $formato=Array(
                Array('log_fecha',       'Fecha Recep.',            0, 'center'),
                Array('log_doc_id',      'N&deg; Recep.',           0, 'left'),
                Array('stock_vence',     'Fecha Venc.',          	0, 'center'),
                Array('stock_serie',     'N&deg; de Serie',          0, 'center'),
                Array('stock_cant',      'Cantidad',                0, 'right')
              );

     ejecutar_consulta();

    procesar_formulario('Numeros de Serie por Art&iacute;culo');

?>
