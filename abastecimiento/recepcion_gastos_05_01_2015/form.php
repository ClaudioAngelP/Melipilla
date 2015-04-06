<?php

  require_once("../../conectar_db.php");

  $gastoshtml = desplegar_opciones("gasto_externo", "gastoext_id, gastoext_nombre",'1','1=1', 'ORDER BY gastoext_nombre');

?>

		<script type='text/javascript'>
		
		var doc_desc=0;
		
		redibujar_tabla = function() {
      
      // SELECCION CON FECHAS DE VENCIMIENTO
      
      // Redibuja Tabla
      
      table_html="<table width=100%><tr class='tabla_header' style='font-weight: bold;'><td style='width:200px;'>Descripci&oacute;n del Gasto</td><td>Cant.</td><td>UD</td><td>P. Unit.</td><td style='width:100px;'>Subtotal</td><td>Item Pres.</td></tr>";
    
      for(i=0;i<($('cant_gasto').value*1);i++) {
      
        if(i%2==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        chequeado='';
        
        icono_item='database_error';
        
        table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'" id="gasto_fila_'+i+'"><input type="hidden" id="gasto_item_'+i+'" name="gasto_item_'+i+'" value=""><td style="text-align:right;"><input type="text" id="gasto_glosa_'+i+'" name="gasto_glosa_'+i+'" value="" size=50 maxlength=150></td><td><input type="text" id="gasto_cant_'+i+'" name="gasto_cant_'+i+'" style="text-align: right;" size=4 value="1" onFocus="this.select();" onKeyUp="recalcular_total();" onChange="if(this.value<=0) this.value=1;"></center></td><td><input type="text" id="gasto_unidad_'+i+'" name="gasto_unidad_'+i+'" size=6 value="" onFocus="this.select();"></center></td><td><center><input type="text" id="gasto_valunit_'+i+'" name="gasto_valunit_'+i+'" style="text-align: right;" size=8 value="0" onFocus="this.select();" onKeyUp="recalcular_total();"></center></td><td style="text-align: right; font-size: 10px;"><center><input type="checkbox" id="check_subt_'+i+'" name="check_subt_'+i+'" '+chequeado+'><input type="text" id="gasto_val_'+i+'" name="gasto_val_'+i+'" style="text-align: right;" size=8 value="0" onFocus="this.select();" onKeyUp="recalcular_total();"></center></td><td><center><img src="iconos/'+icono_item+'.png" onClick="seleccionar_item('+i+');" id="gasto_icono_'+i+'"></center></td></tr>';
        
      }
      
      if((i+2)%2==0) clase='tabla_fila'; else clase='tabla_fila2';
      
      table_html+='<tr class="'+clase+'" onMouseOver="this.className=\'mouse_over\';" onMouseOut="this.className=\''+clase+'\'" ><td colspan="3";></td><td >Descuento Neto:</td><td style="text-align:center;"><input type="text" id="doc_descuento" name="doc_descuento" value="'+doc_desc+'" style="width:80px;text-align:right;color:red;" onFocus="this.select();" onKeyUp="recalcular_total();"></td><td>&nbsp;</td></tr>';
        
      table_html+='</table>';
      
      table_html+='</table>';
      
      $('seleccion').innerHTML='';
      $('seleccion').innerHTML=table_html;
      
      recalcular_total();
    
    }
    
    recalcular_total = function() {
      
      subtotales=0;
      
      for(i=0;i<($('cant_gasto').value*1);i++) {
      
        fila = $('gasto_fila_'+i);
        cols = fila.getElementsByTagName('td');
        
        if(!$('check_subt_'+i).checked) {
          subtotal = 
          Math.round($('gasto_valunit_'+i).value*$('gasto_cant_'+i).value);
          $('gasto_val_'+i).value=subtotal;
        } else {
          punit = ($('gasto_val_'+i).value)/$('gasto_cant_'+i).value;
          $('gasto_valunit_'+i).value=Math.round(punit*1000)/1000;
          subtotal=Math.round($('gasto_val_'+i).value*1);
        }
        
        subtotales += subtotal;
        
        
      }
      
      subtotales=subtotales-$('doc_descuento').value;
      
      
      if($('iva_incl').checked) {
        
        _total=subtotales;
        _neto=Math.round(subtotales/_global_iva);
        _iva=_total-_neto;
                
      } else {

        _neto=subtotales;
        _total=Math.round(subtotales*_global_iva);
        _iva=_total-_neto;
        
      }
      
      $('val_total').innerHTML=formatoDinero(_total);
      $('val_iva').innerHTML=formatoDinero(_iva);
      $('val_neto').innerHTML=formatoDinero(_neto);
      
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
        parameters: 'proveedor_encontrado='+$('prov_id').value+'&'+$('bodega_doc_asociado_num').serialize()+'&'+$('bodega_doc_asociado').serialize(),
        onComplete: function(respuesta) {
          id_doc = respuesta.responseText.evalJSON(true);
          
          if(id_doc==0) {
            $('doc_id').value=0;
            $('carga_documento').innerHTML='<img src="iconos/page_edit.png">';
    
          } else {
          
            /*if(confirm('El documento ya fu&eacute; recepcionado en el sistema. &iquest;Desea agregar art&iacute;culos a esta recepci&oacute;n?'.unescapeHTML()))  
            {*/
              $('doc_id').value=id_doc;
              $('carga_documento').innerHTML='<img src="iconos/page_error.png">';
            /*} else {
              $('doc_id').value=0;
              $('bodega_doc_asociado_num').value='';
              $('carga_documento').innerHTML='';
              $('bodega_doc_asociado_num').focus();
            }*/
               
          }
          
        }
      }
      );
    
    }

		bloquear_ingreso=false;
		
    verifica_tabla = function() {
		
		
		
    
      if(bloquear_ingreso) return;
      
      if(!validacion_fecha($('fecha1'))) {
			alert('Debe ingresar una fecha v&aacute;lida para la recepci&oacute;n.'.unescapeHTML());
			return;
			}
			
			var now = new Date(); 
			var fecha_recep = $('fecha1').value;
			var hoy = $('hoy').value;
		
			/*if(fecha_recep>hoy){
			alert('No puede ingresar una fecha mayor a Hoy.'.unescapeHTML());
			return;	
			}*/
    
      prod_bodega_doc_num = document.getElementById('bodega_doc_asociado_num');
			prov_id = document.getElementById('prov_id');
			
			if(prov_id.value==0) { alert('No se ha ingresado un RUT de Proveedor v&aacute;lido.'.unescapeHTML()); return; }
			
			if((prod_bodega_doc_num.value*1)==0) { alert('No se ha ingresado un n&uacute;mero de documento v&aacute;lido.'.unescapeHTML()); return; }
			
			for(var i=0;i<$('cant_gasto').value;i++) {
        if($('gasto_valunit_'+i).value!=0) {
          if($('gasto_item_'+i).value=='') {
            alert('No ha seleccionado el item presupuestario de todos los gastos ingresados.');
            return;
          }
        }
      }
      
      pasarcampos=$('ingreso_gasto').serialize();
			
      bloquear_ingreso=true;
			
			var myAjax = new Ajax.Request(
			'abastecimiento/recepcion_gastos/sql.php', 
			{
				method: 'post', 
				parameters: pasarcampos+'&gastonum='+$('cant_gasto').value,
				onComplete: function(pedido_datos) {
				
				 //alert(pedido_datos.responseText); 
				  
				  datos = pedido_datos.responseText.evalJSON(true);
	        
          		bloquear_ingreso=false;
  			
					if(datos[0]) {
					
						alert('Ingreso de documento realizado exitosamente.');
						cambiar_pagina('abastecimiento/recepcion_gastos/form.php', function() { visualizador_documentos('Visualizar Recepci&oacute;n', 'doc_id='+encodeURIComponent(datos[1])); } );
						
					} else if(datos[0]==false){
					
					   alert('ERROR: \n\n'+datos[1].unescapeHTML());
					
					} else {
					
					 	alert('ERROR: \n\n'+pedido_datos.responseText.unescapeHTML());
						
					}
					
				}
			}		
			);	
			
		}
		
		
		seleccionar_item = function(fila) {
    
      params= 'item='+encodeURIComponent($('gasto_item_'+fila).value)+
              '&fila='+encodeURIComponent(fila);
    
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('abastecimiento/recepcion_gastos/seleccionar_item.php?'+
      params,
      'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
		}

		seleccionar_centro = function() {
    
      params='centro_ruta='+encodeURIComponent($('centro_ruta').value);
    
      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-200;
      
      new_win = 
      window.open('abastecimiento/recepcion_gastos/seleccionar_centro.php?'+
      params,
      'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=400, height=300, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
		}


  mostrar_proveedor=function(datos) {
    $('prov_id').value=datos[3];
    $('nombre_prov_id').value=datos[2].unescapeHTML();
  }

  
  liberar_proveedor=function() {
    $('prov_id').value=0;
    $('nombre_prov_id').value='';
    $('_prov_id').value='';
    $('_prov_id').focus();
  }

		
		autocompletar_proveedores = new AutoComplete(
    '_prov_id', 
    'autocompletar_sql.php',
    function() {
      if($('_prov_id').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('_prov_id').value)
      }
    }, 'autocomplete', 350, 200, 250, 1, 2, mostrar_proveedor);
  

    ver_tipo_campos = function() {
    
      if($('tipo_dist').value==0) {
        $('centro_nombre').style.display='';
        $('centro_ruta_icono').style.display='';
        $('centro_grupo').style.display='none';
      } else {
        $('centro_nombre').style.display='none';
        $('centro_ruta_icono').style.display='none';
        $('centro_grupo').style.display='';
      }
    
    }
    
    validacion_fecha($('fecha1'));

    </script>
		
		
		<center>
		<table><tr><td>
	
	<div class='sub-content'>
                <div class='sub-content'>
                    <img src='iconos/page_gear.png'>
                    <b>Recepci&oacute;n de Consumo Inmediato</b> 
                </div>
		<form id='ingreso_gasto' name='ingreso_gasto' 
    onSubmit='return false;'>
		
		<table><tr><td valign='top'>
		
		
		<div id='listado' class='sub-content'><br>
		
		<table>
		<tr>
    <td class='derecha'>
    Tipo de Distribuci&oacute;n:
    </td>
    <td>
    <select Id='tipo_dist' name='tipo_dist' onClick='ver_tipo_campos();'>
    <option value=0>Gasto Directo a Centro de Costo</option>
    <option value=1>Gasto Subdistribuido</option>
    </select>
    </td>
    </tr>
		<tr><td class='derecha'>Centro de Resp./Costo:</td>
		<td>
		<input type='hidden' id='centro_ruta' name='centro_ruta' value=''>
    
		<input type='text' id='centro_nombre' name='centro_nombre' value='' disabled
    size=70>
    <img src='iconos/zoom_in.png' onClick='seleccionar_centro();'
    id='centro_ruta_icono' name='centro_ruta_icono'>
    
    <select id='centro_grupo' name='centro_grupo' style='display: none;'>
    <?php echo $gastoshtml; ?>
    </select>
    
    </td></tr>
		<tr><td class='derecha'>Fecha Recepci&oacute;n F&iacute;sica:</td>
		<td>
    <input type='text' name='fecha1' id='fecha1' size=10
    style='text-align: center;' value='' onKeyUp='validacion_fecha(this);'>
    <img src='iconos/date_magnify.png' id='fecha1_boton'>
    <input type='hidden' id='hoy' name='hoy' value='<?php echo date("d/m/Y"); ?>' >
    </td>
    </tr>
    
    <tr><td class='derecha'>&Oacute;rden de Compra Nro.:</td>
		<td><input type='text' id='orden_compra_num' name='orden_compra_num'>
    </td></tr>
		
    <tr>
    
    <td style='text-align: right;'>Proveedor:</td>
    <td colspan=3>
    <input type='hidden' id='prov_id' name='prov_id' value=0>
    <input type='text' id='_prov_id' name='_prov_id' 
    onDblClick='liberar_proveedor();' size=15>
    <input type='text' id='nombre_prov_id' name='nombre_prov_id' 
    size=55 DISABLED>
    </td>
    </tr>

    <tr><td class='derecha'>Documento Asociado:</td>
		<td>
		<select id='bodega_doc_asociado' name='bodega_doc_asociado' 
      onChange='confirmar_doc();'>
		<option value='0' SELECTED>Gu&iacute;a de Despacho</option>
		<option value='1'>Factura</option>
		<option value='2'>Boleta</option>
		<option value='3'>Pedido</option>
        <option value='4'>Resoluci&oacute;n (Donaciones)</option>
		</select>
		</td></tr>
		<tr><td class='derecha'>N&uacute;mero Documento:</td>
		<td>
    
    <input type='hidden' id='doc_id' name='doc_id' value=0>
    
    <table cellpadding=0 cellspacing=0><tr><td>
    <input type='text' id='bodega_doc_asociado_num' size=13
    onChange='confirmar_doc();' name='bodega_doc_asociado_num'>
    </td><td>
    &nbsp;&nbsp;<span id='carga_documento'></span>
    </td></tr>
    
    </table>
    
    
    
    </td></tr>
		
    <tr>
    <td style='text-align: right;'>Cantidad de Gastos:</td>
    <td>
    <input type='text' id='cant_gasto' name='cant_gasto' value=1 
    size=5 style='text-align: right;' onChange='redibujar_tabla();'
    onClick='this.select();'>
    </td>
    </tr>
    
    <tr>
	 <td style='text-align: right;'>Observaciones:</td>
	 <td><textarea id='observaciones' name='observaciones' cols=30 rows=1></textarea></td>                                                
    </tr>
    
    </table>
		<br>
		</div>
		
		<div class='sub-content'>
    <div class='sub-content'>
    <img src='iconos/layout.png'> <b>Detalle de Gastos</b>
    ( <input type='checkbox' id='iva_incl' name='iva_incl' onChange='recalcular_total();'> Valores con I.V.A. Inclu&iacute;do )
    </div>
    <div name='seleccion' id='seleccion' class='sub-content2'>
    
    
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
    
    
    
    
    
   
    </td></tr></table>

		<center>
    <table><tr><td>
		
		
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'>Ingresar Art&iacute;culos...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick="cambiar_pagina('abastecimiento/recepcion_gastos/form.php');">
		Limpiar Formulario...</a>
		</td></tr></table>
		
		</td></tr></table>
		</center>
		</div>
		
		</td></tr></table>
		
		</form>
		
		<script>

		  Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
      });

		  redibujar_tabla();

		</script>
		
