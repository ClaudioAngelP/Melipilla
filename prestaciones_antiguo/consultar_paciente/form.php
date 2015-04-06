<?php 

	require_once('../../conectar_db.php');

?>

<script>

buscar=function() {
	
	if($('tipo').value==0) {
		$('busca').value=$('busca').value.toUpperCase();
			if(!comprobar_rut($('busca').value)) {
				$('busca').style.background='red';
				alert('R.U.T. incorrecto.');
				$('busca').select(); $('busca').focus();
				return;				
			} 
	}
		
	$('busca').style.background='';

	var myAjax=new Ajax.Updater(
		'lista_presta',
		'prestaciones/consultar_paciente/listar_prestaciones.php',
		{
			method:'post',
			evalScripts:true,
			parameters:$('tipo').serialize()+'&'+$('busca').serialize()	
		}
	);
	
}

</script>

<center>

<div class='sub-content' style='width:950px;'>

<div class='sub-content'>
<img src='iconos/user.png' />
<b>Consultas por Paciente</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr>
<td style='text-align:right;'>Buscar:</td>
<td><center>
<select id='tipo' name='tipo'>
<option value='1' SELECTED>Nro. de Ficha</option>
<option value='0'>RUT</option>
<option value='2'>Nro. de N&oacute;mina</option>
</select></center>
</td>
<td>
<input type='text' id='busca' name='busca' size=40 />
</td>
<td style='width:40%;'><input type='button' value='Realizar B&uacute;squeda...'
onClick='buscar();' /></td>
</table>
</div>

<div class='sub-content2' id='lista_presta' 
style='height:300px;overflow:auto;'>


</div>

</div>

</center>
