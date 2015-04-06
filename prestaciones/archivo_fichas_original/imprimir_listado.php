<?php require_once('../../conectar_db.php');
?>
<html>
<title>Solicitur de Ficha Espont&aacute;nea</title>
<?php cabecera_popup('../..'); ?>
<script>

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
	<td colspan=3>

<center>
	<input type='button' id='' name='' value='[Enviar Solivitud]' onClick='enviar_solicitud();' style='font-size:20px;' />
</center>

	</td></tr>	

</table>

</form>

</body>
</html>

<script>


    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('paciente').value=d[2].unescapeHTML();
		$('pac_id').value=d[4];
    	
    }

    limpiar_paciente = function(d) {
    
		$('pac_rut').value='';
		$('paciente').value='';
		$('pac_id').value=0;
    	
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
    	
    }

    limpiar_profesional = function(d) {
    
		$('doc_rut').value='';
		$('profesional').value='';
		$('doc_id').value=0;
    	
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
    	
    }

    limpiar_especialidad = function(d) {
    
		$('esp_id').value=0;
		$('esp_desc').value='';
    	
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
	
}

</script>
