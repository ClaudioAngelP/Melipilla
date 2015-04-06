<?php
    require_once("../../conectar_db.php");
    $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(3),')','ORDER BY bod_glosa'); 
    $servs="'".str_replace(',','\',\'',_cav2(3))."'";
    $servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND centro_medica AND centro_ruta IN (".$servs.") ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 
    $bodegas2html = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','1=1','ORDER BY bod_glosa');
    $institucionhtml = desplegar_opciones("institucion_solicita", "instsol_id, instsol_desc",'','true','ORDER BY instsol_desc'); 
    $centroshtml=desplegar_opciones("centro_costo", "centro_ruta, centro_nombre",'',"length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=2",'ORDER BY centro_ruta,centro_nombre');
    $lista_servicios=cargar_registros_obj("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE length(regexp_replace(centro_ruta, '[^.]', '', 'g'))>=3 ORDER BY centro_ruta,centro_nombre;",true);
    //print_r($lista_servicios);
    //die();
        
	
?>
	
	<script type='text/javascript'>
	  cont=1;
	  bloquear_ingreso=false;
	
	  bloquear_descarga=false;
	
	  tipo_tabla_arts=0;
	  articulos=new Array();
		
		abrir_busqueda_vencidos = function() {
		
		var win = new Window("busca_vencidos", {className: "alphacube", 
                          top:40, left:0, 
                          width: 600, height: 250, 
                          title: '<img src="iconos/date_error.png"> Seleccionar Lotes Vencidos',
                          minWidth: 600, minHeight: 250,
                          maximizable: false, minimizable: false, 
                          wiredDrag: true, resizable: false });
                          
    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
    
    win.setAjaxContent('abastecimiento/movimiento_articulos/listar_lotes_vencidos.php', 
			{
				method: 'get', 
				evalScripts: true,
        parameters: $('bodega_origen').serialize()
	
			});
			
		$("busca_vencidos").win_obj=win;
  
    win.setDestroyOnClose();
    win.showCenter();
    win.show();
			
		}
		
		abrir_busqueda_balance = function() {
		
		  params=$('bodega_origen').serialize()+'&'+$('institucion_destino').serialize();
    
      top=Math.round(screen.height/2)-225;
      left=Math.round(screen.width/2)-300;
      
      new_win = 
      window.open('abastecimiento/movimiento_articulos/balance_prestamos.php?'+
      params,
      'win_balance', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=600, height=450, '+
      'top='+top+', left='+left);
      
      new_win.focus();

		}
		
		buscar_articulos_presta = function() {
      if($('tipo_prestamo').value==1)
        buscar_codigos_barra($("bodega_origen").value,seleccionar_articulo,0, "buscar_arts", "bodega_origen");
      else
        buscar_codigos_barra($("bodega_origen").value,seleccionar_articulo,1, "buscar_arts", "bodega_origen");
      
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
            var myAjax1 = new Ajax.Request('abastecimiento/movimiento_articulos/bajar_pedidos.php',
            {
                method: 'get',
                parameters: $('bodega_origen').serialize()+'&'+$('numero_pedido').serialize(),
                onComplete: function(respuesta)
                {
                    datos = respuesta.responseText.evalJSON(true);
                    
                    i=0;
                    remover_pedido();
                    
                    if(datos[0]==true && datos[1]==false)
                    {
                        $('error_pedido_img').style.display='none';
                        $('enlazar_pedido_img').style.display='none';
                        $('numero_pedido').value='';
                        $('chk_pedido_nuevo').checked=true;
                        $('numero_pedido').style.background='';
                        $('id_pedido').value=0;
                        $('bodega_destino').disabled=false;
                        redibujar_tabla();
                        $('cargar_pedido_img').style.display='none';
                        return;
                    }
                    if(datos[0]==false && datos[1]==false)
                    {
                        $('error_pedido_img').style.display='';
                        $('enlazar_pedido_img').style.display='none';
                        $('numero_pedido').style.background='red';
                        $('chk_pedido_nuevo').checked=false;
                        $('id_pedido').value=-1;
                        $('bodega_destino').disabled=false;
                        redibujar_tabla();
                        $('cargar_pedido_img').style.display='none';
                        return;
                    }
                    try
                    {
                        var __nro_pedido_tmp = $('numero_pedido').value;
                        if(datos[2]*1!=0)
                        {
                            $('tipo_movimiento').value=0;
                        }
                        else
                        {
                            $('tipo_movimiento').value=7;
                        }
                        corrige_campos();
                        $('numero_pedido').value = __nro_pedido_tmp;
                        if(datos[2]*1!=0)
                        {
                            $('bodega_destino').value=(datos[2]*1);
                            $('bodega_destino').disabled=true;
                        }
                        else
                        {
                            var tmp = datos[4].split('.');
                            var ccosto='.'+tmp[1]+'.'+tmp[2];
                            $('centro_costo').value=ccosto;
                            cargar_servicios();
                            $('centro_servicio').value=datos[4];
                            $('centro_costo').disabled=true;
                            $('centro_servicio').disabled=true;
                        }
                        $('error_pedido_img').style.display='none';
                        $('enlazar_pedido_img').style.display='';
                        $('numero_pedido').style.background='';
                        $('chk_pedido_nuevo').checked=false;
                        $('id_pedido').value=(datos[3]*1);
                        cargar_pedido(datos[1]);
                        $('cargar_pedido_img').style.display='none';
                    }
                    catch (err)
                    {
                        alert('ERROR:\n\n'+err);
                    }
                }
            });
        }
    }
    
    cargar_pedido = function(_cadena_articulos) {
    
      for(i=0;i<_cadena_articulos.length;i++) {
      
        _detalle_articulo = _cadena_articulos[i];
        
        encontrado=false;
        
        for(u=0;u<articulos.length;u++) {
        
          if(articulos[u][0]==_detalle_articulo[3]) {
            articulos[u][6]=_detalle_articulo[2];
            encontrado=true;
            break;
          }
          
        }
        
        if(!encontrado) {
        
          num=articulos.length;
          articulos[num] = new Array(7);
          articulos[num][0]=_detalle_articulo[3];
          articulos[num][1]=_detalle_articulo[2]*1>_detalle_articulo[4]*1?_detalle_articulo[4]:_detalle_articulo[2];
          articulos[num][2]=_detalle_articulo[0];
          articulos[num][3]=_detalle_articulo[0];
          articulos[num][4]=_detalle_articulo[1];
          articulos[num][5]=_detalle_articulo[4];
          articulos[num][6]=_detalle_articulo[2];
          articulos[num][7]=null;
          
        }
      
      }
      
      redibujar_tabla();
    	$('boton_agregar').style.display='';
    }
    
    remover_pedido = function()
    {
        i=0;
        while(i<articulos.length)
        {
            articulos[i][6]=0;
            if(articulos[i][1]==0)
            {
                borrar_art(articulos[i][0]);
                i=0;
            }
            else
            {
                i++;
            }
        }
        $('boton_agregar').style.display='none';
    }
    
    forzar_pedido_nuevo=function() {
    
      if($('chk_pedido_nuevo').checked && $('id_pedido').value>0) {
        remover_pedido();
        $('error_pedido_img').style.display='none';
        $('enlazar_pedido_img').style.display='none';
        $('numero_pedido').value='';
        $('id_pedido').value=0;
        redibujar_tabla();
      }
    
    }
    
    borrar_art = function(numero) {
    
      // Borra rapido para procesos internos.
      articulos_tmp=new Array();
		
			for(i=0;i<articulos.length;i++) {
        if(articulos[i][0]!=numero) {
          articulos_tmp[articulos_tmp.length]=articulos[i];
        }
      }
        
      articulos=articulos_tmp;
      
    }
		
		quitar_art = function(numero, tipo_art, fecha_art) {
		
		  // Borra y redibuja para borrado del usuario.
		
      if(tipo_art==0) {

        articulos_tmp=new Array();
		
			  for(i=0;i<articulos.length;i++) {
          if(tipo_tabla_arts==0) {
          
            if(articulos[i][0]!=numero) {
              articulos_tmp[articulos_tmp.length]=articulos[i];
            }
            
          } else {
          
            if(!(articulos[i][0]==numero && articulos[i][7]==fecha_art)) {
              articulos_tmp[articulos_tmp.length]=articulos[i];
            }
          
          }
        }
        
        articulos=articulos_tmp;
      
      } else {
      
        for(i=0;i<articulos.length;i++) {
          if(articulos[i][0]==numero) {
            articulos[i][1]=0;
            break;
          }
        }
        
      }
      
      redibujar_tabla();
			
			
		}
		
		limpiar_art = function() {
		
		  // Crea un array nuevo para los art�culos
		  // sobreescribiendo el anterior...
		
			articulos=new Array();
			
			// Quita selecci�n de Pedidos Actual...
      
      $('id_pedido').value=0;
      $('numero_pedido').value='';
		  $('numero_pedido').style.background='';
		  $('error_pedido_img').style.display='none';
      $('enlazar_pedido_img').style.display='none';
      $('chk_pedido_nuevo').checked=true;
      $('bodega_destino').disabled=false;
      
      // Redibuja la tabla de art�culos
      
			redibujar_tabla();
			//$('boton_agregar').style.display='none';
		}
		
		seleccionar_articulo = function(art_id, art_codigo, artc_codigo, 
                            art_glosa, art_cantidad, art_stock, art_fecha_venc,
                            art_punit_med)
    {

        encontrado=false;

        for(i=0;i<articulos.length;i++) {
          if(articulos[i][0]==art_id && articulos[i][7]==art_fecha_venc) {
            temp_cant=articulos[i][1]+art_cantidad;
            
            if(tipo_tabla_arts==0 && temp_cant>art_stock) {
              alert('No se puede mover m&aacute;s stock que el disponible.'.unescapeHTML());
              return;
            }
            
            articulos[i][1]+=art_cantidad;
            articulos[i][2]=art_codigo;
            articulos[i][3]=artc_codigo;
            articulos[i][4]=art_glosa;
            articulos[i][5]=art_stock;
            
            encontrado=true;
            
          }
        }
        
        if(!encontrado) {
        
          if(tipo_tabla_arts==0 && art_cantidad>art_stock) {
            alert('No se puede mover m&aacute;s stock que el disponible.'.unescapeHTML());
            return;
          }
          
          if(comprobar_talonario(art_id)) {
            if($('tipo_movimiento').value!=0 && $('tipo_movimiento').value!=7) {
              alert('Los talonarios de recetas solo pueden ser recibidos por conducto regular en Recepci&oacute;n de Art&iacute;culos o Inicio por Control del Sistema.'.unescapeHTML()); 
              return;
            }
          }
          
          
          num=articulos.length;
          articulos[num] = new Array(8);
          articulos[num][0]=art_id;
          articulos[num][1]=art_cantidad;
          articulos[num][2]=art_codigo;
          articulos[num][3]=artc_codigo;
          articulos[num][4]=art_glosa;
          articulos[num][5]=art_stock;
          articulos[num][6]=0;
          articulos[num][7]=art_fecha_venc;
          articulos[num][8]='';
          articulos[num][9]=art_punit_med;
          articulos[num][10]=true;
          
        }
		
    redibujar_tabla();


		}
		
		seleccionar_articulo_s = function(art_id, art_codigo, artc_codigo, 
                            art_glosa, art_cantidad, art_stock, art_fecha_venc,
                            art_punit_med)
    {

        encontrado=false;

        for(i=0;i<articulos.length;i++) {
          if(articulos[i][0]==art_id && articulos[i][7]==art_fecha_venc) {
            temp_cant=articulos[i][1]+art_cantidad;
            
            if(tipo_tabla_arts==0 && temp_cant>art_stock) {
              alert('No se puede mover m&aacute;s stock que el disponible.'.unescapeHTML());
              return;
            }
            
            articulos[i][1]+=art_cantidad;
            articulos[i][2]=art_codigo;
            articulos[i][3]=artc_codigo;
            articulos[i][4]=art_glosa;
            articulos[i][5]=art_stock;
            
            encontrado=true;
            
          }
        }
        
        if(!encontrado) {
        
          if(tipo_tabla_arts==0 && art_cantidad>art_stock) {
            alert('No se puede mover m&aacute;s stock que el disponible.'.unescapeHTML());
            return;
          }
          
          if(comprobar_talonario(art_id)) {
            if($('tipo_movimiento').value!=0 && $('tipo_movimiento').value!=7) {
              alert('Los talonarios de recetas solo puedenNaN,0 ser recibidos por conducto regular en Recepci&oacute;n de Art&iacute;culos o Inicio por Control del Sistema.'.unescapeHTML()); 
              return;
            }
          }
          
          
          num=articulos.length;
          articulos[num] = new Array(8);
          articulos[num][0]=art_id;
          articulos[num][1]=art_cantidad;
          articulos[num][2]=art_codigo;
          articulos[num][3]=artc_codigo;
          articulos[num][4]=art_glosa;
          articulos[num][5]=art_stock;
          articulos[num][6]=0;
          articulos[num][7]=art_fecha_venc;
          articulos[num][8]='';
          articulos[num][9]=art_punit_med;
          articulos[num][10]=false;
          
        }
		
    redibujar_tabla();


		}
				
		redibujar_tabla = function() {
		
		  if(tipo_tabla_arts==0) {
		  
		  // SELECCION SIN FECHAS DE VENCIMIENTO
    
      table_html='<table style="width:100%;"><tr class="tabla_header"><td style="width:100px;"><b>Cod. Interno</td><td style="width:200px;"><b>Glosa</td><td><b>Pedido</td><td><b>Cant.</td><td><b>Dif.</td><td><b>Stock</td><td colspan=2 style="width:60px;"><b>Acciones</td></tr>';
    
      for(i=0;i<articulos.length;i++) {
      
        if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        if(articulos[i][6]>0) tipo_art=1; else tipo_art=0;
        
        
        if(articulos[i][1]<articulos[i][6]) 
          _text_color='color: red;';
        else 
          _text_color='';
          
        diferencia = articulos[i][1]-articulos[i][6];
        
        signo = (diferencia>0)?'+':'';
        
        if(comprobar_talonario(articulos[i][0])) {
          if($('tipo_movimiento').value==7 && 
                          funcionario_talonario(articulos[i][0]))
            icono_talonario='<img src="iconos/page_edit.png" style="cursor: pointer;" onClick="sel_talonarios_func('+i+');">';
          else 
            icono_talonario='<img src="iconos/page_edit.png" style="cursor: pointer;" onClick="sel_talonarios('+i+');">';
        } else {
          icono_talonario='';
        }       	
        
        table_html+='<tr class="'+clase+'" style="'+_text_color+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'"><input type="hidden" id="id_art_'+i+'" name="id_art_'+i+'" value='+articulos[i][0]+'><input type="hidden" id="cant_art_'+i+'" name="cant_art_'+i+'" value='+articulos[i][1]+'><td style="text-align:right;">'+articulos[i][2]+'</td><td>'+articulos[i][4]+'</td><td style="text-align:right;">'+number_format(articulos[i][6],1)+'</td><td style="text-align:center;"><input type="text" style="text-align:right;" id="cant_art_'+i+'" name="cant_art_'+i+'" size=5 value="'+articulos[i][1]+'" onKeyUp="this.value=(this.value*1); if(this.value>'+articulos[i][5]+') this.value='+articulos[i][5]+';" /></td><td style="text-align:right;"  onKeyUp="if(event.which==13) {recalcular_cant();}"/>'+signo+''+number_format(diferencia,1)+'</td><td style="text-align:right;">'+number_format(articulos[i][5],1)+'</td><td style="width:30px;"><center>'+icono_talonario+'</center></td><td style="width:30px;"><center><img src="iconos/delete.png" style="cursor: pointer;" onClick="quitar_art('+articulos[i][0]+', '+tipo_art+', null);"></center></td></tr>';
      }
      
      table_html+='</table>';
      
      $('seleccion').innerHTML=table_html;
      
      } else if(tipo_tabla_arts==1) {
      
      // SELECCION CON FECHAS DE VENCIMIENTO
      
      table_html='<table style="width:100%;"><tr class="tabla_header"><td style="width:100px;"><b>Cod. Interno</td><td style="width:200px;"><b>Glosa</td><td><b>Fecha de Venc.</td><td><b>Cant.</td><td colspan=2 style="width:60px;"><b>Acciones</td></tr>';
    
      for(i=0;i<articulos.length;i++) {
      
        if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        if(articulos[i][6]>0) tipo_art=1; else tipo_art=0;
        
        if(articulos[i][7]==null) {
          glosa_fecha='<i>No Perecible</i>';
          valor_fecha='';
          paso_fecha=null;
        } else {
          glosa_fecha=articulos[i][7];
          valor_fecha=glosa_fecha;
          paso_fecha="'"+glosa_fecha+"'";
        }
        
        if(comprobar_talonario(articulos[i][0])) {
          icono_talonario='<img src="iconos/page_edit.png" style="cursor: pointer;" onClick="sel_talonarios('+i+');">';
        } else {
          icono_talonario='';
        }
        
        table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'"><input type="hidden" id="id_art_'+i+'" name="id_art_'+i+'" value='+articulos[i][0]+'><input type="hidden" id="cant_art_'+i+'" name="cant_art_'+i+'" value='+articulos[i][1]+'><input type="hidden" id="fecha_art_'+i+'" name="fecha_art_'+i+'" value="'+valor_fecha+'"><td style="text-align:right;">'+articulos[i][2]+'</td><td>'+articulos[i][4]+'</td><td style="text-align:center;">'+glosa_fecha+'</td><td style="text-align:right;">'+number_format(articulos[i][1],1)+'</td><td style="width:30px;"><center>'+icono_talonario+'</center></td><td style="width:30px;"><center><img src="iconos/delete.png" style="cursor: pointer;" onClick="quitar_art('+articulos[i][0]+', 0, '+paso_fecha+');"></center></td></tr>';
      
      } 

      table_html+='</table>';
      
      $('seleccion').innerHTML=table_html;
    
      } else if(tipo_tabla_arts==2) {
      
      // SELECCION CON Y SIN FECHAS DE VENCIMIENTO (CANJE)
      
      // CON FECHAS - ENTRADA DE ARTICULOS
      
      table_html='<div class="sub-content">Entrada de Art&iacute;culos</div><table style="width:100%;font-weight:bold;"><tr class="tabla_header"><td style="width:100px;">Cod. Interno</td><td style="width:200px;">Glosa</td><td>Fecha de Venc.</td><td>Cant.</td><td>Subtotal</td><td colspan=2 style="width:60px;">Acciones</td></tr>';
    
      for(i=0;i<articulos.length;i++) {
        
        if(!articulos[i][10]) continue;
        
        if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        if(articulos[i][6]>0) tipo_art=1; else tipo_art=0;
        
        if(articulos[i][7]==null) {
          glosa_fecha='<i>No Perecible</i>';
          valor_fecha='';
          paso_fecha=null;
        } else {
          glosa_fecha=articulos[i][7];
          valor_fecha=glosa_fecha;
          paso_fecha="'"+glosa_fecha+"'";
        }
        
        if(comprobar_talonario(articulos[i][0])) {
          icono_talonario='<img src="iconos/page_edit.png" style="cursor: pointer;" onClick="sel_talonarios('+i+');">';
        } else {
          icono_talonario='';
        }
        
        table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'"><input type="hidden" id="id_art_'+i+'" name="id_art_'+i+'" value='+articulos[i][0]+'><input type="hidden" id="cant_art_'+i+'" name="cant_art_'+i+'" value='+articulos[i][1]+'><input type="hidden" id="fecha_art_'+i+'" name="fecha_art_'+i+'" value="'+valor_fecha+'"><td style="text-align:right;">'+articulos[i][2]+'</td><td>'+articulos[i][4]+'</td><td style="text-align:center;">'+glosa_fecha+'</td><td style="text-align:right;">'+articulos[i][1]+'</td><td style="text-align:right;">'+formatoDinero(articulos[i][1]*articulos[i][9])+'</td><td style="width:30px;"><center>'+icono_talonario+'</center></td><td style="width:30px;"><center><img src="iconos/delete.png" style="cursor: pointer;" onClick="quitar_art('+articulos[i][0]+', 0, '+paso_fecha+');"></center></td></tr>';
      
      
      }
      
      table_html+='</table>';
      
      // SIN FECHAS - SALIDA DE ARTICULOS
      
      table_html+='<hr><div class="sub-content">Salida de Art&iacute;culos</div><table style="width:100%;font-weight:bold;"><tr class="tabla_header"><td style="width:100px;">Cod. Interno</td><td style="width:200px;">Glosa</td><td>Stock</td><td>Cant.</td><td>Subtotal</td><td colspan=2 style="width:60px;">Acciones</td></tr>';
    
      for(i=0;i<articulos.length;i++) {

        if(articulos[i][10]) continue;

      
        if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        if(articulos[i][6]>0) tipo_art=1; else tipo_art=0;
        
        if(articulos[i][1]<articulos[i][6]) 
          _text_color='color: red;';
        else 
          _text_color='';
          
        diferencia = articulos[i][1]-articulos[i][6];
        
        signo = (diferencia>0)?'+':'';
        
        if(comprobar_talonario(articulos[i][0])) {
          if($('tipo_movimiento').value==7 && 
                          funcionario_talonario(articulos[i][0]))
            icono_talonario='<img src="iconos/page_edit.png" style="cursor: pointer;" onClick="sel_talonarios_func('+i+');">';
          else 
            icono_talonario='<img src="iconos/page_edit.png" style="cursor: pointer;" onClick="sel_talonarios('+i+');">';
        } else {
          icono_talonario='';
        }
        
        
        table_html+='<tr class="'+clase+'" style="'+_text_color+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'"><input type="hidden" id="id_art_'+i+'" name="id_art_'+i+'" value='+articulos[i][0]+'><input type="hidden" id="cant_art_'+i+'" name="cant_art_'+i+'" value='+articulos[i][1]+'><td style="text-align:right;">'+articulos[i][2]+'</td><td>'+articulos[i][4]+'</td><td style="text-align:right;">'+articulos[i][5]+'</td><td style="text-align:right;">'+articulos[i][1]+'</td><td style="text-align:right;">'+formatoDinero(articulos[i][1]*articulos[i][9])+'</td><td style="width:30px;"><center>'+icono_talonario+'</center></td><td style="width:30px;"><center><img src="iconos/delete.png" style="cursor: pointer;" onClick="quitar_art('+articulos[i][0]+', '+tipo_art+', null);"></center></td></tr>';
      }
      
      table_html+='</table>';
      
      
      $('seleccion').innerHTML=table_html;
      
      
      }
		
		}
		
    verifica_tabla = function()
    {
        if(bloquear_ingreso)
            return;
        if($('tipo_movimiento').value=='-1')
        {
            alert('No ha seleccionado un movimiento a realizar.');
            return;
        }
        if($('tipo_movimiento').value==1) 
        {
            if(($('institucion_destino').value*1)==-1) 
            {
                alert('Debe seleccionar una instituci&oacute;n.'.unescapeHTML());
                return;
            }
        }
        if($('tipo_movimiento').value==7) 
        {
            if((cont>1) && $('centro_servicio').value==-1)
            {   
                if(!confirm('No ha Seleccionado Un Servicio para Despachar el Pedido Desea Enviarlo al Centro de Costo de todas formas?'.unescapeHTML()))
                    return;
                //alert('Debe seleccionar un Servicio para Despachar Pedido'.unescapeHTML());
                //return;
            }
        }
      
        if($('tipo_movimiento').value==8) 
        {
            if((cont>1) && $('centro_servicio').value==-1)
            {        
                if(!confirm('No ha Seleccionado Un Servicio para Despachar el Pedido Desea Enviarlo al Centro de Costo de todas formas?'.unescapeHTML()))
                    return;
                //alert('Debe seleccionar un Servicio para Despachar Pedido'.unescapeHTML());
                //return;
            }
        }
        
        if($('tipo_movimiento').value==0)
        {
            if($('bodega_origen').value==$('bodega_destino').value)
            {
                alert('La ubicacion de or&iacute;gen no puede ser la misma que la de destino.'.unescapeHTML());
                return;
            }
        }
		
	if(articulos.length==0)
        {
            alert('No ha seleccionado productos para trasladar.');
            return;
	}
			
	if($('tipo_movimiento').value==0 || $('tipo_movimiento').value==1)
        {
            if(($('id_pedido').value*1)==-1)
            {
                alert('Debe ingresar un n&uacute;mero de pedido v&aacute;lido.'.unescapeHTML());
		return;
            }
	}
			
	cantidad_prod=0;
	cantidad_no_cumple=0;
	cadena_no_cumple='';
	
        for(i=0;i<articulos.length;i++)
        {
            cantidad_prod+=articulos[i][1];
            if(articulos[i][6]>articulos[i][1])
            {
                cantidad_no_cumple++;
		cadena_no_cumple+='['+articulos[i][2]+'] -- '+articulos[i][4]+'\n';
            }
        }
			
	if(cantidad_prod==0)
        {
            alert("No ha movido ning&uacute;n art&iacute;culo.".unescapeHTML());
            return;
	}
      
	if(cantidad_no_cumple>0)
            if(!confirm('No ha cubierto la necesidad de:\n\n'+cadena_no_cumple+'\n&iquest;Desea continuar de todas formas?'.unescapeHTML()))
                return;
        
        tal_cadena='';  
        for(i=0;i<articulos.length;i++)
        {
            if(comprobar_talonario(articulos[i][0]))
            {
                if(trim(articulos[i][8])=='')
                {
                    alert('No ha especificado el detalle de los talonarios que se estan moviendo.');
                    return;
		}
                else
                {
                    tal_cadena+='tal_art_'+i+'='+encodeURIComponent(articulos[i][8])+'&'
		}
            }
        }
        loquear_ingreso=true;
        var dtemp=$('centro_costo').disabled;
  	var stemp=$('centro_servicio').disabled;
        $('centro_costo').disabled=false;
  	$('centro_servicio').disabled=false;
        var myAjax = new Ajax.Request('abastecimiento/movimiento_articulos/sql.php',
        {
            method: 'post', 
            parameters: tal_cadena+'cant='+articulos.length+'&'+$('buscar_form').serialize()+'&bodega_destino='+($('bodega_destino').value)+'&'+$('numero_pedido').serialize(),
            onComplete: function (pedido_datos)
            {
                try { 
                    datos = pedido_datos.responseText.evalJSON(true);
                }
                catch (err) { alert(err); }
                if(datos[0]==true)
                {
                    switch(($('tipo_movimiento').value*1))
                    {
                        case 0: alert('Traslado realizado exitosamente.'); break;
                        case 1: alert('Pr&eacute;stamo/Devoluci&oacute;n de Art&iacute;culos realizado exitosamente.'.unescapeHTML()); break;
                        case 2: alert('Baja de Art&iacute;culos realizado exitosamente.'.unescapeHTML()); break;
                        case 3: alert('Baja por Vencimiento realizada exitosamente.'); break;
                        case 5: alert('Ingreso por Donaci&oacute;n realizado exitosamente.'.unescapeHTML()); break;
                        case 6: alert('Ingreso de Inicio al Sistema realizado exitosamente.'.unescapeHTML()); break;
                    }
                    if($('tipo_movimiento').value==0 || $('tipo_movimiento').value==7)
                    {
                        //ventana_lotes(datos[1]);
                        cambiar_pagina('abastecimiento/movimiento_articulos/form.php', function() {visualizador_documentos('Visualizar Pedido', 'id_pedido='+encodeURIComponent(datos[1]));} );
                    }
                    else if ($('tipo_movimiento').value==8 || $('tipo_movimiento').value==1)
                    {
                        cambiar_pagina('abastecimiento/movimiento_articulos/form.php', function() {visualizador_documentos('Visualizar Movimiento', 'log_id='+encodeURIComponent(datos[1]));} );
                    }
                    else
                    {
                        cambiar_pagina('abastecimiento/movimiento_articulos/form.php');
                    }
                }
                else
                {
                    alert('ERROR:\n\n'+pedido_datos.responseText);
                }
            }
	});
	$('centro_costo').disabled=dtemp;
  	$('centro_servicio').disabled=stemp;
    }
		
		ventana_lotes = function(_lotes) {
		
		_html='<div class="sub-content3" style="height:380px;"><table width="100%"><tr class="tabla_header"><td>C&oacute;digo Int.</td><td>Nombre</td><td>Fecha Venc.</td><td>Cantidad</td></tr>';
		
		for(i=0;i<_lotes.length;i++) {
		
		  for(a=0;a<articulos.length;a++) {
        if(_lotes[i][0]==articulos[a][0]) break; 
      }
      
      if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
    
      _html+='<tr class="'+clase+'"><td style="text-align: right;">'+articulos[a][2]+'</td><td>'+articulos[a][4]+'</td><td style="text-align: center;">'+_lotes[i][2]+'</td><td style="text-align: right;">'+_lotes[i][1]+'</td></tr>';
    
    }
    
    _html+='</table></div>';
    
    var win = new Window("ver_lotes", {className: "alphacube", 
                          top:40, left:0, 
                          width: 400, height: 400, 
                          title: '<img src="iconos/lorry_go.png"> Lotes Trasladados',
                          minWidth: 400, minHeight: 400,
                          maximizable: false, minimizable: false, 
                          wiredDrag: true, resizable: false,
                          onClose:  cambiar_pagina('abastecimiento/movimiento_articulos/form.php') });
                          
    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
    
    win.setHTMLContent(_html);
			
		$("ver_lotes").win_obj=win;
  
    win.setDestroyOnClose();
    win.showCenter();
    win.show();
    
    }
		
    corrige_campos = function()
    {
        switch($('tipo_movimiento').value)
        {
            case '-1':
                // No muestra nada...
                $('fila_destino').style.display='none';
                $('fila_destino_ext').style.display='none';
                $('servicio_destino').style.display='none';
                $('servicio_destino_2').style.display='none';
                $('fila_nro_pedido').style.display='none';
                $('numero_pedido').value='';
                $('boton_agregar').style.display='none';
                $('boton_agregar_venc').style.display='none';
                $('boton_agregar_sacar').style.display='none';
                $('boton_vencidos').style.display='none';
                $('boton_balance').style.display='none';
                tipo_tabla_arts=1;
                break;
            
            case '0':
                // Traslado Interno
                $('fila_destino').style.display='';
                $('fila_destino_ext').style.display='none';
                $('_tipo_prestamo').style.display='none';
                $('servicio_destino').style.display='none';
                $('servicio_destino_2').style.display='none';
                $('fila_nro_pedido').style.display='';
                $('id_pedido').value=0;
                $('numero_pedido').value='';
                $('numero_pedido').style.background='';
                $('error_pedido_img').style.display='none';
                $('enlazar_pedido_img').style.display='none';
                $('chk_pedido_nuevo').checked=true;
                if($('bodega_origen').value!=1)
                {
                    $('boton_agregar').style.display='';
                }
                else
                {
                    $('boton_agregar').style.display='none';
                }
                $('boton_agregar_venc').style.display='none';
                $('boton_agregar_sacar').style.display='none';
                $('boton_vencidos').style.display='none';
                $('boton_balance').style.display='none';
                tipo_tabla_arts=0;
                break;
            
            case '7': 
                // Despacho a Servicios
                $('fila_destino').style.display='none';
                $('fila_destino_ext').style.display='none';
                $('_tipo_prestamo').style.display='none';
                $('servicio_destino').style.display='';
                $('servicio_destino_2').style.display='';
                $('fila_nro_pedido').style.display='';
                $('id_pedido').value=0;
                $('numero_pedido').value='';
                $('numero_pedido').style.background='';
                $('error_pedido_img').style.display='none';
                $('enlazar_pedido_img').style.display='none';
                $('chk_pedido_nuevo').checked=true;
                if($('bodega_origen').value!=1)
                {
                    $('boton_agregar').style.display='';
                }
                else
                {
                    $('boton_agregar').style.display='none';
                }
                $('boton_agregar_venc').style.display='none';
                $('boton_agregar_sacar').style.display='none';
                $('boton_vencidos').style.display='none';
                $('boton_balance').style.display='none';
                $('centro_costo').disabled=false;
                $('centro_servicio').disabled=false;
                cargar_centros();
                tipo_tabla_arts=0;
                break;
            
            case '8':
                // Devoluci�n desde Servicios
                $('fila_destino').style.display='none';
                $('fila_destino_ext').style.display='none';
                $('_tipo_prestamo').style.display='none';
                $('servicio_destino').style.display='';
                $('servicio_destino_2').style.display='';
                $('fila_nro_pedido').style.display='none';
                $('id_pedido').value=0;
                $('numero_pedido').value='';
                $('numero_pedido').style.background='';
                $('error_pedido_img').style.display='none';
                $('enlazar_pedido_img').style.display='none';
                $('chk_pedido_nuevo').checked=true;
                $('boton_agregar').style.display='none';
                $('boton_agregar_venc').style.display='';
                $('boton_agregar_sacar').style.display='none';
                $('boton_vencidos').style.display='none';
                $('boton_balance').style.display='none';
                cargar_centros();
                tipo_tabla_arts=1;
                break;
            
            case '1':
                // Pr�stamo
                $('fila_destino').style.display='none';
                $('fila_destino_ext').style.display='';
                $('_tipo_prestamo').style.display='';
                $('servicio_destino').style.display='none';
                $('servicio_destino_2').style.display='none';
                $('fila_nro_pedido').style.display='none';
                $('id_pedido').value=0;
                $('numero_pedido').value='';
                $('numero_pedido').style.background='';
                $('error_pedido_img').style.display='none';
                $('enlazar_pedido_img').style.display='none';
                $('chk_pedido_nuevo').checked=true;
                $('boton_agregar').style.display='none';
                $('boton_agregar_venc').style.display='none';
                $('boton_agregar_sacar').style.display='none';
                $('boton_vencidos').style.display='none';
                $('boton_balance').style.display='';
                if($('tipo_prestamo').value==1)
                    tipo_tabla_arts=0;
                else
                    tipo_tabla_arts=1;
                break;
            
            case '4':
                // Canje
                $('fila_destino').style.display='none';
                $('fila_destino_ext').style.display='';
                $('_tipo_prestamo').style.display='none';
                $('servicio_destino').style.display='none';
                $('servicio_destino_2').style.display='none';
                $('fila_nro_pedido').style.display='none';
                $('id_pedido').value=0;
                $('numero_pedido').value='';
                $('numero_pedido').style.background='';
                $('error_pedido_img').style.display='none';
                $('enlazar_pedido_img').style.display='none';
                $('chk_pedido_nuevo').checked=true;
                $('boton_agregar').style.display='none';
                $('boton_agregar_venc').style.display='none';
                $('boton_agregar_sacar').style.display='';
                $('boton_vencidos').style.display='none';
                $('boton_balance').style.display='none';
                tipo_tabla_arts=2;
                break;
            
            case '3':
                // Baja por Vencimiento
                $('fila_destino').style.display='none';
                $('fila_destino_ext').style.display='none';
                $('_tipo_prestamo').style.display='none';
                $('servicio_destino').style.display='none';
                $('servicio_destino_2').style.display='none';
                $('fila_nro_pedido').style.display='none';
                $('numero_pedido').value='';
                $('id_pedido').value=0;
                $('boton_agregar').style.display='none';
                $('boton_agregar_venc').style.display='none';
                $('boton_agregar_sacar').style.display='none';
                $('boton_vencidos').style.display='';
                $('boton_balance').style.display='none';
                tipo_tabla_arts=1;
                break;
      
            case '5': case '6':
                // Ingreso por Excedente // Inicio del Sistema
                $('fila_destino').style.display='none';
                $('fila_destino_ext').style.display='none';
                $('_tipo_prestamo').style.display='none';
                $('servicio_destino').style.display='none';
                $('servicio_destino_2').style.display='none';
                $('fila_nro_pedido').style.display='none';
                $('numero_pedido').value='';
                $('boton_agregar').style.display='none';
                $('boton_agregar_venc').style.display='';
                $('boton_agregar_sacar').style.display='none';
                $('boton_vencidos').style.display='none';
                $('boton_balance').style.display='none';
                tipo_tabla_arts=1;
                break;
        }
	limpiar_art();
    }

    listar_pedidos = function()
    {
        origen=$('bodega_origen').value;
        destino=$('bodega_destino').value;
        var win = new Window("busca_pedidos", {className: "alphacube", 
        top:40, left:0, 
        width: 550, height: 350, 
        title: '<img src="iconos/page_white_link.png"> Seleccionar Pedidos Vigentes',
        minWidth: 550, minHeight: 350,
        maximizable: false, minimizable: false, 
        wiredDrag: true, resizable: false });
        win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
        win.setAjaxContent('abastecimiento/movimiento_articulos/listar_pedidos.php', 
        {
            method: 'get', 
            evalScripts: true,
            parameters: $('bodega_origen').serialize()
        });
	$("busca_pedidos").win_obj=win;
        win.setDestroyOnClose();
        win.showCenter();
        win.show();
    }
    
    tipo_servicios_desp=0;
    
    cargar_opciones = function () {
      
        $('div_tipo_movimientos').innerHTML='<select><option>(Cargando...)</option></select> <img src="imagenes/ajax-loader1.gif">';
        
        var myAjax = new Ajax.Updater(
        'div_tipo_movimientos', 
        'abastecimiento/movimiento_articulos/movs_select.php', 
        {
          method: 'get',
          parameters: $('bodega_origen').serialize(),
          evalScripts: true
        });
        
    }
    
    cargar_centros = function ()
    {
        $('centro_costo').value=-1;
        var selhtml='<select id="centro_servicio" name="centro_servicio"><option value=-1>(Seleccionar...)</option></select>';
        $('div_centro_servicio').innerHTML=selhtml;
    }
    
    cargar_servicios = function ()
    {
        cont=1;
        centros = $('centro_costo').value;
        var servicios=<?php echo json_encode($lista_servicios); ?>;
        var selhtml='<select id="centro_servicio" name="centro_servicio"><option value=-1>(Seleccionar...)</option>';
        for(var i=0;i<servicios.length;i++)
        {
            var tmp = servicios[i].centro_ruta.split('.');
            //var ccosto = '.'+tmp[1]+'.'+tmp[2]+'.'+tmp[3];
            var ccosto='.'+tmp[1]+'.'+tmp[2];
            //var ccosto='';
            //for(x=0;x<tmp.length;x++)
            //{
            //	ccosto+='.'+tmp[x];
            //}
            //console.log(ccosto+' '+centros);
            //alert("CCOSTO "+  ccosto);
            //alert("CENTROS "+ centros);
            if(ccosto==centros)
            {
                selhtml+='<option value="'+servicios[i].centro_ruta+'">'+servicios[i].centro_nombre+'</option>';
                cont=cont+1;
            }
        }
        selhtml+='</select>'        
        $('div_centro_servicio').innerHTML=selhtml;
        if(cont==1)
            $('centro_servicio').value=0;
    }
    
    conf_talonarios = function(fila_art) {
    
      tipotal = $('id_art_'+fila_art).value;
      canttal = articulos[fila_art][1];
    
      params='tipo=1&art_num=' + fila_art + '&tipotalonario_id=' + tipotal + '&nro_talonarios=' + canttal + '&cadena_actual='+encodeURIComponent(articulos[fila_art][8]);
    
      top=Math.round(screen.height/2)-100;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('abastecimiento/ingreso_talonarios.php'+
      '?'+params, 'win_talonarios', 
      'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=200, '+
      'top='+top+', left='+left);
      
      new_win.focus();
      
    }
    
    sel_talonarios = function(fila_art) {
    
      tipotal = $('id_art_'+fila_art).value;
      canttal = articulos[fila_art][1];
    
      params='tipo=0&art_num=' + fila_art + '&tipotalonario_id=' + tipotal + 
      '&nro_talonarios=' + canttal + 
      '&cadena_actual='+encodeURIComponent(articulos[fila_art][8])+
      '&bodega_id='+$('bodega_origen').value;
    
      top=Math.round(screen.height/2)-100;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
    window.open('abastecimiento/seleccion_talonarios.php'+
      '?'+params, 'win_talonarios', 
      'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=200, '+
      'top='+top+', left='+left);
      
      new_win.focus();
      
    }
    
    sel_talonarios_func = function(fila_art) {
    
      tipotal = $('id_art_'+fila_art).value;
      canttal = articulos[fila_art][1];
    
      params='tipo=1&art_num=' + fila_art + '&tipotalonario_id=' + tipotal + 
      '&nro_talonarios=' + canttal + 
      '&cadena_actual='+encodeURIComponent(articulos[fila_art][8])+
      '&bodega_id='+$('bodega_origen').value;
    
      top=Math.round(screen.height/2)-100;
      left=Math.round(screen.width/2)-300;
      
      new_win = 
    window.open('abastecimiento/seleccion_talonarios.php'+
      '?'+params, 'win_talonarios', 
      'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=600, height=200, '+
      'top='+top+', left='+left);
      
      new_win.focus();
      
    }
		
</script>
  <center>
  <table><tr><td>
	
	<div class='sub-content'>
                <div class='sub-content'>
                    <img src='iconos/page_gear.png'>
                    <b>Movimiento de Art&iacute;culos</b>
                </div>
    <center>
      <form name='buscar_form' id='buscar_form' onSubmit='return false;'>
        <input type='hidden' id='id_pedido' name='id_pedido' value=0>
        <div id='bodega_origen_div' class='sub-content' style='width:650px; text-align: left;'>
		        <div class='sub-content'><img src='iconos/building.png'>
              <b>Tipo de Movimiento y Ubicaciones</b>
            </div>
            <table width=100%>
              <tr id='fila_origen'>
                <td style='text-align: right; width:100px;' class='form_titulo'>
		              Or&iacute;gen:
		            </td>
                <td class='form_campo'>
		              <select name='bodega_origen' id='bodega_origen' onChange='limpiar_art(); cargar_opciones();' style='font-size:16px; color:red; background-color:white; border:2px solid black;'>
		                <?php echo $bodegashtml; echo $servicioshtml; ?>
		              </select>
		            </td>
              </tr>
              <tr>
                <td style='text-align: right;' class='form_titulo'>
                  Tipo:
		            </td>
                <td class='form_campo'>
                  <div id='div_tipo_movimientos'>
                    <select name='tipo_movimiento' id='tipo_movimiento'>
		                  <option value=-1>(Seleccione Ubicaci&oacute;n...)</option>
		                </select>
                  </div>
                </td>
              </tr>
              <tr id='_tipo_prestamo' style='display: none;'>
                <td style='text-align: right;' class='form_titulo'>Transacci&oacute;n:</td>
                <td class='form_campo'>
                  <select id='tipo_prestamo' name='tipo_prestamo' onChange='corrige_campos();'>
                    <option value=0 SELECTED>Entrada de Art&iacute;culos</option>
                    <option value=1>Salida de Art&iacute;culos</option>
                  </select>
                </td>
              </tr>
		          <tr id='fila_destino' style='display: none;'>
                <td style='text-align: right;' class='form_titulo'>
		              Destino:
		            </td>
                <td class='form_campo'>
		              <select name='bodega_destino' id='bodega_destino'>
		                <?php echo $bodegas2html; ?>
		              </select>
                </td>
              </tr>
              <tr id='fila_destino_ext' style='display: none;'>
                <td style='text-align: right;' class='form_titulo'>
		              Or&iacute;gen/Destino:
		            </td>
                <td class='form_campo'>
		              <select name='institucion_destino' id='institucion_destino'>
		                <option value=-1 SELECTED>(Seleccionar...)</option>
		                <?php echo $institucionhtml; ?>
		              </select>
                </td>
              </tr>
              <tr id='servicio_destino' style='display: none;'>
                <td style='text-align: right;' class='form_titulo'>Centro de Costo:</td>
                <td class='form_campo'>
                  <div id='div_centro_costo'>
                    <select id='centro_costo' name='centro_costo' onClick='cargar_servicios();'>
                      <?php echo $centroshtml; ?>
                    </select>
                  </div>
                </td>
              </tr>
              <tr id='servicio_destino_2' style='display:none;'>
                <td style='text-align: right;' class='form_titulo'>
                  Servicio:
                </td>
                <td class='form_campo'>
                  <div id='div_centro_servicio'>
                    <select id='centro_servicio' name='centro_servicio'>
                      <option value=-1>(Seleccionar...)</option>
                    </select>
                  </div>
                </td>
              </tr>
              <tr id='fila_nro_pedido' style='display: none;'>
                <td style='text-align: right;' class='form_titulo'>
		              Nro. de Pedido:
                </td>
                <td class='form_campo' colspan=4>
                  <table cellpadding=0 cellspacing=0>
                    <tr>
                      <td width=150>
                        <input type='text' id='numero_pedido' name='numero_pedido' size=7 
                        style='text-align: right;' onChange='descargar_pedido();'>
                        <img src='iconos/zoom_in.png' onClick='listar_pedidos();'
                        style='cursor: pointer;'>
                        <img src='iconos/page_white_link.png' 
                        id='enlazar_pedido_img' name='enlazar_pedido_img'
                        style='display: none;'
                        alt='Enlazado con Pedido...'
                        title='Enlazado con Pedido...'>
    
                        <img src='iconos/page_white_error.png' 
                        id='error_pedido_img' name='error_pedido_img'
                        style='display: none;'
                        alt='N&uacute;mero de Pedido no puede ser utilizado...'
                        title='N&uacute;mero de Pedido no puede ser utilizado...'>
                      </td>
                      <td>
                        <img src='imagenes/ajax-loader1.gif' 
                        id='cargar_pedido_img' name='cargar_pedido_img'
                        style='display: none;'>
                      </td>
                      <td>
                        <input type='checkbox' id='chk_pedido_nuevo' name='chk_pedido_nuevo' onClick='forzar_pedido_nuevo();' CHECKED>
                      </td>
                      <td class='form_titulo' width=100>
                        Pedido Nuevo
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style='text-align: right;' class='form_titulo' valign=center>
                  Comentarios:
                </td>
                <td class='form_campo'>
                  <textarea id='comentarios' name='comentarios' cols=55 rows=1></textarea>
                </td>
              </tr>
            </table>
        </div>
        <br>
        <center>
          <div style='width: 500px; display:none;' id='boton_agregar'>
            <div class='boton'>	
              <table>
                <tr>
                  <td>
		                <img src='iconos/add.png'>
		              </td>
                  <td>
		                <a href='#' 
                    onClick='
                    buscar_codigos_barra($("bodega_origen").value,seleccionar_articulo,0, "buscar_arts", "bodega_origen");
                    '>
                    Agregar Productos...
                    </a>
		              </td>
                </tr>
              </table>
            </div>
          </div>
          <div style='width: 500px; display: none;' id='boton_agregar_sacar'>
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
		                      <a href='#' 
                          onClick='
                          buscar_codigos_barra($("bodega_origen").value,seleccionar_articulo,1, "buscar_arts", "bodega_origen");
                          '>
                            Entrada de Productos...
                          </a>
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
		                      <img src='iconos/delete.png'>
		                    </td>
                        <td>
		                      <a href='#' 
                          onClick='
                          buscar_codigos_barra($("bodega_origen").value,seleccionar_articulo_s,0, "buscar_arts", "bodega_origen");
                          '>
                          Salida de Productos...
                          </a>
		                    </td>
                      </tr>
                    </table>
                  </div>	
                </td>
              </tr>
            </table>
          </div>
          <div style='width: 500px; display: none;' id='boton_agregar_venc'>
            <div class='boton'>	
              <table>
                <tr>
                  <td>
		                <img src='iconos/add.png'>
		              </td>
                  <td>
		                <a href='#' 
                    onClick='
                    buscar_codigos_barra($("bodega_origen").value,seleccionar_articulo,1, "buscar_arts", "bodega_origen");
                    '>
                    Agregar Productos...
                    </a>
		              </td>
                </tr>
              </table>
            </div>
          </div>
          <div style='width: 500px; display: none;' id='boton_vencidos'>
            <div class='boton'>
		          <table>
                <tr>
                  <td>
		                <img src='iconos/date_error.png'>
		              </td>
                  <td>
		                <a href='#' onClick='abrir_busqueda_vencidos();'> Seleccionar Lotes Vencidos...</a>
		              </td>
                </tr>
              </table>
            </div>
          </div>
          <div style='width: 500px; display: none;' id='boton_balance'>
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
                          <a href='#' onClick='buscar_articulos_presta();'>
                            Agregar Productos...
                          </a>
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
		                    <img src="iconos/arrow_refresh_small.png">
		                  </td>
                      <td>
		                    <a href='#' onClick='abrir_busqueda_balance();'>Balance de Pr&eacute;stamos...</a>
		                  </td>
                    </tr>
                  </table>
		            </div>
                </td>
              </tr>
            </table>
          </div>
          <div class='sub-content' style='width:650px;'>
            <div class='sub-content'>
              <img src='iconos/page.png'>
              <b>Selecci&oacute;n de Productos</b></div>
              <div id='seleccion' name='seleccion' class='sub-content2'>
                <table style="width:100%;">
                  <tr class="tabla_header">
                    <td style="width:100px;">
                      <b>Cod. Interno</b>
                    </td>
                    <td style="width:200px;">
                      <b>Glosa</b>
                    </td>
                    <td>
                      <b>Pedido</b>
                    </td>
                    <td>
                      <b>Cant.</b>
                    </td>
                    <td>
                      <b>Dif.</b>
                    </td>
                    <td>
                      <b>Stock</b>
                    </td>
                    <td>
                      <b>Acciones</b>
                    </td>
                  </tr>
                </table>
              </div>
            </div>
            <div class='sub-content' style='width:650px;'>
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
		                          <a href='#' onClick='verifica_tabla();'>Realizar Traslado...</a>
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
		                          <img src='iconos/delete.png'>
		                        </td>
                            <td>
		                          <a href='#' onClick='limpiar_art();'>
		                            Limpiar Formulario...</a>
		                        </td>
                          </tr>
                        </table>
                      </div>
                    </td>
                  </tr>
                </table>
              </center>
            </div>
          </center>
        </form>
        </div>
</td></tr></table>
      </center>
      <div id='lotes_movidos' style='display: none;'>
      </div>
	<script> cargar_opciones(); </script>
	