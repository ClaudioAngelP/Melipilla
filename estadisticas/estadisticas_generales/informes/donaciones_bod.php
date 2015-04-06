<?php
    /*
   Nombre Informe: Donaciones por ubicacion o bodegas
   Entrega informacion de las donaciones recibidas
   por ubicacion entre un periodo de tiempo.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
                
    $campos=Array(
              Array(  'bodega', 'Ubicaci&oacute;n',           0   ),
              Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   )
            );

    $query=
    "

         SELECT
          DATE_TRUNC('day',log_fecha)::date as fecha,
          art_codigo,
          art_glosa,
          forma_nombre,
          ABS(SUM(stock_cant)) as cant_despachada,
          log_comentario as comentario
          FROM logs
          INNER JOIN stock on stock_log_id=log_id
          LEFT JOIN articulo on art_id=stock_art_id
          LEFT JOIN bodega_forma on forma_id=art_forma
          WHERE
            (
              DATE_TRUNC('day', log_fecha)>='[%fecha1]' AND
              DATE_TRUNC('day', log_fecha)<='[%fecha2]'
            ) AND
               stock_bod_id=[%bodega] AND
               log_tipo=5
               GROUP BY art_codigo,art_glosa,forma_nombre,logs.log_fecha,logs.log_comentario
               ORDER BY log_fecha ;


      ";

    $formato=Array(
                Array('fecha',            'Fecha',                  0, 'right'),
                Array('art_codigo',       'C&oacute;digo',          0, 'right'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('forma_nombre',     'Forma',                  0, 'left'),
                Array('cant_despachada',  'Cant.',                  1, 'right'),
                Array('comentario',       'Comentario',             0, 'justify;font-size:9px')

              );


    ejecutar_consulta();
    procesar_formulario('Donaciones por Ubicaci&oacute;n');

?>
