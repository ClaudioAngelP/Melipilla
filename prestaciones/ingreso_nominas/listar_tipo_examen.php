<?php
    require_once("../../conectar_db.php");
    $esp_id_examen=($_POST['esp_examen']*1);
    $tipo_examen=cargar_registros_obj("SELECT DISTINCT COALESCE (pc_grupo_examen,'0')as option FROM procedimiento_codigo WHERE esp_id=$esp_id_examen AND pc_activo order by option",true);
    if(!$tipo_examen)
    {
        $tipo_examen=false;
    }
    $kit_examen=cargar_registro("SELECT * from examen_kits where kit_esp_id=$esp_id_examen",true);
    if(!$kit_examen)
    {
        $kit_examen=false;
    }
    
    echo json_encode(Array($tipo_examen,$kit_examen));
?>