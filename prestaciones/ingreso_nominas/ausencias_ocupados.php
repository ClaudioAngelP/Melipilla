<?php
    ini_set("memory_limit","800M");
    require_once("../../conectar_db.php");
    set_time_limit(0);
    $esp_id=($_POST['esp_id']*1);
    $doc_id=$_POST['doc_id']*1;
    
    
    $consulta="SELECT DISTINCT
    date_trunc('day', cupos_fecha) AS cupos_fecha,
    cupos_horainicio, cupos_horafinal, cupos_id, 
    cupos_cantidad_n, cupos_cantidad_c, cupos_ficha, cupos_adr,
    esp_desc, nom_motivo,
    nom_id,
    (select count(*) from nomina_detalle where nomina_detalle.nom_id=nomina.nom_id and (pac_id!=0 and pac_id is not null) AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL))as cant,
    esp_id
    FROM cupos_atencion
    JOIN especialidades ON esp_id=cupos_esp_id
    LEFT JOIN nomina USING (nom_id)
    WHERE cupos_doc_id=$doc_id ORDER BY cupos_fecha, cupos_horainicio;";
    
    
    $fechas = cargar_registros_obj($consulta, true);
    
    
    $fechas_ausencias = cargar_registros_obj("SELECT DISTINCT ausencia_fechainicio, ausencia_fechafinal FROM ausencias_medicas WHERE (doc_id=$doc_id OR doc_id=0);", true);
    $ausencias=cargar_registros_obj("SELECT * FROM ausencias_medicas JOIN ausencias_motivos ON motivo_id=ausencia_motivo WHERE doc_id=$doc_id ORDER BY ausencia_fechainicio", true);
    $fechas_ausente=Array();
    for($i=0;$i<count($fechas_ausencias);$i++)
    {
        if($fechas_ausencias[$i]['ausencia_fechafinal']=='')
            $fechas_ausente[count($fechas_ausente)]=$fechas_ausencias[$i]['ausencia_fechainicio'];
        else
        {
            $finicio=explode('/',$fechas_ausencias[$i]['ausencia_fechainicio']);
            $ffinal=explode('/',$fechas_ausencias[$i]['ausencia_fechafinal']);
            $fi=mktime(0,0,0,$finicio[1],$finicio[0],$finicio[2]);
            $ff=mktime(0,0,0,$ffinal[1],$ffinal[0],$ffinal[2]);
            for(;$fi<=$ff;$fi+=86400)
            {
                $fechas_ausente[count($fechas_ausente)]=date('d/m/Y',$fi);
            }           
        }  
    }
    echo json_encode(array($fechas,$fechas_ausente));
?>