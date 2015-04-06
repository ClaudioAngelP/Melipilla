<?php
    require_once("../../conectar_db.php");
    $fecha=pg_escape_string($_POST['fecha1']);
    $func_id=$_SESSION['sgh_usuario_id']*1;
    
    $consulta="
    SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
    upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre,
    pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,
    date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
    especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
    COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as esp_id_actual,
    COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as doc_id_actual,
    COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as am_estado,
    COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),null) as centro_ruta_actual,
    COALESCE((SELECT am_func_id FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as am_func_id,
    1 as programada
    FROM nomina_detalle
    LEFT JOIN nomina USING (nom_id)
    LEFT JOIN especialidades ON nom_esp_id=esp_id
    LEFT JOIN doctores ON nom_doc_id=doc_id
    JOIN pacientes USING (pac_id)
    WHERE esp_ficha AND nomd_diag_cod NOT IN ('X','T','B')
    AND nom_fecha::date='$fecha'
    ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
    ";
    
    
    
    
    $nomdl1=cargar_registros_obj($consulta, true);
    
    $consulta="SELECT fesp_fecha AS fecha_sol, fesp_fecha::date AS fecha_asigna,esp_id,doc_rut,
    pacientes.pac_ficha, 
    (
    case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
    else (select func_nombre from funcionario where func_id=fesp_func_id) 
    end 
    ) AS doc_nombre,
    pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,
    fesp_estado, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id ,fesp_id AS nomd_id,pac_id,amp_nombre,
    COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as esp_id_actual,
    COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as doc_id_actual,
    COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as am_estado,
    COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),null) as centro_ruta_actual,
    COALESCE((SELECT am_func_id FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and archivo_movimientos.am_final ORDER BY am_id DESC LIMIT 1),0) as am_func_id,
    (case when ficha_espontanea.esp_id!=0 then upper(esp_desc) else (select centro_nombre from centro_costo where centro_ruta=fesp_centro_ruta) end)AS esp,
    esp_ficha,
    0 as programada,
    fesp_centro_ruta
    FROM ficha_espontanea
    LEFT JOIN especialidades using(esp_id)
    LEFT JOIN doctores using (doc_id )
    LEFT JOIN archivo_motivos_prestamo USING (amp_id)
    JOIN pacientes USING (pac_id)
    WHERE (esp_ficha or esp_ficha is null) AND fesp_fecha::date='$fecha' AND fesp_estado=0
    GROUP BY fesp_fecha,esp_id, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
    pac_appat,pac_apmat, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id,amp_nombre
    ORDER BY fesp_fecha,esp_desc,doc_nombre";
    
    //print($consulta);
    //die();
    
    $nomdl2=cargar_registros_obj($consulta, true);

    
    
    if($nomdl1 AND $nomdl2)
        $nomdl=array_merge($nomdl1,$nomdl2);
    else if($nomdl1 AND !$nomdl2)
        $nomdl=$nomdl1;
    else if(!$nomdl1 AND $nomdl2)
        $nomdl=$nomdl2;
    else
        $nomdl=false;

    //print_r($nomdl);
    //die();
    if($nomdl)
        for($i=0;$i<sizeof($nomdl);$i++)
        {
            $nomd=$nomdl[$i];
            $estado_actual=$nomd['am_estado']*1;
            if($estado_actual!=1)
                continue;
            //if($func_id!=($nomd['am_func_id']*1))
            //    continue;
                
            $nomd_id=$nomd['nomd_id']*1;
            $esp_id=$nomd['esp_id_actual']*1;
            $doc_id=$nomd['doc_id_actual']*1;
            $centro_actual=$nomd['centro_ruta_actual'];
            $doc_id2=$nomd['doc_id_actual']*1;
            $tipo_solicitud=$nomd['programada']*1;
            
            $estado=2;
            $pac_id=$nomd['pac_id']*1;
            $pac_ficha=pg_escape_string($nomd['pac_ficha']);
            //$esp_id2=$nomd['esp_id_actual']*1;
            //if($nomd['esp_id']!="" && $nomd['esp_id']!="0" && $nomd['esp_id']!=0)
            if($nomd['esp_id_actual']!="" && $nomd['esp_id_actual']!="0" && $nomd['esp_id_actual']!=0)
            {
                $esp_id2=$nomd['esp_id_actual']*1;
            }
            else
            {
                $esp_id2=pg_escape_string($nomd['centro_ruta_actual']);
            }
            /*
            if($nomd['esp_id_actual']!="" && $nomd['esp_id_actual']!="0" && $nomd['esp_id_actual']!=0)
            {
                if(($nomd['esp_id']*1)!=($nomd['esp_id_actual']*1))
                {
                    continue;
                }
            }
            else
            {
                if($nomd['centro_ruta_actual']=="")
                {
            
                }
                else
                {
                    
                }
            }
            */
            //------------------------------------------------------------------
            pg_query("START TRANSACTION;");
            pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
            if(strstr($esp_id2,'.'))
            {
                if($centro_actual!="")
                {
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,'$centro_actual','$esp_id2','$tipo_solicitud');");
                }
                else
                {
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,null,'$esp_id2','$tipo_solicitud');");
                }
            }
            else
            {
                if($centro_actual!="")
                {
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,'$centro_actual',null,'$tipo_solicitud');");    
                }
                else
                {
                    //print("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");    
                }
            }
            //pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true);");
            pg_query("COMMIT;");
        }
?>