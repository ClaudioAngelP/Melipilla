<?php 
    require_once('../../conectar_db.php');
    $kit_id=$_POST['kit_examen_id']*1;
    $esp_id=$_POST['esp_id']*1;
    $cant=$_POST['cant']*1;
    $d=cargar_registro("SELECT * FROM examen_kits WHERE kit_id=$kit_id", true);
    if(trim($d['kit_grupo'])=='')
    {
       $string_grupo="";
    }
    else
    {
        $string_grupo="AND upper(pc_grupo_examen)=upper('".$d['kit_grupo']."')";
    }
    
    $exams=explode('|',trim($d['kit_detalle']));
    $datos=Array();
    for($i=0;$i<sizeof($exams);$i++)
    {
        $exam_codigo=strtoupper($exams[$i]);
        if(strstr($exam_codigo,'X') )
        {
            list($exam_codigo, $exam_cant) = explode('X', $exam_codigo);
	}
        else
        {
            $exam_cant='1';
        }
        
        $consulta="SELECT
        case when strpos(pc_codigo, '.')>0 then pc_codigo else codigo end as codigo,
        upper(pc_desc)as pc_desc, 
        glosa,
        pc_id,
        upper(pc_grupo)as pc_grupo,
        upper(pc_grupo_examen)as pc_grupo_examen,
        0 as tipo
        FROM codigos_prestacion
        JOIN procedimiento_codigo ON pc_codigo='$exam_codigo' AND esp_id=$esp_id AND split_part(pc_codigo, '.', 1)=codigo and pc_activo";
        
        //print($consulta);
        
        
        $exam=cargar_registro($consulta);
            
        
        if(!$exam)
            continue;
        
        $num=sizeof($datos);
        $datos[$num]->codigo=$exam['codigo'];
        $datos[$num]->pc_desc=$exam['pc_desc'];
        $datos[$num]->glosa=$exam['glosa'];
        $datos[$num]->pc_id=$exam['pc_id'];
        $datos[$num]->pc_grupo=$exam['pc_grupo'];
        $datos[$num]->pc_grupo_examen=$exam['pc_grupo_examen'];
        $datos[$num]->tipo=$exam['tipo'];
        $datos[$num]->cantidad=$exam_cant*$cant;
    }
    print(json_encode($datos, true));
?>