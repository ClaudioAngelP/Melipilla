<?php
    require_once("../../conectar_db.php");
    set_time_limit(0);
    $esp_id=($_POST['esp_id']*1);
    $tipo_exam=$_POST['tipo_exam'];
    if($tipo_exam=="0")
    {
        $string_grupo="";
    }
    else
    {
        //$tipo_exam=$tipo_exam;
        $string_grupo="AND pc_grupo_examen='$tipo_exam'";
    }
    
    $consulta="SELECT
    codigo,
    pc_desc, 
    glosa,
    pc_id,
    upper(pc_grupo)as pc_grupo,
    upper(pc_grupo_examen)as pc_grupo_examen
    FROM codigos_prestacion
    JOIN procedimiento_codigo ON esp_id=$esp_id AND pc_codigo=codigo $string_grupo order by pc_grupo";
    
    $reg_prestaciones = cargar_registros_obj($consulta,true);
    if(!$reg_prestaciones)
    {
        $reg_prestaciones=false;
    }
    echo json_encode(array($reg_prestaciones));
?>