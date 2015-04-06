<?php
   /*
   Nombre Informe: Lotes vencidos y por vencer (Por artículo)
   Entrega informacion de lotes vencidos y por vencer(alerta de 6 mes)
   para cada ubicación.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

    $tipo_venc=Array(
                Array(-1,   'Vencidos')
             );



    $campos=Array(
              Array(  'bodega', 'Ubicaci&oacute;n',           0   ),              
              Array(  'cod',    'Art&iacute;culo',            2   ),
              Array(  'tipo',   'Tipo',     10  ,   -1,   $tipo_venc)
            );

	
    $query=
    "
          SELECT art_codigo, art_glosa, stock_vence, stock_cant, forma_nombre
          ,(art_val_ult*stock_cant) as total
          FROM stock_precalculado
          JOIN articulo ON stock_art_id=art_id
          JOIN bodega_forma on forma_id=art_forma
          WHERE
            stock_vence<=now()
          AND stock_cant>0
          AND stock_bod_id=[%bodega]
          AND art_activado
          AND art_id=[%cod]          
          ORDER BY art_codigo, stock_vence

    ";

    $formato=Array(
                Array('art_codigo',       'C&oacute;digo Int.',     0, 'left'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('forma_nombre',     'Forma',                  4, 'left'),
                Array('stock_vence',      'Fecha de Venc.',         4, 'center'),
                Array('stock_cant',       'Cantidad',               0, 'right'),
                Array('total',            'Subtotal',               3, 'right')
                );



  ejecutar_consulta();

    $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=5>Valorizado total:</td>
      <td>'.number_format(infoSUM('total'),2,',','.').'</td>
      </tr>
    ';

   procesar_formulario('Lotes Vencidos y por Vencer(6 Meses desde la fecha actual)');



?>
