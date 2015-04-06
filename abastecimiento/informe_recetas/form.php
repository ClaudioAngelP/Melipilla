<?php
    require_once("../../conectar_db.php");
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(4),')', 'ORDER BY bod_glosa');
    $itemshtml = desplegar_opciones("item_presupuestario", "item_codigo, item_glosa",'','true','ORDER BY item_codigo'); 
?>
<script>
    generar_reporte=function()
    {
		
		if ($('tipo').value==2)
        {
            
            if(!validacion_hora2($('hora1'))){
				alert('Hora m&iacute;nima incorrecta.'.unescapeHTML());
				$('hora1').select();
				$('hora1').focus();
				return;
			}

			if(!validacion_hora2($('hora2'))){
				alert('Hora m&aacute;xima incorrecta.'.unescapeHTML());
				$('hora2').select();
				$('hora2').focus();
				return;
			}
		}
	
        $('reporte').innerHTML='<br><br><img src="imagenes/ajax-loader3.gif" /><br>Cargando...';
        var myAjax=new Ajax.Updater('reporte', 'abastecimiento/informe_recetas/listar_totales.php',
        {
            method: 'post',
            parameters: 'tipo='+$('tipo').value+'&'+$('fecha1').serialize()+'&'+$('fecha2').serialize()+'&'+$('fecha3').serialize()+'&'+$('hora1').serialize()+'&'+$('hora2').serialize()+'&'+$('bodega').serialize()+'&'+$('prod_item').serialize()+'&'+$('func_id').serialize()
        });
    }

    generar_reporte_detalle=function(opcion)
    {
        if((opcion*1)==1){
            var myAjax=new Ajax.Updater('reporte', 'abastecimiento/informe_recetas/listar_detalle.php',
            {
                method: 'post',
                parameters: $('dreporte').serialize()+'&opcion=1'
            });
        }
        else {
            $('dreporte').action='abastecimiento/informe_recetas/listar_detalle.php';
            $('dreporte').submit();
        }
    }


    generar_reporte_xls=function()
    {
        $('dreporte').action='abastecimiento/informe_recetas/listar_totales.php';
	$('dreporte').submit();
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
    
    limpiar_funcionario=function()
    {
        $('func_id').value='';
        $('func_rut').value='';
        $('func_nombre').innerHTML="";
        generar_reporte();
    }
    
    actualizar_tipo=function(){
        if(($('tipo').value*1)==1){ 
            $("tr_fecha1").style.display=""; 
            $("tr_fecha2").style.display=""; 
            $("tr_item").style.display=""; 
            $("tr_fecha").style.display="none"; 
            $("tr_hora").style.display="none"; 
            $("tr_func").style.display="none"; 
            $("print_report").hide();
            $("ver_detalle").hide();
        } else { 
            $("tr_fecha1").style.display="none"; 
            $("tr_fecha2").style.display="none"; 
            $("tr_item").style.display="none"; 
            $("tr_fecha").style.display=""; 
            $("tr_hora").style.display=""; 
            $("tr_func").style.display=""; 
            $("print_report").show();
            $("ver_detalle").show();
        }
    }
    
    
    print_reporte=function()
    {
        //_general = $('tab_general_content').innerHTML;
        var _general='';
        
        _general="<div>";
            _general+='<table style="font-size: 12px;">';
                _general+='<tr>';
                    _general+="<td style='text-align: right; width:150px;'>Tipo de Informe:</td>";
                    _general+="<td style='font-size: 12px;'><b>"+document.getElementById('tipo').options[document.getElementById('tipo').selectedIndex].text+"</b></td>";
                _general+='</tr>';
                _general+='<tr>';
                    _general+="<td style='text-align: right; width:150px;'>Ubicaci&oacute;n:</td>";
                    _general+='<td><b>'+document.getElementById('bodega').options[document.getElementById('bodega').selectedIndex].text+'</b></td>';
                _general+='</tr>';
                _general+='<tr>';
                    _general+="<td style='text-align: right; width:150px;'>Fecha Reporte:</td>";
                    _general+='<td><b>'+$('fecha3').value+'</b></td>';
                _general+='</tr>';
                if(($('hora1').value!='')){
                    _general+='<tr>';
                    _general+="<td style='text-align: right; width:150px;'>Hora Inicio:</td>";
                    _general+='<td><b>'+$('hora1').value+'</b></td>';
                    _general+='</tr>';
                }
                if(($('hora2').value!='')){
                    _general+='<tr>';
                        _general+="<td style='text-align: right; width:150px;'>Hora Final:</td>";
                        _general+='<td><b>'+$('hora2').value+'</b></td>';
                    _general+='</tr>';
                }
                
                
                
                
                if(($('func_id').value!='')){
                    _general+='<tr>';
                        _general+="<td style='text-align: right; width:150px;'>Funcionario:</td>";
                        _general+='<td><b>'+$('func_nombre').innerHTML+'</b></td>';
                    _general+='</tr>';
                    
                }
                
                /*
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
                */
            _general+='</table>';
        _general+='</div>';
        
        var _detalle = $('reporte').innerHTML;
        var _separador2 = '<hr><h3>Detalle de Salidas</h3></hr>';
        imprimirHTML(_general+_separador2+_detalle);
    }
    
</script>
<center>
    <div style='width:900px;' class='sub-content'>
        <div class='sub-content'>
            <img src='iconos/script.png' />
            <b>Total de Recetas y Prescripciones</b>
        </div>
        <form id='dreporte' name='dreporte' onSubmit='return false;' method='post'>
            <input type='hidden' id='xls' name='xls' value='1' />
            <table style='width:100%;'>
                <tr>
                    <td style='width:150px;text-align:right;'>Informe:</td>
                    <td>
                        <select id='tipo' name='tipo' onChange='actualizar_tipo();'>
                            <option value=1>Total de Recetas y Prescripciones</option>
                            <option value=2>Controlados por Funcionario</option>
                        </select>
                    </td>
                </tr>
                <tr id='tr_fecha1'>
                    <td style='width:150px;text-align:right;'>Fecha Inicio:</td>
                    <td>
                        <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y", mktime(0,0,0,date('m')*1-1,date('d')*1,date('Y')*1)); ?>'>
                        <img src='iconos/date_magnify.png' id='fecha1_boton'>
                    </td>
                </tr>
                <tr id='tr_fecha2'>
                    <td style='width:100px;text-align:right;'>Fecha Final:</td>
                    <td>
                        <input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'>
                        <img src='iconos/date_magnify.png' id='fecha2_boton'>
                    </td>
                </tr>
                <tr id='tr_fecha' style='display:none;'>
                    <td style='width:100px;text-align:right;'>Fecha:</td>
                    <td>
                        <input type='text' name='fecha3' id='fecha3' size=10 style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'>
                        <img src='iconos/date_magnify.png' id='fecha3_boton'>
                    </td>
                </tr>
                <tr id="tr_hora" style="display:none;">
                    <td style='text-align:right;'>Hora:</td>
                    <td>
                        Desde <input type='text' id='hora1' name='hora1' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;' />
                        Hasta <input type='text' id='hora2' name='hora2' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;' />
                    </td>
                </tr>
                <tr id='tr_func' style='display:none;'>
                    <td style='text-align:right;'>Funcionario:</td>
                    <td>
                        <input type='hidden' id='func_id' name='func_id'>
                        <input type='text' id='func_rut' name='func_rut' onDblClick='limpiar_funcionario();'>&nbsp;
                        <span id='func_nombre' name='func_nombre'></span>
                    </td>
                </tr>
                <tr id='tr_ubicacion'>
                    <td style='text-align: right;'>Ubicaci&oacute;n:</td>
                    <td>
                        <select name='bodega' id='bodega'>
                            <?php echo $bodegashtml; ?>
                        </select>
                    </td>
                </tr>
                <tr id='tr_item' >
                    <td style='text-align: right;'>Item Presupuestario:</td>
                    <td>
                        <select id='prod_item' name='prod_item' style='width:200px;' onChange=''>
                            <option value=0 selected>Todos los Item Presupuestario</option>
                                <?php echo $itemshtml; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <center>
                            <input type='button' id='' name='' value='-- Generar Reporte --' onClick='generar_reporte();' />
                            <input type='button' id='ver_detalle' name='ver_detalle' value='-- Ver Detalle Recetas --' onClick='generar_reporte_detalle(1);' style="display: none"/>
                            <input type='button' id='' name='' value='-- Descargar Detalle --' onClick='generar_reporte_detalle();' />
                            <input type='button' id='' name='' value='-- Generar XLS --' onClick='generar_reporte_xls();' />
                            <input type='button' id='print_report' name='print_report' onClick='print_reporte();' value='-- Imprimir Reporte... --' style="display: none"/>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
        <div class='sub-content2' style='height:300px;overflow:auto;' id='reporte'>
        </div>
    </div>
</center>
<script>
  
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
    
    Calendar.setup({
        inputField     :    'fecha3',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha3_boton'
    });
    
    seleccionar_funcionario = function(datos){
        $('func_id').value=datos[3];
        $('func_rut').value=datos[1];
        $('func_nombre').innerHTML=datos[2].unescapeHTML();
    }
	
    autocompletar_funcionario = new AutoComplete(
    'func_rut', 
    'autocompletar_sql.php',
    function() {
      if($('func_rut').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=funcionarios&nomfuncio='+encodeURIComponent($('func_rut').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, seleccionar_funcionario);
    
    
    validacion_hora2($('hora1'));
    validacion_hora2($('hora2'));
    
</script>
