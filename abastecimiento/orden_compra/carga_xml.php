<?php

  require_once('../../conectar_db.php');
  
?>

<html>
<title>Cargar &Oacute;rdenes de Compra en XML (ChileCompra)</title>

<?php cabecera_popup('../..'); ?>

<script>

enviar_xml=function() {

  var miform = $('formxml');
  
  l=(screen.availWidth/2)-365;
  t=(screen.availHeight/2)-250;

  win = window.open('', 'orden_carga_xml',
                    'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=730, height=500');

  win.window.opener=window.opener;
  
  miform.target='orden_carga_xml';
  
  miform.submit();
  
  window.close();

}

</script>

<body class="popup_background fuente_por_defecto">

<form id='formxml' name='formxml'
action='carga_xml2.php' enctype="multipart/form-data" method='post'>

<div class='sub-content'>
Seleccionar Archivo XML (ChileCompra)
</div>
<div class='sub-content'>
<table style='width:100%;text-align:center;'>
<tr><td>Seleccionar Archivo:
<input type='file' id='archivo' name='archivo'>
</td></tr>
<tr><td>
<input type='button' value='Cargar Archivo...' onClick='enviar_xml();'>
</td></tr>
</table>
</div>

</form>

</body>
</html>



