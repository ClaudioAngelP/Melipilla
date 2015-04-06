<?php
   /*
   Nombre Informe: Lsitado de pacientes con recetas
   Entrega informacion de todos los pacientes que tengan recetas, especificando
   Claudio Esteban angel Pinda.
   Soluciones Computacionales
   Viña del mar.
   */
   set_time_limit(0);
   ini_set("memory_limit","250M");
   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),
           );

    $query="    select distinct receta_paciente_id, pacientes.*, comunas.ciud_desc from receta
                inner join pacientes on receta_paciente_id=pac_id
                inner join recetas_detalle on receta_id=recetad_receta_id
                inner join logs on log_recetad_id=recetad_id
                inner join stock on stock_log_id=log_id
                inner join comunas on pacientes.ciud_id=comunas.ciud_id
                WHERE date_trunc('day',receta_fecha_emision)::date BETWEEN '[%fecha1]' AND '[%fecha2]'
                AND stock_bod_id=[%bodega];";

    $formato=Array(
                Array('pac_rut',      'Rut',          0, 'center'),
                Array('pac_nombres',       'Nombres',        0, 'left'),
                Array('pac_appat',  'Apellido paterno',  0, 'left'),
                Array('pac_apmat',       'Apellido Materno',                  0,      'left'),
                Array('pac_direccion',              'Direcci&oacute;n',                 0, 'left'),
                Array('sector_nombre',            'Sector',                 0, 'left'),
                Array('ciud_desc',            'Ciudad',                 0, 'left')

              );

     ejecutar_consulta();

     procesar_formulario('Listado de Pacientes Por Direcci&oacute;n');

?>
