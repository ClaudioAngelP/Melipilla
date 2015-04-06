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
              Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),
              Array(  'rut_id',   'Paciente',                 4   )

            );

    $query=
    "     SELECT art_codigo,art_glosa,date_trunc('second',receta_fecha_emision)::date as receta_emision,
            recetad_cant as cant,recetad_horas as hr,recetad_dias as dias FROM pacientes
            INNER JOIN receta on receta_paciente_id=(pac_id)::Integer
            INNER JOIN recetas_detalle on recetad_receta_id=receta_id
	        	INNER JOIN articulo on recetad_art_id=art_id
            INNER JOIN logs on log_recetad_id=recetad_id
            INNER JOIN stock on stock_log_id=log_id
            WHERE date_trunc('day',receta_fecha_emision)::date BETWEEN '[%fecha1]' AND '[%fecha2]'
            AND stock_bod_id=[%bodega]
            AND pac_id=[%rut_id]
            GROUP BY articulo.art_codigo,articulo.art_glosa,receta.receta_fecha_emision,
            recetas_detalle.recetad_cant,recetas_detalle.recetad_horas,recetas_detalle.recetad_dias
            ORDER BY receta_fecha_emision;

     ";

    $formato=Array(
                Array('art_codigo',      'C&oacute;digo',          0, 'left'),
                Array('art_glosa',       'Art&iacute;culo',        0, 'left'),
                Array('receta_emision',  'Emisi&oacute;n Receta',  0, 'left'),
                Array('cant',       'Cant.',                  0,      'right'),
                Array('hr',              'C/Hrs.',                 0, 'right'),
                Array('dias',            'C/Dias',                 0, 'right')

              );

     ejecutar_consulta();

     procesar_formulario('Perfil Farmacologico');

?>
