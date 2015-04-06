<?php
   /*
   Nombre Informe: Pacientes AUGE
   Entrega informacion del consumo por paciente,
   según medicamento, entre periodo de tiempo
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
  */

     require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

    $campos=Array(
              Array(  'bodega',   	   'Ubicaci&oacute;n',         3   ),
              Array(  'fecha1',        'Fecha de Inicio',        1   ),
              Array(  'fecha2',        'Fecha de T&eacute;rmino',1   ),
              Array(  'cod',           'Medicamento',            2   )

            );

    $query=
    "
           SELECT pac_rut,pac_nombres,pac_appat,pac_apmat,date_trunc('second',log_fecha) as fecha,
           ABS(SUM(stock_cant)) as cantidad_despachada FROM stock
            JOIN logs ON 
				log_fecha::date >= '[%fecha1]' AND log_fecha::date <= '[%fecha2]' 
				AND stock_log_id=log_id
            JOIN recetas_detalle on log_recetad_id=recetad_id
            JOIN receta on recetad_receta_id=receta_id
            JOIN pacientes ON pac_id=receta_paciente_id
           WHERE stock_bod_id=[%bodega]
           AND recetad_art_id=[%cod]
           GROUP BY pacientes.pac_rut,pacientes.pac_nombres,pacientes.pac_appat,
           pacientes.pac_apmat,logs.log_fecha
           ORDER BY log_fecha
     ";

    $formato=Array(
                Array('pac_rut',             'RUT',                  0, 'right'),
                Array('pac_nombres',         'Nombre ',              0, 'left'),
                Array('pac_appat',           'Paterno',              0, 'left'),
                Array('pac_apmat',           'Materno',              0, 'left'),
                Array('fecha',               'Fecha Despacho',       4,'center'),
                Array('cantidad_despachada', 'Cantidad ',            1 ,'right')

              );

     ejecutar_consulta();

     procesar_formulario('Consumo De Pacientes por Medicamento');

?>
