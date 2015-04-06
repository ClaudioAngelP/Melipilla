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
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),
           );

    $query="select receta_func_id,func_nombre,count(*)as total from receta 
			left join funcionario on func_id=receta_func_id
			where receta_fecha_emision BETWEEN '[%fecha1] 00:00:00' AND '[%fecha2] 23:59:59'
			group by receta_func_id,func_nombre
			order by func_nombre;";

    $formato=Array(
                Array('func_nombre',      'Funcionario',               0, 'left'),
                Array('total',        'Total Recetas',    		   0, 'right')

              );

     ejecutar_consulta();

     procesar_formulario('Total de Recetas Digitadas por Funcionario');

?>
