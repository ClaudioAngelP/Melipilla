<?php 
    require_once('../../conectar_db.php');
    $cant_extra=$_POST['cant_extra']*1;
    $cupo_id=$_POST['cupo_id']*1;
    $esp_id=$_POST['esp_id']*1;
    $doc_id=$_POST['doc_id']*1;
    $chk=cargar_registro("SELECT * FROM cupos_atencion where cupos_id=$cupo_id");
    if($chk)
    {
        if($cant_extra!="")
        {
            pg_query("Update cupos_atencion set cupos_cantidad_c=$cant_extra WHERE cupos_id=$cupo_id;");
        }
    }
    $consulta="
    SELECT DISTINCT
    date_trunc('day', cupos_fecha) AS cupos_fecha,
    cupos_horainicio, cupos_horafinal, cupos_id,
    cupos_cantidad_n, cupos_cantidad_c, cupos_ficha, cupos_adr,
    esp_desc, nom_motivo,nom_tipo_contrato,
    nom_id,
    (select count(*) from nomina_detalle where nomina_detalle.nom_id=nomina.nom_id and (pac_id!=0 and pac_id is not null) AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL))as cant,
    esp_id
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    LEFT JOIN nomina USING (nom_id)
    WHERE cupos_doc_id=$doc_id and cupos_esp_id=$esp_id ORDER BY cupos_fecha, cupos_horainicio;
    ";
    $fechas = cargar_registros_obj($consulta, true);
    print(json_encode($fechas));
?>
