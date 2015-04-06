<?php
    require_once('../../conectar_db.php'); 
    $str=pg_escape_string(utf8_decode($_POST['str']))*1; 
?>
<table style='width:100%;'>
    <tr class='tabla_header'>
        <td>N&deg;</td>
        <td>Fecha Emisi&oacute;n</td>
        <td>Paciente</td>
        <td>Bodega</td>
        <td>Ver</td>
    </tr>
    <?php 
    $s=cargar_registros_obj("SELECT receta_numero,receta_fecha_emision::date,
    pac_rut||' '||pac_nombres||' '||pac_appat||' '||pac_apmat AS pac,bod_glosa,receta_id,
    (receta_fecha_emision::date=CURRENT_DATE) AS receta_editable,receta_tipotalonario_id,receta_bod_id
    FROM receta
    LEFT JOIN pacientes ON pac_id=receta_paciente_id
    LEFT JOIN bodega ON bod_id=receta_bod_id
    WHERE receta_numero='$str'", true);
    if($s)
        for($i=0;$i<sizeof($s);$i++)
        {
            $clase=($i%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase' style='cursor:pointer;' onMouseOver='this.className=\"mouse_over\"' onMouseOut='this.className=\"$clase\";'>
            <td style='text-align:right;'>".$s[$i]['receta_numero']."</td>
            <td style='text-align:left;font-size:12px;'>".$s[$i]['receta_fecha_emision']."</td>
            <td style='text-align:left;font-weight:bold;'>".($s[$i]['pac'])."</td>
            <td style='text-align:left;font-weight:bold;'>".($s[$i]['bod_glosa'])."</td>
            <td><center><b><i>
            ");
            //if(_cax(505) OR $s[$i]['receta_editable']=='t')
            if(_cax(505))
                print("
                <img src='../../iconos/pencil.png' style='cursor:pointer;' onClick='window.opener.editar_receta(".$s[$i]['receta_id'].",".$s[$i]['receta_tipotalonario_id'].",".$s[$i]['receta_bod_id'].");window.close();');'>
		<img src='../../iconos/calendar_delete.png' style='cursor:pointer;' onClick='despachos_receta(".$s[$i]['receta_id'].");');'>
		");
            
            print("
            <img src='../../iconos/printer.png' style='cursor:pointer;'	onClick='imprimir_talonario(".$s[$i]['receta_id'].");'></i></b></center></td>
            </tr>");
        }
    ?>
</table>