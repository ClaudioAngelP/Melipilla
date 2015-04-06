<?php 

    require_once('../../conectar_db.php');
    
    
?>

<html>
<title>Abrir Proceso de Monitoreo GES</title>

<?php cabecera_popup('../..'); ?>

<script>

    function iniciar_proceso() {
    
        if($('vigentes').value=='') {
            alert('Debe seleccionar planilla de garantias vigentes.');
            return;
        }
        
        $('boton').style.display='none';
        $('cargando').style.display='';
        
        $('monitoreo').submit();
    
    }


    function iniciar_proceso2() {
    
        if($('cerradas').value=='') {
            alert('Debe seleccionar la planillas solicitada.');
            return;
        }
        
        $('boton2').style.display='none';
        $('cargando2').style.display='';
        
        $('monitoreo2').submit();
    
    }

</script>

<body class='fuente_por_defecto popup_background'>

<form id='monitoreo' name='monitoreo' enctype='multipart/form-data'
action='proceso.php' method='POST' onSubmit='return false;'>

<div class='sub-content'>
<img src='../../iconos/book_open.png'>
<b>Actualizar Diaria Registro de Monitoreo GES</b>
</div>

<div class='sub-content'>

<center>

<table style='width:100%;'>

<tr><td colspan=2 style='text-align:center;'><br><b>CARGA DIARIA DE GARANT&Iacute;AS<br/>Seleccione Planillas SIGGES</b></td></tr>

<tr>
<td style='text-align:right;'>Garant&iacute;as Vigentes:</td>
<td>
<input type='file' id='vigentes' name='vigentes'>
</td>
</tr>

<tr>
<td style='text-align:right;'>Garant&iacute;as Vencidas:</td>
<td>
<input type='file' id='vencidas' name='vencidas'>
</td>
</tr>

<!--- <tr>
<td style='text-align:right;'>Garant&iacute;as Cerradas:</td>
<td>
<input type='file' id='cerradas' name='cerradas'>
</td>
</tr> --->


</table>


<br>
<input type='button' value='Realizar Actualizaci&oacute;n...' 
id='boton' onClick='iniciar_proceso();'>
<span id='cargando' style='display:none;'>
<img src='../../imagenes/ajax-loader3.gif'><br />Procesando Informaci&oacute;n...<br /><br /></span>

</center>

</div>

</form>




<hr>




<form id='monitoreo2' name='monitoreo2' enctype='multipart/form-data'
action='proceso_cerradas.php' method='POST' onSubmit='return false;'>

<div class='sub-content'>
<img src='../../iconos/book.png'>
<b>Ingresar Garant&iacute;as Cerradas - Monitoreo GES</b>
</div>

<div class='sub-content'>

<center>

<table style='width:100%;'>

<tr><td colspan=2 style='text-align:center;'><br><b>Seleccione Planilla SIGGES</b></td></tr>

<tr>
<td style='text-align:right;'>Garant&iacute;as Cerradas:</td>
<td>
<input type='file' id='cerradas' name='cerradas'>
</td>
</tr>

</table>


<br>
<input type='button' value='Realizar Actualizaci&oacute;n...' 
id='boton2' onClick='iniciar_proceso2();'>
<span id='cargando2' style='display:none;'>
<img src='../../imagenes/ajax-loader3.gif'><br />Procesando Informaci&oacute;n...<br /><br /></span>

</center>

</div>

</form>




</body>
</html>

