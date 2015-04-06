<?php
    require_once('../../conectar_db.php');
    $esp_exam=$_POST['esp_exam']*1;
    $reg_grupos=cargar_registros_obj("SELECT DISTINCT pc_grupo_examen from procedimiento_codigo where esp_id=$esp_exam and pc_activo");
    print("<select id='grupo_exam' name='grupo_exam' onChange='listar_prestaciones_examen();'>");
    print("<option value='-1' selected>Seleccionar grupo ex&aacute;men....</option>");
    if($reg_grupos) {
        print("<option value='0' SELECTED>TODOS LOS GRUPOS</option>");
        for($i=0;$i<count($reg_grupos);$i++){
            if($reg_grupos[$i]['pc_grupo_examen']==null or $reg_grupos[$i]['pc_grupo_examen']=="") 
                print("<option value='".$reg_grupos[$i]['pc_grupo_examen']."'>EX&Aacute;MENES SIN GRUPO</option>");
            else
                print("<option value='".$reg_grupos[$i]['pc_grupo_examen']."'>".$reg_grupos[$i]['pc_grupo_examen']."</option>");
        }
        print("<option value='kit'>KITS DE EX&Aacute;MENES</option>");
    }
    print("</select>");
?>