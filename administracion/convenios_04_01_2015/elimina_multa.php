<?php

require_once('../../conectar_db.php');

$multa_id=$_POST['multa_id']*1;

$borrar_multa = "DELETE FROM convenio_multa WHERE covnm_id=$multa_id";

if(pg_query($borrar_multa)){
	print(true);
}else{
	print(false);
}

?>
