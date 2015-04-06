<?php
    /*
    Nombre Informe: Consumo por Medicamento/Servicio
    RODRIGO CARVAJAL
    CLAUDIO ANGEL
    HOSP. SAN JOSE DE MELIPILLA -- MELIPILLA
   */
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
    $campos=Array(
    Array(  'bodega',   'Ubicaci&oacute;n',         3   ),
    Array(  'fecha1',   'Fecha de Inicio',          1   ),
    Array(  'fecha2',   'Fecha de T&eacute;rmino',  1   )
    );

    $query="SELECT centro_nombre, doctor, art_codigo, art_glosa, abs(total) AS cant, round(art_val_ult) as art_punit, 
    round(abs(total*art_val_ult)) AS subtotal  
    FROM 
    (select  coalesce(c1.centro_ruta,c2.receta_centro_ruta) AS centro_ruta,(doc_nombres||' '||doc_paterno||' '||doc_materno)as doctor, stock_art_id, sum(stock_cant) AS total from stock 
    join logs on stock_log_id=log_id and log_tipo in (2,9)
    left join cargo_centro_costo as c1 using (log_id)
    left join recetas_detalle on log_recetad_id=recetad_id
    left join receta as c2 on recetad_receta_id=receta_id
    left join doctores on receta_doc_id=doc_id
    where stock_bod_id=[%bodega] and log_fecha between '[%fecha1] 00:00:00' and '[%fecha2] 23:59:59'
    group by stock_art_id, c1.centro_ruta, c2.receta_centro_ruta,doc_nombres,doc_paterno,doc_materno
    ) AS foo
    JOIN articulo ON stock_art_id=art_id
    JOIN centro_costo USING (centro_ruta)
    ORDER BY centro_nombre, art_glosa;
    ";
    
    $formato=Array(
    Array('centro_nombre','Centro de Costo', 0, 'center'),
    Array('doctor','Doctor', 0, 'left'),
    Array('art_codigo','C&oacute;digo', 0, 'left'),
    Array('art_glosa','Art&iacute;culo', 0, 'left'),
    Array('cant','Cant.', 0, 'right'),
    Array('art_punit','P.Unit.', 3, 'right'),
    Array('subtotal','Subtotal', 3, 'right')
    );
    ejecutar_consulta();
    procesar_formulario('Consumo Valorizado de Art&iacute;culos por Centro de Costo');
?>