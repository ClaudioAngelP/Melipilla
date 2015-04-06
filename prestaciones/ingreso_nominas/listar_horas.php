<?php
    require_once('../../conectar_db.php');
    $nom_id=$_POST['nom_id']*1;
    $consulta="SELECT DISTINCT nomd_hora FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora IS NOT NULL AND NOT nomd_hora='00:00:00' ORDER BY nomd_hora";
    $reg_horas=cargar_registros_obj($consulta);
    if(!$reg_horas)
    {
        $reg_horas=false;
    }
?> 
<script>
    
    
</script>
<br>
<input type='hidden' id='nom_id' name='nomd_id' value='<?php echo $nom_id; ?>' />
<div class='sub-content2' id='lista_presta_examen' style='height:200px;overflow:auto;'>
    <table >
        <tr class="tabla_header" style="font-size:12px;">
            <td>&nbsp;</td>
            <td>Hora</td>
        </tr>
<?php   
        for ($k=0;$k<count($reg_horas);$k++)
        {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>");
                print("<td><input type='checkbox' id='chk_hora' name='chk_hora' ></td>");
                print("<td>".substr($reg_horas[$k]['nomd_hora'],0,5)."</td>");
            print("</tr>");
        }
?>
    </table>
</div>
<script>
    
    
</script>
