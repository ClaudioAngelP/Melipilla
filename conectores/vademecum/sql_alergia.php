<?php 

	require_once('../../conectar_db.php');

	$pac_id=$_POST['pac_id']*1;

	if(isset($_POST['al_id'])) {
		$al_id=$_POST['al_id']*1;
		pg_query("DELETE FROM paciente_alergias WHERE al_id=$al_id");
		exit();
	}

	list($id_alergia, $alergia, $tipo)=explode('|',pg_escape_string($_POST['id_alergia']));
	$func_id=$_SESSION['sgh_usuario_id']*1;

	$id_alergia*=1; $tipo*=1;
	$alergia=html_entity_decode($alergia);

	$chk=cargar_registro("SELECT * FROM paciente_alergias WHERE pac_id=$pac_id AND id_alergia=$id_alergia");

	if(!$chk)
	pg_query("INSERT INTO paciente_alergias VALUES (DEFAULT, $pac_id, $id_alergia, '$alergia', $func_id, CURRENT_TIMESTAMP, $tipo);");

?>
