<?php
   /*
   Nombre Informe: Consumo valorizado por centro de costo
   Entrega informacion del consumo por centro de costo,
   ya sea por despachos, recetas y traslados.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
                
    $campos=Array(
              Array(  'bodega', 'Ubicaci&oacute;n',   0  ),
              Array(  'fecha1', 'Fecha Inicio',    1  ),
              Array(  'fecha2', 'Fecha Final',    1  )

            );

    $query="

		SELECT *, (stock*art_val_ult) AS subtotal FROM (

		SELECT *, (
			SELECT SUM(stock_cant) FROM stock
			JOIN logs ON stock_log_id=log_id
			LEFT JOIN pedido ON log_id_pedido=pedido_id
			LEFT JOIN pedido_detalle ON pedido.pedido_id=pedido_detalle.pedido_id AND stock_art_id=pedido_detalle.art_id
			WHERE stock_art_id=foo3.art_id AND (pedido.pedido_id IS NULL OR pedidod_estado OR origen_bod_id=0) AND stock_bod_id=[%bodega]
		) AS stock 
		
		FROM (

		SELECT *
		FROM (
		
			SELECT * FROM (
			SELECT DISTINCT stock_art_id FROM stock WHERE stock_bod_id=[%bodega]
			) AS foo WHERE stock_art_id NOT IN (
				SELECT DISTINCT stock_art_id FROM stock 
				JOIN logs ON stock_log_id=log_id
				WHERE stock_bod_id=[%bodega] AND log_fecha BETWEEN '[%fecha1]' AND '[%fecha2]'
			)
			
		) AS foo2
		
		JOIN articulo ON stock_art_id=art_id
		JOIN articulo_bodega ON artb_art_id=art_id AND artb_bod_id=[%bodega]
		JOIN bodega_forma ON art_forma=forma_id
		
		) AS foo3
		
		) AS foo4
		
		ORDER BY art_codigo;

     ";

    $formato=Array(
                Array('art_codigo',       'C&oacute;digo',          0, 'left'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('forma_nombre',     'Forma',                  0, 'left'),
                Array('stock',            'Cant.',                  1, 'right'),
                Array('art_val_ult',            'P Unit.',                  3, 'right'),
                Array('subtotal',            'Subtotal',                  3, 'right')
              );

    ejecutar_consulta();

    $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=5>Valor Total:</td>
      <td>'.number_format(infoSUM('subtotal'),2,',','.').'</td>
      </tr>
    ';

    procesar_formulario('Productos Sin Movimientos');

?>
