<?php 

	require_once('../../conectar_db.php');

	$nom_id=$_POST['nom_id']*1;
	$pac_id=$_POST['pac_id']*1;

	$tipo='N';
	$extra='S';
	$diag='';
	$sficha='';
	$diag_cod='';
	$motivo='';
	$destino='';
	$auge='';
	$estado='';
	$hora='';

	$n=cargar_registro("SELECT * FROM nomina WHERE nom_id=$nom_id");
	$folio=$n['nom_folio'];

	pg_query("INSERT INTO nomina_detalle VALUES (
		DEFAULT,
		$nom_id,
		$pac_id,
		'$tipo','$extra',
		'$diag','$sficha',
		'$diag_cod','$motivo',
		'$destino','$auge',
		'$estado',
		0, '00:00:00', 'M', '$folio', $nom_id				
	);");

?>