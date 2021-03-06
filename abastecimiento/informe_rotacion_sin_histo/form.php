<?php
    require_once('../../conectar_db.php');
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(4).')', 'ORDER BY bod_glosa');
?>
<script>
    mostrar_valores=function()
    {
        if ($('valorizado').checked)
        {
            $("valor_tr").show();
        }
        else
        {
            $("valor_tr").hide();
        }
        
   }
    
</script>
<center>
    <div class='sub-content' style='width:850px;'>
    <div class='sub-content'>
        <img src='iconos/package_go.png' />
        <b>Indice de Rotaci&oacute;n por Bodega</b>
    </div>
    <form id='reporte' name='reporte' method='post' action='abastecimiento/informe_rotacion/listado_rotacion.php' onSubmit='return false;' target='_self'>
        <input type='hidden' id='xls' name='xls' value='1' />
        <div class='sub-content'>
            <table style='width:100%;'>
                <tr id='ubicacion'>
                    <td style='text-align:right;'>Ubicaci&oacute;n:</td>
                    <td>
                        <select id='bod_id' name='bod_id'>
				<?php echo $bodegashtml; ?>
			</select>
                    </td>
                </tr>
                <tr>
                    <td style='text-align: right;'>Fecha Inicio:</td>
                    <td>
                        <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y",mktime(0,0,0,date('m')-1,date('d'),date('Y'))); ?>'>
                        <img src='iconos/date_magnify.png' id='fecha1_boton'>
                    </td>
                </tr>
                <tr>
                    <td style='text-align: right;'>Fecha Final:</td>
                    <td>
                        <input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
                        <img src='iconos/date_magnify.png' id='fecha2_boton'>
                    </td>
                </tr>
                <tr>
                    <td style='text-align: right;'>Informe:</td>
                    <td>
                        <select id='tipo_reporte' name='tipo_reporte' onChange='if(this.value=="1") { $("valor").show(); $("centro_tr").show(); $("ubicacion").show(); } else if(this.value=="2") {$("valor").hide(); $("centro_tr").hide(); $("ubicacion").hide();} else { $("valor").hide(); $("centro_tr").hide(); $("ubicacion").show();}'>
                            <option value='0' SELECTED>Informe de Rotaci&oacute;n</option>
                            <option value='3'>Informe de Promedio de Stock (Balance Score Card)</option>
                            <option value='1'>Consumo por Servicio/Art&iacute;culo</option>
                            <option value='2'>Porcentaje de Uso del Arsenal Farmacol&oacute;gico</option>
                        </select>
                        <span id='valor' style='display:none;'><input type='checkbox' id='valorizado' name='valorizado' onChange='mostrar_valores();'/> Valorizado</span>
                    </td>
                </tr>
                <tr id='centro_tr' style='display:none;'>
                    <td style='text-align: right;'>Centro de Costo:</td>
                    <td>
                        <select id='centro_ruta' name='centro_ruta'>
                            <option value='' SELECTED>(Todos...)</option>
                                <?php 
                                $cc=cargar_registros_obj("SELECT * FROM centro_costo ORDER BY centro_nombre;", true);
                                for($i=0;$i<sizeof($cc);$i++)
                                {
                                    print("<option value='".$cc[$i]['centro_ruta']."'>".$cc[$i]['centro_nombre']."</option>");
				}
                                ?>
                        </select>
                    </td>
                </tr>
                <tr>
                <tr id='valor_tr' style='display:none;'>
                    <td style='text-align: right;'>Valorizaci&oacute;n Seg&uacute;n:</td>
                    <td>
                        <select name="valor_tipo" id="valor_tipo">
                            <option value="1" SELECTED>Valor minimo</option>
                            <option value="2">Valor medio</option>
                            <option value="3">Valor maximo</option>
                            <option value="4">Ultimo valor</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <center>
                            <input type='button' id='' name='' onClick='generar_reporte();' value='-- Generar Reporte... --' />
                            <input type='button' id='' name='' onClick='xls_reporte();' value='-- Descargar XLS Reporte... --' />
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <div class='sub-content2' id='listado_informe' style='height:300px;overflow:auto;'>
            
    </div>
    </div>
</center>
<script>
    generar_reporte=function()
    {
        if($('tipo_reporte').value==0)
        {
            var url_reporte='abastecimiento/informe_rotacion/listado_rotacion.php';
	}
        else if ($('tipo_reporte').value==1)
        {
            var url_reporte='abastecimiento/informe_rotacion/listado_salidas.php';
	}
        else if ($('tipo_reporte').value==2)
        {
            var url_reporte='abastecimiento/informe_rotacion/uso_arsenal.php';
	}
        else
        {
            var url_reporte='abastecimiento/informe_rotacion/listado_promedio.php';
	}
	reporte.action=url_reporte;
	var myAjax=new Ajax.Updater('listado_informe',
        url_reporte,
        {
            method: 'post',
            parameters: $('bod_id').serialize()+'&'+$('fecha1').serialize()+'&'+$('fecha2').serialize()+'&'+$('valorizado').serialize()+'&'+$('centro_ruta').serialize()+'&'+valor_tipo.serialize(),
            onComplete: function(r)
            {
            }
	});
    }
    xls_reporte=function()
    {
        var url_reporte='';
        //alert($('tipo_reporte').value);
        if($('tipo_reporte').value*1==0)
        {
            url_reporte='abastecimiento/informe_rotacion/listado_rotacion.php';
	}
        else if ($('tipo_reporte').value*1==1)
        {
            url_reporte='abastecimiento/informe_rotacion/listado_salidas.php';
	}
        else if ($('tipo_reporte').value*1==2)
        {
            url_reporte='abastecimiento/informe_rotacion/uso_arsenal.php';
	}
        else
        {
            url_reporte='abastecimiento/informe_rotacion/listado_promedio.php';
	}
        reporte.action=url_reporte;
        //alert(url_reporte);
        $('reporte').submit();
    }
    Calendar.setup({
    inputField     :    'fecha1',         // id of the input field
    ifFormat       :    '%d/%m/%Y',       // format of the input field
    showsTime      :    false,
    button          :   'fecha1_boton'
    });
    
    Calendar.setup({
    inputField     :    'fecha2',
    ifFormat       :    '%d/%m/%Y',
    showsTime      :    false,
    button          :   'fecha2_boton'
    });
</script>