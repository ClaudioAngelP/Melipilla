<?php 
    require_once('../../conectar_db.php');
    $esp=cargar_registros_obj("SELECT * FROM especialidades ORDER BY esp_desc", true);
    $pro=cargar_registros_obj("SELECT * FROM procedimiento ORDER BY esp_desc", true);
?>
<script>
    editar_esp=function(esp_id)
    {
        top=Math.round(screen.height/2)-200;
        left=Math.round(screen.width/2)-375;
        new_win = window.open('administracion/mantenedor_especialidades/editar.php?esp_id='+esp_id,      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+      'menubar=no, scrollbars=yes, resizable=no, width=750, height=400, '+      'top='+top+', left='+left);
        new_win.focus();		
    }
</script>
<center>
    <div class='sub-content' style='width:800px;'>
        <div class='sub-content'>
            <img src='iconos/building.png'>
            <b>Mantenedor de Especialidades/Servicios</b>
        </div>
        <div class='sub-content2' style='height:350px;overflow:auto;' id='listado'>
        <?php 
        print("<table style='width:100%;'>");
            print("<tr class='tabla_header'>");
                print("<td>C&oacute;digo Int.</td>");
                print("<td style='width:50%;'>Descripci&oacute;n</td>");
                print("<td>Cod. Orden</td>");
                print("<td>Editar</td>");
            print("</tr>");
        if($esp)
            for($i=0;$i<sizeof($esp);$i++)
            {
                $clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
                print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>");
                    print("<td>".$esp[$i]['esp_id']."</td>");
                    print("<td>".$esp[$i]['esp_desc']."</td>");
                    print("<td>".$esp[$i]['esp_codigo_int']."</td>");
                    print("<td><center><img src='iconos/pencil.png' style='cursor:pointer;' onClick='editar_esp(".$esp[$i]['esp_id'].");' /></center></td>");
                print("</tr>");	
            }
        print("</table>");
        ?>
        </div>
        <center>
            <input type='button' id='' value='Crear Nueva Especialidad...' onClick='editar_esp(0);' />
        </center>
    </div>
</center>