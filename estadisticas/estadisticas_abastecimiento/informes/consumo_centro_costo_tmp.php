<?php
   /*
   Nombre Informe: Perfil Farmacologico
   Entrega informacion por paciente, especificando
   dosis y frecuencia de medicamentos despachados
   según fecha de receta.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */
   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

    $campos=Array(
              Array(  	'fecha1',   	'Fecha de Inicio',          1   ),
              Array(  	'fecha2',   	'Fecha de T&eacute;rmino',  1   )
            );

    $query=
    " 
		select * from (
		
		select centro_ruta, centro_nombre,  log_fecha, serv_item AS item, '' AS codigo, serv_glosa AS glosa, 0 as solicitud, serv_cant AS cantidad, '' AS unidad, serv_subtotal AS subtotal, '' AS bodega from documento
		left join logs on log_doc_id=doc_id
		left join cargo_centro_costo using (log_id)
		left join centro_costo using (centro_ruta)
		left join servicios on serv_log_id=logs.log_id
		where log_fecha between '[%fecha1] 00:00:00' and '[%fecha2] 23:59:59' AND centro_ruta='[%centro]'

		UNION

		select centro_ruta, centro_nombre, log_fecha, art_item AS item, art_codigo AS codigo, art_glosa AS glosa, pedidod_cant as solicitud, -(stock_cant) as cantidad, forma_nombre AS unidad, (art_val_ult*-(stock_cant)) AS subtotal, bod_glosa AS bodega
		from pedido
		left join logs on log_id_pedido=pedido_id
		left join cargo_centro_costo using (log_id)
		left join centro_costo using (centro_ruta)
		left join stock on stock_log_id=log_id
		left join bodega on stock_bod_id=bod_id
		left join articulo on stock_art_id=art_id
		left join pedido_detalle on pedido_detalle.pedido_id=pedido.pedido_id AND pedido_detalle.art_id=articulo.art_id
		left join bodega_forma on art_forma=forma_id
		join articulo_bodega on (artb_art_id=stock_art_id ANd artb_bod_id=stock_bod_id)
		where origen_bod_id=0
		and log_fecha between '[%fecha1] 00:00:00' and '[%fecha2] 23:59:59'
		
		) AS foo order by log_fecha;
	";

    $formato=Array(
                Array('centro_nombre',          'Centro de Costo',            	0, 'center'),
                Array('log_fecha',      		'Fecha',          		0, 'center'),
                Array('item',      	'Item Presupuestario',          		0, 'center'),
                Array('codigo',       	'C&oacute;digo',        		0, 'right'),
                Array('glosa',  		'Descripci&oacute;n',  			0, 'left'),
                Array('solicitud',       	'Solicitado',          	1, 'right'),
                Array('cantidad',       	'Cant.',          	1, 'right'),
                Array('unidad',       	'Unidad',          	0, 'left'),
                Array('subtotal',         	'$ Subtotal',           	3, 'right'),
                Array('bodega',         	'Bodega',           	0, 'left')
              );

     ejecutar_consulta();

     procesar_formulario('Consumo de Centros de Costo');

?>
