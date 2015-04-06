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

if($_GET['tipo']=='cc') {
  $titulo='Cierre de Caso AUGE';
  $ingreso='Cierre de Caso AUGE ingresado exitosamente.';
} else {
  $titulo='Excepci&oacute;n de Garant&iacute;a';
  $ingreso='Excepci&oacute;n de Garant&iacute;a ingresada exitosamente.';  
}

?>

<script>

    var ramas=<?php echo json_encode($ramas); ?>;
    
    actualizar_ramas = function() {
    
      var pat_id=getRadioVal('cierre_cuerpo','pat_id');
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
    
    casos_auge = function() {
    
    	var myAjax2=new Ajax.Request(
      'prestaciones/casos_vigentes.php',
      {
        method:'post',
        parameters:'pac_id='+encodeURIComponent($('paciente_id').value),
        onComplete: function(dat) {
        
          d=dat.responseText.evalJSON(true);
          
          try {
          
          if(d) {
            
            var html='<table style="width:100%;">';
            
            for(var n=0;n<d.length;n++) {
            
              if(d.length==1) chk='CHECKED'; else chk='';
            
              html+='<tr><td style="width:20px;">';
              html+='<input type="radio" name="pat_id" ';
              html+=' onClick="actualizar_ramas();" '
              html+='value="P'+d[n].pat_id+'" '+chk+'>';
              html+='</td><td>'+d[n].pat_glosa+'</td></tr></table>';
            
            }
            
            html+='</table>';
            
            $('select_pat').innerHTML=html;
            
            if(d.length==1) actualizar_ramas();

          } else {
          
            $('select_pat').innerHTML='<i>Paciente no registra Casos AUGE vigentes.</i>';
          
          }

          } catch(err) {
            console.error(err);
          }
        
        }
      });

    
    }

       
    actualizar_form = function() {
    
      var c=getRadioVal("cierre_cuerpo", "causa")*1;
      
      if(c==1) { 
        $("fecha_defuncion").disabled=false; 
        $("fecha_defuncion_boton").style.display="";
      } else {
        $("fecha_defuncion").disabled=true; 
        $("fecha_defuncion_boton").style.display="none";    
      }  
      
      if(c>=9 && c<10) {
        $('semanas_gesta').disabled=false;
        $('fecha_parto').disabled=false;
        $('fecha_parto_boton').style.display='';
      } else {
        $('semanas_gesta').disabled=true;      
        $('fecha_parto').disabled=true;
        $('fecha_parto_boton').style.display='none';
      }
      
      if(c==25) {
        $('documento').disabled=false;
      } else {
        $('documento').disabled=true;
      }
      
      $('causal').value=c;
    
    }

		verifica_tabla_cierre = function() {
		
		  /*
		
			if(($('nro_folio').value*1)==0) {
				alert('N&uacute;mero de Folio incorrecto.'.unescapeHTML());
				return;
			}
			
			*/
			
			if(trim($('paciente_id').value)==0) {
				alert('Debe seleccionar un paciente.'.unescapeHTML());
				return;
			}

      if($('causal').value=='') {
      	alert('Debe seleccionar un causal de cierre.'.unescapeHTML());
				return;
      }
			
		
		  var params='tipo=<?php echo $_GET['tipo']; ?>&';
      params+=$('paciente').serialize()+'&'+$('cierre_cabecera').serialize();
		  params+='&'+$('cierre_cuerpo').serialize();
		
			var myAjax = new Ajax.Request(
			'prestaciones/formulario_cierre/sql.php', 
			{
				method: 'post', 
				parameters: params,
				onComplete: function (pedido_datos) {
				
				  if(pedido_datos.responseText=='OK') {
					
						alert('<?php echo $ingreso; ?>'.unescapeHTML());
						cambiar_pagina('prestaciones/formulario_cierre/form.php?tipo=<?php echo $_GET['tipo']; ?>');
						
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
<?php echo $titulo; ?>
</div>

		<form id='cierre_cabecera' name='cierre_cabecera' onSubmit='return false;'>		

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


<?php desplegar_ficha_basica('casos_auge();'); ?>

<form id='cierre_cuerpo' name='cierre_cuerpo' 
onChange='actualizar_form();' onSubmit='return false;'>

<input type='hidden' id='causal' name='causal' value=''>

<div class='sub-content'>
<div class='sub-content'>
<img src='iconos/layout.png'>
<b>Datos <?php echo $titulo; ?></b>
</div>
<div class='sub-content2'>
<table style="width:100%;">

<tr><td style="text-align:right">Problema de salud AUGE:</td>
<td id='select_pat'>
<i>(Seleccione Paciente...)</i>
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

<tr><td valign='top' style='text-align: right;'>Causa del Cierre de Caso:</td>
<td>

<table style='width:100%;'>
<tr><td colspan=2 style='font-weight:bold;'>A) Decisi&oacute;n Profesional Tratante</td></tr>
<tr><td colspan=2>
  <input type='radio' name='causa' value=12> 
  Criterios de Exclusi&oacute;n (seg&uacute;n protocolos)
</td></tr>
<tr><td colspan=2>
  <input type='radio' name='causa' value=5>  
  T&eacute;rmino de Tratamiento
</td></tr>
<tr><td>
  <input type='radio' name='causa' value='9.3'> 
  Parto a T&eacute;rmino
</td>
<td rowspan=3>
<table><tr><td style='text-align:right;'>
Fecha del Parto/Aborto:</td><td>
<input type='text' size=10 id='fecha_parto' name='fecha_parto' style='text-align:center;' value="<?php echo date('d/m/Y'); ?>" DISABLED>
<img src='iconos/date_magnify.png' id='fecha_parto_boton' style='display:none;'></td></tr><tr>
<td style='text-align:right;'>Semanas de Gestaci&oacute;n:</td><td>
<input type='text' size=5 id='semanas_gesta' name='semanas_gesta' DISABLED></td></tr>
</table>
</td>
</tr>
<tr><td>
  <input type='radio' name='causa' value='9.4'> 
  Parto Pret&eacute;rmino
</td></tr>
<tr><td>
  <input type='radio' name='causa' value='9.6'> 
  Aborto
</td></tr>

<tr><td colspan=2 style='font-weight:bold;'>B) Relacionado con el Seguro</td></tr>

<tr><td>
  <input type='radio' name='causa' value=6> 
  T&eacute;rmino de Garant&iacute;a
</td></tr>
<tr><td>
  <input type='radio' name='causa' value=4>  
  Cambio de Previsi&oacute;n
</td></tr>

<tr><td colspan=2 style='font-weight:bold;'>C) Fallecimiento</td></tr>

<tr><td>
  <input type='radio' name='causa' value=1> 
  Fallecimiento
</td><td>
<table><tr><td style='text-align:right;'>
Fecha defunci&oacute;n:</td><td>
<input type='text' size=10 id='fecha_defuncion' name='fecha_defuncion' style='text-align:center;' value="<?php echo date('d/m/Y'); ?>" DISABLED>
<img src='iconos/date_magnify.png' id='fecha_defuncion_boton' style='display:none;'></td></tr>
</table>
</td></tr>

<tr><td colspan=2 style='font-weight:bold;'>D) Causas atribuibles al paciente o sus representantes</td></tr>

<tr><td colspan=2>
  D.1) <input type='radio' name='causa' value=24> 
  Por inasistencia (seg&uacute;n protocolo)
</td></tr>
<tr><td colspan=2>
  D.2) Por expresi&oacute;n de la voluntad del paciente o de sus representantes<br>
<table>
<tr><td><input type='radio' name='causa' value=3>Por rechazo del prestador designado</td></tr>
<tr><td><input type='radio' name='causa' value=13>Por rechazo del tratamiento</td></tr>
<tr><td><input type='radio' name='causa' value=25>Por otra causa</td></tr>
<tr><td style='text-align:right;'>Acompa&ntilde;a documento:</td>
<td><select id='documento' name='documento'><option value='0' SELECTED>No</option><option value='1'>Si</option></select></td></tr>

</table>

  
</td></tr>
  

</td></tr>
</table>

</td></tr>

<tr><td valign='top' style='text-align: right;'>Observaciones:</td>
<td><textarea cols=50 rows=6 id='cierre_observaciones' name='cierre_observaciones'></textarea></td></tr>

</table>
</div>
</div>

</form>

<div class='sub-content'>
<center>
	<table><tr><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/accept.png'>
		</td><td>
		<a href='#' onClick='verifica_tabla_cierre();'>Ingresar Cierre de Caso...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina("prestaciones/formulario_cierre/form.php");'>
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
        inputField     :    'fecha_parto',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha_parto_boton'
    });

    Calendar.setup({
        inputField     :    'fecha_defuncion',
        ifFormat       :    '%d/%m/%Y',
        showsTime      :    false,
        button          :   'fecha_defuncion_boton'
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
      
		
      $('paciente_id').observe('change', casos_auge);

</script>
