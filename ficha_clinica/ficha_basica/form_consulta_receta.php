<?php
    require_once('../../conectar_db.php');
?>
<html>
    <title>Consulta Saldos Farmacias</title>
    <?php cabecera_popup('../..'); ?>
    <script>
        function buscar()
        {
            if(trim($('str').value).length<3)
                return;
            $('listado').innerHTML='<center><br><br><img src="../../imagenes/ajax-loader3.gif" /></center>';
            var myAjax=new Ajax.Updater('listado','consulta_receta.php',
            {
                method:'post',
                parameters: $('str').serialize()
            });
        }

        despachos_receta = function (receta_id)
        {
            win = window.open('despachos_receta.php?receta_id='+receta_id,'_self');
            win.focus();                
        }

        imprimir_talonario = function (receta_id)
        {
            win = window.open('../../recetas/entregar_recetas/talonario.php?receta_id='+receta_id,'win_talonario');
            win.focus();
        }
    </script>
    <body class='popup_background fuente_por_defecto' onLoad='$("str").focus();'>
        <div class='sub-content'>
            <img src='../../iconos/pill.png' />
            <b>Consulta de Recetas de Farmacias</b>
        </div>
        <table style='width:100%;'>
            <tr>
                <td style='text-align:right;'>Ingrese N&uacute;mero:</td>
                <td>
                    <input type='text' id='str' name='str' style='width:350px;' onDblClick="this.value=''; $('art').innerHTML='';"  onKeyUp='if(event.which==13) buscar();' />
                    <input type='button' value='[Buscar...]' onClick='buscar();' />
                </td>
            </tr>
            <tr>
                <td colspan=2>
                    <div class='sub-content2' id='listado' style='height:300px;overflow:auto;'>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>