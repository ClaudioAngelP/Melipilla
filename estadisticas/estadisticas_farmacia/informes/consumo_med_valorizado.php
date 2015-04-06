<?php

     /*
   Nombre Informe: Consumo por medico valorizado
   Entrega cantidad total por medicamento, por
   medico, valorizando cada medicamento por su ultimo precio,
   dado un periodo de tiempo.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */
   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),
              Array(  'rut_id',   'M&eacute;dico',            6   )

            );

    $query=
    "       SELECT art_codigo,art_glosa,
            ABS(SUM(stock_cant)) as cant,
            ABS(SUM(stock_cant)*art_val_ult) as valor FROM doctores
               INNER JOIN receta on receta_doc_id=doc_id
               INNER JOIN recetas_detalle on recetad_receta_id=receta_id
	           INNER JOIN articulo on recetad_art_id=art_id
               INNER JOIN logs on log_recetad_id=recetad_id
               INNER JOIN stock on stock_log_id=log_id
            WHERE
            date_trunc('day',receta_fecha_emision)::date BETWEEN '[%fecha1]' AND '[%fecha2]'
            AND stock_bod_id=[%bodega]
            AND doc_id=[%rut_id]
            GROUP BY articulo.art_codigo,articulo.art_glosa,articulo.art_val_ult
            ORDER BY art_codigo;

     ";

    $formato=Array(
                Array('art_codigo',           'C&oacute;digo',          0, 'left'),
                Array('art_glosa',            'Art&iacute;culo',        0, 'left'),
                Array('cant',                 'Cant.',                  0, 'right'),
                Array('valor',                'Valor',                  3, 'right')


              );

     ejecutar_consulta();

     $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=3>Valor Total:</td>
      <td>'.number_format(infoSUM('valor'),2,',','.').'</td>
      </tr>
    ';

     procesar_formulario('Consumo Valorizado Por M&eacute;dicos');

?>
