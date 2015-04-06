<?php
    /*
   Nombre Informe: Prestamos y devoluciones Por Bodega
   Entrega informacion de todos los prestamos o devoluciones pendientes,es decir,
   saldos a favor o en contra por bodega, ordenados por la institucion con la cúal se
   realizo el movimiento.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

   $campos=Array(
              Array(  'bodega',       'Ubicaci&oacute;n',           0   ),
               );

    $query=
    "    SELECT * FROM (
         SELECT log_fecha::date as log_fecha, art_codigo, art_glosa, SUM(cantidad) AS saldo,
         CASE WHEN SUM(cantidad)>0 then instsol_desc else 'Hospital Dr. Gustavo Fricke' END as inst_presta,
         CASE WHEN SUM(cantidad)>0 then 'Hospital Dr. Gustavo Fricke' else instsol_desc END as inst_recibe,
	 log_comentario
                  FROM (
                          SELECT instsol_id, stock_art_id, SUM(stock_cant) AS cantidad,
                           date_trunc('second', log_fecha) AS log_fecha, log_id, log_comentario
                          FROM stock
                          JOIN logs ON stock_log_id=log_id
                          JOIN cargo_instsol USING (log_id)
                          WHERE stock_bod_id=[%bodega]
                          GROUP BY instsol_id, stock_art_id, log_fecha, log_id,log_comentario
                      ) AS foo
            JOIN institucion_solicita USING (instsol_id)
            JOIN articulo ON stock_art_id=art_id
              WHERE NOT cantidad=0
              GROUP BY art_codigo, art_glosa,instsol_desc,log_fecha,log_comentario
              ORDER BY art_codigo DESC
              ) AS foo
             WHERE NOT saldo=0
             ORDER BY art_glosa
      ";

    $formato=Array(
                Array('log_fecha',  'Fecha',   0, 'left'),
                Array('inst_presta',  'Instituci&oacute;n Presta',   0, 'left'),
                Array('art_codigo',    'C&oacute;digo',        0, 'left'),
                Array('art_glosa',     'Glosa',                0, 'left'),
                Array('saldo',         'Cant.',                0, 'left'),
                Array('inst_recibe',    'Prestado a',      0, 'left'),
		Array('log_comentario', 'Comentarios', 0, 'left')

              );


    ejecutar_consulta();                    
    procesar_formulario('Prestamos y devoluciones Por Bodega(Todos los Movimientos)');



?>
