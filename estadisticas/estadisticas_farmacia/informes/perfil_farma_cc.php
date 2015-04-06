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
   
   set_time_limit(0);

    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   )
            );

    $query=
    "     
			SELECT centro_nombre,
			pac_rut, upper(pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) AS pac_nombre, 
			art_codigo,art_glosa,date_trunc('second',receta_fecha_emision) as receta_emision,
            recetad_cant as cant,recetad_horas as hr,recetad_dias as dias FROM receta
            JOIN pacientes ON receta_paciente_id=pac_id 
            JOIN centro_costo ON receta_centro_ruta=centro_ruta
            JOIN recetas_detalle on recetad_receta_id=receta_id
	        JOIN articulo on recetad_art_id=art_id
            WHERE receta_fecha_emision::date BETWEEN '[%fecha1]' AND '[%fecha2]' 
            AND receta_bod_id=[%bodega]
            ORDER BY centro_nombre, receta_fecha_emision;

     ";
     
     /*

			JOIN logs on log_recetad_id=recetad_id
            JOIN stock on stock_log_id=log_id
                   
       
      */

    $formato=Array(
                Array('centro_nombre',   'Centro de Costo',     	0, 'center'),
                Array('pac_rut',      	 'RUT',     				0, 'right'),
                Array('pac_nombre',      'Paciente',     			0, 'left'),
                Array('art_codigo',      'C&oacute;digo',          0, 'right'),
                Array('art_glosa',       'Art&iacute;culo',        0, 'left'),
                Array('receta_emision',  'Emisi&oacute;n Receta',  0, 'center'),
                Array('cant',       	 'Cant.',                  0, 'right'),
                Array('hr',              'C/Hrs.',                 0, 'right'),
                Array('dias',            'C/Dias',                 0, 'right')

              );

     ejecutar_consulta();

     procesar_formulario('Perfil Farmacologico');

?>
