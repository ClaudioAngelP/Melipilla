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

    $query=
    "
      select * from (
		select prov_rut, prov_glosa, orden_fecha::date AS ofecha, orden_numero, orden_estado_portal, orden_fecha_entrega, log_fecha::date AS fecha_entrega, (log_fecha::date-orden_fecha_entrega) AS dias_atraso, orden_licitacion  from documento
		join proveedor on doc_prov_id=prov_id
		join logs on log_doc_id=doc_id
		join orden_compra ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
		where 
		orden_fecha_entrega is not null AND NOT orden_fecha_entrega = '01/01/0001'
		AND
		log_fecha>orden_fecha_entrega OR CURRENT_DATE>orden_fecha_entrega
		) AS foo ORDER BY dias_atraso DESC;
      ";

    $formato=Array(
                Array('prov_rut',        'RUT',        0, 'right'),
                Array('prov_glosa',      'Nombre',     0, 'left'),
		Array('ofecha',  'Fecha Emisi&oacute;n', 0, 'center'),
                Array('orden_numero',  'Orden de Compra', 0, 'center'),
		Array('orden_licitacion',	'Licitaci&oacute;n',	0,	'center'),
                Array('orden_estado_portal',  'Estado', 0, 'center'),
                Array('orden_fecha_entrega',       'Fecha Comprometida',         0, 'center'),
                Array('fecha_entrega',       'Fecha Recepci&oacute;n',            0, 'center'),
                Array('dias_atraso',       'D&iacute;as de Atraso',            0, 'right')
              );

     ejecutar_consulta();
      

   procesar_formulario('Ordenes de Compra con Despacho Atrasado');

?>
