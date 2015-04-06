<?php

  require_once('../../conectar_db.php');
  
  $imagen=$_FILES['archivo'];
  $marca=md5_file($imagen['tmp_name']);
  
  if(move_uploaded_file($imagen['tmp_name'], '../../adjuntos/'.strtolower($imagen['name']))) {
  
?>

<html>
<body style='background-color:#dfe6ef;	font: 0.7em Tahoma, sans-serif;'>
<center>
<br>
<img src='../../imagenes/ajax-loader2.gif'><br><br>
Archivo cargado exitosamente...
</center>

<script>

  var func=window.opener.agregar_archivo.bind(window.opener);

  func('<?php echo 'adjuntos/'.strtolower($imagen['name']); ?>',
                                '<?php echo strtolower($imagen['name']); ?>',
                                <?php echo $imagen['size']; ?>);
  window.close();

</script>

</body>
</html>

<?php 
  
  
  } else {
  
?>

<html>
<body style='background-color:#dfe6ef;	font: 0.7em Tahoma, sans-serif;'>
<center>
<br>
<img src='../../imagenes/ajax-loader2.gif'><br><br>
Error al cargar archivo.
</center>
</body>
</html>

<?php
  
  }
  

?>
