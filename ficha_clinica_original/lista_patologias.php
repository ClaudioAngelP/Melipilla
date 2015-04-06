<?php
    require_once('../conectar_db.php');
?>
<table style='width:100%;'>
    <tr style='background-color:#dddddd;'>
        <td colspan=3>Patolog&iacute;as Activas</td>
    </tr>
    <?php 
    $pac_id=$_GET['pac_id']*1;
    $bod_id=$_GET['farma']*1;
    
    $opcion_patologia=$_GET['opcion_patologia']*1;
            
    if(isset($_GET['accion']))
    {
        $pat=$_GET['pat']*1;
        if($_GET['accion']=='eliminar')
        {
            if($opcion_patologia==1)
            {
                $pacpat_id=$_GET['pacpat_id']*1;
                // pacpat_func_id IS NULL OR
                pg_query("DELETE FROM pacientes_patologia WHERE pacpat_id=$pacpat_id AND pacpat_func_id=".$_SESSION['sgh_usuario_id']);
            }
            if($opcion_patologia==2)
            {
                $pacpat_id=$_GET['pacpat_id']*1;
                pg_query("UPDATE autorizacion_farmacos_pacientes SET autfp_vigente=false, autfp_fecha_elimina=CURRENT_TIMESTAMP WHERE autf_id=$pacpat_id AND pac_id=$pac_id");
            }
            
	}
        
        
	if($_GET['accion']=='agregar')
        {
            $pacpat_desc=pg_escape_string(strtoupper(trim(utf8_decode($_GET['pacpat_desc']))));
            if($opcion_patologia==1)
            {
                $chk=cargar_registro("SELECT * FROM pacientes_patologia WHERE pac_id=$pac_id AND pacpat_descripcion ILIKE '%$pacpat_desc%'");
                if(!$chk)
                {
                    pg_query("INSERT INTO pacientes_patologia VALUES (DEFAULT, $pac_id, '$pacpat_desc', null, null, ".$_SESSION['sgh_usuario_id'].");");
                }
            }
            if($opcion_patologia==2)
            {
                $chk=cargar_registro("SELECT * FROM autorizacion_farmacos_pacientes WHERE pac_id=$pac_id AND autf_id=$pat and autfp_vigente=true");
                if(!$chk)
                {
                    pg_query("INSERT INTO autorizacion_farmacos_pacientes VALUES (DEFAULT, $pat, $pac_id, ".$_SESSION['sgh_usuario_id'].", 0, now(), null);");
                }
            }
	}
    }
    $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id;");
    $l=cargar_registros_obj("SELECT * FROM monitoreo_ges WHERE mon_rut='".$pac['pac_rut']."' ORDER BY mon_patologia;", true);
    $x=0;
    if($l)
    {
        for($i=0;$i<sizeof($l);$i++)
        {
            $clase=($x%2==0)?'tabla_fila':'tabla_fila2';
            print("<tr class='$clase'>");
                print("<td style='color:blue;'>".$l[$i]['mon_patologia']." (".$l[$i]['mon_garantia'].")</td>");
                print("<td>&nbsp;</td>");
                print("<td>&nbsp;</td>");
            print("</tr>");
            $x++;
	}
    }
    if($opcion_patologia==1)
    {
        $l=cargar_registros_obj("SELECT * FROM pacientes_patologia LEFT JOIN funcionario ON pacpat_func_id=func_id WHERE pac_id=$pac_id ORDER BY pacpat_descripcion;", true);
        if($l)
        {
            for($i=0;$i<sizeof($l);$i++)
            {
                $clase=($x%2==0)?'tabla_fila':'tabla_fila2';
                print("<tr class='$clase'>");
                    print("<td style='color:green;'>".$l[$i]['pacpat_descripcion']."</td>");
                    print("<td style='font-size:11px;'>".$l[$i]['func_nombre']."</td>");
                    print("<td style='width:50px;'>");
                        print("<center><img src='iconos/delete.png' style='cursor:pointer;' onClick='eliminar_pat(".$l[$i]['pacpat_id'].");'></center>");
                    print("</td>");
                print("</tr>");
                $x++;
            }
        }
    }
    if($opcion_patologia==2)
    {
        $consulta="select * from (
        SELECT *,
        (case when autfp_fecha_final is null then
        (CASE WHEN (autfp_vigente AND autfp_fecha_inicio<=CURRENT_DATE) THEN true ELSE false END)
        else
        (CASE WHEN (autfp_vigente AND autfp_fecha_inicio<=CURRENT_DATE AND autfp_fecha_final>CURRENT_DATE) THEN true ELSE false END)
        end)as vigente
        FROM autorizacion_farmacos_pacientes 
        JOIN autorizacion_farmacos USING (autf_id)
        JOIN pacientes USING (pac_id)
        JOIN funcionario USING (func_id) 
        WHERE pac_id=$pac_id)as foo
        where vigente
        ";
        
        $l=cargar_registros_obj($consulta, true);
        if($l)
        {
            for($i=0;$i<sizeof($l);$i++)
            {
                $clase=($x%2==0)?'tabla_fila':'tabla_fila2';
                print("<tr class='$clase'>");
                    print("<td style='color:green;'>".$l[$i]['autf_patologia_ges']."</td>");
                    print("<td style='font-size:11px;'>".$l[$i]['func_nombre']."</td>");
                    print("<td style='width:50px;'>");
                        print("<center><img src='iconos/delete.png' style='cursor:pointer;' onClick='eliminar_pat(".$l[$i]['autf_id'].");'></center>");
                    print("</td>");
                print("</tr>");
                $x++;
            }
        }
    }
    $clase=($x%2==0)?'tabla_fila':'tabla_fila2';
    print("<tr class='$clase'>");
        print("<td style='color:green;' colspan=2>");
            print("<input type='hidden' id='pacpat_valid' name='pacpat_valid' />");
            print("<input type='text' id='pacpat_nueva' name='pacpat_nueva' style='width:100%;' value='' />");
        print("</td>");
        print("<td style='width:50px;'>");
            print("<center>");
                print("<img src='iconos/add.png' style='cursor:pointer;' onClick='agregar_pat(1);'>");
            print("</center>");
        print("</td>");
    print("</tr>");
    ?>
</table>
<?php
//if(($bod_id==36) OR ($bod_id==4))
if(($bod_id==36))
{
?>
    <script>
        seleccionar_patologia = function(d)
        {
            $('pacpat_valid').value=d[0];
            $('pacpat_nueva').value=d[1].unescapeHTML();
        }
        autocompletar_patologia = new AutoComplete(
        'pacpat_nueva', 
        'autocompletar_sql.php',
        function() {
        if($('pacpat_nueva').value.length<3) return false;
        return {
        method: 'get',
        parameters: 'tipo=control_patologia&'+$('pacpat_nueva').serialize()
        +'&bod='+encodeURIComponent($('bodega_id').value)
        +'&opcion_patologia='+encodeURIComponent(<?php echo $opcion_patologia;?>)
        }
        }, 'autocomplete', 650, 100, 150, 1, 1, seleccionar_patologia);
        
    </script>
<?php
}
?>