<?php
    error_reporting(E_ALL);
    //require_once('../../config.php');
    require_once('../../conectar_db.php');
    //require_once('../../conectores/sigh.php');
    require_once('../../fpdf/fpdf.php');
    $tipo=$_GET['tipo_inf']*1;
    $fecha = pg_escape_string($_GET['fecha']);
    
    if($tipo!=3)
    {
        $esp_id = $_GET['esp_id']*1;
        if($esp_id!=-1)
            $esp="especialidades.esp_id=$esp_id";
        else
            $esp="true";
        
    }
    if($tipo==3)
    {
        if(strstr($_GET['esp_id'],'.'))
        {
            $esp_id = pg_escape_string($_GET['esp_id']);
            $esp="fesp_centro_ruta='$esp_id'";
        }
        else
        {
            $esp_id = $_GET['esp_id']*1;
            if($esp_id!=-1)
                $esp="especialidades.esp_id=$esp_id";
            else
                $esp="true";
        }
        
    }
    
    
    $doc_id= $_GET['doc_id']*1;
    if($doc_id!=-1 and $doc_id!=0)
        $doc="doctores.doc_id=$doc_id";
    else
    {
        if($tipo==3)
        {
            if($doc_id==0)
            {
                $func_id=($_GET['func_id']*1);
                $doc="fesp_func_id=$func_id";
                
                /*
                $func_id=($_GET['func_id']*1);
                $doc="(
                case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
                else
                (select upper(func_nombre) from funcionario where func_id=fesp_func_id)
                end
                )=upper('$nombre_doc')";
                //$doc="true";
                /*
                $nombre_doc=pg_escape_string($_GET['doc_nombre']);
                $doc="upper(doc_nombres||' '||doc_paterno||' '||doc_materno)='$nombre_doc'";
                * 
                */
            }
            else
            {
                $doc="true";
            }
        }
        else
        {
            $doc="true";
        }
    }
	
    $agrupar=$_GET['agrupar']*1;
    if($agrupar==0) $agrupar_o='esp_desc,doc_nombre';
    if($agrupar==1) $agrupar_o='esp_desc';
	
    class PDF extends FPDF
    {
        function header()
        {
            $this->SetFont('Arial','B', 12);
            //$this->Image('../imagenes/logo_cementerio.jpg',0,5,40,35);
            //$this->Image('../imagenes/logo_corporacion.jpg',165,10,50,28);
            //$this->Image('../imagenes/boletin_backgr.jpg',90,120,180,180);
            $this->Image('logo_min.jpg',5,7,35,20);
            //$this->Ln(20);
            $this->SetX(40);
            $this->Cell(150,4,('Ministerio de Salud'),0,0,'L');	
            $this->Ln();
            $this->SetX(40);
            $this->Cell(150,4,('SS Metropolitano Occidente'),0,0,'L');	
            $this->Ln();
            $this->SetX(40);
            $this->Cell(150,4,('Hospital San José de Melipilla'),0,0,'L');	
            $this->Ln();
            $this->SetFontSize(14);		
            $this->SetY(30);	
	}
    }
	
    if($tipo==1 OR $tipo==3)
    {
        $nom_ant='';
	$doc_ant='';
	$esp_ant='';
	if($tipo==1)
        {
            /*
            $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,nomd_diag_cod,
            especialidades.esp_id,doctores.doc_id,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
            (SELECT COUNT(*) FROM nomina_detalle AS nd2 JOIN nomina AS n2 USING (nom_id) WHERE nd2.pac_id=pacientes.pac_id AND n2.nom_fecha=nomina.nom_fecha AND nomd_diag_cod NOT IN ('X','T','H')) AS peticiones,
            (SELECT COUNT(*) FROM ficha_espontanea AS fesp WHERE fesp.pac_id=pacientes.pac_id AND fesp.fesp_fecha::date=nomina.nom_fecha::date AND fesp_estado=0) AS peticiones2
            FROM nomina
            LEFT JOIN nomina_detalle USING (nom_id)
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            LEFT JOIN doctores ON nom_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE esp_ficha AND nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('B','T')
            AND $esp AND $doc
            ORDER BY nom_fecha,$agrupar_o,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
            */
            $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,nomd_diag_cod,
            especialidades.esp_id,doctores.doc_id,
            COALESCE(
                (
                    SELECT COALESCE(esp_desc,
                    ( 
                        SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') 
                        FROM archivo_movimientos 
                        LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                        AND archivo_movimientos.pac_id=pacientes.pac_id 
                        ORDER BY am_fecha DESC LIMIT 1 
                    )
                ) 
                FROM archivo_movimientos 
                LEFT JOIN especialidades ON origen_esp_id=esp_id 
                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_anterior
            ,
            COALESCE(
            (
                SELECT COALESCE(esp_desc,(
                    SELECT COALESCE('(' || centro_nombre ||')','ARCHIVO') FROM archivo_movimientos 
                    LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
                    WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                    ORDER BY am_fecha DESC LIMIT 1
                )) 
                FROM archivo_movimientos 
                LEFT JOIN especialidades ON destino_esp_id=esp_id 
                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_actual,
            COALESCE(
                    (
			SELECT COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno,func_nombre) 
			FROM archivo_movimientos 
			left join doctores on destino_doc_id=doc_id
			left join funcionario on am_func_id=func_id
			WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
			ORDER BY am_fecha DESC LIMIT 1
                    ),'0'
                    ) as doc_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
            (SELECT COUNT(*) FROM nomina_detalle AS nd2 JOIN nomina AS n2 USING (nom_id) WHERE nd2.pac_id=pacientes.pac_id AND n2.nom_fecha=nomina.nom_fecha AND nomd_diag_cod NOT IN ('X','T','H')) AS peticiones,
            (SELECT COUNT(*) FROM ficha_espontanea AS fesp WHERE fesp.pac_id=pacientes.pac_id AND fesp.fesp_fecha::date=nomina.nom_fecha::date AND fesp_estado=0) AS peticiones2
            FROM nomina
            LEFT JOIN nomina_detalle USING (nom_id)
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            LEFT JOIN doctores ON nom_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE esp_ficha AND nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('B','T')
            AND $esp AND $doc
            ORDER BY nom_fecha,$agrupar_o,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
            $salidas = cargar_registros_obj($consulta);
        }
        if($tipo==3)
        {
            $consulta="SELECT 
            fesp_fecha::date AS fecha_asigna,
            (case when ficha_espontanea.esp_id!=0 then upper(esp_desc) else (select upper(centro_nombre) from centro_costo where centro_ruta=fesp_centro_ruta) end)AS esp,
            doc_rut,
            pacientes.pac_ficha, 
            (
            case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
            else (select func_nombre from funcionario where func_id=fesp_func_id) 
            end ) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
            fesp_estado, especialidades.esp_id,doctores.doc_id ,fesp_id AS nomd_id,pac_id,amp_nombre,'' AS nomd_diag_cod,
            COALESCE(
            (
                SELECT COALESCE(esp_desc,
                ( 
                    SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') 
                    FROM archivo_movimientos 
                    LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
                    WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                    AND archivo_movimientos.pac_id=pacientes.pac_id 
                    ORDER BY am_fecha DESC LIMIT 1 
                )
            ) 
            FROM archivo_movimientos 
            LEFT JOIN especialidades ON origen_esp_id=esp_id 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
            ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_anterior
            ,
            COALESCE(
                    (
                        SELECT COALESCE(esp_desc,(
                                SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') FROM archivo_movimientos 
                                LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
                                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                                ORDER BY am_fecha DESC LIMIT 1
                        )) 
                        FROM archivo_movimientos 
                        LEFT JOIN especialidades ON destino_esp_id=esp_id 
                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                        ORDER BY am_fecha DESC LIMIT 1
                    ),'ARCHIVO'
                    ) as ubic_actual,
                    COALESCE(
                    (
			SELECT COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno,func_nombre) 
			FROM archivo_movimientos 
			left join doctores on destino_doc_id=doc_id
			left join funcionario on am_func_id=func_id
			WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
			ORDER BY am_fecha DESC LIMIT 1
                    ),'0'
                    ) as doc_actual,
                    COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                    COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
                    (SELECT COUNT(*) FROM nomina_detalle AS nd2 JOIN nomina AS n2 USING (nom_id) WHERE nd2.pac_id=pacientes.pac_id AND nom_fecha::date=ficha_espontanea.fesp_fecha::date AND nomd_diag_cod NOT IN ('X','T','H')) AS peticiones,
                    (SELECT COUNT(*) FROM ficha_espontanea AS fesp WHERE fesp.pac_id=pacientes.pac_id AND fesp.fesp_fecha::date=ficha_espontanea.fesp_fecha::date AND fesp.fesp_estado=0) AS peticiones2,
                    esp_ficha
                    FROM ficha_espontanea
                    LEFT JOIN especialidades using(esp_id)
                    LEFT JOIN doctores using (doc_id )
                    LEFT JOIN archivo_motivos_prestamo USING (amp_id)
                    LEFT JOIN centro_costo on fesp_centro_ruta=centro_ruta
                    JOIN pacientes USING (pac_id)
                    WHERE (esp_ficha or esp_ficha is null) AND fesp_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND fesp_estado=0 AND
                    $esp
                    AND $doc
                    GROUP BY fesp_fecha,esp_desc, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
                    pac_appat,pac_apmat, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id, amp_nombre
                    ORDER BY fesp_fecha,esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
                    
                    //print($consulta);
                    //die();
                    $salidas = cargar_registros_obj($consulta);
                }
	}
	elseif($tipo==2)
	{
            $nom_ant='';
            $doc_ant='';
            $esp_ant='';
            $salidas = cargar_registros_obj("SELECT am_fecha::date AS fecha,am_fecha::time AS nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
		pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,'' AS nomd_diag_cod, 
		date_trunc('second',
		COALESCE((SELECT nomd_fecha_asigna FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomina_detalle.pac_id=pacientes.pac_id AND nom_esp_id=destino_esp_id AND nom_doc_id=destino_doc_id ORDER BY nom_fecha DESC LIMIT 1),
		(SELECT fesp_fecha FROM ficha_espontanea WHERE ficha_espontanea.pac_id=pacientes.pac_id AND ficha_espontanea.esp_id=destino_esp_id AND ficha_espontanea.doc_id=destino_doc_id ORDER BY fesp_fecha DESC LIMIT 1)))AS fecha_asigna,
		especialidades.esp_id,doctores.doc_id,
		COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
		COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
		COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
		(SELECT am_fecha FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id AND am_estado=2 ORDER BY am_fecha DESC LIMIT 1) as fecha_envio
		FROM archivo_movimientos
		LEFT JOIN especialidades ON destino_esp_id=esp_id
		LEFT JOIN doctores ON destino_doc_id=doc_id
		JOIN pacientes USING (pac_id)
		WHERE 
		am_final AND am_estado IN (2,3) AND archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id
		AND $esp AND $doc
		ORDER BY esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint");
		
		// nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('X','T','B')
		// LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
				
	}
        elseif($tipo==4)
	{
            $nom_ant='';
            $doc_ant='';
            $esp_ant='';
            $esp_id = $_GET['esp_id'];
            $w_propias="";
            if(strstr($esp_id,'.'))
            {
                $servicio=true;
                $esp_id = pg_escape_string($esp_id);
                
                if(isset($_GET['propias'])) {
                    if(($_GET['propias']*1)==1) {
                        $func_id=$_SESSION['sgh_usuario_id']*1;
                        $w_propias=" AND fesp_func_id=$func_id";
                    }
                }
                
                $consulta="SELECT 
                fesp_fecha::date AS fecha_asigna,
                (
                    case when ficha_espontanea.esp_id!=0 then upper(esp_desc) 
                    else 
                    (select upper(centro_nombre) from centro_costo where centro_ruta=fesp_centro_ruta) 
                    end
                )AS esp,
                doc_rut,
                pacientes.pac_ficha, 
                (
                    case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
                    else 
                        (select func_nombre from funcionario where func_id=fesp_func_id) 
                    end 
                ) AS doc_nombre, 
                pac_rut,
                upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                fesp_estado, 
                especialidades.esp_id,
                doctores.doc_id ,
                fesp_id AS nomd_id,
                pac_id,
                amp_nombre,
                '' AS nomd_diag_cod,
                COALESCE
                (
                            (
                                SELECT 
                                COALESCE
                                        (
                                            esp_desc,
                                            ( 
                                                SELECT 
                                                COALESCE ('(' || centro_nombre || ')','ARCHIVO') 
                                                FROM archivo_movimientos 
                                                LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
                                                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                                                AND archivo_movimientos.pac_id=pacientes.pac_id 
                                                ORDER BY am_fecha DESC LIMIT 1 
                                            )
                                        ) 
                                FROM archivo_movimientos 
                                LEFT JOIN especialidades ON origen_esp_id=esp_id 
                                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                                ORDER BY am_fecha DESC LIMIT 1
                            )
                            ,'ARCHIVO'
                ) as ubic_anterior
                ,
                COALESCE
                (
                    (
                        SELECT 
                        COALESCE
                                (
                                    esp_desc,
                                    (
                                        SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO')
                                        FROM archivo_movimientos 
                                        LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
                                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                                        ORDER BY am_fecha DESC LIMIT 1
                                    )
                                ) 
                        FROM archivo_movimientos 
                        LEFT JOIN especialidades ON destino_esp_id=esp_id 
                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                        ORDER BY am_fecha DESC LIMIT 1
                    ),'ARCHIVO'
                ) as ubic_actual,
                COALESCE
                (
                    (
                        SELECT COALESCE(am_estado,0) 
                        FROM archivo_movimientos 
                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                        AND archivo_movimientos.pac_id=pacientes.pac_id 
                        ORDER BY am_fecha DESC LIMIT 1
                    )
                    ,0
                ) as am_estado,
                COALESCE
                (
                    (
                        SELECT COALESCE(nomd_id,0) 
                        FROM archivo_movimientos 
                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                        AND archivo_movimientos.pac_id=pacientes.pac_id 
                        ORDER BY am_fecha DESC LIMIT 1
                    )
                    ,0
                ) as nomd_id_sel,
                (
                    SELECT COUNT(*) FROM nomina_detalle AS nd2 
                    JOIN nomina AS n2 USING (nom_id) 
                    WHERE nd2.pac_id=pacientes.pac_id 
                    AND nom_fecha::date=ficha_espontanea.fesp_fecha::date 
                    AND nomd_diag_cod NOT IN ('X','T','H')
                ) AS peticiones,
                (
                    SELECT COUNT(*) FROM ficha_espontanea AS fesp 
                    WHERE fesp.pac_id=pacientes.pac_id 
                    AND fesp.fesp_fecha::date=ficha_espontanea.fesp_fecha::date AND fesp.fesp_estado=0
                ) AS peticiones2,
                esp_ficha,
                COALESCE(
                            (
                                SELECT COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno,func_nombre) 
                                FROM archivo_movimientos 
                                left join doctores on destino_doc_id=doc_id
                                left join funcionario on am_func_id=func_id
                                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                                ORDER BY am_fecha DESC LIMIT 1
                            )
                            ,
                            '0'
                ) as doc_actual
                FROM ficha_espontanea
                LEFT JOIN especialidades using(esp_id)
                LEFT JOIN doctores using (doc_id )
                LEFT JOIN archivo_motivos_prestamo USING (amp_id)
                JOIN pacientes USING (pac_id)
                WHERE (esp_ficha or esp_ficha is null) AND fesp_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND fesp_estado=0
                AND fesp_centro_ruta='$esp_id' 
                AND $doc
                $w_propias
                GROUP BY fesp_fecha,esp_desc, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
                pac_appat,pac_apmat, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id, amp_nombre
                ORDER BY fesp_fecha,esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
                //print($consulta);
                //die();
                $salidas = cargar_registros_obj($consulta);
            }
            else
            {
                $servicio=false;
                $tipo_solicitud = $_GET['proceso']*1;
                $nom_ant='';
                $doc_ant='';
                $esp_ant='';
                if($tipo_solicitud==1)
                {
                    $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
                    upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
                    pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
                    date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,nomd_diag_cod,
                    especialidades.esp_id,doctores.doc_id,
                    COALESCE(
                        (
                            SELECT COALESCE(esp_desc,
                            ( 
                                SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') 
                                FROM archivo_movimientos 
                                LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
                                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                                AND archivo_movimientos.pac_id=pacientes.pac_id 
                                ORDER BY am_fecha DESC LIMIT 1 
                            )
                        ) 
                        FROM archivo_movimientos 
                        LEFT JOIN especialidades ON origen_esp_id=esp_id 
                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                        ORDER BY am_fecha DESC LIMIT 1
                    ),'ARCHIVO'
                    ) as ubic_anterior
                    ,
                    COALESCE(
                    (
                        SELECT COALESCE(esp_desc,(
                            SELECT COALESCE('(' || centro_nombre ||')','ARCHIVO') FROM archivo_movimientos 
                            LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
                            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                            ORDER BY am_fecha DESC LIMIT 1
                        )) 
                        FROM archivo_movimientos 
                        LEFT JOIN especialidades ON destino_esp_id=esp_id 
                        WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                        ORDER BY am_fecha DESC LIMIT 1
                    ),'ARCHIVO'
                    ) as ubic_actual,
                    COALESCE(
                    (
			SELECT COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno,func_nombre) 
			FROM archivo_movimientos 
			left join doctores on destino_doc_id=doc_id
			left join funcionario on am_func_id=func_id
			WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
			ORDER BY am_fecha DESC LIMIT 1
                    ),'0'
                    ) as doc_actual,
                    COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
                    COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
                    (SELECT COUNT(*) FROM nomina_detalle AS nd2 JOIN nomina AS n2 USING (nom_id) WHERE nd2.pac_id=pacientes.pac_id AND n2.nom_fecha=nomina.nom_fecha AND nomd_diag_cod NOT IN ('X','T','H')) AS peticiones,
                    (SELECT COUNT(*) FROM ficha_espontanea AS fesp WHERE fesp.pac_id=pacientes.pac_id AND fesp.fesp_fecha::date=nomina.nom_fecha::date AND fesp_estado=0) AS peticiones2
                    FROM nomina
                    LEFT JOIN nomina_detalle USING (nom_id)
                    LEFT JOIN especialidades ON nom_esp_id=esp_id
                    LEFT JOIN doctores ON nom_doc_id=doc_id
                    JOIN pacientes USING (pac_id)
                    WHERE esp_ficha AND nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('B','T')
                    AND $esp AND $doc
                    ORDER BY nomd_hora,$agrupar_o,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
                    $salidas = cargar_registros_obj($consulta);
                    
                    
                }
                if($tipo_solicitud==2)
                {
                    if(isset($_GET['propias'])) {
                        if(($_GET['propias']*1)==1) {
                            $func_id=$_SESSION['sgh_usuario_id']*1;
                            $w_propias=" AND fesp_func_id=$func_id";
                        }
                    }
                    $consulta="SELECT 
                    fesp_fecha::date AS fecha_asigna,
                    (
                        case when ficha_espontanea.esp_id!=0 then upper(esp_desc) 
                        else 
                        (select upper(centro_nombre) from centro_costo where centro_ruta=fesp_centro_ruta) 
                        end
                    )AS esp,
                    doc_rut,
                    pacientes.pac_ficha, 
                    (
                        case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
                        else 
                            (select func_nombre from funcionario where func_id=fesp_func_id) 
                        end 
                    ) AS doc_nombre, 
                    pac_rut,
                    upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
                    fesp_estado, 
                    especialidades.esp_id,
                    doctores.doc_id ,
                    fesp_id AS nomd_id,
                    pac_id,
                    amp_nombre,
                    '' AS nomd_diag_cod,
                    COALESCE
                    (
                                (
                                    SELECT 
                                    COALESCE
                                            (
                                                esp_desc,
                                                ( 
                                                    SELECT 
                                                    COALESCE ('(' || centro_nombre || ')','ARCHIVO') 
                                                    FROM archivo_movimientos 
                                                    LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
                                                    WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                                                    AND archivo_movimientos.pac_id=pacientes.pac_id 
                                                    ORDER BY am_fecha DESC LIMIT 1 
                                                )
                                            ) 
                                    FROM archivo_movimientos 
                                    LEFT JOIN especialidades ON origen_esp_id=esp_id 
                                    WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                                    ORDER BY am_fecha DESC LIMIT 1
                                )
                                ,'ARCHIVO'
                    ) as ubic_anterior
                    ,
                    COALESCE
                    (
                        (
                            SELECT 
                            COALESCE
                                    (
                                        esp_desc,
                                        (
                                            SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO')
                                            FROM archivo_movimientos 
                                            LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
                                            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                                            ORDER BY am_fecha DESC LIMIT 1
                                        )
                                    ) 
                            FROM archivo_movimientos 
                            LEFT JOIN especialidades ON destino_esp_id=esp_id 
                            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                            ORDER BY am_fecha DESC LIMIT 1
                        ),'ARCHIVO'
                    ) as ubic_actual,
                    COALESCE(
                    (
			SELECT COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno,func_nombre) 
			FROM archivo_movimientos 
			left join doctores on destino_doc_id=doc_id
			left join funcionario on am_func_id=func_id
			WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
			ORDER BY am_fecha DESC LIMIT 1
                    ),'0'
                    ) as doc_actual,
                    COALESCE
                    (
                        (
                            SELECT COALESCE(am_estado,0) 
                            FROM archivo_movimientos 
                            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                            AND archivo_movimientos.pac_id=pacientes.pac_id 
                            ORDER BY am_fecha DESC LIMIT 1
                        )
                        ,0
                    ) as am_estado,
                    COALESCE
                    (
                        (
                            SELECT COALESCE(nomd_id,0) 
                            FROM archivo_movimientos 
                            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                            AND archivo_movimientos.pac_id=pacientes.pac_id 
                            ORDER BY am_fecha DESC LIMIT 1
                        )
                        ,0
                    ) as nomd_id_sel,
                    (
                        SELECT COUNT(*) FROM nomina_detalle AS nd2 
                        JOIN nomina AS n2 USING (nom_id) 
                        WHERE nd2.pac_id=pacientes.pac_id 
                        AND nom_fecha::date=ficha_espontanea.fesp_fecha::date 
                        AND nomd_diag_cod NOT IN ('X','T','H')
                    ) AS peticiones,
                    (
                        SELECT COUNT(*) FROM ficha_espontanea AS fesp 
                        WHERE fesp.pac_id=pacientes.pac_id 
                        AND fesp.fesp_fecha::date=ficha_espontanea.fesp_fecha::date AND fesp.fesp_estado=0
                    ) AS peticiones2,
                    esp_ficha
                    FROM ficha_espontanea
                    LEFT JOIN especialidades using(esp_id)
                    LEFT JOIN doctores using (doc_id )
                    LEFT JOIN archivo_motivos_prestamo USING (amp_id)
                    JOIN pacientes USING (pac_id)
                    WHERE (esp_ficha or esp_ficha is null) AND fesp_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND fesp_estado=0
                    AND $esp
                    AND $doc
                    $w_propias
                    GROUP BY fesp_fecha,esp_desc, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
                    pac_appat,pac_apmat, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id, amp_nombre
                    ORDER BY fesp_fecha,esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
                    //print($consulta);
                    //die();
                    $salidas = cargar_registros_obj($consulta);
                    
                    
                }
            }
        }
        

	//print_r($salidas);
	$opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
	$opts_color=Array('black','gray','blue','purple','green','red');
	if($tipo==2 AND !$salidas)
	{
		print("<center><h1>(No tiene fichas pendientes por recepcionar...)</h1></center>");
                exit();
  	}
        if($tipo==4 AND !$salidas)
	{
		print("<center><h1>(No tiene fichas pendientes solicitadas para el dia ( $fecha )...)</h1></center>");
                exit();
  	}
	if($salidas)
	{
            if($tipo!=4)
                $pdf=new PDF('P','mm','Letter');
            else
                $pdf=new PDF('L','mm','Letter');
            //$pdf=new PDF('P', 'mm', '200, 300');  
            $pdf->AliasNbPages();
            //$pdf->SetAutoPageBreak(true,20);
            //$pdf->AddPage();
            for($i=0;$i<count($salidas);$i++)
            {
                ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                $checked='';
                $color='';
                
                if($salidas[$i]['doc_id']!="") {
                    $doc_string=$salidas[$i]['doc_id'];
                } else {
                    $doc_string=$salidas[$i]['doc_nombre'];
                }
                
                if($salidas[$i]['esp_id']!="") {
                    $esp_string=$salidas[$i]['esp_id'];
                } else {
                    $esp_string=$salidas[$i]['esp'];
                }
                //if(($agrupar==0 AND $doc_ant!=$salidas[$i]['doc_id']) OR $esp_ant!=$salidas[$i]['esp_id'])
                if(($agrupar==0 AND $doc_ant!=$doc_string) OR $esp_ant!=$esp_string)
                {
                    //$doc_ant=$salidas[$i]['doc_id'];
                    if($salidas[$i]['doc_id']!=""){
                        $doc_ant=$salidas[$i]['doc_id'];
                    } else {
                        $doc_ant=$salidas[$i]['doc_nombre'];
                    }
                    //$esp_ant=$salidas[$i]['esp_id'];
                    if($salidas[$i]['esp_id']!="")
                    {
                        $esp_ant=$salidas[$i]['esp_id'];
                    }
                    else
                    {
                        $esp_ant=$salidas[$i]['esp'];
                    }
                    $cont=1;
                    //if($i>0)
                        //print("</table>");

                    if(isset($salidas[$i]['amp_nombre']) AND $salidas[$i]['amp_nombre']!='')
                    {
                        $motivo='<br/>Motivo Solicitud: <b>'.htmlentities($salidas[$i]['amp_nombre']).'</b>';
                    }
                    else
                    {
                        $motivo='';
                    }
                    $pdf->AddPage();
                    $pdf->SetFillColor(250,250,250);	
                    $pdf->SetFont('Arial','', 10);
                    $pdf->SetFillColor(230,230,230);
                    $pdf->Cell(200,7,('Programa: '.$salidas[$i]['esp'].'          Fecha Listado: '.$fecha),0,0,'L',1);
                    if($agrupar==0)
                    {
                        $pdf->Ln();
                        $pdf->Cell(200,7,('Profesional / Servicio: '.$salidas[$i]['doc_nombre'].''),0,0,'L',1);
                    }
                    $pdf->Ln();
                    $pdf->SetFillColor(200,200,200);
                    $pdf->SetFont('Arial','',8);
                    //$pdf->Cell(9,9,'#',1,0,'C',1);
                    //$pdf->Cell(12,9,'Hora',1,0,'C',1);
                    //$pdf->Cell(21,9,'Solicitado',1,0,'C',1);
                    if($tipo==2)
                        $pdf->Cell(20,9,'Enviada',1,0,'C',1);
                                
                    $pdf->Cell(10,9,'Nº',1,0,'C',1);
                    $pdf->Cell(25,9,'Ficha',1,0,'C',1);
                    //if($tipo==1 OR $tipo==3)
                        //$pdf->Cell(9,9,'P*',1,0,'C',1);
                    $pdf->Cell(30,9,'RUN',1,0,'C',1);
                    $pdf->Cell(65,9,'Nombre Completo',1,0,'C',1);
                    if($tipo!=2)
                    {
                        //$pdf->Cell(30,9,'Ubic. Anterior',1,0,'C',1);
			$pdf->Cell(60,9,'Ubic. Actual',1,0,'C',1);
                    }
                    if($tipo==4)
                    {
                        $pdf->Cell(30,9,'Estado Actual',1,0,'C',1);
                        if($servicio or $tipo_solicitud==2)
                        {
                            $pdf->Cell(30,9,'Motivo',1,0,'C',1);
                        }
                        else
                        {
                            $pdf->Cell(20,9,'Hora',1,0,'C',1);
                        }
                    }
                    /*
                    print("<table style='width:100%;' class='lista_small' cellspacing=0 cellpadding=1>");
                    */
                }
                $options='';
                for($l=0;$l<sizeof($opts);$l++)
                {
                    if($salidas[$i]['am_estado']*1==$l)
                        $sel='SELECTED';
                    else
                        $sel='';
                    
                    $options.='<option value="'.$l.'" '.$sel.'>'.$opts[$l].'</option>';
                }
                if($salidas[$i]['pac_ficha']!='' and $salidas[$i]['pac_ficha']!='0')
                {
                    $ficha=$salidas[$i]['pac_ficha'];
                }
                else
                {
                    //$ficha="<center>";
                    //$ficha.="Sin Asignar</center>";
                    $ficha="Sin Asignar";
                }
                if($tipo!=2)
                {
                    if($salidas[$i]['nomd_id_sel']*1==$salidas[$i]['nomd_id']*1)
                    {
                        $color='background-color:#bbbbff;';
                    }
                    else
                    {
                        $color='';
                    }
                }
                if($salidas[$i]['nomd_diag_cod']=='X' OR $salidas[$i]['nomd_diag_cod']=='T')
                {
                    $tachar='text-decoration:line-through;';
        	}
        	else
        	{
                    $tachar='';
        	}
                $pdf->Ln();
                //$pdf->Cell(9,9,$cont,1,0,'C');
		//$pdf->Cell(12,9,substr($salidas[$i]['nomd_hora'],0,5),1,0,'C',1);
                //$pdf->Cell(21,9,substr($salidas[$i]['fecha_asigna'],0,16),1,0,'C');
                if($tipo==2)
                    $pdf->Cell(20,9,substr($salidas[$i]['fecha_envio'],0,16),1,0,'C');
                
                $pdf->SetFont('','B');
                $pdf->SetFont('Arial','',10);
                $pdf->Cell(10,9,$cont,1,0,'C',1);
                $pdf->Cell(25,9,$ficha,1,0,'C');
                $pdf->SetFont('Arial','',8);
                $pdf->SetFont('','');
                //if($tipo==1 OR $tipo==3)
                    //$pdf->Cell(9,9,(($salidas[$i]['peticiones']*1)+($salidas[$i]['peticiones2']*1)),1,0,'C');
                $pdf->Cell(30,9,$salidas[$i]['pac_rut'],1,0,'C');
                $pdf->SetFont('Arial','',8);
                $pdf->Cell(65,9,$salidas[$i]['pac_nombre'],1,0,'L');
                if($tipo!=2)
                {
                    $pdf->SetFont('Arial','',6);
                    //$pdf->Cell(30,9,htmlentities($salidas[$i]['ubic_anterior']),1,0,'C');
                    //doc_actual
                    if(strlen($salidas[$i]['ubic_actual'])>25)
                    {
                        $str_ubicacion=substr($salidas[$i]['ubic_actual'],0,25).'...';
                    }
                    else
                    {
                        $str_ubicacion=$salidas[$i]['ubic_actual'];
                    }
                    if($salidas[$i]['ubic_actual']!='ARCHIVO')
                    {
                        if($salidas[$i]['esp']!=$salidas[$i]['ubic_actual'])
                        {
                            $str_ubicacion=$str_ubicacion."-(".$salidas[$i]['doc_actual'].")";
                        }
                    }
                    $pdf->Cell(60,9,$str_ubicacion,1,0,'C');
                }
                $pdf->SetFont('Arial','',7);
                if($tipo==4)
                {
                    if(($salidas[$i]['am_estado']*1)==0)
                    {
                        if($salidas[$i]['ubic_actual']=="ARCHIVO")
                        {
                            $pdf->Cell(30,9,"En Archivo",1,0,'C');
                        }
                        else
                        {
                            $pdf->Cell(30,9,$opts[$salidas[$i]['am_estado']*1],1,0,'C');
                        }
                    }
                    else
                    {
                        $pdf->Cell(30,9,$opts[$salidas[$i]['am_estado']*1],1,0,'C');
                    }
                    if($servicio or $tipo_solicitud==2)
                    {
                        $pdf->Cell(30,9,$salidas[$i]['amp_nombre'],1,0,'C');
                    }
                    else
                    {
                        $pdf->Cell(20,9,substr($salidas[$i]['nomd_hora'],0,5),1,0,'C');
                    }
                }
        	//$nom_ant=$salidas[$i]['nom_id'];
                $cont++;
            }
            /*
            print("</table>");
            */
	}
	$pdf->Output('fichas_'.date("Ymd").'.pdf','I');
?>
