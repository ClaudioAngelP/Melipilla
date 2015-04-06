<?php 

	require_once('../../conectar_db.php');

	$pac_id=$_POST['pac_id']*1;
	
	$motivo=$_POST['motivo']*1;
		
	$direccion=pg_escape_string(utf8_decode($_POST['direccion']));
	$fono=pg_escape_string(utf8_decode($_POST['fono']));
	
	$desde=pg_escape_string(utf8_decode($_POST['desde']));
	$hacia=pg_escape_string(utf8_decode($_POST['hacia']));
	
	if($_POST['desde2']!='')
		$desde=pg_escape_string(utf8_decode($_POST['desde2']));
		
	if($_POST['hacia2']!='')
		$hacia=pg_escape_string(utf8_decode($_POST['hacia2']));
	
	$proc=pg_escape_string(utf8_decode($_POST['proc']));
	$lugar=pg_escape_string(utf8_decode($_POST['lugar']));
	$hora=pg_escape_string(utf8_decode($_POST['hora']));

	if($hora!='') $hora="'$hora'"; else $hora='null';

	$contacto=pg_escape_string(utf8_decode($_POST['contacto']));
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	
	$condiciones='';
	
	for($i=1;$i<5;$i++) {
		if(isset($_POST['cond'.$i])) {
			$condiciones.='|'.pg_escape_string(utf8_decode($_POST['cond'.$i]));
		}
	}

	$requisitos='';
	
	for($i=1;$i<11;$i++) {
		if(isset($_POST['req'.$i])) {
			$requisitos.='|'.pg_escape_string(utf8_decode($_POST['req'.$i]));
		}
	}
	
	/*
	
	CREATE TABLE hospitalizacion_ambulancias
	(
	  hospa_id bigserial NOT NULL,
	  hospa_fecha_ing timestamp without time zone,
	  hospa_func_id bigint,
	  hospa_pac_id bigint,
	  hospa_motivo smallint,
	  hospa_direccion text,
	  hospa_telefono text,
	  hospa_desde text,
	  hospa_hacia text,
	  hospa_procedimiento text,
	  hospa_lugar_proc text,
	  hospa_fecha_proc timestamp without time zone,
	  hospa_contacto text,
	  hospa_condiciones text,
	  hospa_requisitos text,
	  hospa_prioridad smallint default 0,
	  hospa_movil smallint,
	  CONSTRAINT hospitalizacion_ambulancias_hospa_id_key PRIMARY KEY (hospa_id)
	)
	WITH (
	  OIDS=FALSE
	);

	
	*/

	pg_query("INSERT INTO hospitalizacion_ambulancias VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, 
			'$motivo', '$direccion', '$fono', '$desde', '$hacia', '$proc', '$lugar', $hora, '$contacto',
			'$condiciones', '$requisitos', 0, null);");

?>
