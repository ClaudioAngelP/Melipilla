<?php
require_once('../../conectar_db.php');

$servs="'".str_replace(',','\',\'',_cav2(51))."'";
$servicioshtml = desplegar_opciones_sql( 
?>
<script>
    casos_auge = function() {
    	var myAjax2=new Ajax.Request(
          d=dat.responseText.evalJSON(true);
          try {
          if(d) {
            var html='<select id="ca_id" name="ca_id">';
            html+='<option value="0" SELECTED>(Evento sin caso...)</option>';
            for(var n=0;n<d.length;n++) {
              html+='<option value="'+d[n].id_sigges+'">';
            }
            html+='</select>';
            $('select_pat').innerHTML=html;
            //if(d.length==1) actualizar_ramas();
          } else {
            $('select_pat').innerHTML='<i>Paciente no registra Casos AUGE vigentes.</i>';
          }
          } catch(err) {
            console.error(err);
          }
        }
      });
    }

	verifica_tabla_oa = function() {
			if($('esp_id').value*1==0) {
			if($('paciente_id').value*1==0) {
		  var params=$('paciente').serialize();
		  params+='&'+$('orden_cabecera').serialize();
			var myAjax = new Ajax.Request(
			{
				  if(pedido_datos.responseText=='OK') {
						alert('Orden de Atenci&oacute;n registrada exitosamente.'.unescapeHTML());
						cambiar_pagina("prestaciones/orden_atencion/form.php");
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
</div>

<form id='orden_cabecera' name='orden_cabecera' onSubmit='return false;'>		

	<div class='sub-content'>
		
	<div align='right'>
	<table style='width:100%;'><tr>
    <td style='text-align: right;'>Unidad/Poli Solicitante:</td>
		<td width='45%' style='text-align: left;'>
		<input type='hidden' id='esp_id' name='esp_id' value=''>
    <input type='text' id='esp_desc' name='esp_desc' value='' size=45>
    </td>
		<td style='text-align: right;width:100%;'><b>N&uacute;mero Folio:</b></td>
		<td><input type='text' name='nro_folio' id='nro_folio' size=12
		style='text-align:center;'></td></tr>
	<tr>
	<td style='width:100px;text-align:right;' >Servicio Cl&iacute;nico:</td>
    <td>
    <select id='centro_ruta' name='centro_ruta'>
    <?php echo $servicioshtml; ?>
    </select>
    </td>
	<td style='text-align:right;'>
    Fecha O.A.:
    </td><td>
    <input type='text' size=8 style='text-align:center;' 
    id='fecha' name='fecha' value='<?php echo date('d/m/Y'); ?>' />
    <img src='iconos/calendar.png' id='fecha_boton' />
    </td>
	</tr>
	</table>
	</div>
		
		</div>
		
		</form>


<?php desplegar_ficha_basica('casos_auge();'); ?>

<form id='orden_cuerpo' name='orden_cuerpo' 
<div class='sub-content'>
<div class='sub-content'>
<img src='iconos/layout.png'>
<b>Datos de la Orden de Atenci&oacute;n</b>
</div>
<div class='sub-content2'>
<table style="width:100%;">
<tr><td style="text-align:right;width:150px;" class='tabla_fila2'>Problema de salud AUGE:</td>
<td id='select_pat' class='tabla_fila'>
<i>(Seleccione Paciente...)</i>
</td></tr>

<tr>
<td style='text-align:right;' class='tabla_fila2'>
C&oacute;digo Prestaci&oacute;n:
</td>
<td class='tabla_fila'>
<input type='hidden' id='codigo_prestacion' name='codigo_prestacion'
value=''>
<input type='text' id='cod_presta' name='cod_presta'>
</td>
</tr>
<tr><td class='tabla_fila2' style='text-align:right;' valign='top'>Descripci&oacute;n Prestaci&oacute;n:</td>
<td class='tabla_fila' id='desc_presta' style='text-align:justify;' valign='top'>
</td>
<tr><td style='width:100px;text-align:right;' class='tabla_fila2'>
M&eacute;dico Solicitante:
</td><td class='tabla_fila' style='text-align:left;'>
<input type='hidden' id='doc_id' name='doc_id' value=''>
<input type='text' id='rut_medico' name='rut_medico' size=10
style='text-align: center;' value='' disabled>
<input type='text' id='nombre_medico' 
value='' name='nombre_medico' size=45>
</td></tr>

<tr>
<td valign='top' style='text-align: right;' class='tabla_fila2'>Hop&oacute;tesis Diag.:</td>
<td class='tabla_fila'><textarea cols=50 rows=6 id='oa_hipotesis' name='oa_hipotesis'></textarea></td>
</tr>

<tr>
<td valign='top' style='text-align: right;' class='tabla_fila2'>Fecha de Atenci&oacute;n:</td>
<td class='tabla_fila'>
<select id='fecha_aten' name='fecha_aten'>
<?php for($i=0;$i<=12;$i++) { ?>
	<option value='<?php echo $i?>'><?php echo $i.' '.(($i<=1)?'mes':'meses'); ?></option>
<?php } ?>

</select></td>
</tr>


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
		<a href='#' onClick='verifica_tabla_oa();'>Ingresar Orden de Atenci&oacute;n...</a>
		</td></tr></table>
		</div>
		</td><td>
		<div class='boton'>
		<table><tr><td>
		<img src='iconos/delete.png'>
		</td><td>
		<a href='#' onClick='cambiar_pagina("prestaciones/orden_atencion/form.php");'>
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
        inputField     :    'fecha',
    });
    
    seleccionar_especialidad = function(d) {
      $('esp_id').value=d[0];
    }
    seleccionar_patologia = function(d) {
      $('pat_id').value=d[0];
      actualizar_ramas();
    }
    
        return {
      }, 'autocomplete', 350, 100, 150, 1, 3, seleccionar_especialidad);

    seleccionar_prestacion = function(presta) {
      $('codigo_prestacion').value=presta[0];
    }

    lista_prestaciones=function() {
        if($('cod_presta').value.length<3) return false;
        var params='tipo=prestacion&'+$('cod_presta').serialize();
        return {
    }

    autocompletar_prestaciones = new AutoComplete(
          
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

    $('paciente_id').observe('change', casos_auge);

</script>