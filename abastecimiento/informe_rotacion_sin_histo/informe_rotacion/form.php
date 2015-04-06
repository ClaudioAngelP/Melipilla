<?php 

	require_once('../../conectar_db.php');
	
	$bodegashtml = desplegar_opciones("bodega", "bod_id, bod_glosa",'1','bod_id IN ('._cav(4).')', 'ORDER BY bod_glosa'); 

?>

<center>

<div class='sub-content' style='width:850px;'>
<div class='sub-content'>
<img src='iconos/package_go.png' />
<b>Indice de Rotaci&oacute;n por Bodega</b>
</div>

<form id='reporte' name='reporte' method='post'
action='abastecimiento/informe_rotacion/listado_rotacion.php' 
onSubmit='return false;' target='_self'>

<input type='hidden' id='xls' name='xls' value='1' />

<div class='sub-content'>
<table style='width:100%;'>
	<tr>
		<td style='text-align:right;'>Ubicaci&oacute;n:</td>
		<td><select id='bod_id' name='bod_id'><?php echo $bodegashtml; ?></select></td>
	</tr>
	
	<tr>
       <td style='text-align: right;'>Fecha Inicio:</td>
       <td>
           <input type='text' name='fecha1' id='fecha1' size=10
           style='text-align: center;' value='<?php echo date("d/m/Y",mktime(0,0,0,date('m')-1,date('d'),date('Y'))); ?>'>
           <img src='iconos/date_magnify.png' id='fecha1_boton'>
           
       </td>
    </tr>

	<tr>
       <td style='text-align: right;'>Fecha Final:</td>
       <td>
           <input type='text' name='fecha2' id='fecha2' size=10
           style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
           <img src='iconos/date_magnify.png' id='fecha2_boton'>
       </td>
    </tr>
    
    <tr>
    
    <td colspan=2><center>
		<input type='button' id='' name='' 
		onClick='generar_reporte();' 
		value='-- Generar Reporte... --' />

		<input type='button' id='' name='' 
		onClick='xls_reporte();' 
		value='-- Descargar XLS Reporte... --' />
    </center></td>
    
    </tr>

	
</table>
</div>

</form>

<div class='sub-content2' id='listado_informe' style='height:300px;overflow:auto;'>

</div>

</div>


</center>

<script>

	generar_reporte=function() {
	
		var myAjax=new Ajax.Updater(
			'listado_informe',
			'abastecimiento/informe_rotacion/listado_rotacion.php',
			{
				method: 'post',
				parameters: $('bod_id').serialize()+'&'+$('fecha1').serialize()+'&'+$('fecha2').serialize(),
				onComplete: function(r) {
					
					
					
				}
			}
		);
		
	}

	xls_reporte=function() {
	
		$('reporte').submit();
		
	}
	

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
