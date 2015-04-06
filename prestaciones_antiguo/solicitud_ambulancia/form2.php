<?php 

	require_once('../../conectar_db.php');
	
	

?>

<script>

	listado_sol=function() {
		
		var myAjax=new Ajax.Updater(
			'listado_sol',
			'prestaciones/solicitud_ambulancia/listado_solicitudes.php',
			{
				method:'post'
			}
		);
		
	}
	
	listado_sol();


</script>

<center>
<div class='sub-content' style='width:950px;'>

<div class='sub-content'>
<img src='iconos/building_go.png' />
<b>Gesti&oacute;n de Solicitudes de Traslado en Ambulancia</b>
</div>

<div class='sub-content2' style='height:400px;overflow:auto;' id='listado_sol' >

</div>

<center><input type='button' value='-- Guardar Modificaciones --' onClick='guardar_mods();' /></center>

</div>
</center>
