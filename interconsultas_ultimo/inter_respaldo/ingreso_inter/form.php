<?php
require_once('../../conectar_db.php');
require_once('../../ficha_clinica/ficha_basica.php');
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
	
		
		verifica_tabla_inter = function() {
		
		  if($('inst_id1').value=='' || $('inst_id2').value=='') {
      	alert('Debe seleccionar Instituciones de Or&iacute;gen y de Destino.'.unescapeHTML());
				return;
      }
		
			if(trim($('paciente_id').value)==0) {
				alert('Debe seleccionar un paciente.'.unescapeHTML());
				return;
			}

			if($('esp_id').value=='') {
				alert('Debe seleccionar Especialidad.'.unescapeHTML());
				return;
			}
			
			if(trim($('inter_funda').value)=='') {
				alert('Fundamento Cl&iacute;nico de la Interconsulta est&aacute; vac&iacute;o.'.unescapeHTML());
				return;
			}
		
		  var params=$('paciente').serialize()+'&'+$('ic_cabecera').serialize();
		  params+='&'+$('ic_cuerpo').serialize();
		
			var myAjax = new Ajax.Request(
			'interconsultas/ingreso_inter/sql.php', 
			{
				method: 'post', 
				parameters: params,
				onComplete: function (pedido_datos) {
				
				  if(pedido_datos.responseText=='OK') {
					
						alert('Interconsulta ingresada exitosamente.');
						cambiar_pagina('interconsultas/ingreso_inter/form.php');
						
					} else {
					
						alert('ERROR:\\n'+pedido_datos.responseText.unescapeHTML());
						
					}
				}
			}
			
			);
		
		}
		
    verificar_rut_prof = function() {
  
      $('prof_rut').value=trim($('prof_rut').value);
  
      if(comprobar_rut($('prof_rut').value)) {
      
        $('prof_rut').style.background='inherit';
        
        cargar_profesional_externo();
      
      } else {
      
        $('prof_rut').style.background='red';
      
      }
  
    }
    
    cargar_profesional_externo = function() {
    
      var myAjax=new Ajax.Request(
      'interconsultas/profesional_externo.php',
      {
        method:'get', parameters: $('prof_rut').serialize(),
        onComplete: function(resp) {
        
          prof=resp.responseText.evalJSON(true);
          
          if(prof) {
          
            $('prof_id').value=prof.prof_id;
            $('prof_paterno').value=prof.prof_paterno;
            $('prof_materno').value=prof.prof_materno;
            $('prof_nombres').value=prof.prof_nombres;
            
          
          } else {
          
            $('prof_id').value=0;
            $('prof_paterno').value='';
            $('prof_materno').value='';
            $('prof_nombres').value='';
          
          }
        
        }
      });
    
    }
    
    

		
		$('nro_folio').focus();
		
		</script>
				
		<center>

    <div class='sub-content' style="width:700px;">
    
    <div class='sub-content' 
    style='background-color:#cccccc;text-align:center;font-weight:bold;'>
    Solicitud de Interconsulta
    </div>

		<form id='ic_cabecera' name='ic_cabecera' onSubmit='return false;'>		

		<div class='sub-content'>
		
		<div align='right'>
		<table style='width:100%;'>
    <tr>
		<td style='text-align: right;'>Inst. Solicitante:</td>
		<td width=55% style='text-align: left;'>
		<input type='hidden' id='inst_id1' name='inst_id1' value=''>

		<input type='text' id='institucion1' name='institucion1' size=40>

		</td>
		<td style='text-align: right;'><b>N&uacute;mero Folio:</b></td>
		<td><input type='text' name='nro_folio' id='nro_folio' size=8
		style='text-align: right;'></td>
    </tr>
		
    <tr>
		<td style='text-align: right;'>Inst. Receptora:</td>
		<td width=55% style='text-align: left;'>
		<input type='hidden' id='inst_id2' name='inst_id2' value=''>

		<input type='text' id='institucion2' name='institucion2' size=40>


		</td>
		<td style='text-align: right;'><b>Fecha:</b></td>
		<td>
    <?php echo date('d/m/Y'); ?>
    </td>
    </tr>
		
    </table>
		</div>
		
		</div>
		
		</form>
		
    <!------ Función Despliegue de FICHA BÁSICA ---->

    <?php desplegar_ficha_basica(); ?>
		
		<!------ Datos de INTERCONSULTA ----->
		
<div class='sub-content'>
<div class='sub-content'><img src='iconos/chart_organisation.png'> <b>Datos de Interconsulta</b></div>

<form id='ic_cuerpo' name='ic_cuerpo' onSubmit='return false;'>

<div class='sub-content2'>
<center>
<table>
<tr>
<td style='text-align: right;'>Especialidad Cl&iacute;nica:</td>
<td>
<input type='hidden' id='esp_id' name='esp_id' value=''>
<input type='text' id='esp_desc' name='esp_desc' value='' size=40>
</td></tr>

<tr>
<td style='text-align:right;'>Se env&iacute;a consulta para:</td>
<td>
<select id='motivo' name='motivo'>
<option value=0>Confirmaci&oacute;n Diagn&oacute;stica</option>
<option value=1>Realizar Tratamiento</option>
<option value=2>Seguimiento</option>
<option value=3>Control Especialidad</option>
<option value=4>Otro Motivo</option>
</select>
</td>
</tr>

      <tr><td style='text-align: right;'>C&oacute;digo Diag. Presuntivo:</td><td>
      <input type='text' id='diag_cod' name='diag_cod' 
      style='text-align:center;' size=10>
      </td></tr>
      
      <tr>
      <td style='text-align: right;'>Diagn&oacute;stico Presuntivo:</td>
      <td width=70% style='text-align:left;'>
      <span id='diagnostico' style='font-weight: bold;'>
      (No Asociado...)
      </span>
      </td></tr>
      
<tr><td valign='top' style='text-align: right;'>&iquest;Sospecha problema AUGE?</td>
<td>
<input type='checkbox' id='' name='' onClick='
if(!this.checked) {
  $("pat_desc").disabled=true; $("pat_id").value="G1"; 
  $("pat_desc").value=""; actualizar_ramas();
} else {
  $("pat_desc").disabled=false; $("pat_id").value=""; $("pat_desc").focus();
}
' CHECKED> Sospecha Patolog&iacute;a AUGE
</td>
</tr>
<tr><td></td>
<td>
<input type='hidden' id='pat_id' name='pat_id' value=''>
<input type='text' id='pat_desc' name='pat_desc' value='' size=60>
</td>
</tr>

<tr><td valign='top' style='text-align: right;'>Subgrupo o subproblema AUGE:</td>
<td>
<div id='patrama'>
<select id='patrama_id' name='patrama_id'>
<option value=-1>(Seleccione Patolog&iacute;a...)</option>
</select>
</div>
</td></tr>


<tr><td valign='top' style='text-align: right;'>Fundamentos Cl&iacute;nicos:</td>
<td><textarea cols=50 rows=6 id='inter_funda' name='inter_funda'></textarea></td></tr>
<tr><td valign='top' style='text-align: right;'>Ex&aacute;menes Complementarios:</td>
<td><textarea cols=50 rows=6 id='inter_examen' name='inter_examen'></textarea></td></tr>
<tr><td valign='top' style='text-align: right;'>Comentarios:</td>
<td><textarea cols=50 rows=6 id='inter_comenta' name='inter_comenta'></textarea></td></tr>

</table>
		</center>
		</div>
		
	<div class='sub-content'><img src='iconos/user_comment.png'> <b>Datos del Profesional</b></div>

<div class="sub-content2">

<center>
<table border=0>
<tr>
<td style='font-weight: bold;text-align:center;'>R.U.T.</td>
<td style='text-align:center;'>Nombre(s)</td>
<td style='text-align:center;'>Apellido Paterno</td>
<td style='text-align:center;'>Apellido Materno</td>
</tr>
<tr>
<td width=100>
<input type='hidden' id='prof_id' name='prof_id' value='0'>
<input type='text' id='prof_rut' name='prof_rut' size=11
style='text-align: center;font-size:13px;' onKeyUp='
if(event.which==13) { 
  this.value=this.value.toUpperCase();
  verificar_rut_prof();
  $("prof_nombres").focus();
}
' onBlur='verificar_rut_prof();' maxlength=11>
</td>
<td><input type='text' id='prof_nombres' name='prof_nombres' size='22' onKeyUp='
if(event.which==8 && this.value.length==0) $("prof_rut").focus();
' maxlength=100></td>
<td><input type='text' id='prof_paterno' name='prof_paterno' size='22'
onKeyUp='
if(event.which==8 && this.value.length==0) $("prof_nombre").focus();
' maxlength=50></td>
<td><input type='text' id='prof_materno' name='prof_materno' size='22'
onKeyUp='
if(event.which==8 && this.value.length==0) $("prof_paterno").focus();
' maxlength=50></td>
</tr>
</table>


</div>

</form>
		
</div>
		
<div class='sub-content'>
<center>
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla_inter();'>Ingresar Interconsulta...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina("interconsultas/ingreso_inter/form.php");'>
		Limpiar Formulario...</a>
		</td></tr></table>
		</div>
	</td></tr></table>
</center>
</div>
		
		</div>
		
		</center>
		
		
<script>

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
    seleccionar_inst1 = function(d) {
    
      $('inst_id1').value=d[0];
      $('institucion1').value=d[2].unescapeHTML();
    
    }

    seleccionar_inst2 = function(d) {
    
      $('inst_id2').value=d[0];
      $('institucion2').value=d[2].unescapeHTML();
    
    }
    
    autocompletar_institucion1 = new AutoComplete(
      'institucion1', 
      'autocompletar_sql.php',
      function() {
        if($('institucion1').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('institucion1').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst1);


      autocompletar_institucion2 = new AutoComplete(
      'institucion2', 
      'autocompletar_sql.php',
      function() {
        if($('institucion2').value.length<3) return false;

        return {
          method: 'get',
          parameters: 'tipo=instituciones&cadena='+encodeURIComponent($('institucion2').value)
        }
      }, 'autocomplete', 350, 200, 150, 2, 3, seleccionar_inst2);

</script>
		

