<?php

  require_once('../../conectar_db.php');
    
?>

<script>

listar_pacientes = function() {
  $('seleccion').value=$('filtro').value;
   $('fecha11').value=$('fecha1').value;
    $('fecha22').value=$('fecha2').value;
  $('mostrar_oc').style.display='none';
  
  var myAjax = new Ajax.Updater(
  'lista_pac',
  'ingresos/cierre_prestaciones/listar_pac.php',
  {
    method: 'post',
    parameters:$('fecha1').serialize()+'&'+$('fecha2').serialize()+'&filtro='+$('filtro').value
  }
  );

}



cerrar_prestaciones=function() {
	
	var myAjax=new Ajax.Request(
	'ingresos/cierre_prestaciones/sql.php',
	{
		method:'post',
		 parameters:$('fecha11').serialize()+'&'+$('fecha22').serialize()+'&filtro='+$('seleccion').value+'&ids='+$('ids').value,
		onComplete: function(resp) {
		
			try {		
		
				d=resp.responseText.evalJSON(true);
				//imprimir_boletin(d[1]);
			alert( 'Proceso Completado.');	
			cambiar_pagina('ingresos/cierre_prestaciones/form.php');
			
			} catch(err) {
			
				alert( 'ERROR:\n\n' + resp.responseText.unescapeHTML() );			
				//$('ingresa').disabled=false;	
			
			}
		
		}	
	}	
	);	  

	}
	
	
	
 	
    
    imprimir_oc=function(oc_id) {
	window.open('ingresos/cierre_prestaciones/imprimir_cerrados.php?oc_id='+oc_id,'_blank');
	}
	
	
	


 //**************************************************






</script>

<center>
<table style='width:950px;'>
<tr><td>
<div class='sub-content'>

<div class='sub-content'>
<img src='iconos/pill.png'> <b>Cierre de Prestaciones y Recetas</b>

<table style='width:950px;'>
<tr>
<td style='width:100px;text-align:right;'>Fecha Inicio:</td>
<td>
<input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'>
</td>
</tr>
<tr>
<td style='width:100px;text-align:right;'>Fecha Termino:</td>
<td>
<input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha2_boton'>
</td>
</tr>

<tr><td style='text-align:right;'>Filtro:</td>
<td><select id='filtro' name='filtro'>
<option value='10,12'>A y B</option>
<option value='12'>A</option>
<option value='10'>B</option>
<option value='11'>C</option>
<option value='15'>D</option>
<option value='6'>Particular</option>
<option value='5'>Isapre</option>
<option value='10,11,12,15,5,6'>Todos</option>
</select></td></tr>
<input type='hidden' name='seleccion' id='seleccion' value='' />
<input type='hidden' name='fecha11' id='fecha11' value='' />
<input type='hidden' name='fecha22' id='fecha22' value='' />

<tr>
<td>
<center>
<input type='button' onClick='listar_pacientes();' value='Visualizar...'>
</center>
</td>
</tr>

</table>

</div>


<div class='sub-content' id="mostrar_oc">

<center>


<form id='datos_oc' name='datos_convenio' onSubmit='return false;'>



</form>

</center>


</div>

<div id='listado_pac'>

<div class='sub-content2' id='lista_pac'
style='overflow: auto; height: 350px;'>



</div>
<table style='width:950px;'>
<center>
	<tr>
<td>

<input type='button' onClick='cerrar_prestaciones();' value='Cerrar Prestaciones y Recetas...'>

</td>
</tr>
</center>
</table>
</div>


</div>

</td>


</tr>


</table>
</center>

