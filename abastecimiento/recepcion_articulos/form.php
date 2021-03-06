<?php

  require_once("../../conectar_db.php");

	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1',
    'bod_id IN ('._cav(2),')', 'ORDER BY bod_glosa');



?>

<script type='text/javascript'>
    articulos=new Array();
	doc_desc=0;
	orden_compra=false;
    seleccionar_articulo = function(art_id, art_codigo, artc_codigo, art_glosa, art_cantidad, art_stock, art_fecha_venc,
                            art_punit_med, art_forma)
    {
        artpedido=false;
        encontrado=false;
        cant_solicitada=0;
        pedido_id=0;
        pedido_nro=0;
        pedidod_id=0;
        //art_forma = '';
        
        if(orden_compra)
        {
            // Comprueba que los art�culos est�n asociados
            // a la orden de compra para evitar errores.
            var chequeado=false;
            odet = orden_compra.detalle;
            if(odet!=false)
            {
                for(var n=0;n<odet.length;n++)
                {
                    if(odet[n].ordetalle_art_id==art_id)
                    {
                        if(pedidos==true)
                        {
                            for(var xx=0;xx<orden_compra.detallep.length;xx++)
                            {
                                if(orden_compra.detallep[xx].art_id==art_id)
                                {
                                    cant_solicitada=orden_compra.detallep[xx].pedidod_cant*1;
                                    pedido_id=orden_compra.detallep[xx].pedido_id;
                                    pedido_nro=orden_compra.detallep[xx].pedido_nro;
                                    pedidod_id=orden_compra.detallep[xx].pedidod_id;
                                    pedidod_estado=orden_compra.detallep[xx].pedidod_estado;
                                    artpedido=true;
                                    break;
                                }
                            }
                            if(artpedido==false)
                            {
                                confirmar3 = confirm('El art&iacute;culo '.unescapeHTML() +artc_codigo+' No se encuentra incorporado en los pedidos asociados a esta orden de compra, desea recepcionar este art&iacute;culo de todas formas?'.unescapeHTML());
                                if(!confirmar3)
                                {
                                    return;
                                    
                                }
                                else
                                {
                                    cant_solicitada=0;
                                }
                            }
                            
                        }
                        chequeado=true; break;
                    }
                }
                /*if(!chequeado)
                {
                    alert(('Este art&iacute;culo no est&aacute; inclu&iacute;do en'+
                    ' la &oacute;rden de compra seleccionada.').unescapeHTML());
                    return;
                }*/
            }

        }
        for(i=0;i<articulos.length;i++)
        {
            if(articulos[i][0]==art_id && articulos[i][7]==art_fecha_venc)
            {
                if(art_codigo==artc_codigo)
                {
                    confirmar = confirm('El art&iacute;culo con el lote especificado aparentemente ya est&aacute; ingresado, &iquest;Desea sumar '.unescapeHTML()+art_cantidad+' U.A. a esta cantidad?'.unescapeHTML());
                    if(!confirmar)
                    {
                        confirmar2 = confirm('&iquest;Desea agregarlo como producto distinto con el mismo c&oacute;digo? (Ej. Productos iguales de distinto precio.)'.unescapeHTML());
                        if(!confirmar2) return;
                        break;
                    }
                }
                // Guarda Valores Ingresados
                articulos[i][1]+=art_cantidad;
                articulos[i][2]=art_codigo;
                articulos[i][3]=artc_codigo;
                articulos[i][4]=art_glosa;
                articulos[i][5]=art_stock;
                articulos[i][23]=art_forma;
                encontrado=true;


            }
        }
        if(!encontrado)
        {
            // Guarda Valores Ingresados
            guarda_vals();
            num=articulos.length;
            articulos[num]=new Array();
            articulos[num][0]=art_id;
            articulos[num][1]=art_cantidad;
            articulos[num][2]=art_codigo;
            articulos[num][3]=artc_codigo;
            articulos[num][4]=art_glosa;
            articulos[num][5]=art_stock;
            articulos[num][6]=0;
            articulos[num][7]=art_fecha_venc;
            articulos[num][8]=0;
            articulos[num][9]=1;
            articulos[num][10]=0;
            articulos[num][11]=0;
            articulos[num][12]='';
            articulos[num][13]='';
            articulos[num][14]=false;
            articulos[num][15]=art_punit_med;
            articulos[num][16]='';
            articulos[num][17]=cant_solicitada;
            articulos[num][18]=pedidod_id;
            articulos[num][19]=pedido_id;
            articulos[num][20]=0;
            articulos[num][21]=pedido_nro;
            articulos[num][22]=(art_fecha_venc==null?0:1);
            articulos[num][23]=art_forma;
        }
 
        redibujar_tabla();
        $('contenido').scrollTop=$('contenido').scrollHeight;
    }
	
    
    actualizar_tabla = function ()
    {
        guarda_vals();
        redibujar_tabla();
    }
	
    redibujar_tabla = function()
    {
        // SELECCION CON FECHAS DE VENCIMIENTO
        // Redibuja Tabla
        if($('veringr').value==0)
        {
            noserie='';
            serie='display:none;';
            partida='display:none;';
        }
        else
        {
            if($('veringr').value==1)
            {
                noserie='display:none;';
                serie='';
                partida='display:none;';
            }
            else
            {
                noserie='display:none;';
                serie='display:none;';
                partida='';
            }
        }
        table_html="<table width=100%><tr class='tabla_header' style='font-weight: bold;'><td style='width:80px;'>C&oacute;digo Int.</td><td style='width:150px;'>Nombre</td><td>Fecha de Venc.</td><td>Cant.</td><td>Cant Sol.</td><td style='"+noserie+"'>Ud. Div.</td><td style='"+noserie+"'>P. Unit.</td><td style='width:100px; "+noserie+"'>Subtotal</td><td style='"+serie+"'>N&uacute;meros de Serie</td><td style='"+partida+"'>N&uacute;meros de Partida</td><td colspan=4>Acciones</td></tr>";
           
        for(i=0;i<articulos.length;i++)
        {
            if(i%2==0)
            {
                clase='tabla_fila';
            }
            else
            {
                clase='tabla_fila2';
            }
            if(articulos[i][6]>0)
            {
                tipo_art=1;
            }
            else
            {
                tipo_art=0;
            }
            if(articulos[i][7]==null)
            {
                glosa_fecha='<i>No Perecible</i>';
                valor_fecha='';
                paso_fecha=null;
            }
            else
            {
                glosa_fecha=articulos[i][7];
                valor_fecha=glosa_fecha;
                paso_fecha="'"+glosa_fecha+"'";
            }
            if(articulos[i][10]==1) chequeado='checked'; else chequeado='';
            if(articulos[i][20]==1)chequeadot='checked'; else chequeadot='';
            artid = articulos[i][0];
            if(comprobar_talonario(artid))
            {
                icono_talonario='<td style="width: 20px;"><center><img src="iconos/page_edit.png" style="cursor: pointer;" onClick="conf_talonarios('+i+');"></td>';
            }
            else
            {
                icono_talonario='<td style="width: 20px;">&nbsp;</td>';
            }
            

		var difmonto  = articulos[i][15]-
                        (articulos[i][11]/articulos[i][9]);
        var difvar    = (difmonto)*100/articulos[i][15];

            //var difvar=(articulos[i][15]-(articulos[i][11]/articulos[i][9]))*100/
            //articulos[i][15];

            if((difvar>10 || difvar<-10) && !(articulos[i][14]))
            {
                icono_precio='error';
            }
            else
            {
                icono_precio='magnifier';
            }
            
            //table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'" id="art_fila_'+i+'"><input type="hidden" id="id_art_'+i+'" name="id_art_'+i+'" value='+articulos[i][0]+'><input type="hidden" id="cant_art_'+i+'" name="cant_art_'+i+'" value='+articulos[i][1]+'><input type="hidden" id="fecha_art_'+i+'" name="fecha_art_'+i+'" value="'+valor_fecha+'"><td style="text-align:right;">'+articulos[i][2]+'</td><td style="font-size: 10px;">'+articulos[i][4]+'</td><td style="font-size: 10px;">'+articulos[i][4]+'</td><td style="text-align:center;">'+glosa_fecha+'</td><td style="text-align:right;">'+articulos[i][1]+'</td><td style="'+noserie+'"><center><input type="text" id="ud_art_'+i+'" name="ud_art_'+i+'" style="text-align: right;" size=4 value="'+(articulos[i][9]*1)+'" onFocus="this.select();" onKeyUp="recalcular_total();" onChange="if(this.value<=0) this.value=1;"></center></td><td style="'+noserie+'"><center><input type="text" id="valunit_art_'+i+'" name="valunit_art_'+i+'" style="text-align: right;" size=8 value="'+(articulos[i][11]*1)+'" onFocus="this.select();" onKeyUp="recalcular_total();"></center></td><td style="text-align: right; font-size: 10px; '+noserie+'"><center><input type="checkbox" id="check_subt_'+i+'" name="check_subt_'+i+'" '+chequeado+'><input type="text" id="val_art_'+i+'" name="val_art_'+i+'" style="text-align: right;" size=8 value="'+(articulos[i][8]*1)+'" onFocus="this.select();" onKeyUp="recalcular_total();"></center></td><td style="'+serie+'"><input type="text" id="serie_art_'+i+'" name="serie_art_'+i+'" value="'+articulos[i][13]+'" size=25></td><td style="'+partida+'"><input type="text" id="partida_art_'+i+'" name="partida_art_'+i+'" value="'+articulos[i][16]+'" size=25></td>'+icono_talonario+'<td style="width: 20px;"><center><img src="iconos/delete.png" style="cursor: pointer;" onClick="quitar_art('+i+');"></center></td><td style="width: 20px;"><center><img src="iconos/'+icono_precio+'.png" id="art_dif_'+i+'" style="cursor: pointer;" onClick="confirmar_precios('+articulos[i][0]+','+i+');"></center></td></tr>';
            table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'" id="art_fila_'+i+'">';
            table_html+='<input type="hidden" id="id_art_'+i+'" name="id_art_'+i+'" value='+articulos[i][0]+'>';
            table_html+='<td style="text-align:right;font-weight:bold;">'+articulos[i][2]+'</td>';
            table_html+='<td style="text-align:left;font-size:10px;">'+articulos[i][4]+'</td>';
            
            if(articulos[i][22]=='1')
				table_html+='<td><input type="text" size=12 style="font-size:10px;text-align:center;" id="fecha_art_'+i+'" name="fecha_art_'+i+'" value="'+valor_fecha+'" onBlur="validacion_fecha(this);" /></td>';
			else
				table_html+='<td><i>(No Perecible...)</i></td>';
				
            table_html+='<td><input type="text" onBlur="recalcular_total();" size=5 style="font-size:10px;text-align:right;" id="cant_art_'+i+'" name="cant_art_'+i+'" value='+articulos[i][1]+' /> <b>'+articulos[i][23]+'</b></td>';
            table_html+='<td style="text-align:right;font-weight:bold;color:#0000ff;">'+articulos[i][17]+'</td>';
            table_html+='<td style="'+noserie+'">';
            table_html+='<center>';
            table_html+='<input type="text" id="ud_art_'+i+'" name="ud_art_'+i+'" style="text-align: right;" size=4 value="'+(articulos[i][9]*1)+'" onFocus="this.select();" onKeyUp="recalcular_total();" onChange="if(this.value<=0) this.value=1;">';
            table_html+='</center>';
            table_html+='</td>';
            table_html+='<td style="'+noserie+'">';
            table_html+='<center>';
            table_html+='<input type="text" id="valunit_art_'+i+'" name="valunit_art_'+i+'" style="text-align: right;" size=8 value="'+(articulos[i][11]*1)+'" onFocus="this.select();" onKeyUp="recalcular_total();">';
            table_html+='</center>';
            table_html+='</td>';
            table_html+='<td style="text-align: right; font-size: 10px; '+noserie+'">';
            table_html+='<center>';
            table_html+='<input type="checkbox" id="check_subt_'+i+'" name="check_subt_'+i+'" '+chequeado+'>';
            table_html+='<input type="text" id="val_art_'+i+'" name="val_art_'+i+'" style="text-align: right;" size=8 value="'+(articulos[i][8]*1)+'" onFocus="this.select();" onKeyUp="recalcular_total();">';
            table_html+='</center>';
            table_html+='</td>';
            table_html+='<td style="'+serie+'">';
            table_html+='<input type="text" id="serie_art_'+i+'" name="serie_art_'+i+'" value="'+articulos[i][13]+'" size=25>';
            table_html+='</td>';
            table_html+='<td style="'+partida+'">';
            table_html+='<input type="text" id="partida_art_'+i+'" name="partida_art_'+i+'" value="'+articulos[i][16]+'" size=25>';
            table_html+='</td>';
            table_html+=''+icono_talonario+'';
            table_html+='<td style="width: 20px;">';
            table_html+='<center>';
            table_html+='<img src="iconos/delete.png" style="cursor: pointer;" onClick="quitar_art('+i+');">';
            table_html+='</center>';
            table_html+='</td>';
            table_html+='<td style="width:20px;">';
            table_html+='<center>';
            table_html+='<img src="iconos/'+icono_precio+'.png" id="art_dif_'+i+'" style="cursor: pointer;width:24px;height:24px;" onClick="confirmar_precios('+articulos[i][0]+','+i+');">';
            table_html+='</center>';
            table_html+='</td>';
            table_html+='<td style="width: 20px;">';
            table_html+='<center>';
            if(articulos[i][1]>=articulos[i][17])
            {
                table_html+='<input type="checkbox" id="check_ter_'+i+'" name="check_ter_'+i+'" disabled>';
            
            }
            else
            {
                table_html+='<input type="checkbox" id="check_ter_'+i+'" name="check_ter_'+i+'" '+chequeadot+'>';
            }
            table_html+='</center>';
            table_html+='</td>';
            table_html+='<input type="hidden" id="id_pedidod_'+i+'" name="id_pedidod_'+i+'" value='+articulos[i][18]+'>';
            table_html+='<input type="hidden" id="id_pedido_'+i+'" name="id_pedido_'+i+'" value='+articulos[i][19]+'>';
            table_html+='<input type="hidden" id="cod_art_'+i+'" name="cod_art_'+i+'" value='+articulos[i][2]+'>';
            table_html+='<input type="hidden" id="pedido_nro_'+i+'" name="pedido_nro_'+i+'" value='+articulos[i][21]+'>';
            table_html+='</tr>';

        }
        if((i+2)%2==0) clase='tabla_fila'; else clase='tabla_fila2';
        table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'" style="'+noserie+'"><td></td><td colspan=6>Descuento General</td><td style="text-align:right;"><input type="text" id="doc_descuento" name="doc_descuento" value="'+doc_desc+'" style="width:80px;text-align:right;color:red;" onFocus="this.select();" onKeyUp="recalcular_total();"></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table>';
        $('seleccion').innerHTML='';
        $('seleccion').innerHTML=table_html;

		for(var i=0;i<articulos.length;i++) {
			if(articulos[i][22]=='1')
				validacion_fecha($('fecha_art_'+i));
		}

        recalcular_total();
    }
    
    
    validar=function(){

		if($('iva_incl').checked){
			$('iva_exe').checked=false;
			$('iva_exe').disabled=true;
		}else if($('iva_exe').checked){
			$('iva_incl').checked=false;
			$('iva_incl').disabled=true;
		}else{
			$('iva_incl').checked=false;
			$('iva_incl').disabled=false;
			$('iva_exe').disabled=false;
			$('iva_exe').checked=false;
		}
		
		recalcular_total();
		
}

    
    recalcular_total = function() {
		
		
      
      subtotales=0;
      
      for(i=0;i<articulos.length;i++) {
      
        fila = $('art_fila_'+i);
        cols = fila.getElementsByTagName('td');
    
        if($('check_subt_'+i).checked) {
          articulos[i][10]=1;
        } else {
          articulos[i][10]=0;
        }
        
        if(($('ud_art_'+i).value*1)>0) {
          articulos[i][9]=($('ud_art_'+i).value*1);
          udtrans = ($('ud_art_'+i).value*1);
        } else {
          articulos[i][9]=1;
          udtrans = 1;
        }
        
        
        if(articulos[i][10]==0) {
          subtotal = Math.round(($('valunit_art_'+i).value*($('cant_art_'+i).value*1))/udtrans);
          $('val_art_'+i).value=subtotal;
        } else {
          punit = ($('val_art_'+i).value*udtrans)/($('cant_art_'+i).value*1);
          $('valunit_art_'+i).value=Math.round(punit*1000)/1000;
          subtotal=Math.round($('val_art_'+i).value*1);
          $('val_art_'+i).value=subtotal;
        }
        
        var difmonto  = articulos[i][15]-
                        ($('valunit_art_'+i).value/articulos[i][9]);
        var difvar    = (difmonto)*100/articulos[i][15];
        
        if((difvar>25 || difvar<-25) && !(articulos[i][14]))
          icono_precio='error';
        else 
          icono_precio='magnifier';
        
        $('art_dif_'+i).src='iconos/'+icono_precio+'.png';    
        
        subtotales += subtotal;
        
        
      }
      
      subtotales=subtotales-$('doc_descuento').value;
      
      if(!$('iva_incl').checked && !$('iva_exe').checked) {
        
        _neto=subtotales;
        _total=Math.round(subtotales*_global_iva);
        _iva=_total-_neto;
                
      } else {
		  
			if($('iva_incl').checked){
				
				$('iva_exe').checked=false;
				_total=subtotales;
				_neto=Math.round(subtotales/_global_iva);
				_iva=_total-_neto;
				
			}else{
				
				$('iva_incl').checked=false;
				_total=subtotales;
				_neto=Math.round(subtotales);
				_iva=_total-_neto;
				
			}
      }
      
      
      
      $('valor_total').value=(_total);
      $('val_total').innerHTML=formatoDinero(_total);
      $('val_iva').innerHTML=formatoDinero(_iva);
      $('val_neto').innerHTML=formatoDinero(_neto);
      
    }
		
		guarda_vals=function() {
    
      for(i=0;i<articulos.length;i++) 
      {
          if($('check_subt_'+i).checked)
          {
            articulos[i][10]=1;
          }
          else
          {
            articulos[i][10]=0;
          }
          
          if($('check_ter_'+i).checked)
          {
              articulos[i][20]=1;
          }
          else
          {
              articulos[i][20]=0;
          }

          
		  articulos[i][1]=$('cant_art_'+i).value;
          
          if($('fecha_art_'+i)!=null && validacion_fecha($('fecha_art_'+i)))
			articulos[i][7]=($('fecha_art_'+i).value);


          articulos[i][9]=($('ud_art_'+i).value*1);
          articulos[i][11]=($('valunit_art_'+i).value*1);
          articulos[i][13]=($('serie_art_'+i).value);
          articulos[i][16]=($('partida_art_'+i).value);
          articulos[i][8]=($('val_art_'+i).value*1);
          
      }
      if($('doc_descuento'))
      {
        doc_desc=$('doc_descuento').value*1;
      }
    }
		
		borrar_art = function(numero) {
    
      guarda_vals();
    
      // Borra rapido para procesos internos.
      articulos_tmp=new Array();
		
			for(i=0;i<articulos.length;i++) {
        if(articulos[i][0]!=numero) {
          articulos_tmp[articulos_tmp.length]=articulos[i];
        }
      }
        
      articulos=articulos_tmp;
      
    }
		
		quitar_art = function(numero) {
		
		  guarda_vals();
		  
      // Borra y redibuja para borrado del usuario.
		
        articulos_tmp=new Array();
		
			  for(i=0;i<articulos.length;i++) {
            if(i!=numero) {
              articulos_tmp[articulos_tmp.length]=articulos[i];
            }
        }
        
        articulos=articulos_tmp;
      
      redibujar_tabla();
			
			
		}
		
		limpiar_art = function() {
		
		  // Crea un array nuevo para los art�culos
		  // sobreescribiendo el anterior...
		
			articulos=new Array();
			
			// Quita selecci�n de Pedidos Actual...
      
      $('id_pedido').value=-1;
      $('numero_pedido').value='';
		  $('numero_pedido').style.background='';
		  $('error_pedido_img').style.display='none';
      $('enlazar_pedido_img').style.display='none';
      $('nuevo_pedido_img').style.display='none';
      $('bodega_destino').disabled=false;
      
      // Redibuja la tabla de art�culos
      
			redibujar_tabla();
			
		}

    confirmar_doc = function() {

      if($('proveedor_encontrado').value==0) {
        return;
      }
      
      if($('bodega_doc_asociado_num').value=='') {
        return;
      }
    
      $('carga_documento').innerHTML='<img src="imagenes/ajax-loader1.gif">';
    
      var myAjax = new Ajax.Request(
      'abastecimiento/recepcion_articulos/confirmar_docs.php',
      {
        method: 'get',
        parameters: $('proveedor_encontrado').serialize()+'&'+$('bodega_doc_asociado_num').serialize()+'&'+$('bodega_doc_asociado').serialize(),
        onComplete: function(respuesta) {
          id_doc = respuesta.responseText.evalJSON(true);
          
          if(id_doc==0) {
            $('doc_id').value=0;
            $('carga_documento').innerHTML='<img src="iconos/page_edit.png">';
    
          } else {
          
            if(confirm('El documento ya fu&eacute; recepcionado en el sistema. &iquest;Desea agregar art&iacute;culos a esta recepci&oacute;n?'.unescapeHTML()))  
            {
              $('doc_id').value=id_doc;
              $('carga_documento').innerHTML='<img src="iconos/page_error.png">';
            } else {
              $('doc_id').value=0;
              $('bodega_doc_asociado_num').value='';
              $('carga_documento').innerHTML='';
              $('bodega_doc_asociado_num').focus();
            }
               
          }
          
        }
      }
      );
    
    }
    
    mostrar_info_prod = function()
    {
        if($('provee_bodega').value!='')
        {
            $('doc_id').value=0;
            $('info').innerHTML='Cargando...';
            var myAjax = new Ajax.Updater('info', 'mostrar.php',
            {
                method: 'get',
                parameters: 'tipo=proveedor&id='+
                encodeURIComponent($('provee_bodega').value), evalScripts: true,
                onComplete: function()
                {
                    confirmar_doc();
                }
            }
            );
        }
        else
        {
            $('info').innerHTML='(No ingresado...)';
        }
    }

    bloquear_ingreso=false;
//**************************************************************************************************************************************
    verifica_tabla = function()
    {
            if(bloquear_ingreso) return;
            
            if(!validacion_fecha($('fecha_recep'))) {
			alert('Debe ingresar una fecha v&aacute;lida para la recepci&oacute;n.'.unescapeHTML());
			return;
			}
			
			var now = new Date(); 
			var fecha_recep = $('fecha_recep').value;
			var hoy = $('hoy').value;
		
			if(fecha_recep>hoy){
			alert('No puede ingresar una fecha mayor a Hoy.'.unescapeHTML());
			return;	
			}
			
            prod_bodega_doc_num = document.getElementById('bodega_doc_asociado_num');
            provee_encontrado = document.getElementById('proveedor_encontrado');
            if(articulos.length==0) { alert('No ha seleccionado productos a ingresar.'); return; }
            if(provee_encontrado.value==0) { alert('No se ha ingresado un RUT de Proveedor v&aacute;lido.'.unescapeHTML()); return; }
            if((prod_bodega_doc_num.value*1)==0) { alert('No se ha ingresado un n&uacute;mero de documento v&aacute;lido.'.unescapeHTML()); return; }
            pasarcampos=$('ingreso_producto').serialize();
            cadena_talonarios='';
				
				//if($('total_recep').value!=$('val_total').value){alert('Los valores no coinciden.'); return;}


		guarda_vals();

            for(i=0;i<articulos.length;i++)
            {
                if(comprobar_talonario(articulos[i][0]))
                {
                    if(articulos[i][12]!='')
                    {
                        cadena_talonarios+='&talonario_'+i+'=';
                        cadena_talonarios+=encodeURIComponent(articulos[i][12]);
                    }
                    else
                    {
                        alert('Tiene talonarios ingresados en el listado que a&uacute;n no est&aacute;n definidos.'.unescapeHTML());
                        return;
                    }
                }

				if(articulos[i][22]=='1' && !validacion_fecha($('fecha_art_'+i))) {
						alert('Debe ingresar fecha de vencimiento v&aacute;lida del producto.'.unescapeHTML());
						$('fecha_art_'+i).select();
						$('fecha_art_'+i).focus();
						return;
				}
				
				if(isNaN($('cant_art_'+i).value) || $('cant_art_'+i).value==0){
						alert('Debe ingresar cantidad v&aacute;lida del producto.'.unescapeHTML());
						$('cant_art_'+i).select();
						$('cant_art_'+i).focus();
						return;
				}
				
				if(isNaN($('val_art_'+i).value) || ($('val_art_'+i).value*1)<0){
						alert('Debe ingresar precio v&aacute;lido del producto.'.unescapeHTML());
						$('valunit_art_'+i).select();
						$('valunit_art_'+i).focus();
						return;
				}
				
				if(isNaN($('valunit_art_'+i).value) || ($('valunit_art_'+i).value*1)<0){
						alert('Debe ingresar precio unitario v&aacute;lido del producto.'.unescapeHTML());
						$('valunit_art_'+i).select();
						$('valunit_art_'+i).focus();
						return;
				}
				
				if(isNaN($('ud_art_'+i).value) || ($('ud_art_'+i).value*1)<1){
						alert('Debe ingresar unidad divosora v&aacute;lida del producto.'.unescapeHTML());
						$('ud_art_'+i).select();
						$('ud_art_'+i).focus();
						return;
				}

				/* DESACTIVADO PARA CAPACITACIONES!!

				var difmonto  = articulos[i][15]-
                        		(articulos[i][11]/articulos[i][9]);
       				 var difvar    = (difmonto)*100/articulos[i][15];
	
				if((difvar>10 || difvar<-10) && !(articulos[i][14]) && articulos[i][15] != 0) {
					alert('Debe confirmar que las variaciones de precio ingresadas son correctas.');
					confirmar_precios(articulos[i][0],i);	
					return;
				}*/

            }


            bloquear_ingreso=true;
            var myAjax = new Ajax.Request('abastecimiento/recepcion_articulos/sql.php',
            {
                method: 'post',
                parameters: pasarcampos+'&artnum='+(articulos.length)+cadena_talonarios,
                onComplete: function(pedido_datos)
                {
                    datos = pedido_datos.responseText.evalJSON(true);
                    bloquear_ingreso=false;
                    if(datos[0])
                    {
                        alert('Ingreso de documento realizado exitosamente.');
                        cambiar_pagina('abastecimiento/recepcion_articulos/form.php', function() { visualizador_documentos('Visualizar Recepci&oacute;n', 'doc_id='+encodeURIComponent(datos[1])); } );
                    }
                    else
                    {
                        if(datos[0]==false)
                        {
                            alert('ERROR: \n\n'+datos[1].unescapeHTML());
                        }
                        else
                        {
                            alert('ERROR: \n\n'+pedido_datos.responseText.unescapeHTML());
                        }
                    }
                }
            }
            );
  }

//***********************************************************************************************************************************************************8

conf_talonarios = function(fila_art)
{
     
    
      tipotal = $('id_art_'+fila_art).value;
      canttal = articulos[fila_art][1];
    
      params='tipo=0&art_num=' + fila_art + '&tipotalonario_id=' + tipotal + 
      '&nro_talonarios=' + canttal + 
      '&cadena_actual='+encodeURIComponent(articulos[fila_art][12]);
    
      top=Math.round(screen.height/2)-100;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('abastecimiento/ingreso_talonarios.php?'+params,
      'win_talonarios', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=200, '+
      'top='+top+', left='+left);
      
      new_win.focus();
      
    }
    
    confirmar_precios = function(art_id, fila) {
    
      punit = $('valunit_art_'+fila).value*1;
     
      params='bodega='+($('prod_bodega').value*1)+'&art_id='+(art_id*1)+
              '&punit='+encodeURIComponent(punit);
    
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('abastecimiento/recepcion_articulos/confirmar_punit.php?'+
      params,
      'win_punit', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
		}
		
		aceptar_precio = function(art_id, acepta_valor) {
    
      for(i=0;i<articulos.length;i++) {
      
        if(articulos[i][0]==art_id) {
          articulos[i][14] = acepta_valor;
          recalcular_total();
          return;
        }
      
      }
    
    }
    
    aceptado_precio = function(art_id) {

      for(i=0;i<articulos.length;i++) {
      
        if(articulos[i][0]==art_id) {
          if(articulos[i][14]) return true; else return false;
        }
      
      }

      return false;
    
    }
    
    buscar_oc = function() {
    
      top=(screen.availHeight/2)-250;
      left=(screen.availWidth/2)-300;
      
      params='orden=orden_compra_num&';
      params+='prov_rut=provee_bodega&';
      params+='func=cargar_oc';
      
      win = window.open(
      'abastecimiento/busca_orden.php?'+params,
      'win_ordenes', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=600, height=450, '+
      'top='+top+', left='+left);
      
      win.focus();
    
    }
    
    cargar_oc = function() {
        //alert($('orden_compra_num').value*1);
        pedidos=false;
        $('orden_compra_num').value=$('orden_compra_num').value.toUpperCase();
        var myAjax = new Ajax.Request('abastecimiento/cargar_orden.php',
        {
            method: 'get',
            parameters: 'orden_numero='+encodeURIComponent($('orden_compra_num').value),
            onComplete: function(resp) {
                orden_compra=resp.responseText.evalJSON(true);
                if(orden_compra)
                {
                    if(orden_compra.cabecera[0].orden_estado<2){
                        $('orden_id').value=orden_compra.cabecera[0].orden_id;
                	$('orden_compra_num').value=orden_compra.cabecera[0].orden_numero;
                	$('provee_bodega').value=orden_compra.proveedor[0].prov_rut;
                	mostrar_info_prod();
                	$('provee_bodega').disabled=true;
                	$('prov_lock').style.display='';
                	$('orden_select').style.display='';
                        if(orden_compra.recepcionado==false){
                            $('nota').innerHTML='Items con C&oacute;digo: '+orden_compra.cabecera[0].articulos+'<br/><br/>Items sin C&oacute;digo: '+orden_compra.cabecera[0].servicios+'<br/><br/>TOTAL ITEMS: '+((orden_compra.cabecera[0].articulos*1)+(orden_compra.cabecera[0].servicios*1));
                        } else {
                            var string_nro_recep="";
                            if(orden_compra.documentos!=false) {
                                for(var z=0;z<orden_compra.documentos.length;z++) {
                                    string_nro_recep+=" | "+orden_compra.documentos[z]['doc_id'];
				}
                            }
                            $('nota').innerHTML='Items con C&oacute;digo: '+orden_compra.cabecera[0].articulos+'<br/><br/>Items sin C&oacute;digo: '+orden_compra.cabecera[0].servicios+'<br/><br/><font color="green"><i>Items Recepcionados: '+orden_compra.recepcionado[0].count+' <br/>Nro Recepci&oacute;n: '+string_nro_recep+'</i></font><br/><br/>TOTAL ITEMS: '+((orden_compra.cabecera[0].articulos*1)+(orden_compra.cabecera[0].servicios*1));
                        }
                        /*
                        $('orden_iva').value=orden_compra.cabecera[0].orden_iva;
                        if($('orden_iva').value=='1.10') {
                            $('imp').checked=true;
			}
                        $('orden_total').value=orden_compra.cabecera[0].orden_total;
			doc_desc=orden_compra.cabecera[0].orden_descuento;
			doc_cargo=orden_compra.cabecera[0].orden_cargo;
			doc_imp=orden_compra.cabecera[0].orden_imp_especifico;
                        */
                        var det=orden_compra.detalle;
                        guarda_vals();
                	articulos=[];
                        for(var i=0;i<det.length;i++) {
                            num=articulos.length;
                            articulos[num]=new Array();
                            articulos[num][0]=det[i].art_id;
                            articulos[num][1]=det[i].ordetalle_cant-det[i].recepcionado;
                            articulos[num][2]=det[i].art_codigo;
                            articulos[num][3]=det[i].art_codigo;
                            articulos[num][4]=det[i].art_glosa;
                            articulos[num][5]=det[i].art_stock;
                            articulos[num][6]=0;
                            articulos[num][7]='';
                            articulos[num][8]=det[i].ordetalle_subtotal;
                            articulos[num][9]=1;
                            articulos[num][10]=0;
                            articulos[num][11]=(det[i].ordetalle_subtotal/det[i].ordetalle_cant);
                            articulos[num][12]='';
                            articulos[num][13]='';
                            articulos[num][14]=false;
                            articulos[num][15]=det[i].art_punit_med;
                            articulos[num][16]='';
                            articulos[num][17]=det[i].ordetalle_cant;
                            articulos[num][18]=0;
                            articulos[num][19]=0;
                            articulos[num][20]=0;
                            articulos[num][21]=0;
                            articulos[num][22]=det[i].art_vence;
                            articulos[num][23]=det[i].forma_nombre;
                            //alert(orden_compra.cabecera[0].orden_estado);
			}
                        redibujar_tabla();
                        if(orden_compra.cabecera[0].servicios>0){
                            alert("La Orden de Compra ingresada en su detalle presenta;\n- articulos sin codigos.-\n - Servicios de los cuales no se pueden ingresar en este modulo.-");
                            return;
			}
                        if(orden_compra.cabecera[0].articulos==orden_compra.recepcionado[0].count){
                            alert("La Orden de Compra ingresada, pesenta una RECEPCION COMPLETA de los art\u00edculos que se\u00f1ala.\n No se pueden realizar mas recepciones asociados a esta orden de compra.\n El NUMERO DEL LA RECEPCION es : "+string_nro_recep+"");
                            return;
			} else {
                            if(orden_compra.recepcionado[0].count>0){
                                alert("La Orden de Compra ingresa, presenta una recepci\u00f3n parcial de los articulos, El numero de La recepci\u00f3n es: "+string_nro_recep+"");
				return;
                            }
			}
                    } else if(orden_compra.cabecera[0].orden_estado==2) {
                        alert(('La Orden de Compra '+orden_compra.cabecera[0].orden_numero+' ha sido recepcionada completamente.').unescapeHTML());
			$('orden_compra_num').value='';
			return;
                    } else {
                        alert('La Orden de Compra '+orden_compra.cabecera[0].orden_numero+' se encuentra ANULADA');
                        $('orden_compra_num').value='';
			return;
                    }
                } else {
                    if($('orden_compra_num').value!='') {
                        $('nota').innerHTML="";
                        confirmar=confirm(('La &Oacute;rden de Compra a la cual se Hace referencia no ha sido ingresada. Est&aacute; Seguro de ingresar la &Oacute;rden de Compra de Todas Maneras; Est&aacute; opci&oacute;n es solo para &oacute;rdenes de compra externas.').unescapeHTML());
                        if (confirmar){
                            $('orden_id').value=0;
                            $('provee_bodega').disabled=false;
                            $('provee_bodega').value='';
                            $('info').innerHTML='(No ingresado...)';
                            $('prov_lock').style.display='none';
                            $('orden_select').style.display='none';
                            $('provee_bodega').focus();
                        } else {
                            //alert(('La &Oacute;rden de Compra a la Cual se Hace referencia no ha sido ingresada').unescapeHTML());
                            $('orden_compra_num').value='';
                            $('orden_id').value='';
                            $('provee_bodega').disabled=false;
                            $('provee_bodega').value='';
                            $('info').innerHTML='(No ingresado...)';
                            $('prov_lock').style.display='none';
                            $('orden_select').style.display='none';
                            $('orden_compra_num').focus();
                        }
                    }
                }
            }
        });
    }
    validacion_fecha($('fecha_recep'));
    </script>
		
		
<center>

	<table><tr><td>
	
	<div class='sub-content'>
                <div class='sub-content'>
                    <img src='iconos/page_gear.png'>
                    <b>Recepci&oacute;n de Art&iacute;culos</b>
                </div>

    <form id='ingreso_producto' name='ingreso_producto' onSubmit='return false;'>
        <input type='hidden' id='talonarios_datos' name='talonarios_datos' value=''>
		<table>
            <tr>
                <td valign='top'>
                <center>
                    <table>
                        <tr>
                            <td valign='top'>
                                <div id='listado' class='sub-content'><br>
                                    <table>
                                        <tr>
                                            <td style='text-align: right;' class='form_titulo'>Bodega Ingreso:</td>
                                            <td class='form_campo'>
                                                <select id='prod_bodega' name='prod_bodega'>
                                                    <?php echo $bodegashtml; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align:right;'class='form_titulo'>Intermediaci&oacute;n:</td>
                                            <td class='form_campo'>
                                                <select id='prod_interm' name='prod_interm'>
                                                 <option value=''>(Sin Intermediaci&oacute;n...)</option>
                                                 <option value='CENABAST'>CENABAST</option>
                                                 <option value='SSMOC'>SSMOC</option>
                                                 <option value='PROG. MINISTERIAL'>PROG. MINISTERIAL</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;' class='form_titulo'>&Oacute;rden de Compra Nro.:</td>
                                            <td class='form_campo'>
                                                <input type='hidden' id='orden_id' name='orden_id' value=''>
                                                <input type='hidden' id='valor_total' name='valor_total' value=''>
                                                <input type='text' id='orden_compra_num' name='orden_compra_num' size=13 onChange='cargar_oc();'>
                                                <img src="iconos/zoom_in.png" onClick='buscar_oc();' style='cursor:pointer;'>
                                                <span id='orden_select' style='display:none;'>
                                                    <img src='iconos/accept.png'>
                                                    <img src='iconos/eye.png' onClick='abrir_orden($("orden_id").value);' style='cursor:pointer;'>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;' class='form_titulo'>RUT del Proveedor:</td>
                                            <td class='form_campo'>
                                                <input type='text' name='provee_bodega' id='provee_bodega' size=13	onBlur='mostrar_info_prod();'>
                                                <img src='iconos/lock.png' id='prov_lock' style='display:none;'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;' class='form_titulo'>Documento Asociado:</td>
                                            <td class='form_campo'>
                                                <select id='bodega_doc_asociado' name='bodega_doc_asociado' onChange='confirmar_doc();'>
                                                    <option value='0' SELECTED>Gu&iacute;a de Despacho</option>
                                                    <option value='1'>Factura</option>
                                                    <option value='2'>Boleta</option>
                                                    <option value='3'>Pedido</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='text-align: right;' class='form_titulo'>N&uacute;mero Documento:</td>
                                            <td class='form_campo'>
                                                <input type='hidden' id='doc_id' name='doc_id' value=0>
                                                        <table cellpadding=0 cellspacing=0>
                                                        <tr>
                                                            <td>
                                                                <input type='text' id='bodega_doc_asociado_num' size=13 onChange='confirmar_doc();' name='bodega_doc_asociado_num'>
                                                            </td>
                                                            <td>
                                                                &nbsp;&nbsp;<span id='carga_documento'></span>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style='text-align: right;' class='form_titulo'>Ver Informaci&oacute;n:</td>
                                                    <td class='form_campo'>
                                                        <select id='veringr' name='veringr' onClick='actualizar_tabla();'>
                                                            <option value=0>Cantidad y Precios</option>
                                                            <option value=1>N&uacute;meros de Serie</option>
                                                            <option value=2>N&uacute;meros de Partida</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
																	<td style='text-align: right;' class='form_titulo'>Observaciones:</td>
																	<td class='form_campo'><textarea id='observaciones' name='observaciones' cols=30 rows=1></textarea></td>                                                
                                                </tr>
                                                <!--<tr>
																	<td style='text-align: right;' class='form_titulo'>$ Total Recepcionar:</td>
																	<td class='form_campo'><input tyle='text' id='total_recep' name='total_recep' size=15></td>                                                
                                                </tr>-->
                                                <tr>	
                                                	<td style='text-align:right;' class='form_titulo'>Fecha de Recepci&oacute;n F&iacute;sica:</td>
                                                	<td class='form_campo'><input type='text' size=10 id='fecha_recep' name='fecha_recep' 
														style='text-align:center;'
														value='' onKeyUp='validacion_fecha(this);'
														 /><img src='iconos/date_magnify.png' id='fecha_boton'>
														 <input type='hidden' id='hoy' name='hoy' value='<?php echo date("d/m/Y"); ?>' >
													</td>
                                                </tr>
                                            </table>
                                        <br>
                                    </div>
                                </td>
                                <td valign='top'>
                                    <div class='sub-content'>
                                        <center>
                                            <div class='sub-content'>
                                                <img src='iconos/lorry.png'>
                                                <b>Datos del Proveedor</b>
                                            </div>
                                            <div id='info' class='sub-content2' style='width: 260px;'>
                                                <input type='hidden' name='proveedor_encontrado'  id='proveedor_encontrado' value='0'>
                                                (No ingresado...)
                                            </div>
                                            <div id='nota' class='sub-content2' style='color:blue;text-align:center;font-weight:bold;font-size:12px;'>
                    
											</div>
                                        </center>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <div class='boton' id='agrega'>
                            <table>
                                <tr>
                                    <td>
                                        <img src='iconos/add.png'>
                                    </td>
                                    <td>
                                        <a href='#'
                                        onClick='buscar_codigos_barra($("prod_bodega").value,seleccionar_articulo,1, "buscar_arts_stock", "prod_bodega");'>
                                        Agregar Productos...</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class='sub-content'>
                            <div class='sub-content'>
                                <img src='iconos/layout.png'>
                                <b>Detalle de Productos</b>
                                (<input type='checkbox' id='iva_incl' name='iva_incl' onChange='validar();'>
                                Valores con I.V.A. Inclu&iacute;do )&nbsp;
                                (<input type='checkbox' id='iva_exe' name='iva_exe' onChange='validar();'>
                                Valores Exentos de I.V.A. )
                            </div>
                            <div name='seleccion' id='seleccion' class='sub-content2'>
                                <table width='100%'>
                                    <tr class='tabla_header' style='font-weight: bold;'>
                                        <td style='width:80px;'>C&oacute;digo Int.</td>
                                        <td style='width:150px;'>Nombre</td>
                                        <td>Fecha de Venc.</td>
                                        <td>Cant.</td>
                                        <td>Cant Sol</td>
                                        <td>Ud. Div.</td>
                                        <td>P. Unit.</td>
                                        <td>Subtotal</td>
                                        <td>Acciones</td>
                                    </tr>
                                </table>
                            </div>
                            <div class='tabbed_content'>
                                <table width=100%>
                                    <tr>
                                        <td style='text-align: center; width:60%;' rowspan=3>Total General</td>
                                        <td style='text-align: right;'>Neto:</td>
                                        <td id='val_neto' style='text-align: right; width:100px;'>$0.-</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align: right;'>I.V.A.:</td>
                                        <td id='val_iva' style='text-align: right; width:100px;'>$0.-</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align: right;'>Total:</td>
                                        <td id='val_total' style='text-align: right; width:100px;'>$0.-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </center>
                    <div class='sub-content'>
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
                                                        <a href='#' onClick='verifica_tabla();'>Ingresar Art&iacute;culos...</a>
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
                                                        <a href='#' onClick="cambiar_pagina('abastecimiento/recepcion_articulos/form.php');">
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
                </td>
            </tr>
        </table>
    </form>
    	</div>
</td></tr></table>

</center>
<script>
	
	Calendar.setup({
        inputField     :    'fecha_recep',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton'
    });
    
    
    
</script>
