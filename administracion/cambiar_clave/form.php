<?php

	require_once("../../conectar_db.php");

?>
<script>

   bloqueo=false;

    habilitar = function()
    {
        $('datos').style.display='';
        $('guardar_clave').style.display='';
        $('cancelar_cambios').style.display='';
        $('cambiar_clave').style.display='none';
        $('txt_clave_ant').focus();
    }

    desabilitar = function()
    {
        $('txt_clave_ant').value='';
        $('txt_clave_nuev').value='';
        $('txt_rept_nuev').value='';
        $('datos').style.display='none';
        $('guardar_clave').style.display='none';
        $('cancelar_cambios').style.display='none';
        $('cambiar_clave').style.display='';
    }

    guardar  = function()
    {

        if(bloqueo) return;

        if($('txt_clave_ant').value=='' )
        {
            alert('Debe ingresar Clave de inicio actual'.unescapeHTML());
            $('txt_clave_ant').select();
            return;
        }
        if($('txt_clave_nuev').value=='')
        {
            alert('Debe ingresar Clave de inicio Nueva'.unescapeHTML());
            $('txt_clave_nuev').select();
            return;
        }
        if($('txt_rept_nuev').value=='')
        {
            alert('Debe Repetir la Clave de inicio Nueva'.unescapeHTML());
            $('txt_rept_nuev').select();
            return;
        }
        if($('txt_clave_nuev').value!=$('txt_rept_nuev').value)
        {
            alert('La clave Nueva y la de confirmaci&oacute;n de la clave nueva son distintas'.unescapeHTML());
            $('txt_clave_nuev').select();
            return;
        }

        clave_actual=document.getElementById('txt_clave_ant').value;

        bloqueo=true;

        var myAjax = new Ajax.Request('administracion/cambiar_clave/sql.php',
        {
            method:'get',
            parameters: $('txt_clave_ant').serialize()+'&'+$('txt_clave_nuev').serialize(),
            evalScripts:true,
            onComplete: function(resp)
            {

                bloqueo=false;

                if(resp.responseText==1)
                {
                    alert('Clave de inicio al sistema Modificada Exitosamente')
                    desabilitar();

                }
                else
                {
                   alert('La clave de inicio actual no es correcta'.unescapeHTML());
                   $('txt_clave_ant').select();
                   $('txt_clave_ant').focus();
                   return;
                }


            }
        }
        );
    }
</script>
<center>
<div class='sub-content' style="width:650px;">
    <div class="sub-content">
        <img src="iconos/wand.png">
        <b>Cambio de Clave de Acceso a Sistema</b>
    </div>
    <div class="sub-content3">
        <div class='sub-content'>
            <img src="iconos/user.png">
            <b>Datos de Funcionario</b>
            
            <!--<input type='hidden' name='usuario_id' id='usuario_id' value="<--?php echo(htmlentities($_SESSION['sgh_usuario_id'])); ?>"-->
        </div>
        <center>
            <table style='padding: 5px;'>
                <tr>
                    <td style='text-align: right;'><b>Rut:</b></td>
                    <td><?php print (htmlentities($_SESSION['sgh_username'])); ?></td>
                </tr>
                <tr>
                    <td style='text-align:right;'><b>Nombre:</b></td>
                    <td><?php print (htmlentities($_SESSION['sgh_usuario'])); ?></td>
                </tr>
                <tr>
                    <td style='text-align:right;'><b>Cargo:</b></td>
                    <td><?php print (htmlentities($_SESSION['sgh_cargo'])); ?></td>
                </tr>
            </table>
            <div id='cambiar_clave' class='boton'>
                <center>
                <table>
                <tr>
                    <td>
                        <img src='iconos/database_edit.png'>
                    </td>
                    <td>
                        <a href='#' onClick='habilitar();' style='text-align:center;'>Cambiar Clave de Acceso</a>
                    </td>
                </tr>
                </table>
                </center>
            </div>
            <div id='datos' class='sub-content'style="width:280px; display:none;" >
                <table>
                <tr>
                    <td style='text-align: right;'><b>Clave de Inicio Actual:</b></td>
                    <td><input type='password' name='txt_clave_ant' id='txt_clave_ant' size=10></td>
                </tr>
                <tr>
                    <td style='text-align:right;'><b>Clave de Inicio Nueva</b></td>
                    <td><input type='password' name='txt_clave_nuev' id='txt_clave_nuev' size=10></td>
                </tr>
                <tr>
                    <td style='text-align:right;'><b>Repita Clave de Inicio Nueva</b></td>
                    <td><input type='password' name='txt_rept_nuev' id='txt_rept_nuev' size=10></td>
                </tr>
                </table>
            </div>
            <table>
            <tr>
                <td>
                <div id='guardar_clave' class='boton'style="display:none;">
                <center>
                <table>
                <tr>
                    <td>
                        <img src='iconos/database_edit.png'>
                    </td>
                    <td>
                        <a href='#' onClick='guardar();' style='text-align:center;'>Guardar Clave Nueva</a>
                    </td>
                </tr>
                </table>
                </center>
                </div>
                </td>
                <td>
                <div id='cancelar_cambios' class='boton' style="display:none;">
                    <center>
                    <table>
                    <tr>
                        <td>
                            <img src='iconos/cancel.png'>
                        </td>
                        <td>
                            <a href='#' onClick='desabilitar();' style='text-align:center;'>Cancelar Cambios</a>
                        </td>
                    </tr>
                    </table>
                    </center>
                </div>
                </td>
            </tr>
            </table>
        </center>
    </div>
 </div>
</center>