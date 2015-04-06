<?php
    require_once("../../../conectar_db.php");
    $unidad_origen=pg_escape_string($_POST['select_origen']);
    if(strstr($unidad_origen,'.'))
    {
        $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto and centro_ruta!='".$unidad_origen."' ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
        $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
    }
    else
    {
        $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
        $espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades where esp_id!=$unidad_origen ORDER BY esp_desc", NULL, '', '');
    }
    
    /*
    if(isset($_POST['recepcionar']))
    {
        if(($_POST['recepcionar']*1)==1)
        {
           $recepcionar=true;
        }
        else
        {
            $recepcionar=false;
        }
    }
    else
    {
        $recepcionar=false;
    }
    */
    
    //$espechtml=desplegar_opciones_sql("SELECT esp_id, esp_desc FROM especialidades ORDER BY esp_desc", NULL, '', '');
    //$servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
?>
<script type="text/javascript" >
    var lista_envio=new Array();
    
    tipo_destino = function()
    {
        if($('select_tipo_destino').value=="-1")
        {
            $('destino_masivo_esp').value = -1;
            $('destino_masivo_serv').value = -1;
            $('amp_id').value = -1;
            $('destino_masivo_esp').disabled=true;
            $('destino_masivo_serv').disabled=true;
            $('destino_masivo_esp').style.display='block';
            $('destino_masivo_serv').style.display='none';
            $('amp_id').disabled=true;
            $('doc_rut').disabled=true;
            limpiar_profesional();
        }
        if($('select_tipo_destino').value=="1")
        {
            $('destino_masivo_serv').value = -1;
            $('destino_masivo_esp').disabled=false;
            $('destino_masivo_serv').disabled=true;
            $('destino_masivo_esp').style.display='block';
            $('destino_masivo_serv').style.display='none';
            $('amp_id').disabled=false;
            $('doc_rut').disabled=false;
            limpiar_profesional();
        }
        if($('select_tipo_destino').value=="2")
        {
            $('destino_masivo_esp').value = -1;
            $('destino_masivo_esp').disabled=true;
            $('destino_masivo_serv').disabled=false;
            $('destino_masivo_esp').style.display='none';
            $('destino_masivo_serv').style.display='block';
            $('amp_id').disabled=false;
            $('doc_rut').disabled=true;
            limpiar_profesional();
            
            
        }
        
        //limpiar_paciente();
    }
    
    pistolear_ficha_list=function()
    {
        if($('barras_list').value=='')
        {
            $('barras_list').value='';
            $('barras_list').select();
            $('barras_list').focus();
            return;
        }
        var str = $('barras_list').value;
        var n = str.indexOf("'"); 
        if(n!=-1)
        {
            var res = str.replace("'", "-");
            $('barras_list').value=res.toUpperCase();
        }
        else
        {
            $('barras_list').value=$('barras_list').value.toUpperCase();
        }
        url='prestaciones/archivo_fichas/buzon_fichas/buscar_fichas.php';
        var myAjax=new Ajax.Request(url,
        {
            method:'post',
            parameters:'barras='+$('barras_list').value+'&list_ficha=1&'+$('origen').serialize(),
            onComplete:function(r)
            {
                try
                {
                    var datos=r.responseText.evalJSON(true);
                    if(datos[0]==false)
                    {
                        if(datos[1]==true)
                        {
                            $('barras_list').style.background='yellow';
                            $('barras_list').value='';
                            $('barras_list').select();
                            $('barras_list').focus();
                            alert('No se ha puede realizar movimientos ya que este PACIENTE SE ENCUENTRA DUPLICADO.-'.unescapeHTML());
                            return;
                        }
                        else
                        {
                            alert('Error Ficha no encontrada en su unidad');
                            $('barras_list').style.background='yellow';
                            $('barras_list').value='';
                            $('barras_list').select();
                            $('barras_list').focus();
                            return;
                        }
                    }
                    else
                    {
                        if(datos[1]==true)
                        {
                            $('barras_list').style.background='yellow';
                            $('barras_list').value='';
                            $('barras_list').select();
                            $('barras_list').focus();
                            alert('No se ha puede realizar movimientos ya que este PACIENTE SE ENCUENTRA DUPLICADO.-'.unescapeHTML());
                            return;
                        }
                        else
                        {
                            var encontrado=false
                            for(var i=0;i<lista_envio.length;i++)
                            {
                                if(lista_envio[i]['pac_rut']==$('barras_list').value)
                                {
                                    encontrado=true;
                                    break;
                                }
                                if(lista_envio[i]['pac_ficha']==$('barras_list').value)
                                {
                                    encontrado=true;
                                    break;
                                }
                            }
                            if(encontrado==false)
                            {
                                //var opts = new Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
                                /*
                                if($('txt_recepcion').value=='1')
                                {
                                    if((datos[0]['am_estado']*1)!=2 && (datos[0]['am_estado']*1)!=4 && (datos[0]['am_estado']*1)!=3)
                                    {
                                        alert('Ficha No ha sido enviada, la ubicaci&oacute;n actual es ARCHIVO.-\nEstado Actual: '+opts[(datos[0]['am_estado']*1)]+''.unescapeHTML());
                                        $('barras_list').style.background='yellow';
                                        $('barras_list').value='';
                                        $('barras_list').select();
                                        $('barras_list').focus();
                                        return;
                                    }
                                    if((datos[0]['am_estado']*1)!=4)
                                    {
                                        if(datos[0]['ubic_actual']=='ARCHIVO')
                                        {
                                            alert('Ficha ya ha sido enviada recepcionada en su unidad de ARCHIVO.-\nEstado Actual: '+opts[(datos[0]['am_estado']*1)]+''.unescapeHTML());
                                            $('barras_list').style.background='yellow';
                                            $('barras_list').value='';
                                            $('barras_list').select();
                                            $('barras_list').focus();
                                            return;
                                        }
                                    }
                                }
                                */
                                if((datos[0][0]['am_estado']*1)==2)
                                {
                                    if(!confirm('La Ficha No ha Sido Recepcionada por su unidad Desea enviarla de todas formas?'.unescapeHTML()))
                                    {
                                        $('barras_list').style.background='yellowgreen';
                                        $('barras_list').value='';
                                        $('barras_list').select();
                                        $('barras_list').focus();
                                        return;
                                    }
                                }
                                num=lista_envio.length;
                                lista_envio[num]=new Object();
                                lista_envio[num].pac_rut=datos[0][0]['pac_rut'];
                                lista_envio[num].pac_ficha=datos[0][0]['pac_ficha'];
                                lista_envio[num].pac_nombre=datos[0][0]['pac_nombre'];
                                lista_envio[num].pac_id=datos[0][0]['pac_id'];
                                lista_envio[num].am_estado=datos[0][0]['am_estado'];
                                dibujar_lista();
                                $('barras_list').style.background='yellowgreen';
                                $('barras_list').value='';
                                $('barras_list').select();
                                $('barras_list').focus();
                            }
                            else
                            {
                                alert("Ficha ya ha sido ingresada a la lista");
                                $('barras_list').style.background='yellow';
                                $('barras_list').value='';
                                $('barras_list').select();
                                $('barras_list').focus();
                                return;
                            }
                        }
                    }
                }
                catch(err)
                {
                    alert(err);
                }
            }
        });
        
        
        
    }
    
    /*
    carga_inicial=function()
    {
        if($('ficha_inicial').value!='')
        {
            url='prestaciones/archivo_fichas/buscar_ficha.php';
            var myAjax=new Ajax.Request(url,
            {
                method:'post',
		parameters:'barras='+$('ficha_inicial').value+'&list_ficha=1',
		onComplete:function(r)
                {
                    try
                    {
                        var datos=r.responseText.evalJSON(true);
                        if(!datos)
                        {
                            alert("Error al buscar Ficha del Paciente");
                            return;
                        }
                        else
                        {
                            var opts = new Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
                            
                            if($('txt_recepcion').value=='1')
                            {
                                if((datos[0]['am_estado']*1)!=2 && (datos[0]['am_estado']*1)!=4 && (datos[0]['am_estado']*1)!=3)
                                {
                                    alert('Ficha No ha sido enviada, la ubicaci&oacute;n actual es ARCHIVO.-\nEstado Actual: '+opts[(datos[0]['am_estado']*1)]+''.unescapeHTML());
                                    $('barras_list').style.background='yellow';
                                    $('barras_list').value='';
                                    $('barras_list').select();
                                    $('barras_list').focus();
                                    return;
                                }
                                if((datos[0]['am_estado']*1)!=4)
                                {
                                    if(datos[0]['ubic_actual']=='ARCHIVO')
                                    {
                                        alert('Ficha ya ha sido recepcionada en su unidad de ARCHIVO.-\nEstado Actual: '+opts[(datos[0]['am_estado']*1)]+''.unescapeHTML());
                                        $('barras_list').style.background='yellow';
                                        $('barras_list').value='';
                                        $('barras_list').select();
                                        $('barras_list').focus();
                                        return;
                                    }
                                }
                            }
                            num=lista_envio.length;
                            lista_envio[num]=new Object();
                            lista_envio[num].pac_rut=datos[0]['pac_rut'];
                            lista_envio[num].pac_ficha=datos[0]['pac_ficha'];
                            lista_envio[num].pac_nombre=datos[0]['pac_nombre'];
                            lista_envio[num].pac_id=datos[0]['pac_id'];
                            lista_envio[num].am_estado=datos[0]['am_estado'];
                            dibujar_lista();
                            $('barras_list').style.background='yellowgreen';
                            $('barras_list').value='';
                            $('barras_list').select();
                            $('barras_list').focus();
                        }
                        
                    }
                    catch(err)
                    {
                        alert(err);
                    }
                    
                }
            });
        }
    }
    */
    dibujar_lista=function()
    {
        if(lista_envio.length==0)
        {
            $('list_masivo').innerHTML='';
        }
        else
        {
            var html='';
            html+='<table style="width:100%" border="0">';
                html+='<tr class="tabla_header">';
                    html+='<td style="text-align:center;">Rut</td>';
                    html+='<td style="text-align:center;">Ficha</td>';
                    html+='<td style="text-align:center;">Nombre</td>';
                    html+='<td style="text-align:center;">Estado</td>';
                    html+='<td style="text-align:center;width:40px;">&nbsp;</td>';
                html+='</tr>';
                var opts = new Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
                var opts_color = new Array('black','gray','blue','purple','green','red');
                for(var i=0;i<lista_envio.length;i++)
                {
                    if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
                    html+='<tr class="'+clase+'" style="color:'+opts_color[(lista_envio[i].am_estado*1)]+'">';
                        html+='<td style="text-align:center;font-weight:bold;">'+lista_envio[i].pac_rut+'</td>';
                        if(lista_envio[i].pac_ficha!='')
                        {
                            html+='<td style="text-align:center;font-weight:bold;">'+lista_envio[i].pac_ficha+'</td>';
                        }
                        else
                        {
                            <?php
                                if(_cax(20002))
                                {
                            ?>
                                    //html+='<td style="text-align:center;font-weight:bold;"><center><input type="button" style="font-size:8px;margin:0px;padding:0px;" id="asigna_'+lista_envio[i].pac_id+'" name="asigna_'+lista_envio[i].pac_id+'" onClick="asignar_ficha_list('+lista_envio[i].pac_id+',0,'+i+');" value="[ASIGNAR]" />';
                            <?php
                                }
                            ?>
                            //html+='<input type="button" style="font-size:8px;margin:0px;padding:0px;" id="crea_'+lista_envio[i].pac_id+'" name="crea_'+lista_envio[i].pac_id+'" onClick="crear_ficha('+lista_envio[i].pac_id+')" value="[CREAR]" /></center></td>';
                        }
                        html+='<td style="text-align:left;">'+lista_envio[i].pac_nombre+'</td>';
                        html+='<td style="text-align:left;">'+opts[(lista_envio[i].am_estado*1)]+'</td>';
                        html+='<td><center><img src=\'/produccion/iconos/delete.png\' style=\'cursor:pointer;\' onClick="quitar_ficha('+i+');" /></center></td>';
                    html+='</tr>';
                }
            html+='</table>';
            $('list_masivo').innerHTML=html;
        }
    }
    
    
    quitar_ficha=function(id)
    {
        lista_envio=lista_envio.without(lista_envio[id]);
        dibujar_lista();
    }
    
    limpiar_list=function()
    {
        lista_envio = new Array();
        dibujar_lista();
    }
    
    
    enviar_masivo=function()
    {
        if(lista_envio.length==0)
        {
            alert('No hay fichas seleccionadas para realizar envi&oacute;'.unescapeHTML());
            return;
        }
        if(($('select_tipo_destino').value*1)===-1)
        {
            alert('Debe seleccionar tipo de destino'.unescapeHTML());
            return;
        }
        else
        {
            if(($('select_tipo_destino').value*1)===1)
            {
                if(($('destino_masivo_esp').value*1)===-1)
                {
                    alert('Debe seleccionar Especialidad de envi&oacute;'.unescapeHTML());
                    return;
                }
                
                if(($('doc_id').value*1)===0)
                {
                    alert('Debe seleccionar Profesional de envi&oacute;'.unescapeHTML());
                    return;
                }
            }
            else
            {
                if(($('destino_masivo_serv').value*1)===-1)
                {
                    alert('Debe seleccionar servicio de envi&oacute;'.unescapeHTML());
                    return;
                }
            }
        }
        if(($('amp_id').value*1)===-1)
        {
            alert('Debe seleccionar motivo de solicitud'.unescapeHTML());
            return;
        }
        var sin_ficha=false;
        var paciente='';
        for(var i=0;i<lista_envio.length;i++)
        {
            if(lista_envio[i]['pac_ficha']=='' || lista_envio[i]['pac_ficha']=='0' || lista_envio[i]['pac_ficha']==0 || lista_envio[i]['pac_ficha']==null)
            {
                sin_ficha=true;
                paciente=lista_envio[i]['pac_rut'];
                break;
            }
        }
        if(sin_ficha==true)
        {
            alert("No se puede mover ficha ya que el paciente "+paciente+" no tiene asisgnado un numero de ficha");
            return;
        }
        
        url='prestaciones/archivo_fichas/buzon_fichas/sql_mover_ficha.php';
        var myAjax=new Ajax.Request(url,
        {
            method:'post',
            parameters:'&llamada=1&fichas='+encodeURIComponent(lista_envio.toJSON())+'&destino_esp='+$('destino_masivo_esp').value+'&destino_serv='+$('destino_masivo_serv').value+'&motivo_envio='+$('amp_id').value+'&tipo_destino='+$('select_tipo_destino').value+'&doc_id='+$('doc_id').value+'&'+$('origen').serialize(),
            onComplete:function(r)
            {
                try
                {
                    var datos=r.responseText.evalJSON(true);
                    //alert(datos);
                    alert("Fichas enviadas con exito");
                    actualizar_listados();
                    $("form_listfichas_buzon").win_obj.close();
                    
                }
                catch(err)
                {
                    alert(err);
                }
            }
        });
        
        
    }
    
    soloNumeros=function(e)
    {
        //var keynum = window.event ? window.event.keyCode : e.which;
        var keynum = (document.all) ? e.keyCode : e.which;
        var keyCrt=e.ctrlKey
        //alert(keynum);
        if((keyCrt && keynum==99) || (keyCrt && keynum==118) || (keyCrt && keynum==120))
        {
            return true;
        }
        if(keynum==0 && !document.all)
            return true;
        if (keynum == 8 || keynum == 46 || keynum == 45 || keynum == 39 || keynum == 75 || keynum == 107)
            return true;
        
        if(keynum < 48 || keynum > 57)
        {
            return false;
        }
        return true;
    }
    
    /*
    recepcionar_masivo=function()
    {
        if(lista_envio.length==0)
        {
            alert('No hay fichas seleccionadas para realizar recepci&oacute;n'.unescapeHTML());
            return;
        }
        var sin_ficha=false;
        var paciente='';
        for(var i=0;i<lista_envio.length;i++)
        {
            if(lista_envio[i]['pac_ficha']=='' || lista_envio[i]['pac_ficha']=='0' || lista_envio[i]['pac_ficha']==0 || lista_envio[i]['pac_ficha']==null)
            {
                sin_ficha=true;
                paciente=lista_envio[i]['pac_rut'];
                break;
            }
        }
        if(sin_ficha==true)
        {
            alert("No se puede mover ficha ya que el paciente "+paciente+" no tiene asisgnado un numero de ficha");
            return;
        }
        url='prestaciones/archivo_fichas/sql_mover_ficha.php';
        var myAjax=new Ajax.Request(url,
        {
            method:'post',
            parameters:'&llamada=1&fichas='+encodeURIComponent(lista_envio.toJSON())+'&recepcion=1',
            onComplete:function(r)
            {
                try
                {
                    var datos=r.responseText.evalJSON(true);
                    //alert(datos);
                    alert("Fichas recepcionadas con exito");
                    $("form_listfichas_buzon").win_obj.close();
                    
                }
                catch(err)
                {
                    alert(err);
                }
            }
        });
        
    }
    */
    
    /*
    asignar_ficha_list = function(pac_id,llamada,pos)
    {
        <?php
        /*
        $last_ficha=cargar_registro("select pac_ficha from pacientes where (pac_ficha!='' and pac_ficha!='0' and pac_ficha is not null) order by pac_ficha::bigint desc limit 1");
        if($last_ficha)
        {
            $nro_fecha=$last_ficha['pac_ficha'];
            $string="El &Uacute;ltimo N&uacute;mero de ficha ingresado corresponde al:".$nro_fecha;
        }
        else
        {
            $nro_fecha=0;
            $string="Problemas al encontrar último número de ficha";
        }
        */
        ?>
        var ficha=prompt('Ingrese N&uacute;mero de Ficha para el paciente:\n\n<?php //echo $string;?>\n\n(Deje en blanco para cancelar)'.unescapeHTML());
	if(ficha=='' || ficha==undefined)
        {
            alert('Acci&oacute;n cancelada.'.unescapeHTML());
            return;
	}
	
        var myAjax=new Ajax.Request('prestaciones/archivo_fichas/sql_ficha.php',
        {
            method:'post',
            parameters:'pac_id='+pac_id+'&ficha='+encodeURIComponent(ficha)+'&llamada='+llamada,
            onComplete:function(r) 
            {
                alert(r.responseText);
                lista_envio[pos].pac_ficha=encodeURIComponent(ficha);
                dibujar_lista();
                
            }
	});
    }
    */
    
</script>
<html>
    <div class="sub-content">
        <input type="hidden" id="origen" name="origen" value="<?php echo $unidad_origen;?>" />
        <!--<input type="hidden" id="ficha_inicial"  name="ficha_inicial" value="<?php //echo $ficha_inicial;?>"/>-->
        <!--<input type="hidden" id="txt_recepcion"  name="txt_recepcion" value="<?php //echo $recepcionar;?>"/>-->
        <table style="font-size:12px;width: 100%;">
            <?php
            //if(!$recepcionar)
            //{
            ?>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Tipo Destino:</td>
                <td class='tabla_fila'>
                    <select name='select_tipo_destino' id='select_tipo_destino' TABINDEX="1" onchange="tipo_destino();">
                        <option value="-1">(Seleccionar Tipo Destino...)</option>
                        <option value="1">Especialidades</option>
                        <option value="2">Servicios</option>

                    </select>
                </td>
            </tr>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Destino de Envio:</td>
                <td class='tabla_fila'>
                    <select name='destino_masivo_esp' id='destino_masivo_esp' TABINDEX="2" disabled>
                        <option value="-1">(Seleccionar Especialidad de Envio...)</option>
                        <?php echo $espechtml; ?>
                    </select>
                    <select name='destino_masivo_serv' id='destino_masivo_serv' TABINDEX="2" disabled style="display: none">
                        <option value="-1">(Seleccionar Servicio de Envio...)</option>
                        <?php echo $servicioshtml; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>Motivo Solicitud:</td>
		<td class='tabla_fila'>
                    <select id='amp_id' name='amp_id' style='font-size:12px;' disabled>
                        <option value="-1">(Especifique el motivo de su solicitud...)</option>
                        <?php 
                            $amp=cargar_registros_obj("SELECT * FROM archivo_motivos_prestamo ORDER BY amp_id;", true);
                            for($i=0;$i<sizeof($amp);$i++) {
				print("<option value='".$amp[$i]['amp_id']."'>".$amp[$i]['amp_nombre']."</option>");
                            }
                        ?>
                    </select>
		</td>
            </tr>
            <tr>
		<td class='tabla_fila2' style='text-align:right;'>Profesional:</td>
		<td class='tabla_fila'>
                    <input type='hidden' id='doc_id' name='doc_id' value='0' />
                    <input type='text' size=10 id='doc_rut' name='doc_rut' value='' onDblClick='limpiar_profesional();' style='font-size:16px;' disabled />
                    <input type='text' id='profesional' name='profesional'  onDblClick='limpiar_profesional();' style='text-align:left;font-size:16px;' DISABLED size=40 />
		</td>
            </tr>
            <?php
            //}
            ?>
            <tr>
                <td class='tabla_fila2' style='text-align:right;'>
                    <img src='abastecimiento/hoja_cargo/barras.png' />
                </td>
                <td class='tabla_fila'>
                    <input type='text' id='barras_list' name='barras_list' size=25 style='font-size:16px;text-align:center;' onkeypress="return soloNumeros(event);" onKeyUp='if(event.which==13) pistolear_ficha_list();' onFocus='this.style.border="3px dashed red";this.select();' onBlur='this.style.border="";this.style.background="";' />
                </td>
            </tr>
        </table>
    </div>
    <div class='sub-content2' id='list_masivo' name='list_masivo' style='height:300px;overflow:auto;'>
        
    </div>
    <center>
        <table>
            <tr>
                <td>
                    <div class='boton' style="min-width: 100px;">
                        <center>
                            <table>
                                <tr>
                                    <td>
                                        <img src='iconos/page_white_swoosh.png'>
                                    </td>
                                    <td>
                                        <a href='#' onClick='enviar_masivo();'>Enviar</a>
                                        <!--<?php //if(!$recepcionar){ ?><a href='#' onClick='enviar_masivo();'>Enviar</a><?php //} ?>-->
                                        <!--<?php //if($recepcionar){ ?><a href='#' onClick='recepcionar_masivo();'>Recepcionar</a><?php //} ?>-->
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </div>
                </td>
                <td>
                    <div class='boton' style="min-width: 100px;">
                        <table>
                            <tr>
                                <td>
                                    <img src='iconos/cancel.png'>
                                </td>
                                <td>
                                    <a href='#' onClick='limpiar_list();'>Limpiar Lista</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </center>
</html>
<script>
    //carga_inicial();
    <?php
    //if(!$recepcionar)
    //{
    ?>
    seleccionar_profesional = function(d)
    {
        $('doc_rut').value=d[1];
	$('profesional').value=d[2].unescapeHTML();
	$('doc_id').value=d[0];
    }

    limpiar_profesional = function(d)
    {
        $('doc_rut').value='';
	$('profesional').value='';
	$('doc_id').value=0;
    }
    
    autocompletar_profesionales = new AutoComplete(
        'doc_rut', 
        'autocompletar_sql.php',
        function() {
        if($('doc_rut').value.length<2) return false;
        return {
        method: 'get',
        parameters: 'tipo=doctor&nombre_doctor='+encodeURIComponent($('doc_rut').value)
        }
        }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_profesional);
    
    <?php
    //}
    ?>
</script>