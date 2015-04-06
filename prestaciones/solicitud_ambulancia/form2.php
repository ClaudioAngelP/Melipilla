<?php 
    require_once('../../conectar_db.php');
?>
<script>
    listado_sol=function() {
        var myAjax=new Ajax.Updater('listado_sol','prestaciones/solicitud_ambulancia/listado_solicitudes.php',
        {
            method:'post',
            parameters:$('select_tipo').serialize()
        });
    }
    
    guardar_mods=function(){
        
        
    }
</script>
<center>
    <div class='sub-content' style='width:950px;'>
        <div class='sub-content'>
            <img src='iconos/building_go.png' />
            <b>Gesti&oacute;n de Solicitudes de Traslado en Ambulancia</b>
        </div>
        <table style="width: 100%">
            <tr>
                <td>
                    Estado de Solicitudes:
                </td>
                <td>
                    <select id="select_tipo" name="select_tipo" >
                        <option value="1" selected="">Solicitudes Por Validar</option>
                        <option value="2" >Solicitudes Validadas</option>
                    </select>
                </td>
            </tr>
        </table>
        <div class='sub-content2' style='height:400px;overflow:auto;' id='listado_sol' >
        </div>
        <center><input type='button' value='-- Guardar Modificaciones --' onClick='guardar_mods();' /></center>
    </div>
</center>
<script type="text/javascript" >
    listado_sol();
</script>