<?php 

	require_once('../../conectar_db.php');

	$servhtml = desplegar_opciones_sql('SELECT * FROM centro_costo
	WHERE centro_hosp ORDER BY centro_nombre'); 

?>

<script>

actualizar_campos=function() {
	
	var valor=$('tipo_informe').value*1;
	
	if(valor==3 || valor==4) {
		
		$('fecha_1').hide();
		$('fecha_2').hide();
		$('btn_ver').hide();
		
	} else {
		
		$('fecha_1').show();
		$('fecha_2').show();
		$('btn_ver').show();
		
	}
	
}

listar_hosp=function() {
	
	$('listado').style.display='';
    $('listado').innerHTML='<br><img src="imagenes/ajax-loader2.gif"><br><br>';

	$('xls').value=0;
	
	var myAjax=new Ajax.Updater(
		'listado',
		'prestaciones/reportes_ges/reportes.php',
		{
			method:'post',
			parameters: $('datos').serialize(),
			evalScripts: true
		}	
	);

}

descargar_xls=function() {

	$('xls').value=1;
		
	$('datos').method='post';
	$('datos').action='prestaciones/reportes_ges/reportes.php';
		
	$('datos').submit();
		
}	


</script>

<center>
	<div class='sub-content' style='width:950px;'>
		<div class='sub-content'>
			<img src='iconos/table.png'>
				<b>Reportes Monitoreo GES</b>
		</div>
		<div class='sub-content'>
			
			<form id='datos' name='datos' method="post" onSubmit='return false;'>
			<input type='hidden' id='xls' name='xls' value='0' />
			
				<table style='width:100%;'>

					<tr>
						<td style='text-align:right;'>Tipo de Informe:</td>
						<td>
							<select id='tipo_informe' name='tipo_informe' onchange="actualizar_campos();">
							<option value='1' SELECTED>Cartas Enviadas</option>
							<option value='7'>Cartas Enviadas con Respuesta</option>
                        			        <option value='2' >Compras</option>
				                        <option value='6' >Env&iacute;os a Compras</option>
			                                <option value='5' >Pacientes GES Sin Datos</option>
				                        <option value='3' >Garant&iacute;as Cerradas</option>
                        			        <option value='4' >Garant&iacute;as Exceptuadas</option>
							<option value='8' >Productividad Monitores</option>
							<option value='9' >Productividad Monitores (Detalle)</option>
							</select>
						</td>
					</tr>


					<tr id='fecha_1'>
						<td style='text-align: right;'>Fecha Inicio:</td>
  						<td>
  							<input type='text' name='fecha1' id='fecha1' size=10
  							style='text-align: center;' value='<?php echo date("d/m/Y",mktime(0,0,0,date('m')-1)); ?>' onBlur='validacion_fecha(this);'>
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
  					
  					

  					
					<tr>
						<td colspan=2>
							<center>
								<input type='button' id='btn_ver' onClick='listar_hosp();' 
								  value='-- Actualizar Listado... --'>
						
								<input type='button'  onClick='descargar_xls();' 
								value='-- Obtener Archivo XLS... --'>
								
								
								&nbsp;&nbsp;&nbsp;&nbsp;Cantidad de Registros: <span id='cant_registros' style='font-weight:bold;'>0</span>
								
								
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
  
