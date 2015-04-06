<?php 
	require_once('../../conectar_db.php');
	$pta_id=$_POST['pta_id']*1;
	$esp_id=$_POST['esp_id']*1;
		
	pg_query("UPDATE prestaciones_tipo_atencion SET activado=false WHERE pta_id=".$pta_id."");
	

	$consulta="SELECT *,(SELECT COUNT(*) FROM nomina_detalle_prestaciones WHERE nomina_detalle_prestaciones.pc_id=prestaciones_tipo_atencion.pta_id) AS cnt
    FROM prestaciones_tipo_atencion where esp_id=".$esp_id." AND activado!=false";
    
	$presta=cargar_registros_obj($consulta,true);
	
	print(json_encode($presta));
	
?>	
	
	
