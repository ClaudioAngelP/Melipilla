<?php

    /*
   Nombre Informe: Consumo por Medicamento/Servicio
   RODRIGO CARVAJAL
   HOSP. DR GUSTAVO FRICKE -- VIÑA DEL MAR
   */
   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   )
            );

    $query=
    "       SELECT centro_nombre, art_codigo, art_glosa, total, round(art_val_ult) as art_punit, round(abs(total*art_val_ult)) AS subtotal  
			FROM 
			(select  centro_ruta, stock_art_id, sum(stock_cant) AS total from stock 
			join logs on stock_log_id=log_id
			join cargo_centro_costo using (log_id)
			where stock_bod_id=[%bodega] and log_fecha between '[%fecha1]' and '[%fecha2]'
			group by stock_art_id, centro_ruta) AS foo
			JOIN articulo ON stock_art_id=art_id
			JOIN centro_costo USING (centro_ruta)
			ORDER BY centro_nombre, art_glosa;

     ";

    $formato=Array(
                Array('centro_nombre',          'Centro de Costo',          0, 'center'),
                Array('art_codigo',           	'C&oacute;digo',          0, 'left'),
                Array('art_glosa',            	'Art&iacute;culo',        0, 'left'),
                Array('total',                 	'Cant.',                  0, 'right'),
                Array('art_punit',              'P.Unit.',      3, 'right'),
                Array('subtotal',               'Subtotal',      3, 'right')
              );

     ejecutar_consulta();

     procesar_formulario('Consumo Valorizado de Art&iacute;culos por Centro de Costo');

?>
