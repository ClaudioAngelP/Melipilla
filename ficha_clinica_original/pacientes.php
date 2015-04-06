<?php 

	require_once('../conectar_db.php');
	require_once('ficha_basica.php');
	
	$pac_id=$_GET['pac_id']*1;

?>

<html>
<title>Ficha B&acute;sica de Pacientes</title>

<?php cabecera_popup('../'); ?>

<body>
<?php

	desplegar_ficha_basica('','../',$pac_id);

?>
</body>
</html>
