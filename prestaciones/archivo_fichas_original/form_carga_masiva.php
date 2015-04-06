<center>
<div class='sub-content' style='width:750px;'>
<div class='sub-content'>
<img src='iconos/application_get.png' />
<b>Carga Masiva de N&oacute;minas</b>
</div>
<center>
<form id='datos' name='datos' method='post' action='prestaciones/archivo_fichas/sql_carga_masiva.php' target='hidden_iframe' enctype="multipart/form-data">
<table style='width:100%;'>
<tr><td style='text-align:right;'>Tipo Archivo:</td><td>
<select id='tipo' name='tipo'>
<option value='0'>Tablas I.Q.</option>
<option value='1'>Solicitud Masiva de Fichas</option>
</td></tr>
<tr><td style='text-align:right;'>Archivo:</td><td>
<input type='file' id='archivo' name='archivo' />
</td></tr>
</table>
<input type='button' value='[[ PROCESAR ARCHIVO ]]' onClick='$("datos").submit();' />
</form>
<iframe id='hidden_iframe' name='hidden_iframe' style='width:90%;height:300px;'>
</iframe>
</center>
</center>
