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
    parameters:$('fecha1').serialize()+'&'+$('fecha2').serialize()+'&filtro='+$('filtro').value,
    onComplete: function(resp) {
		
		
   try {		
		
			
			//	imprimir_boletin(d[1]);
			ver_tabla();
			} catch(err) {
			
				
			
			}
		}
    
  }
  );
  
  
	
}


ver_historial = function() {
  var myAjax = new Ajax.Updater(
  'lista_pac',
  'ingresos/cierre_prestaciones/listar_historial.php',
  {
    method: 'post',
    onComplete: function(resp) {
		
		
   try {		
		
			
			//	imprimir_boletin(d[1]);
			ver_tabla();
			} catch(err) {
			
				
			
			}
		}
    
  }
  );
  
  
	
}

ver_tabla=function(){
	
	if($('ids').value=='')
		{
			alert('No hay datos para la busqueda indicada.');
			$('btn_cierra').disabled=true;
			
		}else{
			$('btn_cierra').disabled=false;
		}
}


cerrar_prestaciones=function() {
	
	$('btn_cierra').disabled=true;
	var myAjax=new Ajax.Request(
	'ingresos/cierre_prestaciones/sql.php',
	{
		method:'post',
		 parameters:$('fecha11').serialize()+'&'+$('fecha22').serialize()+'&filtro='+$('seleccion').value+'&nominas='+$('ids').value,
		onComplete: function(resp) {
		
							d=resp.responseText.evalJSON(true);
							
			if(d[0]==true) {
			imprimir_cerrados(d[1]);
			alert( 'Proceso Completado.');	
			listar_pacientes();
			
			
			} else {
			
				alert( 'ERROR:\n\n' + resp.responseText.unescapeHTML() );			
				//$('ingresa').disabled=false;	
				$('btn_cierra').disabled=true;
			
			}
		
		}	
	}	
	);	  

	}
	
	
	
 	
    
	
	
	


 //**************************************************
imprimir_cerrados=function(id) {

	 window.open('ingresos/cierre_prestaciones/imprimir_cerrados_historial.php?id='+id,'_blank');
    win.focus();

	}

 Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'

    });

Calendar.setup({
        inputField     :    'fecha2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha2_boton'

    });
    
    
    xls_busqueda = function() {

		    	
    	var __ventana = window.open('ingresos/cierre_prestaciones/listar_xls.php?xls&'+$("fecha1").serialize()+'&'
    	+$("fecha2").serialize()+'&filtro='+$("filtro").value, '_self');
  }
</script>

<center>
<table style='width:950px;'>
<tr><td>
<div class='sub-content'>

<div class='sub-content'>

<table style='width:950px;'>
<td style='text-align:left;'><img src='iconos/pill.png'> <b>Cierre de Prestaciones y Recetas</b> </td>
	<td style='text-align:right;'><input type='button' onClick='ver_historial();' value='Historial...'></td></table>


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
<option value='1,2'>A y B</option>
<option value='1'>A</option>
<option value='2'>B</option>
<option value='3'>C</option>
<option value='4'>D</option>
<option value='6'>Particular</option>
<option value='5'>Isapre</option>
<option value='1,2,3,4,5,6'>Todos</option>
</select></td></tr>
<input type='hidden' name='seleccion' id='seleccion' value='' />
<input type='hidden' name='fecha11' id='fecha11' value='' />
<input type='hidden' name='fecha22' id='fecha22' value='' />

<tr>
<td>
<center>
<input type='button' onClick='listar_pacientes();' value='Visualizar...'>
</center>
<div class='boton'>
								<table><tr><td>
								<img src='iconos/page_excel.png'>
								</td><td>
								<a href='#' onClick='xls_busqueda();'><span id='texto_boton'>Descargar XLS (MS Excel)...</span></a>
								</td></tr></table>
								</div>
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

<input type='button' id='btn_cierra' name='btn_cierra'  onClick='cerrar_prestaciones();' DISABLED value='Cerrar Prestaciones y Recetas...'>

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

