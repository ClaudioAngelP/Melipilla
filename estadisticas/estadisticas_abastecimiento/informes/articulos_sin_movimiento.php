<?php

   /*
	Nombre Informe: Pedidos y Envíos Por Bodega
   */

    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');


   $campos=Array(
			  Array(  'bodega', 'Ubicaci&oacute;n',        11   ),
              Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   ),

            );

    $query=
    "
    SELECT *, 
		(cant*punit) AS subtotal,
		(SELECT log_fecha::date FROM stock 
		JOIN logs ON stock_log_id=log_id
		WHERE stock_art_id=art_id AND stock_bod_id=[%bodega] AND stock_cant<0 
		ORDER BY log_fecha DESC LIMIT 1) AS ultimo_mov    
    FROM (
		SELECT *, COALESCE(calcular_stock(art_id, [%bodega]),0.00) AS cant FROM (
			SELECT art_codigo,art_glosa,forma_nombre,item_glosa, art_val_ult AS punit, art_id FROM articulo 
				JOIN articulo_bodega ON art_id=artb_art_id AND artb_bod_id=[%bodega]
				LEFT JOIN bodega_forma ON forma_id=art_forma
				LEFT JOIN item_presupuestario ON item_codigo=art_item
				WHERE 
				(NOT art_id IN (SELECT DISTINCT stock_art_id FROM stock 
												LEFT JOIN logs ON log_id=stock_log_id 
												WHERE stock_bod_id=[%bodega] 
												AND log_fecha>='[%fecha1]' 
												AND log_fecha<='[%fecha2]'))
				
				AND art_activado=true ORDER BY art_glosa
		) AS foo
	) AS foo2;
    ";

    $formato=Array(
                Array('art_codigo',     'C&oacute;digo',                  0, 'right'),
                Array('art_glosa',       'Art&iacute;culo',          0, 'left'),
                Array('forma_nombre',        'Forma',        0, 'left'),
                Array('cant',     'Cantidad',                 0, 'right'),
                Array('punit',     'P Unit.($)',                 3, 'right'),
                Array('subtotal',     'Subtotal($)',                 3, 'right'),
                Array('ultimo_mov',     'Ultimo Mov.',                 0, 'center')
              );


    ejecutar_consulta();

    /*$pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=4>Cantidad Total:</td>
      <td>'.number_format(infoSUM('valor'),2,',','.').'</td>
      </tr>
    ';*/

    procesar_formulario('Resumen de Art&iacute;culos sin Movimientos');



?>
