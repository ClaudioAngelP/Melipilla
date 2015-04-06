<?php
    require_once('../../conectar_db.php');
    if(isset($_POST['ausencia_id']))
    {
        $doc_id=$_POST['doc_id']*1;
        pg_query("DELETE FROM ausencias_medicas WHERE ausencia_id=".($_POST['ausencia_id']*1));
    }
    else
    {
        $fecha1=pg_escape_string($_POST['fecha1']);
        $fecha2=pg_escape_string($_POST['fecha2']);
        $hr_desde=pg_escape_string($_POST['hr_desde']);
        $hr_hasta=pg_escape_string($_POST['hr_hasta']);
        $motivo=$_POST['motivo']*1;
        $doc_id=$_POST['doc_id']*1;
        if($fecha2=='')
            $fecha2='null';
        else
            $fecha2="'$fecha2'";
        
        
        
        pg_query("INSERT INTO ausencias_medicas VALUES (default, '$fecha1', $fecha2, $motivo, $doc_id, '$hr_desde','$hr_hasta')");
    }
    $a = cargar_registros_obj("SELECT * FROM ausencias_medicas JOIN ausencias_motivos ON motivo_id=ausencia_motivo WHERE doc_id=$doc_id ORDER BY ausencia_fechainicio", true);
  

    pg_query("UPDATE nomina_detalle SET nomd_diag_cod='X', nomd_codigo_cancela=foo2.ausencia_motivo FROM 
    (
	select nomd_id,ausencia_motivo from (
		select ausencia_id, ausencia_fechainicio, ausencia_fechafinal, nom_fecha::date, nomd_id, pac_id,
		ausencia_motivo
		from ausencias_medicas
		join nomina on (nomina.nom_doc_id=ausencias_medicas.doc_id OR ausencias_medicas.doc_id=0) AND nom_fecha::date BETWEEN ausencia_fechainicio AND COALESCE(ausencia_fechafinal, ausencia_fechainicio)
		join nomina_detalle on (nomina.nom_id=nomina_detalle.nom_id) AND nomd_hora between hora_inicio and hora_final
		where pac_id=0
	) AS foo
    )as foo2
    WHERE  nomina_detalle.nomd_id=foo2.nomd_id");

    pg_query("UPDATE nomina_detalle SET nomd_diag_cod='X', nomd_codigo_cancela=foo.ausencia_motivo FROM (
    select ausencia_id, ausencia_motivo, ausencia_fechainicio, ausencia_fechafinal, 
    nom_fecha::date, esp_desc, doc_rut, trim(doc_paterno || ' ' || doc_materno || ' ' || doc_nombres), nomd_id, pac_rut, 
    pac_ficha, pac_appat, pac_apmat, pac_nombres from ausencias_medicas
    join nomina on (nomina.nom_doc_id=ausencias_medicas.doc_id OR ausencias_medicas.doc_id=0) AND
    nom_fecha::date BETWEEN ausencia_fechainicio AND COALESCE(ausencia_fechafinal, ausencia_fechainicio)
    join especialidades on nom_esp_id=esp_id
    join doctores on nom_doc_id=doctores.doc_id
    join nomina_detalle on (nomina.nom_id=nomina_detalle.nom_id) AND nomd_hora between hora_inicio and hora_final
    left join pacientes using (pac_id)
    where not pac_id=0
    ) AS foo WHERE nomina_detalle.nomd_id=foo.nomd_id");

    pg_query("delete from cupos_atencion where nom_id in (select nom_id from nomina where nom_id not in (select distinct nom_id from nomina_detalle))");
    pg_query("delete from nomina where nom_id in (select nom_id from nomina where nom_id not in (select distinct nom_id from nomina_detalle)) AND (COALESCE(nom_estado,0)<>-1 AND COALESCE(nom_estado,0)<>10 AND COALESCE(nom_estado,0)<>11)");
	print(json_encode($a));
?>
