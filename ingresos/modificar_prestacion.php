<?php 


	require_once('../conectar_db.php');

	$p=json_decode($_GET['p']);
	
?>

<html>
<title>Edici&oacute;n Manual de Prestaciones</title>

<?php cabecera_popup('..'); ?>

<style>

.cheq {
	font-size:11px;
	width:100%;
}

</style>
 <script src="codificarmd5.js" type="text/javascript"></script>
<script>

var n=<?php echo $_GET['n']*1; ?>;

function init() {

	$('modalidad').value=window.opener.prestaciones[n].modalidad.unescapeHTML();
	$('codigo').value=window.opener.prestaciones[n].codigo.unescapeHTML();
	$('glosa').value=window.opener.prestaciones[n].glosa.unescapeHTML();
	$('cantidad').value=window.opener.prestaciones[n].cantidad;
	$('precio').value=window.opener.prestaciones[n].precio;
	$('copago').value=window.opener.prestaciones[n].copago;

	$('copago').select();
	$('copago').focus();
	
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
						
						if(!confirm('&iquest;Desea guardar los cambios a la prestaci&oacute;n?'.unescapeHTML()))
							return;
					
						
						window.opener.prestaciones[n].modalidad=$('modalidad').value;
						window.opener.prestaciones[n].codigo=escapeHTML($('codigo').value);
						window.opener.prestaciones[n].glosa=escapeHTML($('glosa').value);
						window.opener.prestaciones[n].cantidad=$('cantidad').value*1;
						window.opener.prestaciones[n].precio=$('precio').value*1;
						window.opener.prestaciones[n].copago=$('copago').value*1;
						window.opener.prestaciones[n].cobro='S';
						
						var fn=window.opener.redibujar_tabla.bind(window.opener);
						fn();
						
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
<b>Edici&oacute;n Manual de Prestaciones</b>
</div>

<form id='form_presta' name='form_presta' onSubmit='return false;'>

<div class='sub-content2' id='lista_seguros'>

<table style='width:100%;'>
<tr><td style='text-align:right;'>Modalidad:</td>
<td><select id='modalidad' name='modalidad'>
<option value='mai'>INSTITUCIONAL</option>
<option value='mle'>PARTICULAR</option>
</select></td></tr>
<tr><td style='text-align:right;'>C&oacute;digo:</td>
<td><input type='text' id='codigo' name='codigo' value='<?php echo $p['codigo']; ?>' /></td></tr>
<tr><td style='text-align:right;'>Glosa:</td>
<td><input type='text' id='glosa' name='glosa' value='<?php echo $p['glosa']; ?>' size=50 /></td></tr>
<tr><td style='text-align:right;'>Cantidad:</td>
<td><input type='text' id='cantidad' name='cantidad' value='<?php echo $p['precio']; ?>' style='text-align:right;' size=5 /></td></tr>
<tr><td style='text-align:right;'>Valor $:</td>
<td><input type='text' id='precio' name='precio' value='<?php echo $p['precio']; ?>' style='text-align:right;font-size:20px;' size=10 /></td></tr>
<tr><td style='text-align:right;'>Copago $:</td>
<td><input type='text' id='copago' name='copago' value='<?php echo $p['copago']; ?>' style='text-align:right;font-size:20px;' size=10 /></td></tr>


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





