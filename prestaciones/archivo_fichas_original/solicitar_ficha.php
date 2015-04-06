<?php require_once('../../conectar_db.php');
?>
<html>
<title>Solicitud de Pr&eacute;stamo de Ficha</title>
<?php cabecera_popup('../..'); ?>
<script>
solicitar_ficha=function() {
	
	if($('pac_id').value==0 || $('doc_id').value==0 || $('esp_id').value==0 || $('amp_id').value==0){
		alert('Complete el formulario para solicitar la ficha.');
		return
	}
	
	var myAjax=new Ajax.Request(
		'sql_solicitar.php',
		{
			method:'post',
			parameters: $('datos').serialize(),
			onComplete:function(r) {
				resp = r.responseText;
				//alert(resp.unescapeHTML());
				//listar_nominas();
				if(trim(resp)=='') alert('Solicitud de pr&eacute;stamo enviada exitosamente.'.unescapeHTML());
				this.close();
			}
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
		<td class='tabla_fila2' style='text-align:right;'>Motivo Solicitud:</td>
		<td class='tabla_fila' colspan=2>

		<select id='amp_id' name='amp_id' style='font-size:18px;'>
		<option value=''>(Especifique el motivo de su solicitud...)</option>
		<?php 
			$amp=cargar_registros_obj("SELECT * FROM archivo_motivos_prestamo ORDER BY amp_id;", true);
			for($i=0;$i<sizeof($amp);$i++) {
				print("<option value='".$amp[$i]['amp_id']."'>".$amp[$i]['amp_nombre']."</option>");
			}
		?>
		</select>

		</td>
	</tr>


	
	<tr>
	<td colspan=3>

<center>
	<input type='button' id='' name='' value='[Enviar Solicitud]' onClick='solicitar_ficha();' style='font-size:20px;' />
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

		$('amp_id').value='';
		
}

</script>
