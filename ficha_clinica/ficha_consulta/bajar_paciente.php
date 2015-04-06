<?php

  require_once('../../conectar_db.php');
  
  $busqueda = ($_GET['buscar']*1);
	
	$personal = pg_query($conn,"
	SELECT pac_id,pac_rut,(trim(pac_nombres)||' '||trim(pac_appat)||' '||trim(pac_apmat))AS pac_nombre,
       pac_fc_nac,sex_desc,estciv_nombre,prev_desc,getn_desc,sang_desc,pac_direccion,ciud_desc,nacion_nombre,pac_fono
	FROM pacientes 
LEFT JOIN sexo USING (sex_id)
LEFT JOIN prevision USING (prev_id)
LEFT JOIN grupos_etnicos USING (getn_id)
LEFT JOIN grupo_sanguineo USING (sang_id)
LEFT JOIN estado_civil USING (estciv_id)
LEFT JOIN comunas USING (ciud_id)
LEFT JOIN nacionalidad USING (nacion_id)
	WHERE pac_id=$busqueda 
	LIMIT 1
	");
	
	$datos=pg_fetch_row($personal);
	
	for($i=0;$i<count($datos);$i++) {
		$datos[$i]=htmlentities($datos[$i]);
	}
	
	print(json_encode($datos));


?>
