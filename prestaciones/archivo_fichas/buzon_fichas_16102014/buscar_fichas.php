<?php
    require_once("../../../conectar_db.php");
    if(!isset($_POST['list_ficha']))
    {
        $especialidad=pg_escape_string($_POST['especialidad']);
        if(strstr($especialidad,'.'))
        {
            $servicio=1;
        }
        else
        {
            $servicio=0;
        }
        if($servicio==0)
        {
            $especialidad=pg_escape_string($_POST['especialidad']);
            $tipo_busqueda=$_POST['tipo_busqueda'];
            $fecha = pg_escape_string($_POST['fecha2']); 
            $doc_id=($_POST['doc_id']*1);
            //--------------------------------------------------------------------------
            if($doc_id!=-1)
                $w_doc="and destino_doc_id=$doc_id";
            else
                $w_doc=" and true";

            if($tipo_busqueda=="1")
            {
                //----------------------------------------------------------------------
                if(strstr($especialidad,'.'))
                {
                    $esp="true";
                    $centro="centro_ruta='$especialidad'";
                    $centro_costo=true;
                }
                else
                {
                    $esp="especialidades.esp_id=$especialidad";
                    $centro="true";
                    $centro_costo=false;
                }
                /*
                $consulta="select * from (SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
                pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
                date_trunc('second',am_fecha)AS fecha_asigna,
                especialidades.esp_id,doctores.doc_id,
                COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
                COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                date_trunc('second',COALESCE((SELECT COALESCE(am_fecha,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),null)) as fecha_recepcion
                FROM archivo_movimientos
                LEFT JOIN especialidades ON destino_esp_id=esp_id
                LEFT JOIN doctores ON destino_doc_id=doc_id
                JOIN pacientes USING (pac_id)
                WHERE 
                am_final AND $esp
                ORDER BY esp_desc,doc_nombre,pac_ficha)
                as foo where am_estado=3 $w_doc";
                * 
                */
                $consulta="
                select 
                case when am_tipo_solicitud='1' then
                (
                    select nomd_fecha_asigna::date from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                ( 
                    select  fesp_fecha::date
                    from ficha_espontanea 
                    where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                    and esp_id=$especialidad
                    order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select nomd_fecha_asigna::date from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  fesp_fecha::date
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                )
                end as fecha,
                case when am_tipo_solicitud='1' then
                (
                    select date_trunc('second',nomd_fecha_asigna)::time from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                ( 
                    select  date_trunc('second',fesp_fecha)::time
                    from ficha_espontanea 
                    where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                    and esp_id=$especialidad
                    order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select date_trunc('second',nomd_fecha_asigna)::time from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  date_trunc('second',fesp_fecha)::time
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                )
                end as  nomd_hora,
                case when am_tipo_solicitud='1' then
                (
                    select nom_doc_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                (
                    select  doc_id
                    from ficha_espontanea 
                    where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                    and esp_id=$especialidad
                    order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select nom_doc_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  doc_id
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                )
                end as  fesp_doc_id,
                case when am_tipo_solicitud='1' then
                (
                    select nom_esp_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                (
                    select  esp_id
                    from ficha_espontanea 
                    where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                    and esp_id=$especialidad
                    order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select nom_esp_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                        select  esp_id
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$especialidad
                        order by fesp_fecha desc  limit 1
                        )
                    )
                    
                )
                end as  fesp_esp_id,
                case when am_tipo_solicitud='1' then
                (
                    select date_trunc('second',nomd_fecha_asigna) from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                ( 
                    select  date_trunc('second',fesp_fecha)
                    from ficha_espontanea 
                    where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                    and esp_id=$especialidad
                    order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select date_trunc('second',nomd_fecha_asigna) from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  date_trunc('second',fesp_fecha)
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                )
                end as  fecha_asigna,
                case when am_tipo_solicitud='1' then
                (
                    'Programada'
                )
                else
                ( 
                    select  amp_nombre
                    from ficha_espontanea
                    left join archivo_motivos_prestamo on archivo_motivos_prestamo.amp_id=ficha_espontanea.amp_id
                    where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                    and esp_id=$especialidad
                    order by fesp_fecha desc  limit 1
                )
                end as  amp_nombre,
                (case when origen_esp_id=0 and am_centro_ruta_origen is null then 'ARCHIVO' when origen_esp_id=0 and am_centro_ruta_origen is not null then null else upper(esp1.esp_desc) end)as origen_esp_desc,
                (case when destino_esp_id=0 and am_centro_ruta_destino is null then 'ARCHIVO' when destino_esp_id=0 and am_centro_ruta_destino is not null then null else upper(esp2.esp_desc) end)as destino_esp_desc,
                COALESCE(centro1.centro_nombre,null)as origen_centro_nombre,
                COALESCE(centro2.centro_nombre,null)as destino_centro_nombre,
                upper(d1.doc_nombres||' '||d1.doc_paterno||' '||d1.doc_materno) AS origen_doc_nombre,
                upper(d2.doc_nombres||' '||d2.doc_paterno||' '||d2.doc_materno) AS destino_doc_nombre,
                upper(d1.doc_rut)as origen_doc_rut,
                upper(d2.doc_rut)as destino_doc_rut,
                pacientes.pac_ficha,
                pacientes.pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                pacientes.pac_id,
                am_estado,
                am_id,
                nomd_id,
                am_tipo_solicitud,
                date_trunc('second',am_fecha)as fecha_recepcion
                from archivo_movimientos 
                LEFT JOIN pacientes on archivo_movimientos.pac_id=pacientes.pac_id
                LEFT JOIN doctores as d1 on origen_doc_id=d1.doc_id
                LEFT JOIN doctores as d2 on destino_doc_id=d2.doc_id
                LEFT JOIN especialidades as esp1 on origen_esp_id=esp1.esp_id
                LEFT JOIN especialidades as esp2 on destino_esp_id=esp2.esp_id
                LEFT JOIN centro_costo as centro1 on am_centro_ruta_origen=centro1.centro_ruta
                LEFT JOIN centro_costo as centro2 on am_centro_ruta_destino=centro2.centro_ruta
                where am_estado=3
                and destino_esp_id=$especialidad
                and am_final=true
                and am_fecha>='25-06-2014'
                $w_doc
                order by destino_esp_desc,destino_doc_nombre,pac_ficha
                ";
                
                $registros = cargar_registros_obj($consulta,true);
                if(!$registros)
                {
                    $registros=false;
                }
                echo json_encode(array($registros));
            }
            if($tipo_busqueda=="3")
            {
                if(strstr($especialidad,'.'))
                {
                    $esp="true";
                    $centro="centro_ruta='$especialidad'";
                }
                else
                {
                    $esp="especialidades.esp_id=$especialidad";
                    $centro="true";
                }
                
                $consulta="
                select 
                case when am_tipo_solicitud='1' then
                (
                        select nomd_fecha_asigna::date from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                ( 
                        select  fesp_fecha::date
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$especialidad
                        order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select nomd_fecha_asigna::date from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  fesp_fecha::date
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                )
                end as fecha,
                case when am_tipo_solicitud='1' then
                (
                        select date_trunc('second',nomd_fecha_asigna)::time from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                ( 
                        select  date_trunc('second',fesp_fecha)::time
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$especialidad
                        order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select date_trunc('second',nomd_fecha_asigna)::time from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  date_trunc('second',fesp_fecha)::time
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                
                )
                end as  nomd_hora,
                case when am_tipo_solicitud='1' then
                (
                        select nom_doc_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                (
                        select  doc_id
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$especialidad
                        order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select nom_doc_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  doc_id
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                
                )
                end as  fesp_doc_id,
                case when am_tipo_solicitud='1' then
                (
                        select nom_esp_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                (
                        select  esp_id
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$especialidad
                        order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select nom_esp_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  esp_id
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                
                )
                end as  fesp_esp_id,
                case when am_tipo_solicitud='1' then
                (
                        select date_trunc('second',nomd_fecha_asigna) from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
                )
                when am_tipo_solicitud='0' then
                ( 
                        select  date_trunc('second',fesp_fecha)
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$especialidad
                        order by fesp_fecha desc  limit 1
                )
                else
                (
                    COALESCE
                    (
                        (select date_trunc('second',nomd_fecha_asigna) from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                        ,
                        (
                            select  date_trunc('second',fesp_fecha)
                            from ficha_espontanea 
                            where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                            and esp_id=$especialidad
                            order by fesp_fecha desc  limit 1
                        )
                    )
                )
                end as  fecha_asigna,
                case when am_tipo_solicitud='1' then
                (
                    'Programada'
                )
                else
                ( 
                        select  amp_nombre
                        from ficha_espontanea
                        left join archivo_motivos_prestamo on archivo_motivos_prestamo.amp_id=ficha_espontanea.amp_id
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$especialidad
                        order by fesp_fecha desc  limit 1
                )
                end as  amp_nombre,
                (case when origen_esp_id=0 and am_centro_ruta_origen is null then 'ARCHIVO' when origen_esp_id=0 and am_centro_ruta_origen is not null then null else upper(esp1.esp_desc) end)as origen_esp_desc,
                (case when destino_esp_id=0 and am_centro_ruta_destino is null then 'ARCHIVO' when destino_esp_id=0 and am_centro_ruta_destino is not null then null else upper(esp2.esp_desc) end)as destino_esp_desc,
                COALESCE(centro1.centro_nombre,null)as origen_centro_nombre,
                COALESCE(centro2.centro_nombre,null)as destino_centro_nombre,
                upper(d1.doc_nombres||' '||d1.doc_paterno||' '||d1.doc_materno) AS origen_doc_nombre,
                upper(d2.doc_nombres||' '||d2.doc_paterno||' '||d2.doc_materno) AS destino_doc_nombre,
                upper(d1.doc_rut)as origen_doc_rut,
                upper(d2.doc_rut)as destino_doc_rut,
                pacientes.pac_ficha,
                pacientes.pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                pacientes.pac_id,
                am_estado,
                am_id,
                nomd_id,
                am_tipo_solicitud,
                date_trunc('second',am_fecha)as fecha_recepcion
                from archivo_movimientos
                LEFT JOIN pacientes on archivo_movimientos.pac_id=pacientes.pac_id
                LEFT JOIN doctores as d1 on origen_doc_id=d1.doc_id
                LEFT JOIN doctores as d2 on destino_doc_id=d2.doc_id
                LEFT JOIN especialidades as esp1 on origen_esp_id=esp1.esp_id
                LEFT JOIN especialidades as esp2 on destino_esp_id=esp2.esp_id
                LEFT JOIN centro_costo as centro1 on am_centro_ruta_origen=centro1.centro_ruta
                LEFT JOIN centro_costo as centro2 on am_centro_ruta_destino=centro2.centro_ruta
                where am_estado=2
                and destino_esp_id=$especialidad
                and am_final=true
                and am_fecha>='25-06-2014'
                $w_doc
                order by destino_esp_desc,destino_doc_nombre,pac_ficha
                ";
           
                $registros = cargar_registros_obj($consulta,true);
                if(!$registros)
                {
                    $registros=false;
                }
                echo json_encode(array($registros));
            }
            if($tipo_busqueda=="4")
            {
                $ficha=pg_escape_string($_POST['ficha']);
                //$encontrado_ficha=pg_escape_string($_POST['encontrado_ficha']);
                //$encontrado_rut=pg_escape_string($_POST['encontrado_rut']);
                //$fecha=pg_escape_string($_POST['fecha1']);
                //$especialidad=pg_escape_string($_POST['ubicacion']);
                if(strstr($ficha,'-'))
                    $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE upper(pac_rut)=upper('$ficha') LIMIT 1", true);
                else
                    $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE upper(pac_ficha)=upper('$ficha') LIMIT 1", true);

                $pac_id=$tmp[0]['pac_id']*1;
                $pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
                //----------------------------------------------------------------------
                $nomd1_w='true';
                $nomd2_w='true';
                //if($tipo_inf==1)
                $consulta="SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
                pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
                date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
                especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
                COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
                COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
                FROM nomina_detalle
                LEFT JOIN nomina USING (nom_id)
                LEFT JOIN especialidades ON nom_esp_id=esp_id
                LEFT JOIN doctores ON nom_doc_id=doc_id
                JOIN pacientes USING (pac_id)
                WHERE esp_ficha AND nomd_diag_cod NOT IN ('X','T','B')
                AND pac_id=$pac_id AND nom_fecha::date='$fecha' AND $nomd1_w
                ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora";
                $nomdl1=cargar_registros_obj($consulta, true);

                //if($tipo_inf==3)

                $consulta="SELECT 
                fesp_fecha AS fecha_sol, 
                fesp_fecha::date AS fecha_asigna,
                esp_id,
                doc_rut,
                pacientes.pac_ficha, 
                (
                case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
                else (select func_nombre from funcionario where func_id=fesp_func_id) 
                end ) AS doc_nombre,
                pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                fesp_estado,
                especialidades.esp_desc, 
                especialidades.esp_id,
                doctores.doc_id ,
                fesp_id AS nomd_id,
                pac_id,
                amp_nombre,
                COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
                COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_actual,
                esp_ficha,
                (case when ficha_espontanea.esp_id!=0 then upper(esp_desc) else (select centro_nombre from centro_costo where centro_ruta=fesp_centro_ruta) end)AS esp,
                0 as programada,
                fesp_centro_ruta
                FROM ficha_espontanea
                LEFT JOIN especialidades using(esp_id)
                LEFT JOIN doctores using (doc_id )
                LEFT JOIN archivo_motivos_prestamo USING (amp_id)
                JOIN pacientes USING (pac_id)
                WHERE (esp_ficha or esp_ficha is null) AND fesp_fecha::date='$fecha' AND pac_id=$pac_id AND $nomd2_w AND fesp_estado=0
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

                if($nomdl AND sizeof($nomdl)==1)
                {
                    $nomd=$nomdl[0];
                    echo json_encode(array(1));
                }
                elseif($nomdl AND sizeof($nomdl)>1)
                {
                    exit(json_encode(array($tmp,false,$nomdl), true));
                }
                elseif(!$nomdl)
                {
                    echo json_encode(array(1));
                }
            }
            //echo json_encode(array($registros));
        }
        if($servicio==1)
        {
            $tipo_busqueda=($_POST['tipo_busqueda']*1);
            $w_fecha="true";
            $especialidad=pg_escape_string($_POST['especialidad']);
            $w_fecha="true";

            $doc_id=($_POST['doc_id']*1);
            //----------------------------------------------------------------------
            if($doc_id!=-1)
                $w_doc="and doc_id=$doc_id";
            else
                $w_doc=" and true";

            if($tipo_busqueda==1)
            {
                if(strstr($especialidad,'.'))
                {
                    $esp="true";
                    $centro="centro_ruta_destino='$especialidad'";
                }
        
                $consulta="
                select 
                ( 
                        select  fesp_fecha::date
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and fesp_centro_ruta='$especialidad' 
                        order by fesp_fecha desc  limit 1
                )as fecha,
                ( 
                        select  date_trunc('second',fesp_fecha)::time
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and fesp_centro_ruta='$especialidad' 
                        order by fesp_fecha desc  limit 1
                )as  nomd_hora,
                (
                select  doc_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  fesp_doc_id,
                (
                select  esp_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  fesp_esp_id,
                ( 
                select  date_trunc('second',fesp_fecha)
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  fecha_asigna,
                ( 
                select  amp_nombre
                from ficha_espontanea
                left join archivo_motivos_prestamo on archivo_motivos_prestamo.amp_id=ficha_espontanea.amp_id
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  amp_nombre,
                (case when origen_esp_id=0 and am_centro_ruta_origen is null then 'ARCHIVO' when origen_esp_id=0 and am_centro_ruta_origen is not null then null else upper(esp1.esp_desc) end)as origen_esp_desc,
                (case when destino_esp_id=0 and am_centro_ruta_destino is null then 'ARCHIVO' when destino_esp_id=0 and am_centro_ruta_destino is not null then null else upper(esp2.esp_desc) end)as destino_esp_desc,
                COALESCE(centro1.centro_nombre,null)as origen_centro_nombre,
                COALESCE(centro2.centro_nombre,null)as destino_centro_nombre,
                upper(d1.doc_nombres||' '||d1.doc_paterno||' '||d1.doc_materno) AS origen_doc_nombre,
                upper(d2.doc_nombres||' '||d2.doc_paterno||' '||d2.doc_materno) AS destino_doc_nombre,
                upper(d1.doc_rut)as origen_doc_rut,
                upper(d2.doc_rut)as destino_doc_rut,
                pacientes.pac_ficha,
                pacientes.pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                pacientes.pac_id,
                am_estado,
                am_id,
                nomd_id,
                am_tipo_solicitud,
                date_trunc('second',am_fecha)as fecha_recepcion
                from archivo_movimientos 
                LEFT JOIN pacientes on archivo_movimientos.pac_id=pacientes.pac_id
                LEFT JOIN doctores as d1 on origen_doc_id=d1.doc_id
                LEFT JOIN doctores as d2 on destino_doc_id=d2.doc_id
                LEFT JOIN especialidades as esp1 on origen_esp_id=esp1.esp_id
                LEFT JOIN especialidades as esp2 on destino_esp_id=esp2.esp_id
                LEFT JOIN centro_costo as centro1 on am_centro_ruta_origen=centro1.centro_ruta
                LEFT JOIN centro_costo as centro2 on am_centro_ruta_destino=centro2.centro_ruta
                where am_estado=3
                and am_centro_ruta_destino='$especialidad'
                and am_final=true
                and am_fecha>='25-06-2014'
                order by destino_esp_desc,destino_doc_nombre,pac_ficha
                ";

                $registros=cargar_registros_obj($consulta, true);
                if(!$registros)
                {
                    $registros=false;
                }
                echo json_encode(array($registros));
            }
            if($tipo_busqueda==3)
            {
                if(strstr($especialidad,'.'))
                {
                    $esp="true";
                    //$centro="centro_ruta_destino='$especialidad'";
                }

                //$consulta="select * from archivo_movimientos where am_final and am_centro_ruta_destino='$centro_ruta'";
                /*
                $consulta="select distinct on (pac_id) pac_id,foo.* from (
                SELECT 
                0 as nom_id,
                fesp_fecha::date AS fecha,
                fesp_fecha::time AS nomd_hora,
                upper(especialidades.esp_desc)AS esp,
                doc_rut,
                pacientes.pac_ficha,
                upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre,
                pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                pacientes.pac_id,
                date_trunc('second',fesp_fecha)AS fecha_asigna,
                fesp_id as nomd_id,
                esp_id,
                doctores.doc_id,
                COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
                COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
                COALESCE((SELECT COALESCE(origen_esp_id,-1) FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),-1) as id_esp_anterior,
                COALESCE((SELECT COALESCE(destino_esp_id,-1) FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),-1) as destino_esp_id,
                COALESCE((SELECT COALESCE(am_centro_ruta_origen,null) FROM archivo_movimientos LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_origen,
                COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_destino,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
                COALESCE((SELECT COALESCE(origen_esp_id) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=false ORDER BY am_fecha DESC LIMIT 1),0) as am_enviado_por,
                amp_nombre,
                date_trunc('second',COALESCE((SELECT COALESCE(am_fecha,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),null)) as fecha_recepcion
                FROM ficha_espontanea
                LEFT JOIN especialidades using(esp_id)
                LEFT JOIN doctores using (doc_id )
                LEFT JOIN archivo_motivos_prestamo USING (amp_id)
                JOIN pacientes USING (pac_id)
                WHERE (esp_ficha or esp_ficha is null) AND $w_fecha AND true AND fesp_estado=0
                and $esp
                GROUP BY fesp_fecha,esp_id, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
                pac_appat,pac_apmat, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id,amp_nombre
                ORDER BY fesp_fecha desc,esp_desc,doc_nombre
                )as foo
                where am_estado=2 and $centro ";
                 * 
                 */
                $consulta="
                select 
                ( 
                        select  fesp_fecha::date
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and fesp_centro_ruta='$especialidad' 
                        order by fesp_fecha desc  limit 1
                )as fecha,
                ( 
                        select  date_trunc('second',fesp_fecha)::time
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and fesp_centro_ruta='$especialidad' 
                        order by fesp_fecha desc  limit 1
                )as  nomd_hora,
                (
                select  doc_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  fesp_doc_id,
                (
                select  esp_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  fesp_esp_id,
                ( 
                select  date_trunc('second',fesp_fecha)
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  fecha_asigna,
                ( 
                select  amp_nombre
                from ficha_espontanea
                left join archivo_motivos_prestamo on archivo_motivos_prestamo.amp_id=ficha_espontanea.amp_id
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$especialidad' 
                order by fesp_fecha desc  limit 1
                )as  amp_nombre,
                (case when origen_esp_id=0 and am_centro_ruta_origen is null then 'ARCHIVO' when origen_esp_id=0 and am_centro_ruta_origen is not null then null else upper(esp1.esp_desc) end)as origen_esp_desc,
                (case when destino_esp_id=0 and am_centro_ruta_destino is null then 'ARCHIVO' when destino_esp_id=0 and am_centro_ruta_destino is not null then null else upper(esp2.esp_desc) end)as destino_esp_desc,
                COALESCE(centro1.centro_nombre,null)as origen_centro_nombre,
                COALESCE(centro2.centro_nombre,null)as destino_centro_nombre,
                upper(d1.doc_nombres||' '||d1.doc_paterno||' '||d1.doc_materno) AS origen_doc_nombre,
                upper(d2.doc_nombres||' '||d2.doc_paterno||' '||d2.doc_materno) AS destino_doc_nombre,
                upper(d1.doc_rut)as origen_doc_rut,
                upper(d2.doc_rut)as destino_doc_rut,
                pacientes.pac_ficha,
                pacientes.pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                pacientes.pac_id,
                am_estado,
                am_id,
                nomd_id,
                am_tipo_solicitud
                from archivo_movimientos 
                LEFT JOIN pacientes on archivo_movimientos.pac_id=pacientes.pac_id
                LEFT JOIN doctores as d1 on origen_doc_id=d1.doc_id
                LEFT JOIN doctores as d2 on destino_doc_id=d2.doc_id
                LEFT JOIN especialidades as esp1 on origen_esp_id=esp1.esp_id
                LEFT JOIN especialidades as esp2 on destino_esp_id=esp2.esp_id
                LEFT JOIN centro_costo as centro1 on am_centro_ruta_origen=centro1.centro_ruta
                LEFT JOIN centro_costo as centro2 on am_centro_ruta_destino=centro2.centro_ruta
                where am_estado=2
                and am_centro_ruta_destino='$especialidad'
                and am_final=true
                and am_fecha>='25-06-2014'
                order by destino_esp_desc,destino_doc_nombre,pac_ficha
                ";
                //print($consulta);
                //die();

                $registros=cargar_registros_obj($consulta, true);
                if(!$registros)
                {
                    $registros=false;
                }
                echo json_encode(array($registros));
            }
        }
    }
    else
    {
        $barras=pg_escape_string($_POST['barras']);
        if($barras=='')
            exit(json_encode(array(false)));
        
        if(strstr($barras,'-'))
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE upper(pac_rut)=upper('$barras') LIMIT 1");
        else
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE upper(pac_ficha)=upper('$barras') LIMIT 1");
        if(!$tmp)
        {
            exit(json_encode(array(false)));
        }
        $pac_id=$tmp[0]['pac_id']*1;
        
        $origen=pg_escape_string($_POST['origen']);
        if(strstr($origen,'.'))
        {
            $servicio=1;
            $esp="true";
            $centro="centro_ruta_destino='$origen'";
            
            $consulta="
            select 
                ( 
                        select  fesp_fecha::date
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and fesp_centro_ruta='$origen' 
                        order by fesp_fecha desc limit 1
                )as fecha,
                ( 
                        select  date_trunc('second',fesp_fecha)::time
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and fesp_centro_ruta='$origen' 
                        order by fesp_fecha desc limit 1
                )as  nomd_hora,
                (
                select  doc_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$origen' 
                order by fesp_fecha desc limit 1
                )as  fesp_doc_id,
                (
                select  esp_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$origen' 
                order by fesp_fecha desc limit 1
                )as  fesp_esp_id,
                ( 
                select  date_trunc('second',fesp_fecha)
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$origen' 
                order by fesp_fecha desc limit 1
                )as  fecha_asigna,
                ( 
                select  amp_nombre
                from ficha_espontanea
                left join archivo_motivos_prestamo on archivo_motivos_prestamo.amp_id=ficha_espontanea.amp_id
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and fesp_centro_ruta='$origen' 
                order by fesp_fecha desc limit 1
                )as  amp_nombre,
                (case when origen_esp_id=0 then 'ARCHIVO' else upper(esp1.esp_desc) end)as origen_esp_desc,
                (case when destino_esp_id=0 then null else upper(esp2.esp_desc) end)as destino_esp_desc,
                COALESCE(centro1.centro_nombre,null)as origen_centro_nombre,
                COALESCE(centro2.centro_nombre,null)as destino_centro_nombre,
                upper(d1.doc_nombres||' '||d1.doc_paterno||' '||d1.doc_materno) AS origen_doc_nombre,
                upper(d2.doc_nombres||' '||d2.doc_paterno||' '||d2.doc_materno) AS destino_doc_nombre,
                upper(d1.doc_rut)as origen_doc_rut,
                upper(d2.doc_rut)as destino_doc_rut,
                pacientes.pac_ficha,
                pacientes.pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                pacientes.pac_id,
                am_estado,
                am_id,
                nomd_id,
                am_tipo_solicitud,
                date_trunc('second',am_fecha)as fecha_recepcion
                from archivo_movimientos 
                LEFT JOIN pacientes on archivo_movimientos.pac_id=pacientes.pac_id
                LEFT JOIN doctores as d1 on origen_doc_id=d1.doc_id
                LEFT JOIN doctores as d2 on destino_doc_id=d2.doc_id
                LEFT JOIN especialidades as esp1 on origen_esp_id=esp1.esp_id
                LEFT JOIN especialidades as esp2 on destino_esp_id=esp2.esp_id
                LEFT JOIN centro_costo as centro1 on am_centro_ruta_origen=centro1.centro_ruta
                LEFT JOIN centro_costo as centro2 on am_centro_ruta_destino=centro2.centro_ruta
                where (am_estado=3 or am_estado=2)
                and am_centro_ruta_destino='$origen'
                and am_final=true
                and am_fecha>='25-06-2014'
                and archivo_movimientos.pac_id=$pac_id
                order by destino_esp_desc,destino_doc_nombre,pac_ficha
                ";
        }
        else
        {
            $esp="especialidades.esp_id=$origen";
            $centro="true";
            $centro_costo=false;

            $consulta="
            select 
            case when am_tipo_solicitud='1' then
            (
                select nomd_fecha_asigna::date from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
            )
            when am_tipo_solicitud='0' then
            ( 
                select  fesp_fecha::date
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and esp_id=$origen
                order by fesp_fecha desc  limit 1
            )
            else
            (
                COALESCE
                (
                    (select nomd_fecha_asigna::date from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                     ,
                    (
                        select  fesp_fecha::date
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$origen
                        order by fesp_fecha desc  limit 1
                    )
                )
            )
            end as fecha,
            case when am_tipo_solicitud='1' then
            (
                select date_trunc('second',nomd_fecha_asigna)::time from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
            )
            when am_tipo_solicitud='0' then
            ( 
                select  date_trunc('second',fesp_fecha)::time
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and esp_id=$origen
                order by fesp_fecha desc  limit 1
            )
            else
            (
                COALESCE
                (
                    (select date_trunc('second',nomd_fecha_asigna)::time from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                     ,
                    (
                        select  date_trunc('second',fesp_fecha)::time
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$origen
                        order by fesp_fecha desc  limit 1
                    )
                )
            )
            end as  nomd_hora,
            case when am_tipo_solicitud='1' then
            (
                select nom_doc_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id
            )
            when am_tipo_solicitud='0' then
            (
                select  doc_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and esp_id=$origen
                order by fesp_fecha desc  limit 1
            )
            else
            (
                COALESCE
                (
                    (select nom_doc_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id)
                     ,
                    (
                        select  doc_id
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$origen
                        order by fesp_fecha desc  limit 1
                    )
                )
            )
            end as  fesp_doc_id,
            case when am_tipo_solicitud='1' then
            (
                select nom_esp_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id
            )
            when am_tipo_solicitud='0' then
            (
                select  esp_id
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and esp_id=$origen
                order by fesp_fecha desc limit 1
            )
            else
            (
                COALESCE
                (
                    (select nom_esp_id from nomina_detalle left join nomina on nomina_detalle.nom_id=nomina.nom_id where nomd_id=archivo_movimientos.nomd_id)
                     ,
                    (
                        select  esp_id
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$origen
                        order by fesp_fecha desc limit 1
                    )
                )
            )
            end as fesp_esp_id,
            case when am_tipo_solicitud='1' then
            (
                select date_trunc('second',nomd_fecha_asigna) from nomina_detalle where nomd_id=archivo_movimientos.nomd_id
            )
            when am_tipo_solicitud='0' then
            ( 
                select  date_trunc('second',fesp_fecha)
                from ficha_espontanea 
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and esp_id=$origen
                order by fesp_fecha desc  limit 1
            )
            else
            (
                COALESCE
                (
                    (select date_trunc('second',nomd_fecha_asigna) from nomina_detalle where nomd_id=archivo_movimientos.nomd_id)
                     ,
                    (
                        select  date_trunc('second',fesp_fecha)
                        from ficha_espontanea 
                        where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                        and esp_id=$origen
                        order by fesp_fecha desc  limit 1
                    )
                )
            )
            end as fecha_asigna,
            case when am_tipo_solicitud='1' then
            (
                'Programada'
            )
            else
            ( 
                select  amp_nombre
                from ficha_espontanea
                left join archivo_motivos_prestamo on archivo_motivos_prestamo.amp_id=ficha_espontanea.amp_id
                where ficha_espontanea.pac_id=archivo_movimientos.pac_id
                and esp_id=$origen
                order by fesp_fecha desc  limit 1
            )
            end as  amp_nombre,
            (case when origen_esp_id=0 then 'ARCHIVO' else upper(esp1.esp_desc) end)as origen_esp_desc,
            (case when destino_esp_id=0 then 'ARCHIVO' else upper(esp2.esp_desc) end)as destino_esp_desc,
            COALESCE(centro1.centro_nombre,null)as origen_centro_nombre,
            COALESCE(centro2.centro_nombre,null)as destino_centro_nombre,
            upper(d1.doc_nombres||' '||d1.doc_paterno||' '||d1.doc_materno) AS origen_doc_nombre,
            upper(d2.doc_nombres||' '||d2.doc_paterno||' '||d2.doc_materno) AS destino_doc_nombre,
            upper(d1.doc_rut)as origen_doc_rut,
            upper(d2.doc_rut)as destino_doc_rut,
            pacientes.pac_ficha,
            pacientes.pac_rut,
            upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
            pacientes.pac_id,
            am_estado,
            am_id,
            nomd_id,
            am_tipo_solicitud,
            date_trunc('second',am_fecha)as fecha_recepcion
            from archivo_movimientos 
            LEFT JOIN pacientes on archivo_movimientos.pac_id=pacientes.pac_id
            LEFT JOIN doctores as d1 on origen_doc_id=d1.doc_id
            LEFT JOIN doctores as d2 on destino_doc_id=d2.doc_id
            LEFT JOIN especialidades as esp1 on origen_esp_id=esp1.esp_id
            LEFT JOIN especialidades as esp2 on destino_esp_id=esp2.esp_id
            LEFT JOIN centro_costo as centro1 on am_centro_ruta_origen=centro1.centro_ruta
            LEFT JOIN centro_costo as centro2 on am_centro_ruta_destino=centro2.centro_ruta
            where (am_estado=3 or am_estado=2)
            and destino_esp_id=$origen
            and am_final=true
            and am_fecha>='25-06-2014'
            and archivo_movimientos.pac_id=$pac_id
            order by destino_esp_desc,destino_doc_nombre,pac_ficha
            ";
        }
        
        $regs=cargar_registros_obj($consulta,true);
        if(!$regs)
        {
            $regs=false;
        }
        echo json_encode(array($regs));
    }
?>