<?php 

	require_once('../../conectar_db.php');
	
	$fecha=$_POST['fecha'];
	$esp_id=$_POST['esp_id']*1;
	$doc_id=$_POST['doc_id']*1;

	pg_query("INSERT INTO nomina VALUES (
		DEFAULT,
		max_folio(),
		$esp_id,
		$doc_id,
		'',
		0, false,
		'$fecha',
		0, true,
		0, 0, 0,
		'', null, null,
		true
	);");
	
	$n=cargar_registro("SELECT * FROM nomina WHERE nom_id=CURRVAL('nomina_nom_id_seq');");
	
	$folio=$n['nom_folio'];

	print(json_encode($folio));

?>