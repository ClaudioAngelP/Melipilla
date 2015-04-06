<?php
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    require_once("../../../conectar_db.php");
    $especialidades = desplegar_opciones("especialidades", "esp_id, esp_desc",'1','esp_id IN ('._cav(20001),')', 'ORDER BY esp_desc');
    //$servs="'".str_replace(',','\',\'',_cav2(20001))."'";
    //$servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto AND centro_ruta IN (".$servs.") ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
?>
<script type="text/javascript" >
    var bloquear_listar_fichas=false;
    var registros_fichas="";
    var fichas_recepcionar="";
    var fichas_unidad="";
    var tipo_busqueda=0;
    var opts=new Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
    var opts_color=new Array('black','yellowgreen','yellowgreen','purple','green','red');
    //---------------------------------------------------------------------------
    //---------------------------------------------------------------------------
    $j(document).ready(function()
    {
        $j('#select_origen').change(function()
        {
            if($j('#select_origen').val()==-1) 
            {
                $j('#list_fichas_unidad').html("");
                $j('#list_fichas_solicitadas').html("");
                $j('#list_fichas_recepcionadas').html("");
                $j('#tab_fichas_unidad').html("<img src='iconos/report_add.png'> Fichas en Unidad (0)");
                $j('#tab_fichas_solicitadas').html("<img src='iconos/report_add.png'> Fichas por Solicitadas (0)");
                $j('#tab_fichas_recepcionar').html("<img src='iconos/report_add.png'> Fichas por Recepcionar (0)");
                $j("#table_codbarras").css("display","none");
                $j("#table_codbarras_unidad").css("display","none");
                return;
            }
            else
            {
                listar_fichas(1);
                //listar_fichas(2);
                listar_fichas(3);
            }
        });
    });
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    listar_fichas = function(index_busqueda)
    {
        if(bloquear_listar_fichas)
        {
            return;
        }
        var especialidad=$j('#select_origen').val();
        var doc_id=$j('#doc_id').val();
        //var enviado_por=$j('#bodega_filtro').val();
        //var tipo_doc=$j('#select_tipodoc').val();
        $j('#barras_unidad').val('');
        bloquear_listar_fichas=true;
        if(index_busqueda==1)
        {
            $j('#list_fichas_unidad').html('<center><table><tr><td><img src=imagenes/ajax-loader1.gif></td></tr></table></center>');
        }
        /*
        if(index_busqueda==2)
        {
            $j('#list_fichas_solicitadas').html('<center><table><tr><td><img src=imagenes/ajax-loader1.gif></td></tr></table></center>');
        }
        */
        if(index_busqueda==3)
        {
            $j('#list_fichas_recepcionadas').html('<center><table><tr><td><img src=imagenes/ajax-loader1.gif></td></tr></table></center>');
        }
        $j.ajax(
        {
            url: 'prestaciones/archivo_fichas/buzon_fichas/buscar_fichas.php',
            type: 'POST',
            dataType: 'json',
            async:false,
            data: {especialidad:especialidad,tipo_busqueda:index_busqueda,doc_id:$j('#doc_id').val(),fecha1:$j('#fecha1').val()},
            success: function(data)
            {
                registros_fichas=data;
                dibujar_fichas(index_busqueda);
            }
        });
        bloquear_listar_fichas=false;
    }
    //--------------------------------------------------------------------------
    dibujar_fichas=function(index_dibujar)
    {
        if(index_dibujar==1)
        {
            if(registros_fichas[0]==false)
            {
                $j('#list_fichas_unidad').html('<center><table><tr><td>NO SE HAN ENCONTRADO FICHAS EN LA UNIDAD</td></tr></table></center>');
		$j('#tab_fichas_unidad').html("<img src='iconos/report_add.png'> Fichas en Unidad (0)");	
 		$j("#table_codbarras_unidad").css("display","none");
 		$j("#table_codbarras").css("display","none");
            }
            else
            {
                fichas_unidad=registros_fichas;
		if(tipo_busqueda==0)
		{
                    $j("#table_codbarras_unidad").css("display","block");
                    $j("#table_codbarras").css("display","none");
		}
                else
                {
                    if(tipo_busqueda==1)
                    {
                        $j("#table_codbarras_unidad").css("display","block");
                        $j("#table_codbarras").css("display","none");
                    }
                }  
		var html="";
		doc_ant="";
		esp_ant="";
                for(var i=0;i<fichas_unidad[0].length;i++)
                {
                    if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
                    if(doc_ant!=fichas_unidad[0][i]['doc_id'] || esp_ant!=fichas_unidad[0][i]['esp_id'])
                    {
                        doc_ant=fichas_unidad[0][i]['doc_id'];
			esp_ant=fichas_unidad[0][i]['esp_id'];
                        cont=1;
                        html+='<table style="width:100%;" class="lista_small">';
                            html+='<tr class="tabla_header">';
                                html+='<td style="text-align:left;font-size:16px;" colspan=11>Programa: <b>'+ fichas_unidad[0][i]['esp'] +'</b><br/>Profesional / Servicio: <b>'+fichas_unidad[0][i]['doc_nombre']+'</b></td>';
                            html+="</tr>";
                            html+="<tr class='tabla_header'>";
                                html+="<td style='width:3%;'>#</td>";
                                html+="<td style='width:15%;'>Fecha Recepci&oacute;n</td>";
                                html+="<td style='width:10%;'>Ficha</td>";
                                html+="<td style='width:12%;'>RUN</td>";
                                html+="<td style='width:40%;'>Nombre Completo</td>";
                                html+="<td style='width:20%;'>Ubic. Actual</td>";
                                html+="<td>Estado</td>";
				//html+="<td>Etiqueta</td>";
				html+="<td>Historial</td>";
                            html+="</tr>";
                    }
                    var estado="";
                    for(var x=0;x<opts.length;x++)
                    {
                        if(fichas_unidad[0][i]['am_estado']*1==x)
                        {
                            estado=opts[x];
                            break;
                        }
                    }
                    html+="<tr class='"+clase+"' style='color:"+opts_color[fichas_unidad[0][i]['am_estado']*1]+";' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\""+clase+"\";'>";
                        html+="<td style='text-align:center;'>"+cont+"</td>";
                        html+="<td style='text-align:center;'>"+fichas_unidad[0][i]['fecha_recepcion']+"</td>";
                        html+="<td style='text-align:right;font-size:14px;font-weight:bold;'>"+fichas_unidad[0][i]['pac_ficha']+"</td>";
                        html+="<td style='text-align:right;'>"+fichas_unidad[0][i]['pac_rut']+"</td>";
                        html+="<td style='text-align:left;'>"+fichas_unidad[0][i]['pac_nombre']+"</td>";
                        html+="<td style='text-align:left;'>"+fichas_unidad[0][i]['ubic_actual']+"</td>";
                        html+="<td style='text-align:center;'>";
                            html+=""+estado+"";
                        html+="</td>";
                        /*
                        html+="<td>";
                            html+="<center>";
                                html+="<img src='iconos/printer.png'  style='cursor:pointer;' alt='Imprimir Etiqueta' title='Imprimir Etiqueta' onClick='imprimir_etiqueta("+fichas_unidad[0][i]['pac_id']+");' />";
                            html+="</center>";
                 	html+="</td>";
                        */
                        html+="<td>";
                            html+="<center>";
                                html+="<img src='iconos/magnifier.png'  style='cursor:pointer;' alt='Ver Historial' title='Ver Historial' onClick='historial_ficha("+fichas_unidad[0][i]['pac_id']+");' />";
                            html+="</center>";
                 	html+="</td>";
                    html+="</tr>";
                    cont++;
		}
		html+="</table>";
                $j('#list_fichas_unidad').html(html);
                $j('#tab_fichas_unidad').html("<img src='iconos/report_add.png'> Fichas en Unidad ( <font size='2' color='red'><i><b>"+fichas_unidad[0].length+"</b></i></font> )");         	
            }
        }
        if(index_dibujar==3)
        {
            if(registros_fichas[0]==false)
            {
                $j('#list_fichas_recepcionadas').html('<center><table><tr><td>NO SE HAN ENCONTRADO FICHAS POR RECEPCIONAR</td></tr></table></center>');
                $j('#tab_fichas_recepcionar').html("<img src='iconos/report_add.png'> Fichas por Recepcionar (0)");
                $j("#table_codbarras").css("display","none");
            }
            else
            {
                fichas_recepcionar=registros_fichas;
                if(tipo_busqueda==3)
                {
                    $j("#barras_recepcion").val('');
                    $j("#table_codbarras").css("display","block");
		}
		var html="";
		doc_ant="";
		esp_ant="";
                for(var i=0;i<fichas_recepcionar[0].length;i++)
                {
                    if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
                    if(doc_ant!=fichas_recepcionar[0][i]['doc_id'] || esp_ant!=fichas_recepcionar[0][i]['esp_id'])
                    {
                        doc_ant=fichas_recepcionar[0][i]['doc_id'];
			esp_ant=fichas_recepcionar[0][i]['esp_id'];
                        cont=1;
                        html+='<table style="width:100%;" class="lista_small">';
                            html+='<tr class="tabla_header">';
                                html+='<td style="text-align:left;font-size:16px;" colspan=11>Programa: <b>'+ fichas_recepcionar[0][i]['esp'] +'</b><br/>Profesional / Servicio: <b>'+fichas_recepcionar[0][i]['doc_nombre']+'</b></td>';
                            html+="</tr>";
                            html+="<tr class='tabla_header'>";
                                html+="<td style='width:3%;'>#</td>";
                                html+="<td style='width:15%;'>Solicitado</td>";
                                html+="<td style='width:10%;'>Ficha</td>";
                                html+="<td style='width:12%;'>RUN</td>";
                                html+="<td style='width:40%;'>Nombre Completo</td>";
                                html+="<td style='width:20%;'>Enviada Por</td>";
                    		html+="<td>Estado</td>";
				//html+="<td>Etiqueta</td>";
				html+="<td>Historial</td>";
                          	html+="<td>Rechazar</td>";
                            html+="</tr>";
                    }
                    var estado="";
                    for(var x=0;x<opts.length;x++)
                    {
                        if(fichas_recepcionar[0][i]['am_estado']*1==x)
                        {
                            estado=opts[x];
                            break;
                    	}
                    }
                    html+="<tr class='"+clase+"' style='color:#CC6666;' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\""+clase+"\";'>";
                        //html+="<input type='hidden' id='nomd_id_"+fichas_recepcionar[0][i]['pac_ficha']+"' name='nomd_id_"+fichas_recepcionar[0][i]['pac_ficha']+"' value='"+fichas_recepcionar[0][i]['nomd_id']+"' />";
                        html+="<td style='text-align:center;'><input type='hidden' id='nomd_id_"+fichas_recepcionar[0][i]['pac_ficha']+"' name='nomd_id_"+fichas_recepcionar[0][i]['pac_ficha']+"' value='"+fichas_recepcionar[0][i]['nomd_id']+"' />"+cont+"</td>";
                    	html+="<td style='text-align:center;'>"+fichas_recepcionar[0][i]['fecha_asigna']+"</td>";
                   	html+="<td style='text-align:right;font-size:14px;font-weight:bold;'>"+fichas_recepcionar[0][i]['pac_ficha']+"</td>";
                    	html+="<td style='text-align:right;'>"+fichas_recepcionar[0][i]['pac_rut']+"</td>";
                    	html+="<td style='text-align:left;'>"+fichas_recepcionar[0][i]['pac_nombre']+"</td>";
                    	if(fichas_recepcionar[0][i]['ubic_anterior']==fichas_recepcionar[0][i]['ubic_actual'])
                    	{
                            if(fichas_recepcionar[0][i]['am_enviado_por']==0)
                            {
                                html+="<td style='text-align:left;'>ARCHIVO</td>";
                            }
                            else
                            {
                                html+="<td style='text-align:left;'>"+fichas_recepcionar[0][i]['ubic_anterior']+"</td>";
                            }
			}
			else
                        {
                            html+="<td style='text-align:left;'>"+fichas_recepcionar[0][i]['ubic_anterior']+"</td>";
			}		
                   	html+="<td style='text-align:center;'>";
                            html+="Por Recepcionar";
                            //html+=""+estado+"";
                    	html+="</td>";
                        /*
                        html+="<td>";
                            html+="<center>";
                        	html+="<img src='iconos/printer.png'  style='cursor:pointer;' alt='Imprimir Etiqueta' title='Imprimir Etiqueta' onClick='imprimir_etiqueta("+fichas_recepcionar[0][i]['pac_id']+");' />";
                            html+="</center>";
                        html+="</td>";
                        */
                        html+="<td>";
                            html+="<center>";
                                html+="<img src='iconos/magnifier.png'  style='cursor:pointer;' alt='Ver Historial' title='Ver Historial' onClick='historial_ficha("+fichas_recepcionar[0][i]['pac_id']+");' />";
                            html+="</center>";
                    	html+="</td>";
                        html+="<td>";
                            html+="<center>";
                                html+="<img src='iconos/delete.png'  style='cursor:pointer;' alt='Rechazar Envio' title='Rechazar Envio' onClick='Rechazar_envio("+fichas_recepcionar[0][i]['am_id']+");' />";
                            html+="</center>"; 
                    	html+="</td>";
                    html+="</tr>";
                    cont++;
                }
                html+="</table>";
                $j('#list_fichas_recepcionadas').html(html);
                $j('#tab_fichas_recepcionar').html("<img src='iconos/report_add.png'> Fichas por Recepcionar ( <font size='2' color='red'><i><b>"+fichas_recepcionar[0].length+"</b></i></font> )");	
            }
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    ver_fichas=function(tabs_index)
    {
        if($j('#select_origen').val()==-1) 
        {
            $j('#list_fichas_unidad').html("");
            //$j('#list_fichas_solicitadas').html("");
            $j('#list_fichas_recepcionadas').html("");
            $j('#tab_fichas_unidad').html("<img src='iconos/report_add.png'> Fichas en Unidad (0)");
            //$j('#tab_fichas_solicitadas').html("<img src='iconos/report_add.png'> Fichas por Solicitadas (0)");
            $j('#tab_fichas_recepcionar').html("<img src='iconos/report_add.png'> Fichas por Recepcionar (0)");
            $j("#table_codbarras").css("display","none");
            $j("#table_codbarras_unidad").css("display","none");
            return;
        }
        if(tabs_index==1)
        {
            //tab_down('tab_fichas_solicitadas');
            tab_down('tab_fichas_recepcionar');
            tab_up('tab_fichas_unidad');
            $j("#table_codbarras").css("display","none");
            tipo_busqueda=tabs_index;
            actualizar_listados();
            //listar_fichas(tipo_busqueda)
        }
        if(tabs_index==2)
        {
            tab_down('tab_fichas_unidad');
            tab_down('tab_fichas_recepcionar');
            //tab_up('tab_fichas_solicitadas');
            $j("#table_codbarras").css("display","none");
            $j("#table_codbarras_unidad").css("display","none");
            tipo_busqueda=tabs_index;
            actualizar_listados();
            //listar_fichas(tipo_busqueda)
        }
        if(tabs_index==3)
        {
            tab_down('tab_fichas_unidad');
            //tab_down('tab_fichas_solicitadas');
            tab_up('tab_fichas_recepcionar');
            $j("#table_codbarras_unidad").css("display","none");
            tipo_busqueda=tabs_index;
            actualizar_listados();
            //listar_fichas(tipo_busqueda)
        }
    }
    //**************************************************************************
    //**************************************************************************
    actualizar_listados=function()
    {
        if($j('#select_origen').val()==-1) 
        {
            $j('#list_fichas_unidad').html("");
            //$j('#list_fichas_solicitadas').html("");
            $j('#list_fichas_recepcionadas').html("");
            $j('#tab_fichas_unidad').html("<img src='iconos/report_add.png'> Fichas en Unidad (0)");
            //$j('#tab_fichas_solicitadas').html("<img src='iconos/report_add.png'> Fichas por Solicitadas (0)");
            $j('#tab_fichas_recepcionar').html("<img src='iconos/report_add.png'> Fichas por Recepcionar (0)");
            $j("#table_codbarras").css("display","none");
            $j("#table_codbarras_unidad").css("display","none");
            return;
        }
        else
        {
            listar_fichas(1);
            //listar_fichas(2);
            listar_fichas(3);
        }
        
    }
//----------------------------------------------------------------------------
//----------------------------------------------------------------------------
    pistolear_ficha = function ()
    {
        if($j('#barras_recepcion').val()=="")
        {
            alert("Debe Ingresar Nro de Ficha o Rut del Paciente para recepcionar ficha");
            return;
            
        }
        else
        {
            var encontrado=false;
            var encontrado_ficha=false;
            var encontrado_rut=false;
            for(var i=0;i<fichas_recepcionar[0].length;i++)
            {
                if(fichas_recepcionar[0][i]['pac_ficha']==$j('#barras_recepcion').val())
                {
                    encontrado=true;
                    encontrado_ficha=true;
                    break;
                }
                else
                {
                    if(fichas_recepcionar[0][i]['pac_rut']==$j('#barras_recepcion').val())
                    {
                        encontrado=true;
                        encontrado_rut=true;
                        break;
                    }
                }
            }
            if(encontrado==true)
            {
                //url='sql_mover_ficha.php';
                //$fecha = pg_escape_string($_POST['fecha1']); 
                //params='&nomd_id='+encodeURIComponent(nomd_id)+'&tipo_inf=1&fecha=&estado_ficha=2'
                //*
                
                var myAjax=new Ajax.Request('prestaciones/archivo_fichas/buzon_fichas/sql_mover_ficha.php',
                {
                    method:'post',
                    parameters:$('form_mov_fichas').serialize()+'&tipo_inf=1&estado_ficha=3'+'&encontrado_rut='+encontrado_rut+'&encontrado_ficha='+encontrado_ficha+'&ubicacion='+$j('#select_origen').val()+'&barras='+$('barras_recepcion').value+'&fecha=',
                    onComplete:function(r)
                    {
                        try
                        {
                            var datos=r.responseText.evalJSON(true);
                            var limpiar=false;
                            if(!datos)
                            {
                                alert("Error al recepcionar ficha de paciente");
                                $('barras_recepcion').style.background='yellow';
                                return;
                            }
                            else
                            {
                                limpiar=true;
                                $('barras_recepcion').style.background='yellowgreen';
                            }
                            actualizar_listados();
                            if(limpiar)
                                $('barras_recepcion').value='';
                            $('barras_recepcion').select();
                            $('barras_recepcion').focus();
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
                alert("No se encontrado paciente en el listado de recepciones");
                return;
            }
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    pistolear_ficha_unidad=function(nomd_id)
    {
        var datos_fichas="";
        if(nomd_id!=undefined)
        {
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            url='prestaciones/archivo_fichas/buzon_fichas/sql_mover_ficha.php';
            //$fecha = pg_escape_string($_POST['fecha1']); 
            params='&nomd_id='+encodeURIComponent(nomd_id)+'&tipo_inf=1&fecha=&estado_ficha=2'
            //info_nominas
            var myAjax=new Ajax.Request(url,
            {
                method:'post',
		parameters:'barras='+$j('#barras_unidad').val()+params,
		onComplete:function(r)
                {
                    try
                    {
                        var datos=r.responseText.evalJSON(true);
			var limpiar=true;
                        /*
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
                        */
                        /*
                        if(!datos)
                        {
                            alert("no datos");
                        }
                        else
                        {
                            alert("datos");
                        }
                        return;
			if(datos[1])
                        {
                            listar_nominas();
			}
                        else
                        {
                            var html='<center><h2><b><u>ATENCI&Oacute;N:</u></b><br/><br/>Paciente tiene '+datos[2].length+' solicitudes para esta misma fecha ('+$('fecha1').value+'), debe seleccionar destino de la ficha:</h2><br/><br/>';
                            html+='<table style="width:80%;font-size:18px;"><tr class="tabla_header"><td>Fecha Solicitud</td><td>Especialidad</td><td>Profesional/Servicio</td><td>Hora</td><td>Motivo</td><td>Enviar</td></tr>'
                            var d=datos[2];
                            for(var i=0;i<d.length;i++)
                            {
                                html+='<tr class="'+(i%2==0?'tabla_fila':'tabla_fila2')+'" ';
				html+='onMouseOver="this.className=\'mouse_over\';" ';
				html+='onMouseOut="this.className=\''+(i%2==0?'tabla_fila':'tabla_fila2')+'\';" >';
				html+='<td style="text-align:center;">'+d[i].fecha_sol.substr(0,16)+'</td>';
				html+='<td style="font-weight:bold;font-size:14px;">'+d[i].esp_desc+'</td>';
				html+='<td style="font-size:14px;">'+d[i].doc_nombre+'</td>';
				html+='<td style="text-align:center;font-size:20px;">'+(d[i].nomd_hora!=undefined?d[i].nomd_hora.substr(0,5):'')+'</td>';
				html+='<td style="font-weight:bold;font-size:12px;">'+d[i].amp_nombre+'</td>';
				html+='<td><center><img src="iconos/arrow_right.png" style="cursor:pointer;width:32px;height:32px;" onClick="pistolear_ficha('+d[i].nomd_id+');" /></center></td>';
				html+='</tr>'
                            }
                            html+='</table></center>';
                            $('listado_nominas').innerHTML=html;
                            return;
			}
                        */
                        if(limpiar)
                            $('barras_unidad').value='';
                            $('barras_recepcion').value='';
                        
                    }
                    catch(err)
                    {
                        alert(err);
                    }
                }
            });
            listar_fichas(1);
            listar_fichas(3);
            return;
            
            
            
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            
            
        }
        if($j('#barras_unidad').val()=="")
        {
            alert("Debe Ingresar Nro de Ficha o Rut del Paciente para devolver ficha");
            return;
            
        }
        else
        {
            var encontrado=false;
            var encontrado_ficha=false;
            var encontrado_rut=false;
            var fecha_ficha="";
            for(var i=0;i<fichas_unidad[0].length;i++)
            {
                if(fichas_unidad[0][i]['pac_ficha']==$j('#barras_unidad').val())
                {
                    encontrado=true;
                    encontrado_ficha=true;
                    //fecha_ficha=fichas_unidad[0][i]['fecha_asigna'];
                    fecha_ficha=$('fecha1').value;
                    break;
                }
                else
                {
                    if(fichas_unidad[0][i]['pac_rut']==$j('#barras_unidad').val())
                    {
                        encontrado=true;
                        encontrado_rut=true;
                        //fecha_ficha=fichas_unidad[0][i]['fecha_asigna'];
                        fecha_ficha=$('fecha1').value;
                        break;
                    }
                }
            }
            if(encontrado==true)
            {
                if(bloquear_listar_fichas)
                {
                    return;
                }
                var ubicacion=$j('#select_origen').val();
                bloquear_listar_fichas=true;
                var myAjax=new Ajax.Request('prestaciones/archivo_fichas/buzon_fichas/buscar_fichas.php',
		{
                    method:'post',
                    parameters:'ubicacion='+ubicacion+'&tipo_busqueda=4&option=2&doc_id='+$j('#doc_id').val()+'&fecha1='+fecha_ficha+'&ficha='+$j('#barras_unidad').val(),
                    onComplete:function(r)
                    {
                        try
                        {
                            var datos=r.responseText.evalJSON(true);
                            var limpiar=false;
                            if(!datos)
                            {
                                /*
                                $('barras_unidad').style.background='yellow';
				$('texto_barras').style.background='yellow';
				$('texto_barras').style.color='black';
				$('texto_barras').value='PACIENTE NO ENCONTRADO.';
                                */
                            }
                            else
                            {
                                /*
                                $('barras').style.background='yellowgreen';						
				$('texto_barras').style.background='black';
				$('texto_barras').style.color='white';
				$('texto_barras').value=(datos[0][0].pac_rut+' - '+datos[0][0].pac_nombres+' '+datos[0][0].pac_appat+' '+datos[0][0].pac_apmat).unescapeHTML();
				limpiar=true;
                                */
                            }
                            if(datos=="")
                            {
                                //alert("Uno solo");
                                //alert("Enviar a archivo");
                                
                                
                                var myAjax=new Ajax.Request('prestaciones/archivo_fichas/buzon_fichas/sql_mover_ficha.php',
                                {
                                    method:'post',
                                    parameters:$('form_mov_fichas').serialize()+'&tipo_inf=-1'+'&encontrado_rut='+encontrado_rut+'&encontrado_ficha='+encontrado_ficha+'&ubicacion='+$j('#select_origen').val(),
                                    onComplete:function(r)
                                    {
                                        try
                                        {
                                            var datos=r.responseText.evalJSON(true);
                                            var limpiar=false;
                                            if(!datos)
                                            {
                                                alert("Error al recepcionar ficha de paciente");
                                                $('barras_recepcion').style.background='yellow';
                                                return;
                                            }
                                            else
                                            {
                                                limpiar=true;
                                                $('barras_recepcion').style.background='yellowgreen';
                                            }
                                            actualizar_listados();
                                            if(limpiar)
                                                $('barras_recepcion').value='';
                                                $('barras_recepcion').select();
                                                $('barras_recepcion').focus();
                                        }
                                        catch(err)
                                        {
                                            alert(err);
                                        }
                                    }
                                });
                                bloquear_listar_fichas=false;
                                return;
                                //listar_nominas();
                                
                            }
                            else
                            {
                                var html='<center><h2><b><u>ATENCI&Oacute;N:</u></b><br/><br/>Paciente tiene '+datos[2].length+' solicitudes para esta misma fecha ('+$('fecha1').value+'), debe seleccionar destino de la ficha:</h2><br/><br/>';
				html+='<table style="width:80%;font-size:18px;"><tr class="tabla_header"><td>Fecha Solicitud</td><td>Especialidad</td><td>Profesional/Servicio</td><td>Hora</td><td>Motivo</td><td>Enviar</td></tr>'
				var d=datos[2];
				for(var i=0;i<d.length;i++)
                                {
                                    html+='<tr class="'+(i%2==0?'tabla_fila':'tabla_fila2')+'" ';
                                    html+='onMouseOver="this.className=\'mouse_over\';" ';
                                    html+='onMouseOut="this.className=\''+(i%2==0?'tabla_fila':'tabla_fila2')+'\';" >';
                                    html+='<td style="text-align:center;">'+d[i].fecha_sol.substr(0,16)+'</td>';
                                    html+='<td style="font-weight:bold;font-size:14px;">'+d[i].esp_desc+'</td>';
                                    html+='<td style="font-size:14px;">'+d[i].doc_nombre+'</td>';
                                    html+='<td style="text-align:center;font-size:20px;">'+(d[i].nomd_hora!=undefined?d[i].nomd_hora.substr(0,5):'')+'</td>';
                                    html+='<td style="font-weight:bold;font-size:12px;">'+d[i].amp_nombre+'</td>';
                                    html+='<td><center><img src="iconos/arrow_right.png" style="cursor:pointer;width:32px;height:32px;" onClick="pistolear_ficha_unidad('+d[i].nomd_id+');" /></center></td>';
                                    html+='</tr>'
				}
				html+='</table></center>';
				$('list_fichas_unidad').innerHTML=html;
                                bloquear_listar_fichas=false;
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
                
                
                
                
                /*
                $j.ajax(
                {
                    url: 'prestaciones/archivo_fichas/buzon_fichas/buscar_fichas.php',
                    type: 'POST',
                    dataType: 'json',
                    async:false,
                    data: {ubicacion:ubicacion,tipo_busqueda:4,option:2,doc_id:$j('#doc_id').val(),fecha1:fecha_ficha,ficha:$j('#barras_unidad').val()},
                    success: function(data)
                    {
                        registros_fichas_busqueda=data;
                        //dibujar_fichasbusqu(index_busqueda);
                        datos_fichas= JSON.stringify(registros_fichas_busqueda);
                    }
                });
                return;
                bloquear_listar_fichas=false;
                if(registros_fichas_busqueda!=false)
                {
                    if(registros_fichas_busqueda[0].length>1)
                    {
                        //var origen="ARCHIVOS";
                        var titulo="Proceso de Validaci&oacute;n de ENVIO DE FICHA Nro: "+ $j('#barras_unidad').val();
                        var ubicacion_text="ARCHIVOS";
                        var win = new Window("validar_ficha",
                        {
                            className: "alphacube", top:40, left:0, width: 570, height: 550, 
                            title: '<img src="iconos/page_white_link.png"> '+titulo+'',
                            minWidth: 500, minHeight: 400,
                            maximizable: false, minimizable: false,
                            wiredDrag: true, draggable: true,
                            closable: true, resizable: false 
                        });
                        win.setDestroyOnClose();
                        win.setAjaxContent('prestaciones/archivo_fichas/buzon_fichas/validar_form.php',
                        {
                            method: 'post',
                            async:false,
                            dataType: 'json',
                            //parameters: 'estado='+estado+'&ubicacion='+ubicacion_text+'&datos_fichas='+datos_fichas+'&color_estado='+color+'&fecha1='+$j('#fecha1').val(),
                            parameters:'ubicacion='+ubicacion+'&tipo_busqueda=2&option=2&doc_id=-1&fecha1='+fecha_ficha+'&ficha='+$j('#barras_unidad').val()+'&fichas_futuras='+datos_fichas,
                        });
                        $("validar_ficha").win_obj=win;
                        win.showCenter();
                        win.show(true);
                        
                    }
                    else
                    {
                        alert("Encontro Uno");
                        return;
                    }
                }
                else
                {
                    var myAjax=new Ajax.Request('prestaciones/archivo_fichas/buzon_fichas/sql_mover_ficha.php',
                    {
                        method:'post',
                        parameters:$('form_mov_fichas').serialize()+'&tipo_mov=2'+'&encontrado_rut='+encontrado_rut+'&encontrado_ficha='+encontrado_ficha+'&ubicacion='+$j('#select_origen').val(),
                        onComplete:function(r)
                        {
                            try
                            {
                                var datos=r.responseText.evalJSON(true);
                                var limpiar=false;
                                if(!datos)
                                {
                                    alert("Error al recepcionar ficha de paciente");
                                    $('barras_recepcion').style.background='yellow';
                                    return;
                                }
                                else
                                {
                                    limpiar=true;
                                    $('barras_recepcion').style.background='yellowgreen';
                                }
                                actualizar_listados();
                                if(limpiar)
                                    $('barras_recepcion').value='';
                                $('barras_recepcion').select();
                                $('barras_recepcion').focus();
                            }
                            catch(err)
                            {
                                alert(err);
                            }
                        }
                    });
                }
                */
            }
            else
            {
                alert("No se encontrado la fichas ingresada en el listado de fichas en su unidad");
                return;
            }
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
</script>
<style type="text/css">
    .a{
        background-color: #00F0FF;
        margin: 2px;
    }
</style>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<body>
<form action="" id='form_mov_fichas' autocomplete="off" onsubmit="return false;">
<center>
    <table>
        <tr>
            <td style="width: 1090px;">
                <form name='form_doc' id='form_doc'>
                    <div class='sub-content'>
                        <table width=100%>
                            <tr>
                                <td valign='top' >
                                    <div class='sub-content'>
                                        <img src='iconos/page_find.png'>
                                        <b>Recepci&oacute;n y Env&iacute;o de Fichas</b>
                                    </div>
                                    <div class='sub-content'>
                                        <table>
                                            <tr>
                                                <td style='text-align: right;'>Ubicaci&oacute;n de Or&iacute;gen:</td>
                                                <td>
                                                    <select name='select_origen' id='select_origen' TABINDEX="1">
                                                        <option value="-1" selected>Seleccionar Origen..</option>
                                                        <!--<option value="0">Todos Los Origenes..</option>-->
                                                        <?php echo $especialidades;?>
                                                    </select>
                                                </td>
                                                <td style='text-align:right;'>Fecha:</td>
                                                <td>
                                                    <input type='text' name='fecha1' id='fecha1' size=10 style='text-align: center;' value='<?php echo date("d/m/Y")?>' onChange='actualizar_listados();' />
                                                    <img src='iconos/date_magnify.png' id='fecha1_boton' />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style='text-align:left;'>Profesional:</td>
                                                <td colspan=3>
                                                    <input type='text' id='doc_rut' name='doc_rut' size=25 onDblClick='this.value=""; $("span_doc_nombre").innerHTML="(Todos los Profesionales)"; $("doc_id").value=-1;listar_fichas(1);' />
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <span id='span_doc_nombre' name='span_doc_nombre'>(Todos los Profesionales...)</span>
                                                    <input type='hidden' id='doc_id' name='doc_id' value='-1' />
                                                </td>
                                            </tr>
                                        </table>
                                        <table width=100% cellpadding=0 cellspacing=0>
                                            <tr>
                                                <td>
                                                    <table cellpadding=0 cellspacing=0>
                                                        <tr>
                                                            <td>
                                                                <div class='tabs' id='tab_fichas_unidad' style='cursor: default;' onClick='ver_fichas(1);'>
                                                                    <img src='iconos/report.png'>
                                                                    Fichas en Unidad (0)
                                                                </div>
                                                            </td>
                                                            <!--
                                                            <td>
                                                                <div class='tabs_fade' id='tab_fichas_solicitadas' style='cursor: pointer;' onClick='ver_fichas(2);'>
                                                                    <img src='iconos/report_go.png'>
                                                                    Fichas Solicitadas (0)
                                                                </div>
                                                            </td>
                                                            -->
                                                            <td>
                                                                <div class='tabs_fade' id='tab_fichas_recepcionar' style='cursor: pointer;' onClick='ver_fichas(3);'>
                                                                       <img src='iconos/report_add.png'>
                                                                       Fichas por Recepcionar (0)
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <table id="table_codbarras_unidad" style="display: none">
                                                                    <tr>
                                                                        <td style="text-align:right;">
                                                                            <img src="abastecimiento/hoja_cargo/barras.png" style="width: 25px;height: 30px;"/>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" onblur="this.style.border=&quot;&quot;;this.style.background=&quot;&quot;;" onfocus="this.style.border=&quot;3px dashed red&quot;;this.select();" onkeyup="if(event.which==13) pistolear_ficha_unidad();" style="font-size: 15px; text-align: center;" size="25" name="barras_unidad" id="barras_unidad" />
                                                                            <!--<input type="text" value="&lt;&lt; Seleccione Fichas con Código de Barras" style="font-size:18px;text-align:left;border:none;" disabled="" readonly="" size="60" name="texto_barras" id="texto_barras" />-->
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table id="table_codbarras" style="display: none">
                                                                    <tr>
                                                                        <td style="text-align:right;">
                                                                            <img src="abastecimiento/hoja_cargo/barras.png" style="width: 25px;height: 30px;"/>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" onblur="this.style.border=&quot;&quot;;this.style.background=&quot;&quot;;" onfocus="this.style.border=&quot;3px dashed red&quot;;this.select();" onkeyup="if(event.which==13) pistolear_ficha();" style="font-size: 15px; text-align: center;" size="25" name="barras_recepcion" id="barras_recepcion" />
                                                                            <!--<input type="text" value="&lt;&lt; Seleccione Fichas con Código de Barras" style="font-size:18px;text-align:left;border:none;" disabled="" readonly="" size="60" name="texto_barras" id="texto_barras" />-->
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class='tabbed_content' id='tab_fichas_unidad_content'>
                                                        <div class='sub-content2' id='list_fichas_unidad' name='list_fichas_unidad' style='height:250px;overflow:auto;'>

                                                        </div>
                                                    </div>
                                                    <!--
                                                    <div class='tabbed_content' id='tab_fichas_solicitadas_content' style='display:none;'>
                                                        <div class='sub-content2' id='list_fichas_solicitadas' name='list_fichas_unidad' style='height:250px;overflow:auto;'>

                                                        </div>
                                                    </div>
                                                    -->
                                                    <div class='tabbed_content' id='tab_fichas_recepcionar_content' style='display:none;'>
                                                        <div class='sub-content2' id='list_fichas_recepcionadas' name='list_fichas_unidad' style='height:250px;overflow:auto;'>

                                                        </div>
                                                    </div>
                                                    <div class='tabbed_content' id='tab_documentos_recepcionar_content' style='display:none;'>
                                                        <div class='sub-content2' id='list_recepcionar' name='list_recepcionar' style='height: 250px; overflow:auto;'>
                                                            
                                                        </div>
                                                    </div>
                                                    <!--
                                                    <div class='tabbed_content' id='tab_documentos_masivo_content' style='display:none;'>
                                                        <div class='sub-content2' id='list_masivo' name='list_masivo' style='height: 250px; overflow:auto;'>
                                                            
                                                        </div>
                                                    </div>
                                                    -->
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </td>
        </tr>
    </table>
</center>
</form>
</body>
</html>
<script type="text/javascript" >
    
    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    
    
    
    //--------------------------------------------------------------------------
    seleccionar_profesional = function(d)
    {
        $('doc_rut').value=d[1];
        $('span_doc_nombre').innerHTML=d[2];
        $('doc_id').value=d[0];
        listar_fichas(1);
    }
    //--------------------------------------------------------------------------
    
    //--------------------------------------------------------------------------
    
    autocompletar_profesionales = new AutoComplete(
    'doc_rut', 'autocompletar_sql.php',
    function() {
        if($('doc_rut').value.length<2) return false;
        return {
            method: 'get',
            parameters: 'tipo=doctor&nombre_doctor='+encodeURIComponent($('doc_rut').value)
        }
    }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_profesional);
    /*
    listar_documentos();
    listar_enviados();
    listar_porrecepcionar();
    listar_masivos_precep();
    __set_timeout_timer2();
    */
</script>