
	<script>
	
	cargar_listado = function() {
	
		var myAjax = new Ajax.Updater(
			'listado', 
			'administracion/funcionarios/listado_funcionarios.php', 
			{
				method: 'get', 
				parameters: 'orden='+($('personal_orden').value*1)+'&buscar='+serializar('personal_filtro'),
				evalScripts: true
				
			}
			
			);
	
	}
	
	seleccionar_usuario = function(idusuario) {
	
		var myAjax = new Ajax.Request(
			'administracion/funcionarios/bajar_funcionario.php', 
			{
				method: 'get', 
				parameters: 'buscar='+(idusuario*1),
				onComplete: function(pedido_datos) {
				
					try {
          
          datos=eval(pedido_datos.responseText);
					
					func_id_text.value = datos[0];
					func_rut_text.value = datos[1];
					func_clave_text.value = datos[2].unescapeHTML();
					func_nombre_text.value = datos[3].unescapeHTML();
					func_cargo_text.value = datos[4].unescapeHTML();
					
					func_rut_text.disabled=true;
					func_nombre_text.disabled=true;
					func_cargo_text.disabled=true;
					func_clave_text.disabled=true;
					
					$('editar_boton').style.display='';
					$('borrar_boton').style.display='';
					$('permisos_boton').style.display='';
					$('guardar_boton').style.display='none';
					
					} catch (err) { alert("ERROR:\n\n"+err); }
					
				}
				
			}
			
			);
	
	}
	
	agregar_usuario = function () {
	
		
		func_rut_text.disabled=false;
		func_nombre_text.disabled=false;
		func_cargo_text.disabled=false;
		func_clave_text.disabled=false;
		
		func_id_text.value=0;
		func_rut_text.value='';
		func_nombre_text.value='';
		func_cargo_text.value='';
		func_clave_text.value='';
				
		$('guardar_texto').innerHTML = 'Guardar Usuario Nuevo...';
		$('guardar_boton').style.display='';
		$('borrar_boton').style.display='none';
		$('editar_boton').style.display='none';
		$('permisos_boton').style.display='none';
    			
		func_rut_text.focus();
		
	}
	
	editar_usuario = function() {
	
		func_rut_text.disabled=false;
		func_nombre_text.disabled=false;
		func_cargo_text.disabled=false;
		func_clave_text.disabled=false;
					
		$('guardar_texto').innerHTML = 'Guardar Cambios a Usuario...';
		$('guardar_boton').style.display='';
		
		func_nombre_text.focus();
		func_nombre_text.select();
				
	
	}
	
	borrar_usuario = function() {
		
		confirma=confirm('¿Est&aacute; seguro que desea eliminar este usuario? - No hay opciones para deshacer.'.unescapeHTML());
		
	}
	
	verifica_tabla = function() {
		
			
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
			'administracion/funcionarios/sql.php', 
			{
				method: 'get', 
				parameters: serializar_objetos('registro'),
				onComplete: function(pedido_datos) {
				  
				  try {
				
				  datos = pedido_datos.responseText.evalJSON(true);
				
					if(pedido_datos.responseText=='1') {
					
						if(func_id_text.value='') {
							alert('Ingreso de usuario realizado exitosamente.'.unescapeHTML());
						} else {
							alert('Edici&oacute;n de usuario realizado exitosamente.'.unescapeHTML());
							
						}
						
						func_rut_text.disabled=true;
						func_nombre_text.disabled=true;
						func_cargo_text.disabled=true;
						func_clave_text.disabled=true;
						
						$('editar_boton').style.display='';
						$('borrar_boton').style.display='';
						$('permisos_boton').style.display='';
						$('guardar_boton').style.display='none';
						
						cargar_listado();
						
					} 
          
          } catch (err) {
						alert('Error: \n\n'+err);
						
					}
					
				}
			}		
			);	
		
		}
		
	
	permisos_funcionario = function () {
  
    var win = new Window("func_accesos", {className: "alphacube", 
                          top:40, left:0, 
                          width: 800, height: 550, 
                          title: '<img src="iconos/key_go.png"> Accesos del Funcionario al Sistema',
                          minWidth: 800, minHeight: 550,
                          maximizable: false, minimizable: false, 
                          wiredDrag: true, resizable: false });
                          
    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
    
    win.setAjaxContent('administracion/funcionarios/acceso_funcionario.php', 
			{
				method: 'get', 
				evalScripts: true,
        parameters: func_id_text.serialize()
	
			});
			
		$("func_accesos").win_obj=win;
  
    win.setDestroyOnClose();
    win.showCenter();
    win.show(true);
		
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
	
	<div class='boton' id='permisos_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/computer_key.png'>
	</td><td>
	<a href='#' onClick='permisos_funcionario();'>Permisos Usuario...</a>
	</td></tr></table>
	</div>
	
	</center>
	
	</div>
	
	<div class='sub-content'>
	
	<div class='sub-content'><img src='iconos/user_green.png'> <b>Datos del Funcionario</b></div>
	
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
	
	<script>
	
	func_id_text = document.getElementById('func_id');
	func_rut_text = document.getElementById('func_rut');
	func_clave_text = document.getElementById('func_clave');
	func_nombre_text = document.getElementById('func_nombre');
	func_cargo_text = document.getElementById('func_cargo');
	
  </script>
  				
