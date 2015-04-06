<?php

  require_once('../../conectar_db.php');
  
?>

<html>
<title>Adjuntar Archivos a &Oacute;rden de Trabajo</title>

<?php cabecera_popup('../..'); ?>

<script>

enviar_foto=function() {

  var miform = $('formfoto');
  
  /*
  l=(screen.availWidth/2)-150;
  t=(screen.availHeight/2)-100;

  win = window.open('../../cargando.php?msg='+encodeURIComponent('Cargando Im&aacute;gen...'),
                    'carga_foto',
                    'scrollbars=yes, toolbar=no, left='+l+', top='+t+', '+
                    'resizable=no, width=300, height=200');

  miform.target='carga_foto';
  */
  
  miform.submit();
  
  //win.window.opener=window.opener;
  
  //window.close();

}

</script>

<body class="popup_background fuente_por_defecto">

<form id='formfoto' name='formfoto'
action='sql_adjuntar.php' enctype="multipart/form-data" method='post'>

<div class='sub-content'>
<img src='../../iconos/folder_page.png'>
Seleccionar Archivo
</div>
<div class='sub-content'>
<table style='width:100%;text-align:center;'>
<tr><td>Seleccionar Archivo:
<input type='file' id='archivo' name='archivo'>
</td></tr>
<tr><td>
<input type='button' value='Cargar Archivo...' onClick='enviar_foto();'>
</td></tr>
</table>
</div>

</form>

</body>
</html>

