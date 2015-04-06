// JavaScript
// Formulario de Ingreso/Edición de Artículos

  alert("!!!");

	bloquear_boton=true;
	
	abrir_busqueda = function() {
		
			buscar_win = window.open('formularios.php?form=productos_buscar',
			'buscar_productos', 'left='+(screen.width-620)+',top='+(screen.height-270)+',width=600,height=250,status=0');
			
			buscar_win.focus();
		
		}
		
	seleccionar_articulo = function(codigo_enviado) {
  
    $('prod_codigo').value=codigo_enviado;
    
    buscar_win.close();
  
    $('prod_codigo').focus();
    buscar_codigo_prod();
    
  }
	
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
					prod_item_text = document.getElementById('prod_item');
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
						prod_item_text.selectedIndex=0;
						
						$('item_icono').src='iconos/cross.png';
						
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
					sel_valor(prod_item_text,datos_nuevos[8]);
					
					if(prod_item_text.value==0)
							{ $('item_icono').src='iconos/cross.png'; } 
					else 	{ $('item_icono').src='iconos/tick.png'; }

					
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
						alert('Error: \r\n'+pedido_datos.responseText.unescapeHTML());
					}
					
				}
			}		
			);	
		
		}
		
		$('prod_codigo').focus();
		
