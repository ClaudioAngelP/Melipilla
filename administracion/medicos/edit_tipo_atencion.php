<?php
    require_once("../../conectar_db.php");
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $nomd_id=($_POST['nomd_id']*1);
    $doc_id=($_POST['doc_id']*1);
    $esp_id=($_POST['esp_id']*1);
    $fecha=$_POST['fecha'];
    $nomd_hora=$_POST['nomd_hora'];
    $cupo_id=($_POST['cupo_id']*1);
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $consulta="SELECT nom_motivo from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=$nomd_id";
    $reg_original=cargar_registro("SELECT nom_motivo from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=$nomd_id", true);
    if($reg_original)
       $motivo_original=$reg_original['nom_motivo'];
    else
    {
        print("Error al buscar motivo original");
        die();
    }
       
    $tipo_atencion_edit=cargar_registros_obj("SELECT DISTINCT nom_motivo from nomina where nom_motivo is not null order by nom_motivo", true);
    $tipoatencionhtml_edit='';
    for($i=0;$i<count($tipo_atencion_edit);$i++)
    {
        if($motivo_original!=$tipo_atencion_edit[$i]['nom_motivo'])
        {
            $tipoatencionhtml_edit.='<option value="'.$tipo_atencion_edit[$i]['nom_motivo'].'">'.$tipo_atencion_edit[$i]['nom_motivo'].'</option>';
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
?>
<script type="text/javascript" >
    var bloquear_mod_atencion=false;
    
    
    modificar_tipo_atencion = function()
    {
        var nomd_id=<?php echo $nomd_id; ?>;
        var doc_id=<?php echo $doc_id; ?>;
        var esp_id=<?php echo $esp_id; ?>;
        var fecha="<?php echo $fecha; ?>";
        var nomd_hora="<?php echo $nomd_hora; ?>";
        var cupo_id="<?php echo $cupo_id; ?>";
        if(bloquear_mod_atencion)
        {
            return;
        }
        if($j('#select_motivo_edit').val()==-1)
        {
            alert('Debe seleccionar Nuevo tipo de atenci&oacute;n para el cupo seleccionado'.unescapeHTML());
            return;
        }
        confir=confirm(('Esta seguro realizar el cambio del tipo de atenci&oacute;n').unescapeHTML());
        if(confir)
        {
            bloquear_mod_atencion=true;
            $j.ajax(
            {
                url: 'sql_tipo_atencion.php',
                type: 'POST',
                dataType: 'json',
                async:false,
                data: {nomd_id: nomd_id,doc_id:doc_id,esp_id:esp_id,fecha:fecha,nomd_hora:nomd_hora,tipo_atencion_original:$j('#select_motivo_original').val(),tipo_atencion_nueva:$j('#select_motivo_edit').val(),cupo_id: cupo_id},
                success: function(data)
                {
                    resp=data;
                    if(resp[0]=='OK')
                    {
                    
                        fechas_ocupadas=resp[1];
                        cargar_horas(fecha);
                        $("form_change_motivo").win_obj.close();
                        $("editar_cupos").win_obj.close();
                        /*
                        if(resp[1]==0)
                        {
                            $j('#span_cantidad_relaciones').unbind('click');
                            $j('#span_cantidad_relaciones').html('( [ '+resp[1]+' ] - Documentos Relacionados )');
                        }
                        else
                        {
                            $j('#span_cantidad_relaciones').unbind('click');
                            $j('#span_cantidad_relaciones').click(function ()
                            {
                                abrir_relacion_doc(resp[2],resp[3]);
                            });
                            $j('#span_cantidad_relaciones').html('( [ '+resp[1]+' ] - Documentos Relacionados )');
                        }
                        $j('#nro_relacion').val(resp[2]);
                        listar_document();
                        */
                    }
                    else
                    {
                        alert("No se puede cambiar tipo de atenci&oacute; del cupo seleccionado".unescapeHTML());
                        return;
                    }
                }
            });
            bloquear_mod_atencion=false;
            //listar_documentos();
            //listar_enviados();
            //listar_porrecepcionar();
        }
    }
    
    cancelar_mod_atencion = function()
    {
        $("form_change_motivo").win_obj.close();
    }

</script>
<html>
    <table>
        <tr>
            <td style='text-align:left;width:110px;white-space:nowrap;' valign='top' class='tabla_fila2'>Tipo de Atenci&oacute;n:</td>
            <td class='tabla_fila'>
                <select id='select_motivo_original' name='select_motivo_original'>
                    <option value="<?php echo $motivo_original;?>"><?php echo $motivo_original;?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td style='text-align:left;width:110px;white-space:nowrap;' valign='top' class='tabla_fila2'>Nuevo Tipo de Atenci&oacute;n:</td>
            <td class='tabla_fila'>
                <select id='select_motivo_edit' name='select_motivo_edit'>
                    <option value='-1' selected="">(Seleccionar Nuevo Tipo...)</option>
                    <?php echo $tipoatencionhtml_edit;?>
                </select>
            </td>
        </tr>
    </table>
    <center>
        <input type="button" id="btn_modificar" name="btn_modificar" value="Modificar" onclick="modificar_tipo_atencion();">
        <input type="button" id="btn_cancelar" name="btn_cancelar" value="Cancelar" onclick="cancelar_mod_atencion();">
    </center>
</html>