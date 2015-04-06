<?php
    require_once("../../conectar_db.php");
    $esp_id_examen=($_POST['esp_examen']*1);
    $tipo_examen=cargar_registros_obj("select distinct coalesce (pc_grupo_examen,'0')as option from procedimiento_codigo where esp_id=$esp_id_examen");
    if(!$tipo_examen)
    {
        $tipo_examen=false;
        
    }
    echo json_encode(Array($tipo_examen));
?>