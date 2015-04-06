<?php

  require_once('../../conectar_db.php');

?>

<script>

listar_eot = function() {

  
  var myAjax=new Ajax.Updater(
  'lista_eot',
  'equipos/asignar_equipos/listar_eot.php',
  {
    method:'post', parameters: $('filtro').serialize()
  });

}

asignar_eot = function(eot_id) {

    var l=(screen.width/2)-250;
    var t=(screen.height/2)-250;
  
    var eot = window.open('equipos/asignar_equipos/seleccionar_tecnico.php?eot_id='+eot_id,
		'ot', 'left='+l+',top='+t+',width=480,height=290,status=0,scrollbars=1');
			
		eot.focus();

}

asignar_prev = function(eagenda_id) {

    var l=(screen.width/2)-250;
    var t=(screen.height/2)-250;
  
    var eot = window.open('equipos/asignar_equipos/seleccionar_tecnico.php?eagenda_id='+eagenda_id,
		'ot', 'left='+l+',top='+t+',width=480,height=260,status=0,scrollbars=1');
			
		eot.focus();

}

abrir_eot = function (eot_id) {

  var l=(screen.availWidth/2)-250;
  var t=(screen.availHeight/2)-200;
      
  win = window.open('equipos/visualizar_eot.php?eot_id='+eot_id, 'ver_eot',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=500, height=415');
                    
  win.focus();

}

listar_eot();

</script>

<center>

<div class='sub-content' style='width:750px;' id='listado'>
<div class='sub-content'>
<img src="iconos/wrench.png">
<b>Asignaci&oacute;n de &Oacute;rdenes de Trabajo</b>
</div>

<div class='sub-contente'>

<form id='filtro' name='filtro' onSubmit='return false;'>
<table style='width:100%;'>
<tr><td style='text-align:right;'>Estado:</td>
<td>
<select id='estado' name='estado'>
<option value='-2'>(Todos los estados...)</option>
<option value='-1' SELECTED>Esperando Asignaci&oacute;n / Mant. Preventivas</option>
<option value='0'>Esperando Recepci&oacute;n</option>
<option value='1'>Recepcionado en U.E.M.</option>
<option value='2'>Trabajo Iniciado</option>
<option value='3'>Trabajo T&eacute;rminado</option>
<option value='4'>Entregado al Servicio</option>
<option value='5'>Recepci&oacute;n Conforme del Servicio</option>
<option value='10'>En proveedor por garant&iacute;a.</option>
</select>
</td></tr>

<tr><td colspan=2>
<center>
<input type='button' onClick='listar_eot();' 
value='Actualizar Listado...'>
</center>
</td></tr>
</table>

</form>
</div>

<div class='sub-content2' id='lista_eot' style='height:300px;overflow:auto;'>

</div>

</div>

<div class='sub-content' style='width:750px;display:none;' id='eot'>
<div class='sub-content'>
<img src="iconos/wrench.png">
<b>Asignaci&oacute;n de &Oacute;rden de Trabajo</b>
</div>

<div id='datos_eot' style='height:300px;overflow:auto;'>

</div>

</div>

</center>

