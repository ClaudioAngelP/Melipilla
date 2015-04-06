<?php

  require_once('../conectar_db.php');

  require_once('ficha_basica.php');

  $pac_id=$_GET['pac_id']*1;

?>

<html>
<title>Ficha Básica de Paciente</title>
<?php cabecera_popup('..'); ?>

<body class='popup_background fuente_por_defecto'>

<?php desplegar_ficha_basica('','../', $pac_id); ?>

</body>
</html>
                     