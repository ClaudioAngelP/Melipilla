<?php 

	require_once('../../conectar_db.php');

	$nomd_id=$_POST['nomd_id']*1;
	$oa_id=$_POST['oa_id']*1;
	
	if($oa_id!=0) {	
	
		$oa=cargar_registro("SELECT *, oa_fecha::date AS oa_fecha, 
								$sgh_inst_id AS institucion, 
								0 AS inter_id
								FROM orden_atencion 
								LEFT JOIN centro_costo ON oa_centro_ruta=centro_ruta
								LEFT JOIN especialidades ON oa_especialidad=esp_id
								LEFT JOIN instituciones ON oa_inst_id=inst_id
								LEFT JOIN doctores ON oa_doc_id=doc_id
								LEFT JOIN profesionales_externos ON oa_prof_id=prof_id
								WHERE oa_id=$oa_id", true);
	
	} else {
	
		$oa=cargar_registro("SELECT *, origen_fecha_solicitud::date AS oa_fecha, 
								$sgh_inst_id AS institucion, 
								origen_oa_id AS oa_id, origen_inter_id AS inter_id
								FROM nomina_detalle_origen 
								LEFT JOIN centro_costo ON origen_centro_ruta=centro_ruta
								LEFT JOIN especialidades ON origen_esp_id=esp_id
								LEFT JOIN instituciones ON origen_inst_id=inst_id
								LEFT JOIN doctores ON origen_doc_id=doc_id
								LEFT JOIN profesionales_externos ON origen_prof_id=prof_id
								WHERE nomd_id=$nomd_id", true);		
		
	}
	
	echo json_encode($oa);

?>