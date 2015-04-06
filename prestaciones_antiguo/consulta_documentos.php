<?php 

	require_once('../conectar_db.php');

	$data=pg_query("SELECT
                mon_fecha_ingreso, mon_rut, pac_ficha, mon_nombre, 
mon_fecha_inicio, mon_fecha_limite, monr_fecha_evento, mon_fecha_limite::date-monr_fecha_evento::date, 
pst_patologia_interna, pst_garantia, pst_rama_interna, nombre_condicion, 
monr_subcondicion, monr_observaciones, doc_rut, replace(doc_paterno,'(AGEN)',''), replace(doc_materno,'(AGEN)',''), doc_nombres
                FROM monitoreo_ges_registro
                JOIN monitoreo_ges USING (mon_id)
                JOIN lista_dinamica_condiciones ON monr_clase=id_condicion::text
                JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
                JOIN pacientes on pac_rut=mon_rut
                JOIN nomina_detalle ON nomina_detalle.pac_id=pacientes.pac_id
                JOIN nomina ON nomina_detalle.nom_id=nomina.nom_id AND nom_fecha>=monr_fecha
                LEFT JOIN especialidades ON nom_esp_id=esp_id
                LEFT JOIN doctores ON nom_doc_id=doc_id
                WHERE NOT mon_estado AND monr_estado=0 AND
                monr_clase IN ('1', '33') AND
                nomina.nom_id IS NOT NULL AND nomd_diag_cod='' AND
                ((codigos_sic ILIKE '%' || esp_cod_especialidad || '%') OR (mon_cod_especialidad=esp_cod_especialidad))
                ORDER BY mon_id, nom_fecha;
		");

	// We'll be outputting a PDF
header('Content-type: application/csv');

// It will be called downloaded.pdf
header('Content-Disposition: attachment; filename="reporte.csv"');


	while($r=pg_fetch_row($data)) {

		for($i=0;$i<sizeof($r);$i++) 
			print($r[$i].';');

		print("\r\n");

	}

?>
