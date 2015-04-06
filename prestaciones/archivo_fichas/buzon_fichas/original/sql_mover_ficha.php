<?php 
    require_once('../../../conectar_db.php');
    $tipo_inf=$_POST['tipo_inf']*1;
    if($tipo_inf!=-1)
    {
        //$fecha=pg_escape_string($_POST['fecha1']);
        $fecha=pg_escape_string($_POST['fecha']);
        $barras=trim($_POST['barras']);
        if($barras=='')
            exit(json_encode(false));
        
        if(strstr($barras,'-'))
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$barras' LIMIT 1", true);
        else
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$barras' LIMIT 1", true);
        
        if(!$tmp)
        {
            exit(json_encode(array(false)));
        }
        else
        {
            if($tmp[0]['pac_ficha']==null or $tmp[0]['pac_ficha']=="" or $tmp[0]['pac_ficha']==0 or $tmp[0]['pac_ficha']=="0")
            {
                exit(json_encode(array(false)));
            }
        }
        
        
        
    
        $pac_id=$tmp[0]['pac_id']*1;
        $pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
        if($tipo_inf==1 OR $tipo_inf==3)
        {
            if(isset($_POST['nomd_id']))
            {
                $nomd_id=($_POST['nomd_id']*1);
                $nomd1_w='nomd_id='.$nomd_id;
                $nomd2_w='fesp_id='.$nomd_id;
            }
            else
            {
                if(isset($_POST['nomd_id_'.$pac_ficha]))
                {
                    $nomd_id=$_POST['nomd_id_'.$pac_ficha];
                    $nomd1_w='nomd_id='.$nomd_id;
                    $nomd2_w='fesp_id='.$nomd_id;
                }
                else
                {
                    $nomd1_w='true';
                    $nomd2_w='true';
                }
            }
            if($fecha=="")
            {
                $w_fecha="true";
                $w_fecha2="true";
            }
            else
            {
                $w_fecha=" nom_fecha::date='$fecha' ";
                $w_fecha2=" fesp_fecha::date='$fecha' ";
            }
            
            /*
            $consulta="
            SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
            especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
            COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
            COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
            FROM nomina_detalle
            LEFT JOIN nomina USING (nom_id)
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            LEFT JOIN doctores ON nom_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE esp_ficha AND nomd_diag_cod NOT IN ('X','T','B')
            AND pac_id=$pac_id AND $w_fecha AND $nomd1_w
            ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
            ";
            
            //if($tipo_inf==1)
            $nomdl1=cargar_registros_obj($consulta, true);
            
            //if($tipo_inf==3)
            $consulta="SELECT fesp_fecha AS fecha_sol, fesp_fecha::date AS fecha_asigna,esp_id,doc_rut,
            pacientes.pac_ficha, upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre,
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
            fesp_estado, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id ,fesp_id AS nomd_id,pac_id,amp_nombre,
            COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
            COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
            FROM ficha_espontanea
            LEFT JOIN especialidades using(esp_id)
            LEFT JOIN doctores using (doc_id )
            LEFT JOIN archivo_motivos_prestamo USING (amp_id)
            JOIN pacientes USING (pac_id)
            WHERE esp_ficha AND $w_fecha2 AND pac_id=$pac_id AND $nomd2_w AND fesp_estado=0
            GROUP BY fesp_fecha,esp_id, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
            pac_appat,pac_apmat, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id,amp_nombre
            ORDER BY fesp_fecha,esp_desc,doc_nombre";
               
            $nomdl2=cargar_registros_obj($consulta, true);
            if($nomdl1 AND $nomdl2)
                $nomdl=array_merge($nomdl1,$nomdl2);
            else if($nomdl1 AND !$nomdl2)
                $nomdl=$nomdl1;
            else if(!$nomdl1 AND $nomdl2)
                $nomdl=$nomdl2;
            else $nomdl=false;
            if($nomdl AND sizeof($nomdl)==1)
            {
                $nomd=$nomdl[0];
            }
            elseif($nomdl AND sizeof($nomdl)>1)
            {
                exit(json_encode(array($tmp,false,$nomdl), true));		
            }
            */
            if($nomd1_w!='true')
            {
                $consulta="
                SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre
                , 
                pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
                date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
                especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
                COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
                COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
                COALESCE((SELECT COALESCE(origen_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_origen,
                COALESCE((SELECT COALESCE(origen_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_origen,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                COALESCE((SELECT COALESCE(am_centro_ruta_origen,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_origen,
                COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_destino,
                1 as programada
                FROM nomina_detalle
                LEFT JOIN nomina USING (nom_id)
                LEFT JOIN especialidades ON nom_esp_id=esp_id
                LEFT JOIN doctores ON nom_doc_id=doc_id
                JOIN pacientes USING (pac_id)
                WHERE esp_ficha 
                AND pac_id=$pac_id AND $w_fecha AND $nomd1_w
                ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
                ";
            }
            else
            {
                $consulta="
                SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre
                , 
                pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
                date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
                especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
                COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
                COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
                COALESCE((SELECT COALESCE(origen_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_origen,
                COALESCE((SELECT COALESCE(origen_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_origen,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                COALESCE((SELECT COALESCE(am_centro_ruta_origen,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_origen,
                COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_destino,
                1 as programada
                FROM nomina_detalle
                LEFT JOIN nomina USING (nom_id)
                LEFT JOIN especialidades ON nom_esp_id=esp_id
                LEFT JOIN doctores ON nom_doc_id=doc_id
                JOIN pacientes USING (pac_id)
                WHERE esp_ficha AND nomd_diag_cod NOT IN ('X','T','B')
                AND pac_id=$pac_id AND $w_fecha AND $nomd1_w
                ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
                ";
            }
        
            //print($consulta);
            //die();
            //print("<br>");
        
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
            COALESCE((SELECT COALESCE(origen_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_origen,
            COALESCE((SELECT COALESCE(origen_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_origen,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((SELECT COALESCE(am_centro_ruta_origen,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_origen,
            COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_destino,
            esp_ficha,
            (case when ficha_espontanea.esp_id!=0 then upper(esp_desc) else (select centro_nombre from centro_costo where centro_ruta=fesp_centro_ruta) end)AS esp,
            0 as programada,
            fesp_centro_ruta
            FROM ficha_espontanea
            LEFT JOIN especialidades using(esp_id)
            LEFT JOIN doctores using (doc_id )
            LEFT JOIN archivo_motivos_prestamo USING (amp_id)
            JOIN pacientes USING (pac_id)
            WHERE (esp_ficha or esp_ficha is null) AND $w_fecha2 AND pac_id=$pac_id AND $nomd2_w AND fesp_estado=0
            GROUP BY fesp_fecha,esp_id, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
            pac_appat,pac_apmat, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id,amp_nombre
            ORDER BY fesp_fecha,esp_desc,doc_nombre";

            //print($consulta);
            

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
            }
            elseif($nomdl AND sizeof($nomdl)>1)
            {
                exit(json_encode(array($tmp,false,$nomdl), true));		
            }
        }
        
        /*
        $nomd_id=$nomd['nomd_id']*1;
        $esp_id=$nomd['esp_id_actual']*1;
        $doc_id=$nomd['doc_id_actual']*1;
        $esp_id2=$nomd['esp_id']*1;
        $doc_id2=$nomd['doc_id']*1;
        $func_id=$_SESSION['sgh_usuario_id']*1;
        
        if($tipo_inf==1 OR $tipo_inf==3)
            $estado=$_POST['estado_ficha']*1;
    
        if($tipo_inf==2)
        {
            $estado=4; $esp_id2=0; $doc_id2=0; 
            $chk=cargar_registro("SELECT * FROM archivo_movimientos WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha' AND am_final AND am_estado IN (2,3) ORDER BY am_fecha DESC;");
            if(!$chk)
                exit(json_encode(array($tmp,true), true));
            $esp_id=$chk['destino_esp_id']*1;
            $doc_id=$chk['destino_doc_id']*1;
        }
        if($estado==0 OR $estado==5)
        {
            $esp_id2=0;
            $doc_id2=0;
            $nomd_id=0;
        }
        pg_query("START TRANSACTION;");
        pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
        pg_query("INSERT INTO archivo_movimientos VALUES (
        DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true
        );");
        pg_query("COMMIT;");
        */
        
        //print_r($nomd);
        
        
        
        $nomd_id=$nomd['nomd_id']*1;
        $esp_id=$nomd['esp_id_actual']*1;
        $doc_id=$nomd['doc_id_actual']*1;
        //$centro_actual=$nomd['centro_ruta_actual'];
        $centro_ruta_origen=$nomd['centro_ruta_origen'];
        $centro_ruta_destino=$nomd['centro_ruta_destino'];
        $esp_id_origen=$nomd['esp_id_origen'];
        $tipo_solicitud=$nomd['programada']*1;
        
        if($nomd['esp_id']!="" && $nomd['esp_id']!="0")
        {
            $esp_id2=$nomd['esp_id']*1;
        }
        else
        {
            $esp_id2=pg_escape_string($nomd['fesp_centro_ruta']);
        }
        $doc_id2=$nomd['doc_id']*1;
        $func_id=$_SESSION['sgh_usuario_id']*1;
    
    
        if($tipo_inf==1 OR $tipo_inf==3)
            $estado=$_POST['estado_ficha']*1;
    
        if($tipo_inf==2)
        { 
            $estado=4;
            $esp_id2=0;
            $doc_id2=0;
            $chk=cargar_registro("SELECT * FROM archivo_movimientos WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha' AND am_final AND am_estado IN (2,3) ORDER BY am_fecha DESC;");
            if(!$chk)
                exit(json_encode(array($tmp,true), true));
            $esp_id=$chk['destino_esp_id']*1;
            $doc_id=$chk['destino_doc_id']*1;
        }
        if($estado==0 OR $estado==5)
        {
            $esp_id2=0;
            $doc_id2=0;
            $nomd_id=0;
        }
        pg_query("START TRANSACTION;");
    
    
        pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
    
    
        if(strstr($esp_id2,'.'))
        {
            if($esp_id=="" || $esp_id=="0" || $esp_id==0)
            {
                if($centro_ruta_origen!="")
                {
                    if($estado==3)
                    {
                        pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,'$centro_ruta_origen','$centro_ruta_destino','$tipo_solicitud');");
                    }
                    else
                    {
                        pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,'$centro_ruta_destino','$centro_ruta_origen','$tipo_solicitud');");
                        
                    }
                }
                else
                {
                    if($centro_ruta_destino!="")
                    {
                        pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, 0, $doc_id, 0, $doc_id2, $estado, '', true,'$centro_ruta_destino','$esp_id2','$tipo_solicitud');");
                    }
                    else
                    {
                        pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id_origen, $doc_id, 0, $doc_id2, $estado, '', true,null,'$esp_id2','$tipo_solicitud');");
                    }
                }
            }
            else
            {
                pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,null,'$esp_id2','$tipo_solicitud');");
            }
        }   
        else
        {
            if($esp_id=="" || $esp_id=="0" || $esp_id==0)
            {
                if($centro_ruta_destino!="")
                {
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, 0, $doc_id, $esp_id2, $doc_id2, $estado, '', true,'$centro_ruta_destino',null,'$tipo_solicitud');");    
                }
                else
                {
                    print("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id_origen, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id_origen, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");    
                }
            }
            else
            {
                if($estado==3)
                {
                    $esp_id=$esp_id_origen;
                }
                if($centro_ruta_origen!="")
                {
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,'$centro_ruta_origen',null,'$tipo_solicitud');");
                }
                else
                {
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");
                }
            }
        }
        pg_query("COMMIT;");
        
        exit(json_encode(array($tmp,true), true));
    }
    else
    {
        $func_id=$_SESSION['sgh_usuario_id'];
        $ficha=pg_escape_string($_POST['barras_unidad']);
        
        $encontrado_ficha=pg_escape_string($_POST['encontrado_ficha']);
	$encontrado_rut=pg_escape_string($_POST['encontrado_rut']);
	$fecha=pg_escape_string($_POST['fecha1']);
	$especialidad=pg_escape_string($_POST['ubicacion']);
        if(strstr($ficha,'-'))
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$ficha' LIMIT 1", true);
	else
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$ficha' LIMIT 1", true);
	if(!$tmp)
        {
            exit(json_encode(array(false)));		
        }
        else
        {
            if($tmp[0]['pac_ficha']==null or $tmp[0]['pac_ficha']=="" or $tmp[0]['pac_ficha']==0 or $tmp[0]['pac_ficha']=="0")
            {
                exit(json_encode(array(false)));
            }
        }
        
        
        $pac_id=$tmp[0]['pac_id']*1;
	$pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
        //----------------------------------------------------------------------
        $estado=4;
        $esp_id2=0;
        $doc_id2=0; 
        //print("SELECT * FROM archivo_movimientos WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha' AND am_final AND am_estado IN (2,3) ORDER BY am_fecha DESC;");
        
        
	$chk=cargar_registro("SELECT * FROM archivo_movimientos WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha' AND am_final AND am_estado IN (2,3) ORDER BY am_fecha DESC;");
	if(!$chk)
            exit(json_encode(array($tmp,true), true));
        
        
        $nomd_id=$chk['nomd_id']*1;
        $esp_id=$chk['destino_esp_id']*1;
	$doc_id=$chk['destino_doc_id']*1;
        $origen_esp_id=$chk['origen_esp_id']*1;
        $am_centro_ruta_origen=pg_escape_string($chk['am_centro_ruta_origen']);
        $am_centro_ruta_destino=pg_escape_string($chk['am_centro_ruta_destino']);
        $tipo_solicitud=$chk['am_tipo_solicitud'];
        
        
        
        pg_query("START TRANSACTION;");
        pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
        if($esp_id==0 || $esp_id=="0" || $esp_id=="")
        {
            pg_query("INSERT INTO archivo_movimientos VALUES(DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, 0, $doc_id, $esp_id2, $doc_id2, $estado, '', true,'$am_centro_ruta_destino',null,'$tipo_solicitud');");
        }
        else
        {
            pg_query("INSERT INTO archivo_movimientos VALUES(DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");
        }
        pg_query("COMMIT;");
        exit(json_encode(array($tmp,true), true));
        
        
        
        
    }
?>