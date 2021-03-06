<?php
    require_once('../../conectar_db.php');
    error_reporting(E_ALL);
    $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
    $opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
    $opts_color=Array('black','gray','blue','purple','green','red');
    $options='';
    
    for($l=0;$l<sizeof($opts);$l++)
    {
        if($l==3 OR $l==4)
            continue;
        if($l==1)
            $sel='SELECTED';
        else
            $sel='';
	$options.='<option value="'.$l.'" '.$sel.'>'.$opts[$l].'</option>';
    }
?>
<script>
    actualizar_list = function()
    {
        var data = $('fecha1').value;
	var tipo = $('tipo_inf').value;
	var myAjax=new Ajax.Updater('listado_esp','prestaciones/archivo_fichas/esp_select.php',
        {
            method:'post',
            parameters:'data='+data+'&tipo='+tipo
	});
	if(tipo*1==1 || tipo*1==3)
            $('tr_estado').show();
	else
            $('tr_estado').hide();
    }

    listar_nominas=function()
    {
        $('listado_nominas').innerHTML='<br><br><br><img src="imagenes/ajax-loader3.gif"><br>Espere un momento...';
        var url='';
        
	if($('tipo_inf').value*1<4)
            url='prestaciones/archivo_fichas/listar_nominas.php';
	else
            url='prestaciones/archivo_fichas/buscar_ficha.php';
		
	var myAjax=new Ajax.Updater('listado_nominas',url,
        {
            method:'post',
            parameters:$('info_nominas').serialize()
	});
    }

    asignar_ficha = function(pac_id,llamada)
    {
        var ficha=prompt('Ingrese N&uacute;mero de Ficha para el paciente:\n\n(Deje en blanco para cancelar)'.unescapeHTML());
	if(ficha=='' || ficha==undefined)
        {
            alert('Acci&oacute;n cancelada.'.unescapeHTML());
            return;
	}
	
        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/sql_ficha.php',
        {
            method:'post',
            parameters:'pac_id='+pac_id+'&ficha='+encodeURIComponent(ficha)+'&llamada='+llamada,
            onComplete:function(r) {
                alert(r.responseText);
                listar_nominas();
            }
	});
    }

    crear_ficha = function(pac_id)
    {
        if(!confirm('&iquest;Est&aacute; seguro que desea crear nuevo numero de ficha para el paciente?.'.unescapeHTML()))
        {
            alert('Acci&oacute;n cancelada.'.unescapeHTML());
            return;
	}
        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/sql_ficha.php',
        {
            method:'post',
            parameters:'pac_id='+pac_id,
            onComplete:function(r)
            {
                alert(r.responseText);
                listar_nominas();
            }
        });
    }

    imprimir_reporte=function()
    {
        var fecha = new Date();
        var dd = fecha.getDate();
        var mm = fecha.getMonth()+1;
        var yyyy = fecha.getFullYear();
        if(dd<10){dd='0'+dd} 
        if(mm<10){mm='0'+mm}
	fecha = dd+'/'+mm+'/'+yyyy;
        _general = '<table width="100%"><tr><td style="text-align:left;"><hr><h5><?php echo htmlentities($sghservicio); ?></h5></hr></td>';
        _general += '<td style="text-align:right;">'+fecha+'</td></tr>';
        _general += '<tr><td style="text-align:left;"><h5><?php echo htmlentities($sghinstitucion); ?></h5></td></tr>';
        _general += '<tr><td style="text-align:left;"><hr><h4>Listado de Fichas Solicitadas para el d&iacute;a '+$('fecha1').value+'.</h4></hr></td></tr>';
        _general += '</table>';
        imprimirHTML(_general+$('listado_nominas').innerHTML);
    }

    imprimir_etiqueta=function(pac_id)
    {
        /*
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win =
        window.open('prestaciones/archivo_fichas/generar_pdf.php?pac_id='+pac_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();*/
        var myAjax=new Ajax.Request('conectores/zebra/imprimir_ficha.php', {method:'get',parameters:'pac_id='+pac_id});
    }

    historial_ficha=function(pac_id)
    {
        top=Math.round(screen.height/2)-300;
        left=Math.round(screen.width/2)-400;
        new_win =
        window.open('prestaciones/archivo_fichas/historial_ficha.php?pac_id='+pac_id,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=800, height=600, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
			
    pistolear_ficha = function (nomd_id,llamada)
    {
        var url='';
	if($('tipo_inf').value*1<4)
        {
            var str = $('barras').value;
            var n = str.indexOf("'"); 
            if(n!=-1)
            {
                var res = str.replace("'", "-");
                $('barras').value=res;
            }
            if(nomd_id==undefined)
            {
                if(!$j('#'+$j('#barras').val()+'_ficha').length)
                {
                    //alert("No se encuentra la Ficha ingresada en el listado de solicitudes");
                    //return;
                    if(($('tipo_inf').value*1)==3)
                    {
                        titulo="Lista De Fichas Para Env&iacute;o";
                        var win = new Window("form_listfichas",
                        {
                            className: "alphacube", top:40, left:0, width: 550, height: 580, 
                            title: '<img src="iconos/page_white_link.png"> '+titulo+'',
                            minWidth: 500, minHeight: 400,
                            maximizable: false, minimizable: false,
                            wiredDrag: true, draggable: true,
                            closable: true, resizable: false 
                        });
                        win.setDestroyOnClose();
                        win.setAjaxContent('prestaciones/archivo_fichas/list_fichas.php', 
                        {
                            method: 'post',
                            parameters: 'ficha='+$('barras').value,
                            evalScripts: true
                        });
                        $("form_listfichas").win_obj=win;
                        win.showCenter();
                        win.show(true);
                        return;
                        /*
                        var ficha=prompt('LA FICHA INGRESADA NO SE ENCUENTRA EN LISTADO DE SOLICITUDES:\n\nINGRESE FICHA NUEVAMENTE PARA CREAR LISTA DE ENVIO\n\n(Deje en blanco para cancelar)'.unescapeHTML());
                        if(ficha=='' || ficha==undefined)
                        {
                            alert('Acci&oacute;n cancelada.'.unescapeHTML());
                            return;
                        }
                        /*
                        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/sql_ficha.php',
                        {
                            method:'post',
                            parameters:'pac_id='+pac_id+'&ficha='+encodeURIComponent(ficha)+'&llamada='+llamada,
                            onComplete:function(r) {
                                alert(r.responseText);
                                listar_nominas();
                            }
                        });
                        */
                        //return;
                    }
                    else
                    {
                        alert("No se encuentra la Ficha ingresada en el listado de solicitudes");
                        return;
                    }
                   
                    
                    
                    
                
                }
                if($j('#'+$j('#barras').val()+'_ficha').val()==''  || $j('#'+$j('#barras').val()+'_ficha').val()=='0')
                {
                    alert("No se puede mover el documtento ya no se ha asigando numero de ficha");
                    return;
                }
            }
            url='prestaciones/archivo_fichas/sql_mover_ficha.php';
            var params='';
            if(nomd_id==undefined)
            {
                params='';
            }
            else
            {
                params='&nomd_id='+encodeURIComponent(nomd_id)+'&llamada='+encodeURIComponent(llamada);
            }
            var myAjax=new Ajax.Request(url,
            {
                method:'post',
		parameters:$('barras').serialize()+'&'+$('info_nominas').serialize()+params,
		onComplete:function(r)
                {
                    try {
                        var datos=r.responseText.evalJSON(true);
			var limpiar=false;
                        if(!datos)
                        {
                            $('barras').style.background='yellow';
                            $('texto_barras').style.background='yellow';
                            $('texto_barras').style.color='black';
                            $('texto_barras').value='PACIENTE NO ENCONTRADO.';
			}
                        else
                        {
                            $('barras').style.background='yellowgreen';						
                            $('texto_barras').style.background='black';
                            $('texto_barras').style.color='white';
                            $('texto_barras').value=(datos[0][0].pac_rut+' - '+datos[0][0].pac_nombres+' '+datos[0][0].pac_appat+' '+datos[0][0].pac_apmat).unescapeHTML();
                            limpiar=true;
			}
			if(datos[1])
                        {
                            listar_nominas();
			}
                        else
                        {
                            var html='<center><h2><b><u>ATENCI&Oacute;N:</u></b><br/><br/>Paciente tiene '+datos[2].length+' solicitudes para esta misma fecha ('+$('fecha1').value+'), debe seleccionar destino de la ficha:</h2><br/><br/>';
                            html+='<table style="width:80%;font-size:18px;"><tr class="tabla_header"><td>Fecha Solicitud</td><td>Especialidad/Servicio</td><td>Profesional/Funcionario</td><td>Hora</td><td>Motivo</td><td>Enviar</td></tr>'
                            var d=datos[2];
                            for(var i=0;i<d.length;i++)
                            {
                                html+='<tr class="'+(i%2==0?'tabla_fila':'tabla_fila2')+'" ';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+(i%2==0?'tabla_fila':'tabla_fila2')+'\';" >';
				html+='<td style="text-align:center;">'+d[i].fecha_sol.substr(0,16)+'</td>';
                                if(d[i].esp_desc!="")
                                {
                                    html+='<td style="font-weight:bold;font-size:14px;">'+d[i].esp_desc+'</td>';
                                }
                                else
                                {
                                    html+='<td style="font-size:14px;font-style: italic;">('+d[i].esp+')</td>';
                                }
				html+='<td style="font-size:14px;">'+d[i].doc_nombre+'</td>';
				html+='<td style="text-align:center;font-size:20px;">'+(d[i].nomd_hora!=undefined?d[i].nomd_hora.substr(0,5):'')+'</td>';
				html+='<td style="font-weight:bold;font-size:12px;">'+d[i].amp_nombre+'</td>';
				html+='<td><center><img src="iconos/arrow_right.png" style="cursor:pointer;width:32px;height:32px;" onClick="pistolear_ficha('+d[i].nomd_id+','+d[i].programada+');" /></center></td>';
				html+='</tr>'
                            }
                            html+='</table></center>';
                            $('listado_nominas').innerHTML=html;
                            return;
			}
                        if(limpiar)
                            $('barras').value='';
			
                        $('barras').select();
			$('barras').focus();
                    }
                    catch(err)
                    {
                        alert(err);
                    }
                }
            });
	}
        else
        {
            listar_nominas();
        }
    }

    enviar_nominas=function()
    {
        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/sql_enviar_retiradas.php',
        {
            method:'post',parameters:$('info_nominas').serialize(),
            onComplete:function(r)
            {
                listar_nominas();
            }
	});
    }

    imprimir_especialidad=function(esp_id,agrupar,doc_id,fecha)
    {
        if(esp_id==-1)
	{
		esp_id=$j('#esp_id').val();
		agrupar=$j('#agrupar').val();
		doc_id=$j('#doc_id').val();
		fecha=$j('#fecha1').val();
		proceso=1;
	}
	else
	{
		proceso=0;
	}
        top=Math.round(screen.height/2)-250;
        left=Math.round(screen.width/2)-340;
        new_win = 
        window.open('prestaciones/archivo_fichas/imprimir_fichas.php?esp_id='+esp_id+'&fecha='+fecha+'&agrupar='+agrupar+'&doc_id='+doc_id+'&tipo_inf=1&proceso='+proceso,
        'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
        'top='+top+', left='+left);
        new_win.focus();
    }
</script>
<center>
    <div class='sub-content' style='width:95%;'>
        <form id='info_nominas' onSubmit='return false;'>
            <div class='sub-content'>
                <table style='width:100%;'>
                    <tr>
                        <td style='width:30px;'><img src='iconos/table_edit.png'></td>
                        <td style='font-size:14px;'>
                            <select id='tipo_inf' name='tipo_inf' onChange='actualizar_list();' style='font-size:18px;'>
                                <option value=1>Salida de Fichas Programadas</option>
                                <option value=3>Salida de Fichas en Pr&eacute;stamo</option>
                                <!--<option value=5>Salida de Fichas Espontaneas</option>-->
                                <option value=2>Recepci&oacute;n de Fichas</option>
                                <option value=4>B&uacute;squeda de Fichas</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <div class='sub-content' id='buscar_nominas'>
                <center>
                    <table style='width:100%;'>
                        <tr>
                            <td style='text-align:right;'>Fecha:</td>
                            <td>
                                <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y")?>' onChange='actualizar_list();'>
                                <img src='iconos/date_magnify.png' id='fecha1_boton'>
                            </td>
                            <td style='text-align:right;'>Especialidad:</td>
                            <td id='select_especialidades'>
                                <div id='listado_esp' name='listado_esp' ></div>
                            </td>
                        </tr>
                        <tr id='tr_fecha' name='tr_fecha' style='display:none;'>
                            <td style='text-align:right;'>Fecha Fin:</td>
                            <td>
                                <input type='text' name='fecha2' id='fecha2' size=10 style='text-align: center;' value='<?php echo date("d/m/Y")?>' onChange='actualizar_list();'>
                                <img src='iconos/date_magnify.png' id='fecha1_boton'>
                            </td>
                            <td>Total:&nbsp;<span id='total' name='total'></span></td>
                        </tr>
			<tr>
                            <td style='text-align:right;'>Profesional:</td>
                            <td colspan=3>
                                <input type='text' id='nombre_medico' name='nombre_medico' size=25 onDblClick='this.value=""; $("doc_nombre").innerHTML="(Todos los Profesionales)"; $("doc_id").value=-1;'>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <span id='doc_nombre' name='doc_nombre'>(Todos los Profesionales...)</span>
                                <input type='hidden' id='doc_id' name='doc_id' value='-1'>
                            </td>
                        </tr>
                        <tr id='tr_estado' >
                            <td style='text-align:right;'>Estado:</td>
                            <td colspan=5>
                                <select id='estado_ficha' name='estado_ficha' style='font-size:20px;'>
                                    <?php echo $options; ?>
                                </select>
                            </td>
                        </tr>
                        <tr id='' >
                            <td style='text-align:right;'>Agrupar:</td>
                            <td colspan=5>
                                    <select id='agrupar' name='agrupar' style=''>
                                        <option value='0'>Especialidad y Profesional</option>
                                        <option value='1'>Especialidad</option>
                                    </select>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:right;'><img src='abastecimiento/hoja_cargo/barras.png' /></td>
                            <td colspan=5>                         
                                <input type='text' id='barras' name='barras' size=25 style='font-size:16px;text-align:center;' onKeyUp='if(event.which==13) pistolear_ficha();' onFocus='this.style.border="3px dashed red";this.select();' onBlur='this.style.border="";this.style.background="";' />
                                <input type='text' id='texto_barras' name='texto_barras' size=60 READONLY DISABLED style='font-size:16px;text-align:left;border:none;' value='&lt;&lt; Seleccione Fichas con C&oacute;digo de Barras' />
                            </td>
                        </tr>
                        <tr>
                            <td colspan=4>
                                <center>
                                    <input type='button' id='actualiza' name='actualiza' value='-- Actualizar Listado... --' onClick='listar_nominas();'/>
                                    <input type='button' id='enviar_retiradas' name='enviar_retiradas' value='-- Enviar Fichas Retiradas... --' onClick='enviar_nominas();' />
                                </center>
                            </td>
                        </tr>
                    </table>
                </center>
            </div>
            <div class='sub-content2' style='height:360px;overflow:auto;'id='listado_nominas'>
            </div>
            <center>
                <table>
                    <tr>
                        <td>
                            <!--
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/pencil.png'>
                                        </td>
                                        <td>
                                            <a href='#' onClick='guardar_estado();'> Guardar Listado...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            -->
                            <div class='boton'>
                                <table>
                                    <tr>
                                        <td>
                                            <img src='iconos/printer.png'>
                                        </td>
                                        <td>
                                            <!--<a href='#' onClick='imprimir_reporte();'> Imprimir Listado...</a>-->
                                            <a href='#' onClick='imprimir_especialidad(-1,0,0,0);'> Imprimir Listado...</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </center>
        </form>
    </div>
</center>
<script>

    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'

    });
    
    ingreso_rut=function(datos_medico) {
		
	  $('doc_id').value=datos_medico[3];	
      $('nombre_medico').value=datos_medico[1];
      $('doc_nombre').innerHTML=datos_medico[0];
      
    }
      
      autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<2) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_rut);

    actualizar_list();
</script>
