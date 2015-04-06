<?php 

	require_once('../../conectar_db.php');
	
	$mon_id=$_POST['mon_id'];
	
	$m=cargar_registro("SELECT * FROM monitoreo_ges WHERE mon_id=$mon_id");
	
	$cod_especialidad=pg_escape_string($_POST['mon_cod_especialidad']);
	
	pg_query("UPDATE monitoreo_ges SET mon_cod_especialidad='$cod_especialidad' WHERE mon_id=$mon_id;");
	
	$clase=($_POST['id_condicion']*1);
	$subclase=pg_escape_string(utf8_decode($_POST['codigo_bandeja']));
	$descripcion=pg_escape_string(utf8_decode($_POST['monges_descripcion']));
	
	$fecha=pg_escape_string(utf8_decode($_POST['monr_fecha_proxmon']));
	
	if($fecha!='')
		$fecha="'$fecha'"; else $fecha='null';

	$fecha_evento=pg_escape_string(utf8_decode($_POST['monr_fecha_evento']));
	
	if($fecha_evento!='')
		$fecha_evento="'$fecha_evento'"; else $fecha_evento='null';
		
	$observa=pg_escape_string(utf8_decode($_POST['mon_observaciones']));
	
	$subcond='';
	if(isset($_POST['monr_subcondicion']))
		$subcond=pg_escape_string(utf8_decode($_POST['monr_subcondicion']));
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
		
	pg_query("START TRANSACTION;");
	
	$tmp=cargar_registro("SELECT * FROM monitoreo_ges_registro  WHERE mon_id=$mon_id AND monr_estado=0;");

	if($tmp['monr_clase']*1==$clase) {
		$subclase=$tmp['monr_subclase'];
	}
	
	pg_query("UPDATE monitoreo_ges_registro SET monr_estado=2 WHERE mon_id=$mon_id AND monr_estado=0;");
	
	pg_query("INSERT INTO monitoreo_ges_registro VALUES (
		DEFAULT, $mon_id, $func_id, now(), '$clase', '$subclase', '$observa', $fecha, '$descripcion', '$subcond', $fecha_evento
	);");
	
	pg_query('COMMIT;');

?>
