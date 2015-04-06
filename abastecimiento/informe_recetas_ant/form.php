<?php 

	require_once("../../conectar_db.php");
	
  $bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(4),')', 'ORDER BY bod_glosa'); 	

?>

<script>

generar_reporte=function() {
	
	$('reporte').innerHTML='<br><br><img src="imagenes/ajax-loader3.gif" /><br>Cargando...';
	
	var myAjax=new Ajax.Updater(
	'reporte', 
	'abastecimiento/informe_recetas/listar_totales.php',
		{
			method: 'post',
			parameters: $('fecha1').serialize()+'&'+$('fecha2').serialize()+'&'+$('bodega').serialize()
		}
	);
	
}

generar_reporte_xls=function() {
	
	$('dreporte').action='abastecimiento/informe_recetas/listar_totales.php';
	
	$('dreporte').submit();
		
}


</script>

<center>

<div style='width:900px;' class='sub-content'>

<div class='sub-content'>
<img src='iconos/script.png' />
<b>Total de Recetas y Preescripciones</b>
</div>

<form id='dreporte' name='dreporte' onSubmit='return false;' method='post'>

<input type='hidden' id='xls' name='xls' value='1' />

<table style='width:100%;'>

<tr>
<td style='width:100px;text-align:right;'>Fecha Inicio:</td>
<td>
<input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y", mktime(0,0,0,date('m')*1-1,date('d')*1,date('Y')*1)); ?>'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'>
</td>
</tr>


<tr>
<td style='width:100px;text-align:right;'>Fecha Final:</td>
<td>
	<input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y"); ?>'>
  <img src='iconos/date_magnify.png' id='fecha2_boton'>
</td>
</tr>

<tr>
<td style='text-align: right;'>Ubicaci&oacute;n:</td>
<td>

<select name='bodega' id='bodega'>
<?php echo $bodegashtml; ?>
</select>

</td></tr>

<tr><td colspan=2>
<center>

<input type='button' id='' name='' value='-- Generar Reporte --' onClick='generar_reporte();' />
<input type='button' id='' name='' value='-- Generar XLS --' onClick='generar_reporte_xls();' />

</center>
</td></tr>

</table>

</form>

<div class='sub-content2' style='height:300px;overflow:auto;' id='reporte'>

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

  
  </script>
