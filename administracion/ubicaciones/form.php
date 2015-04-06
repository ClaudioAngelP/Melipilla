
  <script>
	
	cargar_listado = function() {
	
		var myAjax = new Ajax.Updater(
			'listado', 
			'administracion/ubicaciones/listado_ubicaciones.php', 
			{
				method: 'get', 
				parameters: 'buscar='+serializar('bodegas_filtro'),
				evalScripts: true
				
			}
			
			);
	
	}
	
	habilitar_radios = function() {
    if($('bodega_consume').disabled) {
    
      $('servicios').disabled=true;
      
    } else {
      
      if($('bodega_consume').checked) {
        $('servicios').disabled=false;
      } else {
        $('servicios').disabled=true;
      }
      
      
    }
    
  }
	
	seleccionar_bodega = function(idbodega) {
	
		var myAjax = new Ajax.Request(
			'administracion/ubicaciones/bajar_ubicacion.php', 
			{
				method: 'get', 
				parameters: 'buscar='+(idbodega*1),
				onComplete: function(pedido_datos) {
				
				  try {
				
					datos=eval(pedido_datos.responseText);
					
					bod_id_text.value=datos[0];
					bod_nombre_text.value=datos[1].unescapeHTML();
					bod_ubica_text.value=datos[2].unescapeHTML();
					if(datos[6]!='') {
						bod_costoid_text.value=datos[5];
						bod_costoglosa_text.innerHTML=datos[6].unescapeHTML();
						bod_costoicono_text.src='iconos/tick.png';
					} else {
						bod_costoid_text.value='';
						bod_costoglosa_text.innerHTML='(sin asignar...)';
						bod_costoicono_text.src='iconos/exclamation.png';
					}
					
					$('selec_centro').style.display='none';
					$('bodega_costosel').value='Asignar...';
					
					if(datos[3]=='f') { provee=false; } else { provee=true; }
					if((datos[4]*1)==0) { consum=false; } else { consum=true; }
					if(datos[7]=='f') { inter=false; } else { inter=true; }
					if(datos[8]=='f') { despacho=false; } else { despacho=true; }
					if(datos[9]=='f') { controlados=false; } else { controlados=true; }
					if(datos[10]=='t') { repone=true; } else { repone=false; }
					
					bod_proveedor_text.checked=provee;
					bod_inter_text.checked=inter;
					bod_despacho_text.checked=despacho;
					bod_controlados_text.checked=controlados;
					bod_consume_text.checked=consum;
		      bod_repone_text.checked=repone;
		
					bod_nombre_text.disabled=true;
					bod_ubica_text.disabled=true;
					bod_proveedor_text.disabled=true;
					bod_inter_text.disabled=true;
		      bod_despacho_text.disabled=true;
		      bod_controlados_text.disabled=true;
					bod_consume_text.disabled=true;
					bod_costo_text.disabled=true;
				  bod_repone_text.disabled=true;
				  
				  if((datos[4]*1)!=0) {
            $('servicios').value=(datos[4]*1);
          } else {
            $('servicios').value=1;
          }
          
          habilitar_radios();
					
					$('editar_boton').style.display='';
					$('borrar_boton').style.display='';
					$('guardar_boton').style.display='none';
		      
		      } catch(err) {
            alert(err);
          }
		
		
				}
				
			}
			
			);
	
	}
	
	agregar_usuario = function () {
							
		bod_id_text.disabled=false;
		bod_nombre_text.disabled=false;
		bod_ubica_text.disabled=false;
		bod_proveedor_text.disabled=false;
		bod_inter_text.disabled=false;
		bod_despacho_text.disabled=false;
		bod_controlados_text.disabled=false;
		bod_consume_text.disabled=false;
		bod_costo_text.disabled=false;
		bod_repone_text.disabled=false;
		
		bod_id_text.value='';
		bod_nombre_text.value='';
		bod_ubica_text.value='';
		bod_proveedor_text.checked=false;
		bod_inter_text.checked=false;
		bod_despacho_text.checked=false;
		bod_controlados_text.checked=false;
		bod_consume_text.checked=false;
		
		
		bod_costoid_text.value='';
		bod_costoglosa_text.innerHTML='(sin asignar...)';
		bod_costoicono_text.src='iconos/exclamation.png';
					
				
		$('guardar_texto').innerHTML = 'Guardar Bodega Nueva...';
		$('guardar_boton').style.display='';
		$('borrar_boton').style.display='none';
		$('editar_boton').style.display='none';
		
		$('selec_centro').style.display='none';
		$('bodega_costosel').value='Asignar...';
			
		bod_nombre_text.focus();
		
	}
	
	editar_usuario = function() {
		
		bod_id_text.disabled=false;
		bod_nombre_text.disabled=false;
		bod_ubica_text.disabled=false;
		bod_proveedor_text.disabled=false;
		bod_inter_text.disabled=false;
		bod_despacho_text.disabled=false;
		bod_controlados_text.disabled=false;
		bod_consume_text.disabled=false;
		bod_costo_text.disabled=false;
		bod_repone_text.disabled=false;
		
					
		$('guardar_texto').innerHTML = 'Guardar Cambios a Bodega...';
		$('guardar_boton').style.display='';
		
		habilitar_radios();
		
		bod_nombre_text.focus();
		bod_nombre_text.select();
				
	
	}
	
	borrar_usuario = function() {
		
		confirma=confirm('¿Est&aacute; seguro que desea eliminar este usuario? - No hay opciones para deshacer.'.unescapeHTML());
		
	}
	
	seleccionar_centro = function () {
    
      if($('selec_centro').style.display=='none') {
	  	  $('selec_centro').style.display='inline';
      	posicion=findPos($('bodega_costoicono'));
      	$('selec_centro').style.top=posicion[1]+25;
      	$('selec_centro').style.left=posicion[0];
      	$('bodega_costosel').value='Cerrar...';
		    $('selec_centro').inspect();
      } else {
	  	  $('selec_centro').style.display='none';
		    $('bodega_costosel').value='Asignar...';
      }
      
  }
  
  asignar_centro = function(id, glosa) {
  
  $('bodega_costoid').value=id;
	$('bodega_costo').innerHTML=glosa;
	$('bodega_costoicono').src='iconos/tick.png';
	
	$('selec_centro').style.display='none';
	$('bodega_costosel').value='Asignar...';

  }
	
	verifica_tabla = function() {
		
			if(trim(bod_nombre_text.value)=='') {
				alert('El campo Nombre est&aacute; vac&iacute;o.'.unescapeHTML());
				bod_nombre_text.select();
				return;
			}	
			
			var myAjax = new Ajax.Request(
			'administracion/ubicaciones/sql.php', 
			{
				method: 'get', 
				parameters: serializar_objetos('registro'),
				onComplete: function(pedido_datos) {
				
					if(pedido_datos.responseText=='1') {
					
						bod_id_text = document.getElementById('bodega_id');
		
						if(bod_id_text.value='') {
							alert('Ingreso de ubicaci&oacute;n realizado exitosamente.'.unescapeHTML());
						} else {
							alert('Edici&oacute;n de ubicaci&oacute;n realizado exitosamente.'.unescapeHTML());
						}
							
						bod_nombre_text.disabled=true;
						bod_ubica_text.disabled=true;
						bod_proveedor_text.disabled=true;
						bod_inter_text.disabled=true;
		        bod_despacho_text.disabled=true;
		        bod_controlados_text.disabled=true;
		        bod_consume_text.disabled=true;
						bod_costo_text.disabled=true;
					  bod_repone_text.disabled=true;
					
						$('editar_boton').style.display='';
						$('borrar_boton').style.display='';
						$('guardar_boton').style.display='none';
						
						habilitar_radios();
						
						cargar_listado();
						
					} else {
					
						alert('ERROR: \n\n'+pedido_datos.responseText.unescapeHTML());
					
					}
					
				}
			}		
			);	
		
		}
		
	
	cargar_listado();
	
	</script>
	
	<center>
	
	<table><tr><td valign='top'>
	
	<div class='sub-content'>
	
	<div class='sub-content'><img src='iconos/table.png'> <b>B&uacute;squeda por Listado</b></div>
	
	<div class='sub-content'><table>
	<tr><td style='text-align: right;'><b>Filtrar:</b></td><td>
	<input type='text' name='bodegas_filtro' id='bodegas_filtro' size=30 onKeyUp='cargar_listado();'>
	</td></tr>
	</table></div>
	
	<div class='sub-content3' id='listado'>
		
	</div>
	
	</div>
	
	</td><td valign='top'>
	
	<div class='sub-content'>
	
	<center>
	<div class='boton' id='agregar_boton'>
	<table><tr><td>
	<img src='iconos/building_add.png'>
	</td><td>
	<a href='#' onClick='agregar_usuario();'>Agregar Ubicaci&oacute;n...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='editar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/building_edit.png'>
	</td><td>
	<a href='#' onClick='editar_usuario();'>Editar Ubicaci&oacute;n...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='borrar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/building_delete.png'>
	</td><td>
	<a href='#' onClick='borrar_usuario();'>Eliminar Ubicaci&oacute;n...</a>
	</td></tr></table>
	</div>
	
	</center>
	
	</div>
	
	<div class='sub-content'>
	
	<div class='sub-content'><img src='iconos/building.png'> <b>Datos de Ubicaci&oacute;n</b></div>
	
	<div class='sub-content3' id='registro'>
	<input type='hidden' name='bodega_id' id='bodega_id'>
	<table style='padding: 5px;'>
	<tr><td style='text-align: right;'>Nombre:</td>		
	<td><input type='text' name='bodega_glosa' id='bodega_glosa' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Ubicaci&oacute;n:</td>		
	<td><input type='text' name='bodega_ubica' id='bodega_ubica' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Centro de Resp.:</td>		
	<td>
	
	<table><tr><td>
  <img src='iconos/exclamation.png' id='bodega_costoicono'>
  </td><td width=80>
  <input type='hidden' name='bodega_costoid' id='bodega_costoid'>
  <b><span id='bodega_costo'>(sin asignar...)</span></b>
	</td><td>
	<input type='button' name='bodega_costosel' id='bodega_costosel' value='Asignar...' DISABLED onClick='seleccionar_centro();'>
	</td></tr></table>
	
	
	</td></tr>
	<tr><td style='text-align: right;' valign="top"><input type='checkbox' name='bodega_proveedor' id='bodega_proveedor' value=1 DISABLED></td>	
	<td>Recibe directamente desde<br>Proveedores.</td></tr>
	
	<tr><td style='text-align: right;' valign="top"><input type='checkbox' name='bodega_inter' id='bodega_inter' value=1 DISABLED></td>	
	<td>Recibe pedidos desde otras<br>Ubicaciones.</td></tr>
	
	<tr><td style='text-align: right;' valign="top"><input type='checkbox' name='bodega_despacho' id='bodega_despacho' value=1 DISABLED></td>	
	<td>Se despachan recetas a pacientes.</td></tr>
	
	<tr><td style='text-align: right;' valign="top"><input type='checkbox' name='bodega_controlados' id='bodega_controlados' value=1 DISABLED></td>	
	<td>Se manejan medicamentos controlados.</td></tr>
	
	<tr><td style='text-align: right;' valign="top"><input type='checkbox' name='bodega_repone' id='bodega_repone' value=1 DISABLED></td>	
	<td>Se hacen reposiciones de medicamentos<br> controlados a otras bodegas.</td></tr>
	
	<tr><td style='text-align: right;' valign="top"><input type='checkbox' name='bodega_consume' id='bodega_consume' value=1 size=25 DISABLED onClick='habilitar_radios();'></td>		
	<td>Despacha art&iacute;culos a <br>centros de costo/servicios.<br>
	<select id='servicios' name='servicios' disabled>
	<option value=1 SELECTED>Cl&iacute;nicos</option>
	<option value=2>Todos</option>
	</select>
  
  </td>
  
  
  </tr>
		
	</table>
	
	<center>
	<div class='boton' id='guardar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/building_go.png'>
	</td><td>
	<a href='#' onClick='verifica_tabla();'><span id='guardar_texto'>Guardar cambios a Usuario...</span></a>
	</td></tr></table>
	</div>
	</center>
	
	</div>
	
	</div>
	
	</td></tr></table>
	
	</center>
	
	<iframe id='selec_centro' name='selec_centro' 
	src='mostrar.php?tipo=listado_centros_corto'
  style='
  display:none;
  border:1px solid black;
  position:absolute;
  z-index:5;
  width:200px;
  height:200px;
  '
  onBlur='
  this.style.display=\"none\";
  '
  ></iframe>
  
  <script>
  
      bod_id_text = document.getElementById('bodega_id');
			bod_nombre_text = document.getElementById('bodega_glosa');
			bod_ubica_text = document.getElementById('bodega_ubica');
			bod_proveedor_text = document.getElementById('bodega_proveedor');
					
      bod_inter_text = document.getElementById('bodega_inter');
			bod_despacho_text = document.getElementById('bodega_despacho');
			bod_controlados_text = document.getElementById('bodega_controlados');
					 
      bod_consume_text = document.getElementById('bodega_consume');
			bod_costo_text = document.getElementById('bodega_costosel');
			bod_costoicono_text = document.getElementById('bodega_costoicono');
			bod_costoid_text = document.getElementById('bodega_costoid');
			bod_repone_text = document.getElementById('bodega_repone');
			bod_costoglosa_text = document.getElementById('bodega_costo');
					
	</script>
