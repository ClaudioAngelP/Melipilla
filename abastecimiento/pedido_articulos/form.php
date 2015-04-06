<?php
    date_default_timezone_set("America/Santiago");
    require_once("../../conectar_db.php");
    error_reporting(E_ALL);
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $hora = time(); 
    $hora_real=date ("H:i", $hora);
    $hora_actual = strtotime($hora_real);
    $hora_bloqueo = strtotime( "14:00" );
    $bloqueo=0;
    if( $hora_actual > $hora_bloqueo )
    {
        $bloqueo=1;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(8),')', 'ORDER BY bod_glosa');
    if(!_cax(65))
        $bodegasdesthtml = desplegar_opciones("bodega", "bod_id, bod_glosa, bod_proveedores, bod_despacho",'1','bod_proveedores OR bod_despacho','ORDER BY bod_glosa'); 
    else
        $bodegasdesthtml = desplegar_opciones("bodega", "bod_id, bod_glosa, bod_proveedores, bod_despacho",'-1','bod_id IN ('._cav(65).')','ORDER BY bod_glosa'); 
  
    $servs="'".str_replace(',','\',\'',_cav2(8))."'";
	
    $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto AND centro_ruta IN (".$servs.")
    ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
  
    $controlhtml = desplegar_opciones("receta_tipo_talonario","tipotalonario_id, tipotalonario_medicamento_clase",'-1','true','ORDER BY tipotalonario_id');
    
?>	
<script>
    var lista=new Array();
    var ids=new Array();	  
    buscar_reposicion = function()
    {  
        lista=new Array();
        ids=new Array();	
        params=$('origen').serialize()+'&'+$('destino').serialize();
        var myAjax=new Ajax.Request('abastecimiento/pedido_articulos/listado_reposicion.php',
        {
            method:'get',
            parameters:params,
            onComplete:function(r) 
            {
                var datos=r.responseText.evalJSON(true);
                if(datos!=',')
                {
                    lista=datos[0];
                    //alert(lista.toSource());
                    ids=datos[1].split(',');
                    redibujar_detalle();
                    //alert('Listado de art&iacute;culos pendientes de despacho en pedidos anteriores.\nSi desea olvidar lo pedido indique con un 0 (CERO).'.unescapeHTML());
                    alert('Usted registra pedidos pendientes de despacho.\nA continuaci&oacute;n se solicita revisar los &iacute;tems y cantidades.\nSi desea eliminar un &iacute;tem pendiente solo indique 0 (cero) en "Cantidad".'.unescapeHTML());
                }
                else
                {
                    var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>#</td><td>C&oacute;digo</td><td>Descripci&oacute;n</td><td>Forma</td><td>Pendiente</td><td>Cantidad</td></tr></table>';
                    $('lista_productos').innerHTML=html;
                }
            }
        });
    }
  
    agregar_detalle=function()
    {
        if($('art_id').value==0 || $('art_id').value=='')
        {
            alert('Debe seleccionar el art&iacute;culo.'.unescapeHTML()); return;
        }
        /*
        if($('art_stock').value==0 || $('art_stock').value=='' || $('art_stock').value=='0')
        {
            alert('El art&iacute;culo seleccionado no presenta STOCK en la bodega de destino seleccionada.'.unescapeHTML()); return;
        }
        if($('art_stock').value<=0)
        {
            alert('El art&iacute;culo seleccionado no presenta STOCK en la bodega de destino seleccionada.'.unescapeHTML()); return;
        }
        */
        if($('cant').value=='' || ($('cant').value*1)!=$('cant').value)
        {
            alert('Debe ingresar una cantidad.'); $('cant').focus(); return;
        }
		 
        for(var y=0;y<ids.length;y++)
        {
            if(ids[y]==$('art_id').value)
            {
                alert('El art&iacute;culo ya est&aacute; ingresado en la lista'.unescapeHTML());
                $('art_id').value='';
                $('cant').value='';
                $('art_nombre').innerHTML='';
                $('codigo').value='';
                $('codigo').focus();
                return;
            }
        }
        
        for(var x=0;x<ids.length;x++)
        {
            if($('cant_'+ids[x]))
            lista[ids[x]].cantidad=$('cant_'+ids[x]).value;
        }

        ids.push([$('art_id').value]);
		 
        var idx=$('art_id').value;	 
        lista[idx]=new Object();
        lista[idx].art_id=$('art_id').value;
        lista[idx].art_codigo=$('codigo').value;
        lista[idx].art_glosa=$('art_nombre').innerHTML;
        lista[idx].art_forma=$('art_forma').innerHTML;
        lista[idx].pendiente=0;
        lista[idx].cantidad=$('cant').value;
        lista[idx].pedidos='';
		 		 
        $('art_id').value='';
        $('codigo').value='';
        $('art_nombre').innerHTML='';
        $('art_forma').innerHTML='';
        $('cant').value='';
        redibujar_detalle();
        $('codigo').focus();		  
    }

    quitar_detalle=function(id)
    {
        ids=ids.without(ids[id]);
        redibujar_detalle();
    }

    redibujar_detalle=function()
    {
        var i=0;
        var cont=0;
        //var i=ids.length;
        var idshtml='';
        var html='<table style="width:100%;font-size:11px;"><tr class="tabla_header"><td>#</td><td>C&oacute;digo</td><td>Descripci&oacute;n</td><td>Forma</td><td>Pendiente</td><td>Cantidad</td><td>&nbsp;</td></tr>';    
        if(ids.length>0)
        {
            //while(i<ids.length){	
            for(i=(ids.length-1);i>(-1);i--)
            {
                //alert(lista[ids[i]].toSource());
                cont++;
                clase=(i%2==0)?'tabla_fila':'tabla_fila2';
                html+='<tr class="'+clase+'"><td style="text-align:right;font-weight:bold;"><input type="hidden" name="art_pedidos_'+ids[i]+'" id="art_pedidos_'+ids[i]+'" value="'+lista[ids[i]].pedidos+'" >'+(cont)+'</td>';
                html+='<td style="text-align:right;font-weight:bold;">'+lista[ids[i]].art_codigo+'</td>';
                html+='<td>'+lista[ids[i]].art_glosa+'</td>';
                html+='<td>'+lista[ids[i]].art_forma+'</td>';
                html+='<td style="text-align:right;">'+lista[ids[i]].pendiente+'</td>';
                html+='<td><center><input style="text-align:right;" name="cant_'+ids[i]+'" id="cant_'+ids[i]+'" value="'+lista[ids[i]].cantidad+'" size=6></center></td>';
                html+='<td><center><img src=\'iconos/delete.png\' style=\'cursor:pointer;\' onClick=\'quitar_detalle('+i+');\' /></center></td></tr>';
                idshtml+=ids[i]+',';
	 	//i++;
            }
            html+='<tr><td><input type="hidden" name="ids" id="ids" value="'+idshtml+'"</td></tr>';
        }
        html+='</table>' 
        $('lista_productos').innerHTML=html;   	
    }

    abrir_pedido = function (pedido_numero)
    {
        l=(screen.availWidth/2)-250;
        t=(screen.availHeight/2)-200;
        win = window.open('visualizar.php?pedido_nro='+pedido_numero, 'ver_pedido',
        'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
        'resizable=no, width=500, height=445');             
        win.focus();
    }
  
    verifica_tabla = function()
    {
        if($('destino').value=='-1')
        {
            alert('Debe seleccionar bodega de destino.'.unescapeHTML());
            return;
        }
        
        if(($('bloqueo_pedido').value*1)==1)
        {
            var funcionario=($('funcionario').value*1);
            if(funcionario!=6332 && funcionario!=5687 && funcionario!=6306 && funcionario!=7 && funcionario!=5784)
            {
                var bloquea_bodega=false;
                var bodega_glosa='';
                if($('destino').value=='25')
                {
                    bodega_glosa="Bodega Abastecimiento";
                    bloquea_bodega=true;
                }
                if($('destino').value=='24')
                {
                    bodega_glosa="Bodega Farmacia";
                    bloquea_bodega=true;
                }
		

                if(bloquea_bodega)
                {
                    alert("No se pueden realizar pedidos despu&eacute;s de las 14:00 Horas.-\nSeg&uacute;n lo solicitado por el o la Encarda de la Bodega de: "+bodega_glosa+''.unescapeHTML());
                    return;
                }
            }
        }
	
        if($('art_id').value!='')
        {
            alert('Debe ingresar el art&iacute;culo seleccionado.'.unescapeHTML());
            return;
        }

        if(ids.length<1)
        {
            alert('Debe seleccionar art&iacute;culos para pedir.'.unescapeHTML());
            return;
        }
  
        if(!confirm('Est&aacute; seguro que desea continuar?.'.unescapeHTML()))
            return;

        params=$('pedido').serialize();
   
        var myAjax=new Ajax.Request('abastecimiento/pedido_articulos/generar_reposicion.php',
        {
            method:'post',
            parameters:params,
            onComplete:function(r)
            {
                var dato=r.responseText.evalJSON(true);
                if(dato[0])
                {
                    limpiar_form();					
                    abrir_pedido(dato[1]);
                    cambiar_pagina('abastecimiento/pedido_articulos/form.php', function() { alert('Pedido ingresado exitosamente.'); } );
                }
                else
                {
                    alert('ERROR:\n\n'+r.responseText);
                }
            }
        });
    }
	
    limpiar_form = function()
    {
        lista=new Array();
        ids=new Array();	 		
        $('art_id').value='';
        $('codigo').value='';
        $('art_nombre').innerHTML='';
        $('art_forma').innerHTML='';
        $('cant').value='';
        redibujar_detalle();
        $('codigo').focus();
    }
        
    buscar_articulo=function(art_id)
    {
            
            
    }
    
    
    
</script>
<center>
    <table>
        <tr>
            <td style='width:850px;'>
                <div class='sub-content'>
                    <div class='sub-content'>
                        <img src='iconos/page_refresh.png'> <b>Pedido de Art&iacute;culos</b>
                    </div>
                    <form name='pedido' id='pedido'>
                        <table>
                            <tr>
                                <td style='text-align: right;'>Origen:</td>
                                <td>
                                    <select name='origen' id='origen' ><!--onChange='buscar_reposicion();'-->
                                        <?php echo $bodegashtml; echo $servicioshtml; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td style='text-align: right;'>Destino:</td>
                                <td>
                                    <select name='destino' id='destino' ><!--onChange='buscar_reposicion();'-->
                                        <option value='-1' SELECTED>(Seleccionar...)</option>
                                        <?php echo $bodegasdesthtml; ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div class='sub-content' id='listado_detalle'>
                            <div class='sub-content'>
                                <img src='iconos/script.png'>
                                <b>Listado de Art&iacute;culos</b>
                                <br/>
                                <br/>
                                <img src='iconos/add.png'>
                                <input type='text' id='codigo' name='codigo' onFocus='if($("destino").value=="-1"){ alert("Debe seleccionar bodega de Destino."); $("destino").focus();}' onKeyUp='if(event.which==13){$("cant").focus();}'>
                                <input type='hidden' name='art_id' id='art_id' >
                                <input type='hidden' name='art_stock' id='art_stock' >
                                <input type='hidden' name='bloqueo_pedido' id='bloqueo_pedido' value="<?php echo $bloqueo;?>">
                                <input type='hidden' name='funcionario' id='funcionario' value="<?php echo ($_SESSION['sgh_usuario_id']*1);?>">
                                <span id='art_nombre' name='art_nombre' ></span>
                                <br/>
                                &nbsp;&nbsp;&nbsp;&nbsp;Cantidad:&nbsp;
                                <input type='text' name='cant' id='cant' size=6 onKeyUp='if(event.which==13){agregar_detalle();}'>
                                <span id='art_forma' name='art_forma'></span>  
                            </div>
                            <div class='sub-content2' id='lista_productos' style='height: 250px; overflow:auto;'>
                                <?php
                                if($bloqueo)
                                {
                                ?>
                                <table style="width: 100%">
                                    <tr>
                                        <td bgcolor="#FF3333">El envi&oacute; de pedidos se encuentra bloqueado a partir de las 14:00 , para las bodegas (Bodega Farmacia y Bodega de Abastecimiento).- </td>
                                    </tr>
                                </table>
                                <?php
                                }
                                else
                                {   
                                ?>
                                    (No ha seleccionado art&iacute;culos...)
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <center>
                            <table>
                                <tr>
                                    <td>
                                        <div class='boton'>
                                            <table>
                                                <tr>
                                                    <td>
                                                        <img src='iconos/script_go.png'>
                                                    </td>
                                                    <td>
                                                        <a href='#' onClick='verifica_tabla();' > Generar Pedido...</a>
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
            </td>
        </tr>
    </table>
</center>
<script>
    seleccionar_articulo = function(d)
    {
        params=$('origen').serialize()+'&'+$('destino').serialize()+'&art_id='+d[6];
        var myAjax=new Ajax.Request('abastecimiento/pedido_articulos/buscar_art.php',
        {
            method:'get',
            parameters:params,
            onComplete:function(r) 
            {
                var datos=r.responseText.evalJSON(true);
                if(datos[0]!=false)
                {
                    alert('Usted registra pedidos pendientes de despacho, de '+d[2]+', de todas formas puede realizar un nuevo pedido con este art&iacute;culo.'.unescapeHTML());
                }
                $('codigo').value=d[1];
                $('art_nombre').innerHTML=d[2];
                $('art_stock').value=d[4]*1;
                $('art_id').value=d[6];
                $('art_forma').innerHTML=d[3];
            }
        });
    }
      
      autocompletar_articulo = new AutoComplete(
      'codigo', 
      'autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts_stock&'+$('codigo').serialize()+'&bodega_id='+($('destino').value*1)
        }
      }, 'autocomplete', 500, 250, 250, 1, 3, seleccionar_articulo);
	

	//buscar_reposicion();
</script>
  
