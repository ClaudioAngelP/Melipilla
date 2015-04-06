<?php

      /*
   Nombre Informe: Proveedores Asociados HSMQ
   Lista todos los proveedores asociados al hospital, entregando datos
   como rut, nombre,dirección y correo electronico
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */

   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

    $campos=Array(
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   )
            );


    $query=
    "
      select *, total_orden_1+total_orden_2 AS total_orden, (total_orden_1+total_orden_2)-total_recepcion AS diferencia from (
      select prov_rut, prov_glosa, orden_fecha::date AS ofecha, orden_fecha, orden_numero, orden_licitacion, orden_estado_portal, orden_fecha_entrega, 
		coalesce((select sum(ordetalle_subtotal) from orden_detalle where ordetalle_orden_id=oc.orden_id),0) AS total_orden_1,
		coalesce((select sum(orserv_subtotal) from orden_servicios where orserv_orden_id=oc.orden_id),0) AS total_orden_2,
		coalesce((select sum(stock_subtotal) from stock where stock_log_id in (select log_id from logs WHERE log_doc_id in (select doc_id FROM documento where doc_orden_desc=orden_numero or doc_orden_id=orden_id))),0) AS total_recepcion
		from orden_compra AS oc
		join proveedor on orden_prov_id=prov_id
		where orden_fecha::date between '[%fecha1]' and '[%fecha2]'
		order by orden_fecha
		) AS foo;
      ";

    $formato=Array(
                Array('prov_rut',        'RUT',        0, 'right'),
                Array('prov_glosa',      'Nombre',     0, 'left'),
		Array('ofecha',  'Fecha Emisi&oacute;n', 0, 'center'),
                Array('orden_numero',  		'Orden de Compra', 0, 'center'),
		Array('orden_licitacion',       'Licitaci&oacute;n',    0,      'center'),
                Array('orden_estado_portal',  'Estado Portal', 0, 'center'),
                Array('total_orden',       		'Total OC',         3, 'right'),
				Array('total_recepcion',       'Total Recep.',         3, 'right'),
				Array('diferencia',       'Diferencia',         3, 'right')
              );

     ejecutar_consulta();
      

   procesar_formulario('Ordenes de Compra y Documentos de Recepci&oacute;n');

?>
