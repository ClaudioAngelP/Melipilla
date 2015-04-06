<?php
    require_once('../../conectar_db.php');
    $nomd_id=$_POST['nomd_id']*1;
    
    $obs=cargar_registro("SELECT date(nom_fecha)as fecha,doc_nombres||' '||doc_paterno||' '||doc_materno as doc,nomd_observaciones,nomd_id
    FROM nomina
    LEFT JOIN nomina_detalle USING(nom_id)
    LEFT JOIN doctores on doc_id=nom_doc_id
    LEFT JOIN especialidades on nom_esp_id=esp_id
    WHERE nomd_id=$nomd_id ORDER BY nom_fecha DESC",true);
    echo $obs['nomd_observaciones'];
?>