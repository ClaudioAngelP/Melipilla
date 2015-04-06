<?php

   /*
   Nombre Informe: Consumo por recepciones de todos los proveedores y la CENABAST
   Entrega informacion valorizada de los productos recepcionados
   de todos los proveedores dando la opcion de ver el consumo aparte de la CENABAST,
   dividido por bodega según un periodo de tiempo.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */
     require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

     $tipo=Array(
                Array('0',     '(Todos los proveedores...)'),
                Array('1',    'Solo Cenabast'),
                array('2',    'Solo SSMOC'),
                array('3',    'Solo PROG. MINISTERIAL')
    );


    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',        11   ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),
              Array(  'tipo',     'Opci&oacute;n Consumo',            10  ,   -1,   $tipo )


            );
    /*
    $query=
    "
        SELECT
            art_codigo,
            art_glosa,
            forma_nombre,
            COALESCE(item_glosa, '(No Asignado...)') as item_glosa,
            ABS(SUM(stock_cant)) as consumo,
            art_val_ult AS punit,
            ((ABS(SUM(stock_cant))*art_val_ult)) as subtotal,
             COALESCE(orden_numero, doc_orden_desc) AS nro_orden
        FROM stock
        LEFT JOIN articulo on art_id=stock_art_id
        LEFT JOIN bodega_forma on forma_id=art_forma
        LEFT JOIN logs on stock_log_id=log_id
        LEFT JOIN documento on log_doc_id=doc_id
        LEFT JOIN orden_compra ON doc_orden_id=orden_id
        LEFT JOIN item_presupuestario on item_codigo=art_item
        WHERE (log_tipo=1) AND
            (
              date_trunc('day', log_fecha)>='[%fecha1]' AND
              date_trunc('day', log_fecha)<='[%fecha2]'
            ) AND
            stock_bod_id=[%bodega]
            AND art_activado
	        [if %tipo==1] AND
                 doc_prov_id=56
            [else] AND
                 doc_prov_id<>56
            [/if]
            GROUP BY art_codigo,art_glosa,forma_nombre,item_glosa,
                    art_val_ult, orden_numero, doc_orden_desc
            ORDER BY art_codigo;
     ";
    */
    
    $query=
    "
    SELECT
        art_codigo,
        art_glosa,
        forma_nombre,
        COALESCE(item_glosa, '(No Asignado...)') as item_glosa,
        ABS(SUM(stock_cant)) as consumo,
        art_val_ult AS punit,
        ((ABS(SUM(stock_cant))*art_val_ult)) as subtotal,
        COALESCE(orden_numero, doc_orden_desc) AS nro_orden,
        doc_interm
    FROM stock
    LEFT JOIN articulo on art_id=stock_art_id
    LEFT JOIN bodega_forma on forma_id=art_forma
    LEFT JOIN logs on stock_log_id=log_id
    LEFT JOIN documento on log_doc_id=doc_id
    LEFT JOIN orden_compra ON doc_orden_id=orden_id
    LEFT JOIN item_presupuestario on item_codigo=art_item
    WHERE (log_tipo=1) AND
    (
        date_trunc('day', log_fecha)>='[%fecha1]' AND
        date_trunc('day', log_fecha)<='[%fecha2]'
    )
    AND
    stock_bod_id=[%bodega]
    AND art_activado
    [if %tipo==1] 
        AND doc_interm='CENABAST'
    [/if]
    [if %tipo==2] 
        AND doc_interm='SSMOC'
    [/if]
    [if %tipo==3] 
        AND doc_interm='PROG. MINISTERIAL'
    [/if]
    [if %tipo==0] 
        AND true
    [/if]
    GROUP BY art_codigo,art_glosa,forma_nombre,item_glosa,
    art_val_ult, orden_numero, doc_orden_desc,doc_interm
    ORDER BY art_codigo;
    ";

    $formato=Array(
    Array('art_codigo','C&oacute;digo',0,'left'),
    Array('art_glosa','Art&iacute;culo',0,'left'),
    Array('forma_nombre','Forma',0,'left'),
    Array('item_glosa','Item',0,'left'),
    Array('nro_orden','Nro. OC',0,'center'),
    Array('doc_interm','Intermediaci&oacute;n',0,'center'),
    Array('consumo','Cant.',0,'right'),
    Array('punit','P Unit ($)',3,'right'),
    Array('subtotal','Subtotal',3,'right')
    );
    
    ejecutar_consulta();

    $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=8>Valor Total:</td>
      <td>'.infoMONEY(infoSUM('subtotal')).'</td>
      </tr>
    ';

    procesar_formulario('Consumo de Recepciones');

?>
