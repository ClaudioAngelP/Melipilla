<?php

  require_once("../../conectar_db.php");

	$sexohtml = desplegar_opciones("sexo", 
	"sex_id, sex_desc",'','true','ORDER BY sex_id'); 
	
    $estcivhtml = desplegar_opciones("estado_civil", 
	"estciv_id, estciv_nombre",'0','true', 'ORDER BY estciv_id');

	$nacionhtml = desplegar_opciones("nacionalidad", 
	"nacion_id, nacion_nombre",'0','true', 'ORDER BY nacion_id');

	$sangrehtml = desplegar_opciones("grupo_sanguineo", 
	"sang_id, sang_desc",'0','true','ORDER BY sang_id'); 
	
	$previsionhtml = desplegar_opciones("prevision", 
	"prev_id, prev_desc",'0','true','ORDER BY prev_id'); 
	
	$institucionhtml = desplegar_opciones("institucion_solicita", 
	"instsol_id, instsol_desc",'','true','ORDER BY instsol_desc');
  
    $comunahtml = desplegar_opciones("comunas", 
	"ciud_id, ciud_desc",'','true','ORDER BY ciud_desc');
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1',
  'bod_id IN ('._cav(10),')',	'ORDER BY bod_glosa'); 

  
?>
		
		<script>
		
    
    
		calcular_edad = function() {
		
			if(trim($('paciente_fecha').value)=='') {
				$('paciente_fecha').style.background='';
				return;
			}
		
			
			if(isDate($('paciente_fecha').value)) {
				$('mostrar_edad').innerHTML='<i>'+calc_edad($('paciente_fecha').value)+'</i>';
				$('paciente_fecha').style.background='inherit';
			} else {
				alert('Fecha de Nacimiento Incorrecta.');
        		$('paciente_fecha').style.background='red';
			}
			
		}
		
		limpiar_ficha_basica = function() {
    
        		$('paciente_id').value=0;
				$('paciente_nombre').value='';
				$('paciente_paterno').value='';
				$('paciente_materno').value='';
				$('paciente_dire').value='';
				$('paciente_comuna').value=-1;
				$('paciente_fecha').value='';
				$('paciente_sexo').value='';
				$('paciente_prevision').value=0;
				$('paciente_cod_prev').value='0';
        		$('paciente_sangre').value=-1;
				
				$('paciente_sector').value='';
				$('paciente_fono').value='';
				$('paciente_nacion').value=0;
				$('paciente_estciv').value=0;
				$('paciente_tramo').value='';
				$('paciente_padre').value='';
				$('paciente_madre').value='';
				
				$('parentezco_div').innerHTML='';
				
				$('paciente_rut').value='';
				$('paciente_rut').style.background='';
				
				deshabilitar_ficha_basica(true);
    
        $('paciente_rut').disabled=false;
        
        $('paciente_nuevo').style.display='none';
        $('paciente_antiguo').style.display='none';
        $('paciente_editar').style.display='none';
        
        $('paciente_rut').focus();
            
    }
		
		deshabilitar_ficha_basica = function(estado) {
            
            $('paciente_nombre').disabled=estado;
						$('paciente_paterno').disabled=estado;
						$('paciente_materno').disabled=estado;
						$('paciente_dire').disabled=estado;
						$('paciente_comuna').disabled=estado;
						$('paciente_fecha').disabled=estado;
						$('paciente_sexo').disabled=estado;
						$('paciente_prevision').disabled=estado;
						$('paciente_sangre').disabled=estado;
						
						$('paciente_sector').disabled=estado;
						$('paciente_fono').disabled=estado;
						$('paciente_nacion').disabled=estado;
						$('paciente_estciv').disabled=estado;
						$('paciente_cod_prev').disabled=estado;
						$('paciente_tramo').disabled=estado;
						$('paciente_padre').disabled=estado;
						$('paciente_madre').disabled=estado;
						
    
    }
		
	buscar_paciente = function() {
    
    $('cargando').style.display='';
   

	 deshabilitar_ficha_basica(true);
      
      params=$('paciente_rut').serialize();
      params+='&'+$('paciente_tipo_id').serialize();
    
      var myAjax = new Ajax.Request(
			'registro.php', 
			{
				method: 'get', 
				parameters: 'tipo=paciente&'+params,
				onComplete: function (pedido_datos) {
				
				  if(pedido_datos.responseText=='') {
				  
            ingresar_paciente();
						
					} else {
					
					  $('titulo_form').innerHTML='Datos del Paciente';
					  $('paciente_nuevo').style.display='none';
				    $('paciente_antiguo').style.display='';
					
          	datosxxx = eval(trim(pedido_datos.responseText)); 
					
					  $('paciente_id').value=datosxxx[0]*1;
						$('paciente_nombre').value=datosxxx[2].unescapeHTML();
						$('paciente_paterno').value=datosxxx[3].unescapeHTML();
						$('paciente_materno').value=datosxxx[4].unescapeHTML();
						$('paciente_dire').value=datosxxx[11].unescapeHTML();
						$('paciente_comuna').value=datosxxx[12];
						$('paciente_fecha').value=datosxxx[5];
						$('paciente_sexo').value=datosxxx[6];
						$('paciente_prevision').value=datosxxx[7];
						$('paciente_tramo').value=datosxxx[18].unescapeHTML();
						$('paciente_sangre').value=datosxxx[10];
						
						$('paciente_sector').value=datosxxx[8].unescapeHTML();
						$('paciente_fono').value=datosxxx[15].unescapeHTML();
						$('paciente_nacion').value=datosxxx[13];
						$('paciente_estciv').value=datosxxx[14];
						$('paciente_padre').value=datosxxx[16].unescapeHTML();
						$('paciente_madre').value=datosxxx[17].unescapeHTML();
						
						calcular_edad();
						
            //actualizar_cod_prev();

            $('paciente_prevision').disabled=true;                                    
                  
						deshabilitar_ficha_basica(true);
						
						listar_parientes(datosxxx[0]*1,0);
						
						listar_patologias();
						
						$('paciente_rut').select();
						$('sigges').show();

												
					}
				
          $('cargando').style.display='none';
          
         
           
    
        }
			}
			
			);
		
		buscar_prevision();
		
    }


	buscar_prevision = function(){
		
			 $('pac_prev').value='Cargando...';
			 
			var myAjax=new Ajax.Request(
			'ingresos/certificar_paciente.php',
			{
				method:'post',parameters:'rut='+encodeURIComponent($('paciente_rut').value),
				onComplete:function(r) {
				
					var d=r.responseText.evalJSON(true);
					 
					//$('cargar_fonasa').hide();
					
					if(d) {
						$('prev_id').value=d['prev_id']*1;
						
						if(d['prev_id']*1!=6)
							$('pac_prev').value=d['prev_desc'].unescapeHTML();
						else
							$('pac_prev').value=d['desc'].unescapeHTML();
	
						$('frec_id').value=d['frec_id']*1;	
							
						if(d['frec_id']*1>0) {
							$('ver_func').show();
						} else {
							$('ver_func').hide();
						}
						
						if(d['prais']!='000' && d['prais']!='') {
							alert('Alerta PRAIS:\n===================================\n\nPACIENTE ES PRAIS, NO DEBE RECAUDAR ESTAS PRESTACIONES.'.unescapeHTML());
							cambiar_pagina('creditos/ingreso/form.php');
						}		
					cargar_prestaciones();
					} else {
					
						$('prev_id').value='';
						$('pac_prev').value='ERROR FONASA';
						$('ver_func').hide();
					}
				}
			}
		);
		
	}
	
	ver_sigges=function() {
		
		 sigges = window.open('ficha_clinica/registro_sigges.php?rut='+encodeURIComponent($('paciente_rut').value),
		 'sigges', 'left='+((screen.width/2)-325)+',top='+((screen.height/2)-200)+',width=650,height=400,status=0,scrollbars=1');
			
		 sigges.focus();
		
	}

    
    ingresar_paciente_nuevo = function() {
    
      ingresar_paciente();
      $('paciente_rut').style.background='inherit';
      $('paciente_rut').value='';
      $('paciente_rut').focus();
    
    }
    
    ingresar_paciente = function() {
    
            if($('paciente_tipo_id').value==2)
              $('paciente_rut').value='*';
    
    				$('titulo_form').innerHTML='Ingreso de Paciente Nuevo';
				    $('paciente_nuevo').style.display='';
				    $('paciente_antiguo').style.display='none';
					
						$('paciente_fecha').style.background='';
				    $('mostrar_edad').innerHTML='';
				    
						$('paciente_id').value=0;
						$('paciente_nombre').value='';
						$('paciente_paterno').value='';
						$('paciente_materno').value='';
						$('paciente_dire').value='';
						$('paciente_comuna').value=-1;
						$('paciente_fecha').value='';
						$('paciente_sexo').value='';
						$('paciente_prevision').value=0;
						$('paciente_cod_prev').value='0';
            			$('paciente_sangre').value=-1;
						
						$('paciente_sector').value='';
						$('paciente_fono').value='';
						$('paciente_nacion').value=0;
						$('paciente_estciv').value=0;
						$('paciente_tramo').value='';
						$('paciente_padre').value='';
						$('paciente_madre').value='';
												
						$('parentezco_div').innerHTML='';
											
						deshabilitar_ficha_basica(false);
						
						$('paciente_nombre').focus();

    }
    
    actualizar_prevision = function() {
    
          $('ajax_fonasa').style.display='';
             
          rut_partes = $('paciente_rut').value.split('-');
						
			    var myAjax3 = new Ajax.Request(
			    'fonasa_conector.php', 
			    {
				    method: 'get', 
				    parameters: 'rut='+rut_partes[0]+'&rutv='+rut_partes[1],
				    onComplete: function(respuesta) {
                  
              if(!respuesta.responseXML) {

                alert(respuesta.responseText.unescapeHTML());
                $('ajax_fonasa').style.display = 'none';

                deshabilitar_ficha_basica(false);

						    return;
						    
              }
                  
              $('ajax_fonasa').style.display = 'none';
                  
              deshabilitar_ficha_basica(false);
						      
              xmldoc = respuesta.responseXML;
				
              $('paciente_prevision').value = trim(xmldoc.getElementsByTagName('COD_CYBL').item(0).firstChild.data)*1;
              $('paciente_tramo').value = trim(xmldoc.getElementsByTagName('TRAMO').item(0).firstChild.data);
                  
              $('paciente_prevision').disabled=true;                                    
              $('paciente_tramo').disabled=true;  

              actualizar_cod_prev();
                   
            }  }  );
		
    
    }
  


    listar_parientes = function(id_paciente, modo) {
    
      if(modo==0) {
      
      var myAjax = new Ajax.Updater(
			'parentezco_div',
      'ficha_clinica/parientes.php', 
			{
				method: 'get', 
				parameters: 'accion=listar_parientes_estatico&paciente='+id_paciente,
				evalScripts: true
			}
			
			);
			
			} else {
			
      var myAjax = new Ajax.Updater(
			'parentezco_div',
      'ficha_clinica/parientes.php', 
			{
				method: 'get', 
				parameters: 'accion=listar_parientes&paciente='+id_paciente
			}
			
			);
			
      }
    
    }
    
    abrir_pariente = function (rut_pariente) {
    
      $('paciente_editar').style.display='none';
			$("paciente_rut").disabled=false; 
      $("paciente_rut").value=rut_pariente;
      verificar_rut();			
    
    
    }
    
    agregar_pariente = function () {
      $('pariente_nuevo_2').style.display='none';
      $('pariente_nuevo').style.display='';
      $('pariente_rut').focus();
      
    }
    
    cancelar_agregar_pariente = function () {
      $('pariente_nuevo_2').style.display='';
      $('pariente_nuevo').style.display='none';
      $('pariente_rut').value='';
      $('pariente_nombre').innerHTML='&nbsp;';
      $('pariente_relacion').value=0;
      
    }
    
    ver_pariente = function() {
    
      rut_pariente=$('pariente_rut').value;
    
      if(trim(rut_pariente)=='') {
        $('pariente_id').value='';
        return;
      }
    
      var myAjax = new Ajax.Request(
			'ficha_clinica/parientes.php', 
			{
				method: 'get', 
				parameters: 'accion=ver_pariente&pariente='+rut_pariente,
				onComplete: function (registro) {
				  
          datos = eval(registro.responseText);
				  
          if(datos) {
            $('pariente_id').value      =datos[0];
            $('pariente_nombre').innerHTML  =datos[1];
          } else {
            $('pariente_id').value      ='';
            $('pariente_nombre').innerHTML  ='&nbsp;';
          
          }
          
        }
			}
			
			);
    
    }
    
    guardar_pariente = function () {
      
      if($('pariente_id').value=='') {
        alert('No se ha seleccionado un pariente v&aacute;lido.'.unescapeHTML());
        return;
      }
      
      id_paciente=$('paciente_id').value;
      id_relacion=$('pariente_relacion').value;
      id_pariente=$('pariente_id').value;
      
      var myAjax = new Ajax.Request(
			'ficha_clinica/parientes.php', 
			{
				method: 'get', 
				parameters: 'accion=agregar_pariente&paciente='+id_paciente+'&relacion='+id_relacion+'&pariente='+id_pariente,
				onComplete: function () {
          listar_parientes($('paciente_id').value, 1);
        }
			}
			
			);
    
    }
    
    quitar_pariente = function(id_paciente, id_relacion, id_pariente) {
    
      alert(id_paciente+' -> '+id_relacion+' -> '+id_pariente);
    
      var myAjax = new Ajax.Request(
			'ficha_clinica/parientes.php', 
			{
				method: 'get', 
				parameters: 'accion=eliminar_pariente&paciente='+id_paciente+'&relacion='+id_relacion+'&pariente='+id_pariente,
				onComplete: function (respuesta) {
				  listar_parientes($('paciente_id').value, 1);
        }
			}
			
			);
    
    }
		
		verificar_rut = function() {
    
      var texto = $('paciente_rut').value;
      
      if(texto.charAt(0)=='R') {
        $('paciente_tipo_id').value=0;
        $('paciente_rut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='P') {
        $('paciente_tipo_id').value=1;
        $('paciente_rut').value=texto.substring(1,texto.length);
      } else if(texto.charAt(0)=='I') {
        $('paciente_tipo_id').value=2;
        $('paciente_rut').value=texto.substring(1,texto.length);
      }
      
      if($('paciente_tipo_id').value==0) {
      
        if(comprobar_rut($('paciente_rut').value)) {
      
          $('paciente_rut').style.background='inherit';
          buscar_paciente();
      
        } else {
      
          $('paciente_rut').style.background='red';
      
        }
        
      } else if($('paciente_tipo_id').value>0) {
      
          $('paciente_rut').style.background='yellowgreen';
          buscar_paciente();
          
      }
      
    }
    
    editar_paciente = function () {
    
      $('titulo_form').innerHTML='Modificar Datos del Paciente';
			$('paciente_nuevo').style.display='none';
			$('paciente_antiguo').style.display='none';
			$('paciente_editar').style.display='';
					
			deshabilitar_ficha_basica(false);
						
			$('paciente_nombre').select();
			
			$('paciente_rut').disabled=true;
			
			listar_parientes($('paciente_id').value*1,1);
						
    
    }
    
    cancelar_edicion = function () {
    
      $('paciente_editar').style.display='none';
			$("paciente_rut").disabled=false; 
      verificar_rut();			
    
    }
		
		verifica_tabla = function() {
		
		  if($('paciente_tipo_id').value==0) {
  			if(trim($('paciente_rut').value)=='' || $('paciente_rut').style.background=='red') {
  				alert('RUT del Paciente incorrecto.'.unescapeHTML());
  				return;
  				}
  			}
			
			
			if(trim($('paciente_nombre').value)=='') {
				alert('Nombre del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_paterno').value)=='') {
				alert('Paterno del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
			
			if(trim($('paciente_materno').value)=='') {
				alert('Materno del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
			
			/*
        if(trim($('paciente_fecha').value)=='') {
				alert('Fecha de Nacimiento del Paciente est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			   }
      */
			
			$('paciente_rut').disabled=false;
		  $('paciente_prevision').disabled=false;
		  $('paciente_tramo').disabled=false;
      $('paciente_editar').style.display='none';
			
			var myAjax = new Ajax.Request(
			'ingresos/ingreso_paciente/sql.php', 
			{
				method: 'get', 
				parameters: $('paciente').serialize(),
				onComplete: function (pedido_datos) {
				
				  try {
            _datos = pedido_datos.responseText.evalJSON(true);
          } catch (err) {
            alert(err);
            return;
          }
          
          if(_datos[0]) {
					
						if($('paciente_id').value==0) {
              
              alert('Ficha B&aacute;sica de Paciente ingresada exitosamente.'.unescapeHTML());
              
              $('titulo_form').innerHTML='Datos del Paciente';
						  $('paciente_nuevo').style.display='none';
			        $('paciente_antiguo').style.display='';
			        
			        deshabilitar_ficha_basica(true);
						
						  $('paciente_id').value=_datos[1];
              
              if($('paciente_tipo_id').value==2) 
                $('paciente_rut').value=_datos[1];
                  
              $('paciente_rut').select();
            
            } else {
						  
              alert('Ficha B&aacute;sica de Paciente actualizada exitosamente.'.unescapeHTML());
						  
              $('titulo_form').innerHTML='Datos del Paciente';
						  $('paciente_nuevo').style.display='none';
			        $('paciente_antiguo').style.display='';
			        
			        deshabilitar_ficha_basica(true);
											
						  $('paciente_rut').select();
						  
						  listar_parientes(datosxxx[0]*1,0);
						
						  
			      }
			
            
					} else {
					
						alert('ERROR:\n'+_datos[1].unescapeHTML());
						
					}
				}
			}
			
			);
		
		}
		
		
    
    actualizar_cod_prev = function() {
    
      $("paciente_cod_prev").disabled=false;
      $("paciente_cod_prev").value=$("paciente_prevision").value;
      $("paciente_cod_prev").disabled=true;
      
    }
    
  
 
		$('paciente_rut').focus();
		
		busqueda_pacientes = function(objetivo, callback_func) {

      top=Math.round(screen.height/2)-150;
      left=Math.round(screen.width/2)-250;
  
      new_win =
      window.open('buscadores.php?tipo=pacientes', 'win_funcionarios',
        'toolbar=no, location=no, directories=no, status=no, '+
        'menubar=no, scrollbars=yes, resizable=no, width=650, height=400, '+
        'top='+top+', left='+left);
  
      new_win.objetivo_cod = objetivo;
      new_win.onCloseFunc = callback_func;
  
      new_win.focus();

    }
    
	
		
</script>
		
		<center>
		
		<form name='paciente' id='paciente'>
		
    <table width=670>
		<tr><td>
		
		<div class='sub-content'> 
		<table style="width:100%;"><tr><td>
    Ubicaci&oacute;n:<input type='hidden' id='prev_id' name='prev_id' value='' />
    </td><td> 
    <select name='bodega_id' id='bodega_id' style='font-size:16px; color:red; background-color:white; border:2px solid black;'>
    <?php echo $bodegashtml; ?>
    </select>
    </td>
    <td style="text-align:right;width:400px;">Acciones:
    <input type="button" value="Ingresar Paciente..."
    onClick="ingresar_paciente_nuevo();">
    </td></tr>
    </table>
    </div>
		
		<div class='sub-content'>
		
		<div class='sub-content'><img src='iconos/user_red.png'> <b><span id='titulo_form'>B&uacute;squeda/Ingreso de Pacientes</span></b>

		<input type='button' id='sigges' name='sigges' value='-- SIGGES --' style='display:none;' onClick='ver_sigges();' /> 
		</div>
		
		<div class='sub-content'>
<table>
<tr style='height:22px; font-weight: bold;'>
<td style='text-align:center;' colspan=2>

<center>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td style='font-weight: bold;'>
<select id="paciente_tipo_id" name="paciente_tipo_id"
style="font-size:10px;" >
<option value=0 SELECTED>R.U.T.</option>
<option value=3>Nro. Ficha</option>
<option value=1>ID SIDRA</option>
<option value=2>Cod. Interno</option>
</select>
</td>
<td>&nbsp;
<img src='iconos/zoom_in.png' id='buscar_paciente'
onClick='
busqueda_pacientes("paciente_rut", function() { verificar_rut(); });
'
onKeyUp="fix_bar(this);"
alt='Buscar Paciente...'
title='Buscar Paciente...'>
</td>
</tr>
</table></center>
</center>

</td>
<td style='text-align:center;'>Nombre(s)</td>
<td style='text-align:center;'>Apellido Paterno</td>
<td style='text-align:center;'>Apellido Materno</td>
<td style='text-align:center;'>Previsi&oacute;n</td>
</tr>
<tr>
<td width=100>
<input type='text' id='paciente_rut' name='paciente_rut' size=11
style='text-align: center;font-size:13px;' onKeyUp='
if(event.which==13) { this.value=this.value.toUpperCase();
verificar_rut(); }
' maxlength=11>
</td>
<td>
<img src='imagenes/ajax-loader1.gif' id='cargando' style='display: none;'>
</td>
<td><input type='text' id='paciente_nombre' name='paciente_nombre' size='22' onKeyUp='
if(event.which==8 && this.value.length==0) $("paciente_rut").focus();
' maxlength=100></td>
<td><input type='text' id='paciente_paterno' name='paciente_paterno' size='22'
onKeyUp='
if(event.which==8 && this.value.length==0) $("paciente_nombre").focus();
' maxlength=50></td>
<td><input type='text' id='paciente_materno' name='paciente_materno' size='22'
onKeyUp='
if(event.which==8 && this.value.length==0) $("paciente_paterno").focus();
' maxlength=50></td>
<td><input type='text' id='pac_prev' name='pac_prev' size='22'
onKeyUp='
if(event.which==8 && this.value.length==0) $("paciente_paterno").focus();
' maxlength=50 disabled></td>
</tr>
</table>
    
    </div>
		
		<div class='sub-content' id='ajax_fonasa' style='display: none;'>
		
		<center><table><tr><td>
    <img src='imagenes/ajax-loader3.gif'></td><td>
		<b>Conectando con Fonasa...</b><br></td></tr></table>
		</center>
		</div>	
		
		
		<table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_ficha' style='cursor: default;' 
     >
      <img src='iconos/report_user.png'>
      Ficha B&aacute;sica</div>
		  </td><td>
		 
      </td></tr>
      </table>
		<div class='tabbed_content' id='tab_ficha_content'>
		
<center>
<input type='hidden' id='paciente_id' name='paciente_id' value=0>
<table width=100%>
<tr style='text-align: center;'>
<td colspan=2>Direcci&oacute;n:</td>
<td>Sector:</td>
<td>Tel&eacute;fono:</td>
</tr>
<tr style='text-align: center;'>
<td colspan=2>
<input type='text' name='paciente_dire' id='paciente_dire' size=45>
</td>
<td>
<input type='text' name='paciente_sector' id='paciente_sector' size=24
maxlength=80>
</td>
<td>
<input type='text' name='paciente_fono' id='paciente_fono' size=15
maxlength=60>
</td>
</tr>
<tr style='text-align: center;'>
<td>Comuna:</td>
<td>Nacionalidad:</td>
<td>Fecha de Nacimiento:</td>
<td>Edad:</td>
</tr>
<tr style='text-align: center;'>
<td>
<select name='paciente_comuna' id='paciente_comuna'>
<option value=-1>(Seleccionar Comuna...)</option>
<?php echo $comunahtml; ?>
</select>
</td>
<td>
<select id='paciente_nacion' name='paciente_nacion'>
<?php echo $nacionhtml; ?>
</select>
</td>
<td style='text-align: center;'>
<input type='text' id='paciente_fecha' name='paciente_fecha'
style='text-align: center;' onBlur='calcular_edad();'></td>

<td width=200 id='mostrar_edad' style='text-align: center;'></td></tr>
<tr style='text-align: center;'>
<td>Estado Civil:</td>
<td>Grupo Sangu&iacute;neo:</td>
<td>Previsi&oacute;n:</td>
<td>Sexo</td>
</tr>
<tr style='text-align: center;'>
<td>
<select id='paciente_estciv' name='paciente_estciv'>
<?php echo $estcivhtml; ?>
</select>
</td>
<td>
<select id='paciente_sangre' name='paciente_sangre'>
<?php echo $sangrehtml; ?>
</select>
</td>
<td>
<select id='paciente_prevision' name='paciente_prevision'
    onClick='actualizar_cod_prev();' style='text-align: center;'>
    <?php echo $previsionhtml; ?>
</select>
</td>
<td rowspan=2 valign='top'>
<select id='paciente_sexo' name='paciente_sexo'>
<?php echo $sexohtml; ?>
</select>
</td>

</tr>
</table>


    <input type='text' size='2' 
    id='paciente_cod_prev' name='paciente_cod_prev'
    style='text-align: center;display:none;' value=0>

    <select id='paciente_prevision' name='paciente_prevision'
    onClick='actualizar_cod_prev();' style='display:none;'>
    <?php echo $previsionhtml; ?>
    </select>

    <input type='text' size='1' maxlength=1 
    id='paciente_tramo' name='paciente_tramo'
    style='text-align: center;display:none;'>

<div class='' style='border:1px solid black;overflow:auto;height:80px;' id='lista_patologias'>
<table style='width:100%;'>
	<tr style='background-color:#dddddd;'>
		<td colspan=2>Patolog&iacute;as Activas</td>
	</tr>
</table>
</div>


</center>



		</div>
		
		<div class='tabbed_content' id='tab_refs_content' style='display: none;'>
		<table>
		<tr>
		<td style='text-align: right;'>Nombre del Padre:</td>
    <td><input type='text' size=70 maxlength=200
    id='paciente_padre' name='paciente_padre'></td>
		</tr>
		<tr>
		<td style='text-align: right;'>Nombre de la Madre:</td>
    <td><input type='text' size=70 maxlength=200 
    id='paciente_madre' name='paciente_madre'></td>
		</tr>
		</table>
		
		<div class='sub-content'>
		<div class='sub-content'>
		<img src='iconos/group_link.png'> Parentezco
		</div>
		<div class='sub-content2' id='parentezco_div' name='parentezco_div'
    style='min-height: 100px; height: 100px; overflow: auto;'>
		
		</div>
		</div>
		</div>
		
		<div class='tabbed_content' id='tab_recetas_content'
    style='height:250px; overflow:auto; display: none;'>
		  (No ha seleccionado un paciente...)
		</div>
		
		<div class='tabbed_content' id='tab_historial_content'
    style='height:250px; overflow:auto; display: none;'>
		  (No ha seleccionado un paciente...)
		</div>
		
		<div id='paciente_nuevo' style='display: none;'>
    <center>
	  <table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/user_add.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'>Guardar Ficha B&aacute;sica...</a>
		</td></tr></table>
		</div>
		</td></tr></table>
		</center>
    </div>


    <div id='paciente_editar' style='display: none;'>
    <center>
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/user_add.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'>Guardar Ficha B&aacute;sica...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/user_delete.png'>
		</td><td>
		<a href='#' onClick='cancelar_edicion();'>Cancelar Cambios a Ficha...</a>
		</td></tr></table>
		</div>
	  </td></tr></table>
		</center>
    </div>
    
    
    <div id='paciente_antiguo' style='display: none;'>
    <center>
	  <table cellspacing=0 cellspacing=0><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/user_edit.png'>
		</td><td>
		<a href='#' onClick='editar_paciente();'>Editar Ficha B&aacute;sica...</a>
		</td></tr></table>
		</div>
		</td><td>
		
    
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/pill.png'>
		</td><td>
		<a href='#' onClick='despachar_receta($("paciente_id").value, false);'>
		Desp. Receta...</a>
		</td></tr></table>
		</div>
	  
    
    </td><td>
    
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/pill.png'>
		</td><td>
		<a href='#' onClick='despachar_receta($("paciente_id").value, true);'>
		Desp. Receta Controlados...</a>
		</td></tr></table>
		</div>
	  
    
    </td></tr></table>
    </center>
    </div>
    
		
		</div>
		
		</td></tr>
    
    </table>
		
		</form>
		
		
		<script> deshabilitar_ficha_basica(true); </script>

