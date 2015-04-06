<?php 

	require_once('../../conectar_db.php');
		
	date_default_timezone_set('America/Halifax');
	
	

?>

<script>



	listado=function() {
	
		var params=$('filtro').serialize()+'&'+$('busqueda').serialize()+'&'+$('cuentaCte').serialize()+'&'+$('fecha_hosp').serialize()+'&'+$('fecha_hosp2').serialize();		
	
		var myAjax=new Ajax.Updater(
			'lista_pacientes',
			'prestaciones/asignar_camas/listado_informe.php',
			{  method:'post', parameters:params 	}	
			
		);
	
	}
	
	listar_hosp=function() {
	
	$('listado').style.display='';
    $('listado').innerHTML='<br><img src="imagenes/ajax-loader2.gif"><br><br>';

	$('xls').value=0;
	
	var myAjax=new Ajax.Updater(
		'listado',
		'prestaciones/informes_camas/listado_camas.php',
		{
			method:'post',
			parameters: $('regs').serialize()
		}	
	);

}
	
	descargar_xls=function() {

	$('xls').value=1;
		
	$('regs').method='post';
	$('regs').action='prestaciones/asignar_camas/listado_informe.php';
		
	$('regs').submit();
		
}
	




</script>

<center>

<form id='regs' name='regs' onSubmit='return false;'>
<input type='hidden' id='xls' name='xls' value='0' />
<div class='sub-content' style='width:980px;'>

<div class='sub-content'>
<img src='iconos/building.png'>
<b>Historial de Prestaciones  - Gesti&oacute;n de Camas</b>
</div>

<div>
<table style='width:100%;'>

<tr><td  style='text-align:right;'>
Filtro:
</td><td  style='width:30%;'>
<select  id='filtro' name='filtro'>
<option value='2' SELECTED>Todos los Pacientes...</option>

</select>
</td>
<td>





<tr><td style='text-align:right;'>
Buscar Paciente:
</td><td colspan="2">
<input type='text' id='busqueda' name='busqueda' size=45 />&nbsp;&nbsp;(Por: RUT, Ficha, Nombre o Apellidos)
</td></tr>

<tr><td style='text-align:right;'>
Cta. Corriente:
</td><td colspan="2">
<input type='text' id='cuentaCte' name='cuentaCte' size=10 />
</td></tr>

<tr>
<td id='tag_esp' style='text-align:right;'>
Fecha Inicio:</td><td>
<input type='text' name='fecha_hosp' id='fecha_hosp' value="<?php echo date('d/m/y'); ?>" size='10'>
<img src='iconos/date_magnify.png' name='fecha_boton1' id='fecha_boton1'>
</td>
</tr>

<tr>
<td id='tag_esp' style='text-align:right;'>
Fecha T&eacute;rmino:</td><td>
<input type='text' name='fecha_hosp2' id='fecha_hosp2' value="<?php echo date('d/m/y'); ?>" size='10' onchange='listado();'>
<img src='iconos/date_magnify.png' name='fecha_boton2' id='fecha_boton2'>
</td>
</tr>

<tr><td colspan="3">
<center>
<input type='button' onClick='listado();' value='-- Actualizar Listado --' />
<input type='button' value='-- Obtener Listado XLS... --' onClick='descargar_xls();' />
</center>
</td></tr>

</table>

</div>

<div class='sub-content2' style='height:290px;overflow:auto;' 
id='lista_pacientes'>

</div>

<script> 

    Calendar.setup({
        inputField     :    'fecha_hosp',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton1'
    });
    
    Calendar.setup({
        inputField     :    'fecha_hosp2',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton2'
    });
  
   


	listado(); 

</script>
