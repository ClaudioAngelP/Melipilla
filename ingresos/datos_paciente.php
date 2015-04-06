<?php 

	require_once('../conectar_db.php');
	
	$rutdv=pg_escape_string(utf8_decode($_POST['str']));
	$tipo=$_POST['tipo_rut']*1;
	
	switch($tipo) {			
				case 0:
					
					$sw="pac_rut='$rutdv'";
				break;
				case 1:
				 	$sw="pac_pasaporte='$rutdv'";
				break;
				case 2:
				 $sw='pac_id='.$rutdv;
				break;
				case 3:
				 $sw="pac_ficha='$rutdv.'";
				break;
			}
	 
	
		
	$pac=cargar_registro("
		SELECT *,
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
		date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
		date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
		COALESCE(ciud_desc,'Peñalolén') AS ciud_desc
		FROM pacientes 
		LEFT JOIN prevision USING (prev_id)
		LEFT JOIN comunas USING (ciud_id)
		WHERE $sw
		LIMIT 1
	", true);
	
	exit(json_encode($pac));

?>