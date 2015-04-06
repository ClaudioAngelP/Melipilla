<?php
	require_once('../../conectar_db.php');
	$talonarioshtml = desplegar_opciones("receta_tipo_talonario", "tipotalonario_id, tipotalonario_nombre", '0', '1=1', 'ORDER BY tipotalonario_id');
	$doctoreshtml = desplegar_opciones("doctores", "doc_id,'['||doc_rut||'] '||doc_paterno || ' ' || doc_materno || ' ' || doc_nombres AS nombre,doc_paterno", '1', '1=1 AND doc_id in (select distinct talonario_func_id from talonario)', 'ORDER BY doc_paterno');
?>
<script>	
	cargar_talonarios = function() {
		var myAjax = new Ajax.Updater('lista_talonarios','administracion/talonarios/listar_talonarios.php',
		{
		  method: 'get',
		  parameters: $('buscar_talonario').serialize()
		});
	}

	cargar_talonario = function(id_tal) {
		var myAjax = new Ajax.Request('administracion/talonarios/cargar_talonario.php',
		{
			method: 'get',
			parameters: 'id_talonario='+id_tal,
			onComplete: function(respuesta) {
				try {
						editar_talonario();
						datos = respuesta.responseText.evalJSON(true);
						__talonario_tipo=datos.talonario_tipotalonario_id*1;
						$('talonario_id').value=datos.talonario_id;
						selval($('tipo_talonario_2'),__talonario_tipo);
						$('nro_talonario').value=datos.talonario_numero;
						selval($('_estado_talonario'),datos.talonario_estado*1);
						$('nro_inicial').value=datos.talonario_inicio;
						$('nro_final').value=datos.talonario_final;
						$('func_id').value=datos.doc_id;
						$('func_rut').value=datos.doc_rut;
						$('func_nombre').value=datos.doc_nombres + ' ' + datos.doc_paterno + ' ' + datos.doc_materno;
						//$('tipo_talonario_2').value=datos.talonario_tipotalonario_id;
						cargar_recetas(id_tal);
				} catch(err) {
					alert(err);
				}
			}
		});
	}

  cargar_recetas = function(id_tal) {

    $('cheques').innerHTML='<br><br><img src="imagenes/ajax-loader2.gif">';

    var myAjax = new Ajax.Updater(
    'cheques',
    'administracion/talonarios/cargar_recetas.php',
    {
      method: 'get',
      parameters: 'id_talonario='+id_tal
    }
    );

  }

  cargar_funcionario = function() {

    var myAjax = new Ajax.Request(
    'nombre_funcionario.php',
    {
      method: 'get',
      parameters: 'func_rut='+encodeURIComponent($('func_rut').value),
      onComplete: function(respuesta) {

        func_arr = respuesta.responseText.evalJSON();

        $('func_id').value=func_arr[0];
        if(func_arr[0]!=0) {
          $('rutselfun').style.display='';
          $('func_nombre').value=func_arr[1].unescapeHTML();
        } else {
          $('func_id').value=0;
          $('func_rut').value='';
          $('func_nombre').value='';
          $('rutselfun').style.display='none';
        }

      }
    }
    );

  }
   comprobar_nro_receta = function(){
	   
	   
	 var myAjax = new Ajax.Request(
			'ficha_clinica/comprueba_nro_receta.php', 
			{
				method: 'get', 
				parameters: 'nro_receta='+$('n_receta').value,
				onComplete: function(pedido_datos) {
				
				    try {
						datos = pedido_datos.responseText.evalJSON(true);
						
						$('notificacion').innerHTML=datos[1].unescapeHTML();
						
						if(datos[0]==true){
								$('notificacion').style.color='green';
								$('notification_img').src='iconos/tick.png';
						}else{
								$('notificacion').style.color='red';
								$('notification_img').src='iconos/cross.png';
								//alert(datos[1].unescapeHTML());
						}
						return;
						
				    } catch(err) { 
						  alert(err);
						  bloquear_ingreso=false;
						  return;
					}
				}
				
			}
			
			);  

	}    

  cargar_medico = function() {

    var myAjax = new Ajax.Request(
    'nombre_medico.php',
    {
      method: 'get',
      parameters: 'func_rut='+encodeURIComponent($('func_rut').value),
      onComplete: function(respuesta) {

        func_arr = respuesta.responseText.evalJSON();

        $('func_id').value=func_arr[0];
        if(func_arr[0]!=0) {
          $('rutselmed').style.display='';
          $('func_nombre').value=func_arr[1].unescapeHTML();
        } else {
          $('func_id').value=0;
          $('func_rut').value='';
          $('func_nombre').value='';
          $('rutselmed').style.display='none';
         
          
        }
      }
    }
    );

  }

	ingresar_talonario  = function() {
		$('ingresar_talonario').style.display='';
		$('busqueda_talonario').style.display='none';
		$('lista_talonarios').style.height='150px';
		$('talonario_id').value='0';
		selval($('tipo_talonario_2'),0);
		$('nro_talonario').value='';
		selval($('_estado_talonario'),0);
		$('nro_inicial').value='';
		$('nro_final').value='';
		$('func_id').value='';
		$('func_rut').value='';
		$('func_nombre').value='';
		$('servicio').value='';
		$('centro_ruta').value='';
		$('cheques').innerHTML='';

  }


  editar_talonario = function() {

    $('ingresar_talonario').style.display='';
    $('busqueda_talonario').style.display='none';
    $('lista_talonarios').style.height='150px';

  }


  cancelar_ingreso= function() {

    $('ingresar_talonario').style.display='none';
    $('busqueda_talonario').style.display='';
    $('lista_talonarios').style.height='310px';

  }

  guardar_talonario = function() {
	  
	 if($('talonario_id').value!=0) {
	  
		 for(c=$('nro_inicial').value*1;c<=$('nro_final').value*1;c++) {
			console.log(c);
		 if($('check_receta_'+c).checked==false) {
			
			if($('causal_receta_'+c).value==''){
				alert('Debe seleccionar una causal de invalidez para la receta número '+c+''.unescapeHTML());
				$('causal_receta_'+c).focus();
				return;
			}
		  }
		}
	}
	  
    var myAjax = new Ajax.Request(
    'administracion/talonarios/sql.php',
    {
      method: 'post',
      parameters: $('talonario_nuevo').serialize(),
      onComplete: function(respuesta) {

        try {
          datos = respuesta.responseText.evalJSON(true);
        } catch(err) {
          alert(err);
        }

        if(datos[0]==true) {
          alert('Talonario modificado exitosamente.');
          cancelar_ingreso();
          cargar_talonarios();
        } else {
          alert(datos[1].unescapeHTML());
        }

      }
    }
    );

  }

  borrar_talonarios = function(id) {

    var myAjax = new Ajax.Request(
    'administracion/talonarios/sql_quitar.php',
    {
      method: 'get',
      parameters: 'talonario_id='+(id*1),
      onComplete: function(respuesta) {

          alert('Talonario eliminado exitosamente.');
          cargar_talonarios();

      }
    }
    )

  }

  modificar_validez = function() {

    if(($('modini').value*1)<($('nro_inicial').value*1)) {
      alert('El n&uacute;mero inicial est&aacute; fuera del rango del talonario.'.unescapeHTML());
      return;
    }

    if(($('modfin').value*1)>($('nro_final').value*1)) {
      alert('El n&uacute;mero final est&aacute; fuera del rango del talonario.'.unescapeHTML());
      return;
    }

    if($('modvalidez').value==0)
      chequear=false;
    else
      chequear=true;

    var obviar=0;

    for(i=$('modini').value*1;i<=$('modfin').value*1;i++) {

      if($('check_receta_'+i).disabled==false) {
        $('check_receta_'+i).checked=chequear;
        if(!chequear){
			$('causal_receta_'+i).disabled=false;
			$('causal_receta_'+i).value=$('causal').value;
		}
      } else {
        obviar++;
      }

    }

    if(obviar>0) {
      obvtxt=' Dejando sin modificar '+obviar+' recetas que ya estaban emitidas.';
    } else {
      obvtxt='';
    }

    alert(('Se ha modificado desde la receta '+($('modini').value*1)+
    ' hasta la '+($('modfin').value*1)+'.'+obvtxt).unescapeHTML());

    $('modini').value='';
    $('modfin').value='';


  }

</script>

<center>
<table width=850>
<tr><td>

<div class='sub-content'>
<div class='sub-content'>
<img src='iconos/page_white_edit.png'>
<b>Administraci&oacute;n de Talonarios de Recetas</b>
</div>

<form id='buscar_talonario' name='buscar_talonario'
onChange='cargar_talonarios();'
onSubmit='return false;'>

<div class='sub-content' id='busqueda_talonario'>
<table>
<tr>
<td style='text-align: right;'>Tipo:</td>
<td>
<select id='tipo_talonario' name='tipo_talonario'>
<option value=-1 SELECTED>(Todos...)</option>
<?=$talonarioshtml?>
</select>
</td>


<td style='text-align: right;'><b>Receta N°:</b></td>	
		<td><input type='text' id='n_receta' name='n_receta'size=20 style='text-align: left;'OnKeyUp='if(event.which==13)comprobar_nro_receta()'> 
		<img src='iconos/information.png' id='notification_img'>
		[<label id='notificacion' name='notificacion' >NOTIFICACI&Oacute;N</label>]
</td>
</tr>


<tr>
<td style='text-align: right;'>Estado:</td>
<td colspan=3>
<select id='estado_talonario' name='estado_talonario'>
<option value=-1 SELECTED>(Todos...)</option>
<option value=1>Activo</option>
<option value=2>Inactivo</option>
</select>
&nbsp;&nbsp;Profesional:
<select id='doc_id' name='doc_id'>
<option value='' SELECTED>(Todos...)</option>
<?=$doctoreshtml?>
</select>
</td>
</tr>

<tr>
<td style='text-align: right;'>Ver:</td>
<td>
<select id='ver_talonario' name='ver_talonario'>
<option value=1 SELECTED>Funcionario Responsable</option>
<option value=2>Centro de Costo</option>
</select>
</td>
</tr>



</table>

<center><input type='button' id='crear_talonario' value='Ingresar nuevo talonario...' onClick='ingresar_talonario();' /></center>

</div>
</form>

<div class='sub-content' id='ingresar_talonario' style='display: none;'>
<center>
<form id='talonario_nuevo' name='talonario_nuevo' onSubmit='return false;'>
<input type='hidden' id='talonario_id' name='talonario_id' value=0>
<table>
<tr style='text-align: center; font-weight: bold;'>
<td>Tipo de Receta:</td>
<!---<td>Folio:</td>-->
<td></td>
<td>Estado:</td>
<td>Nro. Inicial</td>
<td>Nro. Final</td>
</tr>
<tr>
<td>
<select id='tipo_talonario_2' name='tipo_talonario_2'>
<?=$talonarioshtml?>
</select>
</td>
<td>
<input type='text' style='display:none;' id='nro_talonario' name='nro_talonario' value='' />
</td>
<td>
<select id='_estado_talonario' name='_estado_talonario'>
<option value=0 SELECTED>Sin Asignar</option>
<option value=1>Activo</option>
<option value=2>Inactivo</option>
</select>
</td>
<td>
<input type='text' id='nro_inicial' name='nro_inicial'  size=10
style='text-align: right;'>
</td>
<td>
<input type='text' id='nro_final' name='nro_final' size=10
style='text-align: right;'>
</td>

</tr>
</table>

<table>
<tr style='text-align: center; font-weight: bold;'>
<td>
<span id='rutmedlab'>Rut M&eacute;dico:</span>
<span id='rutfunlab' style='display: none;'>Rut Funcionario:</span>
</td>
<td>
<span id='nommedlab'>Nombre M&eacute;dico:</span>
<span id='nomfunlab' style='display: none;'>Nombre Funcionario:</span>

</td>

</tr>
<tr>
<td>
<input type='hidden' id='func_id' name='func_id' value=0>
<input type='text' id='func_rut' name='func_rut' size=10 disabled>
</td>
<td>
<input type='text' id='func_nombre' name='func_nombre' size=40
style='color: black;'>
</td>

</tr>
<tr>
	<td style='text-align: right;'><b>Servicio:</b></td>
	<td><input type='text' id='servicio' name='servicio'  size=40 style='text-align: left;'>
		<input type='hidden' id='centro_ruta' name='centro_ruta'>
	</td>
</tr>
</table>

<table><tr><td style='width:400px;'>
<div class='sub-content2' id='cheques' style='height: 150px; overflow:auto;'>

</div>
</td><td style='width:200px;'>

<div class='sub-content2' style='height: 150px;'>

<table>
<tr>
<td>Validez:</td>
<td>
<select id='modvalidez' name='modvalidez' style='font-size:10px;' onChange="if(this.value==0) $('tr_causal').style.display=''; else $('tr_causal').style.display='none';">

<option value=0>Invalidar</option>
<option value=1 SELECTED >Validar</option>
</select>
</td>
</tr>
<td>Rango:</td>
<td>
<input type='text' size=5 style='font-size:10px;' id='modini'>
<input type='text' size=5 style='font-size:10px;' id='modfin'>

</td>
</tr>
<tr style='display:none;'id='tr_causal' name='tr_causal'>
	<td>Causal:</td>
	<td><select id='causal' name='causal' >
		<option value=''>Seleccione...</option>
		<option value='EXTRAVIO'>Extrav&iacute;o</option>
		<option value='ROBO'>Robo</option>
		<option value='DEVOLUCION'>Devoluci&oacute;n</option>
	</select></td>
</tr>
</table>

<center>
<div class='boton'>
		<table><tr><td>
		<img src='iconos/wrench.png'>
		</td><td>
		<a href='#' onClick='modificar_validez();'>
		Realizar Cambio...</a>
		</td></tr></table>
</div>
</center>


</div>

</td></tr>


</form>


<table>
<tr><td>

<div class='boton'>
		<table><tr><td>
		<img src='iconos/disk.png'>
		</td><td>
		<a href='#' onClick='guardar_talonario();'>
		Guardar Modificaci&oacute;n de Talonario...</a>
		</td></tr></table>
</div>
</td><td>
<div class='boton'>
		<table><tr><td>
		<img src='iconos/stop.png'>
		</td><td>
		<a href='#' onClick='cancelar_ingreso();'>
		Cancelar Modificaci&oacute;n...</a>
		</td></tr></table>
</div>

</td></tr>
</table>

</center>

</div>

<div class='sub-content2' id='lista_talonarios' name='lista_talonarios'
style="height: 310px; overflow:auto;">



</div>

</div>

</td></tr>
</table>
</center>

<script>

ingreso_rut=function(datos_medico) {
        $('func_id').value=datos_medico[3];
        $('func_rut').value=datos_medico[1];
	$('func_nombre').value=datos_medico[0].unescapeHTML();
      }

      autocompletar_medicos = new AutoComplete(
      'func_nombre',
      'autocompletar_sql.php',
      function() {
        if($('func_nombre').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=medicos&nombre_medico='+encodeURIComponent($('func_nombre').value)
        }
	}, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);
	
	ingreso_servicio=function(datos_servicio) {
        $('centro_ruta').value=datos_servicio[0];
        $('servicio').value=datos_servicio[2];	
      }
      
      autocompletar_servicio = new AutoComplete(
      'servicio',
      'autocompletar_sql.php',
      function() {
        if($('servicio').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=centro_costo&cadena='+encodeURIComponent($('servicio').value)
        }
	}, 'autocomplete', 350, 200, 250, 2, 2, ingreso_servicio);

cargar_talonarios();

</script>
