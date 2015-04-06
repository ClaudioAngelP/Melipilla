<?php 

	require_once('../../conectar_db.php');
	
	$fecha=pg_escape_string(utf8_decode($_POST['fecha']));
	$tcama=pg_escape_string(utf8_decode($_POST['tcamas']));
	$hora=pg_escape_string(utf8_decode($_POST['hora']));
	
		$l=cargar_registros_obj("
			SELECT * FROM (
			
			SELECT *, h1.hosp_fecha_ing::date AS hosp_fecha_ingreso, 
			h1.hosp_id AS id, COALESCE((
				SELECT ptras_cama_destino 
				FROM paciente_traslado AS p1
				WHERE p1.hosp_id=h1.hosp_id AND ptras_fecha<='$fecha $hora'
				ORDER BY ptras_fecha DESC, ptras_id DESC
				LIMIT 1
			), hosp_numero_cama) AS cama_censo
			FROM hospitalizacion AS h1
			WHERE (h1.hosp_fecha_egr>='$fecha $hora' OR h1.hosp_fecha_egr IS NULL) AND COALESCE(h1.hosp_fecha_hospitalizacion, h1.hosp_fecha_ing)<='$fecha $hora'
			AND NOT hosp_numero_cama = 0
			AND hosp_solicitud
			
			) AS foo
			JOIN pacientes ON hosp_pac_id=pac_id
			LEFT JOIN censo_diario ON 
				censo_diario.hosp_id=foo.hosp_id AND
				censo_diario.censo_fecha='$fecha $hora'
			LEFT JOIN tipo_camas ON
				cama_num_ini<=cama_censo AND cama_num_fin>=cama_censo
			LEFT JOIN clasifica_camas ON 
				tcama_num_ini<=cama_censo AND tcama_num_fin>=cama_censo
			WHERE 
				tcama_id=$tcama
				
			ORDER BY cama_censo, foo.hosp_fecha_ing	
	", true);

	if($l)
	for($i=0;$i<sizeof($l);$i++) {

		$valor=pg_escape_string($_POST['clase_'.$l[$i]['id']]);
		$sel=pg_escape_string($_POST['sel_'.$l[$i]['id']]);
		
		pg_query("DELETE FROM censo_diario WHERE hosp_id=".$l[$i]['id']." AND censo_fecha='$fecha $hora'");
			
		if($valor!='')
			pg_query("INSERT INTO censo_diario VALUES (
			DEFAULT,
			".$l[$i]['id'].",
			'".$valor."',
			'".$fecha." ".$hora."',
			".$l[$i]['hosp_numero_cama'].",
			".($_SESSION['sgh_usuario_id']*1).",
			'".$sel."'
			);");
		

		pg_query("UPDATE hospitalizacion SET
					hosp_criticidad=(SELECT censo_diario FROM censo_diario WHERE hosp_id=".$l[$i]['id']." ORDER BY censo_fecha DESC LIMIT 1)
					WHERE hosp_id=".$l[$i]['id']);

	}


?>
