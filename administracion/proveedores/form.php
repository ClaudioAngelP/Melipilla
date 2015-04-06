
	<script>
	
	cargar_listado = function() {
	
		var myAjax = new Ajax.Updater(
			'listado', 
			'administracion/proveedores/listar_proveedores.php', 
			{
				method: 'get', 
				parameters: 'buscar='+serializar('proveedor_filtro'),
				evalScripts: true
				
			}
			
			);
	
	}

    limpia_datos = function()  {

      prov_id_text.value='';
		prov_rut_text.value='';
		prov_nombre_text.value='';
		prov_direccion_text.value='';
		prov_ciudad_text.value='';
		prov_fono_text.value='';
		prov_fax_text.value='';
		prov_mail_text.value='';

      }


      confirmar_proveedor = function() {

      	var myAjax = new Ajax.Request(
		'administracion/proveedores/confirmar_proveedor.php',
		{
		method: 'get',
		parameters: 'buscar='+(prov_id_text.value*1),
        onComplete: function(respuesta) {
        id_doc = respuesta.responseText.evalJSON(true);

        if(id_doc==0) {
            borrar_proveedor();

          } else {

            alert('El proveedor no puede puede ser eliminado,consulte al administrador'.unescapeHTML());

		}
       }
       }
      );

     }

	seleccionar_proveedor = function(idproveedor) {

		var myAjax = new Ajax.Request(
			'administracion/proveedores/bajar_proveedor.php',
			{
				method: 'get',
				parameters: 'buscar='+(idproveedor*1),
				onComplete: function(pedido_datos) {

					datos=eval(pedido_datos.responseText);

					prov_id_text = document.getElementById('proveedor_id');
					prov_rut_text = document.getElementById('proveedor_rut');
					prov_nombre_text = document.getElementById('proveedor_nombre');
					prov_direccion_text = document.getElementById('proveedor_direccion');
					prov_ciudad_text = document.getElementById('proveedor_ciudad');
					prov_fono_text = document.getElementById('proveedor_fono');
					prov_fax_text = document.getElementById('proveedor_fax');
					prov_mail_text = document.getElementById('proveedor_mail');

					prov_id_text.value=(datos[0]*1);
					prov_rut_text.value=datos[1].unescapeHTML();
					prov_nombre_text.value=datos[2].unescapeHTML();
					prov_direccion_text.value=datos[3].unescapeHTML();
					prov_ciudad_text.value=datos[4].unescapeHTML();
					prov_fono_text.value=datos[5].unescapeHTML();
					prov_fax_text.value=datos[6].unescapeHTML();
					prov_mail_text.value=datos[7].unescapeHTML();
					
					prov_rut_text.disabled=true;
					prov_nombre_text.disabled=true;
					prov_direccion_text.disabled=true;
					prov_ciudad_text.disabled=true;
					prov_fono_text.disabled=true;
					prov_fax_text.disabled=true;
					prov_mail_text.disabled=true;

					$('editar_boton').style.display='';
					$('borrar_boton').style.display='';
					$('guardar_boton').style.display='none';
		
				}
				
			}
			
			);
	
	}

	agregar_proveedor = function () {

		prov_id_text = document.getElementById('proveedor_id');
		prov_rut_text = document.getElementById('proveedor_rut');
		prov_nombre_text = document.getElementById('proveedor_nombre');
		prov_direccion_text = document.getElementById('proveedor_direccion');
		prov_ciudad_text = document.getElementById('proveedor_ciudad');
		prov_fono_text = document.getElementById('proveedor_fono');
		prov_fax_text = document.getElementById('proveedor_fax');
		prov_mail_text = document.getElementById('proveedor_mail');
					
		prov_rut_text.disabled=false;
		prov_nombre_text.disabled=false;
		prov_direccion_text.disabled=false;
		prov_ciudad_text.disabled=false;
		prov_fono_text.disabled=false;
		prov_fax_text.disabled=false;
		prov_mail_text.disabled=false;

		limpia_datos();

				
		$('guardar_texto').innerHTML = 'Guardar Proveedor Nuevo...';
		$('guardar_boton').style.display='';
		$('borrar_boton').style.display='none';
		$('editar_boton').style.display='none';
					
		prov_rut_text.focus();
		
	}

	editar_proveedor = function() {

		prov_id_text = document.getElementById('proveedor_id');
		prov_rut_text = document.getElementById('proveedor_rut');
		prov_nombre_text = document.getElementById('proveedor_nombre');
		prov_direccion_text = document.getElementById('proveedor_direccion');
		prov_ciudad_text = document.getElementById('proveedor_ciudad');
		prov_fono_text = document.getElementById('proveedor_fono');
		prov_fax_text = document.getElementById('proveedor_fax');
		prov_mail_text = document.getElementById('proveedor_mail');
					
		prov_rut_text.disabled=false;
		prov_nombre_text.disabled=false;
		prov_direccion_text.disabled=false;
		prov_ciudad_text.disabled=false;
		prov_fono_text.disabled=false;
		prov_fax_text.disabled=false;
		prov_mail_text.disabled=false;

		$('guardar_texto').innerHTML = 'Guardar Cambios a Proveedor...';
		$('guardar_boton').style.display='';
		
		prov_nombre_text.focus();
		prov_nombre_text.select();
				
	
	}
	
	borrar_proveedor = function() {
		
	 confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar este proveedor? - No hay opciones para deshacer.'.unescapeHTML());

     if(confirma){

       var myAjax2 = new Ajax.Request(
		'administracion/proveedores/elimina_proveedor.php',
		{
			method: 'get',
           parameters: 'buscar='+(prov_id_text.value*1),
				onComplete: function(pedido_datos) {

                 limpia_datos();

                 alert('Proveedor Eliminado de forma correcta'.unescapeHTML());
				 return;


			}

		}
		);
    }
     cargar_listado();


	}

	verifica_tabla = function() {
		
		prov_id_text = document.getElementById('proveedor_id');
		prov_rut_text = document.getElementById('proveedor_rut');
		prov_nombre_text = document.getElementById('proveedor_nombre');
		prov_direccion_text = document.getElementById('proveedor_direccion');
		prov_ciudad_text = document.getElementById('proveedor_ciudad');
		prov_fono_text = document.getElementById('proveedor_fono');
		prov_fax_text = document.getElementById('proveedor_fax');
		prov_mail_text = document.getElementById('proveedor_mail');
		
			if(trim(prov_rut_text.value)=='') {
				alert('El campo Rut est&aacute; vac&iacute;o.'.unescapeHTML());
				prov_rut_text.select();
				return;
			}
			
			if(trim(prov_nombre_text.value)=='') {
				alert('El campo Nombre est&aacute; vac&iacute;o.'.unescapeHTML());
				prov_nombre_text.select();
				return;
			}
			
			if(trim(prov_direccion_text.value)=='') {
				alert('El campo Direcci&oacute;n est&aacute; vac&iacute;o.'.unescapeHTML());
				prov_direccion_text.select();
				return;
			}
			
			if(trim(prov_ciudad_text.value)=='') {
				alert('El campo Ciudad est&aacute; vac&iacute;o.'.unescapeHTML());
				prov_ciudad_text.select();
				return;
			}
			
			/* se solicito que no sea un campo obligatorio
              if(trim(prov_fono_text.value)=='') {
				alert('El campo Tel&eacute;fono est&aacute; vac&iacute;o.'.unescapeHTML());
				prov_fono_text.select();
				return;
			} */
			
			var myAjax = new Ajax.Request(
			'administracion/proveedores/sql.php', 
			{
				method: 'get', 
				parameters: serializar_objetos('registro'),
				onComplete: function(pedido_datos) {
				
					if(pedido_datos.responseText=='1' || pedido_datos.responseText=='3') {

						prov_id_text = document.getElementById('proveedor_id');
		
						if(pedido_datos.responseText=='3') {
							alert('Ingreso de proveedor realizado exitosamente.'.unescapeHTML());
						}
                        if(pedido_datos.responseText=='1') {
							alert('Edici&oacute;n de proveedor realizado exitosamente.'.unescapeHTML());
						}

						prov_rut_text = document.getElementById('proveedor_rut');
						prov_nombre_text = document.getElementById('proveedor_nombre');
						prov_direccion_text = document.getElementById('proveedor_direccion');
						prov_ciudad_text = document.getElementById('proveedor_ciudad');
						prov_fono_text = document.getElementById('proveedor_fono');
						prov_fax_text = document.getElementById('proveedor_fax');
						prov_mail_text = document.getElementById('proveedor_mail');

						prov_rut_text.disabled=true;
						prov_nombre_text.disabled=true;
						prov_direccion_text.disabled=true;
						prov_ciudad_text.disabled=true;
						prov_fono_text.disabled=true;
						prov_fax_text.disabled=true;
						prov_mail_text.disabled=true;

						$('editar_boton').style.display='';
						$('borrar_boton').style.display='';
						$('guardar_boton').style.display='none';

						cargar_listado();

					} else {
                      if(pedido_datos.responseText=='2') {
						alert('Rut de Proveedor Existente');
                      }
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
	<input type='text' name='proveedor_filtro' id='proveedor_filtro' size=30 onKeyUp='cargar_listado();'>
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
	<img src='iconos/lorry_add.png'>
	</td><td>
	<a href='#' onClick='agregar_proveedor();'>Agregar Proveedor...</a>
	</td></tr></table>
	</div>

	<div class='boton' id='editar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/lorry_error.png'>
	</td><td>
	<a href='#' onClick='editar_proveedor();'>Editar Proveedor...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='borrar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/lorry_delete.png'>
	</td><td>
	<a href='#' onClick='confirmar_proveedor();'>Eliminar Proveedor...</a>
	</td></tr></table>
	</div>

	</center>

	</div>

	<div class='sub-content'>

	<div class='sub-content'><img src='iconos/lorry.png'> <b>Datos del Proveedor</b></div>

	<div class='sub-content3' id='registro'>
	<input type='hidden' name='proveedor_id' id='proveedor_id'>
	<table style='padding: 5px;'>
	<tr><td style='text-align: right;'>RUT:</td>
	<td><input type='text' name='proveedor_rut' id='proveedor_rut' size=20 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Nombre:</td>
	<td><input type='text' name='proveedor_nombre' id='proveedor_nombre' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Direcci&oacute;n:</td>
	<td><input type='text' name='proveedor_direccion' id='proveedor_direccion' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Ciudad:</td>		
	<td><input type='text' name='proveedor_ciudad' id='proveedor_ciudad' size=20 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Tel&eacute;fono:</td>		
	<td><input type='text' name='proveedor_fono' id='proveedor_fono' size=20 DISABLED></td></tr>
	<tr><td style='text-align: right;'>F&aacute;x:</td>		
	<td><input type='text' name='proveedor_fax' id='proveedor_fax' size=20 DISABLED></td></tr>
	<tr><td style='text-align: right;'>e-mail:</td>		
	<td><input type='text' name='proveedor_mail' id='proveedor_mail' size=20 DISABLED></td></tr>
	</table>
	
	<center>
	<div class='boton' id='guardar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/lorry_go.png'>
	</td><td>
	<a href='#' onClick='verifica_tabla();'><span id='guardar_texto'>Guardar cambios a Usuario...</span></a>
	</td></tr></table>
	</div>
	</center>
	
	</div>
	
	</div>
	
	</td></tr></table>
	
	</center>
	
