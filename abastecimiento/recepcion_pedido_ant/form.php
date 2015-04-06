<?php
    require_once("../../conectar_db.php");
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(9),')', 'ORDER BY bod_glosa'); 
    $servs="'".str_replace(',','\',\'',_cav2(9))."'";
    $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto AND centro_ruta IN (".$servs.") ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");
?>
<script>
    articulos=new Array();
    bloquear_descarga=false;
    chequear_todo = function(valor)
    {
        for(i=0;i<articulos.length;i++)
        {
            if(valor)
            {
                $("acepta_art_"+i).checked=true;
            }
            else
            {
                $("acepta_art_"+i).checked=false;
            }
        }
    }
    listar_pedidos = function()
    {
        l=(screen.availWidth/2)-300;
        t=(screen.availHeight/2)-185;
        win = window.open('abastecimiento/recepcion_pedido/listar_pedidos.php?'+$('bodega_origen').serialize(),
        'listar_pedidos',
        'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
        'resizable=no, width=600, height=370');
        win.focus();
    }
    
    abrir_pedido = function(nro_pedido) 
    {
        bloquear_descarga=true;
        $('numero_pedido').value=nro_pedido;
        $("busca_pedidos").win_obj.destroy();
        bloquear_descarga=false;
        descargar_pedido();
    }
    
    descargar_pedido = function()
    {
        $('cargar_pedido_img').style.display='';
        if(!bloquear_descarga)
        {
            var myAjax1 = new Ajax.Request('abastecimiento/recepcion_pedido/bajar_pedidos.php',
            {
                method: 'get',
                parameters: $('bodega_origen').serialize()+'&'+$('numero_pedido').serialize(),
                onComplete: function(respuesta)
                {
                    try {
                        datos = respuesta.responseText.evalJSON(true);
                        i=0;
			//Crea un array nuevo para los artículos
                        //sobreescribiendo el anterior...
                        articulos=new Array();
			// Quita selección de Pedidos Actual...
                      	/*$('id_pedido').value=-1;
                        $('numero_pedido').value='';
                        $('numero_pedido').style.background='';
                        $('error_pedido_img').style.display='none';
                        $('enlazar_pedido_img').style.display='none';
                        $('nuevo_pedido_img').style.display='none';*/
                        // Redibuja la tabla de artículos
    			redibujar_tabla();
                        /*while(i<articulos.length) {
                            articulos[i][6]=0;
                            if(articulos[i][1]==0) {
                                quitar_art(articulos[i][0]);
                                // i=0;
                            }  i++; 
                        }*/
                        if(datos[0]==true && datos[1]==false)
                        {
                            $('error_pedido_img').style.display='';
                            $('enlazar_pedido_img').style.display='none';
                            $('numero_pedido').style.background='yellow';
                            $('id_pedido').value=-1;
                            $('id_log').value=-1;
                            redibujar_tabla();
                            $('cargar_pedido_img').style.display='none';
                            //$('boton_mover').style.display='none';
                            $('boton_aceptar').style.display='none';
                            return;
                        }
                        if(datos[0]==false && datos[1]==false)
                        {
                            $('error_pedido_img').style.display='';
                            $('enlazar_pedido_img').style.display='none';
                            $('numero_pedido').style.background='yellow';
                            $('id_pedido').value=-1;
                            $('id_log').value=-1;
                            redibujar_tabla();
                            $('cargar_pedido_img').style.display='none';
                            //$('boton_mover').style.display='none';
                            $('boton_aceptar').style.display='none';
                            return;
                        }
                        $('error_pedido_img').style.display='none';
                        $('enlazar_pedido_img').style.display='';
                        $('numero_pedido').style.background='';
                        $('id_pedido').value=(datos[3]*1);
                        $('id_log').value=(datos[4]*1);
                        cargar_pedido(datos[1]);
                        chequear_todo(false);
                        $('cargar_pedido_img').style.display='none';
                        //$('boton_mover').style.display='';
                        $('boton_aceptar').style.display='';
                    }
                    catch (err)
                    {
                        alert('ERROR:\n\n'+err);
                    }
                }
            });
        }
    }
    
    cargar_pedido = function(_cadena_articulos)
    {
        for(i=0;i<_cadena_articulos.length;i++)
        {
            _detalle_articulo = _cadena_articulos[i];
            encontrado=false;
            for(u=0;u<articulos.length;u++)
            {
                if(articulos[u][0]==_detalle_articulo[3])
                {
                    articulos[u][6]=_detalle_articulo[2];
                    encontrado=true;
                    break;
                }
            }
            if(!encontrado)
            {
                num=articulos.length;
                articulos[num] = new Array(7);
                articulos[num][0]=_detalle_articulo[3];
                articulos[num][1]=0;
                articulos[num][2]=_detalle_articulo[0];
                articulos[num][3]=_detalle_articulo[0];
                articulos[num][4]=_detalle_articulo[1];
                articulos[num][5]=_detalle_articulo[4];
                articulos[num][6]=_detalle_articulo[2];
                articulos[num][7]=null;
            }
        }
        redibujar_tabla();
    }

    quitar_art = function(numero, tipo_art, fecha_art)
    {
        // Borra y redibuja para borrado del usuario.
        if(tipo_art==0)
        {
            articulos_tmp=new Array();
            for(i=0;i<articulos.length;i++)
            {
                if(tipo_tabla_arts==0)
                {
                    if(articulos[i][0]!=numero)
                    {
                        articulos_tmp[articulos_tmp.length]=articulos[i];
                    }
                }
                else
                {
                    if(!(articulos[i][0]==numero && articulos[i][7]==fecha_art))
                    {
                        articulos_tmp[articulos_tmp.length]=articulos[i];
                    }
                }
            }
            articulos=articulos_tmp;
        }
        else
        {
            for(i=0;i<articulos.length;i++)
            {
                if(articulos[i][0]==numero)
                {
                    articulos[i][1]=0;
                    break;
                }
            }
        }
        redibujar_tabla();
    }
    
    limpiar_art = function()
    {
        // Crea un array nuevo para los artículos
	// sobreescribiendo el anterior...
	articulos=new Array();
	// Quita selección de Pedidos Actual...
        $('id_pedido').value=-1;
        $('numero_pedido').value='';
	$('numero_pedido').style.background='';
	$('error_pedido_img').style.display='none';
        $('enlazar_pedido_img').style.display='none';
        $('nuevo_pedido_img').style.display='none';
        // Redibuja la tabla de artículos
        redibujar_tabla();
    }
    seleccionar_articulo = function(art_id, art_codigo, artc_codigo,art_glosa, art_cantidad, art_stock, art_fecha_venc)
    {
        encontrado=false;
        for(i=0;i<articulos.length;i++)
        {
            if(articulos[i][0]==art_id && articulos[i][7]==art_fecha_venc)
            {
                temp_cant=articulos[i][1]+art_cantidad;
                articulos[i][1]+=art_cantidad;
                encontrado=true;
                redibujar_tabla();
            }
        }
        if(!encontrado)
        {
            alert('Art&iacute;culo no figura en n&oacute;mina de env&iacute;o.'.unescapeHTML());          
        }
    }
    redibujar_tabla = function()
    {
        table_html='<table style="width:100%;"><tr class="tabla_header"><td style="width:100px;" rowspan=2><b>Cod. Interno</td><td style="width:200px;" rowspan=2><b>Glosa</td><td colspan=3><b>Cantidades</b></td><td rowspan=2><b>Aceptar</b></td></tr><tr class="tabla_header"><td><b>Solicitada</b></td><td><b>Enviada</b></td><td><b>Diferencia</b></td></tr>';
        for(i=0;i<articulos.length;i++)
        {
            if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
            diferencia=(articulos[i][5]-articulos[i][6]);
            if(diferencia<0)
            {
                _text_color='color: red;';
                signo='';
            }
            else if (diferencia>0)
            { 
                _text_color='';
                signo='+';
            }
            else
            {
                _text_color='';
                signo='';
            }
            if(articulos[i][1]==articulos[i][5])
            {
                imagen='iconos/tick.png';
            }
            else
            {
                imagen='iconos/cross.png';
            }
            table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'"><input type="hidden" id="id_art_'+i+'" name="id_art_'+i+'" value='+articulos[i][0]+'><input type="hidden" id="cant_art_'+i+'" name="cant_art_'+i+'" value='+articulos[i][1]+'><td style="text-align:right;">'+articulos[i][2]+'</td><td>'+articulos[i][4]+'</td><td style="text-align:right;">'+number_format(articulos[i][6],1)+'</td><td style="text-align:right;"><b>'+number_format(articulos[i][5],1)+'</b></td><td style="text-align:right;'+_text_color+'">'+signo+number_format(diferencia,1)+'</td><td><center><input type="checkbox" id="acepta_art_'+i+'" name="acepta_art_'+i+'" checked></center></td></tr>';
        }
        table_html+='</table>';
        $('detalle_pedido').innerHTML=table_html;
    }

    var bloquear_ingreso=false;
    aceptar_pedido = function (accion)
    {
        if(bloquear_ingreso)
            return;
        if(accion==0)
        {
            _act='aceptar';
            _msg='Recepci&oacute;n aceptada exitosamente.'.unescapeHTML();
        }
        else
        {
            _act='rechazar';
            _msg='Recepci&oacute;n rechazada exitosamente.'.unescapeHTML();
        }
        var acepta=false;
        for(i=0;i<articulos.length;i++)
        {
            if($("acepta_art_"+i).checked)
            {
                acepta=true;
		break;
            }
        }
        if(!acepta)
        {
            if(!confirm("============ ATENCI&Oacute;N: ============\n\n&iquest;Est&aacute; seguro que desea RECHAZAR COMPLETAMENTE el pedido?\n\nNo hay opciones para deshacer.".unescapeHTML()))
            {
                return;
            }
	}
        bloquear_ingreso=true;
        var myAjax10 = new Ajax.Request('abastecimiento/recepcion_pedido/sql.php',
        {
            method: 'get',
            parameters: 'accion='+_act+'&'+$('datos_pedido').serialize()+'&cantidad='+articulos.length,
            onComplete: function(respuesta)
            {
                bloquear_ingreso=false;
                resp=respuesta.responseText.evalJSON(true);
                if(resp[0]=='OK')
                {  
                    alert(_msg);
                    /*recep = window.open('abastecimiento/recepcion_pedido/generar_pdf.php?id_log='+resp[1], 'ver_recepcion','scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+'resizable=no, width=800, height=715');*/
                    recep = window.open('abastecimiento/recepcion_pedido/ver_recepcion.php?id_log='+resp[1], 'ver_recepcion','scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+'resizable=no, width=800, height=715');
                    recep.focus();
                    cambiar_pagina('abastecimiento/recepcion_pedido/form.php');
                }
                else
                {
                    alert('ERROR:\n\n'+respuesta.responseText);
                }
            }
        });
    }
  
    ver_recepcion = function(log_id)
    {
        l=(screen.availWidth/2)-250;
	t=(screen.availHeight/2)-200;
	win = window.open('abastecimiento/recepcion_pedido/ver_recepcion.php?id_log='+log_id, 'ver_recepcion','scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+'resizable=no, width=800, height=715');
    }
</script>
<center>
    <table>
        <tr>
            <td>
                <div class='sub-content'>
                    <div class='sub-content'>
                        <img src='iconos/page_gear.png'>
                        <b>Recepci&oacute;n de Art&iacute;culos</b>
                    </div>
                    <form id='datos_pedido' name='datos_pedido' onSubmit='return false;'>
                        <table style='width: 650px;'>
                            <tr>
                                <td>
                                    <div class='sub-content'>
                                        <div class='sub-content'>
                                            <img src='iconos/page_white_link.png'> <b>Seleccionar Pedido</b>
                                        </div>
                                        <table>
                                            <tr>
                                                <td style='text-align: right;'>Ubicaci&oacute;n:</td>
                                                <td>
                                                    <select name='bodega_origen' id='bodega_origen' style='font-size:16px; color:red; background-color:white; border:2px solid black;'>
                                                        <?php echo $bodegashtml; echo $servicioshtml; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style='text-align: right;'>Nro. de Pedido:</td>
                                                <td>
                                                    <input type='hidden' id='id_pedido' name='id_pedido' value=-1>
                                                    <input type='hidden' id='id_log' name='id_log' value=-1>
                                                    <input type='text' style='text-align: right;' onChange='descargar_pedido();' id='numero_pedido' name='numero_pedido' size=8>
                                                    <img src='iconos/zoom_in.png' style='cursor: pointer;' onClick='listar_pedidos();'>
                                                    <img src='iconos/page_white_link.png' id='enlazar_pedido_img' name='enlazar_pedido_img' style='display: none;' alt='Enlazado con Pedido...' title='Enlazado con Pedido...'>
                                                    <img src='iconos/page_white_error.png' id='error_pedido_img' name='error_pedido_img' style='display: none;' alt='N&uacute;mero de Pedido no puede ser utilizado...' title='N&uacute;mero de Pedido no puede ser utilizado...'>
                                                    <img src='imagenes/ajax-loader1.gif' id='cargar_pedido_img' name='cargar_pedido_img' style='display: none;'>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div style='display: none;' id='boton_mover'>
                                        <center>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <div class='boton'>
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <img src='iconos/add.png'>
                                                                    </td>
                                                                    <td>
                                                                        <a href='#' onClick='buscar_codigos_barra($("bodega_origen").value,seleccionar_articulo,0, "buscar_arts", "bodega_origen")'></a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class='boton'>
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <img src="iconos/printer.png">
                                                                    </td>
                                                                    <td>
                                                                        <a href='#' onClick='imprimirHTML($("detalle_pedido").innerHTML);'> Imprimir Pedido...</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </center>
                                    </div>
                                    <div class='sub-content'>
                                        <div class='sub-content'>
                                            <img src='iconos/page_white_magnify.png'>
                                            <b>Detalle del Pedido</b> ( <input type="checkbox" id="check_all" name="check_all" onClick="chequear_todo(this.checked);"> Aceptar todos los Art&iacute;culos )
                                        </div>
                                        <div class='sub-content2' id='detalle_pedido'>
                                            <table style="width:100%;">
                                                <tr class="tabla_header">
                                                    <td style="width:100px;" rowspan=2><b>Cod. Interno</td>
                                                    <td style="width:200px;" rowspan=2><b>Glosa</td>
                                                    <td colspan=4><b>Cantidades</b></td>
                                                    <td rowspan=2><b>Aceptar</b></td>
                                                </tr>
                                                <tr class="tabla_header">
                                                    <td><b>Solicitada</b></td>
                                                    <td><b>Enviada</b></td>
                                                    <td><b>Diferencia</b></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class='sub-content' style='display: none;' id='boton_aceptar'>
                                        <center>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <div class='boton'>
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <img src='iconos/accept.png'>
                                                                    </td>
                                                                    <td>
                                                                        <a href='#' onClick='aceptar_pedido(0);'>Confirmar Pedido...</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                    <!--
                                                    <td>
                                                        <div class='boton'>
                                                            <table>
                                                                <tr>
                                                                    <td>
                                                                        <img src="iconos/delete.png">
                                                                    </td>
                                                                    <td>
                                                                        <a href='#' onClick='aceptar_pedido(1);'>Rechazar Pedido...</a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </td>
                                                    -->
                                                </tr>
                                            </table>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </td>
        </tr>
    </table>
</center>