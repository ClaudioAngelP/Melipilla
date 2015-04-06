<?php

	// Script de despliegue de Formularios
	// Sistema de Gestión
	// Hospital San Martín de Quillota
	// =============================================================================
	// Rodrigo Carvajal J.
	// Soluciones Computacionales Viña del Mar LTDA.
	// =============================================================================
	
	
	// Formularios pedidos por el Index...
	// =================================================================================
	// Incluyen: 	Divs Propios del Formulario
	// ---------	Funciones Javascript propias del Formulario
	//				Referencias a Tablas de Información y Búsqueda del Formulario 
	
	require_once("conectar_db.php");
				
				
	if($_GET['form']=='ing_prod') {
	
	$clasificahtml = desplegar_opciones("bodega_clasificacion", 
	"clasifica_id, clasifica_nombre",'','true','ORDER BY clasifica_nombre'); 
	
	$formahtml = desplegar_opciones("bodega_forma", 
	"forma_id, forma_nombre",'','true','ORDER BY forma_nombre'); 

	
	print("
	
	<script>
	
	bloquear_boton=true;
	
	buscar_codigo_prod = function() {
		
			articulo_div = document.getElementById('articulo')
			prod_codigo_text = document.getElementById('prod_codigo');
			prod_glosa_text = document.getElementById('prod_glosa');
			bloquear_boton=true;
			
			if(prod_codigo_text.value.length<5) {
				return;
			}
			
			articulo_div.style.background='#afffbc'; // Pone Verde - Cargando Datos
			
			var myAjax2 = new Ajax.Request(
			'registro.php',
			{
				method: 'get',
				parameters: 'tipo=articulo&codigo='+serializar(prod_codigo_text),
				onComplete: function (pedido_datos) {
				
					datos_nuevos = eval(pedido_datos.responseText);
					
					prod_id_text = document.getElementById('id_articulo');
					prod_glosa_text = document.getElementById('prod_glosa');
					prod_nombre_text = document.getElementById('prod_nombre');
					prod_clasif_text = document.getElementById('prod_clasif');
					prod_vence_text = document.getElementById('prod_vence');
					prod_forma_text = document.getElementById('prod_forma');
					prod_auge_text = document.getElementById('prod_auge');
					prod_controlado_text = document.getElementById('prod_controlado');
					
					if(!datos_nuevos) { 
					
						articulo_div.style.background='#fffeaf'; // Pone Amarillo - Ingreso Completo
					
						prod_vence_text.selectedIndex=0;
						prod_forma_text.selectedIndex=0;
						prod_clasif_text.selectedIndex=0;
						
						prod_id_text.value='';
						prod_glosa_text.value='';
						prod_nombre_text.value='';
						
						art = document.getElementById('articulo');
						detalle = document.getElementById('detalle_prod');
						alternador = document.getElementById('desplegar_info');
						
						prod_glosa_text.focus();

						$('guardar_boton').style.display='';
						
						$('texto_boton').innerHTML='Ingresar Art&iacute;culo Nuevo...';
						
						$('imagen_titulo').src='iconos/script_add.png';
						
						$('titulo_formulario').innerHTML='Datos de Producto Nuevo';
						
						bloquear_boton=false;
						
						return; 
					}
					
					prod_id_text.value=datos_nuevos[0].unescapeHTML();
					prod_glosa_text.value=datos_nuevos[1].unescapeHTML();
					prod_nombre_text.value=datos_nuevos[3].unescapeHTML();
					
					sel_valor(prod_forma_text,datos_nuevos[4]);
					sel_valor(prod_clasif_text,datos_nuevos[5]);
					
					if(!(datos_nuevos[2]*1)) { vence=0;	} 	else { vence=1;	}
					if(datos_nuevos[6]=='f') { auge=0; } 	else { auge=1; }
					if(datos_nuevos[7]=='f') { control=0; } else { control=1; }
					
					prod_vence_text.value=vence;
					prod_auge_text.value=auge;
					prod_controlado_text.value=control;
					
					articulo_div.style.background='#5390ff';	// Pone Azul - Edición de Artículo
					
					bloquear_boton=false;
					
					$('guardar_boton').style.display='';
						
					$('texto_boton').innerHTML='Guardar Cambios a Art&iacute;culo...';
					
					$('imagen_titulo').src='iconos/script_edit.png';
						
					$('titulo_formulario').innerHTML='Datos de Producto';
						
					
					prod_glosa_text.select();
					
				}	
			}
			
			);
			
		}
		
		verifica_tabla = function() {
		
			prod_id_text = document.getElementById('id_articulo');
			prod_glosa_text = document.getElementById('prod_glosa');
			prod_nombre_text = document.getElementById('prod_nombre');
			
			if(bloquear_boton) {
				alert('El sistema a&uacute;n no responde por el c&oacute;digo ingresado.'.unescapeHTML());
				prod_glosa_text.select();
				return;
			}
			
			if(trim(prod_glosa_text.value)=='') {
				alert('El campo glosa est&aacute; vac&iacute;o.'.unescapeHTML());
				prod_glosa_text.select();
				return;
			}		
			
			if(trim(prod_nombre_text.value)=='') {
				alert('El campo nombre est&aacute; vac&iacute;o.'.unescapeHTML());
				prod_nombre_text.select();
				return;
			}		
			
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=ingreso_edicion_art&'+serializar_objetos('articulo'),
				onComplete: function(pedido_datos) {
				
					if(pedido_datos.responseText=='1') {
						alert('Ingreso/Edici&oacute;n de art&iacute;culo realizado exitosamente.'.unescapeHTML());
						cambiar_pagina('Ingreso/Edici&oacute;n de Productos','ing_prod');
					} else {
						alert('Error: \\r\\n'+pedido_datos.responseText.unescapeHTML());
					}
					
				}
			}		
			);	
		
		}
		
		$('prod_codigo').focus();
		
		</script>
	
	<center>
	<br>
	<div class='sub-content' style='width: 440px;'>
	
	<div class='sub-content'>
	<img src='iconos/script.png' id='imagen_titulo'> 
	<b><span id='titulo_formulario'>Datos de Producto</span></b></div>
	
	<div class='sub-content2' id='articulo'>
	<table>
	<input type='hidden' name='id_articulo' id='id_articulo'>
	<tr><td style='text-align: right;'>C&oacute;digo Int.:</td><td><input type='text' name='prod_codigo' id='prod_codigo' onBlur='buscar_codigo_prod();'></td></tr>
	<tr><td style='text-align: right;'>Glosa:</td><td><input type='text' name='prod_glosa' id='prod_glosa' size=40></td></tr>
	<tr><td style='text-align: right;'>Nombre:</td><td><input type='text' name='prod_nombre' id='prod_nombre' size=40></td></tr>
	<tr><td style='text-align: right;'>Clasificaci&oacute;n:</td>
	<td>
	<select id='prod_clasif' name='prod_clasif'>
	<option value=0 selected>(No Aplicable...)</option>
	" . ($clasificahtml) . "
	</select>
	</td></tr>
	<tr><td style='text-align: right;'>Forma Farmaceutica:</td><td>
	<select id='prod_forma' name='prod_forma'>
	<option value=0 selected>(No Aplicable...)</option>
	" . ($formahtml) . "
	</select>
	</td></tr>
	<tr><td style='text-align: right;'>Vence:</td><td><select name='prod_vence' id='prod_vence'>
	<option value=0>No</option>
	<option value=1 SELECTED>Si</option>
	</select></td></tr>
	<tr><td style='text-align: right;'>Auge:</td><td><select name='prod_auge' id='prod_auge'>
	<option value=0 SELECTED>No</option>
	<option value=1>Si</option>
	</select></td></tr>
	<tr><td style='text-align: right;'>Controlado:</td><td><select name='prod_controlado' id='prod_controlado'>
	<option value=0 SELECTED>No</option>
	<option value=1>Si</option>
	</select></td></tr>

	</table>
	<br>
	<center>
	
	<div class='boton' id='guardar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/accept.png'>
	</td><td>
	<a href='#' onClick='verifica_tabla();'><span id='texto_boton'>Guardar Art&iacute;culo...</span></a>
	</td></tr></table>
	</div>
	
	</center>
		
	</div>
	
	</div>
	<br>
	</center>
	
	");
	
	}
	
	if($_GET['form']=='personal') {
	
	print("
	
	<script>
	
	cargar_listado = function() {
	
		var myAjax = new Ajax.Updater(
			'listado', 
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=personal_listado&orden='+($('personal_orden').value*1)+'&buscar='+serializar('personal_filtro'),
				evalScripts: true
				
			}
			
			);
	
	}
	
	seleccionar_usuario = function(idusuario) {
	
		var myAjax = new Ajax.Request(
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=personal&buscar='+(idusuario*1),
				onComplete: function(pedido_datos) {
				
					datos=eval(pedido_datos.responseText);
					
					func_id_text = document.getElementById('func_id');
					func_rut_text = document.getElementById('func_rut');
					func_clave_text = document.getElementById('func_clave');
					func_nombre_text = document.getElementById('func_nombre');
					func_cargo_text = document.getElementById('func_cargo');
					func_permisos_text = document.getElementById('func_permiso');
					
					func_id_text.value = datos[0];
					func_rut_text.value = datos[1];
					func_clave_text.value = datos[2].unescapeHTML();
					func_nombre_text.value = datos[3].unescapeHTML();
					func_cargo_text.value = datos[4].unescapeHTML();
					func_permisos_text.value = datos[5];
					
					func_rut_text.disabled=true;
					func_nombre_text.disabled=true;
					func_cargo_text.disabled=true;
					func_clave_text.disabled=true;
					func_permisos_text.disabled=true;
					
					$('editar_boton').style.display='';
					$('borrar_boton').style.display='';
					$('guardar_boton').style.display='none';
				}
				
			}
			
			);
	
	}
	
	agregar_usuario = function () {
	
		func_id_text = document.getElementById('func_id');
		func_rut_text = document.getElementById('func_rut');
		func_clave_text = document.getElementById('func_clave');
		func_nombre_text = document.getElementById('func_nombre');
		func_cargo_text = document.getElementById('func_cargo');
		func_permisos_text = document.getElementById('func_permiso');
		
		func_rut_text.disabled=false;
		func_nombre_text.disabled=false;
		func_cargo_text.disabled=false;
		func_clave_text.disabled=false;
		func_permisos_text.disabled=false;
		
		func_id_text.value='';
		func_rut_text.value='';
		func_nombre_text.value='';
		func_cargo_text.value='';
		func_clave_text.value='';
		func_permisos_text.value='';
				
		$('guardar_texto').innerHTML = 'Guardar Usuario Nuevo...';
		$('guardar_boton').style.display='';
		$('borrar_boton').style.display='none';
		$('editar_boton').style.display='none';
					
		func_rut_text.focus();
		
	}
	
	editar_usuario = function() {
	
		func_id_text = document.getElementById('func_id');
		func_rut_text = document.getElementById('func_rut');
		func_clave_text = document.getElementById('func_clave');
		func_nombre_text = document.getElementById('func_nombre');
		func_cargo_text = document.getElementById('func_cargo');
		func_permisos_text = document.getElementById('func_permiso');
		
		func_rut_text.disabled=false;
		func_nombre_text.disabled=false;
		func_cargo_text.disabled=false;
		func_clave_text.disabled=false;
		func_permisos_text.disabled=false;
					
		$('guardar_texto').innerHTML = 'Guardar Cambios a Usuario...';
		$('guardar_boton').style.display='';
		
		func_nombre_text.focus();
		func_nombre_text.select();
				
	
	}
	
	borrar_usuario = function() {
		
		confirma=confirm('¿Est&aacute; seguro que desea eliminar este usuario? - No hay opciones para deshacer.'.unescapeHTML());
		
	}
	
	verifica_tabla = function() {
		
			func_rut_text = document.getElementById('func_rut');
			func_nombre_text = document.getElementById('func_nombre');
			func_cargo_text = document.getElementById('func_cargo');
			func_clave_text = document.getElementById('func_clave');
			
			if(trim(func_rut_text.value)=='') {
				alert('El campo RUT est&aacute; vac&iacute;o.'.unescapeHTML());
				func_rut_text.select();
				return;
			}	
			
			if(trim(func_nombre_text.value)=='') {
				alert('El campo Nombre est&aacute; vac&iacute;o.'.unescapeHTML());
				func_nombre_text.select();
				return;
			}	
			
			if(trim(func_cargo_text.value)=='') {
				alert('El campo Cargo est&aacute; vac&iacute;o.'.unescapeHTML());
				func_cargo_text.select();
				return;
			}	
			
			if(trim(func_clave_text.value)=='') {
				alert('El campo Clave est&aacute; vac&iacute;o.'.unescapeHTML());
				func_clave_text.select();
				return;
			}				
			
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=ingreso_edicion_personal&'+serializar_objetos('registro'),
				onComplete: function(pedido_datos) {
				
					if(pedido_datos.responseText=='1') {
					
						func_id_text = document.getElementById('func_id');
		
						if(func_id_text.value='') {
							alert('Ingreso de usuario realizado exitosamente.'.unescapeHTML());
						} else {
							alert('Edici&oacute;n de usuario realizado exitosamente.'.unescapeHTML());
						}
						
						func_id_text = document.getElementById('func_id');
						func_rut_text = document.getElementById('func_rut');
						func_clave_text = document.getElementById('func_clave');
						func_nombre_text = document.getElementById('func_nombre');
						func_cargo_text = document.getElementById('func_cargo');
						func_permisos_text = document.getElementById('func_permiso');
					
						func_rut_text.disabled=true;
						func_nombre_text.disabled=true;
						func_cargo_text.disabled=true;
						func_clave_text.disabled=true;
						func_permisos_text.disabled=true;
					
						$('editar_boton').style.display='';
						$('borrar_boton').style.display='';
						$('guardar_boton').style.display='none';
						
						cargar_listado();
						
					} else {
						alert('Error: \\r\\n'+pedido_datos.responseText.unescapeHTML());
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
	<input type='text' name='personal_filtro' id='personal_filtro' size=30 onKeyUp='cargar_listado();'>
	</td></tr>
	<tr><td style='text-align: right;'><b>Ordenar por:</b></td><td>
	<select name='personal_orden' id='personal_orden' onClick='cargar_listado();'>
	<option value=1>Rut</option>
	<option value=0 SELECTED>Nombre</option>
	</select>
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
	<img src='iconos/user_add.png'>
	</td><td>
	<a href='#' onClick='agregar_usuario();'>Agregar Usuario...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='editar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/user_edit.png'>
	</td><td>
	<a href='#' onClick='editar_usuario();'>Editar Usuario...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='borrar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/user_delete.png'>
	</td><td>
	<a href='#' onClick='borrar_usuario();'>Eliminar Usuario...</a>
	</td></tr></table>
	</div>
	
	</center>
	
	</div>
	
	<div class='sub-content'>
	
	<div class='sub-content'><img src='iconos/user_green.png'> <b>Datos del Personal</b></div>
	
	<div class='sub-content3' id='registro'>
	<input type='hidden' name='func_id' id='func_id'>
	<table style='padding: 5px;'>
	<tr><td style='text-align: right;'>Rut:</td>		
	<td><input type='text' name='func_rut' id='func_rut' size=10 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Clave:</td>		
	<td><input type='password' name='func_clave' id='func_clave' DISABLED></td></tr>
	<tr><td style='text-align: right;'>Nombre:</td>	
	<td><input type='text' name='func_nombre' id='func_nombre' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Cargo:</td>		
	<td><input type='text' name='func_cargo' id='func_cargo' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Permisos:</td>	
	<td><input type='text' name='func_permiso' id='func_permiso' DISABLED></td></tr>
	
	</table>
	
	<center>
	<div class='boton' id='guardar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/user_go.png'>
	</td><td>
	<a href='#' onClick='verifica_tabla();'><span id='guardar_texto'>Guardar cambios a Usuario...</span></a>
	</td></tr></table>
	</div>
	</center>
	
	</div>
	
	</div>
	
	</td></tr></table>
	
	</center>
	
	");
	
	}
	
	if($_GET['form']=='bodegas') {
	
	print("
	
	<script>
	
	cargar_listado = function() {
	
		var myAjax = new Ajax.Updater(
			'listado', 
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=bodegas_listado&buscar='+serializar('bodegas_filtro'),
				evalScripts: true
				
			}
			
			);
	
	}
	
	seleccionar_bodega = function(idbodega) {
	
		var myAjax = new Ajax.Request(
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=bodegas&buscar='+(idbodega*1),
				onComplete: function(pedido_datos) {
				
					datos=eval(pedido_datos.responseText);
				
					bod_id_text = document.getElementById('bodega_id');
					bod_nombre_text = document.getElementById('bodega_glosa');
					bod_ubica_text = document.getElementById('bodega_ubica');
					bod_proveedor_text = document.getElementById('bodega_proveedor');
					bod_consume_text = document.getElementById('bodega_consume');
					
					bod_id_text.value=datos[0];
					bod_nombre_text.value=datos[1].unescapeHTML();
					bod_ubica_text.value=datos[2].unescapeHTML();
					
					if(datos[3]=='f') { provee=0; } else { provee=1; }
					if(datos[4]=='f') { consum=0; } else { consum=1; }
					
					bod_proveedor_text.value=provee;
					bod_consume_text.value=consum;
		
					bod_nombre_text.disabled=true;
					bod_ubica_text.disabled=true;
					bod_proveedor_text.disabled=true;
					bod_consume_text.disabled=true;
					
					$('editar_boton').style.display='';
					$('borrar_boton').style.display='';
					$('guardar_boton').style.display='none';
		
				}
				
			}
			
			);
	
	}
	
	agregar_usuario = function () {
	
		bod_id_text = document.getElementById('bodega_id');
		bod_nombre_text = document.getElementById('bodega_glosa');
		bod_ubica_text = document.getElementById('bodega_ubica');
		bod_proveedor_text = document.getElementById('bodega_proveedor');
		bod_consume_text = document.getElementById('bodega_consume');
					
		bod_id_text.disabled=false;
		bod_nombre_text.disabled=false;
		bod_ubica_text.disabled=false;
		bod_proveedor_text.disabled=false;
		bod_consume_text.disabled=false;
		
		bod_id_text.value='';
		bod_nombre_text.value='';
		bod_ubica_text.value='';
		bod_proveedor_text.selectedIndex=0;
		bod_consume_text.selectedIndex=0;
				
		$('guardar_texto').innerHTML = 'Guardar Bodega Nueva...';
		$('guardar_boton').style.display='';
		$('borrar_boton').style.display='none';
		$('editar_boton').style.display='none';
					
		bod_nombre_text.focus();
		
	}
	
	editar_usuario = function() {
	
		bod_id_text = document.getElementById('bodega_id');
		bod_nombre_text = document.getElementById('bodega_glosa');
		bod_ubica_text = document.getElementById('bodega_ubica');
		bod_proveedor_text = document.getElementById('bodega_proveedor');
		bod_consume_text = document.getElementById('bodega_consume');
		
		bod_id_text.disabled=false;
		bod_nombre_text.disabled=false;
		bod_ubica_text.disabled=false;
		bod_proveedor_text.disabled=false;
		bod_consume_text.disabled=false;
					
		$('guardar_texto').innerHTML = 'Guardar Cambios a Bodega...';
		$('guardar_boton').style.display='';
		
		bod_nombre_text.focus();
		bod_nombre_text.select();
				
	
	}
	
	borrar_usuario = function() {
		
		confirma=confirm('¿Est&aacute; seguro que desea eliminar este usuario? - No hay opciones para deshacer.'.unescapeHTML());
		
	}
	
	verifica_tabla = function() {
		
			bod_id_text = document.getElementById('bodega_id');
			bod_nombre_text = document.getElementById('bodega_glosa');
			bod_ubica_text = document.getElementById('bodega_ubica');
			bod_proveedor_text = document.getElementById('bodega_proveedor');
			bod_consume_text = document.getElementById('bodega_consume');
		
			if(trim(bod_nombre_text.value)=='') {
				alert('El campo Nombre est&aacute; vac&iacute;o.'.unescapeHTML());
				bod_nombre_text.select();
				return;
			}	
										
			
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=ingreso_edicion_personal&'+serializar_objetos('registro'),
				onComplete: function(pedido_datos) {
				
					if(pedido_datos.responseText=='1') {
					
						func_id_text = document.getElementById('func_id');
		
						if(func_id_text.value='') {
							alert('Ingreso de usuario realizado exitosamente.'.unescapeHTML());
						} else {
							alert('Edici&oacute;n de usuario realizado exitosamente.'.unescapeHTML());
						}
						
						func_id_text = document.getElementById('func_id');
						func_rut_text = document.getElementById('func_rut');
						func_clave_text = document.getElementById('func_clave');
						func_nombre_text = document.getElementById('func_nombre');
						func_cargo_text = document.getElementById('func_cargo');
						func_permisos_text = document.getElementById('func_permiso');
					
						func_rut_text.disabled=true;
						func_nombre_text.disabled=true;
						func_cargo_text.disabled=true;
						func_clave_text.disabled=true;
						func_permisos_text.disabled=true;
					
						$('editar_boton').style.display='';
						$('borrar_boton').style.display='';
						$('guardar_boton').style.display='none';
						
						cargar_listado();
						
					} else {
						alert('Error: \\r\\n'+pedido_datos.responseText.unescapeHTML());
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
	<img src='iconos/package_add.png'>
	</td><td>
	<a href='#' onClick='agregar_usuario();'>Agregar Bodega...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='editar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/package_edit.png'>
	</td><td>
	<a href='#' onClick='editar_usuario();'>Editar Bodega...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='borrar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/package_delete.png'>
	</td><td>
	<a href='#' onClick='borrar_usuario();'>Eliminar Bodega...</a>
	</td></tr></table>
	</div>
	
	</center>
	
	</div>
	
	<div class='sub-content'>
	
	<div class='sub-content'><img src='iconos/package.png'> <b>Datos de Bodega</b></div>
	
	<div class='sub-content3' id='registro'>
	<input type='hidden' name='bodega_id' id='bodega_id'>
	<table style='padding: 5px;'>
	<tr><td style='text-align: right;'>Nombre:</td>		
	<td><input type='text' name='bodega_glosa' id='bodega_glosa' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Ubica:</td>		
	<td><input type='text' name='bodega_ubica' id='bodega_ubica' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='bodega_proveedor' id='bodega_proveedor' DISABLED></td>	
	<td>Recibe directamente desde<br>Proveedores.</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='bodega_consume' id='bodega_consume' size=25 DISABLED></td>		
	<td>Asumir que art&iacute;culos<br>recibidos son consumidos.</td></tr>
		
	</table>
	
	<center>
	<div class='boton' id='guardar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/package_go.png'>
	</td><td>
	<a href='#' onClick='verifica_tabla();'><span id='guardar_texto'>Guardar cambios a Usuario...</span></a>
	</td></tr></table>
	</div>
	</center>
	
	</div>
	
	</div>
	
	</td></tr></table>
	
	</center>
	
	");
	
	}
	
	
	if($_GET['form']=='rec_prod') {
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_proveedores=true',
	'ORDER BY bod_glosa'); 
	
	$clasificahtml = desplegar_opciones("bodega_clasificacion", 
	"clasifica_id, clasifica_nombre",'','true','ORDER BY clasifica_nombre'); 
	
	$formahtml = desplegar_opciones("bodega_forma", 
	"forma_id, forma_nombre",'','true','ORDER BY forma_nombre'); 
	
	print("
	
		<script type='text/javascript'>
		
		art_num=0;
		art_count=0;
		art_listado='';
				
		mostrar_info_prod = function() {
		
			prov_select = document.getElementById('provee_bodega');
			
			if(prov_select.selectedIndex==0) { 
				$('info').style.display='none';
				return; 
			} else {
				if($('info').style.display=='none') {
					$('info').style.display='block';
					$('info').innerHTML='Cargando...';
				}
			}
			
			var myAjax = new Ajax.Updater(
			'info', 
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=proveedor&id='+serializar(prov_select),
				evalScripts: true
			}
			
			);
		}
		
		buscar_codigo_prod = function(numero) {
		
			articulo_div = document.getElementById('articulo')
			prod_codigo_text = document.getElementById('prod_codigo');
			prod_glosa_text = document.getElementById('prod_glosa');
			
			if(prod_codigo_text.value.length<5) {
				return;
			}
			
			articulo_div.style.background='#afffbc'; // Pone Verde - Cargando Datos
			
			var myAjax2 = new Ajax.Request(
			'registro.php',
			{
				method: 'get',
				parameters: 'tipo=articulo&codigo='+serializar(prod_codigo_text),
				onComplete: function (pedido_datos) {
				
					datos_nuevos = eval(pedido_datos.responseText);
					
					prod_id_text = document.getElementById('id_articulo');
					prod_fecha_text = document.getElementById('fecha');
					prod_fechavence_text = document.getElementById('prod_fechavence');
					prod_formato_text = document.getElementById('prod_formato');
					prod_formato_cant_text = document.getElementById('prod_formato_cant');
					prod_subtotal_text = document.getElementById('prod_subtotal');
					prod_cant_text = document.getElementById('prod_cant');
					prod_glosa_text = document.getElementById('prod_glosa');
					prod_nombre_text = document.getElementById('prod_nombre');
					prod_clasif_text = document.getElementById('prod_clasif');
					prod_vence_text = document.getElementById('prod_vence');
					prod_forma_text = document.getElementById('prod_forma');
					prod_auge_text = document.getElementById('prod_auge');
					prod_controlado_text = document.getElementById('prod_controlado');
					
					if(!datos_nuevos) { 
					
						articulo_div.style.background='#fffeaf'; // Pone Amarillo - Ingreso Completo
					
						prod_vence_text.selectedIndex=0;
						prod_forma_text.selectedIndex=0;
						prod_clasif_text.selectedIndex=0;
						prod_formato_text.selectedIndex=0;
						
						
						prod_id_text.value='';
						prod_fecha_text.value='';
						prod_glosa_text.value='';
						prod_nombre_text.value='';
						prod_fechavence_text.value='';
						prod_cant_text.value='';
						prod_formato_cant_text.value='1';
						prod_subtotal_text.value='';
						
						prod_glosa_text.disabled=true;
						prod_nombre_text.disabled=true;
						prod_auge_text.disabled=true;
						prod_controlado_text.disabled=true;
						
						prod_forma_text.disabled=true;
						prod_clasif_text.disabled=true;
						prod_vence_text.disabled=true;
						
						art = document.getElementById('articulo');
						detalle = document.getElementById('detalle_prod');
						alternador = document.getElementById('desplegar_info');
						
						prod_codigo_text.focus();
						
						return; 
					}
					
					prod_id_text.value=datos_nuevos[0].unescapeHTML();
					prod_fecha_text.value=0;
					prod_glosa_text.value=datos_nuevos[1].unescapeHTML();
					prod_cant_text.value='';
					prod_nombre_text.value=datos_nuevos[3].unescapeHTML();
					
					prod_glosa_text.disabled=true;
					prod_nombre_text.disabled=true;
					
					sel_valor(prod_forma_text,datos_nuevos[4]);
					sel_valor(prod_clasif_text,datos_nuevos[5]);
					
					if(!(datos_nuevos[2]*1)) { 
						vence=0; 
						prod_fechavence_text.disabled=true;
						prod_fecha_text.value=3;
					} 	else { 
						vence=1;
						prod_fechavence_text.disabled=false;
						prod_fecha_text.value=0;
					}
					
					if(datos_nuevos[6]=='f') { auge=0; } 	else { auge=1; }
					if(datos_nuevos[7]=='f') { control=0; } else { control=1; }
					
					prod_vence_text.value=vence;
					prod_auge_text.value=auge;
					prod_controlado_text.value=control;
					
					prod_forma_text.disabled=true;
					prod_clasif_text.disabled=true;
					prod_vence_text.disabled=true;
					prod_auge_text.disabled=true;
					prod_controlado_text.disabled=true;
					prod_formato_text.disabled=false;
					prod_formato_cant_text.disabled=false;
					prod_subtotal_text.disabled=false;
					prod_cant_text.disabled=false;
																			
					articulo_div.style.background='#5390ff';	// Pone Azul - Ingreso Fecha y Cant.
					
					calcular_cant();
					
					if(vence==1) prod_fechavence_text.focus();
					else prod_cant_text.focus();
					
				}	
			}
			
			);
			
		}
		
		confirmar_fecha = function(numero) {
		
			art = document.getElementById('articulo');
			prod_codigo_text = document.getElementById('prod_codigo');
			prod_fechavence_text = document.getElementById('prod_fechavence');
			prod_fecha_text = document.getElementById('fecha');
			prod_id_text = document.getElementById('id_articulo');

			
			if(prod_fechavence_text.value=='') {
				prod_fechavence_text.style.background='';
				prod_fecha_text.value=0;
				return;
			}
			
			if(!isDate(prod_fechavence_text.value)) {
				prod_fechavence_text.style.background='red'; // Fecha Inválida...
				prod_fecha_text.value=0;
				return;
			} 
			
			fecha_esp=prod_fechavence_text.value.split('/');
					
			var fecha_actual = new Date;
			var fecha_lote = new Date;
						
			fecha_lote.setDate(fecha_esp[0]*1);
			fecha_lote.setMonth((fecha_esp[1]*1)-1);
			fecha_lote.setFullYear(fecha_esp[2]*1);
			
			if(fecha_actual>=fecha_lote) {
				prod_fechavence_text.style.background='yellow';
				prod_fecha_text.value=0;
				return;
			}
			
			fecha_actual.setMonth(fecha_actual.getMonth()+1);
			
			if(fecha_actual>=fecha_lote) {
				if(!confirm('La fecha de vencimiento ingresada est&aacute; a menos de un m&eacute;s de cumplirse. ¿Est&aacute; seguro?'.unescapeHTML())) {
				
					prod_fechavence_text.style.background='yellow';
					prod_fecha_text.value=0;
					prod_fechavence_text.focus();
					return;
					
				}
			}
			
			prod_fechavence_text.style.background='yellowgreen';
			
			idprod=serializar(prod_codigo_text);
			fecha_verificar=serializar(prod_fechavence_text);
			
			var myAjax = new Ajax.Request(
			'registro.php', 
			{
				method: 'get', 
				parameters: 'tipo=fecha_lote&codigo='+idprod+'&fecha='+fecha_verificar,
				onComplete: function(pedido_datos) {
				
					datos = eval(pedido_datos.responseText);
					
					if(datos) {
						prod_fechavence_text.style.background='#8fb6Df';
						prod_fecha_text.value=2;
					} else {
						prod_fechavence_text.style.background='#bfe6ff';
						prod_fecha_text.value=1;
					}
						
				}
			}		
			);	
						
		}
		
		limpiar_articulo = function(numero) {
			
			art = document.getElementById('articulo');
			detalle = document.getElementById('detalle_prod');
			
			prod_id_text = document.getElementById('id_articulo');
			prod_fecha_text = document.getElementById('fecha');
			prod_codigo_text = document.getElementById('prod_codigo');
			prod_glosa_text = document.getElementById('prod_glosa');
			prod_fechavence_text = document.getElementById('prod_fechavence');
			prod_cant_text = document.getElementById('prod_cant');
			prod_formato_text = document.getElementById('prod_formato');
			prod_formato_cant_text = document.getElementById('prod_formato_cant');
			prod_subtotal_text = document.getElementById('prod_subtotal');
			prod_nombre_text = document.getElementById('prod_nombre');
			prod_clasif_text = document.getElementById('prod_clasif');
			prod_vence_text = document.getElementById('prod_vence');
			prod_forma_text = document.getElementById('prod_forma');
			prod_auge_text = document.getElementById('prod_auge');
			
			prod_glosa_text.disabled=true;
			prod_nombre_text.disabled=true;
						
			prod_forma_text.disabled=true;
			prod_clasif_text.disabled=true;
			prod_vence_text.disabled=true;
			prod_auge_text.disabled=true;
			prod_fechavence_text.disabled=false;
			prod_formato_text.disabled=false;
			prod_formato_cant_text.disabled=false;
			prod_subtotal_text.disabled=false;
			
			prod_forma_text.selectedIndex=0;
			prod_clasif_text.selectedIndex=0;
			prod_vence_text.selectedIndex=0;
			prod_auge_text.selectedIndex=0;
			prod_formato_text.selectedIndex=0;
			
			prod_id_text.value='';
			prod_fecha_text.value=0;
			prod_codigo_text.value='';
			prod_glosa_text.value='';
			prod_fechavence_text.value='';
			prod_cant_text.value='';
			prod_nombre_text.value='';
			prod_forma_text.value='';
			prod_formato_cant_text.value='';
			prod_subtotal_text.value='';
			
			
			art.style.background='#afd6ff';
			prod_fechavence_text.style.background='';
			
			prod_codigo_text.focus();
			
		}
		
		agregar_art = function() {
		
			prod_id_text = document.getElementById('id_articulo');
			prod_fecha_text = document.getElementById('fecha');
			prod_formato_text = document.getElementById('prod_formato');
			prod_formato_cant_text = document.getElementById('prod_formato_cant');
			prod_subtotal_text = document.getElementById('prod_subtotal');
			prod_codigo_text = document.getElementById('prod_codigo');
			prod_glosa_text = document.getElementById('prod_glosa');
			prod_fechavence_text = document.getElementById('prod_fechavence');
			prod_cant_text = document.getElementById('prod_cant');
			prod_nombre_text = document.getElementById('prod_nombre');
			prod_clasif_text = document.getElementById('prod_clasif');
			prod_vence_text = document.getElementById('prod_vence');
			prod_forma_text = document.getElementById('prod_forma');
			prod_auge_text = document.getElementById('prod_auge');
			
			// Validaciones
			
			if((prod_id_text.value)=='') { alert('Falta c&oacute;digo v&aacute;lido de producto.'.unescapeHTML()); return; }
			if((prod_cant_text.value*1)=='0') { alert('Falta campo cantidad.'); return; }
			if((prod_subtotal_text.value*1)=='0') { alert('Falta campo subtotal.'); return; }
			if((prod_formato_cant_text.value*1)=='0') { alert('Falta la cantidad del formato del producto.'); return; }
			
			if((prod_fecha_text.value*1)==0) {
				alert('Falta especificar fecha de vencimiento v&aacute;lida para los productos.'.unescapeHTML()); return;
			}
			
			$('nosel').style.display='none';
			$('seleccion').style.display='block';
			
			if(prod_formato_text.value==0) formato_prod='Unidades';
			else formato_prod='Caja/Paq.';
			
			if (prod_fecha_text.value==3) fecha_mostrar = '(n/a)';
			else fecha_mostrar = prod_fechavence_text.value;
			
			
			nuevo_art='<tr id=\\'fila_art_'+art_count+'\\' onClick=\\'borrar_fila(this);\\' onMouseOver=\\'this.className=\"mouse_over\";\\' onMouseOut=\\'this.className=\"\";\\'><td><input type=\\'hidden\\' name=\"id_art_'+art_count+'\" id=\"id_art_'+art_count+'\" value=\"'+prod_id_text.value+'\"><input type=\\'hidden\\' name=\\'fecha_art_'+art_count+'\\' id=\\'fecha_art_'+art_count+'\\' value=\\''+prod_fechavence_text.value+'\\'> <input type=\\'hidden\\' name=\\'cant_art_'+art_count+'\\' id=\\'cant_art_'+art_count+'\\' value=\\''+prod_cant_text.value+'\\'> <input type=\\'hidden\\' name=\\'mult_art_'+art_count+'\\' id=\\'mult_art_'+art_count+'\\' value=\\''+prod_formato_cant_text.value+'\\'> <input type=\\'hidden\\' name=\\'subt_art_'+art_count+'\\'  id=\\'subt_art_'+art_count+'\\' value=\\''+prod_subtotal_text.value+'\\'>'+prod_codigo_text.value+'</td> <td>'+prod_glosa_text.value+'</td> <td style=\\'text-align: center;\\'>'+fecha_mostrar+'</td><td style=\\'text-align: right;\\'>'+prod_cant_text.value+'</td><td>'+formato_prod+'</td> <td style=\\'text-align: right;\\'>'+prod_formato_cant_text.value+'</td> <td style=\\'text-align: right;\\'>'+formatoDinero(prod_subtotal_text.value)+'.-</td></tr>';
			
			$('tabla_seleccion').innerHTML+=nuevo_art;
			
			art_count++;
			
			art_num++;
			
			calcular_totales();
			
			limpiar_articulo();
			
		}
		
		borrar_fila = function(fila) {
		
			fila.remove(); 
			
			filas=$('tabla_seleccion').getElementsByTagName('input');
			
			if(filas.length==0) {
				$('nosel').style.display='block';
				$('seleccion').style.display='none';
				art_num=0;
				art_count=0;
			} else {
				$('nosel').style.display='none';
				$('seleccion').style.display='block';
				art_num--;
			}
			
			calcular_totales();
			
		}
		
		verifica_tabla = function() {
		
			prod_bodega_doc_num = document.getElementById('bodega_doc_asociado_num');
			provee_encontrado = document.getElementById('proveedor_encontrado');
			
			if(art_num==0) { alert('No ha seleccionado productos a ingresar.'); return; }
			if(provee_encontrado.value==0) { alert('No se ha ingresado un RUT de Proveedor v&aacute;lido.'.unescapeHTML()); return; }
			if((prod_bodega_doc_num.value*1)==0) { alert('No se ha ingresado un n&uacute;mero de documento v&aacute;lido.'.unescapeHTML()); return; }
			
			pasarcampos=$('ingreso_producto').serialize()+'&'+serializar_objetos('tabla_seleccion');
			
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=ingreso_productos&'+pasarcampos+'&artnum='+(art_count*1),
				onComplete: function(pedido_datos) {
				
					if(pedido_datos.responseText=='1') {
						alert('Ingreso de documento realizado exitosamente.');
						cambiar_pagina('Recepci&oacute;n de Productos','rec_prod');
					} else {
						alert('Error: \\r\\n'+pedido_datos.responseText.unescapeHTML());
					}
					
				}
			}		
			);	
			
		}
		
		calcular_cant = function() {
		
			if($('prod_formato').value==0) {
				$('prod_formato_cant').value='1';
				$('prod_formato_cant').disabled=true;
			} else {
				$('prod_formato_cant').disabled=false;
			}
			
			cant_tot = $('prod_cant').value*$('prod_formato_cant').value;
			
			$('unidad_administracion').innerHTML=cant_tot;
			
			subtot = (($('prod_subtotal').value*1)/cant_tot);
			
			if(isNaN(subtot)) subtot=0;
			
			$('precio_unitario').innerHTML=formatoDinero(subtot)+'.-';
			
		}
		
		calcular_totales = function() {
		
			filas=$('tabla_seleccion').getElementsByTagName('tr');
			
			sumatoria=0;
			
			for(a=1;a<filas.length;a++) {
				columnas = filas[a].getElementsByTagName('input');
				
				sumatoria+=(columnas[4].value*1);
			}
			
			totales=$('detalle_total').getElementsByTagName('td');
			
			totales[2].innerHTML=formatoDinero(sumatoria)+'.-';
			totales[4].innerHTML=formatoDinero(((sumatoria*iva)-sumatoria))+'.-';
			totales[6].innerHTML='<b>'+formatoDinero(sumatoria*iva)+'.-</b>';
		
		}
		
		$('orden_compra_num').focus();
		 
		</script>
		<center>
		<form id='ingreso_producto' name='ingreso_producto'>
		
		<table><tr><td valign='top'>
		
		<table><tr><td valign='top'>
		
		<div id='listado' class='sub-content'><br>
		
		<table>
		<tr><td class='derecha'>Bodega Ingreso:</td>
		<td>
		<select name='prod_bodega'>
		" . ($bodegashtml) . "
		</select>
		</td></tr>
		<tr><td class='derecha'>&Oacute;rden de Compra Nro.:</td>
		<td><input type='text' id='orden_compra_num' name='orden_compra_num'></td></tr>
		<tr><td class='derecha'>Documento Asociado:</td>
		<td>
		<select id='bodega_doc_asociado' name='bodega_doc_asociado'>
		<option value='0' SELECTED>Gu&iacute;a de Despacho</option>
		<option value='1'>Factura</option>
		</select>
		</td></tr>
		<tr><td class='derecha'>N&uacute;mero Documento:</td>
		<td><input type='text' id='bodega_doc_asociado_num' 
		name='bodega_doc_asociado_num'></td></tr>
		</table>
		<br>
		</div>
		
		</td><td valign='top'>
		
		<div class='sub-content'>
		<center>
		<table><tr><td class='derecha'>RUT del Proveedor:</td>
		<td> <table><tr><td>
		<input type='text' name='provee_bodega' id='provee_bodega' size=30
		onChange='mostrar_info_prod();' onKeyUp='mostrar_info_prod();'>
		</td><td> 
		<img src='lupa.png' alt='Buscar...' border=0>
		</td></tr></table>
				
		</td></tr></table>
		
		<div id='info' class='sub-content2' style='width: 260px; display: none;'>
		
		<input type='hidden' name='proveedor_encontrado'  id='proveedor_encontrado' value='0'>
		Cargando...
		
		</div>
		
		</div>
		
		</td></tr></table>
		
		<center>
		
		<div id='titulosmenu' class='sub-content' style='width: 685px;'>
		<div class='sub-content'>
		<img src='iconos/page_add.png'> <b>Ingresar Art&iacute;culos</b>
		</div>
				
	");
	
	
	print("
	
		<div id='articulo' class='sub-content3' style='width: 673px;'>
		<center>
		
		<input type='hidden' name='id_articulo' id='id_articulo'>
		<input type='hidden' name='fecha' id='fecha'>
		
		<table cellpadding=1 cellspacing=0>
		<tr>
		<td><center><i>C&oacute;digo</td>
		<td><center><i>Vencimiento</td>
		<td><center><i>Cant.</td>
		<td colspan=2><center><i>Formato</td>
		<td colspan=2><center><i>Total Unidades</td>
		<td><center><i>Subtotal</td>
		<td colspan=2><center><i>Precio Unitario</td>
		</tr>
		
		<tr>
		<td><input type='text' id='prod_codigo' name='prod_codigo'
		size=12 onBlur='buscar_codigo_prod();' value=''></td>
		<td><input type='text' id='prod_fechavence' name='prod_fechavence'
		style='text-align: center;'
		size=8 onBlur='confirmar_fecha();' value=''></td>
		<td><input type='text' id='prod_cant' name='prod_cant' 
		size=5 style='text-align: right;' onKeyUp='calcular_cant();'></td>
		<td><select type='text' id='prod_formato' name='prod_formato' 
		onKeyUp='calcular_cant();'>
		<option value='0' SELECTED>Unidades</option>
		<option value='1'>Cajas/Paq.</option>
		</select></td>
		<td><input type='text' id='prod_formato_cant' name='prod_formato_cant' 
		size=3 style='text-align: right;' onKeyUp='calcular_cant();'></td>
		<td><center>=</center></td><td id='unidad_administracion' style='text-align: center;'>0</td>
		<td><input type='text' id='prod_subtotal' name='prod_subtotal' onKeyUp='
		if(event.which==13) agregar_art();
		calcular_cant();
		'
		size=10 style='text-align: right;'></td>
		<td><center>=</center></td><td id='precio_unitario' style='text-align: center;'>$0.-</td>
		
		</tr></table>
		
		<div id='detalle_prod'>
		
		<table>		
		
		<tr class='izquierda'><td style='text-align: right;'>Glosa:</td>
		<td colspan=3><input type='text' id='prod_glosa' name='prod_glosa' size=60 DISABLED></td>
		</tr>
		
		<tr class='izquierda'><td style='text-align: right;'>Nombre de Fantas&iacute;a:</td>
		<td colspan=3><input type='text' id='prod_nombre' name='prod_nombre'
		size=60 DISABLED></td></tr>
		
		<tr class='izquierda'><td style='text-align: right;'>Clasificaci&oacute;n:</td>
		<td>
		<select id='prod_clasif' name='prod_clasif' DISABLED>
		<option value=0 selected>(No Aplicable...)</option>
		" . ($clasificahtml) . "
		</select>
		</td><td style='text-align: right;'>Vence:</td>
		<td><select id='prod_vence' name='prod_vence' DISABLED>
		<option value='1' selected>Si</option>
		<option value='0'>No</option>
		</select></td>		
		</tr>
		
		<tr class='izquierda'><td style='text-align: right;'>Forma Farmaceutica:</td><td>
		<select id='prod_forma' name='prod_forma' DISABLED>
		<option value=0 selected>(No Aplicable...)</option>
		" . ($formahtml) . "
		</select>
		</td>
		<td style='text-align: right;'>Auge:</td>
		<td><select id='prod_auge' name='prod_auge' DISABLED>
		<option value='0' selected>No</option>
		<option value='1'>Si</option>
		</select>
		</td></tr>
		
		<tr class='izquierda'><td colspan=2>
		&nbsp;
		</td>
		<td style='text-align: right;'>Controlado:</td>
		<td><select id='prod_controlado' name='prod_controlado' DISABLED>
		<option value='0' selected>No</option>
		<option value='1'>Si</option>
		</select>
		</td></tr>
		
		</table>		
		<br>
		</div>
		
		</div>
	
		");
		
	print("	
	
		</div>
		
		</td></tr></table>
	
		</div>
		<center>
		
		<div class='sub-content' height='400' style='width:685px;'>
		<div class='sub-content'><img src='iconos/page.png'> <b>Detalle de Productos</b></div>
		<div id='nosel'>
		<center>(No se han seleccionado productos a ingresar...)</center>
		</div>
		<div id='seleccion' name='seleccion' class='sub-content2' style='display: none;'>
		
		<table id='tabla_seleccion' width='100%%'>
		<tr class='tabla_header'>
		<td class='tabla_header' width=80><b><i>C&oacute;digo Int.</i></b></td>
		<td width=200><b><i>Glosa Producto</i></b></td>
		<td><b><i>Vencimiento</i></b></td>
		<td><b><i>Cant.</i></b></td>
		<td colspan=2><b><i>Formato</i></b></td>
		<td width=100><b><i>Subtotal</i></b></td>
		</tr>
		
		
		</table>
		
		<table id='detalle_total' width='100%%'>
			<tr><td rowspan=3 style='text-align: center;' class='tabla_header'>Total</td>	
											<td width=100 style='text-align: right;' class='tabla_header'><b>Neto:</b></td>		
														<td width=100 style='text-align: right;'>$0.-</td></tr>
			<tr>							<td style='text-align: right;' class='tabla_header'><b>I.V.A.:</b></td>				
														<td style='text-align: right;'>$0.-</td></tr>
			<tr>							<td style='text-align: right;' class='tabla_header'><b>Total:</b></td>					
														<td style='text-align: right;'>$0.-</td></tr>
			
		</table>
		
		</div>
		</div>

		<br>
		
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
		<a href='#' onClick='cambiar_pagina(\"Recepci&oacute;n de Productos\",\"rec_prod\");'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
		
		</td></tr></table>
		
		<br><br>
		</form>
		
	");
	
	}	
	
	if($_GET['form']=='traslado') {
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','1=1',
	'ORDER BY bod_glosa'); 
	
	printf("
	
	<script type='text/javascript'>
	
		articulos_sel=0;
		art_num=0;
		buscar_win='';
	
		abrir_busqueda = function() {
		
			buscar_win = window.open('formularios.php?form=productos_traslado&'+$('bodega_origen').serialize(),
			'buscar_productos', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0');
			
			buscar_win.focus();
		
		}
		
		quitar_art = function(numero) {
		
			Element.remove('art_'+numero);
			
			articulos_sel = $('seleccion').getElementsByTagName('table');
			
			if(articulos_sel.length==0) {
				art_num=0;
				articulos_sel=0;
				$('nosel').style.display='';
				$('seleccion').style.display='none';
			}
		
		}
		
		seleccionar_articulo = function(idarticulo,cantidad) {
		
			idarticulo=idarticulo*1;
			cantidad=cantidad*1;
		
			var myAjax = new Ajax.Request(
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=sel_stock&id='+idarticulo+'&'+$('bodega_origen').serialize()+
				'&cantidad='+cantidad,
				onComplete: function (pedido_datos) {
					
					datos = eval(pedido_datos.responseText);
					
					detalle_art='';
					
					for(i=1;i<datos.length;i++) {
						detalle_art+=datos[i][0]+'='+datos[i][1];
						if(i<(datos.length-1)) { detalle_art=detalle_art+'!'; } 
					}
					
					producto='<table id=\"art_'+art_num+'\" name=\"art_'+art_num+'\" style=\"width: 720px;\"><input type=\"hidden\" name=\"id_art_'+art_num+'\" value=\"'+idarticulo+'\"><input type=\"hidden\" name=\"detalle_art_'+art_num+'\" value=\"'+detalle_art+'\">';
					
					i=0;
					
					datos.each( function(dato) {
						if(i==0) { 
							clase='tabla_fila'; clase2=''; estilo='<b>'; 
							agregar='<td rowspan='+datos.length+' width=\"5%%\"><center><a href=\"#\"><img src=\"borrar.png\" onClick=\"quitar_art('+art_num+')\" border=0></a></center></td>';
						} else { 
							clase='tabla_fila2'; clase2='derecha'; estilo=''; agregar='';
						}
						
						i++;
						producto+='<tr class=\"'+clase+'\"><td class=\"'+clase2+'\" width=\"50%%\">'+estilo+dato[0]+'</td><td  width=\"10%%\" class=\"derecha\">'+estilo+dato[1]+'</td>'+agregar+'</tr>';
					});
					
					producto+='</table>';
					
					
					if(articulos_sel==0) {
						$('nosel').style.display='none';
						$('seleccion').style.display='';
						
						articulos_sel=1;
					}
					
					$('seleccion').innerHTML+=producto;
					
					art_num++;
					
					buscar_win.$('buscar').value='';
					buscar_win.$('buscar').focus();
					
				}
			}
			
			);
		}
		
		verifica_tabla = function() {
		
			if($('bodega_origen').value==$('bodega_destino').value) {
				alert('La ubicacion de or&iacute;gen no puede ser la misma que la de destino.'.unescapeHTML());
				return;
			}
		
			if(art_num==0) {
				alert('No ha seleccionado productos para trasladar.');
				return;
			
			}
			
			if(($('numero_pedido').value*1)==0) {
				alert('Debe ingresar un n&uacute;mero de pedido.'.unescapeHTML());
			}
			
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=traslado_stock&cant='+art_num+'&'+$('buscar_form').serialize(),
				onComplete: function (pedido_datos) {
					
					if(pedido_datos.responseText=='OK') {
						alert('Traslado ingresado exitosamente.');
						cambiar_pagina(\"Traslado/Pr&eacute;stamo de Productos\",\"traslado\");
						
					} else {
						alert('ERROR:'+chr(13)+pedido_datos.responseText);
						
					}
				}
			}
			
			);
		}
	
	</script>
	
	<center>
	<form name='buscar_form' id='buscar_form'>
	
	<table><tr><td valign='top'>
	
	<center>
	
		<div id='bodega_origen_div' class='sub-content' style='width:300px;'>
		<center>
		<table>
		<tr><td colspan=2><center><b>Ubicaciones</b></center></td></tr>
		<tr><td class='derecha'>
		Or&iacute;gen:
		</td><td>
		<select name='bodega_origen' id='bodega_origen'>
		" . ($bodegashtml) . "
		</select>
		</td></tr><tr><td class='derecha'>
		Destino:
		</td><td>
		<select name='bodega_destino' id='bodega_destino'>
		" . ($bodegashtml) . "
		</select>
		</td></tr><tr><td class='derecha'>
		Nro. de Pedido:</td><td>
		<input type='text' id='numero_pedido' name='numero_pedido'>
		</td></tr></table>
		</center>
		</div>
	
	</td><td valign='bottom'>
				
	<div style='width: 450px;'>
	
	<center>
	<div class='boton'>
		<table><tr><td>
		<img src='informes.png'>
		</td><td>
		<a href='#' onClick='abrir_busqueda();'>Agregar Productos...</a>
		</td></tr></table>
	</div>
	</center>
	
	</div>
	
	</td></tr></table>
	
	<div class='sub-content' height='400' style='width:760px;'>
	<div class='sub-content'><b>Selecci&oacute;n de Productos</b></div>
	<div id='nosel'>
	(No se han seleccionado productos a trasladar...)
	</div>
	<div id='seleccion' name='seleccion' class='sub-content2' style='display: none;'>
	</div>
	</div>
	
	
	<br>
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='guardar.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'>Realizar Traslado...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='resetear.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina(\"Traslado/Pr&eacute;stamo de Productos\",\"traslado\");'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
	</td></tr></table><br>
	
	</center>
	
	</form>
	</center>
	");
	
	}
	
	if($_GET['form']=='recetas') {
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','1=1',
	'ORDER BY bod_glosa'); 
	

	printf("
	
	<script>
	
	mostrar_receta = function() {
	
		if($('tipo_receta').value==1) {
		
			receta_display='';
			
		}
		
		if($('tipo_receta').value==3) {
		
			receta_display='none';
			
		}
		
		$('fic_pac').style.display=receta_display;
		$('medico').style.display=receta_display;
		$('rut_pac').style.display=receta_display;
		$('nom_pac').style.display=receta_display;
		$('auge').style.display=receta_display;
		$('diagnos').style.display=receta_display;
		
	}
	
	abrir_busqueda = function() {
		
		buscar_win = window.open('formularios.php?form=productos_receta&'+$('bodega_origen').serialize(),
		'buscar_productos', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0');
			
		buscar_win.focus();
		
	}
	
		quitar_art = function(numero) {
		
			Element.remove('art_'+numero);
			
			articulos_sel = $('seleccion').getElementsByTagName('table');
			
			if(articulos_sel.length==0) {			
				art_num=0;
				articulos_sel=0;
				$('nosel').style.display='';
				$('seleccion').style.display='none';
			}
		
		}
		
		calcular_cantidad = function() {
		
			cant = Math.ceil(($('dosis').value)*(($('dias').value*24)/$('horas').value));
					
			if(cant>0) {
				$('cantidad').value=cant;
			} else {
				$('cantidad').value=0;
			}
			
		
		}
		
		seleccionar_articulo = function(idarticulo,cantidad) {
		
			idarticulo=idarticulo*1;
			cantidad=cantidad*1;
		
			var myAjax = new Ajax.Request(
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=sel_stock&id='+idarticulo+'&'+$('bodega_origen').serialize()+
				'&cantidad='+cantidad,
				onComplete: function (pedido_datos) {
		
					datos = eval(pedido_datos.responseText);
					
					detalle_art='';
					
					for(i=1;i<datos.length;i++) {
						detalle_art+=datos[i][0]+'='+datos[i][1];
						if(i<(datos.length-1)) { detalle_art=detalle_art+'!'; } 
					}
					
					producto='<table id=\"art_'+art_num+'\" name=\"art_'+art_num+'\" style=\"width: 410;\"><input type=\"hidden\" name=\"id_art_'+art_num+'\" value=\"'+idarticulo+'\"><input type=\"hidden\" name=\"detalle_art_'+art_num+'\" value=\"'+detalle_art+'\">';
					
					i=0;
					
					datos.each( function(dato) {
						if(i==0) { 
							clase='tabla_fila'; clase2=''; estilo='<b>'; 
							agregar='<td rowspan='+datos.length+' width=\"5%%\"><center><a href=\"#\"><img src=\"borrar.png\" onClick=\"quitar_art('+art_num+')\" border=0></a></center></td>';
						} else { 
							clase='tabla_fila2'; clase2='derecha'; estilo=''; agregar='';
						}
						
						i++;
						producto+='<tr class=\"'+clase+'\"><td class=\"'+clase2+'\" width=\"50%%\">'+estilo+dato[0]+'</td><td  width=\"15%%\" class=\"derecha\">'+estilo+dato[1]+'</td>'+agregar+'</tr>';
						
					});
					
					producto+='</table>';
					
					if(articulos_sel==0) {
						$('nosel').style.display='none';
						$('seleccion').style.display='';
						
						articulos_sel=1;
					}
					
					$('seleccion').innerHTML+=producto;
					
					art_num++;
					
					$('buscar').value='';
					
					$('buscar').focus();
				
				}
			}
			
			);
			
		}
		
		verifica_tabla = function() {
		
			if(!($('tipo_receta').value==3)) {
		
			if(($('numero_receta').value*1)==0) {
				alert('No ha ingresado n&uacute;mero de receta.'.unescapeHTML());
				return;
			}
			
			if(($('ficha_paciente').value*1)==0) {
				alert('No ha ingresado ficha de paciente.'.unescapeHTML());
				return;
			}
			
			if(($('rut_medico').value*1)==0) {
				alert('No ha especificado rut del m&eacute;dico.'.unescapeHTML());
				return;
			}
			
			if(articulos_sel==0) {
				alert('No ha seleccionado art&iacute;culos.'.unescapeHTML());
				return;
			}
			
			}
			
			var myAjax = new Ajax.Request(
			'sql.php', 
			{
				method: 'get', 
				parameters: 'accion=receta&cant='+art_num+'&'+$('receta').serialize(),
				onComplete: function (pedido_datos) {
					
					if(pedido_datos.responseText=='OK') {
					
						alert('Receta ingresada exitosamente.');
						cambiar_pagina(\"Ingreso de Recetas\",\"recetas\");
						
					} else {
					
						alert('ERROR:'+chr(13)+pedido_datos.responseText);
						
					}
				}
			}
			
			);
					
		}
	
		$('numero_receta').focus(); 
		articulos_sel=0;
		art_num=0;
	
	</script>
	
	<center>
	<form id='receta' name='receta'>
	<table>
	<tr><td valign='top'>
	
	<div class='sub-content'>
		<center><br>
		<b>Tipo de Receta:</b><br>
		<table><tr><td>
		<select name='tipo_receta' id='tipo_receta' onChange='mostrar_receta()'>
			<option value='1' selected> Receta Agudo</option>
			<option value='2'> Receta Cheque</option>
			<option value='3'> Receta Asistencia P&uacute;blica</option>
		</select>
		</td></tr></table>
		<br>
		</center>
	</div>
	
		<div class='sub-content'>
		<table>
		<tr><td class='derecha'>
		Ubicaci&oacute;n:
		</td><td>
		<select name='bodega_origen' id='bodega_origen'>
		" . ($bodegashtml) . "
		</select>
		</td></tr>
		<tr id='num_rec'><td class='derecha'>Nro. Receta:</td>
		<td><input type='text'  id='numero_receta' name='numero_receta' value=''></td></tr>
		<tr id='fic_pac'><td class='derecha'>Ficha Paciente:</td>
		<td><input type='text' id='ficha_paciente' name='ficha_paciente' value=''></td></tr>
		<tr id='medico'><td class='derecha'>M&eacute;dico:</td>
		<td><input type='text' id='rut_medico' name='rut_medico' value=''></td></tr>
		<tr id='rut_pac'><td class='derecha'>R.U.T. Paciente:</td><td></td></tr>
		<tr id='nom_pac'><td class='derecha'>Nombre Paciente:</td><td></td></tr>
		<tr id='auge'><td class='derecha'>Auge:</td><td></td></tr>
		<tr id='diagnos'><td class='derecha'>Diagn&oacute;stico:</td>
		<td><textarea id='diagnostico' name='diagnostico'></textarea></td></tr>
		</table>
		</div>
			
	</td><td valign='top'>
		
	<div style='width: 450px;'>
	
	<center>
	<div class='boton'>
		<table><tr><td>
		<img src='informes.png'>
		</td><td>
		<a href='#' onClick='abrir_busqueda();'>Agregar Productos...</a>
		</td></tr></table>
	</div>
	</center>
	
	</div>
	
		<div class='sub-content' height='400' style='width:450px;'>
			<div class='sub-content'><b>Selecci&oacute;n de Productos</b></div>
			<div id='nosel'>
				(No se han seleccionado productos para la receta...)
			</div>
			<div id='seleccion' name='seleccion' class='sub-content2' style='display: none;'>
			</div>
		</div>
	
	</td></tr></table>
	
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='guardar.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'>Ingresar Receta...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='resetear.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina(\"Ingreso de Recetas\",\"recetas\");'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
		
	</td></tr></table><br>
	</form>
	
	</center>
	
	");
	
	}
	
	if($_GET['form']=='stock') {
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'0','1=1',
	'ORDER BY bod_glosa'); 
	
	
	printf("
	
	<center>
	<br>
	<table>
	<tr class='tabla_header'><td colspan=2><b>Generar contabilizando desde:</b></td></tr>
	<tr><td style='text-align: right;'>Ubicaci&oacute;n de Stock:
	</td><td>
	<select name='bodega'>
	<option value=0 selected>(Global...)</option>
	" . $bodegashtml . "
	</select>
	</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr class='tabla_header'><td colspan=2><b>Inclu&iacute;r los siguientes campos:</b></td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>C&oacute;digo</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Nombre</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Forma Farmac&eacute;utica</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Stock de Pedido</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Stock de Cr&iacute;tico</td></tr>
	<tr><td style='text-align: right;'><input type='checkbox' name='codigo'></td><td>Stock Actual</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr class='tabla_header'><td colspan=2><b>Cumpliendo con las sgtes. condiciones:</b></td></tr>
	<tr><td colspan=2><center>
	<select name='condicion'>
	<option value=0 selected>(Ninguna...)</option>
	<option value=1 >Stock &lt; Stock Pedido</option>
	<option value=2 >Stock &lt; Stock Cr&iacute;tico</option>
	</select>
	</center></td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr class='tabla_header'><td colspan=2><b>En el sgte. Formato:</b></td></tr>
	<tr><td colspan=2><center><select name='formato'>
	<option value=0 selected>P&aacute;gina Web</option>
	<option value=1>Planilla .xls (MS Excel)</option>
	<option value=2>Documento PDF (Adobe Acrobat)</option>
	</select></center></td></tr>
	</table>
	<br>
	</center>
	
	");
	
	}
	
	if($_GET['form']=='productos_traslado' or $_GET['form']=='productos_receta') {
	
	$bodega_origen = $_GET['bodega_origen'];
	
	printf("
	<html>
	
	<title>Busqueda de Productos para Traslado</title>
	
	<script src='prototype.js' type='text/javascript'></script>
	
	<link rel='stylesheet' href='estilos.css' type='text/css'>	
	
	<style>

	body {
		font-family: Arial, Helvetica, sans-serif;
	}

	</style>
	
	<script>
	
		realizar_busqueda = function(pagina,orden,orienta) {
			
			if($('buscar').value.length<2) {
				return;
			}
		
			var myAjax = new Ajax.Updater(
			'busqueda', 
			'registro.php', 
			{
				method: 'get', 
				evalScripts: true,
				parameters: 'tipo=busca_prod&'+$('buscar').serialize()+'&'+$('bodega_origen').serialize()+'&pagina='+pagina+'&orden='+orden+'&orienta='+orienta
			}
			
			);
		
		}
		
		abrir_producto = function(idarticulo, foco) {
		
			var myAjax = new Ajax.Updater(
			'busqueda', 
			'mostrar.php', 
			{
				method: 'get', 
				parameters: 'tipo=stock&id='+idarticulo+'&'+$('bodega_origen').serialize(),
				onComplete: function() {
					if(foco==1) $('cantidad').focus();
				}
			}
			
			);
		}
		
		sel_art = function(idprod,cantprod) {

				window.opener.seleccionar_articulo(idprod,cantprod);
				
		}

	</script>
	
	<body 
	onLoad='
		$(\"buscar\").focus();
	' 
	style='background-color: #ddd;'>
	<input type='hidden' name='bodega_origen' id='bodega_origen' value='".$bodega_origen."'>
	<div id='articulos' class='sub-content'>
	<center>
	<table><tr><td><img src='lupa.png' border=0></td><td>
	Buscar Art&iacute;culos:
	</td><td>
	<input type='text' id='buscar' name='buscar' onKeyUp='
	realizar_busqueda(0,\"art_glosa\", 0);
	' size=40>
	</td><td>
	</td></tr></table>
	</center>
	</div>
	
	<div id='busqueda' class='sub-content2' style='
	min-height:300px;
  	height:auto !important;
  	height:300px;
	'>
		<center>
		(No se ha realizado a&uacute;n una b&uacute;squeda...)
		</center>
	</div>
	
	</body>
	
	");
	
	}

?>