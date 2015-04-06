<?php

  require_once('../../conectar_db.php');

  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(60).')', 'ORDER BY bod_glosa'); 
  
  $servs="'".str_replace(',','\',\'',_cav2(60))."'";

  $servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))>=2 AND
          centro_ruta IN (".$servs.")
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 


?>


<script>

    var articulos = new Array();
    var archivos = new Array();
    
    seleccionar_articulo = function (art) {
    
      $('art_id').value=art[5];
      $('art_codigo').value=art[0];
      $('art_nombre').innerHTML=art[2];
      
      $('cantidad').value='0';
      $('cantidad').select();
      $('cantidad').focus();
      
   	}
   	
   	agregar_articulo = function () {
    
      if(($('cantidad').value*1)==0) {
      
        alert('Debe especificar una cantidad v&aacute;lida.'.unescapeHTML());
        return;
        
      }
    
      encontrado=false;
      
      // Si el articulo ya fué ingresado se retira.
      for(var i=0;i<articulos.length;i++) 
        if(articulos[i].id==($('art_id').value*1)) { encontrado=true; break; }
      
      if(!encontrado) {
        num = articulos.length;
        articulos[num] = new Object();
      } else {
        num=i;
      }
      
      articulos[num].id=$('art_id').value;
      articulos[num].codigo=$('art_codigo').value;
      articulos[num].glosa=$('art_nombre').innerHTML;
      articulos[num].cantidad=$('cantidad').value*1;
      
      $('cantidad').value='0';
      $('codigo').select();
      $('codigo').focus();
      
      redibujar();

		}
		
		remover = function (art) {
    
      articulos = articulos.without(articulos[art]);
      redibujar();
    
    }


    redibujar = function() {
    
      html='<table style="width:100%;"><tr class="tabla_header">';
      html+='<td>C&oacute;digo Int.</td><td>Glosa</td>';
      html+='<td>Cantidad</td>';
      html+='<td>Acciones</td></tr>';
    
      for(var i=0;i<articulos.length;i++) {
      
        temp_html='';
        temp_html2='';

        if((i%2)==0) clase='tabla_fila'; else clase='tabla_fila2';
        
        html+='<tr class="'+clase+'">';
        html+='<td style="text-align:right;">';
        html+=articulos[i].codigo+'</td>';
        html+='<td>'+articulos[i].glosa+'</td>';
        
        html+='<td style="text-align:right;">'+number_format(articulos[i].cantidad,2)+'</td>';
        
        html+='<td><center>';
        html+='<img src="iconos/delete.png" ';
        html+='style="cursor:pointer;" onClick="remover('+i+');">';
        html+='</center></td>';
        html+='</tr>';
        
      }
      
      html+='</table>';
      
      $('seleccion').innerHTML=html;
    
    }
    
    
    verifica_tabla=function() {
    
      if(articulos.length==0 && $('tipo').value==0) {
      
        alert('No hay art&iacute;culos seleccionados.'.unescapeHTML());
        return;
      
      }
      
      $('item_codigo').disabled=false;
      
      var params='articulos='+encodeURIComponent(articulos.toJSON())+'&';
      params+='archivos='+encodeURIComponent(archivos.toJSON());
      
      var myAjax=new Ajax.Request(
      'abastecimiento/solicitudes_compra/sql.php',
      {
        method:'post',
        parameters:$('solicitud').serialize()+'&'+params,
        onComplete: function(resp) {
          r = resp.responseText.evalJSON(true);
          
          alert('Solicitud de compra ingresada exitosamente');
        }
      }
      );

      $('item_codigo').disabled=true;
    
    }

		autocompletar_medicamentos = new AutoComplete(
      'codigo', 
      'autocompletar_sql.php',
      function() {
        if($('codigo').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_arts&'+$('codigo').serialize()
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_articulo);


		seleccionar_item=function(d) {
			$('item_codigo').value=d[0];
			$('item_glosa').value=d[2].unescapeHTML();	
		}

		autocompletar_items = new AutoComplete(
      'item_glosa', 
      'autocompletar_sql.php',
      function() {
        if($('item_glosa').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=buscar_items&cadena='+encodeURIComponent($('item_glosa').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_item);


      redibujar();
      
      
    seleccionar_archivo = function() {
    
      top=Math.round(screen.height/2)-100;
      left=Math.round(screen.width/2)-225;
      
      new_win = 
      window.open('abastecimiento/solicitudes_compra/seleccionar_archivo.php',
      'win_foto', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=450, height=200, '+
      'top='+top+', left='+left);
      
      new_win.focus();
   		
		}
		
    redibujar_archivos = function() {
    
      var html='<table style="width:100%;">';
      html+='<tr class="tabla_header"><td>Nombre</td>';
      html+='<td style="width:100px;">Tama&ntilde;o</td>';
      html+='<td style="width:40px;">Acciones</td></tr>';
      
      for(var i=0;i<archivos.length;i++) {
      
        (i%2==0) ? clase='tabla_fila' : clase='tabla_fila2';
        
        var ext=archivos[i].nombre.split('.');
        ext=ext[ext.length-1];
      
        html+='<tr class="'+clase+'"><td>'+archivos[i].nombre+'</td>';
        html+='<td style="text-align:right;">'
        html+=number_format(archivos[i].tam/1024,1)+' KB</td>';
        html+='<center>';
        html+='<img src="iconos/delete.png" ';
        html+='style="cursor:pointer;" onClick="remover_archivo('+i+');">';
        html+='</center>';
      
      }
      
      html+='</table>';
      
      $('archivos').innerHTML=html;
    
    }

		agregar_archivo = function(url, nombre, tam) {
      
      var num=archivos.length;
      archivos[num]=new Object;
      archivos[num].url=url;
      archivos[num].nombre=nombre;
      archivos[num].tam=tam;
      
      redibujar_archivos();
      
    }

		remover_archivo = function (n) {
    
      archivos = archivos.without(archivos[n]);
      redibujar_archivos();
    
    }


	fix_form=function() {

		if($('tipo').value==0) {
		  $("detalle_articulos").style.display="";
		  $("archivos_adjuntos").style.display="none";
		  $("referente_tr").style.display="none";
		  $("precio_tr").style.display="none";
		  $("item_tr").style.display="none";
		} else {
		  $("detalle_articulos").style.display="none";
		  $("archivos_adjuntos").style.display="";
		  $("referente_tr").style.display="";
		  $("precio_tr").style.display="";
		  $("item_tr").style.display="";
		}

		
	}		

</script>

<center>

<form id='solicitud' name='solicitud' onSubmit='return false;'>

<div class='sub-content' style='width:650px;'>
<div class='sub-content'>
<img src='iconos/basket.png'>
<b>Solicitud de Compra de Art&iacute;culos</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='text-align:right;'>
Ubicaci&oacute;n:
</td>
<td>
<select id='bodega_id' name='bodega_id'>
<?php echo $bodegashtml; ?>
<?php echo $servicioshtml; ?>
</select>
</td>
</tr>

<tr>
<td style='text-align:right;'>
Tipo:
</td>
<td>
<select id='tipo' name='tipo' onClick='
	fix_form();
'>
<option value=0>Reposici&oacute;n de Art&iacute;culos.</option>
<option value=1>Art&iacute;culos nuevos.</option>
</select>

<input type='checkbox' id='urgente' name='urgente'> Urgente
</td>
</tr>

<tr id='referente_tr'>
<td style='text-align:right;' valign="top">
Referente T&eacute;cnico:
</td>
<td>
<input id='referente_tecnico' name='referente_tecnico' style='width:80%;' />
</td>
</tr>

<tr id='precio_tr'>
<td style='text-align:right;' valign="top">
Precio Referencial (Unitario) $:
</td>
<td>
<input id='precio_referencial' name='precio_referencial' style='width:40%;' />
</td>
</tr>

<tr id='item_tr' style='display:none;'>
<td style='text-align:right;' valign="top">
Item Presupuestario:
</td>
<td>
<input type='text' style='text-align:center;font-size:12px;' 
id='item_codigo' name='item_codigo' size=10 DISABLED />
<input type='text' id='item_glosa' name='item_glosa' size=30 />
</td>
</tr>

<tr>
<td style='text-align:right;' valign="top">
Fecha Estimada para Uso:
</td>
<td>
<input id='fecha_uso' name='fecha_uso' style='width:40%;' />
</td>
</tr>

<tr>
<td style='text-align:right;' valign="top">
Justificaci&oacute;n:
</td>
<td>
<textarea id='observaciones' name='observaciones'
style='width:100%;height:60px;'></textarea>
</td>
</tr>



</table>
</div>

<span id='detalle_articulos'>

<div class='sub-content'>
<img src='iconos/basket_put.png'>
<b>Detalle de Art&iacute;culos</b>
</div>

<center>
  
  <div class="sub-content">
  
  <center>
  <table><tr><td>
  <input type='hidden' id='art_id' name='art_id' value=0>
  <input type='hidden' id='art_codigo' name='art_codigo' value=''>
  
  <input type='text' id='codigo' name='codigo' size=15>
  </td><td style="width:300px;" id="art_nombre">
  (Seleccione Art&iacute;culos...)
  </td><td style="width:150px;text-align:right;" id="art_stock">
  <input type='text' id='cantidad' name='cantidad' 
  style='text-align:right;' value='0' size=10
  onKeyUp='if(event.which==13) agregar_articulo();'>
  </td>
  <td>
  <input type='button' value='Agregar...' onClick='agregar_articulo();'>
  </td>
  </tr></table>
  </center>
  
  </div>
  
</center>
<div class='sub-content2' style='height:200px;overflow:auto;' id='seleccion'>

</div>

</span>

<span id='archivos_adjuntos' style='display:none;'>

<div class='sub-content'>
<img src='iconos/attach.png'>
<b>Documentaci&oacute;n Anexa a la Solicitud</b>
<input type='button' value='Adjuntar Archivo...' onClick='seleccionar_archivo();'>
</div>

<div class='sub-content2' style='height:200px;overflow:auto;' id='archivos'>

</div>


</span>

  <center>
  <table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/basket_go.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla();'> Generar Solicitud...</a>
		</td></tr></table>
		</div>
		</td></tr></table>
  </center>

</form>

</center>

<script> fix_form(); </script>