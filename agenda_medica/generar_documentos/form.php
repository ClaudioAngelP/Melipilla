<?php

  require_once('../../conectar_db.php');

  //$esp_id=$_GET['esp_id']*1;
  $esp_id=-1;
  
  $especialidadhtml = desplegar_opciones("especialidades", 
	"esp_id, esp_desc",$esp_id,'true','ORDER BY esp_desc');   

?>

<html>
<title>Generar Citaciones/Planillas de Atenci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<script>

function generar_documentos() {

  window.resizeTo(750,550);
  window.moveTo((screen.width/2)-375,(screen.height/2)-275);

  $('datos').submit();

}

</script>

<body class='fuente_por_defecto popup_background'>

<form id='datos' name='datos' method='post' action='citaciones.php'>

<div class='sub-content'>
<img src='../../iconos/email_go.png'>
<b> Generar Citaciones/Planillas de Atenci&oacute;n </b>
</div>

<div class='sub-content'>

<table style='width:100%;'>

<tr>
<td style='text-align: right;width:150px;'>Documento:</td>
<td>

<select id='tipo' name='tipo'
onChange='
if(this.value=="C") {
  $("datos").action="citaciones.php";
  $("excluye").style.display="";
} else {
  $("datos").action="planillas.php";
  $("excluye").style.display="none";
}
'>
<option value='C' SELECTED>Citaciones</option>
<option value='P'>Planillas de Atenci&oacute;n</option>
</select>

</td>
</tr>


<tr>
<td style='text-align: right;'>(Sub)Especialidad/Procedimiento:</td>
<td>
<select id='esp_id' name='esp_id'>
<option value=-1 SELECTED>(Todas...)</option>
<?php echo $especialidadhtml; ?>
</select>

</td>
</tr>

  <tr><td style='text-align: right;'>Fecha Inicio:</td>
  <td><input type='text' name='fecha1' id='fecha1' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='../../iconos/date_magnify.png' id='fecha1_boton'></td></tr>
  <tr><td style='text-align: right;'>Fecha Final:</td>
  <td><input type='text' name='fecha2' id='fecha2' size=10
  style='text-align: center;' value='<?php echo date("d/m/Y")?>'>
  <img src='../../iconos/date_magnify.png' id='fecha2_boton'></td></tr>

<tr id='excluye'><td style='text-align:right;'>
<input type='checkbox' id='excluir' name='excluir' CHECKED>
</td><td>
Excluir citaciones ya impresas.
</td></tr>

<tr>
<td colspan=2 style='text-align:center;'>
<input type='button' onClick='generar_documentos();' value=' -- Generar Documento(s) -- '>
</td>
</tr>

</table>

</div>

</form>

</body>
</html>

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

  
  </script>

