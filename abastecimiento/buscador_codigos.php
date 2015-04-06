
<?php 

  $tipo_buscador = ($_GET['tipo_buscador']*1); 
  $buscador = ($_GET['buscador']);
  
  if(isset($_GET['bodega_sel'])) {
    $bodega_sel = $_GET['bodega_sel'];
  }

  
?>

<script>

__tipo_buscador=<?php echo $tipo_buscador; ?>;

__confirma_fecha = function(objeto_fld_fecha) {

  __perecible = $('___vence').value;

  if(__tipo_buscador==0 || __perecible==0 ) return true;
  
  __fecha_fld = $(objeto_fld_fecha);
  
  if(__fecha_fld.value.length==6) {
  
    __fecha_fld.value = __fecha_fld.value.substring(0,2) + '/' + __fecha_fld.value.substring(2,4) + '/20' + __fecha_fld.value.substring(4,6)
  
  } else if (__fecha_fld.value.length==8) {
  
  __fecha_fld.value = __fecha_fld.value.substring(0,2) + '/' + __fecha_fld.value.substring(2,4) + '/' + __fecha_fld.value.substring(4,8)
  
  }
  
  if(!isDate(__fecha_fld.value)) {
		__fecha_fld.style.background='yellow';
		return false;
  }
  
  partes_fecha = __fecha_fld.value.split('/');
  
  var fecha_actual = new Date;
  var fecha_lote = new Date;
						
	fecha_lote.setDate(partes_fecha[0]*1);
	fecha_lote.setMonth((partes_fecha[1]*1)-1);
	fecha_lote.setFullYear(partes_fecha[2]*1);
			
	if(fecha_actual>=fecha_lote) {
		__fecha_fld.style.background='yellow';
		return false;
	}
			
	fecha_actual.setMonth(fecha_actual.getMonth()+1);
			
	if(fecha_actual>=fecha_lote) {
			if(!confirm('La fecha de vencimiento ingresada est&aacute; a menos de un m&eacute;s de cumplirse. &iquest;Est&aacute; seguro?'.unescapeHTML())) {
				
			__fecha_fld.style.background='yellow';
			return false;
					
			}
	}
			
  
  __fecha_fld.style.background='';
  return true;
  
}

__buscar_codigo_prod_int=function() {

  $('___art_id').value=0;
  $('___codigo_articulo').innerHTML='';
  $('___glosa_articulo').innerHTML='<i>Cargando...</i>';
  $('___forma_articulo').innerHTML='';
  $('___mostrar_stock').innerHTML='';

  var myAjax9 = new Ajax.Request(
  'abastecimiento/buscador_codigos_reg.php',
  {
    method: 'get',
    parameters: $('_buscar_codigo_interno').serialize()+'&bodega_origen='+($('busca_cod_bar')._bod_id*1),
    onComplete: function(respuesta, datos) {
    
      try {
    
      datos=eval(respuesta.responseText);
      
      if(!datos) {
      $('___art_id').value=0;
      $('___art_codigo').value='';
      $('___artc_codigo').value='';
      $('___art_glosa').value='';
      $('___codigo_articulo').innerHTML='';
      $('___glosa_articulo').innerHTML='<i>No Encontrado...</i>';
      $('___forma_articulo').innerHTML='';
      $('___mostrar_stock').innerHTML='';
      $('___stock').value=0;
      $('___vence').value=0;
      $('___media').value=0;
      
      
      $('_buscar_codigo_interno').style.background='yellow';
      $('_buscar_codigo_interno').select();
    
      } else {
      
      $('___art_id').value=(datos[0]*1);
      $('___art_codigo').value=datos[1].unescapeHTML();
      $('___artc_codigo').value=datos[1].unescapeHTML();
      $('___art_glosa').value=datos[2].unescapeHTML();
      $('___codigo_articulo').innerHTML=datos[1];
      $('___glosa_articulo').innerHTML=trim(datos[2]);
      $('___forma_articulo').innerHTML=datos[5];
      $('___mostrar_stock').innerHTML=number_format(datos[3]*1,2,',','.');
      $('___stock').value=(datos[3]*1);
      $('___vence').value=(datos[4]);
      $('___media').value=(datos[6]);
      
      $('_buscar_codigo_interno').style.background='';
      
      if(__tipo_buscador==1)
      if($('___vence').value==1) {
        $('__fecha_venc_int').disabled=false;
        $('__fecha_venc_int').focus();
      } else {
        $('__fecha_venc_int').disabled=true;
        $('_cantidad').focus();
      
      }
      else $('_cantidad').focus();
      
      }
      
      } catch (err) {
      
        alert(err);
      
      }
      
    }
  }
  );

}


__buscar_codigo_prod_barra=function() {

  var myAjax8 = new Ajax.Request(
  'abastecimiento/buscador_codigos_reg.php',
  {
    method: 'get',
    parameters: $('_buscar_codigo_barra').serialize()+'&bodega_origen='+($('busca_cod_bar')._bod_id*1),
    onComplete: function(respuesta) {
      
      try {
      
      datos=eval(respuesta.responseText);
      
      if(!datos) {
      $('___art_id').value=0;
      $('___art_codigo').value='';
      $('___artc_codigo').value='';
      $('___art_glosa').value='';
      $('___stock').value=0;
      $('___vence').value=0;
      $('___media').value=0;
      
      $('__cod_bar').innerHTML='';
      $('__cod_int').innerHTML='';
      $('__glosa_prod').innerHTML='';
      $('__cant_prod').innerHTML='';
     $('___mostrar_stock').innerHTML='';
      
      $('_buscar_codigo_barra').style.background='yellow';
      $('_buscar_codigo_barra_bad').style.display='';
     
      $('_buscar_codigo_barra').select();
    
      } else {
      $('___art_id').value=(datos[0]*1);
      $('___art_codigo').value=datos[1].unescapeHTML();
      $('___artc_codigo').value=datos[3].unescapeHTML();
      $('___art_glosa').value=datos[2].unescapeHTML();
      $('___stock').value=(datos[5]*1);
      $('___vence').value=(datos[6]);
      $('___media').value=(datos[8]);
      
      $('__cod_bar').innerHTML=datos[3];
      $('__cod_int').innerHTML=datos[1];
      $('__glosa_prod').innerHTML=datos[2];
      $('__cant_prod').innerHTML=datos[4];
      $('___mostrar_stock').innerHTML=number_format(datos[5]*1,1,',','.');
      
      $('_buscar_codigo_barra').style.background='';
      $('_buscar_codigo_barra_bad').style.display='none';
     
      if(__confirma_fecha()) {
     
        __enviar_art_2((datos[4]*1));
      
        $('_buscar_codigo_barra').select();
      
      } else {
      
        alert('Art&iacute;culo perecible, debe especificar fecha de vencimiento.'.unescapeHTML());
        
        $('__fecha_venc').select();
      
      }
            
      }
      
      } catch (err) {
        alert(err);
      }
      
      
    }
  }
  );
  
}


__enviar_art_1=function(__cant) {

  if(!__confirma_fecha('__fecha_venc_int')) {
    
    alert('Art&iacute;culo perecible, debe especificar fecha de vencimiento.'.unescapeHTML());
        
    $('__fecha_venc_int').select();
    
    return;
  
  }

  if($('___art_id').value==0) {
    alert('No hay art&iacute;culos seleccionados.'.unescapeHTML());
    return;
  
  }
  
  if((__cant*1)==0 || isNaN(__cant*1)) {
    alert('Campo cantidad no fu&eacute; ingresado.'.unescapeHTML());
    return;
  }

  if(__tipo_buscador==0 || $('___vence').value==0) ___fecha_venc=null;
  else ___fecha_venc=$('__fecha_venc_int').value;
  
  
  envia_seleccion(
  $('___art_id').value,
  $('___art_codigo').value,
  $('___artc_codigo').value,
  $('___art_glosa').value,
  (__cant*1),
  $('___stock').value,
  ___fecha_venc,
  $('___media').value,
  $(___forma_articulo).innerHTML
  );
  
  $('___art_id').value=0;
  $('___art_codigo').value='';
  $('___artc_codigo').value='';
  $('___art_glosa').value='';
  $('___stock').value=0;
    
  
  $('_cantidad').value='';
  if(__tipo_buscador==1) $('__fecha_venc_int').value='';
  $('_buscar_codigo_interno').value='';
  $('_buscar_codigo_interno').focus();
  
  
}

__enviar_art_2=function(__cant) {

  if(!__confirma_fecha('__fecha_venc_bar')) {
    
    alert('Art&iacute;culo perecible, debe especificar fecha de vencimiento.'.unescapeHTML());
        
    $('__fecha_venc_bar').select();
    
    return;
  
  }

  if($('___art_id').value==0) {
    alert('No hay art&iacute;culos seleccionados.'.unescapeHTML());
    return;
  
  }
  
  if((__cant*1)==0 || isNaN(__cant*1)) {
    alert('Campo cantidad no fu&eacute; ingresado.'.unescapeHTML());
    return;
  }
  
  
  if(__tipo_buscador==0 || $('___vence').value==0) ___fecha_venc=null;
  else ___fecha_venc=$('__fecha_venc_bar').value;
  
  envia_seleccion(
  $('___art_id').value,
  $('___art_codigo').value,
  $('___artc_codigo').value,
  $('___art_glosa').value,
  (__cant*1),
  $('___stock').value,
  ___fecha_venc,
  $('___media').value,
  $('___forma_articulo').innerHTML
  );
  
}


ver_barras=function() {
      tab_up('tab_cod_barra');
      tab_down('tab_cod_int');
      tab_down('tab_cod_asocia');
      $('_buscar_codigo_barra').focus();
}

ver_interno=function() {
      tab_down('tab_cod_barra');
      tab_up('tab_cod_int');
      tab_down('tab_cod_asocia');
      $('_buscar_codigo_interno').focus();

}

ver_asociar=function() {
      tab_down('tab_cod_barra');
      tab_down('tab_cod_int');
      tab_up('tab_cod_asocia');
      $('_buscar_codigo_interno_asociar').focus();
}

guardar_asociacion = function() {

  if(trim($('_buscar_codigo_interno_asociar').value)=='' || trim($('_buscar_codigo_barras_asociar').value)=='') {
    alert('Alguno de los Campos de C&oacute;digos est&aacute; vac&iacute;o.'.unescapeHTML());
    return;
  
  }

  if(($('_buscar_cantidad_asociar').value*1)==0) {
    alert('Cantidad especificada no es v&aacute;lida.'.unescapeHTML());
    return;
  }

  var myAjax10 = new Ajax.Request(
  'abastecimiento/buscador_codigos_sql.php',
  {
    method: 'get',
    parameters: $('__asociacion').serialize(),
    onComplete: function(respuesta) {
      if(respuesta.responseText=='OK') {
        alert('Asociaci&oacute;n de C&oacute;digos realizada exitosamente.'.unescapeHTML());
        $('__asociacion').reset();
        $('_buscar_codigo_interno_asociar').focus();
      } else {
        alert('ERROR:\n\n'+respuesta.responseText.unescapeHTML());
      
      }
    }
  }
  );

}

envia_seleccion=function(   art_id, art_codigo, artc_codigo, 
                            art_glosa, art_cantidad, art_stock, art_vence, 
                            art_punit_med, forma_articulo) {
                            
      _callback_f = $('busca_cod_bar')._callback_fn;

    
        _callback_f(art_id, art_codigo, artc_codigo, art_glosa,
        art_cantidad, art_stock, art_vence, art_punit_med, forma_articulo);
      

}

cambia_codigo=function() {

  $('_buscar_codigo_barras_asociar').value=$('_buscar_codigo_barra').value;
  
  ver_asociar();
  
  $('_buscar_codigo_interno_asociar').select();

}

      autocompletar_medicamentos = new AutoComplete(
      '_buscar_codigo_interno', 
      'autocompletar_sql.php',
      function() {
        if($('_buscar_codigo_interno').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=<?php echo $buscador; ?>&codigo='+encodeURIComponent($('_buscar_codigo_interno').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, __buscar_codigo_prod_int);
      

</script>

<table style='width:100%;' cellpadding=0 cellspacing=0>
<tr><td>

<table cellpadding=0 cellspacing=0>
      <tr><td>
		  <div class='tabs' id='tab_cod_int' style='cursor: default;'
      onClick='ver_interno();'>
      <img src='iconos/book.png'>
      C&oacute;digo Interno</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_cod_barra' style='cursor: pointer;' 
      onClick='ver_barras();'>
      <img src='iconos/book_addresses.png'>
      C&oacute;digo de Barra</div>
		  </td><td>
		  <div class='tabs_fade' id='tab_cod_asocia' style='cursor: pointer;'
      onClick='ver_asociar();'>
      <img src='iconos/database_link.png'>
      Asociar C&oacute;digos</div>
		  </td></tr>
      </table>



</td></tr>
<tr><td>

<div class='tabbed_content' style='height: 158px; overflow:auto;'>

<div id='tab_cod_barra_content' style='display:none;'>

<table style='width:100%;'>
<tr><td style='text-align: right;'>
C&oacute;digo:
</td><td>
<input type='text' id='__fecha_venc_bar' name='__fecha_venc_bar' style='text-align: center;' onChange='__confirma_fecha(this);' onKeyUp='if(event.which==13) $("_buscar_codigo_barra").focus();' size=10>

<input type='text' id='_buscar_codigo_barra' name='_buscar_codigo_barra'
style='width:60%;' 
onKeyUp='
if(event.which==13) __buscar_codigo_prod_barra();
'> 
<img src='iconos/error_go.png' 
id='_buscar_codigo_barra_bad' style='display:none; cursor: pointer;'
onClick='cambia_codigo();'
alt='C&oacute;digo Incorrecto. Asociar a C&oacute;digo Interno.' title='C&oacute;digo Incorrecto. Asociar a C&oacute;digo Interno.'>
</td></tr>
<tr><td colspan=2>
<div id='_buscar_resultado' class='sub-content2' style='height:100px;'>

<table>
<tr><td style='text-align:right;'>C&oacute;digo de Barras:</td>
<td style='font-weight: bold;' id='__cod_bar'></td></tr>
<tr><td style='text-align:right;'>C&oacute;digo Interno:</td>
<td style='font-weight: bold;' id='__cod_int'></td></tr>
<tr><td style='text-align:right;'>Glosa:</td>
<td style='font-weight: bold;' id='__glosa_prod'></td></tr>
<tr><td style='text-align:right;'>Cantidad U.A.:</td>
<td style='font-weight: bold;' id='__cant_prod'></td></tr>
</table>

</div>
</td>
</table>

</div>





<div id='tab_cod_int_content' style=''>

<input type='hidden' id='___art_id' name='___art_id' value=0>
<input type='hidden' id='___art_codigo' name='___art_codigo' value=0>
<input type='hidden' id='___artc_codigo' name='___artc_codigo' value=0>
<input type='hidden' id='___art_glosa' name='___art_glosa' value=''>
<input type='hidden' id='___stock' name='___stock' value=0>
<input type='hidden' id='___vence' name='___vence' value=0>
<input type='hidden' id='___media' name='___media' value=0>

<table style='width:100%;' cellpadding=0 cellspacing=0>

<?php if($tipo_buscador==1) { ?>

<tr><td style='text-align: center;'>
C&oacute;digo:
</td><td style='text-align:center;<?php if($tipo_buscador==0) echo 'display:none;';?>'>
Fecha Venc.:
</td><td style='text-align: center;'>
Ud. Adm.:
</td>
</tr>
<tr><td style='text-align: center;'>
<input type='text' id='_buscar_codigo_interno' name='_buscar_codigo_interno'
size=15>
</td><td style='text-align:center;'>
<input type='text' id='__fecha_venc_int' name='__fecha_venc_int' style='text-align: center;' onChange='__confirma_fecha(this);' onKeyUp='if(event.which==13) $("_cantidad").focus();'>
</td>
<td style='text-align: center;'>
<input type='text' id='_cantidad' name='_cantidad' size=10
style='text-align: right;'
onKeyUp='
if(event.which==13) __enviar_art_1($("_cantidad").value);
'>

</td>
</tr>

<tr><td>&nbsp;</td></tr>
<tr>
<td colspan=3>
<center>
<table style='width: 400px; font-size: 12px; font-weight: bold;' cellspacing=2>
<tr>
<td style='text-align: center; width:75px;' id='___codigo_articulo'></td>
<td style='width: 250px;' id='___glosa_articulo'></td>
<td style='text-align: center; width:75px;' id='___forma_articulo'></td>
<td style='text-align: right; width:75px;' id='___mostrar_stock'></td>
</tr>
</table>
</center>

</td>
</tr>

<?php } else { ?>

<tr><td style='text-align: center;'>
C&oacute;digo:
</td><td style='text-align: center;'>
<input type='text' id='_buscar_codigo_interno' name='_buscar_codigo_interno'
size=15>
</td><td style='text-align: center;'>
Ud. Adm.:
</td>
<td style='text-align: center;'>
<input type='text' id='_cantidad' name='_cantidad' size=10
style='text-align: right;'
onKeyUp='
if(event.which==13) __enviar_art_1($("_cantidad").value);
'>

</td>
</tr>


<tr><td>&nbsp;</td></tr>
<tr>
<td colspan=4>
<center>
<table style='width: 400px; font-size: 12px; font-weight: bold;' cellspacing=2>
<tr>
<td style='text-align: center; width:75px;' id='___codigo_articulo'></td>
<td style='width: 250px;' id='___glosa_articulo'></td>
<td style='text-align: center; width:75px;' id='___forma_articulo'></td>
<td style='text-align: right; width:75px;' id='___mostrar_stock'></td>

</tr>
</table>
</center>

</td>
</tr>


<?php } ?>

<tr><td>&nbsp;</td></tr>

</table>

<center>
    <div class='boton'>
		<table><tr><td>
		<img src='iconos/link_go.png'>
		</td><td>
		<a href='#' onClick='__enviar_art_1($("_cantidad").value);'>
    Agregar Art&iacute;culos...</a>
		</td></tr></table>
		</div>
</center>

</div>





<div id='tab_cod_asocia_content' style='display:none;'>

<form id='__asociacion' name='__asociacion' onSubmit='return false;'>

<table style='width:100%;'>
<tr>
<td style='text-align: right;'>C&oacute;digo Interno:</td>
<td>
<input type='text' id='_buscar_codigo_interno_asociar' name='_buscar_codigo_interno_asociar'>
<img src='iconos/zoom_in.png' style='cursor: pointer;'
onClick='
    buscar_articulos("_buscar_codigo_interno_asociar", function() { return; } );
'>
</td>
</tr>
<tr>
<td style='text-align: right;'>C&oacute;digo de Barras:</td>
<td>
<input type='text' id='_buscar_codigo_barras_asociar' name='_buscar_codigo_barras_asociar' size=40>
</td>
</tr>
<tr>
<td style='text-align: right;'>Unidadades de Adm.:</td>
<td>
<input type='text' id='_buscar_cantidad_asociar' name='_buscar_cantidad_asociar' size=10 style='text-align: right;'>
</td>
</tr>
<tr>
<td style='text-align: right;'>Formato:</td>
<td>
<select id='_buscar_formato_asociar' name='_buscar_formato_asociar'>
<option value=0 SELECTED>Caja</option>
<option value=1>Display</option>
</select>
</td>
</tr>

</table>

</form>

<center>

    <table><tr><td>
		
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/link_add.png'>
		</td><td>
		<a href='#' onClick='guardar_asociacion();'>
    Realizar Asociaci&oacute;n...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/link_delete.png'>
		</td><td>
		<a href='#' onClick='$("__asociacion").reset();'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
		
		</td></tr></table>
    
    </center>


</div>

</div>



</td></tr></table>

<script> $('_buscar_codigo_barra').focus(); </script>
 
