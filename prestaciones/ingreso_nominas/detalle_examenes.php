<?php
    require_once("../../conectar_db.php");
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $sol_exam_id=($_POST['sol_exam_id']*1);
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $consulta="select * from solicitud_examen_detalle 
    left join procedimiento_codigo on sol_examd_cod_presta=pc_id
    where sol_examd_solexam_id=$sol_exam_id";
    $reg_examenes=cargar_registros_obj($consulta, true);
    if(!$reg_examenes)
    {
        $reg_examenes=false;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
?>
<script type="text/javascript" >
    
</script>
<html>
    <div class='sub-content2' id='list_examenes' name='list_examenes' style='height:250px;overflow:auto;'>
        <?php
        if($reg_examenes!=false)
        {
            print("<table style='width:100%;'>");
                print("<tr class='tabla_header' style='font-size:12px;'>");
                    print("<td>");
                        print("C&oacute;digo");
                    print("</td>");
                    print("<td>");
                        print("Descripci&oacute;n");
                    print("</td>");
                    print("<td>");
                        print("Estado");
                    print("</td>");
                print("</tr>");
                for($i=0;$i<sizeof($reg_examenes);$i++)
                {
                    if($i%2==0) $clase='tabla_fila'; else $clase='tabla_fila2';
                    print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
                        print("<td style='text-align:left;'>".$reg_examenes[$i]['pc_codigo']."</td>");
                        if($reg_examenes[$i]['sol_examd_organo']=="")
                        {
                            print("<td style='text-align:left;'>".$reg_examenes[$i]['pc_desc']."</td>");
                        }
                        else
                        {
                            print("<td style='text-align:left;'>".$reg_examenes[$i]['pc_desc']." [".$reg_examenes[$i]['sol_examd_organo']."]"."</td>");
                        }
                        if(($reg_examenes[$i]['sol_examd_estado']*1)==0)
                        {
                            print("<td style='text-align:left;'>No Realizado</td>");
                        }
                        else
                        {
                            print("<td style='text-align:left;'>Realizado</td>");
                        }
                    print("</tr>");
                }
            print("</table>");
        }
        ?>
    </div>
</html>