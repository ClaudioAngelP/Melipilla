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
   
   actualizar_tipo=function()
   {
        $("print_report").hide();
        var tipo=$('tipo_reporte').value;
        if(tipo=="1")
        {
            $("valor").show();
            $("centro_tr").show();
            $("ubicacion").show();
            $("tr_hora").show();
            if($('bod_id').value=="35" || $('bod_id').value=="36")
            {
                $("recetas").show();
            }
            else
            {
             
                $("recetas").hide();
            }
            $("tr_paciente").hide();
            $("item_tr").hide();
            
        }
        else if(tipo=="2")
        {
            
            $("valor").hide();
            $("centro_tr").hide();
            $("ubicacion").hide();
            $("recetas").hide();
            $("tr_hora").hide();
            $("tr_paciente").hide();
            $("item_tr").hide();
           
        }
        else if(tipo=="8")
        {
            $("valor").hide();
            $("centro_tr").show();
            $("ubicacion").show();
            $("tr_hora").hide();
            $("recetas").hide();
            $("tr_hora").hide();
            $("tr_paciente").hide();
            $("item_tr").hide();
        }
        else if(tipo=="9")
        {
            $("pac_id").value="";
            $("pac_rut").value="";
            $("pac_nombre").value="";
            $("valor").hide();
            $("centro_tr").hide();
            $("ubicacion").show();
            $("recetas").hide();
            $("tr_hora").hide();
            $("tr_paciente").show();
            $("item_tr").hide();
        }
        else
        {
            $("valor").hide();
            $("ubicacion").show();
            $("recetas").hide();
            $("tr_hora").hide();
            $("tr_paciente").hide();
            if(tipo=="4"){
                $("item_tr").show();
            }
            else {
                $("item_tr").hide();
            }
        }
       
   }
   
   actualizar_centro=function()
   {
        var centro=$('centro_ruta').value;
        if(centro=='')
            $("print_report").hide();
        else
        {
            if($('tipo_reporte').value=="1")
                $("print_report").show();
            else
                $("print_report").hide();
        }
   }
   
   
    print_reporte=function()
    {
        //_general = $('tab_general_content').innerHTML;
        _general="<div>";
            _general+='<table style="font-size: 12px;">';
                _general+='<tr>';
                    _general+="<td style='text-align: right; width:150px;'>Ubicaci&oacute;n:</td>";
                    _general+="<td style='font-size: 12px;'><b>"+$j("#bod_id option:selected").html()+"</b></td>";
                _general+='</tr>';
                _general+='<tr>';
                    _general+="<td style='text-align: right; width:150px;'>Fecha de Incio:</td>";
                    _general+='<td><b>'+$('fecha1').value+'</b></td>';
                _general+='</tr>';
                _general+='<tr>';
                    _general+="<td style='text-align: right; width:150px;'>Fecha de Final:</td>";
                    _general+='<td><b>'+$('fecha2').value+'</b></td>';
                _general+='</tr>';
                _general+='<tr>';
                    _general+="<td style='text-align: right;'>Tipo de Infome:</td>";
                    _general+="<td><b>"+$j("#tipo_reporte option:selected").html()+"</b></td>";
                _general+='</tr>';
                _general+='<tr>';
                    _general+="<td style='text-align: right;'>Centro de Costo:</td>";
                    _general+="<td><b>"+$j("#centro_ruta option:selected").html()+"</b></td>";
                _general+='</tr>';
            _general+='</table>';
        _general+='</div>';
        _detalle = $('listado_informe').innerHTML;
        _separador2 = '<hr><h3>Detalle de Salidad</h3></hr>';
        imprimirHTML(_general+_separador2+_detalle);
    }
    
    
    validacion_hora2 = function (obj)
    {
        var obj=$(obj);
        if(trim(obj.value)=='')
        {
            obj.value='';
            obj.style.background='skyblue';
            return true;
	}
        else
            return validacion_hora(obj);
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
                        <select id='bod_id' name='bod_id' onChange='actualizar_tipo();'>
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
                <tr id="tr_hora" style="display:none;">
                    <td style='text-align:right;'>Hora:</td>
                    <td>
                        Desde <input type='text' id='hora1' name='hora1' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;' />
                        Hasta <input type='text' id='hora2' name='hora2' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;' />
                    </td>
                </tr>
                <tr>
                    <td style='text-align: right;'>Informe:</td>
                    <td>
                        <select id='tipo_reporte' name='tipo_reporte' onChange='actualizar_tipo();'>
                            <option value='0' SELECTED>Informe de Rotaci&oacute;n</option>
                            <option value='3'>Informe de Promedio de Stock (Balance Score Card)</option>
                            <option value='1'>Consumo por Servicio/Art&iacute;culo</option>
                            <option value='2'>Porcentaje de Uso del Arsenal Farmacol&oacute;gico</option>
                            <option value='4'>Consumo Mensual de Productos</option>
                            <option value='5'>Histograma Consumo de Productos</option>
                            <option value='8'>Perfil Farmacol&oacute;gico por Servicio</option>
                            <option value='9'>Perfil Farmacol&oacute;gico por Paciente</option>
                        </select>
                        <span id='valor' style='display:none;'><input type='checkbox' id='valorizado' name='valorizado' onChange='mostrar_valores();'/>Valorizado</span>
                        <span id='recetas' style='display:none;'><input type='checkbox' id='solo_receta' name='solo_receta' onChange=''/>Solo Recetas</span>
                    </td>
                </tr>
                <tr id='centro_tr' style='display:none;'>
                    <td style='text-align: right;'>Centro de Costo:</td>
                    <td>
                        <select id='centro_ruta' name='centro_ruta' onclick="actualizar_centro();">
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
                <tr id='item_tr' style='display:none;'>
                    <td style='text-align: right;'>Item Presupuestario:</td>
                    <td>
                        <select id='item_presupuestario' name='item_presupuestario' onclick="">
                            <option value='' SELECTED>(Todos los Item...)</option>
                                <?php 
                                $cc=cargar_registros_obj("SELECT * FROM item_presupuestario ORDER BY item_glosa;", true);
                                for($i=0;$i<sizeof($cc);$i++)
                                {
                                    print("<option value='".$cc[$i]['item_codigo']."'>".$cc[$i]['item_glosa']."</option>");
				}
                                ?>
                        </select>
                    </td>
                </tr>
                <tr id='tr_paciente' style='display:none;'>
                    <td style='text-align:right;'>Paciente:</td>
                    <td>
                        <input type='hidden' id='pac_id' name='pac_id' value='' />
                        <input type='text' size=20 id='pac_rut' name='pac_rut' value='' style='text-align:center;font-weight:bold;' DISABLED />
                        <input type='text' size=55 id='pac_nombre' name='pac_nombre' value='' onDblClick='$("pac_id").value="";$("pac_rut").value="";$("pac_nombre").value="";' />
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
                            <input type='button' id='print_report' name='print_report' onClick='print_reporte();' value='-- Imprimir Reporte... --' style="display: none"/>
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
var reporte="";
    generar_reporte=function()
    {
        if($('tipo_reporte').value==0)
        {
            var url_reporte='abastecimiento/informe_rotacion/listado_rotacion.php';
	}
        else if ($('tipo_reporte').value==1)
        {
            var url_reporte='abastecimiento/informe_rotacion/listado_salidas.php';
            if(!validacion_hora2($('hora1')))
            {
                alert('Hora m&iacute;nima incorrecta.'.unescapeHTML());
		$('hora1').select();
		$('hora1').focus();
		return;
            }

            if(!validacion_hora2($('hora2')))
            {
                alert('Hora m&aacute;xima incorrecta.'.unescapeHTML());
		$('hora2').select();
		$('hora2').focus();
		return;
            }
	}
        else if ($('tipo_reporte').value==2)
        {
            var url_reporte='abastecimiento/informe_rotacion/uso_arsenal.php';
	}
        else if($('tipo_reporte').value*1==3)
        {
            var url_reporte='abastecimiento/informe_rotacion/listado_promedio.php';
	}
        else if ($('tipo_reporte').value*1==4)
        {
            url_reporte='abastecimiento/informe_rotacion/listado_mensual.php';
        }
        else if ($('tipo_reporte').value*1==5)
        {
            url_reporte='abastecimiento/informe_rotacion/graficos_consumo.php';
        }
        else if ($('tipo_reporte').value*1==8)
        {
            url_reporte='abastecimiento/informe_rotacion/perfil_farma_por_servicio.php';
        }
        else if ($('tipo_reporte').value*1==9)
        {
            if($('pac_id').value=="")
            {
                alert("Debe Seleccionar Paciente");
                return;
            }
            url_reporte='abastecimiento/informe_rotacion/perfil_farma_por_paciente.php';
        }
	if(reporte)
		return;
            
	$j('#listado_informe').html('<center><table><tr><td><img src=imagenes/loading_small.gif></td></tr></table></center>');

	reporte=true;
        
	reporte.action=url_reporte;
        
	var myAjax=new Ajax.Updater('listado_informe',
        url_reporte,
        {
            method: 'post',
            parameters: $('bod_id').serialize()+'&'+$('fecha1').serialize()+'&'+$('fecha2').serialize()+'&'+$('valorizado').serialize()+'&'+$('centro_ruta').serialize()+'&'+valor_tipo.serialize()+'&'+$('solo_receta').serialize()+'&'+$('hora1').serialize()+'&'+$('hora2').serialize()+'&'+$('pac_id').serialize()+'&'+$('item_presupuestario').serialize(),
            async:false,
            onComplete: function(r)
            {
		reporte=false;
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
        else if($('tipo_reporte').value*1==3)
        {
            url_reporte='abastecimiento/informe_rotacion/listado_promedio.php';
	}
        else if ($('tipo_reporte').value*1==4)
        {
            url_reporte='abastecimiento/informe_rotacion/listado_mensual.php';
        }
        else if ($('tipo_reporte').value*1==5)
        {
            url_reporte='abastecimiento/informe_rotacion/graficos_consumo.php';
        }
        $('reporte').action=url_reporte;
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
    
    seleccionar_paciente = function(d)
    {
        $('pac_rut').value=d[0];
        $('pac_nombre').value=d[2];
        $('pac_id').value=d[4];
    }

    autocompletar_pacientes = new AutoComplete('pac_nombre','autocompletar_sql.php',
    function(){
    if($('pac_nombre').value.length<2) return false;
    return {method: 'get',
    parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_nombre').value)
    }
    }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);
    
    validacion_hora2($('hora1'));
    validacion_hora2($('hora2'));
</script>
