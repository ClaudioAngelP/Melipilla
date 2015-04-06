<?php 

  require_once('../../conectar_db.php');

?>

<html>
<title>Crear N&oacute;mina de Atenci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<script>

var proc=false;

function guardar_nomina() {

	if($('esp_id').value*1==0) {
		alert('ERROR: Debe seleccionar una especialidad.');
		return;
	}	
	
	if(!$('proc').checked && $('doc_id').value*1==0) {
		alert('ERROR: Debe seleccionar un profesional tratante.');
		return;
	}	
	
	var conf=confirm('&iquest;Desea generar una nueva n&oacute;mina?'.unescapeHTML());
	
	if(!conf) return;	
	
	var myAjax=new Ajax.Request(
		'sql_nomina.php',
		{
			method:'post',
			parameters:$('fecha').serialize()+'&'+$('esp_id').serialize()+
							'&'+$('doc_id').serialize()+'&'+$('proc').serialize(),
			onComplete:function(r) {
				
				var folio=r.responseText.evalJSON(true);
				
				var fn=window.opener.abrir_nomina.bind(window.opener);
				fn(folio, 1);
				window.close();
					
			}	
		}	
	);	
	
}

</script>

<body class='fuente_por_defecto cabecera_popup'>

<input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />

<div class='sub-content'>
<img src='../../iconos/disk_multiple.png' /> 
<b>Crear N&oacute;mina de Atenci&oacute;n</b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align:right;width:150px;'>Fecha:</td>
<td>
<input type='text' id='fecha'  name='fecha' style='text-align:center;'
value='<?php echo date("d/m/Y"); ?>' size=10>
<img src='../../iconos/date_magnify.png' id='fecha_boton'>
</td>
</tr>


<tr>
<td style='text-align:right;'>Especialidad:</td>
<td>
<input type='hidden' id='esp_id' name='esp_id' value=''>
<input type='text' id='especialidad' 
value='' name='especialidad' size=35>

</td>
</tr>

<tr id='rut_tr'>
<td style='text-align:right;'>R.U.T. Profesional:</td>
<td>
<input type='text' id='rut_medico' name='rut_medico' size=10
style='text-align: center;' value='' disabled>
</td></tr>

<tr id='nom_tr'>
<td style='text-align:right;'>Profesional Tratante:</td>
<td>
<input type='hidden' id='doc_id' name='doc_id' value=''>
<input type='text' id='nombre_medico' 
value='' name='nombre_medico' size=35>
</td>
</tr>
<tr>
<td></td><td>
<input type='checkbox' id='proc' name='proc' 
onClick='fix_fields();' />
<b>N&oacute;mina de Procedimientos/Ex&aacute;menes.</b>
</td></tr>


</table>

<center>
<br /><br />
<input type='button' id='copiar' name='copiar' onClick='guardar_nomina();'
value='-- Crear Nueva N&oacute;mina... --' />

</center>

</div>

</body>
</html>

<script>

		fix_fields=function() {
			if($('proc').checked) {
				$('doc_id').value='';
				$('rut_medico').value='';
				$('nombre_medico').value='';
				$('nombre_medico').disabled=true;
			} else {
				$('nombre_medico').disabled=false;
			}				
		}

      ingreso_especialidades=function(datos_esp) {

      	$('esp_id').value=datos_esp[0];
      	$('especialidad').value=datos_esp[2].unescapeHTML();
      	
      }

      autocompletar_especialidades = new AutoComplete(
      	'especialidad', 
      	'../../autocompletar_sql.php',
      function() {
        if($('especialidad').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=subespecialidad_nominas&cadena='+encodeURIComponent($('especialidad').value)
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_especialidades);


      ingreso_rut=function(datos_medico) {
      	$('doc_id').value=datos_medico[3];
      	$('rut_medico').value=datos_medico[1];
      }

      autocompletar_medicos = new AutoComplete(
      	'nombre_medico', 
      	'../../autocompletar_sql.php',
      function() {
        if($('nombre_medico').value.length<3) return false;
        
        return {
          method: 'get',
          parameters: 'tipo=medicos&'+$('nombre_medico').serialize()+'&receta=false'
        }
      }, 'autocomplete', 350, 200, 250, 1, 2, ingreso_rut);
      
      $('especialidad').focus();


    Calendar.setup({        inputField     :    'fecha',         // id of the input field        ifFormat       :    '%d/%m/%Y',       // format of the input field        showsTime      :    false,        button          :   'fecha_boton'
    });

</script>
