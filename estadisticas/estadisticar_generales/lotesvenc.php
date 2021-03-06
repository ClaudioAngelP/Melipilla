<?php
   /*
   Nombre Informe: Lotes vencidos y por vencer
   Entrega informacion de lotes vencidos y por vencer(alerta de 6 mes)
   para cada ubicaci�n.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Vi�a del mar
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

    $tipo_venc=Array(
                Array(-1,   'Vencidos'),
                Array(2,    'Por Vencer')
             );



    $campos=Array(
              Array(  'bodega', 'Ubicaci&oacute;n',           0   ),
              Array(  'tipo',   'Selecci&oacute;n Tipo',     10  ,   -1,   $tipo_venc)
              
            );


    $query="
    	SELECT * FROM (SELECT art_codigo,art_glosa,stock_vence,SUM(stock_cant)AS stock_cant,forma_nombre,(art_val_ult*SUM(stock_cant))AS total
		FROM articulo
		LEFT JOIN stock ON stock_art_id=art_id
		LEFT JOIN bodega_forma ON forma_id=art_forma
		WHERE art_vence='1' AND 
		[if %tipo==-1]
            stock_vence<=now()
        [else]
           ((stock_vence - NOW())<=(INTERVAL '6 MONTH')) AND stock_vence > NOW()
        [/if]
		AND stock_bod_id=1
		GROUP BY art_codigo,art_glosa,stock_vence,forma_nombre,art_val_ult)AS foo
		WHERE stock_cant>0
		ORDER BY art_glosa          
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
