<?php

      /*
   Nombre Informe: Productos en convenio
   Entrega informacion de los convenios asociados al hospital
   y los articulo asociados por convenio
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */

   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');


   $campos=Array(
              Array('convenio', 'Convenio',  5 ),

            );

    $query=
    "
      select art_codigo,art_glosa,forma_nombre from articulo as a
        inner join convenio_detalle as cd on cd.art_id=a.art_id
        inner join convenio as c on c.convenio_id=cd.convenio_id
        inner join bodega_forma on forma_id=art_forma
        where cd.convenio_id=[%convenio]
        order by art_codigo;
      ";

    $formato=Array(
                Array('art_codigo',       'C&oacute;digo',          0, 'left'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('forma_nombre',     'Forma',                  0, 'left'),
              );

     ejecutar_consulta();

    procesar_formulario('Productos En Convenio');

?>
