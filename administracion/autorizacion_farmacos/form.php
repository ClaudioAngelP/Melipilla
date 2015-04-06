<?php

  require_once('../../conectar_db.php');
  
  $pat_internas = pg_query("SELECT DISTINCT pst_patologia_interna
  FROM patologias_sigges_traductor
  ORDER BY pst_patologia_interna");
    
  while($xproblema = pg_fetch_array($pat_internas)){
	  
	   $opciones_xproblema = $opciones_xproblema."<OPTION
	   value='".htmlentities($xproblema['pst_patologia_interna']).
	   "'>".htmlentities($xproblema['pst_patologia_interna']).
	   "</OPTION>";
	  }
	  
?>

<script>

listar_autorizaciones = function() {

  $('mostrar_autorizacion').style.display='none';
  
  var myAjax = new Ajax.Updater(
  'lista_autorizaciones',
  'administracion/autorizacion_farmacos/listar_autorizacion.php',
  {
    method: 'post',
  }
  );

}

buscar_codigo_prod = function() {
	
  $('art_cargando').style.display='';
  $('art_id').value=0;

  var myAjax = new Ajax.Request(
  'administracion/autorizacion_farmacos/abrir_articulo.php',
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
  autf_id = ($('autf_id').value*1);
  
  if(art_id==0 || autf_id==0) return;
  
  var myAjax = new Ajax.Request(
  'administracion/autorizacion_farmacos/sql_articulo.php',
  {
    method: 'get',
    parameters: 'autf_id='+autf_id+'&art_id='+art_id+'&'+$('presta').serialize(),
    onComplete: function(respuesta) {
      
      try {
      
        datos = respuesta.responseText.evalJSON(true);
      
		  if(datos[0]==true) {
			abrir_autorizacion(autf_id);
		  } else {
			alert('El art&iacute;culo ya est&aacute; asociado a un autorizacion. '+
			'Nombre del Convenio ['+datos[1]+']'.unescapeHTML());
		  }
		  
      } catch (err) {
        alert(err);
      }
      
      
    }
  }
  );

}

quitar_articulo = function(id) {

  autf_id = ($('autf_id').value*1);
  
  if(art_id==0 || autf_id==0) return;

   confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar el art&iacute;culo de autorizacion? - No hay opciones para deshacer.'.unescapeHTML());

     if(confirma){
  var myAjax = new Ajax.Request(
  'administracion/autorizacion_farmacos/sql_articulo_quitar.php',
  {
    method: 'get',
    parameters: 'autf_id='+autf_id+'&art_id='+id,
    onComplete: function(respuesta) {
      
      try {
        datos = respuesta.responseText.evalJSON(true);
      } catch (err) {
        alert(err);
      }
      
      if(datos==true) {
        abrir_autorizacion(autf_id);
      }

    }
  }
  );
  }
}

	guardar_autorizacion=function() {
		
	  var myAjax = new Ajax.Request(
	  'administracion/autorizacion_farmacos/sql.php',
	  {
		method: 'post',
		parameters: $('datos_autorizacion').serialize(),
		onComplete: function(respuesta) {
		  if(respuesta.responseText=='1') {
			alert('Autorizaci&oacute;n definida exitosamente.');
			listar_autorizaciones();
		  } else {
			alert('ERROR:\n\n'+respuesta.responseText);
		  }
		}
	  }
	  );

	}


	abrir_autorizacion=function(id) {

	  $('mostrar_autorizacion').style.display='';
	  $('nombre_autorizacion').innerHTML='<img src="imagenes/ajax-loader1.gif">';
	  $('autf_id').value=id;

	  var myAjax = new Ajax.Updater(
	  'lista_autorizaciones',
	  'administracion/autorizacion_farmacos/abrir_autorizacion.php',
	  {
		method: 'get',
		parameters: 'autf_id='+(id*1),
		evalScripts: true
	  }
	  );

	}

	eliminar_autorizacion = function(id) {

	 confirma=confirm('&iquest;Est&aacute; seguro que desea eliminar este autorizaci&oacute;n de f&aacute:rmacos? - No hay opciones para deshacer.'.unescapeHTML());

     if(confirma){

       var myAjax2 = new Ajax.Request(
		'administracion/autorizacion_farmacos/elimina_autorizacion.php',
		{
			method: 'get',
            parameters: 'autorizacion_id='+(id*1),
            onComplete: function(respuesta) {
                if(respuesta.responseText=='2') {
                alert('Convenio Eliminado exitosamente.');
                listar_autorizaciones();
              } else {
                alert('ERROR:\n\n'+respuesta.responseText);
              }
                }
                 }
              );
            }

   }

 //**************************************************
 editar_autorizacion=function() {

  //$('autorizacion_nuevo').style.display='';
  //$('autorizacion_nuevo_boton').style.display='none';
  $('autorizacion_nombre').focus();


}




	abrir_articulo=function(d) {
		$('art_id').value=d[5];
		$('art_nombre').innerHTML=d[2];
	}


</script>

<center>
<table style='width:950px;'>
<tr><td>
<div class='sub-content'>

<div class='sub-content'>
<img src='iconos/pill.png'> <b>Autorizaci&oacute;n de F&aacute;rmacos</b>
</div>

<div class='sub-content' id="mostrar_autorizacion">

<center>


<form id='datos_autorizacion' name='datos_convenio' onSubmit='return false;'>

<input type='hidden' name='autf_id' id='autf_id' value='' />

<table>

<tr>

<td style='text-align:right;'>Nombre Autorizaci&oacute;n:</td>

<td colspan=3>

<input type='text' id="nombre_autorizacion" name="nombre_autorizacion" value="" size=60 />

</td>

</tr>

<tr>

<td style='text-align:right;'>Requiere Validaci&oacute;n:</td>

<td colspan=3>

<input type='checkbox' id="autf_validar" name="autf_validar" value="" size=60 />

</td>

</tr>

<tr >
	
<td style='text-align:right;'>Patolog&iacute;a GES Asociada</td>
	
<td colspan=3>

<SELECT id="pat_ges" name="pat_ges">
	<OPTION value="">(Seleccionar...)</OPTION>
	<?php echo $opciones_xproblema; ?>
</SELECT>

</td>

</tr>

</table>

</form>

<input type='button' id='' name='' value='-- Guardar Autorizaci&oacute;n --' onClick='guardar_autorizacion();' />

<input type='button' id='' name='' value='-- Volver Atr&aacute;s --' onClick='listar_autorizaciones();' />

</center>

</div>

<div id='listado_autorizaciones'>

<div class='sub-content2' id='lista_autorizaciones'
style='overflow: auto; height: 350px;'>



</div>

</div>


</div>

</td></tr>

</table>
</center>

<script>  listar_autorizaciones(); </script>
