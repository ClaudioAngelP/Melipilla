<?php 

    require_once('../../conectar_db.php');
    
    
?>

<html>
<title>Carga Masiva CSV - Monitoreo GES</title>

<?php cabecera_popup('../..'); ?>

<script>

    function iniciar_proceso() {
    
        if($('vigentes').value=='') {
            alert('Debe seleccionar planilla en formato compatible.');
            return;
        }
        
        $('boton').style.display='none';
        $('cargando').style.display='';
        
        $('monitoreo').submit();
    
    }

</script>

<body class='fuente_por_defecto popup_background'>

<form id='monitoreo' name='monitoreo' enctype='multipart/form-data'
action='sql_carga_masiva.php' method='POST' onSubmit='return false;'>

<div class='sub-content'>
<img src='../../iconos/cog_go.png'>
<b>Actualizar Masivamente Registro de Monitoreo GES</b>
</div>

<div class='sub-content'>

<center>

<table style='width:100%;'>

<tr><td colspan=2 style='text-align:center;'><br><b>Seleccione Planillas CSV</b></td></tr>

<tr>
<td style='text-align:right;'>Documento CSV Compatible:</td>
<td>
<input type='file' id='vigentes' name='vigentes'>
</td>
</tr>


<tr>
<td style='text-align:right;'>Tipo de Carga:</td>
<td>
<select id='tipo_carga' name='tipo_carga'>
<option value='0' SELECTED>Normal</option>
<option value='1'>Compra (Vicios de Refracci&oacute;n y Estrabismo)</option>
<option value='2'>Hist&oacute;rico Compras (Incl. Proveedor, Datos O.C.)</option>
<option value='3'>Hist&oacute;rico (Fecha Evento &gt; Fecha Registro)</option>
</select>
</td>
</tr>

</table>


<br>
<input type='button' value='Realizar Carga Masiva...' 
id='boton' onClick='iniciar_proceso();'>
<span id='cargando' style='display:none;'>
<img src='../../imagenes/ajax-loader3.gif'><br />Procesando Informaci&oacute;n...<br /><br /></span>

</center>

</div>

</form>

</body>
</html>
