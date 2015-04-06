<?php

   /*
    *
    *
   Nombre Informe: Consumo Por proveedor
   entrega informacion de recepciones por proveedor, según tipo de
   documento, valorizando con IVA, el periodo a consultar.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar
   */
	set_time_limit(0);
   ini_set("memory_limit","250M");
   
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');



    $documento=Array(
                Array('-1',     '(Todos...)'),
                Array('1',       'Factura'),
                Array('0', 'Guia Despacho'),
                Array('2',        'Boleta'),
                Array('3',        'Pedido')
              );

    $campos=Array(
              Array(  'prove',    'Proveedor',                8   ),
              Array(  'tipo',     'Documento',               10  ,   -1,   $documento ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),

            );

    $query=
    "
        SELECT
            art_codigo,
            art_glosa,
            forma_nombre,
            doc_num,
            date_trunc('day',log_fecha)::date AS fecha,
            ABS(SUM(stock_cant)) as consumo,
            ((ABS(SUM(stock_cant))*art_val_ult)) AS valor,
            art_val_ult AS punit,
            prov_id,
            COALESCE(orden_numero, doc_orden_desc) AS nro_orden
        FROM stock
        LEFT JOIN articulo ON art_id=stock_art_id
        LEFT JOIN bodega_forma ON forma_id=art_forma
        LEFT JOIN logs ON stock_log_id=log_id
        LEFT JOIN documento ON log_doc_id=doc_id
        LEFT JOIN orden_compra ON doc_orden_id=orden_id
        LEFT JOIN proveedor on prov_rut='[%prove]'
        WHERE
             log_tipo=1 AND
            (
              date_trunc('day', log_fecha)>='[%fecha1]' AND
              date_trunc('day', log_fecha)<='[%fecha2]'
            )
            AND art_activado  AND doc_prov_id=prov_id
            AND
                [if %tipo==0] doc_tipo=0 [/if]
                [if %tipo==1] doc_tipo=1 [/if]
                [if %tipo==2] doc_tipo=2 [/if]
                [if %tipo==3] doc_tipo=3 [/if]
                [if %tipo==-1] doc_tipo IN (0,1,2,3)[/if]
            GROUP BY art_codigo,art_glosa,forma_nombre,documento.doc_num,logs.log_fecha,doc_tipo, art_val_ult, prov_id,orden_numero, doc_orden_desc
            ORDER BY log_fecha;
     ";

    $formato=Array(
                Array('art_codigo',       'C&oacute;digo',          0, 'left'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('forma_nombre',     'Forma',                  0, 'left'),
                Array('doc_num',          'N&deg; Doc.',            0, 'left'),
                Array('fecha',            'Fecha',                  0, 'left'),
                Array('nro_orden',            'Nro. OC',            0, 'center'),
                Array('consumo',          'Cant.',                  0, 'right'),
                Array('punit',            'P Unit. $',                  3, 'right'),
                Array('valor',            'Subtotal',                  3, 'right')

              );

    ejecutar_consulta();

    $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=8>Total Neto:</td>
      <td>'.infoMONEY(infoSUM('valor')).'</td>
      </tr>

      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=8>Total+IVA:</td>
      <td>'.infoMONEY(agrega_iva('valor')).'</td>
      </tr>
    ';

    procesar_formulario('Consumo Por Proveedor');

?>
