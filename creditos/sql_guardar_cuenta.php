<?php 

	require_once('../conectar_db.php');

	function fecha_sql($f) {
		$fp=explode('/',$f);
		if(sizeof($fp)==3 AND checkdate($fp[1]*1, $fp[0]*1,$fp[2]*1)) 
			return "'".$f."'";
		else 
			return 'null';
	}


	$pac_id=pg_escape_string($_POST['pac_id']*1);
	$hosp_id=$_POST['hosp_id']*1;
	
	$p=cargar_registro("SELECT * FROM pacientes LEFT JOIN prevision USING (prev_id) WHERE pac_id=$pac_id;");
	$prev_id=$p['prev_id']*1;
	$prev_desc=pg_escape_string($p['prev_desc']);
	
	$modalidad=pg_escape_string(utf8_decode($_POST['modalidad']));
	
	//$prodesc=pg_escape_string($_POST['prodesc']);
	$proval=floor($_POST['proval']*1);
	$total_descuento = floor($_POST['total_descuento']*1);
	$pie=floor($_POST['pie']*1);
	$cuonro=$_POST['cuonro']*1;
	
	$presta=json_decode($_POST['prestaciones']);	


	$func_id=$_SESSION['sgh_usuario_id'];

	pg_query("DELETE FROM cuenta_corriente WHERE hosp_id=$hosp_id");
	pg_query("DELETE FROM cuenta_corriente_encabezado WHERE hosp_id=$hosp_id");

	pg_query("INSERT INTO cuenta_corriente_encabezado VALUES ($hosp_id, $pac_id, $prev_id, '$prev_desc', '$modalidad', $func_id, CURRENT_TIMESTAMP, 0);");	
	
	for($i=0;$i<sizeof($presta);$i++) {
	
		$valort=$presta[$i]->precio*1;	
		$valorp=$presta[$i]->copago*1;	
		
		pg_query("INSERT INTO cuenta_corriente VALUES (
			$hosp_id, 
			".($presta[$i]->presta_id*1).", 
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->fecha)))."', 
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->codigo)))."',
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->glosa, ENT_COMPAT | ENT_HTML401, 'UTF-8')))."',	
			".($presta[$i]->cantidad*1).",
			".($presta[$i]->precio*1).",
			".($presta[$i]->copago*1).",
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->pab)))."',
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->tipo)))."',
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->modalidad)))."',
			'".pg_escape_string(utf8_decode(html_entity_decode($presta[$i]->ptipo)))."',
			'".pg_escape_string(html_entity_decode($presta[$i]->cobro))."'
		);");
		
	}

	print(json_encode(array(true,$hosp_id)));

?>
