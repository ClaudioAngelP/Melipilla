<?php 

	require_once('../../conectar_db.php');

	$nom_id=$_POST['nom_id']*1;
	$pac_id=$_POST['pac_id']*1;
	$nomd_hora=pg_escape_string($_POST['nomd_hora']);

	if(isset($_POST['pac_nombres'])) {

		$pac_ficha=pg_escape_string(utf8_decode($_POST['pac_ficha']));
		 $pac_pasaporte=pg_escape_string(utf8_decode($_POST['pac_pasaporte']));
		
		$pac_nombres=pg_escape_string(utf8_decode($_POST['pac_nombres']));
		$pac_appat=pg_escape_string(utf8_decode($_POST['pac_appat']));
		$pac_apmat=pg_escape_string(utf8_decode($_POST['pac_apmat']));

		$pac_fc_nac=pg_escape_string(utf8_decode($_POST['pac_fc_nac']));
		$sex_id=pg_escape_string(utf8_decode($_POST['sex_id']))*1;

		$pac_direccion=pg_escape_string(utf8_decode($_POST['pac_direccion']));
		$ciud_id=pg_escape_string(utf8_decode($_POST['ciud_id']))*1;

		$pac_fono=pg_escape_string(utf8_decode($_POST['pac_fono']));
		$pac_celular=pg_escape_string(utf8_decode($_POST['pac_celular']));
		$pac_padre=pg_escape_string(utf8_decode($_POST['pac_padre']));
		$pac_ocupacion=pg_escape_string(utf8_decode($_POST['pac_ocupacion']));
		$prev_id=pg_escape_string(utf8_decode($_POST['prev_id']))*1;
		
		pg_query("UPDATE pacientes SET 
		pac_nombres='$pac_nombres',
		pac_appat='$pac_appat',
		pac_apmat='$pac_apmat',
		pac_fc_nac='$pac_fc_nac',
		sex_id=$sex_id,
		pac_direccion='$pac_direccion',
		ciud_id=$ciud_id,
		pac_fono='$pac_fono',
		pac_celular='$pac_celular',
		prev_id=$prev_id,
		pac_ficha='$pac_ficha',
		pac_pasaporte='$pac_pasaporte',
		pac_padre='$pac_padre',
		pac_ocupacion='$pac_ocupacion'
		WHERE pac_id=$pac_id");

		
	}
	
	$tipo='N';
	
	if($nomd_hora=='00:00')
		$extra='S';
	else
		$extra='N';
		
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

	$func_id=$_SESSION['sgh_usuario_id']*1;

	if($nomd_hora=='00:00') {
		pg_query("INSERT INTO nomina_detalle VALUES (
		DEFAULT,
		$nom_id,
		$pac_id,
		'$tipo','$extra',
		'$diag','$sficha',
		'$diag_cod','$motivo',
		'$destino','$auge',
		'$estado',
		0, '$nomd_hora', 'M', '$folio', $nom_id				
		);");
		$tmp=cargar_registro("SELECT CURRVAL('nomina_detalle_nomd_id_seq') AS id;");
		$nomd_id=$tmp['id']*1;
	} else {
		pg_query("UPDATE nomina_detalle SET pac_id=$pac_id WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora';");
		$tmp=cargar_registro("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora' LIMIT 1;");
		$nomd_id=$tmp['id']*1;
	}

	if(isset($_POST['duracion']) AND $_POST['duracion']*1>1) {

		$bloques=($_POST['duracion']*1)-1;

		pg_query("UPDATE nomina_detalle SET nomd_diag_cod='B', pac_id=$pac_id, nomd_fecha_asigna=CURRENT_TIMESTAMP, nomd_func_id=$func_id WHERE nomd_id in (SELECT nomd_id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora>'$nomd_hora' ORDER BY nomd_hora LIMIT $bloques);");

	}

	pg_query("UPDATE nomina_detalle SET nomd_func_id=$func_id, nomd_fecha_asigna=CURRENT_TIMESTAMP WHERE nomd_id=$nomd_id;");
	
	print($nomd_id);

?>
