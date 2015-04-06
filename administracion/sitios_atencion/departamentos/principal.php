	<script type="text/javascript">
  function cargar_listado() {
	
	var myAjax = new Ajax.Updater(
		'listado',
		'administracion/departamentos/listadoDeptos.php', {
			method: 'get',
			parameters: 'tipo=listado&buscar='+serializar('deptos_filtro'),
			evalScripts: true }
	);
}

function seleccionar_depto(idDepto) {
	//alert(idDepto);
		var myAjax1 = new Ajax.Request(
			'administracion/departamentos/listadoDeptos.php', 
			{
				method: 'get', 
				parameters: 'tipo=depto&buscar='+(idDepto*1),
				onComplete: function(pedido_datos) {
				
					datos = eval(pedido_datos.responseText);
					//alert(datos.length);
					//alert(datos[2][1]);
					
					func_id_text = document.getElementById('func_id');
					func_nombre_text = document.getElementById('func_nombre');
					
					func_id_text.value = datos[0].unescapeHTML();
					func_nombre_text.value = datos[1].unescapeHTML();
					
					var myAjax2 = new Ajax.Updater(
						'div_ips',
						'administracion/departamentos/listadoDeptos.php', {
							method: 'get',
							parameters: 'tipo=listarIps2&idDepto='+idDepto+'&arreglo='+datos[2],
							evalScripts: true,
							onComplete: function(pedido_ips) {
								func_ip_text = document.getElementById('ip');
								//alert(datos[2]);
							}
						}
					);
					
					
					func_nombre_text.disabled = true;
					
					Element.remove('listadoPermisos');
					Element.remove('listadoPermisos1');
					new Insertion.Top('listar_ips2','<div id=\'listadoPermisos\'><img src=\'../../iconos/computer_link.png\'><a href=\'#\' onClick=\'nueva_ventana("Listado de Ips asociadas al Departamento", 450, 300, true, "frame_ips.php?tipo=ver&idDepto='+func_id_text.value+'");\'>Listar Ips...</a></div>');
					new Insertion.Top('listar_ips21','<div id=\'listadoPermisos1\'><table><tr><td><img src=\'../../iconos/computer_link.png\'></td><td><a href=\'#\' onClick=\'nueva_ventana("Listado de Ips asociadas al Departamento", 450, 300, true, "frame_ips.php?tipo=ver&idDepto='+func_id_text.value+'");\'>Listar Ips...</a></td></tr></table></div>');
					$('listar_ips2').style.display='';
					$('listar_ips21').style.display='';
					$('editar_boton').style.display='';
					$('borrar_boton').style.display='';
					$('guardar_boton').style.display='none';
					
				}
			}
		);		
		
}

function agregar_depto() {
	func_id_text = document.getElementById('func_id');
	func_nombre_text = document.getElementById('func_nombre');
	
	func_nombre_text.disabled = false;
	
	func_id_text.value = '';
	func_nombre_text.value = '';
	
	$('guardar_texto').innerHTML = 'Guardar Departamento...';
	$('guardar_boton').style.display='';
	$('borrar_boton').style.display='none';
	$('editar_boton').style.display='none';
					
	func_nombre_text.focus();
	
	$('listar_ips2').style.display='none';
}


function editar_depto() {
	func_id_text = document.getElementById('func_id');
	func_nombre_text = document.getElementById('func_nombre');
	
	func_nombre_text.disabled=false;
		
	$('guardar_texto').innerHTML = 'Guardar Cambios Departamento...';
	$('guardar_boton').style.display='';
		
	func_nombre_text.focus();
	func_nombre_text.select();
				
	
}

function borrar_depto() {
		 
		confirma = confirm('¿Est&aacute; seguro que desea eliminar este departamento?'.unescapeHTML());
		if(confirma) {
			func_id_text = document.getElementById('func_id');
			
			var majax = new Ajax.Request(
				'administracion/departamentos/listadoDeptos.php',
				{
					method: 'get', 
					parameters: 'tipo=delete&'+serializar_objetos('registro'),
					onComplete:function(pedido_datos) { 
						if(pedido_datos.responseText == '1') {
							func_nombre_text.disabled	= true;
									
							func_id_text.value='';
							func_nombre_text.value='';
							
							//location.reload();
							cargar_listado();
						} else {
							alert('Error: \\r\\n'+pedido_datos.responseText.unescapeHTML());
						}
					}
				}
			);
			
		} else {
			return;
		}
		
	}


function nueva_ventana(titulo, ancho, alto, max, url) {
    
    //$('contenido').innerHTML='';
    
    //Windows.closeAll();
    
    var win = new Window("win5", {className: "alphacube", top:40, left:0, width: ancho, height: alto, title: titulo,
                          minWidth: ancho, minHeight: alto,
                          maximizable: false, minimizable: false,
                          url: url, wiredDrag: true });
                          
    win.setConstraint(true, {left:10, right:10, top: 75, bottom:10})
    win.setDestroyOnClose();
    win.showCenter();
    win.show();
    
  }

function verifica_tabla() {
	func_nombre_text = document.getElementById('func_nombre');
	
	//alert(serializar_objetos('registro'));
	
	if(trim(func_nombre_text.value)=='') {
		alert('El campo Nombre est&aacute; vac&iacute;o.'.unescapeHTML());
		func_nombre_text.select();
		return;
	}	
			
	var myAjax = new Ajax.Request(
		'administracion/departamentos/listadoDeptos.php', 
		{
			method: 'get', 
			parameters: 'tipo=ingreso_edicion_depto&'+serializar_objetos('registro'),
			onComplete: function(pedido_datos) {
				
					if(pedido_datos.responseText == '1') {
					
						func_id_text = document.getElementById('func_id');
		
						if(func_id_text.value='') {
							alert('Ingreso de Departamento realizado exitosamente.'.unescapeHTML());
						} else {
							alert('Edici&oacute;n de Departamento realizado exitosamente.'.unescapeHTML());
						}
						
						func_id_text = document.getElementById('func_id');
						func_nombre_text = document.getElementById('func_nombre');
						
						func_nombre_text.disabled=true;
						/*
						Element.remove('listadoPermisos');
					new Insertion.Top('listar_ips2','<div id=\'listadoPermisos\'><a href=\'#\' onClick=\'nueva_ventana("Listado de Ips asociadas al Departamento", 450, 400, true, "frame_ips.php?tipo=ver&idDepto='+func_id_text.value+'");\'>Listar Ips...</a></div>');
					$('listar_ips2').style.display='';
				*/
						$('editar_boton').style.display ='none';
						$('borrar_boton').style.display ='none';
						$('guardar_boton').style.display='none';
						
						cargar_listado();
						
					} else {
						alert('Error: \\r\\n'+pedido_datos.responseText.unescapeHTML());
					}
					
				}
			}		
			);
		
		//cargar_listado();
		}

function muestraForm(parent, comp) {
	window.document.body.style.cursor = 'wait';
	
	func_id_text = document.getElementById('func_id');
	alert(func_id_text.value);
	
	var majax = new Ajax.Updater(
		parent, 
			"administracion/departamentos/"+comp+".php",
			{
				method: 'get', 
				parameters: 'op=ver&idParent='+func_id_text.value,
				evalScripts: true,
				onSuccess: function() {
					Element.remove('centro');
					Element.remove('formBox');
					new Insertion.Top('contenidoP','<div id=\'centro\'></div>');
					new Insertion.Bottom('contenidoP','<div id=\'formBox\'></div>');
					$(parent).style.display = 'block';
					window.document.body.style.cursor='default';
		
		
				}
			}
			
	);
			
}

function ingresaIp(formulario) {
		//parent = "tablaItems";
		var params = $(formulario).serialize(true);
		//alert(params);
		var majax = new Ajax.Updater(
			'tablaItems', 
			"administracion/departamentos/"+formulario+".php?op=post",
			{
				asynchronous:true, 
				parameters: params,
				onComplete:function() { 
					//Element.remove('contenidoP');
					Element.remove('centro');
					Element.remove('formBox');
										
					location.reload();
				}
			}
		);
	}

function cancelaIngresoIp(comp) {
		var majax = new Ajax.Updater(
			'formBox', 
			"administracion/departamentos/"+comp+".php?op=remueveForm",
			{
				asynchronous:true, 
				parameters: 'op=remueveForm',
				onComplete:function() { 
					Element.remove('centro');
					Element.remove('formBox');
					new Insertion.Top('contenidoP','<div id=\'centro\'></div>');
					new Insertion.Bottom('contenidoP','<div id=\'formBox\'></div>');
				}
			}
		);
	}

function editarIp(idDepto, ip) {
	//alert(idDepto+" "+ip);
	ipActual = ip;
	ipNomNum = 'ip_'+ip;
	campoIp		= document.getElementById(ipNomNum);
	
	campoIp.disabled = false;
	
	//alert(campoIp.value+' ip_'+ip);
	
	$('guardar_texto').innerHTML = '<a href="#" onClick="actualizarIp(ipActual, campoIp.value);">Guardar Cambios...</a>';
	$('guardar_boton').style.display='';
	
	campoIp.focus();
	campoIp.select();
	
}

function actualizarIp(oldIp, newIp) {
	idDepto = document.getElementById('func_id');
	//alert(idDepto.value+' '+oldIp+' '+newIp);
	
	var majax = new Ajax.Updater(
				'tablaItems', 
				"administracion/departamentos/_ingresaIp.php",
				{
					method: 'get', 
					parameters: 'op=update&idDepto='+idDepto.value+'&ipOld='+oldIp+'&ipNew='+newIp,
					onComplete:function() {
												
						location.reload(); 
					}
				}
			);
	
}

function borrarIp(idDepto, ip) {
	//alert(idDepto+" "+ip);
	//alert(id);
		ipTxt = document.getElementById('ip_'+ip);
		ipTxtValue = ipTxt.value;
		confirmacion=confirm('Realmente desea ELIMINAR la ip \"'+ipTxtValue+'\"?'.unescapeHTML());
		//alert(confirmacion);
		if(confirmacion) {
		
			var majax = new Ajax.Updater(
				'tablaItems', 
				"administracion/departamentos/_ingresaIp.php",
				{
					method: 'get', 
					parameters: 'op=del&idDepto='+idDepto+'&ip='+ipTxtValue,
					onComplete:function() {
												
						location.reload(); 
					}
				}
			);
		} else {
			return;
		}
}

function mostrarDiv(div) {
	new Effect.toggle(div, 'blind');	
}
  </script>

	<link rel="stylesheet" type="text/css" href="../../css/interface.css">
	<body style='font-family: Arial, Helvetica, sans-serif;' onLoad='cargar_listado();'>
	<center>
	<table><tr><td valign="top">
	
	<div class="sub-content">
	
	<div class="sub-content"><img src="iconos/table.png"> <b>B&uacute;squeda por Listado</b></div>
	
	<div class="sub-content">
	<table>
	<tr>
		<td style="text-align: right;"><b>Filtrar:</b></td><td>
	<input type="text" name="deptos_filtro" id="deptos_filtro" size=30 onKeyUp='cargar_listado();'>
	</td></tr>
	</table></div>
	
	<div class="sub-content3" id="listado">
		
	</div>
    <div class="sub-content3" id="listaplop">
		
	</div>
    
		
	</div>
	
	</td><td valign="top">
	
	<div class="sub-content">
	
	<center>
	<div class="boton" id="agregar_boton">
	<table><tr><td>
	<img src="iconos/user_add.png">
	</td>
	<td>
		<a href="#" onClick='agregar_depto();'>Agregar Departamento...</a>
	</td></tr></table>
	</div>
    
    <div class="boton" id="editar_boton" style="display: none;">
	<table><tr><td>
	<img src="iconos/user_edit.png">
	</td><td>
	<a href="#" onClick='editar_depto();'>Editar Departamento...</a>
	</td></tr></table>
	</div>
	
	<div class="boton" id="borrar_boton" style="display: none;">
	<table><tr><td>
	<img src="iconos/user_delete.png">
	</td><td>
	<a href="#" onClick='borrar_depto();'>Eliminar Departamento...</a>
	</td></tr></table>
	</div>
    
    <div class="boton" id="listar_ips21" style="display: none;">
    	<div id="listadoPermisos1"></div>
	</div>
	
	</center>
	
	</div>
	
	<div class="sub-content">
	
	<div class="sub-content"><img src="iconos/user_green.png"> <b>Datos del Departamento</b></div>
	
	<div class="sub-content3" id="registro">
	<input type="hidden" name="func_id" id="func_id">
	<table style="padding: 5px;">
	<tr><td style="text-align: right;">Nombre:</td>	
	<td><input type="text" name="func_nombre" id="func_nombre" size=25 DISABLED></td></tr>
	
	<tr>
    	<td align="left" valign="top">
	    </td>
		<td align="right" valign="top">
		
	    	<div class="boton" id="listar_ips2" style="display: none;">
            <div id="listadoPermisos"></div>
	    	</div>
        
		</td>
	</tr>
	
	</table>
	
	<center>
	<div class="boton" id="guardar_boton" style="display: none;">
	<table><tr><td>
	<img src="../../iconos/user_go.png">
	</td><td>
	<a href="#" onClick='verifica_tabla();'><span id="guardar_texto">Guardar cambios a Usuario...</span></a>
	</td></tr></table>
	</div>
    
	</center>
	
	</div>
	
	</div>
	
	</td></tr></table>
	
	</center>
	</body>
</html>
	   
