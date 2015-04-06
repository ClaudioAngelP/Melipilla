<?php 

	require_once('../../conectar_db.php');


  $htmlfuncionarios = desplegar_opciones_sql( 
  "SELECT DISTINCT func_id, (func_nombre) 
  FROM funcionario 
  JOIN func_acceso USING (func_id) 
  WHERE permiso_id=2 ORDER BY (func_nombre)", 
  NULL, '', "font-style:italic;color:#555555;"); 

?>

<script>

<?php if(!isset($_GET['morosidad'])) { ?>

busqueda = function() {

	var params=$('nombre').serialize();
	
	var myAjax = new Ajax.Updater(
	'listado_clientes','creditos/busqueda/listado_clientes.php', {
		method:'post', parameters: params	
	}	
	);

}

<?php } else { ?>

busqueda = function(v) {

	if(v==0) {	
	
		var params=$('dias').serialize()+'&'+$('orden').serialize()+'&'+$('signo').serialize()+'&'+$('comp').serialize()+'&'+$('funcionarios').serialize();		
		
		$('listado_clientes').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando Informaci&oacute;n...';
		
		var myAjax = new Ajax.Updater(
		'listado_clientes','creditos/busqueda/listado_morosos.php', {
			method: 'post',parameters: params
		});
	
	} else {

		$('filtro').submit();
	
	}

}

<?php } ?>

abrir_credito=function(crecod, ruta) {

	var params='crecod='+crecod;
	
	if(ruta==null) ruta='';
	
    l=(screen.availWidth/2)-300;
    t=(screen.availHeight/2)-250;
        
    win = window.open(ruta+'creditos/visualizar_credito.php?'+params, 
                    '_ver_credito',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=600, height=500');
                    
    win.focus();


}

abrir_boletin=function(bolnum, ruta) {

	var params='bolnum='+bolnum;
	
    l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-200;
        
    win = window.open(ruta+'creditos/visualizar_boletin.php?'+params, 
                    '_ver_boletin',
                    'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=415');
                    
    win.focus();


}




abrir_cliente = function(id) {

	var params='pac_id='+id;
	
	var myAjax=new Ajax.Updater(
	'datos_cliente', 'ingresos/datos_cliente.php', {
		method: 'post', parameters: params
	});

	
	var myAjax=new Ajax.Updater(
	'creditos_cliente', 'creditos/credito_cliente.php', {
		method: 'post', parameters: params
	});

	$('busqueda').style.display='none';
	$('cliente').style.display='';	

}

volver_listado = function() {

	$('busqueda').style.display='';
	$('cliente').style.display='none';	

}

generar_notificacion = function() {

	$('filtro').action='creditos/busqueda/notificaciones.php';
	$('filtro').method='post';
	$('filtro').submit();
	
}

</script>

<center>

<?php if(!isset($_GET['morosidad'])) { ?>

<div class='sub-content' style='width:850px;' id='busqueda'>

<div class='sub-content'>
<img src='iconos/book_open.png'>
<b>B&uacute;squeda de Clientes y Cr&eacute;ditos</b>
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

<?php } else { ?>

<div class='sub-content' style='width:700px;' id='busqueda'>

<div class='sub-content'>
<img src='iconos/book_open.png'>
<b>Listado de Cr&eacute;ditos Morosos</b>
</div>

<div class='sub-content'>

<form id='filtro' name='filtro' method='post' 
action='creditos/busqueda/listado_morosos.php'>
<input type='hidden' id='xls' name='xls' value='1'> 

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:25%;'>D&iacute;as Morosidad:</td>

<td style='width:40%;'>
<select id='comp' name='comp'>
<option value='='>Igual a</option>
<option value='>'>Mayor que</option>
<option value='<' SELECTED>Menor que</option>
</select>

<select id='signo' name='signo'>
<option value='-' SELECTED>-</option>
<option value='+'>+</option>
</select>
<input type='text' size=5 value='10' 
style='text-align:center;' 
id='dias' name='dias' />
</td>

<td style='text-align:center;'>
<input type='button' onClick='busqueda(0);' 
value='Visualizar Informe...'>
</td>

</tr>

<tr>
<td style='text-align:right;'>M&oacute;dulo(s):</td>
<td>
<select id='funcionarios' name='funcionarios'>
<option value=-1>(Todos los M&oacute;dulos...)</option>
<?php echo $htmlfuncionarios; ?>
</select>
</td>
<td style='text-align:center;'>
<input type='button' onClick='busqueda(1);' 
value='Descargar Informe XLS...'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Ordenar por:</td>
<td><select id='orden' name='orden'>
<option value=0 SELECTED>Monto Morosidad</option>
<option value=1>Fecha &Uacute;ltimo Pago</option>
</select></td>
</tr>

</table>

</form>

</div>

<div class='sub-content2' id='listado_clientes' 
style='height:280px;overflow:auto;'>

</div>

<center>
<input type="button" value='Generar Carta de Notificaci&oacute;n...' 
onClick='generar_notificacion();'>
</center>


</div>


<?php } ?>

<div class='sub-content' style='width:800px;display:none;' id='cliente'>

<div class='sub-content'>

<table cellpadding=0 cellspacing=2>
<tr>
<td><img src='iconos/book_open.png'></td>
<td><b>Visualizar Clientes y Cr&eacute;ditos</b></td>
<td>
<input type="button" value='Volver Atr&aacute;s...' 
onClick='volver_listado();'>
</td>
</tr>
</table>


</div>

<div class='sub-content' id='datos_cliente'>

</div>

<div class='sub-content2' id='creditos_cliente' 
style='height:230px;overflow:auto;'>

</div>

</div>


</center>

<?php if(!isset($_GET['morosidad'])) { ?>
	<script> $('nombre').focus(); </script>
<?php } ?>
