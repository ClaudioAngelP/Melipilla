<?php
    require_once('../../conectar_db.php');
    $esp_id=$_POST['esp_id'];
    $doc_id=$_POST['doc_id'];
    
    
    $consulta="select * from diagnosticos where diag_cod in (
    select distinct  trim(split_part(nomd_diag,'|',1))cod_diag from nomina_detalle 
    join nomina using (nom_id)
    where nomd_diag ilike '%|%' 
    and nom_esp_id=$esp_id and nom_doc_id=$doc_id
    ) order by diag_desc";
    $reg_diag_frecuentes=cargar_registros_obj($consulta, true);
    if(!$reg_diag_frecuentes)
    {
        $reg_diag_frecuentes=false;
    }
    //$espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades where esp_recurso=true ORDER BY esp_desc", NULL, '', '');
?>
<script>
    utilizar=function(index)
    {
        $('nomd_diag_cod').value=$('txt_diagcod_'+index+'').value;
        $('nomd_diagnostico').value=$('txt_diagdesc_'+index+'').value;
        $('diag_personal').value='';
        $('diag_frecuentes').win_obj.close();
    }
    
</script>
<div class='sub-content'>
    <?php cabecera_popup('../..'); ?>
    <?php
    if(!$reg_diag_frecuentes)
    {
    ?>
        No Presenta Diagnosticos utilizados anteriormente.-
    <?php
    }
    else
    {
    ?>
    <table style="width:100%;font-size:11px;">
        <tr class="tabla_header">
            <td>Cod. Diag.</td>
            <td>Desc. Diag.</td>
            <td>&nbsp;</td>
        </tr>
        <?php
        for($i=0;$i<count($reg_diag_frecuentes);$i++)
        {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print('<tr class="'.$clase.'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''.$clase.'\';">');
                print("<input type='hidden' id='txt_diagcod_$i' name='txt_diagcod_$i' value='".$reg_diag_frecuentes[$i]['diag_cod']."' />");
                print("<input type='hidden' id='txt_diagdesc_$i' name='txt_diagdesc_$i' value='".$reg_diag_frecuentes[$i]['diag_desc']."' />");
                print("<td style='text-align:center;font-weight:bold;'>".$reg_diag_frecuentes[$i]['diag_cod']."</td>");
                print("<td style='text-align:left;'>".$reg_diag_frecuentes[$i]['diag_desc']."</td>");
                print("<td><img src='../../iconos/accept.png'  style='cursor:pointer;' onClick='utilizar($i);' /></td>");
            print("</tr>");
        }
        ?>
    </table>
    <?php
    }
    ?>
</div>