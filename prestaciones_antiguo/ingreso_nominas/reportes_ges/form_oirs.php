<?php 

	require_once('../../conectar_db.php');

	$servhtml = desplegar_opciones_sql('SELECT * FROM centro_costo
	WHERE centro_hosp ORDER BY centro_nombre'); 

?>

<script>

actualizar_campos=function() {
	
	var valor=$('tipo_informe').value*1;
	
	if(valor==1) {
		
		$('fecha_1').hide();
		$('fecha_2').hide();
		$('especialidad_tr').show();
		$('servicio_tr').show();
		$('rut_medico_tr').show();
		$('nombre_medico_tr').show();
		$('condicion_tr').hide();
		$('dias_tr').show();
		$('filtro_tr').show();
		
	} else if(valor==2) {

		$('fecha_1').hide();
		$('fecha_2').hide();
		$('especialidad_tr').show();
		$('servicio_tr').show();
		$('rut_medico_tr').show();
		$('nombre_medico_tr').show();
		$('condicion_tr').hide();
		$('dias_tr').show();
		$('filtro_tr').show();
		
	} else if(valor==3) {

		$('fecha_1').hide();
		$('fecha_2').hide();
		$('especialidad_tr').show();
		$('servicio_tr').show();
		$('rut_medico_tr').show();
		$('nombre_medico_tr').show();
		$('condicion_tr').show();
		$('filtro_tr').show();
		
	} else if(valor==4) {

		$('fecha_1').show();
		$('fecha_2').show();
		$('especialidad_tr').show();
		$('servicio_tr').show();
		$('rut_medico_tr').show();
		$('nombre_medico_tr').show();
		$('condicion_tr').show();
		$('dias_tr').show();
		$('filtro_tr').show();
		
	} else if(valor==5) {

		$('fecha_1').hide();
		$('fecha_2').hide();
		$('especialidad_tr').show();
		$('servicio_tr').show();
		$('rut_medico_tr').show();
		$('nombre_medico_tr').show();
		$('condicion_tr').hide();
		$('dias_tr').show();
		$('filtro_tr').show();
		
	} else if(valor==6) {

		$('fecha_1').hide();
		$('fecha_2').hide();
		$('especialidad_tr').hide();
		$('servicio_tr').show();
		$('rut_medico_tr').hide();
		$('nombre_medico_tr').hide();
		$('condicion_tr').hide();
		$('dias_tr').show();
		$('filtro_tr').hide();
		
	} else {

		$('fecha_1').show();
		$('fecha_2').show();
		$('especialidad_tr').hide();
		$('servicio_tr').show();
		$('rut_medico_tr').hide();
		$('nombre_medico_tr').hide();
		$('condicion_tr').hide();
		$('dias_tr').hide();
		$('filtro_tr').hide();		
	}
	
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
			parameters: $('datos').serialize()
		}	
	);

}

descargar_xls=function() {

	$('xls').value=1;
		
	$('datos').method='post';
	$('datos').action='prestaciones/informes_camas/listado_camas.php';
		
	$('datos').submit();
		
}	

</script>

<center>
	<div class='sub-content' style='width:950px;'>
		<div class='sub-content'>
			<img src='iconos/table.png'>
				<b>Informes de Gesti&oacute;n Centralizada de Camas</b>
		</div>
		<div class='sub-content'>
			
			<form id='datos' name='datos' method="post" onSubmit='return false;'>
			<input type='hidden' id='xls' name='xls' value='0' />
			
				<table style='width:100%;'>

					<tr>
						<td style='text-align:right;'>Tipo de Informe:</td>
						<td>
							<select id='tipo_informe' name='tipo_informe' onchange="actualizar_campos();">
                                <option value='5' SELECTED>Consulta O.I.R.S.</option>
							</select>
						</td>
					</tr>


					<tr id='fecha_1'>
						<td style='text-align: right;'>Fecha Inicio:</td>
  						<td>
  							<input type='text' name='fecha1' id='fecha1' size=10
  							style='text-align: center;' value='<?php echo date("d/m/Y"); ?>' onBlur='validacion_fecha(this);'>
  							<img src='iconos/date_magnify.png' id='fecha1_boton'>
  						</td>
  					</tr>
  					<tr id='fecha_2'>
  						<td style='text-align: right;'>Fecha Final:</td>
  						<td><input type='text' name='fecha2' id='fecha2' size=10
  						style='text-align: center;' value='<?php echo date("d/m/Y"); ?>' onBlur='validacion_fecha(this);'>
  						<img src='iconos/date_magnify.png' id='fecha2_boton'>
  						</td>
  					</tr>
  					
  					
					<tr id='especialidad_tr'>
					<td id='tag_esp' style='text-align:right;'>
					(Sub)Especialidad:</td><td>
					<input type='hidden' id='esp_id' name='esp_id' value='<?php echo $r[0]['hosp_esp_id']*1; ?>'>
					<input type='text' id='especialidad'  name='especialidad' value='<?php echo $r[0]['esp_desc']; ?>' 
					onDblClick='$("esp_id").value=""; $("especialidad").value="";' size=35>
					</td>
					</tr>

					<tr id='servicio_tr'>
						<td style='text-align:right;width:30%;'>
						Servicio:
						</td>
						<td>
						<input type="hidden" id='centro_ruta0' name='centro_ruta0' value='<?php echo $r[0]['hosp_servicio']; ?>'>
						<input type="text" id='servicios0' name='servicios0' 
						onDblClick='$("centro_ruta0").value=""; $("servicios0").value="";'
						value='<?php echo $r[0]['tcama_tipo_ing']; ?>'>
						</td>
					</tr>

					<tr id='rut_medico_tr'>
					<td style='text-align:right;'>R.U.T. M&eacute;dico:</td>
					<td>
					<input type='text' id='rut_medico' name='rut_medico' size=10
					style='text-align: center;' value='<?php echo $r[0]['doc_rut']; ?>' disabled></td></tr>

					<tr id='nombre_medico_tr'>
					<td style='text-align:right;'>M&eacute;dico Tratante:</td>
					<td>
					<input type='hidden' id='doc_id' name='doc_id' value='<?php echo $r[0]['hosp_doc_id']; ?>'>
					<input type='text' id='nombre_medico' name='nombre_medico' size=35 onDblClick='$("rut_medico").value="";$("doc_id").value="";$("nombre_medico").value="";'
					   value='<?php echo trim($r[0]['doc_paterno'].' '.$r[0]['doc_materno'].' '.$r[0]['doc_nombres']); ?>' />
					</td>
					</tr>
  					
  					<tr id='condicion_tr'>
						<td style='text-align:right;'>Condici&oacute;n Egreso:</td>
						<td>
							<select id='condicion_egreso' name='condicion_egreso'>
								<option value="0" SELECTED>(Cualquiera...)</option>
								<option value="1">Alta a Domicilio</option>
								<option value="2">Derivaci&oacute;n</option>
								<option value="3">Fallecido</option>
								<option value="4">Fugado</option>
								<option value="5">Otro...</option>
							</select>
						</td>
					</tr>

					<tr id='dias_tr'>
					<td style='text-align:right;'>Dias Hospitalizado:</td>
					<td>
					desde <input type='text' id='dias_desde' name='dias_desde' size=5 value='' style='text-align:right;' />
					hasta <input type='text' id='dias_hasta' name='dias_hasta' size=5 value='' style='text-align:right;' />
					</td>
					</tr>

					<tr id='filtro_tr'>
					<td style='text-align:right;'>Buscar Paciente:</td>
					<td>
					<input type='text' id='filtro' name='filtro' size=40 value='' />&nbsp;&nbsp;(Por: RUT, Ficha, Nombre o Apellidos)
					</td>
					</tr>

  					
					<tr>
						<td colspan=2>
							<center>
								<input type='button' onClick='listar_hosp();' 
								  value='-- Actualizar Listado... --'>
						
								<input type='button'  onClick='descargar_xls();' 
								value='-- Obtener Archivo XLS... --'>
							</center>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div class='sub-content2' style='height:400px;overflow:auto;' id='listado'>
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

    ingreso_especialidades=function(datos_esp) {
      $('esp_id').value=datos_esp[0];
      $('especialidad').value=datos_esp[2].unescapeHTML();
    }
      
    autocompletar_especialidades = new AutoComplete(
      'especialidad', 
      'autocompletar_gcamas.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=especialidad_subespecialidad&esp_desc='+encodeURIComponent($('especialidad').value)
        }
    }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);

    seleccionar_serv2 = function(d) {

	$('centro_ruta0').value=d[0].unescapeHTML();
	$('servicios0').value=d[2].unescapeHTML(); 

    }

    autocompletar_servicios2 = new AutoComplete(
      'servicios0', 
      'autocompletar_sql.php',
      function() {
        if($('servicios0').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=servicios_hospitalizacion&cadena='+encodeURIComponent($('servicios0').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_serv2);

      ingreso_rut=function(datos_medico) {
      	$('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      'nombre_medico', 
      'autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);


		actualizar_campos();
  
  </script>
  
