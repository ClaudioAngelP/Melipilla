<?php 

	require_once('../../conectar_db.php');	

	$companias=explode("\n",trim(file_get_contents('../companias.list')));

	sort($companias);
	
	$cias='';
	
	for($i=0;$i<sizeof($companias);$i++) {
		if(trim($companias[$i])=='') continue;
		$cias.='<option value="'.htmlentities($companias[$i]).'">'.htmlentities($companias[$i]).'</option>';
	}
	

	$ts=cargar_registros_obj("SELECT * FROM tipos_seguro ORDER BY ts_id;");

	$tipo_seguros='';

	for($i=0;$i<sizeof($ts);$i++) {
		$tipo_seguros.="<option value='".($ts[$i]['ts_id']*1)."'>".htmlentities($ts[$i]['ts_nombre'])."</option>";
	}
	
	
?>

<script>

consultar=function(v) {

	if(v==0) {	
	
		$('xls').value='0';
	
		var params=$('filtro').serialize();		
		
		$('listado_seguros').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando Informaci&oacute;n...';
		
		var myAjax = new Ajax.Updater(
		'listado_seguros','ingresos/seguros/listado_seguros.php', {
			method: 'post',parameters: params
		});
	
	} else {

		$('xls').value='1';
	
		$('filtro').submit();
	
	}


}

</script>

<center>
<div class='sub-content' style='width:800px;'>

<div class='sub-content'>
<img src='iconos/vcard.png'>
<b>Registro &Uacute;nico de Seguros</b>
</div>

<form id='filtro' name='filtro' method='post' 
action='ingresos/seguros/listado_seguros.php'>
<input type='hidden' id='xls' name='xls' value='1'> 

<div class='sub-content'>

<table style='width:100%;'>

  <tr><td style='text-align: right;'>Tipos de Seguro:</td>
  <td>
	<select id='tipo_seguros' name='tipo_seguros'>
	<option value=''>(Todos los tipos...)</option>
	<?php echo $tipo_seguros; ?>
	</select>
  </td></tr>

  <tr><td style='text-align: right;'>Fecha Inicio:</td>
  <td><input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha1_boton'></td></tr>
  <tr><td style='text-align: right;'>Fecha Final:</td>
  <td><input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='iconos/date_magnify.png' id='fecha2_boton'></td></tr>
  <tr><td style='text-align: right;'>Compa&ntilde;&iacute;a:</td>
  <td>
	<select id='compania' name='compania'>
	<option value=''>(Todas los compa&ntilde;&iacute;as...)</option>
	<?php echo $cias; ?>
	</select>
  </td></tr>

  <tr><td style='text-align: right;'>Estado(s):</td>
  <td>
	<select id='estado' name='estado'>
	<option value='-1'>(Todos los estados...)</option>
	<option value='0'>Ingresado</option>
	<option value='1'>En Proceso</option>
	<option value='2'>Rechazado</option>
	<option value='3'>Cobrado</option>
	<option value='4'>Anulado</option>
	</select>
  
  </td></tr>

<tr><td colspan=2 style='text-align:center;'>
<input type='button' onClick='consultar(0);' value='Visualizar Informe...'>
<input type='button' onClick='consultar(1);' value='Descargar Informe en XLS...'>
</td></tr>

</table>

</div>

</form>

<div class='sub-content2' style='height:300px;overflow:auto;' id='listado_seguros'>

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
  
