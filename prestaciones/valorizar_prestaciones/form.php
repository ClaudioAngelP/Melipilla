<?php 



?>

<script>

	listar_pacientes=function() {
	
		$('lista_pacientes').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando...';	
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/valorizar_prestaciones/listar_pacientes.php',
			{
				method:'post',
				parameters:$('fecha1').serialize()	
			}		
		);	
		
	}

	abrir_pac=function(pac_id) {
	
		$('lista_pacientes').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando...';	
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/valorizar_prestaciones/detalle_paciente.php',
			{
				method:'post',
				evalScripts:true,
				parameters:$('fecha1').serialize()+'&pac_id='+pac_id	
			}		
		);	
		
	}
	
	lista_prestaciones=function(pac_id, prev_id) {
		
		$('lista_prestaciones').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando...';	
	
		var myAjax=new Ajax.Updater(
			'lista_prestaciones',
			'prestaciones/valorizar_prestaciones/lista_prestaciones.php',
			{
				method:'post',
				parameters:$('fecha1').serialize()+'&pac_id='+pac_id+'&prev_id='+prev_id	
			}		
		);	
				
			
	}


	actualizar_fecha=function() {

      top=Math.round(screen.height/2)-165;
      new_win = 
      new_win.focus();
	
	}


</script>

<center>
<div class='sub-content' style='width:850px;'>
<div class='sub-content'>
<img src='iconos/money.png' />
<b>Validaci&oacute;n de Previsi&oacute;n de Prestaciones</b>
</div>

<div class='sub-content' id='buscar_nominas'>
<table style='width:100%;'>
<tr>
  <input type='button' value='Actualizar Fecha...' 
  onClick='actualizar_fecha();' style='font-size:10px;' />
</table>

</div>

<div class='sub-content2' id='lista_pacientes' style='height:350px;overflow:auto;'>


</div>

</div>
</center>

<script> 

    Calendar.setup({
    });
	listar_pacientes(); 

</script>