<?php

  
?>

<script>

  cargar_especialidades = function() {
  
    var myAjax = new Ajax.Updater(
    'especialidades',
    'agenda_medica/atencion/listar_especialidades.php', 
    {
      method: 'get',
      parameters: $('fecha').serialize()
    });
  
  }

  cargar_medicos = function(esp_id) {
  
    $('esp_id').value=esp_id;
  
    var myAjax = new Ajax.Updater(
    'medicos',
    'agenda_medica/atencion/listar_medicos.php', 
    {
      method: 'get',
      parameters: $('fecha').serialize()+'&esp_id='+(esp_id*1)
    });
  
  }

  cargar_atenciones = function(doc_id) {
  
    $('seleccion').style.display='none';
    $('atenciones').style.display='';
  
    $('lista_atenciones').innerHTML='<br><br><img src="imagenes/ajax-loader2.gif"><br><br>Cargando Atenciones...';
  
    var myAjax = new Ajax.Updater(
    'lista_atenciones',
    'agenda_medica/atencion/listar_atenciones.php', 
    {
      method: 'get',
      parameters: 'doc_id='+(doc_id*1)+'&esp_id='+$('esp_id').value+'&'+$('fecha').serialize()
    });
  
  }
  
  seleccionar_atenciones = function () {
  
    $('seleccion').style.display='';
    $('atenciones').style.display='none';
  
  }
  
  definir_registro = function(asigna_id) {
  
    l=(screen.availWidth/2)-325;
    t=(screen.availHeight/2)-200;
        
    params='asigna_id='+asigna_id;
        
    win = window.open('agenda_medica/atencion/definir_registro.php?'+params, 
                    'definir_registro',
                    'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=650, height=430');
                    
    win.focus();
    
  
  }

  abrir_ficha = function(id) {

			inter_ficha = window.open('interconsultas/visualizar_ic.php?tipo=inter_ficha&inter_id='+id,
			'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

			inter_ficha.focus();

  }



</script>

<center>
<div style="width:750px;" class="sub-content">
<div class="sub-content">
<img src="iconos/date.png">
<b>Atenci&oacute;n de Consultorio de Especialidades</b>
</div>

<div class="sub-content" id="seleccion">

<table>
<tr>
<td style="text-align:right;" valign="top">Fecha:</td>
<td>
<input type="text" id="fecha" name="fecha" style="text-align:center;"
size=10 value="<?php echo date("d/m/Y"); ?>" 
onChange="cargar_especialidades();">
<img src='iconos/date_magnify.png' id='fecha_boton'>
</td>
</tr>

<tr>
<td style="text-align:right;" valign="top">Especialidad:</td>
<td>
<div class="sub-content2" 
id="especialidades"
style="width:500px;height:100px;overflow:auto;">

</div>
</td>
</tr>

<tr>
<td style="text-align:right;" valign="top">M&eacute;dico:</td>
<td>
<div class="sub-content2" 
id="medicos"
style="width:500px;height:100px;overflow:auto;">

</div>

</td>
</tr>

</table>

</div>

<div class="sub-content" id="atenciones" style="display:none;">

<div class="sub-content2" id="lista_atenciones"
style="height:300px; overflow:auto;">

</div>

<center>
<input type="button" id="guardaatenciones" onClick="guardar_planilla();" value=" - Guardar Planilla de Atenci&oacute;n - ">
<input type="button" id="selatenciones" onClick="seleccionar_atenciones();" value=" - Seleccionar Listados - ">
</center>

</div>

</div>
</center>

<input type="hidden" id="esp_id" name="esp_id" value="">

<script>

    Calendar.setup({
        inputField     :    'fecha',         // id of the input field
        ifFormat       :    '%d/%m/%Y',       // format of the input field
        showsTime      :    false,
        button          :   'fecha_boton'
    });
    
    cargar_especialidades();

</script>
