<?php

    /*
   Nombre Informe: Recetas Y prescripciones Controlados
   Entrega cantidad de recetas benzodiazepinas, psicotropicos o ambas por servicio
   clinico segun unidad de farmacia y rengo de fechas
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');


    $controlado=Array(
                Array(-1,     '(Todas...)'),
                Array('1',    'Psicotr&oacute;picos'),
                Array('2',    'Benzodiazepinas')
              );

    $campos=Array(
              Array(  'bodega', 'Ubicaci&oacute;n',           3   ),
              Array(  'fecha1', 'Fecha de Inicio',            1   ),
              Array(  'fecha2', 'Fecha de T&eacute;rmino',    1   ),
              Array(  'tipo',   'Tipo Controlado',           10  ,   -1,   $controlado )
            );

    $query=
    "
          SELECT centro_nombre, count(*) AS recetas FROM (
          SELECT receta_id, receta_centro_ruta, count(*) AS prescrip FROM (
          select distinct receta_id, recetad_id, obtener_centro_costo(receta_centro_ruta) AS receta_centro_ruta
          from receta
          join recetas_detalle on receta_id=recetad_receta_id
          join logs on log_recetad_id=recetad_id
          join stock on stock_log_id=log_id
          where date_trunc('day',receta_fecha_emision) between '[%fecha1]' and '[%fecha2]'
          and stock_bod_id=[%bodega] AND
                [if %tipo==1]receta_tipotalonario_id=1 [/if]
                [if %tipo==2]receta_tipotalonario_id=2 [/if]
                [if %tipo==-1]receta_tipotalonario_id IN (1,2)[/if]
          order by receta_id) AS foo2
          GROUP BY receta_id, receta_centro_ruta
          ) AS foo
          JOIN centro_costo ON receta_centro_ruta=centro_ruta
          GROUP BY centro_nombre
          ORDER BY centro_nombre;
    ";

    $formato=Array(
                Array('centro_nombre',  'Centro de Costo',           0, 'left'),
                Array('recetas',        'Nro. de Recetas',           1, 'right')

              );


    ejecutar_consulta();

    $pie='
      <tr class="tabla_header" style="text-align:right;font-weight:bold;">
      <td colspan=1>Totales:</td>
      <td>'.number_format(infoSUM('recetas'),0,',','.').'</td>

      </tr>
    ';

    procesar_formulario('Total de Recetas de Controlados por Centro de Costo');





?>
