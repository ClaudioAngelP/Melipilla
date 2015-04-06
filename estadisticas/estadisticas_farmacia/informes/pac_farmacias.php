<?php
   /*
   Nombre Informe: Pacientes Por unidad de farmacia
   Entrega informacion del consumo total por paciente,
   según medicamento ingresado, entre periodo de tiempo en una
   ubicacion de farmacia
   Cinthia Ormazabal C.
   Soluciones Computacionales
   Viña del mar.
  */

    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');

    $campos=Array(
              Array(  'bodega',        'Ubicaci&oacute;n',       3   ),
              Array(  'fecha1',        'Fecha de Inicio',        1   ),
              Array(  'fecha2',        'Fecha de T&eacute;rmino',1   ),
              Array(  'cod',           'Medicamento',            2   )

            );

    $query=
    "
            SELECT pac_rut, pac_nombres, pac_appat, pac_apmat,
            pac_direccion,sector_nombre,comunas.ciud_desc,
            date_trunc('second',receta_fecha_emision) as fecha,
            ABS(SUM(stock_cant))as cantidad_despachada, SUBSTR((extract(days from now()- pac_fc_nac)/365),1,2) as edad, (doc_nombres || ' ' || doc_paterno )as nombre FROM pacientes
            INNER JOIN receta on receta_paciente_id=pac_id
            INNER JOIN recetas_detalle on recetad_receta_id=receta_id
            INNER JOIN logs on log_recetad_id=recetad_id
            INNER JOIN stock on stock_log_id=log_id
            INNER JOIN doctores on receta_doc_id=doc_id
            INNER JOIN comunas on pacientes.ciud_id=comunas.ciud_id
            WHERE date_trunc('day',receta_fecha_emision) BETWEEN '[%fecha1]' AND '[%fecha2]'
            AND stock_bod_id=[%bodega]
            AND recetad_art_id=[%cod]
            GROUP BY pacientes.pac_rut,pacientes.pac_nombres,pacientes.pac_appat,
            pacientes.pac_apmat,receta.receta_fecha_emision,pac_fc_nac, doc_nombres, doc_paterno, doc_materno,
            pac_direccion, sector_nombre,ciud_desc
            ORDER BY receta_fecha_emision



";



    $formato=Array(
                Array('pac_rut',             'Rut',                  0, 'left'),
                Array('pac_nombres',         'Nombre ',              0, 'left'),
                Array('pac_appat',           'Paterno',              0, 'left'),
                Array('pac_apmat',           'Materno',              0, 'left'),
                Array('edad',                'Edad',                 0, 'center'),
                Array('pac_direccion',       'Dirección',            0, 'left'),
                Array('sector_nombre',       'Sector',               0, 'left'),
                Array('ciud_desc',           'Ciudad',               0, 'left'),
                Array('fecha',               'Emisión Receta',       4, 'center'),
                Array('cantidad_despachada', 'Cantidad ',            0, 'right'),
                Array('nombre',         'Doctor',                    4, 'left')

              );

     ejecutar_consulta();

     procesar_formulario('Consumo De Pacientes Por Ubicacion (Segun Medicamento)');

?>
