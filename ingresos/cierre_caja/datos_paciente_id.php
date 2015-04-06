<?php 

	require_once('../conectar_db.php');
	
	$str=pg_escape_string(utf8_decode($_POST['str']));
	
	if($str== NULL){$str=0;}
		$w="id_sidra=$str";
	
	

	$pac=cargar_registro("
		SELECT *,
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
		COALESCE(ciud_desc,'Peñalolén') AS ciud_desc
		FROM pacientes 
		LEFT JOIN prevision USING (prev_id)
		LEFT JOIN comunas USING (ciud_id)
		WHERE $w
		LIMIT 1
	", true);
	
	exit(json_encode($pac));

?>