<?php
      /*
   Nombre Informe: Variación de precios por artículo
   Entrega historial de precios según articulo, dado un
   periodo de tiempo.
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

          SELECT
             date_trunc('second', log_fecha)::date AS fecha,
             prov_glosa,
             stock_cant,
            (stock_subtotal)::float,
            (stock_subtotal/stock_cant)::real AS stock_punit
          FROM stock
          INNER JOIN logs ON stock_log_id=log_id
          INNER JOIN documento ON log_doc_id=doc_id
          INNER JOIN proveedor ON doc_prov_id=prov_id
          WHERE stock_art_id=[%cod] AND log_tipo=1 AND
                (date_trunc('day', log_fecha)>='[%fecha1]' AND
                 date_trunc('day', log_fecha)<='[%fecha2]')
          ORDER BY log_fecha;

      ";

    $formato=Array(
                Array('fecha',          'Fecha Recep.',     0, 'left'),
                Array('prov_glosa',     'Proveedor',        0, 'left'),
                Array('stock_cant',     'Cantidad',         0, 'left'),
                Array('stock_punit',    'P Unit. $',    	3, 'right'),
                Array('stock_subtotal', 'Subtotal',         3, 'right')

              );


    ejecutar_consulta();

     $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=4>Promedio:</td>
      <td>'.infoMONEY(infoPROM('stock_punit')).'</td>
      </tr>
    ';

    procesar_formulario('Variaci&oacute;n de precios por Art&iacute;culo');

?>
