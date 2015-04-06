<?php 

	require_once('../../conectar_db.php');

?>

<script>

busqueda = function() {

	var params=$('nombre').serialize();
	
	var myAjax = new Ajax.Updater(
	'listado_clientes','ingresos/buscar_historial/listado_fallecidos.php', {
		method:'post', parameters: params	
	}	
	);

}

abrir_sepultura=function(us_id) {

	cambiar_pagina('ingresos/movimientos/form.php?us_id='+(us_id*1));
	
}

</script>

<center>

<div class='sub-content' style='width:900px;' id='busqueda'>

<div class='sub-content'>
<img src='iconos/book_open.png'>
<b>B&uacute;squeda de Usuarios Fallecidos</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>

<tr><td style='text-align:right;'>
Nombre:
</td><td>

<input type='text' size=40 id='nombre' name='nombre'
onKeyUp='if(event.which==13) busqueda();'>

<input type='button' id='buscar' onClick='busqueda();' 
value='Realizar B&uacute;squeda...'>

</td></tr>

</table>
</div>

<div class='sub-content2' id='listado_clientes' 
style='height:330px;overflow:auto;'>

</div>

</div>

<script> $('nombre').focus(); </script>
