<?php 

	require_once('../../conectar_db.php');

?>

<script>

listar_fap=function() {

	$('xls').value=0;
	$('listado').innerHTML='<br><br><br><img src="imagenes/ajax-loader3.gif"><br>Espere un momento...';

	if($('tipo').value!=5)	
		var url='prestaciones/consultar_fap/listado_informe.php';
	else
		var url='prestaciones/ingreso_fap/informe_fap.php';
	

	var myAjax=new Ajax.Updater(
		'listado',
		url,
		{
			method:'post',
			parameters:$('consulta').serialize()
		}	
	);
	
}

descargar_nominas=function() {

	$('xls').value=1;

	if($('tipo').value!=5)	
		var url='prestaciones/consultar_fap/listado_informe.php';
	else
		var url='prestaciones/ingreso_fap/informe_fap.php';

	$('consulta').action=url;

	$('consulta').submit();
	
}

fix_tabla=function() {

	if($('tipo').value==5)
		$("tr_informe").style.display="none";
	else
		$("tr_informe").style.display="";	
	
}

</script>

<center>
<div class='sub-content' style='width:950px;'>
<div class='sub-content'>
<img src='iconos/table.png' />
<b>Informes Estad&iacute;sticos de FAP Unificado</b> 
</div>

<div class='sub-content'>

<form id='consulta' name='consulta' 
method='post' action='prestaciones/consultar_fap/listado_informe.php' 
onSubmit='return false;' />

<input type='hidden' id='xls' name='xls' value='0' />

<table style='width:100%;'>

<tr><td style='width:100px;text-align:right;'>Tipo:</td><td><select id='tipo' name='tipo' onClick='fix_tabla();'>
<?php if(_cax(207)) { ?> <option value=1>Infantil</option> <?php } ?>
<?php if(_cax(206)) { ?> <option value=2>Maternal</option> <?php } ?>
<?php if(_cax(205)) { ?> <option value=3>Adulto</option> <?php } ?>
<?php if(_cax(208)) { ?> <option value=5>Pabell&oacute;n</option> <?php } ?>
</select>
</td></tr>

  <tr><td style='text-align: right;'>Fecha Inicio:</td>
  <td><input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'></td></tr>
  <tr><td style='text-align: right;'>Fecha Final:</td>
  <td><input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha2_boton'></td></tr>
  
  <tr id='tr_informe'><td style='text-align:right;'>Informe:</td>
  <td>
  <select id='tipo_informe' name='tipo_informe'>
  <option value='-1' SELECTED>Informe Completo Registro FAP</option>
  <option value='6'>Informe General FAP</option>
  <option value='3'>Consultas por Comuna y Sexo</option>
  <option value='0'>Consultas por Comuna y Sexo Oftalmol&oacute;gicos</option>
  <option value='1'>Consultas por Comuna y Sexo Otorrinos</option>
  <option value='2'>Consultas por Comuna y Sexo Psiqui&aacute;tricos</option>
  <option value='5'>Categorizaci&oacute;n de Pacientes mediante Selector de Demanda</option>
  <option value='7'>Tabulado de Procedimientos Seg&uacute;n Previsi&oacute;n</option>
  <option value='8'>Tipo de Atenci&oacute;n y Grupo Etario</option>
  <option value='9'>Pacientes Hospitalizados en Camilla de Observaci&oacute;n</option>
  <option value='10'>Prestaciones Dentales Seg&uacute;n Grupo Etario</option>
  </select>
  </td></tr>

<tr><td colspan=2 style='text-align:center;'>
<center>
<input type='button' id='actualiza' name='actualiza' 
onClick='listar_fap();'
value='-- Actualizar Listado... --' />

<input type='button' id='descarga' name='descarga' 
onClick='descargar_nominas();'
value='-- Descargar Informe en XLS... --' />

</center>
</td></tr>

</table>

</form>

</div>

<div class='sub-content2' id='listado' style='height:250px;overflow:auto;'>

</div>

</div>

</center>

  <script>
  
    Calendar.setup({
        inputField     :    'fecha1',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha1_boton'
    });
    Calendar.setup({
        inputField     :    'fecha2',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha2_boton'
    });

	fix_tabla();
  
  </script>
  
