<?php 


	require_once('../conectar_db.php');

	$p=json_decode($_GET['p']);
	
?>

<html>
<title>Edici&oacute;n Manual de Previsi&oacute;n</title>

<?php cabecera_popup('..'); ?>

<style>

.cheq {
	font-size:11px;
	width:100%;
}

</style>
 <script src="codificarmd5.js" type="text/javascript"></script>
<script>

var n=<?php echo $_GET['prev_id']*1; ?>;

function init() {

	$('prevision').value=n;
	

}

var escape = document.createElement('textarea');

function escapeHTML(html) {
    escape.innerHTML = html;
    return escape.innerHTML;
}

function guardar() {
	
	passconfirm = $('passconfirm').value;
	var myAjax=new Ajax.Request(
			'comprobar_autorizacion.php',
			{
				method:'post',
				parameters:'passconfirm='+passconfirm,
				onComplete:function(r) {
				
					var r =r.responseText;
					 
					 if(r != 1 ){
					 	 alert('Usted no esta autorizado a guardar los cambios');
					 	 return;
					}else{
						
						if(!confirm('&iquest;Desea guardar los cambios a la previsi&oacute;n?'.unescapeHTML()))
							return;
					
						//window.opener.prestaciones[n].modalidad=$('modalidad').value;
						var fn=window.opener.actualizar_prev_id.bind(window.opener);
						var e = document.getElementById("prevision");
						var strUser = e.options[e.selectedIndex].text;
						fn($('prevision').value,strUser);
						
						window.close();
						
					} 	 
					 
				}
			}
		);			
}

</script>
 <body class='popup_background fuente_por_defecto' onLoad='init();'>

<div class='sub-content'>
<img src='../iconos/table_edit.png' />
<b>Edici&oacute;n Manual de Previsi&oacute;n</b>
</div>

<form id='form_presta' name='form_presta' onSubmit='return false;'>

<div class='sub-content2' id='lista_seguros'>

<table style='width:100%;'>
<tr><td style='text-align:right;'>Previsi&oacute;n:</td>
<td><select id='prevision' name='prevision'>
<option value='1'>GRUPO-A</option>
<option value='2'>GRUPO-B</option>
<option value='3'>GRUPO-C</option>
<option value='4'>GRUPO-D</option>
<option value='5'>ISAPRE</option>
<option value='6'>BLOQUEADO</option>

</select></td></tr>


</table>

</div>


<center>


Autorizaci&oacute;n
<input type="password" name="passconfirm" id="passconfirm" />
<input type='button' onClick='guardar();' id='confirmar' 
value='--- Guardar Cambios... ---'>
</center>

</body>
</html>





