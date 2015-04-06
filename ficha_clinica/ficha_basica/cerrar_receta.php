<?php
    require_once('../../conectar_db.php');
    $receta_id = ($_GET['receta_id']*1);
    $nro = pg_query($conn,"select receta_numero,(doc_nombres||' '||doc_paterno||' '||doc_materno) as doc_nombre,
    date_trunc('second',receta_fecha_emision) as receta_fecha_emision,
    (pac_nombres||' '||pac_appat||' '||pac_apmat) as pac_nombre from receta
    left join doctores on doc_id=receta_doc_id
    left join pacientes on pac_id=receta_paciente_id
    where receta_id=".$receta_id);
    $nro_rec = pg_fetch_row($nro);
    $nro_receta = $nro_rec[0];
    $doc_nombre = $nro_rec[1];
    $fecha_receta = $nro_rec[2];
    $pac_nombre = $nro_rec[3];
?> 
<script>
    $('motivo').focus();
    verifica_cerrar = function()
    {
        //console.log($('motivo').value);
        //console.log($('motivo').value.length);
        if($('motivo').value=='' || $('motivo').value.length<=5)
        {
            alert('No se puede terminar la receta sin un motivo.'); 
            return;
     	}
        var myAjax = new Ajax.Request('recetas/entregar_recetas/sql_cerrar_receta.php', 
        {
            method: 'get', 
            parameters: $('receta_id').serialize()+'&'+$('motivo').serialize(),
            onComplete: function(cerrada)
            {
                console.log(cerrada);
               	if(cerrada.responseText=="OK")
                {
                    alert('Receta terminada exitosamente.');
                    mostrar_recetas();
                    $("cerrar_receta").win_obj.close();
                }
                else
                {
                    alert('ERROR\n\n'+cerrada.responseText);
                }
            }
        });
    }
</script>
<div class='sub-content'>
    <table width=100%;>
        <tr>
            <td style='text-align: right;'>N&uacute;mero Receta: </td>
            <td style='text-align: left;'><b><?php echo $nro_receta ?></b></td>
            <td style='text-align: right;'>Fecha Emisi&oacute;n: </td>
            <td style='text-align: left;'><b><?php echo $fecha_receta ?></b></td>
        </tr>
        <tr>
            <td style='text-align: right;'>M&eacute;dico: </td>
            <td style='text-align: left;' colspan=3><b><?php echo $doc_nombre ?></b></td>
        </tr>
	<tr>
            <td style='text-align: right;'>Paciente: </td>
            <td style='text-align: left;' colspan=3><b><?php echo $pac_nombre ?></b></td>
        </tr>
    </table>
</div>
<div class='sub-content'>
    <center><b>Motivo para el t&eacute;rmino de la receta</b></center>
</div>
<div class='sub-content3' style='overflow: auto;'>
    <input type='hidden' id='receta_id' name='receta_id' value='<?php echo $receta_id ?>'>
    <center>
        <textarea id='motivo' name='motivo' cols=53 rows=2 ></textarea>
    </center>
</div>
<center>
    <table>
        <tr>
            <td>
                <div class='boton'>
                    <table>
                        <tr>
                            <td><img src='iconos/accept.png'></td>
                            <td><a href='#' onClick='verifica_cerrar();' >Terminar Receta...</a></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</center>