<?php

  require_once('../../conectar_db.php');

  $modo_admin=isset($_GET['modo']);

?>

<script>

var adjuntos=new Array();

var pantallas=Array(  'eot_detalle',
                      'eot_eventos',
                      'eot_documentos',
                      'eot_repuestos'
                    );

ver_pantalla=function(v) {

  for(var i=0;i<pantallas.length;i++)
    if(pantallas[i]==v)
      $(pantallas[i]).setStyle({display:''});
    else
      $(pantallas[i]).setStyle({display:'none'});      

}

listar_eot = function() {

  <?php if($modo_admin) { ?>
  var params='modo=admin';
  <?php } else { ?>
  var params='';
  <?php } ?>
  
  var myAjax=new Ajax.Updater(
  'lista_eot',
  'equipos/listado_mantencion/listar_eot.php',
  {
    method:'post', parameters: params
  });

}

abrir_eot = function (eot_id) {

  var l=(screen.availWidth/2)-250;
  var t=(screen.availHeight/2)-200;
      
  win = window.open('equipos/visualizar_eot.php?eot_id='+eot_id, 'ver_eot',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=500, height=415');
                    
  win.focus();

}

cargar_eot = function (eot_id, estado) {
  
  $('eot').style.display='';
  $('listado').style.display='none';
  
  $('eot_id').value=eot_id;
  $('observaciones').value='';

  $('nro_orden').innerHTML='#'+eot_id;

  $('eot_detalle').innerHTML='<center><img src="imagenes/ajax-loader2.gif"><br><br>Cargando...</center>';

  var myAjax = new Ajax.Updater(
  'eot_detalle',
  'equipos/visualizar_eot.php',
  {
    method:'get', 
    evalScripts:true,
    parameters: 'eot_id='+eot_id+'&nowin=1'
  }
  );
  
  ver_pantalla("eot_detalle");
  
  adjuntos=Array();
  
  $('observaciones').value='';
  $('observaciones2').value='';
  $('tipodoc').value='1';
  
  generar_sestado(estado);
  lista_adjuntos();
  listar_oc();
  
}

generar_sestado = function(estado) {

  $('estado_actual').value=estado;

  var html="<select id='estado' name='estado' style='width:100%;'>";
  
  var o=new Array();
  
  o[0]="<option value='1'>Recepcionado en U.E.M.</option>";
  o[1]="<option value='2'>Trabajo Iniciado</option>";
  o[2]="<option value='3'>Trabajo T&eacute;rminado</option>";
  o[3]="<option value='4'>Entregado al Servicio</option>";
  o[4]="<option value='5'>Recepci&oacute;n Conforme del Servicio</option>";
  if(estado<3) o[9]="<option value='10'>En proveedor por garant&iacute;a.</option>";
  
  if(estado==10) estado=1;
  
  for(var i=estado;i<o.length;i++)
    html+=o[i];
  
  html+='</select>';
  
  $('sestado').innerHTML=html;

}

ver_listado = function() {
  $('eot').style.display='none';
  $('listado').style.display='';
}

adjuntar_archivo = function () {

  var l=(screen.availWidth/2)-150;
  var t=(screen.availHeight/2)-100;
      
  win = window.open('equipos/listado_mantencion/adjuntar_archivo.php', 'adjuntar',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=300, height=200');
                    
  win.focus();

}

agrega_adjunto = function(filename) {

  var n=adjuntos.length;
  adjuntos[n]=filename;

  lista_adjuntos();

}

lista_adjuntos = function() {

  var e=($('eot_eventos').style.display=='');
  
  var n; e?n=1:n=2;

  if(adjuntos.length>0) {
  
    html='<table style="width:100%;">';
    
    for(var i=0;i<adjuntos.length;i++) {
    
      var clase = (i%2==0)?'tabla_fila':'tabla_fila2';
    
      html+='<tr class="'+clase+'"><td>'+adjuntos[i]+'</td>';
      html+='<td style="text-align:center;width:30px;"><img src="iconos/delete.png" style="cursor:pointer;" ';
      html+='onClick="eliminar_adjunto('+i+');"></td></tr>';
    
    }
    
    html+='</table>';
  
  } else {
  
    html='<center><br><i>No hay archivos seleccionados...</i></center>';
  
  }

  
  $('lista_adjuntos1').innerHTML=html;
  $('lista_adjuntos2').innerHTML=html;


}

eliminar_adjunto = function(n) {

  adjuntos=adjuntos.without(adjuntos[n]);

  lista_adjuntos();

}

guardar_evento = function() {

  var params='&adjuntos='+encodeURIComponent(adjuntos.toJSON());

  var myAjax=new Ajax.Request(
  'equipos/listado_mantencion/sql_evento.php',
  {
    method:'post',
    parameters:$('evento').serialize()+params,
    onComplete:function(resp) {
      
      var r=resp.responseText.evalJSON(true);
      
      if(r) {

        if($('accion').value=='evento')
          alert('Cambio de estado de orden realizado exitosamente.'.unescapeHTML());
        else
          alert('Documento/Correspondencia archivado exitosamente.'.unescapeHTML());
        
        if($('estado').value==5) { 
          listar_eot();
          ver_listado();
          return;
        }
        
        cargar_eot($('eot_id').value, $('estado').value);

      } else {

        alert('ERROR:\n\n'+resp.responseText);

      }
      
    }
  });

}

cambiar_ingreso = function() {

  var e=($('agregar_eventos').style.display=='');
  
  if(e) {
    $('agregar_eventos').style.display='none';
    $('agregar_documentos').style.display='';
    $('btoggle').value='Agregar Eventos';
    $('accion').value='documento';
  } else {
    $('agregar_eventos').style.display='';
    $('agregar_documentos').style.display='none'; 
    $('btoggle').value='Adjuntar Documentos/Correspondencia'; 
    $('accion').value='evento';
  }
  
  lista_adjuntos();

}

listar_oc = function() {

    var myAjax=new Ajax.Updater(
    'lista_oc',
    'equipos/listado_mantencion/listar_ordenes.php',
    {
        method:'post',
        parameters:$('eot_id').serialize()
    });

}

asociar_oc = function() {

  var l=(screen.availWidth/2)-225;
  var t=(screen.availHeight/2)-150;
      
  win = window.open('equipos/listado_mantencion/asociar_oc.php?'+$('eot_id').serialize(), 
                    'asociar',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=450, height=300');
                    
  win.focus();


}

quitar_oc = function(orden_id) {

    params=$('eot_id').serialize()+'&orden_id='+orden_id+'&accion=eliminar';

    var myAjax=new Ajax.Request(
    'equipos/listado_mantencion/sql_orden.php',
    {
        method:'post', parameters:params,
        onComplete: function() {listar_oc();}
    }
    );

}


listar_eot();

</script>

<center>

<div class='sub-content' style='width:750px;' id='listado'>
<div class='sub-content'>
<img src="iconos/wrench.png">
<b>&Oacute;rdenes de Trabajo <?php if(!$modo_admin) { ?>Asignadas<?php } ?></b>
</div>

<div class='sub-content2' id='lista_eot' style='height:400px;overflow:auto;'>

</div>

</div>

<div class='sub-content' style='width:750px;display:none;' id='eot'>
<div class='sub-content'>
<img src="iconos/wrench_orange.png">
&Oacute;rden de Trabajo <span id='nro_orden' style='font-weight:bold;'></span>
</div>

<div id='datos_eot'>

<form id='evento' name='evento' onsubmit='return false;'>
<input type='hidden' id='accion' name='accion' value='evento'>

<table style='width:100%;'>
<tr>
<td id='eot_detalle' style='width:65%;'>

</td>

<td id='eot_eventos' style='width:65%;display:none;'>

<div class='sub-content'>
<img src='iconos/database_edit.png'>
Agregar <b>Eventos</b>
</div>

<div class='sub-content2' style='height:340px;'>

<input type='hidden' id='eot_id' name='eot_id' value=''>
<input type='hidden' id='estado_actual' name='estado_actual' value=''>

<table style='width:100%;'>
<tr class='tabla_header'><td>Modificar Estado Actual:</td></tr>
<tr>
<td style='text-align:center;' id='sestado'>

</td>
</tr>

<tr class='tabla_header'><td>Observaciones:</td></tr>
<tr>
<td style='text-align:center;'>
<textarea style='width:100%;height:70px;' 
id='observaciones' name='observaciones'></textarea>
</td>
</tr>

<tr class='tabla_header'><td>Adjuntar Archivos:</td></tr>
<tr><td>
<div id='lista_adjuntos1' 
style='border:1px solid black;height:70px;overflow:auto;'>

</div>
<center>
<input type='button' value='Agregar Archivo...'
onClick='adjuntar_archivo(1);'><br><br><br>
<input type='button' value='-- Ingresar Cambio de Estado --'
onClick='guardar_evento();'>
</center>
</td></tr>

</table>

</div>


</td>

<td id='eot_documentos' style='width:65%;display:none;'>


<div class='sub-content'>
<img src='iconos/database_edit.png'>
Agregar <b>Documentaci&oacute;n</b>
</div>

<div class='sub-content2' style='height:340px;'>

<table style='width:100%;'>
<tr class='tabla_header'><td>Tipo de Documento:</td></tr>
<tr>
<td style='text-align:center;'>
<select id='tipodoc' name='tipodoc' style='width:100%;'>
<option value=1>Correspondencia</option>
<option value=2>Orden de Salida</option>
<option value=3>Orden de Compra</option>
<option value=4>Factura/Boleta</option>
<option value=5>Cotizaci&oacute;n</option>
</select>
</td>
</tr>

<tr class='tabla_header'><td>Observaciones:</td></tr>
<tr>
<td style='text-align:center;'>
<textarea style='width:100%;height:70px;' 
id='observaciones2' name='observaciones2'></textarea>
</td>
</tr>

<tr class='tabla_header'><td>Adjuntar Archivos:</td></tr>
<tr><td>
<div id='lista_adjuntos2' 
style='border:1px solid black;height:70px;overflow:auto;'>

</div>
<center>
<input type='button' value='Agregar Archivo...'
onClick='adjuntar_archivo();'><br><br><br>
<input type='button' value='-- Ingresar Documento --'
onClick='guardar_evento();'>
</center>
</td></tr>

</table>


</div>



</td>

<td id='eot_repuestos' style='width:65%;display:none;'>

<div class='sub-content'>
<img src='iconos/database_edit.png'>
Compra de <b>Repuestos e Insumos</b>
</div>

<div class='sub-content2' style='height:340px;overflow:auto;'>

<center><br>
<input type='button' value='Asociar Orden de Compra...' onClick='asociar_oc();'><br>
</center>

<div id='lista_oc'>

</div>

</div>

</td>


<td valign="top">


<!----- Botones ----->

	<div class='boton' id='ot_boton' style=''>
	<table><tr><td>
	<img src='iconos/layout.png'>
	</td><td>
	<a href='#' onClick='ver_pantalla("eot_detalle");'>&Oacute;rden de Trabajo</a>
	</td></tr></table>
	</div>

  <?php if(!$modo_admin) { ?>

	<div class='boton' id='eventos_boton' style=''>
	<table><tr><td>
	<img src='iconos/hourglass.png'>
	</td><td>
	<a href='#' onClick='ver_pantalla("eot_eventos");$("accion").value="evento";'>Eventos</a>
	</td></tr></table>
	</div>
	
	<?php } ?>

	<div class='boton' id='documentos_boton' style=''>
	<table><tr><td>
	<img src='iconos/page_copy.png'>
	</td><td>
	<a href='#' onClick='ver_pantalla("eot_documentos");$("accion").value="documento";'>Documentos</a>
	</td></tr></table>
	</div>

	<div class='boton' id='repuestos_boton' style=''>
	<table><tr><td>
	<img src='iconos/package.png'>
	</td><td>
	<a href='#' onClick='ver_pantalla("eot_repuestos");'>Repuestos e Insumos</a>
	</td></tr></table>
	</div>

	<div class='boton' id='volver_boton' style=''>
	<table><tr><td>
	<img src='iconos/arrow_left.png'>
	</td><td>
	<a href='#' onClick='ver_listado();'>Volver Atr&aacute;s...</a>
	</td></tr></table>
	</div>


</td>
</tr>
</table>

</form>


</div>

</div>

</center>

