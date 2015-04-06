<?php
set_time_limit(0);
//$conexion = pg_connect("host=localhost dbname=fricke port=5432 user=postgres password=123soluciones");

//echo "BEFORE<br>";
require_once('../config.php');
require_once('sigh.php');
require_once('hgf.php');
//echo "Before<br>";
for($i=1;$i<900000;$i++) {
	//echo "Inicio<br>";
	pg_query("DELETE FROM pacientes WHERE pac_id=$i");
	cargar_paciente($i);
	echo "$i ";
}

?>
