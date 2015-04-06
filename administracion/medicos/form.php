<?php require_once('../../conectar_db.php');

	$esps=desplegar_opciones_sql("
		SELECT esp_id, esp_desc FROM especialidades
		WHERE NOT esp_padre_id=0 ORDER BY esp_desc	
	", NULL, '', '');

?>
	<script>
	
	
	
	
	cargar_listado = function() {
	
		var myAjax = new Ajax.Updater(
			'listado', 
			'administracion/medicos/listar_medicos.php', 
			{
				method: 'get', 
				parameters: 'buscar='+serializar('medico_filtro'),
				evalScripts: true
				
			}
			
			);
	
	}
	
	seleccionar_medico = function(iddoc) {
	
		var myAjax = new Ajax.Request(
			'administracion/medicos/bajar_medico.php', 
			{
				method: 'get', 
				parameters: 'buscar='+(iddoc*1),
				onComplete: function(pedido_datos) {
				
				  try {
				
					datos=eval(pedido_datos.responseText);
				
					med_id_text = document.getElementById('medico_id');
					med_rut_text = document.getElementById('medico_rut');
					med_paterno_text = document.getElementById('medico_paterno');
					med_materno_text = document.getElementById('medico_materno');
					med_nombre_text = document.getElementById('medico_nombre');
					med_fono_text = document.getElementById('medico_fono');
					med_mail_text = document.getElementById('medico_mail');
					
					med_id_text.value=(datos[0]*1);
					med_rut_text.value=datos[1].unescapeHTML();
					med_paterno_text.value=datos[2].unescapeHTML();
					med_materno_text.value=datos[3].unescapeHTML();
					med_nombre_text.value=datos[4].unescapeHTML();
					med_fono_text.value=datos[5].unescapeHTML();
					med_mail_text.value=datos[6].unescapeHTML();
					
					med_rut_text.disabled=true;
					med_paterno_text.disabled=true;
					med_materno_text.disabled=true;
					med_nombre_text.disabled=true;
					med_fono_text.disabled=true;
					med_mail_text.disabled=true;
					
					$('editar_boton').style.display='';
					$('borrar_boton').style.display='';
					$('definir_boton').style.display='';
					$('ausencias_boton').style.display='';
					$('guardar_boton').style.display='none';
		      
		      } catch(err) {
            alert(err);
          }
		      
				}
				
			}
			
			);
	
	}
	
	agregar_medico = function () {
	
		med_id_text = document.getElementById('medico_id');
		med_rut_text = document.getElementById('medico_rut');
		med_paterno_text = document.getElementById('medico_paterno');
		med_materno_text = document.getElementById('medico_materno');
		med_nombre_text = document.getElementById('medico_nombre');
		med_fono_text = document.getElementById('medico_fono');
		med_mail_text = document.getElementById('medico_mail');
					
		med_rut_text.disabled=false;
		med_paterno_text.disabled=false;
		med_materno_text.disabled=false;
		med_nombre_text.disabled=false;
		med_fono_text.disabled=false;
		med_mail_text.disabled=false;
					
		med_id_text.value='';
		med_rut_text.value='';
		med_paterno_text.value='';
		med_materno_text.value='';
		med_nombre_text.value='';
		med_fono_text.value='';
		med_mail_text.value='';
		
				
		$('guardar_texto').innerHTML = 'Guardar Profesional Nuevo...';
		$('guardar_boton').style.display='';
		$('borrar_boton').style.display='none';
		$('editar_boton').style.display='none';
		$('definir_boton').style.display='none';
		$('ausencias_boton').style.display='none';
								
		med_rut_text.focus();
		
	}
	
	editar_medico = function() {
	
		med_id_text = document.getElementById('medico_id');
		med_rut_text = document.getElementById('medico_rut');
		med_paterno_text = document.getElementById('medico_paterno');
		med_materno_text = document.getElementById('medico_materno');
		med_nombre_text = document.getElementById('medico_nombre');
		med_fono_text = document.getElementById('medico_fono');
		med_mail_text = document.getElementById('medico_mail');
					
		med_rut_text.disabled=false;
		med_paterno_text.disabled=false;
		med_materno_text.disabled=false;
		med_nombre_text.disabled=false;
		med_fono_text.disabled=false;
		med_mail_text.disabled=false;
					
		$('guardar_texto').innerHTML = 'Guardar Cambios a Profesional...';
		$('guardar_boton').style.display='';
		
		med_nombre_text.focus();
		med_nombre_text.select();
				
	
	}
	
	agregar_ausencias = function(doc_id) {
	
	 var l=(screen.width/2)-200;
	 var t=(screen.height/2)-200;
  
    ausencias = window.open('administracion/medicos/definir_ausencias.php?doc_id='+doc_id,
		'ausencias', 'left='+l+',top='+t+',width=750,height=400,status=0,scrollbars=1');
			
		ausencias.focus();

  
  }

	deshacer_bloqueos = function() {

         var l=(screen.width/2)-290;
         var t=(screen.height/2)-200;

    ausencias = window.open('administracion/medicos/deshacer_bloqueos.php',
                'ausencias', 'left='+l+',top='+t+',width=580,height=400,status=0,scrollbars=1');

                ausencias.focus();


  }
	
	borrar_medico = function() {
		
		confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar este Profesional? - No hay opciones para deshacer.'.unescapeHTML());
		
	}
	
	definir_medico = function() {
		
		if($('esp_id').value==''){
			alert('Debe seleccionar la especialidad.');
			return;
		}
  
    especialidades = window.open('administracion/medicos/listar_especialidades.php?doc_id='+med_id_text.value,
		'especialidades', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');
			
		especialidades.focus();

  
  }
  
  crear_cupos=function() {
    
    /*if($('esp_id').value==''){
			alert('Debe seleccionar la especialidad.');
			return;
		}
    
    esp_id = $('esp_id').value;*/
    
    l=(screen.availWidth/2)-350;
    t=(screen.availHeight/2)-225;
        
    win = window.open('administracion/medicos/listar_especialidades.php?doc_id='+med_id_text.value, 
                    '_asigna_hora',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=700, height=450, scrollbars=1');
                    
    win.focus();
    window.close();

	}
	
	verifica_tabla = function() {
		
		med_id_text = document.getElementById('medico_id');
		med_rut_text = document.getElementById('medico_rut');
		med_paterno_text = document.getElementById('medico_paterno');
		med_materno_text = document.getElementById('medico_materno');
		med_nombre_text = document.getElementById('medico_nombre');
		med_fono_text = document.getElementById('medico_fono');
		med_mail_text = document.getElementById('medico_mail');
		
			if(trim(med_rut_text.value)=='') {
				alert('El campo Rut est&aacute; vac&iacute;o.'.unescapeHTML());
				med_rut_text.select();
				return;
			}
			
			if(trim(med_nombre_text.value)=='') {
				alert('El campo Nombre est&aacute; vac&iacute;o.'.unescapeHTML());
				med_nombre_text.select();
				return;
			}

			if(trim(med_paterno_text.value)=='') {
				alert('El campo Paterno est&aacute; vac&iacute;o.'.unescapeHTML());
				med_nombre_text.select();
				return;
			}

 			if(trim(med_materno_text.value)=='') {
				alert('El campo Materno est&aacute; vac&iacute;o.'.unescapeHTML());
				med_nombre_text.select();
				return;
			}
			
			var myAjax = new Ajax.Request(
			'administracion/medicos/sql.php', 
			{
				method: 'get', 
				parameters: serializar_objetos('registro'),
				onComplete: function(pedido_datos) {
				
				  try {
				
					if(pedido_datos.responseText=='1') {
					
						med_id_text = document.getElementById('medico_id');
		
						if(med_id_text.value='') {
							alert('Ingreso de Profesional realizado exitosamente.'.unescapeHTML());
						} else {
							alert('Edici&oacute;n de Profesional realizado exitosamente.'.unescapeHTML());
						}
						
						med_rut_text = document.getElementById('medico_rut');
						med_paterno_text = document.getElementById('medico_paterno');
						med_materno_text = document.getElementById('medico_materno');
						med_nombre_text = document.getElementById('medico_nombre');
						med_fono_text = document.getElementById('medico_fono');
						med_mail_text = document.getElementById('medico_mail');
		
						med_rut_text.disabled=true;
						med_paterno_text.disabled=true;
						med_materno_text.disabled=true;
						med_nombre_text.disabled=true;
						med_fono_text.disabled=true;
						med_mail_text.disabled=true;
					
						$('editar_boton').style.display='';
						$('borrar_boton').style.display='';
						$('definir_boton').style.display='';
						$('ausencias_boton').style.display='';
					   $('guardar_boton').style.display='none';
						
						cargar_listado();
						
					} else {
						if(pedido_datos.responseText=='0')
						{
							alert('El Doctor ya se encuentra Ingresado'.unescapeHTML());
							return;
						}
						else
						{
							alert('Error: \r\n'+pedido_datos.responseText.unescapeHTML());

						}
					
					}
					
					} catch(err) {
					
            alert(err);
          
          }
					
				}
			}		
			);	
		
		}
		
	
	
        
        
        buscar_cupos=function()
        {
            top=Math.round(screen.height/2)-275;
            left=Math.round(screen.width/2)-400;
            new_win = window.open('prestaciones/ingreso_nominas/form_buscar.php?mostrar_boqueo=1',
            'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
            'menubar=no, scrollbars=yes, resizable=no, width=800, height=550, '+
            'top='+top+', left='+left);
            new_win.focus();
        }
	
        
	</script>
	
	<center>
	
	<table><tr><td valign='top'>
	
	<div class='sub-content'>
	
	<div class='sub-content'><img src='iconos/table.png'> <b>B&uacute;squeda por Listado</b></div>
	
	<div class='sub-content'><table>
	<tr><td style='text-align: right;'><b>Filtrar:</b></td><td>
	<input type='text' name='medico_filtro' id='medico_filtro' size=30 onKeyUp='cargar_listado();'>
	</td></tr>
	</table></div>
	
	<div class='sub-content3' id='listado'>
		
	</div>
	
	</div>
	
	</td><td valign='top'>
	
	<center>
	<div class='boton' id='agregar_boton'>
	<table><tr><td>
	<img src='iconos/calendar_delete.png'>
	</td><td>
	<a href='#' onClick='agregar_ausencias(0);'>Agregar Ausencias Globales...</a>
	</td></tr></table>
	</div>
        <div class='boton' id='bloqueo_boton'>
            <table>
                <tr>
                    <td>
                        <img src='iconos/calendar_delete.png'>
                    </td>
                    <td>
                        <a href='#' onClick='buscar_cupos();'>Bloqueos de Agenda...</a>
                    </td>
                </tr>
            </table>
        </div>
	<div class='boton' id='deshacer_boton'>
        <table><tr><td>
        <img src='iconos/calendar_delete.png'>
        </td>
        <td>
        <a href='#' onClick='deshacer_bloqueos();'>Recuperar Bloqueos de Agenda...</a>
        </td></tr></table>
        </div>
	</center>
	
	<div class='sub-content'>
	
	<center>
	<div class='boton' id='agregar_boton'>
	<table><tr><td>
	<img src='iconos/pill_add.png'>
	</td><td>
	<a href='#' onClick='agregar_medico();'>Agregar Profesional...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='editar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/pill_go.png'>
	</td><td>
	<a href='#' onClick='editar_medico();'>Editar Profesional...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='borrar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/pill_delete.png'>
	</td><td>
	<a href='#' onClick='borrar_medico();'>Eliminar Profesional...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='definir_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/calendar.png'>
	</td><td>
	<a href='#' onClick='crear_cupos();'>Definir Horario de Atenci&oacute;n...</a>
	</td></tr></table>
	</div>
	
	<div class='boton' id='ausencias_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/calendar_delete.png'>
	</td><td>
	<a href='#' onClick='agregar_ausencias($("medico_id").value);'>Definir Ausencias M&eacute;dicas...</a>
	</td></tr></table>
	</div>
	
	
	</center>
	
	</div>
	
	<div class='sub-content'>
	
	<div class='sub-content'><img src='iconos/lorry.png'> <b>Datos del Profesional</b></div>
	
	<div class='sub-content3' id='registro'>
	<input type='hidden' name='medico_id' id='medico_id'>
	<table style='padding: 5px;'>
	<tr><td style='text-align: right;'>RUT:</td>		
	<td><input type='text' name='medico_rut' id='medico_rut' size=20 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Paterno:</td>		
	<td><input type='text' name='medico_paterno' id='medico_paterno' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Materno:</td>		
	<td><input type='text' name='medico_materno' id='medico_materno' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Nombre:</td>		
	<td><input type='text' name='medico_nombre' id='medico_nombre' size=25 DISABLED></td></tr>
	<tr><td style='text-align: right;'>Tel&eacute;fono:</td>		
	<td><input type='text' name='medico_fono' id='medico_fono' size=20 DISABLED></td></tr>
	<tr><td style='text-align: right;'>e-mail:</td>		
	<td><input type='text' name='medico_mail' id='medico_mail' size=20 DISABLED></td></tr>
	
<tr style='display:none;'><td style='text-align: right;'>Especialidad:</td>		
	<td><select id='esp_id' name='esp_id' style='width:200px;'><option value=''>Seleccionar</option>
	<?php echo $esps; ?>
	</select></td></tr>
	</table>
	
	<center>
	<div class='boton' id='guardar_boton' style='display: none;'>
	<table><tr><td>
	<img src='iconos/lorry_go.png'>
	</td><td>
	<a href='#' onClick='verifica_tabla();'><span id='guardar_texto'>Guardar cambios al Profesional...</span></a>
	</td></tr></table>
	</div>
	</center>
	
	</div>
	
	</div>
	
	</td></tr></table>
	
	</center>
        <script>
            cargar_listado();
        </script>
	
