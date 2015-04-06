<?php 

	require_once('../../conectar_db.php');
	

?>

<html>
<title>B&uacute;squeda de Cupos de Atenci&oacute;n</title>
<?php cabecera_popup('../..'); ?>

<script>

function validacion_fecha2(obj) {
	var obj=$(obj);
	
	if(trim(obj.value)=='') {
		obj.value='';
		obj.style.background='skyblue';
		return true;
	} else
		return validacion_fecha(obj);
}

function validacion_hora2(obj) {
	var obj=$(obj);
	
	if(trim(obj.value)=='') {
		obj.value='';
		obj.style.background='skyblue';
		return true;
	} else
		return validacion_hora(obj);
}

function listar_cupos() {
	
	if(!validacion_fecha2($('fecha1'))) {
		alert('Fecha m&iacute;nima incorrecta.'.unescapeHTML());
		$('fecha1').select();
		$('fecha1').focus();
		return;
	}

	if(!validacion_fecha2($('fecha2'))) {
		alert('Fecha m&aacute;xima incorrecta.'.unescapeHTML());
		$('fecha2').select();
		$('fecha2').focus();
		return;
	}

	if(!validacion_hora2($('hora1'))) {
		alert('Hora m&iacute;nima incorrecta.'.unescapeHTML());
		$('hora1').select();
		$('hora1').focus();
		return;
	}

	if(!validacion_hora2($('hora2'))) {
		alert('Hora m&aacute;xima incorrecta.'.unescapeHTML());
		$('hora2').select();
		$('hora2').focus();
		return;
	}
	
	$('resultados_busqueda').innerHTML='<center><br/><br/><img src="../../imagenes/ajax-loader3.gif"><br/>Espere un momento...</center>';
	
	var myAjax=new Ajax.Updater(
	'resultados_busqueda',
	'buscar_cupos.php',
	{
		method:'post',
		parameters:$('datos').serialize()
	}
	);
	
}

function abrir_nom(nom_folio) {
	
	var fn=window.opener.abrir_nomina.bind(window.opener);
	fn(nom_folio, 1);
	window.opener.focus();
	window.close();
		
}

	imprimir_citacion=function(nomd_id) {
	
      top=Math.round(screen.height/2)-250;
      left=Math.round(screen.width/2)-340;

      new_win = 
      window.open('citaciones.php?nomd_id='+nomd_id,
      'win_nomina', 'toolbar=no, location=no, directories=no, status=no, '+
      'menubar=no, scrollbars=yes, resizable=no, width=680, height=500, '+
      'top='+top+', left='+left);

      new_win.focus();
					
	}

function anular_cupos() {

	if($('motivo_anula').value=='') {
		alert('Debe seleccionar motivo.'); return;
	}

	if(!confirm("&iquest;Est&aacute; seguro que desea anular masivamente los cupos seleccionados? - NO HAY OPCIONES PARA DESHACER.".unescapeHTML()))
		return;

		
	var myAjax=new Ajax.Updater(
	'resultados_busqueda',
	'anular_cupos.php',
	{
		method:'post',
		parameters:$('datos').serialize()
	}
	);

}

</script>

<body class='fuente_por_defecto popup_background'>

<form id='datos' name='datos' onSubmit='return false;'>

<table style='width:100%;'>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Paciente:</td>
		<td class='tabla_fila'>

<input type='hidden' id='pac_id' name='pac_id' value='0' />
<input type='text' size=10 id='pac_rut' name='pac_rut' value='' onDblClick='limpiar_paciente();' style='font-size:16px;' />
</td><td class='tabla_fila'>
<input type='text' id='paciente' name='paciente'  onDblClick='limpiar_paciente();' 
style='text-align:left;font-size:16px;' DISABLED size=40 />


		</td>
	</tr>

	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Especialidad:</td>
		<td class='tabla_fila' colspan=2>

<input type='hidden' id='esp_id' name='esp_id' value='0' />
<input type='text' size=45 id='esp_desc' name='esp_desc' value='' onDblClick='limpiar_especialidad();' style='font-size:16px;' />


		</td>
	</tr>


	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Profesional:</td>
		<td class='tabla_fila'>

<input type='hidden' id='doc_id' name='doc_id' value='0' />
<input type='text' size=10 id='doc_rut' name='doc_rut' value='' onDblClick='limpiar_profesional();' style='font-size:16px;'  />
</td><td class='tabla_fila'>
<input type='text' id='profesional' name='profesional'  onDblClick='limpiar_profesional();'
style='text-align:left;font-size:16px;' DISABLED size=40 />


		</td>
	</tr>

	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Fecha:</td>
		<td colspan=2>
		desde <input type='text' id='fecha1' name='fecha1' value='<?php echo date('d/m/Y'); ?>' onDblClick='this.value="";validacion_fecha2(this);' onBlur='validacion_fecha2(this);' size=10 style='text-align:center;font-weight:bold;font-size:16px;' />
		hasta <input type='text' id='fecha2' name='fecha2' value='' onDblClick='this.value="";validacion_fecha2(this);' onBlur='validacion_fecha2(this);' size=10 style='text-align:center;font-weight:bold;font-size:16px;' />
		</td>
	</tr>

	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Hora:</td>
		<td colspan=2>
		desde <input type='text' id='hora1' name='hora1' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;font-weight:bold;font-size:16px;' />
		hasta <input type='text' id='hora2' name='hora2' value='' onDblClick='this.value="";validacion_hora2(this);' onBlur='validacion_hora2(this);' size=5 style='text-align:center;font-weight:bold;font-size:16px;' />
		</td>
	</tr>

	<tr>
	<td colspan=3>

<center>
	<input type='button' id='' name='' value='[Realizar B&uacute;squeda]' onClick='listar_cupos();' style='font-size:20px;' />
	<input type='button' id='' name='' value='[Limpiar Filtros...]' onClick='limpiar_todo();' style='font-size:20px;' /><br/>
	<select id='motivo_anula' name='motivo_anula' style='font-size:20px;'>
	<option value=''>(Seleccione Motivo de Anulaci&oacute;n...)</option>
<?php
	$m=cargar_Registros_obj("SELECT * FROM nomina_codigo_cancela ORDER BY cancela_id", true);

	for($i=0;$i<sizeof($m);$i++) {
		print("<option value='".$m[$i]['cancela_id']."'>".$m[$i]['cancela_desc']."</option>");
	}

 ?>
	</select>
	<input type='button' id='' name='' value='[Anulaci&oacute;n Masiva de Cupos]' onClick='anular_cupos();' style='font-size:20px;color:red;' />
</center>

	</td></tr>	

	<tr>
	<td colspan=3>
	
	<div class='sub-content' id='resultados_busqueda' style='min-height:200px;'>
	
	<center><h2>(Seleccione Filtros para su B&uacute;squeda, Doble Click para limpiar Filtros)</h2><br/>
	
	</div>
	
	</td>
	</tr>



</table>

</form>

</body>
</html>

<script>


    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('paciente').value=d[2].unescapeHTML();
		$('pac_id').value=d[4];
		listar_cupos();
    	
    }

    limpiar_paciente = function(d) {
    
		$('pac_rut').value='';
		$('paciente').value='';
		$('pac_id').value=0;
		listar_cupos();
    	
    }


    autocompletar_pacientes = new AutoComplete(
      'pac_rut', 
      '../../autocompletar_sql.php',
      function() {
        if($('pac_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);




    seleccionar_profesional = function(d) {
    
		$('doc_rut').value=d[1];
		$('profesional').value=d[2].unescapeHTML();
		$('doc_id').value=d[0];
		listar_cupos();
    	
    }

    limpiar_profesional = function(d) {
    
		$('doc_rut').value='';
		$('profesional').value='';
		$('doc_id').value=0;
		listar_cupos();
    	
    }


    autocompletar_profesionales = new AutoComplete(
      'doc_rut', 
      '../../autocompletar_sql.php',
      function() {
        if($('doc_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=doctor&nombre_doctor='+encodeURIComponent($('doc_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_profesional);



    seleccionar_especialidad = function(d) {
    
		$('esp_id').value=d[0];
		$('esp_desc').value=d[2].unescapeHTML();
		listar_cupos();
    	
    }

    limpiar_especialidad = function(d) {
    
		$('esp_id').value=0;
		$('esp_desc').value='';
		listar_cupos();
    	
    }

    autocompletar_especialidad = new AutoComplete(
      'esp_desc', 
      '../../autocompletar_sql.php',
      function() {
        if($('esp_desc').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=especialidad&'+$('esp_desc').serialize()
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_especialidad);


limpiar_todo=function() {
	
		$('pac_rut').value='';
		$('paciente').value='';
		$('pac_id').value=0;


		$('esp_id').value=0;
		$('esp_desc').value='';

		$('doc_rut').value='';
		$('profesional').value='';
		$('doc_id').value=0;
	
		$('fecha1').value='<?php echo date('d/m/Y'); ?>';
		$('fecha2').value='';
		$('hora1').value='';
		$('hora2').value='';

		validacion_fecha2($('fecha1'));
		validacion_fecha2($('fecha2'));
		validacion_hora2($('hora1'));
		validacion_hora2($('hora2'));
		
		listar_cupos();
	
}

validacion_fecha2($('fecha1'));
validacion_fecha2($('fecha2'));
validacion_hora2($('hora1'));
validacion_hora2($('hora2'));

</script>
