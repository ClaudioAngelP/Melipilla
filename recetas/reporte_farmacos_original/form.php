<?php 

	require_once('../../conectar_db.php');
	
	$aut=cargar_registros_obj("
		SELECT * FROM autorizacion_farmacos ORDER BY autf_nombre;
	");

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
						$('ffinal').serialize()
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
		<input type='text' id='ffinal' name='ffinal' style='text-align:center;' size=10 value='<?php echo date('d/m/Y'); ?>' onBlur='validacion_fecha2(this);' />
		</td>
	</tr>

	<tr>
		<td class='tabla_fila2'  style='text-align:right;'>Ubicaci&oacute;n:</td>
		<td class='tabla_fila' colspan=3>

		<select id='bod_id' name='bod_id'>

		<option value='-1' SELECTED>(Todas las Farmacias...)</option>

		<?php 
		
			$bod=pg_query("SELECT * FROM bodega WHERE bod_id IN (36) ORDER BY bod_glosa");
			
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
      
      validacion_fecha($('finicio'));
      validacion_fecha($('ffinal'));


</script>
