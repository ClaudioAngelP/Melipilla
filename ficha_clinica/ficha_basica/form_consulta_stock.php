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
        var myAjax=new Ajax.Updater('listado','consulta_stock.php',
        {
            method:'post', parameters: $('str').serialize()+'&'+$('bodegas').serialize()
        });
    }
    </script>
    <?php 
    $bods=cargar_registros_obj("SELECT bod_id,bod_glosa FROM bodega WHERE bod_despacho OR bod_id=1 ORDER BY bod_glosa;");
    $t=sizeof($bods);
    for($b=0;$b<sizeof($bods);$b++)
    {
        $bodshtml.="
	<td style='text-align:left;'>
            <input type='checkbox' id='chk_".$b."' name='chk_".$b."' value='".$bods[$b]['bod_id']."' CHECKED>&nbsp;".htmlentities($bods[$b]['bod_glosa'])."
        </td>";
	if($f==3)
        {
            $bodshtml.="</tr><tr>";
        }
        $f++;
        if($f>3)
            $f=0;
    }
    ?>
    <body class='popup_background fuente_por_defecto' onLoad='$("str").focus();'>
        <div class='sub-content'>
            <img src='../../iconos/pill.png' />
            <b>Consulta de Saldos de Farmacias</b>
        </div>
        <table style='width:100%;'>
            <tr>
                <td style='text-align:right;'>Ingrese B&uacute;squeda:</td>
                <td>
                    <input type='text' id='str' name='str' style='width:350px;' onDblClick="this.value=''; $('art').innerHTML='';"  onKeyUp='if(event.which==13) buscar();' />
                    <input type='button' value='[Buscar...]' onClick='buscar();' />
                    <label id='art' name='art' ></label>
                </td>
            </tr>
            <tr>
                <td style='text-align:right;'>Seleccione Bodegas:</td>
                <td>
                    <div id='bods' name='bods' class='sub-content'>
                        <form id='bodegas' name='bodegas'>
                            <table>
                                <tr>
                                    <?php echo $bodshtml; ?>
                                </tr>
                            </table>
                        </form>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan=2>
                    <div class='sub-content2' id='listado' style='height:300px;overflow:auto;'>
                    </div>
                </td>
            </tr>
        </table>
        <script>
            seleccionar_articulo = function (art)
            {
                $('str').value=art[0];
                $('art').innerHTML=art[2];
                buscar();
            }


            autocompletar_medicamentos = new AutoComplete(
            'str',
            '../../autocompletar_sql.php',
            function() {
            if($('str').value.length<3) return false;
            return {
            method: 'get',
            parameters: 'tipo=buscar_arts&codigo='+$('str').value
            }
            }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_articulo);
        </script>
    </body>
</html>