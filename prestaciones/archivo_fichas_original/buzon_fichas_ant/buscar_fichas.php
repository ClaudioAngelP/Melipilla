<?php
    require_once("../../../conectar_db.php");
    $especialidad=pg_escape_string($_POST['especialidad']);
    $tipo_busqueda=$_POST['tipo_busqueda'];
    $fecha = pg_escape_string($_POST['fecha1']); 
    $doc_id=pg_escape_string($_POST['doc_id']);
    if($tipo_busqueda=="1")
    {
        //----------------------------------------------------------------------
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
        $consulta="SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
        upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
        pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
        date_trunc('second',am_fecha)AS fecha_asigna,
        especialidades.esp_id,doctores.doc_id,
        COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
        COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
        COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
        COALESCE((SELECT COALESCE(am_fecha,null) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),null) as fecha_recepcion
        FROM archivo_movimientos
        LEFT JOIN especialidades ON destino_esp_id=esp_id
        LEFT JOIN doctores ON destino_doc_id=doc_id
        JOIN pacientes USING (pac_id)
        WHERE 
        am_final AND am_estado=3
        AND $esp
        ORDER BY esp_desc,doc_nombre,pac_ficha";
        
            
        
        $registros = cargar_registros_obj($consulta);
        if(!$registros)
        {
            $registros=false;
        }
      
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
        $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
        upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
        pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
        date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,
        especialidades.esp_id,doctores.doc_id,
        COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
        COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
        COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
        COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
        COALESCE((SELECT COALESCE(origen_esp_id) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=false ORDER BY am_fecha DESC LIMIT 1),0) as am_enviado_por				
        FROM nomina
        LEFT JOIN nomina_detalle USING (nom_id)
        LEFT JOIN especialidades ON nom_esp_id=esp_id
        LEFT JOIN doctores ON nom_doc_id=doc_id
        JOIN pacientes USING (pac_id)
        WHERE esp_ficha AND nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('X','T','B')
        and $esp
        and COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0)=2
        ORDER BY nom_fecha,esp_desc,doc_nombre,pac_ficha";
        $registros = cargar_registros_obj($consulta);
        if(!$registros)
        {
            $registros=false;
        }
    }
    if($tipo_busqueda=="4")
    {
        
         $ficha=pg_escape_string($_POST['ficha']);
	//$encontrado_ficha=pg_escape_string($_POST['encontrado_ficha']);
	//$encontrado_rut=pg_escape_string($_POST['encontrado_rut']);
	//$fecha=pg_escape_string($_POST['fecha1']);
	//$especialidad=pg_escape_string($_POST['ubicacion']);
        if(strstr($ficha,'-'))
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$ficha' LIMIT 1", true);
	else
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$ficha' LIMIT 1", true);
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
	COALESCE((SELECT COALESCE(destino_esp_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id  ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual,
	COALESCE((SELECT COALESCE(destino_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_actual,
	COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
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
	WHERE esp_ficha AND fesp_fecha::date='$fecha' AND pac_id=$pac_id AND $nomd2_w AND fesp_estado=0
	GROUP BY fesp_fecha,esp_id, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
	pac_appat,pac_apmat, especialidades.esp_desc, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id,amp_nombre
	ORDER BY fesp_fecha,esp_desc,doc_nombre";
        
        
	$nomdl2=cargar_registros_obj($consulta, true);
		   
	if($nomdl1 AND $nomdl2) $nomdl=array_merge($nomdl1,$nomdl2);
	else if($nomdl1 AND !$nomdl2) $nomdl=$nomdl1;
	else if(!$nomdl1 AND $nomdl2) $nomdl=$nomdl2;
	else $nomdl=false;
	if($nomdl AND sizeof($nomdl)==1)
        {
            $nomd=$nomdl[0];
	}
        elseif($nomdl AND sizeof($nomdl)>1)
        {
            exit(json_encode(array($tmp,false,$nomdl), true));		
	}
        
    }
        //$esp_id = $_POST['esp_id']*1;
        /*
        if($ubicacion!=-1)
        {
            if($ubicacion=="0")
            {
                //$esp="especialidades.esp_id=$esp_id";
                //$esp="especialidades.esp_id IN ("._cav(10003).")";
                //$servs="'".str_replace(',','\',\'',_cav2(10004))."'";
                //$servicioshtml = desplegar_opciones_sql("SELECT centro_ruta, centro_nombre FROM centro_costo WHERE centro_gasto AND centro_ruta IN (".$servs.") ORDER BY centro_nombre", NULL, '', "font-style:italic;color:#555555;");   
            }
            else
            {
                if(strstr($ubicacion,'.'))
                {
                    $esp="true";
                    $centro="centro_ruta='$ubicacion'";
                }
                else
                {
                    $esp="especialidades.esp_id=$ubicacion";
                    $centro="true";
                }
                //$esp="especialidades.esp_id=$esp_id";
            }
        }   
        else
        {
            $esp="true";
        }
        //--------------------------------------------------------------------------
        $doc_id= $_POST['doc_id']*1;
        //--------------------------------------------------------------------------
        if($doc_id!=-1)
            $doc="doctores.doc_id=$doc_id";
        else
            $doc="true";
        //--------------------------------------------------------------------------
        if($tipo_busqueda==1)
    {
        if(strstr($ubicacion,'.'))
        {
            $consulta="";
            $registros=false;
        }
        else
        {
            $consulta="SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',am_fecha)AS fecha_asigna,
            especialidades.esp_id,doctores.doc_id,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
            FROM archivo_movimientos
            LEFT JOIN especialidades ON destino_esp_id=esp_id
            LEFT JOIN doctores ON destino_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE 
            am_final AND am_estado=3
            AND $esp AND $doc
            ORDER BY esp_desc,doc_nombre,pac_ficha";
            
            
            
            $registros = cargar_registros_obj($consulta,true);
            if(!$registros)
            {
                $registros=false;
            }
            
            
        }
    }
    if($tipo_busqueda==2)
    {
        
        if(strstr($ubicacion,'.'))
        {
            $consulta="";
            $registros=false;
        }
        else
        {
            if(isset($_POST['option']))
            {
                $ficha=pg_escape_string($_POST['ficha']);
                if($_POST['option']=="2")
                {
                    $ficha_find=" and pac_ficha='$ficha' ";
                    $fecha=explode(" ",$fecha);
                    $fecha=$fecha[0];
                    $esp="true";
                    $doc="true";
                }
                else
                {
                    $ficha_find="";
                }
            }
            else
            {
                $ficha_find="";
            }
            
            
            
            
            $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,
            especialidades.esp_id,doctores.doc_id,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((
            select date(nom_fecha) from nomina where nom_id=(
            select nom_id from nomina_detalle where nomd_id=
            (
                SELECT COALESCE(nomd_id,0) FROM archivo_movimientos 
                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha  
            and am_final
            ORDER BY am_fecha DESC LIMIT 1
            ))),null)as fecha_solicitud_ficha,
            COALESCE((
            select nomd_hora from nomina_detalle where nomd_id=(
            SELECT COALESCE(nomd_id,0) FROM archivo_movimientos 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha  
            and am_final
            ORDER BY am_fecha DESC LIMIT 1)
            ),null)as hora_solicitud_ficha,
            COALESCE((
            select upper(func_nombre) from funcionario where func_id=(
            select nomd_func_id from nomina_detalle where nomd_id=(
            SELECT COALESCE(nomd_id,0) FROM archivo_movimientos 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha  
            and am_final
            ORDER BY am_fecha DESC LIMIT 1)
            )),null)as funcionario_solicitud_ficha,
            upper(func_nombre)as func_nombre_actual,
            COALESCE((SELECT COALESCE(origen_doc_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha and am_final ORDER BY am_fecha DESC LIMIT 1),0) as doc_id_anterior,
            COALESCE((SELECT COALESCE(am_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha and am_final ORDER BY am_fecha DESC LIMIT 1),0) as am_id_anterior,
            COALESCE((SELECT COALESCE(esp_id,0) FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_anterior,
            COALESCE((SELECT COALESCE(esp_id,0) FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as esp_id_actual
            FROM nomina
            LEFT JOIN nomina_detalle USING (nom_id)
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            LEFT JOIN doctores ON nom_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            LEFT JOIN funcionario on nomina_detalle.nomd_func_id=func_id
            WHERE nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' $ficha_find
            AND nomd_diag_cod NOT IN ('X','T','B')
            AND $esp AND $doc
            ORDER BY nom_fecha,esp_desc,doc_nombre,pac_ficha";
            
            //AND COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0)=0
            $registros = cargar_registros_obj($consulta);
            if(!$registros)
            {
                $registros=false;
            }
        }
    }
    //--------------------------------------------------------------------------
    if($tipo_busqueda==3)
    {
        if(strstr($ubicacion,'.'))
        {
            $consulta="";
            $registros=false;
        }
        else
        {
            $consulta="SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',am_fecha)AS fecha_asigna,
            especialidades.esp_id,doctores.doc_id,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
            FROM archivo_movimientos
            LEFT JOIN especialidades ON destino_esp_id=esp_id
            LEFT JOIN doctores ON destino_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE 
            am_final AND am_estado=2
            AND $esp AND $doc
            ORDER BY esp_desc,doc_nombre,pac_ficha";
            
            $registros = cargar_registros_obj($consulta,true);
            if(!$registros)
            {
                $registros=false;
            }
        }
    }
     * 
     */
    //--------------------------------------------------------------------------
    echo json_encode(array($registros));
?>