<?php 

	require_once('../../conectar_db.php');	

?>

<script>


listar_tareas=function() {
	
	var myAjax=new Ajax.Updater(
		'lista_tareas',
		'administracion/control_tareas/listado_tareas.php',
		{
			method: 'post'
		}
	);
	
	
}

editar_tarea = function(t_id) {
	
		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('administracion/control_tareas/editor_tareas.php?t_id='+t_id,'ver_turno',
							'width=800, height=600, toolbar=false, scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
	
}

listar_tareas();


</script>

<center>
<div class='sub-content' style='width:750px;'>
<div class='sub-content'>
<img src='iconos/clock.png'>
<b>Tareas Pendientes</b>
</div>

<div class='sub-content2' style='height:400px;overflow:auto;' id='lista_tareas'>


</div>


</div>

</center>
