<?php
    require_once("../../conectar_db.php");
    set_time_limit(0);
    $esp_id=($_POST['esp_id']*1);
    $tipo_exam=pg_escape_string($_POST['tipo_exam']);
    if($tipo_exam!="KITS")
    {
        if($tipo_exam=="0")
        {
            $string_grupo="";
            $string_grupo_examen="";
        }
        else
        {
            //$tipo_exam=$tipo_exam;
            $string_grupo="AND upper(pc_grupo_examen)=upper('$tipo_exam')";
            $string_grupo_examen="AND upper(kit_grupo)=upper('$tipo_exam')";
        }
    
        $consulta="
        SELECT * FROM (
            SELECT distinct on (pc_id)pc_id,codigo,pc_desc,glosa,pc_grupo,pc_grupo_examen,pc_activo,tipo 
            FROM (    
                SELECT
                case when strpos(pc_codigo, '.')>0 then pc_codigo else codigo end as codigo,
                upper(pc_desc)as pc_desc, 
                glosa,
                pc_id,
                upper(pc_grupo)as pc_grupo,
                upper(pc_grupo_examen)as pc_grupo_examen,pc_activo,
                0 as tipo
                FROM codigos_prestacion
                JOIN procedimiento_codigo ON esp_id=$esp_id AND split_part(pc_codigo, '.', 1)=codigo $string_grupo
                order by pc_desc
            )as foo
            where pc_activo
        )as fooo order by pc_desc
        ";
    
        //print($consulta);
    
        $reg_prestaciones = cargar_registros_obj($consulta,true);
        if(!$reg_prestaciones)
        {
            $reg_prestaciones=false;
        }
        echo json_encode(array($reg_prestaciones));
    }
    else
    {
        
        $consulta="
        SELECT * FROM (    
        SELECT 
        kit_codigo as codigo,
        upper(kit_nombre)as pc_desc,
        upper(kit_nombre) as glosa,
        kit_id as pc_id,
        null as pc_grupo,
        upper(kit_grupo)as pc_grupo_examen,
        1 as tipo
        FROM examen_kits 
        WHERE kit_esp_id=$esp_id
        order by pc_grupo_examen
        )as foo
        "
        ;
    
        //print($consulta);
    
        $reg_prestaciones = cargar_registros_obj($consulta,true);
        if(!$reg_prestaciones)
        {
            $reg_prestaciones=false;
        }
        echo json_encode(array($reg_prestaciones));
        
        
    }
    
?>