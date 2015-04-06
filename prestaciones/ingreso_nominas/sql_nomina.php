<?php 
    require_once('../../conectar_db.php');
    $fecha=$_POST['fecha'];
    $esp_id=$_POST['esp_id']*1;
    $doc_id=$_POST['doc_id']*1;
    
    $func_id=$_SESSION['sgh_usuario_id']*1;
    
    if(isset($_POST['select_nom_motivo']))
    {
        $tipo_atencion=pg_escape_string($_POST['select_nom_motivo']);
    }
    else
    {
        $tipo_atencion="";
    }
        
    if(isset($_POST['select_nom_contrato']))
    {
        if($_POST['select_nom_contrato']!="-1")
        {
            $tipo_contrato=pg_escape_string(utf8_decode($_POST['select_nom_contrato']));
        }
        else
        {
            $tipo_contrato="";
        }
    }
    else
    {
        $tipo_contrato="";
    }
    
    $tmp=cargar_registro("SELECT nom_id AS id FROM nomina WHERE nom_esp_id=$esp_id AND nom_doc_id=$doc_id AND nom_fecha::date='$fecha' AND nom_motivo='$tipo_atencion';");
    if($tmp)
    {
        $folio="X";
    }
    else
    {
        if($tipo_contrato!="")
        {
            pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_centro_ruta, nom_tipo, nom_urgente, nom_fecha,nom_motivo,nom_tipo_contrato,nom_func_id)
            VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 1, false, '$fecha','$tipo_atencion','$tipo_contrato',$func_id);");
        }
        else
        {
            pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_centro_ruta, nom_tipo, nom_urgente, nom_fecha,nom_motivo,nom_func_id)
            VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 1, false, '$fecha','$tipo_atencion',$func_id);");
        }
        $n=cargar_registro("SELECT * FROM nomina WHERE nom_id=CURRVAL('nomina_nom_id_seq');");
        $folio=$n['nom_folio'];
    }
    print(json_encode($folio));
?>