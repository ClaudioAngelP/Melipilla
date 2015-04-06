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
		select prov_rut, prov_glosa, orden_fecha::date AS ofecha, orden_numero, orden_estado_portal, orden_fecha_entrega, (CURRENT_DATE-orden_fecha_entrega) AS dias_atraso, orden_licitacion 
		FROM orden_compra
		join proveedor on orden_prov_id=prov_id
		WHERE 
		orden_fecha_entrega is not null AND NOT orden_fecha_entrega = '01/01/0001'
		AND
		CURRENT_DATE>orden_fecha_entrega
		) AS foo ORDER BY dias_atraso DESC;
      ";

    $formato=Array(
                Array('prov_rut',        'RUT',        0, 'right'),
                Array('prov_glosa',      'Nombre',     0, 'left'),
		Array('ofecha',  'Fecha Emisi&oacute;n', 0, 'center'),
                Array('orden_numero',  'Orden de Compra', 0, 'center'),
		Array('orden_licitacion',       'Licitaci&oacute;n',    0,      'center'),
                Array('orden_estado_portal',  'Estado', 0, 'center'),
                Array('orden_fecha_entrega',       'Fecha Comprometida',         0, 'center'),
                Array('dias_atraso',       'D&iacute;as de Atraso',            0, 'right')
              );

     ejecutar_consulta();
      

   procesar_formulario('Ordenes de Compra con Despacho Atrasado');

?>
