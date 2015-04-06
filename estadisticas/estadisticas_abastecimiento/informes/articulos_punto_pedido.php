<?php

   /*
	Nombre Informe: Pedidos y Envíos Por Bodega
   */

    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');


   $campos=Array(
			  Array(  'bodega', 'Ubicaci&oacute;n',        11   ),
              Array(  'fecha1', 'Fecha',            1   )
            );

    $query=
    "
    SELECT * FROM (
	SELECT *,
	(
		SELECT SUM(stock_cant) FROM stock
		JOIN logs ON stock_log_id=log_id AND log_fecha<='[%fecha1]'
		LEFT JOIN pedido ON log_id_pedido=pedido_id
		LEFT JOIN pedido_detalle ON pedido_detalle.pedido_id=pedido.pedido_id AND pedido_detalle.art_id=stock_art_id	
		WHERE stock_art_id=critico_art_id AND stock_bod_id=[%bodega] AND 
				(pedido.pedido_id IS NULL OR pedidod_estado OR origen_bod_id=0)
	) AS stock_actual 
	FROM stock_critico 
	JOIN articulo ON critico_art_id=art_id
	WHERE critico_bod_id=[%bodega]
	ORDER BY art_glosa
	) AS foo WHERE stock_actual<critico_pedido
    ";

    $formato=Array(
                Array('art_codigo',     		'C&oacute;digo',                  0, 'right'),
                Array('art_glosa',       		'Art&iacute;culo',          0, 'left'),
                Array('critico_critico',        'Critico',        			1, 'right'),
                Array('critico_pedido',     	'Pedido',                 	1, 'right'),
                Array('stock_actual',     		'Saldo',                 	1, 'right'),
              );


    ejecutar_consulta();

    /*$pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=4>Cantidad Total:</td>
      <td>'.number_format(infoSUM('valor'),2,',','.').'</td>
      </tr>
    ';*/

    procesar_formulario('Resumen de Art&iacute;culos en Punto de Pedido');



?>
