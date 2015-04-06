<?php 
    require_once('../../../conectar_db.php');
    $tipo_mov=pg_escape_string($_POST['tipo_mov']*1);
    if($tipo_mov==1)
    {
        $ficha=pg_escape_string($_POST['barras']);
	$encontrado_ficha=pg_escape_string($_POST['encontrado_ficha']);
	$encontrado_rut=pg_escape_string($_POST['encontrado_rut']);
	$fecha=pg_escape_string($_POST['fecha1']);
	$especialidad=pg_escape_string($_POST['ubicacion']);
        if(strstr($ficha,'-'))
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$ficha' LIMIT 1", true);
	else
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$ficha' LIMIT 1", true);
	
        $pac_id=$tmp[0]['pac_id']*1;
	$pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
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
	if(isset($_POST['nomd_id_'.$pac_ficha]))
	{
            $nomd_id=$_POST['nomd_id_'.$pac_ficha];
            $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,
            especialidades.esp_id,doctores.doc_id,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON origen_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_anterior,
            COALESCE((SELECT COALESCE(esp_desc,'ARCHIVO') FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),'ARCHIVO') as ubic_actual,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
            COALESCE((SELECT COALESCE(origen_esp_id) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=false ORDER BY am_fecha DESC LIMIT 1),0) as am_enviado_por,
            COALESCE((SELECT COALESCE(origen_esp_id) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as origen_esp_id_act,				
            COALESCE((SELECT COALESCE(origen_doc_id) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as origen_doc_id,
            COALESCE((SELECT COALESCE(destino_esp_id) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as destino_esp_id,				
            COALESCE((SELECT COALESCE(destino_doc_id) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0) as destino_doc_id				
            FROM nomina
            LEFT JOIN nomina_detalle USING (nom_id)
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            LEFT JOIN doctores ON nom_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE $esp
            and nomd_id=$nomd_id
            and COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id and am_final=true ORDER BY am_fecha DESC LIMIT 1),0)=2
            ORDER BY nom_fecha,esp_desc,doc_nombre,pac_ficha";
                
            $registros = cargar_registros_obj($consulta);
            if($registros)
            {
                //$pac_id=$registros['pac_id']*1;
                $nomd_id=$registros[0]['nomd_id']*1;
		$esp_id=$registros[0]['origen_esp_id_act'];
		$doc_id=$registros[0]['origen_doc_id'];
		$esp_id2=$registros[0]['destino_esp_id'];
		$doc_id2=$registros[0]['destino_doc_id'];
		$func_id=$_SESSION['sgh_usuario_id'];
		//print("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$ficha';");
		//print("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, 3, '', true);");
		//die();
                pg_query("START TRANSACTION;");
		pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
		pg_query("INSERT INTO archivo_movimientos VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', $nomd_id, $esp_id, $doc_id, $esp_id2, $doc_id2, 3, '', true);");
		pg_query("COMMIT;");
		exit(json_encode(array($tmp,true), true));		
            }
            else
            {
                exit(json_encode(array(false,true), true));
            }
        }
    }
    if($tipo_mov==2)
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
	
        $pac_id=$tmp[0]['pac_id']*1;
	$pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
        
        
	
        //----------------------------------------------------------------------
        $estado=4; $esp_id2=0; $doc_id2=0; 
	$chk=cargar_registro("SELECT * FROM archivo_movimientos WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha' AND am_final AND am_estado IN (2,3) ORDER BY am_fecha DESC;");
	if(!$chk)
            exit(json_encode(array($tmp,true), true));
        $esp_id=$chk['destino_esp_id']*1;
	$doc_id=$chk['destino_doc_id']*1;
         pg_query("START TRANSACTION;");
        pg_query("UPDATE archivo_movimientos SET am_final=false WHERE pac_id=$pac_id AND pac_ficha='$pac_ficha';");
        
        
        pg_query("INSERT INTO archivo_movimientos VALUES(DEFAULT, CURRENT_TIMESTAMP, $func_id, $pac_id, '$pac_ficha', 0, $esp_id, $doc_id, $esp_id2, $doc_id2, $estado, '', true);");
        pg_query("COMMIT;");
        exit(json_encode(array($tmp,true), true));
    }
    
?>