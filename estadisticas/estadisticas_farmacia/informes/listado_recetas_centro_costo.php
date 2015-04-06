<?php
   /*
   Nombre Informe: Lsitado de pacientes con recetas
   Entrega informacion de todos los pacientes que tengan recetas, especificando
   Claudio Esteban angel Pinda.
   Soluciones Computacionales
   Viña del mar.
   */
   set_time_limit(0);
   ini_set("memory_limit","250M");
   require_once('../../../conectar_db.php');
   require_once('../../infogen.php');

    $campos=Array(
              Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
              Array(  'centro', 'Centro Costo',               20  ),
              Array(  'cod',    'Art&iacute;culo',            '2|0'),
              Array(  'fecha1',   'Fecha de Inicio',          1   ),
              Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   ),
           );
    
    
    $query="
            select distinct on (receta_id) receta_id, receta_numero, date_trunc('day',receta_fecha_emision)::date as receta_fecha_emision,centro_nombre, doc_nombres, doc_paterno,
            receta_tipotalonario_id, tipotalonario_nombre, recetad_id, recetad_art_id, receta_centro_ruta,
            stock_bod_id, 
            stock_art_id,
            (-1*stock_cant)as cantidad, 
            pac_rut, 
            pac_nombres, pac_appat
            from receta
            join recetas_detalle on receta_id=recetad_receta_id
            join doctores on receta_doc_id=doc_id
            join pacientes on receta_paciente_id=pac_id
            left join receta_tipo_talonario on receta_tipotalonario_id=tipotalonario_id
            join logs on log_recetad_id=recetad_id
            join stock on stock_log_id=log_id
            join articulo on recetad_art_id=articulo.art_id
            left join centro_costo on receta_centro_ruta=centro_ruta
            where date_trunc('day',receta_fecha_emision) between '[%fecha1]' and '[%fecha2]'
            AND receta_centro_ruta LIKE '[%centro]%'
            and stock_bod_id='[%bodega]' 
            [%and recetad_art_id=[%cod]];
            ";

    $formato=Array(
                Array('receta_numero', 'N receta',          0, 'center'),
                Array('centro_nombre', 'Centro Costo',      0, 'left'),
                Array('pac_rut', 'Rut Paciente',          0, 'center'),
                Array('pac_nombres', 'Paciente',          0, 'left'),
                Array('pac_appat', 'Apellido Pat',          0, 'left'),
                Array('cantidad', 'Cantidad',          0, 'center',2),
                Array('receta_fecha_emision', 'Fecha',          0, 'center'),
                Array('doc_nombres', 'Nombre Doctor',          0, 'left'),
                Array('doc_paterno', 'Apellido Doctor',          0, 'left'),
                Array('tipotalonario_nombre', 'Tipo de receta',          0, 'center'),

            );

     ejecutar_consulta();

     procesar_formulario('Listado de Recetas por Centro de Costo');

?>
