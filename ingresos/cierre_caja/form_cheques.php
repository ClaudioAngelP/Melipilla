<?php 

	require_once('../../conectar_db.php');	

?>

<script>

consultar=function(v) {

	if(v==0) {	
	
		var params=$('fecha1').serialize()+'&'+$('fecha2').serialize();		
		
		$('listado_caja').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando Informaci&oacute;n...';
		
		var myAjax = new Ajax.Updater(
		'listado_caja','ingresos/cierre_caja/listado_cheques.php', {
			method: 'post',parameters: params
		});
	
	} else {

		$('filtro').submit();
	
	}


}

</script>

<center>
<div class='sub-content' style='width:700px;'>

<div class='sub-content'>
<img src='iconos/vcard.png'>
<b>Registro de Cheques</b>
</div>

<form id='filtro' name='filtro' method='post' 
action='ingresos/cierre_caja/listado_caja.php'>
<input type='hidden' id='xls' name='xls' value='1'> 

<div class='sub-content'>

<table style='width:100%;'>

  <tr><td style='text-align: right;'>Fecha Inicio:</td>
  <td><input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'></td></tr>
  <tr><td style='text-align: right;'>Fecha Final:</td>
  <td><input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha2_boton'></td></tr>

<tr><td colspan=2 style='text-align:center;'>
<input type='button' onClick='consultar(0);' value='Visualizar Informe...'>
<input type='button' onClick='consultar(1);' value='Descargar Informe en XLS...'>
</td></tr>

</table>

</div>

</form>

<div class='sub-content2' style='height:300px;overflow:auto;' id='listado_caja'>

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
  
