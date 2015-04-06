<?php  require_once('../../conectar_db.php');

		$corr=cargar_registro("select max((replace(art_codigo,'ART00',''))::Integer) AS corr from articulo where art_codigo ilike '%ART00%'");

		print("ART00".($corr['corr']+1));

?>
