<?php 
    require_once('../../conectar_db.php');
    if(!isset($_POST['llamada']))
    {
        $tipo_inf=$_POST['tipo_inf']*1;
        $fecha=pg_escape_string($_POST['fecha1']);
        $barras=trim($_POST['barras']);
        if($barras=='')
            exit(json_encode(false));
    
        if(strstr($barras,'-'))
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE upper(pac_rut)=upper('$barras') LIMIT 1", true);
        else
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$barras' LIMIT 1", true);
	
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
                $nomd1_w='true';
                $nomd2_w='true';
            }
            //if($tipo_inf==1)
            $consulta="
            SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            upper(pac_rut)as pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
            especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
            COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
            COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_actual,
            1 as programada
            FROM nomina_detalle
            LEFT JOIN nomina USING (nom_id)
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            LEFT JOIN doctores ON nom_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE esp_ficha AND nomd_diag_cod NOT IN ('X','T','B')
            AND pac_id=$pac_id AND nom_fecha::date='$fecha' AND $nomd1_w
            ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
            ";
        
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
            upper(pac_rut)as pac_rut,
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
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $nomd_id=$nomd['nomd_id']*1;
        $esp_id=$nomd['esp_id_actual']*1;
        $doc_id=$nomd['doc_id_actual']*1;
        $centro_actual=$nomd['centro_ruta_actual'];
        $doc_id2=$nomd['doc_id']*1;
        $tipo_solicitud=$nomd['programada']*1;
        $func_id=$_SESSION['sgh_usuario_id']*1;
        //----------------------------------------------------------------------
        if($nomd['esp_id']!="" && $nomd['esp_id']!="0" && $nomd['esp_id']!=0)
        {
            $esp_id2=$nomd['esp_id']*1;
        }
        else
        {
            $esp_id2=pg_escape_string($nomd['fesp_centro_ruta']);
        }
        
        //----------------------------------------------------------------------
        if($tipo_inf==1 OR $tipo_inf==3)
            $estado=$_POST['estado_ficha']*1;
        //----------------------------------------------------------------------
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
        //----------------------------------------------------------------------
        if($estado==0 OR $estado==5)
        {
            $esp_id2=0;
            $doc_id2=0;
            $nomd_id=0;
        }
        //----------------------------------------------------------------------
        pg_query("START TRANSACTION;");
        pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
        //----------------------------------------------------------------------
        if(strstr($esp_id2,'.'))
        {
            if($centro_actual!="")
            {
                if($estado==1)
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,null,'$esp_id2','$tipo_solicitud');");
                else
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,'$centro_actual','$esp_id2','$tipo_solicitud');");
            }
            else
            {
                if($estado==1)
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, 0, $doc_id, 0, $doc_id2, $estado, '', true,null,'$esp_id2','$tipo_solicitud');");
                else
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, 0, $doc_id2, $estado, '', true,null,'$esp_id2','$tipo_solicitud');");
            }
        }   
        else
        {
            if($centro_actual!="")
            {
                if($estado==1)
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");    
                else
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,'$centro_actual',null,'$tipo_solicitud');");    
            }
            else
            {
                if($estado==1)
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, 0, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");    
                else
                    pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true,null,null,'$tipo_solicitud');");    
            }
        }
        //----------------------------------------------------------------------
        pg_query("COMMIT;");
        exit(json_encode(array($tmp,true), true));
    }
    else
    {
        $llamada=$_POST['llamada']*1;
        if($llamada==1)
        {
            $fichas=json_decode($_POST['fichas'],true);
            $tipo_destino=$_POST['tipo_destino']*1;
            $destino_esp=$_POST['destino_esp']*1;
            $destino_serv=pg_escape_string($_POST['destino_serv']);
            $motivo_envio=$_POST['motivo_envio']*1;
            $size= count($fichas);
            if($tipo_destino==1)
            {
                $destino=$destino_esp;
                //$esp_id=$destino_esp;
                //$centro_actual="";
            }
            if($tipo_destino==2)
            {
                $destino=$destino_serv;
                //$esp_id=0;
                //$centro_actual=$destino_serv;
            }
            $estado=2;
            $func_id=$_SESSION['sgh_usuario_id']*1;
            pg_query("START TRANSACTION;");
            for($i=0;$i<$size;$i++)
            {
                $pac_id=$fichas[$i]['pac_id']*1;
                $pac_ficha=pg_escape_string($fichas[$i]['pac_ficha']);
                
                $consulta="
                SELECT nomina.nom_id,nomd_fecha_asigna AS fecha_sol, nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
                upper(pac_rut)as pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
                date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,'Programada' AS amp_nombre,
                especialidades.esp_id,especialidades.esp_desc,doctores.doc_id,
                COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
                COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
                COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                COALESCE((SELECT COALESCE(am_centro_ruta_destino,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),null) as centro_ruta_actual,
                1 as programada
                FROM nomina_detalle
                LEFT JOIN nomina USING (nom_id)
                LEFT JOIN especialidades ON nom_esp_id=esp_id
                LEFT JOIN doctores ON nom_doc_id=doc_id
                JOIN pacientes USING (pac_id)
                WHERE esp_ficha AND nomd_diag_cod NOT IN ('X','T','B')
                AND pac_id=$pac_id AND nom_fecha::date='".date("d/m/Y")."' 
                ORDER BY nom_fecha,esp_desc,doc_nombre,nomd_hora
                ";
                //print($consulta);
                //die();
                $nomdl1=cargar_registros_obj($consulta, true);

                $consulta="SELECT 
                fesp_fecha AS fecha_sol, 
                fesp_fecha::date AS fecha_asigna,
                esp_id,
                doc_rut,
                pacientes.pac_ficha, 
                (
                    case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
                    else (select func_nombre from funcionario where func_id=fesp_func_id) 
                    end 
                ) AS doc_nombre,
                upper(pac_rut)as pac_rut,
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
                WHERE (esp_ficha or esp_ficha is null) AND fesp_fecha::date='".date("d/m/Y")."' AND pac_id=$pac_id  AND fesp_estado=0
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
                else
                    $nomdl=false;
                
                //if(!$nomd)
                //{
                    //print($nomdl);
                    //die();
            
                    //$nomd_id=$nomd['nomd_id']*1;
                    //$esp_id=$nomd['esp_id_actual']*1;
                    //$doc_id=0;
                    //$esp_id_actual=
                    //$centro_actual=$destino;
                    
                    
                    /*
                    if($nomd['esp_id']!="" && $nomd['esp_id']!="0" && $nomd['esp_id']!=0)
                    {
                        $esp_id2=$nomd['esp_id']*1;
                    }
                    else
                    {
                        $esp_id2=pg_escape_string($nomd['fesp_centro_ruta']);
                    }
                    */
                    
                    
                    //$doc_id2=$nomd['doc_id']*1;
                    
                    pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
                    
                    if(strstr($destino,'.'))
                    {
                        pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,0,0,$pac_id,'$pac_ficha',current_timestamp,0,null,$motivo_envio,'$destino',$func_id)");
                        pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', CURRVAL('ficha_espontanea_fesp_id_seq'), 0, 0, 0, 0, $estado, null, true,null,'$destino','0');");
                    }
                    else
                    {
                        pg_query("INSERT INTO ficha_espontanea VALUES(DEFAULT,$destino,0,$pac_id,'$pac_ficha',current_timestamp,0,null,$motivo_envio,null,$func_id)");
                        pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', CURRVAL('ficha_espontanea_fesp_id_seq'), 0, 0, $destino, 0, $estado, null, true,null,null,'0');");
                    }
                //}
            }
            pg_query("COMMIT;");
            exit(json_encode(array(true,true), true));
        }
    }
?>
