<?php
   /*
   Nombre Informe: Ajustes de Stock del Sistema
   Entrega informacion de los Ajustes de Stock realizados en el Sistema.
   Sistemas Expertos
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
                
    $campos=Array(
	Array('bodega','Ubicaci&oacute;n',0),
             Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   )

            );

    $query=" select * from logs
join funcionario on log_func_if=func_id
join stock on stock_log_id=log_id
join articulo on stock_art_id=art_id 
join bodega on stock_bod_id=bod_id
where log_fecha::date BETWEEN '[%fecha1]' AND '[%fecha2]'
AND stock_bod_id=[%bodega]
and  log_tipo in (30,31,32) order by log_fecha,log_id

     ";

    $formato=Array(
                Array('log_id',       'N&uacute;mero',          0, 'left'),    
                Array('art_codigo',       'C&oacute;digo',          0, 'left'),
                Array('art_glosa',        'Art&iacute;culo',        0, 'left'),
                Array('log_fecha',     'Fecha',                  0, 'center'),
                Array('stock_cant',            'Cant.',                  0, 'right'),
                Array('bod_glosa',            'Bodega',                  0, 'center'),
                Array('log_comentario',            'Motivo',                  0, 'left'),
                 Array('func_nombre',            'Funcionario',                  0, 'center')
                              
                
               

              );

    ejecutar_consulta();

    procesar_formulario('Ajustes de Stock del Sistema.');

?>
