<?php
   /*
   Nombre Informe: Cantidad de Pedidos Solicitados
   entrega informacion de la cantidad de pedidos hacia abastecimiento, entre los cuales
   se encuentran en tramite(o proceso de orden de compra), anulado, terminado y/o enviado, ademas
   entrega la fecha del pedido y el funcionario que realiza el pedido.
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar
   */

    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');



    $tipo_ped=Array(
                Array('-1',     '(Todos...)'),
                Array('0',       'Enviado'),
                Array('1',        'Retornado'),
                Array('2',        'Terminado'),
                Array('3',        'Anulado'),
                Array('4',        'En Tramite(OC)')

              );

    $campos=Array(
              Array(  'bodega', 'Ubicaci&oacute;n',           11),
              Array(  'tipo',     'Tipo Pedido',              10,   -1,   $tipo_ped ),
              Array(  'fecha1',   'Fecha de Inicio',          1 ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1 ),

            );

   $query=
    "
   SELECT
    pedido_fecha::date,
    pedido_nro,
    CASE WHEN pedido_estado=0 THEN 'Enviado'
         WHEN pedido_estado=1 then 'Retornado'
         WHEN pedido_estado=2 then 'Terminado'
         WHEN pedido_estado=3 then 'Anulado'
         END AS estado,
    COALESCE(bod_glosa, centro_nombre) AS destino,
    (SELECT log_fecha::date FROM logs WHERE log_id_pedido=pedido_id ORDER BY log_fecha DESC LIMIT 1) AS fecha_entrega
		
   FROM pedido
		left join logs on log_id_pedido=pedido.pedido_id
		left join cargo_centro_costo USING (log_id)
		left join centro_costo ON cargo_centro_costo.centro_ruta=centro_costo.centro_ruta OR origen_centro_ruta=centro_costo.centro_ruta
		left join bodega on origen_bod_id=bod_id

   WHERE destino_bod_id=[%bodega] AND (date_trunc('day',pedido_fecha) BETWEEN '[%fecha1]' AND '[%fecha2]') AND
                [if %tipo==0] pedido_estado=0 [/if]
                [if %tipo==1] pedido_estado=1 [/if]
                [if %tipo==2] pedido_estado=2 [/if]
                [if %tipo==3] pedido_estado=3 [/if]
                [if %tipo==4] pedido_tramite [/if]
                [if %tipo==-1]true[/if]
                
      ORDER BY pedido_fecha;
     ";

    $formato=Array(
                Array('pedido_fecha',   'Fecha Pedido',         0, 'center'),
                Array('pedido_nro',     'N&deg; Pedido',        0, 'center'),
                Array('destino',    'Servicio/Unidad',   0, 'left'),
                Array('fecha_entrega',         'Fecha Entrega',        0, 'center'),
                Array('estado',         'Estado Pedido',        0, 'left')


              );

    ejecutar_consulta();
      $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=3>Total Pedidos:</td>
      <td>'.number_format(infoCOUNT('pedido_nro'),0,',','.').'</td>
      </tr>
    ';


    procesar_formulario('Pedidos Por Bodega');

?>
