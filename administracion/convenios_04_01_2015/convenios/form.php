<?php

  require_once('../../conectar_db.php');
    
?>

<script>

listar_convenios = function() {

  $('mostrar_convenio').style.display='none';
  $('filtro_provee').style.display='';

  var myAjax = new Ajax.Updater(
  'lista_convenios',
  'administracion/convenios/listar_convenios.php',
  {
    method: 'post',
    parameters: 'prov_id='+($('id_proveedor2').value*1)
  }
  );

}

buscar_codigo_prod = function() {
  $('art_cargando').style.display='';
  $('art_id').value=0;

  var myAjax = new Ajax.Request(
  'administracion/convenios/abrir_articulo.php',
  {
    method: 'get',
    parameters: $('art_codigo').serialize(),
    onComplete: function(respuesta) {
      
      $('art_cargando').style.display='none';
      
      try {
        datos = respuesta.responseText.evalJSON(true);
      } catch(err) { 
        alert('ERROR:\n\n'+err); 
      }
      
      if(datos[0]) {
        $('art_id').value=datos[1][0];
        $('nombre_articulo').innerHTML='<span id="__art_id_'+datos[1][0]+'" class="texto_tooltip">'+datos[1][2]+'</span>';
        
      } else {
        $('nombre_articulo').innerHTML='<i>Art&iacute;culo no existe.</i>';
      }
      
    }
    
  }
  );
  
}

agregar_articulo=function() {

  $('agregar_articulo').style.display='';
  $('agregar_articulo_boton').style.display='none';
  $('codigo').focus();

}

cancelar_articulo=function() {

  $('agregar_articulo').style.display='none';
  $('agregar_articulo_boton').style.display='';
  $('art_id').value=0;  
  $('codigo').value='';
  $('art_nombre').innerHTML='';
}

insertar_articulo=function() {

  art_id = ($('art_id').value*1);
  convenio_id = ($('convenio_id').value*1);
  
  if(art_id==0 || convenio_id==0) return;
  
  var myAjax = new Ajax.Request(
  'administracion/convenios/sql_articulo.php',
  {
    method: 'get',
    parameters: 'convenio_id='+convenio_id+'&art_id='+art_id+'&'+$('conveniod_punit').serialize(),
    onComplete: function(respuesta) {
      
      try {
        datos = respuesta.responseText.evalJSON(true);
      } catch (err) {
        alert(err);
      }
      
      if(datos[0]==true) {
        abrir_convenio(convenio_id);
      } else {
        alert('El art&iacute;culo ya est&aacute; asociado a un convenio. '+
        'Nombre del Convenio ['+datos[1]+']'.unescapeHTML());
      }
      
    }
  }
  );

}

quitar_articulo = function(id) {

  convenio_id = ($('convenio_id').value*1);
  
  if(art_id==0 || convenio_id==0) return;

   confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar el art&iacute;culo de convenio? - No hay opciones para deshacer.'.unescapeHTML());

     if(confirma){
  var myAjax = new Ajax.Request(
  'administracion/convenios/sql_articulo_quitar.php',
  {
    method: 'get',
    parameters: 'convenio_id='+convenio_id+'&art_id='+id,
    onComplete: function(respuesta) {
      
      try {
        datos = respuesta.responseText.evalJSON(true);
      } catch (err) {
        alert(err);
      }
      
      if(datos==true) {
        abrir_convenio(convenio_id);
      }

    }
  }
  );
  }
}

	guardar_convenio=function() {
		
		if(trim($('convenio_licitacion').value)=='') {
			alert('Debe ingresar ID de licitaci&oacute;n.'.unescapeHTML());
			$('convenio_licitacion').focus();
			return;
		}

		
		if($('id_proveedor').value*1==0) {
			alert('Debe seleccionar un proveedor.');
			$('nombre_proveedor').focus();
			return;
		}

		if($('monto').value*1==0) {
			alert('Debe seleccionar un monto total para el convenio.');
			$('monto').focus();
			return;
		}

		if($('plazo').value*1==0) {
			alert( 'Debe seleccionar un plazo en d&iacute;as mayor que cero.'.unescapeHTML() );
			$('plazo').focus();
			return;
		}

		if(!validacion_fecha($('inicio'))) {
			alert( 'Debe seleccionar una fecha inicial v&aacute;lida.'.unescapeHTML() );
			$('inicio').focus();
			return;
		}

		if(!validacion_fecha($('termino'))) {
			alert( 'Debe seleccionar una fecha final v&aacute;lida.'.unescapeHTML() );
			$('termino').focus();
			return;
		}

		if(!validacion_fecha($('fecha_contrato'))) {
			alert( 'Debe seleccionar una fecha de aprobaci&oacute;n del contrato v&aacute;lida.'.unescapeHTML() );
			$('fecha_contrato').focus();
			return;
		}

		if(trim($('fecha_boleta').value)!='' && !validacion_fecha($('fecha_boleta'))) {
			alert( 'Debe seleccionar una fecha de boleta de garant&iacute;a v&aacute;lida.'.unescapeHTML() );
			$('fecha_boleta').focus();
			return;
		}

	  var myAjax = new Ajax.Request(
	  'administracion/convenios/sql.php',
	  {
		method: 'post',
		parameters: $('datos_convenio').serialize(),
		onComplete: function(respuesta) {
		  if(respuesta.responseText=='1') {
			alert('Convenio Ingresado exitosamente.');
			listar_convenios();
		  } else {
			alert('ERROR:\n\n'+respuesta.responseText);
		  }
		}
	  }
	  );

	}


	abrir_convenio=function(id) {

	  $('mostrar_convenio').style.display='';
		$('filtro_provee').style.display='none';
	  $('nombre_convenio').innerHTML='<img src="imagenes/ajax-loader1.gif">';
	  $('convenio_id').value=id;

	  var myAjax = new Ajax.Updater(
	  'lista_convenios',
	  'administracion/convenios/abrir_convenio.php',
	  {
		method: 'get',
		parameters: 'convenio_id='+(id*1),
		evalScripts: true
	  }
	  );

	}
//**************************************************

	eliminar_convenio = function(id) {

	 confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar este convenio? - No hay opciones para deshacer.'.unescapeHTML());

     if(confirma){

       var myAjax2 = new Ajax.Request(
		'administracion/convenios/elimina_convenio.php',
		{
			method: 'get',
            parameters: 'convenio_id='+(id*1),
            onComplete: function(respuesta) {
                if(respuesta.responseText=='2') {
                alert('Convenio Eliminado exitosamente.');
                listar_convenios();
              } else {
                alert('ERROR:\n\n'+respuesta.responseText);
              }
                }
                 }
              );
            }

   }

 //**************************************************
 editar_convenio=function() {

  //$('convenio_nuevo').style.display='';
  //$('convenio_nuevo_boton').style.display='none';
  $('convenio_nombre').focus();


}




  mostrar_proveedor=function(datos) {
    $('id_proveedor').value=datos[3];
    $('rut_proveedor').value=datos[1];
    $('nombre_proveedor').value=datos[2].unescapeHTML();
  }
  
  liberar_proveedor=function() {
    $('id_proveedor').value=-1;
    $('nombre_proveedor').value='';
    $('rut_proveedor').value='';
    $('nombre_proveedor').focus();
  }

  autocompletar_proveedores = new AutoComplete(
    'nombre_proveedor', 
    'autocompletar_sql.php',
    function() {
      if($('nombre_proveedor').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('nombre_proveedor').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_proveedor);


  mostrar_proveedor2=function(datos) {
    $('id_proveedor2').value=datos[3];
    $('rut_proveedor2').value=datos[1];
    $('nombre_proveedor2').value=datos[2].unescapeHTML();
    listar_convenios();
  }
  
  liberar_proveedor2=function() {
    $('id_proveedor2').value=0;
    $('nombre_proveedor2').value='';
    $('rut_proveedor2').value='';
    $('nombre_proveedor2').focus();
    listar_convenios();
  }

  autocompletar_proveedores = new AutoComplete(
    'nombre_proveedor2', 
    'autocompletar_sql.php',
    function() {
      if($('nombre_proveedor2').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=proveedores&busca_proveedor='+encodeURIComponent($('nombre_proveedor2').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_proveedor2);


  mostrar_funcionario=function(datos) {
    $('func_id').value=datos[3];
    $('rut_funcionario').value=datos[1];
    $('nombre_funcionario').value=datos[2].unescapeHTML();
  }
  
  liberar_funcionario=function() {
    $('func_id').value=-1;
    $('rut_funcionario').value='';
    $('nombre_funcionario').value='';
    $('nombre_funcionario').focus();
  }

  autocompletar_funcionario = new AutoComplete(
    'nombre_funcionario', 
    'autocompletar_sql.php',
    function() {
      if($('nombre_funcionario').value.length<3) return false;
      
      return {
        method: 'get',
        parameters: 'tipo=funcionarios&nomfuncio='+encodeURIComponent($('nombre_funcionario').value)
      }
    }, 'autocomplete', 450, 200, 250, 1, 2, mostrar_funcionario);




	abrir_articulo=function(d) {
		$('art_id').value=d[5];
		$('art_nombre').innerHTML=d[2];
		$('conveniod_punit').value='';
		$('conveniod_punit').focus();
	}


</script>

<center>
<table style='width:950px;'>
<tr><td>
<div class='sub-content'>

<div class='sub-content'>
<img src='iconos/database_link.png'> <b>Convenios</b>
</div>

<div class='sub-content' id="mostrar_convenio">

<center>


<form id='datos_convenio' name='datos_convenio' onSubmit='return false;'>

<input type='hidden' name='convenio_id' id='convenio_id' value='' />

<table>

<tr>

<td style='text-align:right;'>ID Licitaci&oacute;n:</td>

<td colspan=3>

<input type='text' id="convenio_licitacion" name="convenio_licitacion" value="" />

</td>

</tr>


<tr>

<td style='text-align:right;'>Nombre Convenio:</td>

<td colspan=3>

<input type='text' id="nombre_convenio" name="nombre_convenio" value="" size=60 />

</td>

</tr>

<tr>

<td style='text-align:right;'>Nro. Res. Aprueba Bases:</td>

<td>

<input type='text' id="res_aprueba" name="res_aprueba" value="" size=20 />

</td>

<td style='text-align:right;'>A&ntilde;o:</td>

<td>

<input type='text' id="anio_aprueba" name="anio_aprueba" value="" size=5 />

</td>

</tr>


<tr>

<td style='text-align:right;'>Nro. Res. Adjudica:</td>

<td>

<input type='text' id="res_adjudica" name="res_adjudica" value="" size=20 />

</td>

<td style='text-align:right;'>A&ntilde;o:</td>

<td>

<input type='text' id="anio_adjudica" name="anio_adjudica" value="" size=5 />

</td>

</tr>

<tr>

<td style='text-align:right;'>Nro. Res. Aprueba Contrato:</td>

<td>

<input type='text' id="res_contrato" name="res_contrato" value="" size=20 />

</td>

<td style='text-align:right;'>Fecha:</td>

<td>

<input type='text' id="fecha_contrato" name="fecha_contrato" value="" style='text-align:center;' 
onBlur='validacion_fecha(this);' size=10 />

</td>

</tr>


<tr><td style='text-align:right;'>
Proveedor:
</td><td colspan=3>

<input type="hidden" id="id_proveedor" name="id_proveedor" value="-1">
<input type="text" id="rut_proveedor" name="rut_proveedor" size=10
style='text-align:right;font-size:11px;' DISABLED />
<input type="text" id="nombre_proveedor" name="nombre_proveedor" size=50
style='font-size:11px;' onDblClick='liberar_proveedor();' />

</td></tr>

<tr><td style='text-align:right;'>
Administrador Contrato:
</td><td colspan=3>

<input type="hidden" id="func_id" name="func_id" value="-1">
<input type="text" id="rut_funcionario" name="rut_funcionario" size=10
style='text-align:right;font-size:11px;' DISABLED />
<input type="text" id="nombre_funcionario" name="nombre_funcionario" size=50
style='font-size:11px;' onDblClick='liberar_funcionario();' />

</td></tr>


<tr><td style='text-align:right;'>
e-mail(s) Contacto:
</td><td colspan=3>
<input type='text' id='mails' name='mails' value='' size=60 />
</td></tr>

<tr><td style='text-align:right;'>
Monto $:
</td><td>
<input type='text' id='monto' name='monto' value='' size=10 />
</td><td style='text-align:right;'>
Plazo de Entrega:
</td><td>
<input type='text' id='plazo' name='plazo' value='' size=5 />
</td></tr>

<tr><td style='text-align:right;'>
Fecha Inicio:
</td><td>
<input type='text' id='inicio' name='inicio' value='' 
onBlur='validacion_fecha(this);' style='text-align:center;' size=10 />
</td><td style='text-align:right;'>
Fecha T&eacute;rmino:
</td><td>
<input type='text' id='termino' name='termino' value='' 
onBlur='validacion_fecha(this);' style='text-align:center;' size=10 />
</td></tr>

<tr><td style='text-align:right;'>
N&uacute;mero Boleta Garant&iacute;a:
</td><td>
<input type='text' id='nro_boleta' name='nro_boleta' value='' 
style='text-align:center;' size=20 />
</td><td style='text-align:right;'>
Fecha Boleta Garant&iacute;a:
</td><td>
<input type='text' id='fecha_boleta' name='fecha_boleta' value='' 
onBlur='validacion_fecha(this);' style='text-align:center;' size=10 />
</td></tr>

<tr><td style='text-align:right;'>
Banco Boleta Garant&iacute;a:
</td><td>
<input type='text' id='banco_boleta' name='banco_boleta' value='' style='text-align:center;' size=20 />
</td><td style='text-align:right;'>
Monto Boleta $:
</td><td>
<input type='text' id='monto_boleta' name='monto_boleta' value='' style='text-align:center;' size=15 />
</td></tr>

<tr><td style='text-align:right;'>
Multa (Descripci&oacute;n):
</td><td colspan=3>
<textarea id='multa' name='multa' value='' cols=60 rows=3></textarea>
</td></tr>

<tr><td style='text-align:right;'>
Comentarios:
</td><td colspan=3>
<textarea id='comenta' name='comenta' value='' cols=60 rows=3></textarea>
</td></tr>

</table>

</form>

<input type='button' id='' name='' value='-- Guardar Convenio --' onClick='guardar_convenio();' />

<input type='button' id='' name='' value='-- Volver Atr&aacute;s --' onClick='listar_convenios();' />

</center>


</div>

<div id='listado_convenios'>

<table style='width:100%;' id='filtro_provee'>
<tr><td style='text-align:right;'>
Filtro Proveedor:
</td><td colspan=3>

<input type="hidden" id="id_proveedor2" name="id_proveedor2" value="0" onChange='listar_convenios();'>
<input type="text" id="rut_proveedor2" name="rut_proveedor2" size=10
style='text-align:right;font-size:11px;' DISABLED />
<input type="text" id="nombre_proveedor2" name="nombre_proveedor2" size=50
style='font-size:11px;' onDblClick='liberar_proveedor2();' />

</td></tr>
</table>


<div class='sub-content2' id='lista_convenios'
style='overflow: auto; height: 300px;'>



</div>

</div>


</div>

</td></tr>

</table>
</center>

<script>  listar_convenios(); </script>
