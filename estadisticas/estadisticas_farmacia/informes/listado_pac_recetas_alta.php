<?php
    set_time_limit(0);
    ini_set("memory_limit","250M");
    require_once('../../../conectar_db.php');
    require_once('../../infogen.php');
    $campos=Array(
        Array('fecha1','Fecha de Inicio',1),
        Array('fecha2','Fecha de T&eacute;rmino',1)
    );
    
    $query="SELECT pac_rut,upper(pac_nombres||' '||pac_appat||'' ||pac_apmat) AS pac_nombre, 
    date_trunc('second',hosp_fecha_egr) AS fecha_alta, centro_nombre,
    (doc_nombres||' '||doc_paterno||' '||doc_materno)AS doc_nombre,diag_desc,
    receta_numero,art_glosa,forma_nombre,SUM(-stock_cant)AS cantidad,(SUM(-stock_cant)*art_val_ult)AS valor
    FROM receta
    LEFT JOIN recetas_detalle ON recetad_receta_id=receta_id
    LEFT JOIN logs ON log_recetad_id=recetad_id
    LEFT JOIN stock ON stock_log_id=log_id
    LEFT JOIN articulo ON art_id=stock_art_id
    LEFT JOIN bodega_forma ON forma_id=art_forma
    LEFT JOIN doctores ON doc_id=receta_doc_id
    LEFT JOIN centro_costo ON centro_ruta=receta_centro_ruta
    LEFT JOIN pacientes ON pac_id=receta_paciente_id
    LEFT JOIN hospitalizacion ON hosp_pac_id=pac_id
    LEFT JOIN diagnosticos ON diag_cod=receta_diag_cod
    WHERE receta_prov_alta=true
    AND (case when hosp_fecha_egr is null then (receta_fecha_emision BETWEEN '[%fecha1] 00:00:00' AND '[%fecha2] 23:59:59')
    else (hosp_fecha_egr BETWEEN '[%fecha1] 00:00:00' AND '[%fecha2] 23:59:59')
    end)
    GROUP BY pac_rut,pac_nombre,fecha_alta,centro_nombre,doc_nombre,diag_desc,receta_numero,
    art_glosa,forma_nombre,art_val_ult;";

    $formato=Array(
        Array('pac_rut','Rut',0,'center'),
        Array('pac_nombre','Nombre',0,'left'),
        Array('fecha_alta','Fecha Alta',0,'left'),
        Array('centro_nombre','Servicio',0,'left'),
        Array('doc_nombre','M&eacute;dico',0,'left'),
        Array('diag_desc','Diagn&oacute;stico',0,'left'),
        Array('receta_numero','N&ordm; Receta',0,'left'),
        Array('art_glosa','Medicamento',0,'left'),
        Array('forma_nombre','Forma',0,'left'),
        Array('cantidad','Cantidad',0,'left'),
        Array('valor','Valor',0,'left')
    );
    ejecutar_consulta();
    procesar_formulario('Medicamentos Entregados a Pacientes con Alta');
?>