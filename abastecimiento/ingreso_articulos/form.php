<?php

  require_once("../../conectar_db.php");

	$clasificahtml = desplegar_opciones("bodega_clasificacion", 
	"clasifica_id, clasifica_nombre",'','true','ORDER BY clasifica_nombre'); 
	
	$itemshtml = desplegar_opciones("item_presupuestario", 
	"item_codigo, item_glosa",'','true','ORDER BY item_codigo'); 
	
	$formahtml = desplegar_opciones("bodega_forma", 
	"forma_id, forma_nombre",'','true','ORDER BY forma_nombre'); 

  $prioridadhtml = desplegar_opciones("art_prioridad", 
	"art_prioridad_id, art_prioridad_glosa",'1','true',
  'ORDER BY art_prioridad_id DESC'); 

  $controlhtml = desplegar_opciones("receta_tipo_talonario", 
	"tipotalonario_id, tipotalonario_medicamento_clase",'-1','true',
  'ORDER BY tipotalonario_id'); 
	
	?>
	
	<script>
	
	bloquear_boton=true;
	bloquear_ingreso=false;
	
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
  
  
  buscar_arts_prod = function() {
  
  codigo_art = document.getElementById('codigos');
  
  $('prod_id').value='';
  $('cargandoimg').style.display='';
  
  var myAjax2 = new Ajax.Updater(
      'lista_productos',
			'abastecimiento/bincard_articulos/buscar_arts.php',
			{
				method: 'get',
				parameters: 'codigo='+serializar(codigo_art),
				evalScripts: true,
				onComplete: function (pedido_datos) {
				
				  try {
				  
				  $('codigos').style.background=''; // Pone Normal, Existe
					$('cargandoimg').style.display='none';
          $('producto_detalle').style.display='';
          
          if($('id_articulo').value>0) {
            marcar_art($('id_articulo').value);
          }
					
          } catch(err) {
            alert(err);
            
          }
          
				}	
			}
			
			);
			
  }
  
  seleccionar_art = function (art_id, art_codigo) {
  
    $('prod_codigo').value=art_codigo;
    
    marcar_art(art_id);
    
    buscar_codigo_prod();
    
  }
  
  marcar_art = function(art_id) {
    
    if($('art_fila_'+$('prod_id').value)) {
    
      art_fila = $('art_fila_'+$('prod_id').value);
      art_fila2 = $('art_fila_'+$('prod_id').value+'___2');
      
      art_fila.className=art_fila.clase;
      art_fila2.className=art_fila2.clase;
    
    }
    
    if($('art_fila_'+art_id)) {
    
      $('art_fila_'+art_id).clase=$('art_fila_'+art_id).className;
      $('art_fila_'+art_id+'___2').clase=$('art_fila_'+art_id+'___2').className;
    
      $('art_fila_'+art_id).className='mouse_over';
      $('art_fila_'+art_id+'___2').className='mouse_over';
    
    }

    $('prod_id').value=art_id;
    
  }
  
  
	buscar_codigo_prod = function() {
		
			articulo_div = document.getElementById('articulo')
			prod_codigo_text = document.getElementById('prod_codigo');
			prod_glosa_text = document.getElementById('prod_glosa');
			
      bloquear_boton=true;
			
			if(prod_codigo_text.value.length<5) {
				return;
			}
			
			// articulo_div.style.background='#afffbc'; // Pone Verde - Cargando Datos
			$('cargando_codigo').style.display='';
			
			var myAjax2 = new Ajax.Request(
			'registro.php',
			{
				method: 'get',
				parameters: 'tipo=articulo&codigo='+serializar(prod_codigo_text),
				onComplete: function (pedido_datos) {
				
				  try {
				
				  datos_nuevos = eval(pedido_datos.responseText);
					
					prod_id_text = document.getElementById('id_articulo');
					prod_glosa_text = document.getElementById('prod_glosa');
					prod_nombre_text = document.getElementById('prod_nombre');
					prod_item_text = document.getElementById('prod_item');
					prod_clasif_text = document.getElementById('prod_clasif');
					prod_vence_text = document.getElementById('prod_vence');
					prod_forma_text = document.getElementById('prod_forma');
					prod_auge_text = document.getElementById('prod_auge');
					prod_control_text = document.getElementById('prod_control');
					prod_prioridad_text = document.getElementById('prod_prioridad');
					prod_activado_text = document.getElementById('prod_activado');
					prod_valor_text = document.getElementById('art_valor');
					
					if(!datos_nuevos) { 
					
						// articulo_div.style.background='#fffeaf'; // Pone Amarillo - Ingreso Completo
						$('cargando_codigo').style.display='none';

						prod_vence_text.selectedIndex=0;
						prod_forma_text.selectedIndex=0;
						prod_clasif_text.selectedIndex=0;
						prod_item_text.selectedIndex=0;
						prod_prioridad_text.value=1;
						prod_activado_text.checked=true;
						
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
						
						cargar_stock_critico();
						
						bloquear_boton=false;
						
						return; 
					}
					

					prod_id_text.value=datos_nuevos[0].unescapeHTML();
					prod_glosa_text.value=datos_nuevos[1].unescapeHTML();
					prod_nombre_text.value=datos_nuevos[1].unescapeHTML();
					
					selval(prod_forma_text,datos_nuevos[4]);
					selval(prod_clasif_text,datos_nuevos[5]);
					selval(prod_item_text,datos_nuevos[8]);
					selval(prod_control_text,datos_nuevos[11]);
					
					prod_valor_text.value = formatoDinero(datos_nuevos[12]);
					
					if(prod_item_text.value==0)
							{ $('item_icono').src='iconos/cross.png'; } 
					else 	{ $('item_icono').src='iconos/tick.png'; }

					
					if(!(datos_nuevos[2]*1)) { vence=0;	} 	else { vence=1;	}
					if(datos_nuevos[6]=='f') { auge=0; } 	else { auge=1; }
					
					prod_vence_text.value=vence;
					prod_auge_text.value=auge;
					
					prod_prioridad_text.value=(datos_nuevos[9]*1);
					
					if(datos_nuevos[10]=='t') {
            prod_activado_text.checked=true;  
          } else {
            prod_activado_text.checked=false;
          }
					
					// articulo_div.style.background='#5390ff';	// Pone Azul - Edición de Artículo
					$('cargando_codigo').style.display='none';

					bloquear_boton=false;
					
					$('guardar_boton').style.display='';
						
					$('texto_boton').innerHTML='Guardar Cambios a Art&iacute;culo...';
					
					$('imagen_titulo').src='iconos/script_edit.png';
						
					$('titulo_formulario').innerHTML='Datos de Producto';
						
					cargar_stock_critico();
          	
					prod_glosa_text.select();
					
					$('variacion').innerHTML='<img src="graficos/variacion_precio.php?art_id='+($('id_articulo').value*1)+'&r='+(Math.floor(Math.random()*10000000))+'" />';
					
					} catch(err) {
						
						alert(err);
					}
					
				}	
			}
			
			);
			
		}
		
		cargar_stock_critico = function() {
    
      $('stock_criticos').style.display='';
      
      $('contenido_stock').innerHTML='<br><img src="imagenes/ajax-loader2.gif"><br><br>';
      
      var myAjaxian = new Ajax.Updater(
      'contenido_stock',
      'abastecimiento/ingreso_articulos/stock_critico.php',
      {
        method: 'get',
        parameters: 'art_id='+encodeURIComponent($('id_articulo').value)
      });
    
    }
    
    comprobar_item = function(articulo) {
	
	   if(($('pedido_'+articulo).value*1)>0) {
	
		    if(($('critico_'+articulo).value*1)>0) {
		
			   if(($('pedido_'+articulo).value*1)>($('critico_'+articulo).value*1)) {
			
				  $('aceptar_'+articulo).style.display='';
				  $('error_'+articulo).style.display='none';
				
				 } else {
      
          $('aceptar_'+articulo).style.display='none';
				  $('error_'+articulo).style.display='';
				
				 }
		
		  }
	
	   } else {
  
        $('aceptar_'+articulo).style.display='none';
		    
        if($('art_stock_'+articulo).value!='')
          $('error_'+articulo).style.display='';
        else
          $('error_'+articulo).style.display='none';
        
    }
	}
		
		verifica_tabla = function() {
		
			prod_id_text = document.getElementById('id_articulo');
			//prod_glosa_text = document.getElementById('prod_glosa');
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
				
			
			if(bloquear_ingreso) return;
			
			bloquear_ingreso=true;
			
			var myAjax = new Ajax.Request(
			'abastecimiento/ingreso_articulos/sql.php', 
			{
				method: 'get', 
				parameters: serializar_objetos('articulo'),
				onComplete: function(r) {
				
					if(r.responseText!='') {
						resp=r.responseText.split('|');
						alert('Ingreso/Edici&oacute;n de art&iacute;culo realizado exitosamente.'.unescapeHTML());
						$('id_articulo').value=resp[0];
						$('prod_codigo').value=resp[1];
						buscar_codigo_prod();
					} else {
						alert('Error: \r\n'+pedido_datos.responseText.unescapeHTML());
					}
					
					bloquear_ingreso=false;
					
				}
			}		
			);	
		
		}
		
		abrir_articulo = function(d) {
      	
      	$('id_articulo').value=d[5];
		$('prod_glosa').value=d[2].unescapeHTML();
		$('prod_nombre').value=d[2].unescapeHTML();
		$('prod_codigo').value=d[0];
		
      	buscar_codigo_prod();
			
   }
  
  	autocompletar_medicamentos = new AutoComplete(
      'prod_codigo', 
      'autocompletar_sql.php',
      function() {
        if($('prod_codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts&codigo='+encodeURIComponent($('prod_codigo').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 3, abrir_articulo);
	
		
	generar_codigo = function(){
		
		var myAjax2 = new Ajax.Updater(
		'prod_codigo',
			'abastecimiento/ingreso_articulos/genera_cod.php',
			{			
				evalScripts: true,
				onComplete: function (codigo) {
				
				  try {
						$('prod_codigo').value=codigo.responseText;
						buscar_codigo_prod();
					
					} catch(err) {
						alert(err);
					}
          
				}	
			}
			
			);
	}
	</script>
	<center>
	<table><tr><td>
	
	<div class='sub-content'>
                <div class='sub-content'>
                    <img src='iconos/page_gear.png'>
                    <b>Ingreso/Edici&oacute;n de Art&iacute;culos</b>
                </div>
	<center>
		
	<table><tr><td valign='top'>
	
	<div class='sub-content' id='producto_detalle'
  style='width:300px;'>
  <div class='sub-content'>
  <img src='iconos/script.png'> <b>Listado de Art&iacute;culos</b></div>
  <div class='sub-content'>
  <table>
  <tr>
  <td>Filtro:</td>
  <td>
  <input type='text' id='codigos' name='codigos' style='width:200px;' 
  onChange='buscar_arts_prod();'
  onKeyUp='if(event.which==13) buscar_arts_prod();'>
  <img id='cargandoimg'
  src='imagenes/ajax-loader1.gif' style='display: none;'>
  
  <input type='hidden' id='prod_id' name='prod_id'
   value=''>
  
  </td>
  </tr></table>
  </div>
  <div class='sub-content2' id='lista_productos' style='height: 300px; overflow:auto;'>
  (No ha seleccionado art&iacute;culos...)
  </div>
  
  </div>
  
  </td><td valign='top'>
	
	<div class='sub-content' style='width: 450px;'>
	
	<div class='sub-content'>
	<img src='iconos/script.png' id='imagen_titulo'> 
	<b><span id='titulo_formulario'>Datos de Producto</span></b></div>
	
	<div class='sub-content2' id='articulo'>
	<center>
	<table>
	<input type='hidden' name='id_articulo' id='id_articulo'>
	<tr><td></td><td colspan=3>
	<input type='button' id='genera_codigo' name='genera_codigo' onClick='generar_codigo();' value='Generar Correlativo...' style='display:none;'>
	</td></tr>
	<tr><td style='text-align: right;'>C&oacute;digo Int.:</td><td colspan=3>
  <input type='text' name='prod_codigo' id='prod_codigo' 
  onKeyUp='
  if(event.which==13) buscar_codigo_prod();
  '>
  <img src='iconos/zoom_in.png' style='cursor: pointer;' 
  onClick='
  buscar_articulos("prod_codigo", function() { buscar_codigo_prod(); } );
  '>
	<img src='imagenes/ajax-loader1.gif' style='display: none;'
  id='cargando_codigo'>
  <input type='checkbox' id='prod_activado' name='prod_activado' checked> Activado
  
  </td></tr>
	<tr><td style='text-align: right;'>Nombre:</td><td colspan=3><input type='text' name='prod_glosa' id='prod_glosa' size=35></td></tr>
	
	<tr style='display:none;'><td style='text-align: right;'>Nombre:</td><td colspan=3><input type='text' name='prod_nombre' id='prod_nombre' size=35></td></tr>
	
	<tr><td style='text-align: right;'>Item Presupuestario:</td>
	<td colspan=3>
	<select id='prod_item' name='prod_item' style='width:200px;' onChange='
	if(this.value==0)
	  { $("item_icono").src="iconos/cross.png"; } 
	else 	
    { $("item_icono").src="iconos/tick.png"; }
	'>
	<option value=0 selected>(No Asignado...)</option>
	<?php echo $itemshtml; ?>
	</select>
	<img id='item_icono' src='iconos/cross.png'>
	</td></tr>
	<tr><td style='text-align: right;'>Clasificaci&oacute;n:</td>
	<td colspan=3>
	<select id='prod_clasif' name='prod_clasif'>
	<option value=0 selected>(No Aplicable...)</option>
	<?php echo $clasificahtml; ?>
	</select>
	</td></tr>
	<tr><td style='text-align: right;'>Forma:</td><td colspan=3>
	<select id='prod_forma' name='prod_forma'>
	<option value=0 selected>(No Aplicable...)</option>
	<?php echo $formahtml; ?>
	</select>
	</td></tr>
	<tr><td style='text-align: right;'>Vence:</td><td>
  <select name='prod_vence' id='prod_vence'>
	<option value=0>No</option>
	<option value=1 SELECTED>Si</option>
	</select></td><td style='text-align: right;display:none;'>Auge:</td><td style='display:none;'>
	<select name='prod_auge' id='prod_auge'>
	<option value=0 SELECTED>No</option>
	<option value=1>Si</option>
	</select></td></tr>
	<tr><td style='text-align: right;'>Controlado:</td><td colspan=3>
  <select name='prod_control' id='prod_control'>
	<option value=0 SELECTED>No</option>
	<?php echo $controlhtml; ?>
  </select></td></tr>
	<tr><td style='text-align: right;'>Prioridad:</td><td colspan=3>
  <select name='prod_prioridad' id='prod_prioridad'>
	<?php echo $prioridadhtml; ?>
  </select></td></tr>
  <tr style='display:none;'>
  <td style='text-align:right;'>
  Ultimo Valor:
  </td>
  <td colspan=3>
  <input type='text' id='art_valor' name='art_valor' value='$0.-' 
  style='text-align:right;' size=15
  disabled>
  </td>
  </tr>
  <tr>
  <td style='text-align:center;' colspan=4>
	Variaci&oacute;n Precio:
  </td>
  </tr>
  <tr>
  <td colspan=4 id='variacion' style='width:350px;height:60px;background-color:#ffffff;text-align:center;'>
  
  </td>
  </tr>
	</table>
	<br>
	<center>
	
	<div id='stock_criticos' style='display: none;'>
	<div class='sub-content' style='font-weight: bold;'>
  <img src='iconos/error.png'> Punto de Pedido/Cr&iacute;tico
  </div>
  <div class='sub-content2' id='contenido_stock'>
  
  </div>
	</div>
	
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
	
  </td></tr></table>
  
  <br>
	
	
	</center>
	</div>
</td></tr></table>
</center>	
  <script> $('prod_codigo').focus(); 
  
  	
      
   </script>
