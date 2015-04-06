<?php 

	require_once('../../conectar_db.php');
	
	$nomd_id=$_POST['nomd_id']*1;
	
	$nd=cargar_registro("SELECT * FROM nomina_detalle WHERE nomd_id=$nomd_id;");
	
	pg_query("INSERT INTO nomina_detalle_cancela 
			(nomdc_id,nom_id,pac_id,nomdc_tipo,nomdc_extra,nomdc_diag,nomdc_sficha,nomdc_diag_cod,nomdc_motivo,nomdc_destino,
			  nomdc_auge,nomdc_estado,presta_id,nomdc_hora,nomdc_via_ingreso,nomdc_folio,nomdc_nom_id,nomdc_observaciones,
			  oa_id,nomdc_origen,nomdc_prev_id,id_sidra,nomdc_codigo_cancela,nomdc_codigo_no_atiende,nomdc_codigo_presta,nomdc_func_id,
			  nomdc_fecha_asigna,nomdc_enviado_hl7) 
			  SELECT nomd_id,nom_id,pac_id,nomd_tipo,nomd_extra,nomd_diag,nomd_sficha,nomd_diag_cod,
			  nomd_motivo,nomd_destino,nomd_auge,nomd_estado,presta_id,nomd_hora,nomd_via_ingreso,nomd_folio,nomd_nom_id,nomd_observaciones,
			  oa_id,nomd_origen,nomd_prev_id,id_sidra,nomd_codigo_cancela,nomd_codigo_no_atiende,nomd_codigo_presta,nomd_func_id,
			  CURRENT_TIMESTAMP,false 
			  FROM nomina_detalle WHERE nomd_id=$nomd_id");
	
	if($nd['nomd_via_ingreso']=='M')
		pg_query("DELETE FROM nomina_detalle WHERE nomd_id=$nomd_id");
	else
		pg_query("UPDATE nomina_detalle SET 
		pac_id=0, 
		nomd_tipo='N',
		nomd_extra='N',
		nomd_sficha='N',
		nomd_diag_cod='',
		nomd_destino='',
		nomd_auge='',
		nomd_prev_id=0,
		nomd_func_id=0
		WHERE nomd_id=$nomd_id");


	// REGULARIZA BLOQUES POSTERIORES...
	$nom_id=$nd['nom_id']*1;
	$pac_id=$nd['pac_id']*1;
	$nomd_hora=$nd['nomd_hora'];

	pg_query("UPDATE nomina_detalle SET nomd_diag_cod='', pac_id=0 WHERE nom_id=$nom_id AND nomd_diag_cod='B' AND pac_id=$pac_id AND nomd_hora>'$nomd_hora';");

	
?>
