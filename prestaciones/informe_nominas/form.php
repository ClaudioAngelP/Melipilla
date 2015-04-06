<?php 

	require_once('../../conectar_db.php');

	$query='';

	if(_cax(202)) {
		$query="
			SELECT DISTINCT esp_id, esp_desc FROM nomina 
			JOIN especialidades ON nom_esp_id=esp_id
			WHERE esp_id NOT IN (SELECT esp_id FROM procedimiento)
  		";
	}
	
	if(_cax(202) AND _cax(300)) $query.=' UNION ';	
	
	if(_cax(300)) {
		$query.="
			SELECT DISTINCT esp_id, esp_desc FROM especialidades
			WHERE esp_id IN ("._cav(300).") AND 
			esp_id IN (SELECT esp_id FROM procedimiento)
		";
	}
	
	$query='SELECT DISTINCT * FROM ('.$query.') AS foo ORDER BY esp_desc';
	
	$esp=desplegar_opciones_sql($query, $esp_id);

?>

<script>

listar_informe=function(xls) {

	$('informe').innerHTML='<br /><br /><br /><img src="imagenes/ajax-loader2.gif" /><br />Cargando...';

	if($('mostrar').value*1==2)
		url='listar_rem';
	else
		url='listar_produccion';

	var myAjax=new Ajax.Updater(
		'informe',
		'prestaciones/informe_nominas/'+url+'.php',
		{
			method:'post',
			parameters:$('consulta').serialize()	
		}	
	);	
	
}

descargar_informe=function() {

	if($('mostrar').value*1==2)
		$('consulta').action='prestaciones/informe_nominas/listar_rem.php';
	else
		$('consulta').action='prestaciones/informe_nominas/listar_produccion.php';

	$('xls').value=1;
	$('consulta').submit();
	
}

mostrar_agrupar=function(){
	
	if($('mostrar').value*1==2){
		$('agrupar').style.display='';
		$('select_agrupar').style.display='';
	}else{
		$('agrupar').style.display='none';
		$('select_agrupar').style.display='none';
		}
}


</script>

<center>
<div class='sub-content' style='width:980px;'>

<form id='consulta' name='consulta' 
method='post' action='prestaciones/informe_nominas/listar_produccion.php' 
onSubmit='return false;' />

<input type='hidden' id='xls' name='xls' value='0' />

<div class='sub-content'>
<table style='width:100%;' cellpadding=0 cellspacing=0><tr>
<td style='width:30px;'>
</td><td style='font-size:14px;'><b>Informe de Producci&oacute;n</b></td>
</tr></table>
<div class='sub-content' id='buscar_nominas'>
<table style='width:100%;'>
<tr>
</td>


<tr>
</td>


<tr>
<option value=-1 SELECTED>(Todas las Especialidades...)</option>
<?php echo $esp; ?>
</select>

<tr>
</td>


<tr><td style='text-align:right;'>
Mostrar:
</td><td>
<select id='mostrar' name='mostrar' onclick='mostrar_agrupar()' onkeyup='mostrar_agrupar()'>
<option value='0'>Totales Generales</option>
<option value='1'>Totales y Detalle Completo</option>
<option value='2'>Datos para R.E.M.</option>
</select>
</td>
<td id='agrupar' style='text-align:right;display:none;'>Agrupar por:</td>
<td id='select_agrupar' style='display:none;'>
<select id='agrupa' name='agrupa'>
	<option value='0'>C&oacute;digo Prestaci&oacute;n</option>
	<option value='1'>Profesional</option>
	<option value='2'>Especialidad</option>
</select>
 <input id='detalle' name='detalle' type='checkbox' />Incluir Detalle
</td>
</tr>

</table>
</div>

<center>
<input type='button' id='lista_informe' 
<input type='button' id='lista_xls' 
</center>
<div class='sub-content2' id='informe' style='height:250px;overflow:auto;'>

</div>

</form>

</div>

<script>

    Calendar.setup({
    });
    });


</script>