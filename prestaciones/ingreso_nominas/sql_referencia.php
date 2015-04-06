<?php
    require_once('../../conectar_db.php');
    $nomd_id=$_POST['nomd_id']*1;
    $ref_fecha1=pg_escape_string($_POST['ref_fecha1']);
    $ref_fecha2=pg_escape_string($_POST['ref_fecha2']);
    $ref_diagnostico1=pg_escape_string(utf8_decode($_POST['ref_diagnostico1']));
    $ref_diagnostico2=pg_escape_string(utf8_decode($_POST['ref_diagnostico2']));
    $ref_nro_biopsia=$_POST['ref_nro_biopsia']*1;
    $ref_est_id=$_POST['est_id']*1;
    $ref_est_id2=$_POST['est_id2']*1;
    $ref_fecha3=pg_escape_string($_POST['ref_fecha3']);
    $ref_detalle=pg_escape_string(utf8_decode($_POST['ref_detalle']));
    $ref_tratamiento=pg_escape_string(utf8_decode($_POST['ref_tratamiento']));
    $ref_indicaciones=pg_escape_string(utf8_decode($_POST['ref_indicaciones']));
    $ref_control_esp=($_POST['ref_control_esp']*1);
    $ref_control_aps=($_POST['ref_control_aps']*1);
    $ref_pertinecia=($_POST['ref_pertinecia']*1);
    $ref_motivo=pg_escape_string(utf8_decode($_POST['ref_motivo']));
    
    if($ref_fecha3=='') 
        $ref_fecha3="null";
    else 
        $ref_fecha3="'$ref_fecha3'";
    
    pg_query("START TRANSACTION;");
    $reg_ref=cargar_registro("SELECT * FROM nomina_detalle_referencia WHERE nomd_id=".$nomd_id."");
    if($reg_ref) {
        
        pg_query($conn, "
        UPDATE nomina_detalle_referencia 
        SET nomdr_fecha=CURRENT_TIMESTAMP,
        nomdr_func_id=".($_SESSION['sgh_usuario_id']*1).",
        nomdr_fecha_ingreso='$ref_fecha1',
        nomdr_fecha_alta='$ref_fecha2',
        nomdr_diagnostico1='$ref_diagnostico1',
        nomdr_diagnostico2='$ref_diagnostico2',
        nomdr_biopsia_nro='$ref_nro_biopsia',
        nomdr_est_id=$ref_est_id,
        nomdr_fecha2=$ref_fecha3,
        nomdr_detalle='$ref_detalle',
        nomdr_tratamiento='$ref_tratamiento',
        nomdr_indicaciones='$ref_indicaciones',
        nomdr_control=$ref_control_esp,
        nomdr_control_aps=$ref_control_aps,
        nomdr_pertinencia=$ref_pertinecia,
        nomdr_porque='$ref_motivo',
        nomdr_est_id2=$ref_est_id2,
        WHERE nomdr_id=".($reg_ref['nomdr_id']*1).";");
        
        $nomdr_id=($reg_ref['nomdr_id']*1);
    }
    else {
        
        pg_query($conn, "
        INSERT INTO
        nomina_detalle_referencia
        VALUES(
        DEFAULT,
        $nomd_id,
        CURRENT_TIMESTAMP,
        ".($_SESSION['sgh_usuario_id']*1).",
        '$ref_fecha1',
        '$ref_fecha2',
        '$ref_diagnostico1',
        '$ref_diagnostico2',
        $ref_nro_biopsia,
        $ref_est_id,
        $ref_fecha3,
        '$ref_detalle',
        '$ref_tratamiento',
        '$ref_indicaciones',
        $ref_control_esp,
        $ref_control_aps,
        $ref_pertinecia,
        '$ref_motivo',
        $ref_est_id2
        )
        ");
        
        $nomd_ref = pg_query($conn, "SELECT CURRVAL('nomina_detalle_referencia_nomdr_id_seq');");
        $ref_arr = pg_fetch_row($nomd_ref);
        $nomdr_id = $ref_arr[0];
    }
    pg_query("COMMIT;");
    print(json_encode($nomdr_id));
    
?>