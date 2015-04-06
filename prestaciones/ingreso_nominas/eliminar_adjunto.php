<?php 

	require_once('../../conectar_db.php');

	$nomda_id=$_POST['nomda_id'];
	
	$f=cargar_registro("SELECT * FROM nomina_detalle_adjuntos WHERE nomda_id=$nomda_id");
	
	unlink('../../ficha_clinica/adjuntos/'.$f['nomda_archivo']);
	
	pg_query("DELETE FROM nomina_detalle_adjuntos WHERE nomda_id=$nomda_id");

?>