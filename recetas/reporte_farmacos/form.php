<?php 

	require_once('../../conectar_db.php');
	
	$aut=cargar_registros_obj("
		SELECT * FROM autorizacion_farmacos ORDER BY autf_nombre;
	");
	
	$centro_costo = cargar_registros_obj("
		SELECT centro_ruta, centro_nombre FROM centro_costo ORDER BY centro_nombre
	");
	
	$servicios = '<OPTION value="-1">(Todos...)</OPTION>';
	
	for($i=0;$i<sizeof($centro_costo);$i++) {
			$servicios.= '<OPTION value="'.utf8_encode($centro_costo[$i]['centro_ruta']).'">'.utf8_encode($centro_costo[$i]['centro_nombre']).'</OPTION>';
		}

?>

<script>

listar_pacientes=function() {
	
	$('lista_pacientes').innerHTML='<br /><br /><br /><br /><img src="imagenes/ajax-loader3.gif" /><br />Cargando...';
	
	var myAjax=new Ajax.Updater(
		'lista_pacientes',
		'recetas/reporte_farmacos/listar_pacientes.php',
		{
			method:'post',
			parameters:$('autf_id').serialize()+'&'+
						$('bod_id').serialize()+'&'+
						$('ges').serialize()+'&'+
						$('finicio').serialize()+'&'+
						$('ffinal').serialize()+'&'+
						$('rut_medico').serialize()+'&'+
						$('servicio').serialize()
		}
	);
	
}

listar_pacientes_xls=function() {
	
	$('reporte').action='recetas/reporte_farmacos/listar_pacientes.php';
	$('reporte').submit();
	
}

</script>

<form id='reporte' name='reporte' onSubmit='return false;' method='post'>
<input type='hidden' id='xls' name='xls' value='1' />

<input type='hidden' id='pac_id' name='pac_id' value='' />

<center>
<div class='sub-content' style='width:950px;'>
<div class='sub-content'><img src='iconos/pill.png' /> Reportes de F&aacute;rmacos Restringidos, GES y MAC</div>

<div class='sub-content'>
<table>
	<tr>
		<td class='tabla_fila2'  style='text-align:right; width:150px;'>Tipo Reporte:</td>
		<td>
		<select id='autf_id' name='autf_id' style='width:550px;'>
		
		<option value='-1'>(Todas las Patolog&iacute;as/Programas ...)</option>
		<?php 
			
			for($i=0;$i<sizeof($aut);$i++) {
				
				print("<option value='".$aut[$i]['autf_id']."'>".htmlentities($aut[$i]['autf_nombre'])."</option>");
				
			}
			
		?>
		</select>
		</td>
	</tr>

	<tr>
		<td class='tabla_fila2'  style='text-align:right;'>Fecha Inicio - T&eacute;rmino:</td>
		<td class='tabla_fila' colspan=3>
		<input type='text' id='finicio' name='finicio' style='text-align:center;' size=10 value='<?php echo date('d/m/Y',mktime(0,0,0,(date('n')*1)-1, date('j'),date('Y'))); ?>' onBlur='validacion_fecha(this);' />
		 <img src='iconos/date_magnify.png' id='finicio_boton' alt='Buscar Fecha...' title='Buscar Fecha...'>
		<input type='text' id='ffinal' name='ffinal' style='text-align:center;' size=10 value='<?php echo date('d/m/Y'); ?>' onBlur='validacion_fecha(this);' />
		<img src='iconos/date_magnify.png' id='ffinal_boton' alt='Buscar Fecha...' title='Buscar Fecha...'>
		</td>
	</tr>

	<tr>
		<td class='tabla_fila2'  style='text-align:right;'>Ubicaci&oacute;n:</td>
		<td class='tabla_fila' colspan=3>

		<select id='bod_id' name='bod_id'>

		<option value='-1' SELECTED>(Todas las Farmacias...)</option>

		<?php 
		
			$bod=pg_query("SELECT * FROM bodega WHERE bod_id IN (35,36) ORDER BY bod_glosa");
			
			while($b=pg_fetch_assoc($bod)) {
				print("<option value='".$b['bod_id']."'>".htmlentities($b['bod_glosa'])."</option>");
			}
		
		?>

		</select>

		</td>
	</tr>

	<tr>
		<td class='tabla_fila2'  style='text-align:right;'>GES:</td>
		<td class='tabla_fila' colspan=3>
		<select id='ges' name='ges'>
			<option value='0'>(Todos...)</option>
			<option value='1'>S&iacute;</option>
			<option value='2'>No</option>
		</select>
		</td>
	</tr>
	
	<tr>
		<td class="tabla_fila2" style="text-align:right;">M&eacute;dico</td>
		<td class="tabla_fila" colspan=3><input type='text' id='nombre_medico' name='nombre_medico' size=35 onKeyUp=''></td>
	</tr>
	
	<tr>
		<td class="tabla_fila2" style="text-align:right;">Servicio</td>
		<td class="tabla_fila" colspan=3><SELECT name="servicio" id="servicio"><?php echo $servicios; ?></SELECT></td>
	</tr>
	
	<input type="hidden" id="rut_medico" name="rut_medico">

</table>

<center>
<input type='button' value='-- Generar Reporte --' onClick='listar_pacientes();'>
<input type='button' value='-- Generar Reporte XLS --' onClick='listar_pacientes_xls();'>
</center>

</div>

<div class='sub-content2' id='lista_pacientes' style='height:300px;overflow:auto;'>



</div>

</div>
</center>

</form>

<script>
	
	  ingreso_rut=function(datos_medico) {
      
		$('rut_medico').value=datos_medico[1];
      
      }
	
	  autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<2) {
			$('rut_medico').value="";
			return false;
			}
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 450, 300, 250, 1, 2, ingreso_rut);
      
      validacion_fecha($('finicio'));
      validacion_fecha($('ffinal'));

  
    Calendar.setup({
        inputField     :    'finicio',   // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'finicio_boton'
    });

    Calendar.setup({
        inputField     :    'ffinal',   // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'ffinal_boton'
    });
    
</script>

