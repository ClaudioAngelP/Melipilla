<?php

  require_once('../../conectar_db.php');
  
  $imagen=$_FILES['archivo'];
  $marca=md5_file($imagen['tmp_name']);
  
  if(move_uploaded_file($imagen['tmp_name'], '../../fotos/'.strtolower($imagen['name']))) {
  
?>

<html>
<body style='background-color:#dfe6ef;	font: 0.7em Tahoma, sans-serif;'>
<center>
<br>
<img src='../../imagenes/ajax-loader2.gif'><br><br>
Im&aacute;gen cargada exitosamente...
</center>

<script>

  window.opener.$('foto').src='<?php echo 'fotos/'.strtolower($imagen['name']); ?>';
  window.opener.$('nombre_foto').value='<?php echo strtolower($imagen['name']); ?>';
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
Error al cargar im&aacute;gen.
</center>
</body>
</html>

<?php
  
  }
  

?>
