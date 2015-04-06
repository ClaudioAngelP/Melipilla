<?php
    require_once("../../conectar_db.php");
    $esp_id=$_POST['esp_id']*1;
    $grupo_exam=pg_escape_string(utf8_decode($_POST['grupo_exam']));
    
    $reg_esp=cargar_registro("SELECT upper(esp_desc)as esp_desc FROM especialidades WHERE esp_id=$esp_id");
    $esp_desc=$reg_esp['esp_desc'];
    
    
    if($grupo_exam!="kit") {
        if($grupo_exam=="0")
            $string_grupo="";
        elseif($grupo_exam!="")
            $string_grupo="AND upper(pc_grupo_examen)=upper('$grupo_exam')";
        else
            $string_grupo="AND (pc_grupo_examen is null or pc_grupo_examen='')";
        
        $consulta="SELECT distinct on(pc_id)pc_id,codigo,pc_desc,glosa,pc_grupo,pc_grupo_examen,pc_activo,tipo FROM (    
        SELECT (case when strpos(pc_codigo, '.')>0 then pc_codigo else codigo end) as codigo,
        upper(pc_desc)as pc_desc, 
        glosa,
        pc_id,
        upper(pc_grupo)as pc_grupo,
        upper(pc_grupo_examen)as pc_grupo_examen,pc_activo,
        0 as tipo
        FROM codigos_prestacion
        JOIN procedimiento_codigo ON esp_id=$esp_id AND split_part(pc_codigo, '.', 1)=codigo $string_grupo
        order by tipo desc,pc_desc
        )as foo
        where pc_activo";
        
        //print($consulta);
        
              
        $reg_prestaciones = cargar_registros_obj($consulta,true);
        if(!$reg_prestaciones)
        {
            $reg_prestaciones=false;
        }
    } else {
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
        ";
        
        $reg_prestaciones = cargar_registros_obj($consulta,true);
        if(!$reg_prestaciones)
        {
            $reg_prestaciones=false;
        }
        
        
        
        
        
    }
?>
<script type="text/javascript" >

</script>
<form id='form_list_examenes' name='form_list_examenes' onSubmit='return false;'>
    <div class="sub-content">
        <table style="font-size:12px;width: 100%;">
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Tipo Ex&aacute;menes:</td>
                <td class='tabla_fila'><b><?php echo $esp_desc; ?></b></td>
            </tr>
            <tr>
                <td style="text-align:left;width:150px;white-space:nowrap;" valign="top" class="tabla_fila2">Grupo Ex&aacute;men:</td>
                <?php if($grupo_exam=="0") {?>
                    <td class='tabla_fila'><b>Todos Los Grupos</b></td>
                <?php } elseif($grupo_exam=="") {?>
                    <td class='tabla_fila'><b>Sin Grupo Asignado</b></td>                    
                <?php } else {?>
                    <td class='tabla_fila'><b><?php echo $grupo_exam; ?></b></td>
                <?php } ?>
            </tr>
        </table>
        <div class="sub-content2" style="height:500px;overflow:auto;">
            <?php 
            if($reg_prestaciones){
                print('<table style="width:100%;">');
                    print('<tr class="tabla_header" style="font-size:12px;">');
                        print('<td style="text-align:center;font-size:12px;">C&Oacute;DIGO</td>');
                        print('<td style="text-align:center;font-size:12px;">PRESTACI&Oacute;N</td>');
                        print('<td style="text-align:center;font-size:12px;">SECTOR</td>');
                        print('<td style="text-align:center;font-size:12px;">TIPO</td>');
                        print('<td style="text-align:center;font-size:12px;">&nbsp;</td>');
                    print('</tr>');
                    for($i=0;$i<count($reg_prestaciones);$i++) {
                        ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                        $color='';
                        $string_kit='';
                        if(($reg_prestaciones[$i]['tipo']*1)=='1') {
                            $color="font-weight:bold;color:red;";
                            $string_kit=" - (KIT)";
                        }
                        print("<tr class='$clase' style='height:30px;background-color:$color' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";' onClick=''>");
                            print("<input type='hidden' id='presta_codigo_".$i."' name='presta_codigo_".$i."' value='".$reg_prestaciones[$i]['codigo']."' />");
                            print("<input type='hidden' id='presta_desc_".$i."' name='presta_desc_".$i."' value='".$reg_prestaciones[$i]['pc_desc']."' />");
                            print("<input type='hidden' id='pc_grupo_".$i."' name='pc_grupo_".$i."' value='".$reg_prestaciones[$i]['pc_grupo']."' />");
                            print("<input type='hidden' id='pc_id_".$i."' name='pc_id_".$i."' value='".$reg_prestaciones[$i]['pc_id']."' />");
                            print('<td style="font-size:12px;text-align:left;">'.$reg_prestaciones[$i]['codigo'].'</td>');
                            print('<td style="font-size:12px;text-align:left;">'.$reg_prestaciones[$i]['pc_desc'].''.$string_kit.'</td>');
                            print('<td style="font-size:10px;text-align:left;">'.$reg_prestaciones[$i]['pc_grupo'].'</td>');
                            print('<td style="font-size:10px;text-align:center;">'.$reg_prestaciones[$i]['pc_grupo_examen'].'</td>');
                            print('<td><center><img src="../../iconos/add.png" style="cursor: pointer;" onClick="agregar_prestacion_examen('.$i.',1,'.$reg_prestaciones[$i]['tipo'].');"></center></td>');
                        print('</tr>');
                        
                        
                        
                    }
                    
                    
                    
                    
                print("</table>");
                
                
            }
            ?>
        </div>
    </div>
</form>