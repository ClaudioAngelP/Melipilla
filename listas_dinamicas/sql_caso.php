<?php 

	require_once('../conectar_db.php');
	
	$lista_id=$_POST['lista_id']*1;
	$pac_id=$_POST['pac_id']*1;
	
	pg_query("INSERT INTO lista_dinamica_caso VALUES (DEFAULT, 0, $pac_id, '');");
	pg_query("INSERT INTO lista_dinamica_instancia VALUES (DEFAULT, CURRVAL('lista_dinamica_caso_caso_id_seq'), $lista_id, current_timestamp, 0, 0, '');");


?>
