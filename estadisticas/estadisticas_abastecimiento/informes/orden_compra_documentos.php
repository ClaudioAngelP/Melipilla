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
    select prov_rut, prov_glosa, orden_fecha::date AS ofecha, orden_numero, orden_licitacion, orden_estado_portal, orden_fecha_entrega, 
	(case when doc_tipo=0 then 'Guía de Despacho' when doc_tipo=1 then 'Factura' when doc_tipo=2 then 'Boleta' else 'Pedido' end) AS tipo, 
	doc_num, log_fecha::date AS fecha_entrega from orden_compra
	join documento on doc_orden_desc=orden_numero or doc_orden_id=orden_id
	join proveedor on doc_prov_id=prov_id
	join logs on log_doc_id=doc_id
	WHERE orden_fecha::date between '[%fecha1]' and '[%fecha2]'
	order by orden_fecha, log_fecha;
	";

    $formato=Array(
                Array('prov_rut',        'RUT',        0, 'right'),
                Array('prov_glosa',      'Nombre',     0, 'left'),
		Array('ofecha',  'Fecha Emisi&oacute;n', 0, 'center'),
                Array('orden_numero',  'Orden de Compra', 0, 'center'),
		Array('orden_licitacion', 	'Licitaci&oacute;n',	0,	'center'),
                Array('orden_estado_portal',  'Estado Portal', 0, 'center'),
                Array('tipo',       'Tipo Doc.',         0, 'left'),
                Array('doc_num',       'N&uacute;mero Doc.',         0, 'right'),
                Array('fecha_entrega',       'Fecha Recepci&oacute;n',            0, 'center')
              );

     ejecutar_consulta();
      

   procesar_formulario('Ordenes de Compra y Documentos de Recepci&oacute;n');

?>
