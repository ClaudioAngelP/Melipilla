<?php

    /*
   Nombre Informe: Consumo por paciente valorizado
   Entrega cantidad y valor por medicamento recetado, por
   paciente, valorizando cada medicamento por su ultimo precio
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
              Array(  'pac_id',   'Paciente',                 4   )

            );
             
    $query=
    "SELECT log_fecha::date AS log_fecha, art_codigo,
            art_glosa,
            ABS(SUM(stock_cant)) as cant,
            ABS(SUM(stock_cant)*art_val_ult) as valor,
            doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS medico
            FROM pacientes
            INNER JOIN receta on receta_paciente_id=pac_id
            INNER JOIN recetas_detalle on recetad_receta_id=receta_id
	        INNER JOIN articulo on recetad_art_id=art_id
            INNER JOIN logs on log_recetad_id=recetad_id
            INNER JOIN stock on stock_log_id=log_id
            LEFT JOIN doctores ON receta_doc_id=doc_id
            WHERE date_trunc('day',receta_fecha_emision)::date BETWEEN '[%fecha1]' AND '[%fecha2]'
            AND stock_bod_id=[%bodega]
            AND pac_id=[%pac_id]
            GROUP BY log_fecha::date,articulo.art_codigo,articulo.art_glosa,articulo.art_val_ult, doc_paterno, doc_materno, doc_nombres
            ORDER BY log_fecha;

     ";

    $formato=Array(
                Array('log_fecha',            'Fecha',          		0, 'center'),
                Array('art_codigo',           'C&oacute;digo',          0, 'left'),
                Array('art_glosa',            'Art&iacute;culo',        0, 'left'),
                Array('cant',                 'Cant.',                  0, 'right'),
                Array('valor',                'Subtotal ($)',           3, 'right'),
                Array('medico',               'M&eacute;dico',           0, 'left')


              );

     ejecutar_consulta();

     $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=4>Valor Total:</td>
      <td>'.infoMONEY(infoSUM('valor')).'</td>
      <td>&nbsp;</td>
      </tr>
    ';

     procesar_formulario('Consumo Valorizado Por Pacientes');

?>
