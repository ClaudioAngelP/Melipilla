<?php 

	require_once('../../conectar_db.php');
	
	$rut=pg_escape_string($_POST['pac']);
	$folio=$_POST['folio']*1;
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$rut' AND id_sigges>0;");
	
	if($pac) {
		$pac_id=$pac['pac_id'];
		$pac_nombre=trim($pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']);
	} else { 
		$pac_id=0;
		$pac_nombre='<i>(Descargando desde SIGGES...)</i>';
	}	
	
	$chk=cargar_registros_obj("
		SELECT * FROM pacientes_queue 
		WHERE pacq_rut='$rut' AND func_id=".$_SESSION['sgh_usuario_id']."	
	");
	
	if($chk) die("Paciente previamente ingresado en el listado.");
	
	pg_query("INSERT INTO pacientes_queue VALUES (

		DEFAULT,
		".$_SESSION['sgh_usuario_id'].",
		$pac_id,
		'$rut', '$folio',
		'$pac_nombre', false
			
	);");

?>