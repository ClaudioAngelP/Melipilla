<?php

	require_once('../../conectar_db.php');

?>
<script>

cargar_listado=function() {

	$('listado').innerHTML='<center><br><br><img src="imagenes/ajax-loader3.gif" /><br>Cargando...</center>';

	var myAjax=new Ajax.Updater('listado','abastecimiento/articulos_farmacia/listado_farmacia.php', { method:'post',parameters:$('filtro').serialize() });

}

volver_atras=function() {

	$('listado').show();
	$('actualizar').show();
	$('filtro').show();
	$('sfiltro').show();
	$('producto').hide();
	$('volver_atras').hide();
	$('guardar').hide();

}

abrir_art=function(art_id) {

	$('listado').hide();
	$('actualizar').hide();
	$('filtro').hide();
	$('sfiltro').hide();
        $('producto').show();
        $('volver_atras').show();

        $('producto').innerHTML='<center><br><br><img src="imagenes/ajax-loader3.gif" /><br>Cargando...</center>';

        var myAjax=new Ajax.Updater('producto','abastecimiento/articulos_farmacia/editor_farmacia.php', { method:'post',evalScripts:true,parameters:'art_id='+art_id });

}

guardar_producto=function() {

	if(!confirm("&iquest;Desea guardar los cambios?".unescapeHTML())) return;

	var myAjax = new Ajax.Request('abastecimiento/articulos_farmacia/sql_farmacia.php',
		{method:'post',parameters:$('datos_art').serialize(),onComplete:function(r) { alert(r.responseText); }});

}

asignar_vademecum=function() {

 buscar_win = window.open('conectores/vademecum/form.php?'+$('art_id').serialize(),
                        'buscar_productos', 'left='+(screen.width-620)+',top='+(screen.height-370)+',width=600,height=350,status=0');

 buscar_win.focus();

}

ver_vademecum = function() {

      if($('id_vademecum').value=='') {alert('Producto no ha sido asociado con VMP Vademecum.');return;}

      params= $('id_vademecum').serialize();

      top=Math.round(screen.height/2)-300;
      left=Math.round(screen.width/2)-200;

      new_win =
      window.open('conectores/vademecum/visualizar_vademecum.php?'+
      params,
      'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=800, height=600, '+
      'top='+top+', left='+left);

      new_win.focus();

}

ver_vademecum2 = function(id_vademecum) {

      params='id_vademecum='+encodeURIComponent(id_vademecum);

      top=Math.round(screen.height/2)-300;
      left=Math.round(screen.width/2)-200;

      new_win =
      window.open('conectores/vademecum/visualizar_vademecum.php?'+
      params,
      'win_items', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=800, height=600, '+
      'top='+top+', left='+left);

      new_win.focus();

}

cargar_listado();

</script>

<center>
<div class='sub-content' style='width:90%;'>
<div class='sub-content'>
<img src='iconos/pill.png' />
<b>Mantenedor Farmacia</b>&nbsp;<span id='sfiltro'>Filtro:</span><input type='text' id='filtro' name='filtro' value='' size=40 onKeyUp='if(event.which==13) cargar_listado();' />&nbsp;<input type='button' id='actualizar' onClick='cargar_listado();' value='[Actualizar...]' />&nbsp;<input type='button' id='volver_atras' onClick='volver_atras();' value='[Volver Atr&aacute;s...]' style='display:none;' />&nbsp;&nbsp;
<input type='button' id='guardar' value='[ Guardar Cambios... ]' onClick='guardar_producto();' style='display:none;font-weight:bold;' />
</div>

<div class='sub-content2' id='listado' style='height:400px;overflow:auto;'>

</div>

<div class='sub-content2' id='producto' style='height:400px;overflow:auto;display:none;'>

</div>

</div>
</center>
