<?php

require_once('../../conectar_db.php');
require_once('../../ficha_clinica/ficha_basica.php');

$servs="'".str_replace(',','\',\'',_cav2(50))."'";

$servicioshtml = desplegar_opciones_sql( 
  "SELECT centro_ruta, centro_nombre FROM centro_costo WHERE
  length(regexp_replace(centro_ruta, '[^.]', '', 'g'))=3 AND
          centro_medica AND centro_ruta IN (".$servs.")
  ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;"); 

$ramas = cargar_registros_obj("
  SELECT * FROM patologias_auge_ramas ORDER BY rama_nombre
");


?>

<script>

    var ramas=<?php echo json_encode($ramas); ?>;
    
    actualizar_ramas = function() {
    
      var pat_id=$('pat_id').value;
      var c=0;
      
      var s='<select id="patrama_id" name="patrama_id">';
      
      if(pat_id.charAt(0)=='P') {
      
      pat_id=pat_id.replace('P','');
      
      for(var i=0;i<ramas.length;i++) {
      
        if(ramas[i].pat_id==pat_id) {
          c++;
          s+='<option value="'+ramas[i].patrama_id+'">'+ramas[i].rama_nombre+'</option>';
        }
      
      }
      
      }
      
      if(!c)
          s+='<option value="0">(No posee ramas...)</option>';
      
      s+='</select>';
    
      $('patrama').innerHTML=s;
    
    }

		verifica_tabla_ipd = function() {
		
			if(trim($('paciente_id').value)==0) {
				alert('Debe seleccionar un paciente.'.unescapeHTML());
				return;
			}
			
			if(trim($('ipd_fundamentos').value)=='') {
				alert('Fundamento Cl&iacute;nico del IPD est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}

			if(trim($('ipd_tratamiento').value)=='') {
				alert('Tratamiento del IPD est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
		
		  var params=$('paciente').serialize()+'&'+$('ipd_cabecera').serialize();
		  params+='&'+$('ipd_cuerpo').serialize();
		
			var myAjax = new Ajax.Request(
			'prestaciones/formulario_ipd/sql.php', 
			{
				method: 'post', 
				parameters: params,
				onComplete: function (pedido_datos) {
				
				  if(pedido_datos.responseText=='OK') {
					
						alert('Informe Proceso de Diagn&oacute;stico ingresado exitosamente.'.unescapeHTML());
						cambiar_pagina('prestaciones/formulario_ipd/form.php');
						
					} else {
					
						alert('ERROR:\\n'+pedido_datos.responseText.unescapeHTML());
						
					}
				}
			}
			
			);
		
		}


</script>

<center>
<div class='sub-content' style="width:700px;">

<div class='sub-content' 
style='background-color:#cccccc;text-align:center;font-weight:bold;'>
Informe de Proceso de Diagn&oacute;stico
</div>

		<form id='ipd_cabecera' name='ipd_cabecera' onSubmit='return false;'>		

		<div class='sub-content'>
		
		<div align='right'>
		<table width=630><tr>
		<td style='width:100px;text-align:right;' >Servicio:</td>
    <td>
    <select id='centro_ruta' name='centro_ruta' onChange='listar_prestaciones();'>
    <?php echo $servicioshtml; ?>
    </select>
    </td>
		<td style='text-align: right;'><b>N&uacute;mero Folio:</b></td>
		<td><input type='text' name='nro_folio' id='nro_folio' size=8
		style='text-align: right;'></td></tr>
		<tr>
    <td style='text-align: right;'>Especialidad:</td>
		<td width=55% style='text-align: left;'>
		<input type='hidden' id='esp_id' name='esp_id' value=''>
    <input type='text' id='esp_desc' name='esp_desc' value='' size=40>
    </td>
		
		</tr>
		</table>
		</div>
		
		</div>
		
		</form>


<?php desplegar_ficha_basica(); ?>

<form id='ipd_cuerpo' name='ipd_cuerpo' onSubmit='return false;'>

<div class='sub-content'>
<div class='sub-content'>
<img src='iconos/layout.png'>
<b>Datos del Informe de Proceso de Diagn&oacute;stico</b>
</div>
<div class='sub-content2'>
<table style="width:100%;">

<tr><td style="text-align:right">&iquest;Confirma diagn&oacute;stico AUGE?:</td>
<td><select id='confirma' name='confirma'>
<option value='t'>S&iacute;</option>
<option value='f'>No</option>
</select>
</td></tr>

<tr><td style="text-align:right">Problema de salud AUGE:</td>
<td>
<input type='hidden' id='pat_id' name='pat_id' value=''>
<input type='text' id='pat_desc' name='pat_desc' value='' size=60>
</td></tr>

<tr><td style="text-align:right">Subgrupo o subproblema de salud AUGE:</td>
<td>
<div id='patrama'>
<select id='patrama_id' name='patrama_id'>
<option value=-1>(Seleccione Patolog&iacute;a...)</option>
</select>
</div>
</td></tr>

      <tr><td style='text-align: right;'>C&oacute;digo Diag&oacute;stico:</td><td>
      <input type='text' id='diag_cod' name='diag_cod' 
      style='text-align:center;' size=10>
      </td></tr>
      
      <tr>
      <td style='text-align: right;'>Diagn&oacute;stico:</td>
      <td width=70% style='text-align:left;'>
      <span id='diagnostico' style='font-weight: bold;'>
      (No Asociado...)
      </span>
      </td></tr>


<tr><td valign='top' style='text-align: right;'>Fundamentos del(los) Diagn&oacute;sticos:</td>
<td><textarea cols=50 rows=6 id='ipd_fundamentos' name='ipd_fundamentos'></textarea></td></tr>
<tr><td valign='top' style='text-align: right;'>Tratamiento e Indicaciones:</td>
<td><textarea cols=50 rows=6 id='ipd_tratamiento' name='ipd_tratamiento'></textarea></td></tr>

<tr><td style="text-align:right">Iniciar tratamiento a mas tardar el:</td>
<td><input type='text' size=10 style="text-align:center;" value='<?php echo date('d/m/Y'); ?>'
id='ipd_fecha_tratamiento' name='ipd_fecha_tratamiento'>
<img src='iconos/date_magnify.png' id='fecha_boton'>
</td></tr>

</table>
</div>
</div>

<div class='sub-content'>
<center>
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla_ipd();'>Ingresar IPD...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina("prestaciones/formulario_ipd/form.php");'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
	</td></tr></table>
</center>
</div>


</div>

</center>

<script>

    Calendar.setup({
        inputField     :    'ipd_fecha_tratamiento',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha_boton'
    });
    
    seleccionar_especialidad = function(d) {
    
      $('esp_id').value=d[0];
      $('esp_desc').value=d[2].unescapeHTML();
    
    }

    seleccionar_patologia = function(d) {
    
      $('pat_id').value=d[0];
      $('pat_desc').value=d[2].unescapeHTML();
      
      actualizar_ramas();
    
    }

    seleccionar_diagnostico = function(d) {
    
      $('diag_cod').value=d[0];
      $('diagnostico').innerHTML='['+d[0]+'] '+d[2];
    
    }
    
    autocompletar_especialidades = new AutoComplete(
      'esp_desc', 
      'autocompletar_sql.php',
      function() {
        if($('esp_desc').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=especialidad&'+$('esp_desc').serialize()
        }
      }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad);

    autocompletar_patologias = new AutoComplete(
      'pat_desc', 
      'autocompletar_sql.php',
      function() {
        if($('pat_desc').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=garantias_patologias&'+$('pat_desc').serialize()
        }
      }, 'autocomplete', 400, 100, 150, 1, 3, seleccionar_patologia);

    autocompletar_diagnostico = new AutoComplete(
      'diag_cod', 
      'autocompletar_sql.php',
      function() {
        if($('diag_cod').value.length<3) return false;
      
        return {
          method: 'get',
          parameters: 'tipo=diagnostico&cadena='+encodeURIComponent($('diag_cod').value)
        }
      }, 'autocomplete', 350, 200, 150, 1, 3, seleccionar_diagnostico);

    

</script>
