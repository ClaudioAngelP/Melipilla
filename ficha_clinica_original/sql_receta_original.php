<?php
    require_once('../conectar_db.php');
    $paciente = ($_GET['paciente']*1);
    $medicamentos = ($_GET['medica']);
    $diagnostico = pg_escape_string($_GET['nomd_diag_cod']);
    $centro_costo = pg_escape_string($_GET['centro_costo']);
    $centro_servicio = pg_escape_string($_GET['centro_servicio']);
    $rut_medico = pg_escape_string($_GET['rut_medico']);
    $despachar=isset($_GET['despachar']);
    if(isset($_GET['cronica']))
        $cronica='true';
    else
        $cronica='false';
    
    if(isset($_GET['directo']))
    { 
        $bodega_id = ($_GET['bodega_id']*1);
        $directo=true; 
    }
    else
        $directo=false;
    
    $id_med = pg_query($conn,"SELECT doc_id FROM doctores WHERE doc_rut='".$rut_medico."'");
    $doc = pg_fetch_row($id_med);
    $doc_id=($doc[0]*1);
    if($centro_servicio!='-1')
    {
        $centro_costo=$centro_servicio;
    }
    $num_serie= pg_escape_string(utf8_decode($_GET['num_serie']));
    $observaciones = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['observaciones']));
    if(isset($_GET['cheque']))
    {
        $tipo_talonario=($_GET['tipo_talonario']*1);
        $numero=isset($_GET['nro_receta'])?($_GET['nro_receta']*1):0;
        if($tipo_talonario==3)
        {
            $numero="nextval('nro_receta_normal')";
        }
        else
        {
            if($tipo_talonario==0 or $numero==0)
                die(json_encode(Array(false, 'Tipo de Talonario  y/o N&uacute;mero de Receta ingresados son incorrectos.')));
            
            $comprobacion1 = pg_query($conn, "
                SELECT receta_numero, 0 as tipo FROM receta WHERE
                receta_numero=".$numero." AND
                receta_tipotalonario_id=".$tipo_talonario."
                UNION
                SELECT receta_numero, 1 as tipo FROM receta_anulada WHERE
                receta_numero=".$numero." AND
                receta_tipotalonario_id=".$tipo_talonario."
            ");
      
            if(pg_num_rows($comprobacion1)!=0)
            {
                $dato = pg_fetch_row($comprobacion1);
                if($dato[1]==0)
                    die(json_encode(Array(false, 'N&uacute;mero de Receta ya est&aacute; ingresado en el sistema.')));
                else
                    die(json_encode(Array(false, 'N&uacute;mero de Receta est&aacute; inv&aacute;lida por extrav&iacute;o.')));
            }
        }
        // Comprueba talonario válido...
        /*
        $comprobacion2 = pg_query($conn, "SELECT verificar_talonario($tipo_talonario, $numero);");
        $verificar = pg_fetch_row($comprobacion2);
        if($verificar[0]=='f')
        {
            die(json_encode(Array(false, 'N&uacute;mero ingresado no corresponde a ning&uacute;n talonario v&aacute;lido.')));
        }
        */
    }
    else
    {
        $tipo_talonario=0;
        $numero="nextval('nro_receta_normal')";
    }		  
    pg_query($conn, 'START TRANSACTION;');
    // Ingresa Receta...
    $medica = split("!", $medicamentos);
    $prescrip=sizeof($medica)-1;
    pg_query($conn, "INSERT INTO receta VALUES (
        DEFAULT,
        $paciente,
        $doc_id,
        $numero,
        '$observaciones',
        current_timestamp,
        '".$diagnostico."',
        '".$centro_costo."',
        ".$_SESSION['sgh_usuario_id'].",
        $cronica,
        $tipo_talonario,
        '$num_serie',
        $bodega_id,
        $prescrip)
    ");	
    if(isset($_GET['cheque']))
    {
        $insertar_adq = pg_query($conn, "SELECT tipotalonario_adquiriente FROM receta_tipo_talonario WHERE tipotalonario_id=$tipo_talonario");
        if(pg_fetch_result($insertar_adq, 0, 0)=='t')
        {
            $adq_rut = pg_escape_string($_GET['adq_rut']);
            $adq_nombres = pg_escape_string($_GET['adq_nombres']);
            $adq_appat = pg_escape_string($_GET['adq_appat']);
            $adq_apmat = pg_escape_string($_GET['adq_apmat']);
            $adq_direccion = pg_escape_string($_GET['adq_direccion']);
            $adq_query = "INSERT INTO receta_adquiriente VALUES (
            DEFAULT,
            '$adq_rut',
            '$adq_nombres',
            '$adq_appat',
            '$adq_apmat',
            '$adq_direccion',
            0,
            CURRVAL('receta_receta_id_seq')
            )";
            pg_query($conn, $adq_query);
        }
    }
    
    for($i=0;$i<count($medica)-1;$i++)
    {
        $detalle = split("/",$medica[$i]);
        if($cronica=='true')
        {
            $mul1=24; $mul2=30;
        }
        else
        {
            $mul1=1; $mul2=1;
        }
        if($detalle[5]*1>0 OR $detalle[6]*1>0 OR $detalle[7]*1>0)
            $indicaciones="'[M:".($detalle[5]*1)."|T:".($detalle[6]*1)."|N:".($detalle[7]*1)."]'";
        else
            $indicaciones='null';
        
        pg_query($conn, "
            INSERT INTO recetas_detalle VALUES (
            DEFAULT,
            ".$detalle[0].",
            ".$detalle[1].",
            ".($detalle[2]*$mul1).",
            ".($detalle[3]*$mul2).",
            CURRVAL('receta_receta_id_seq'),
            $indicaciones
            )
        ");	
        
        // Para ingreso directo, calcula los lotes a rebajar
        // y realiza la salida de medicamentos en forma directa...
        
        if(($cronica=='false' and $directo) or ($cronica=='true' and $directo and $despachar))
        {
            pg_query($conn, "INSERT INTO logs VALUES (
            DEFAULT,
            ".$_SESSION['sgh_usuario_id'].",
            9,
            current_timestamp,
            CURRVAL('recetas_detalle_recetad_id_seq'),
            NULL,
            0 )
            ");	
          
            if($cronica=='false')
            {
                $cant = ceil((($detalle[3]*24/$detalle[2])*$detalle[1])/$detalle[4]);
            }
            else
            {
                $cant = ((($detalle[3]*30)*24/($detalle[2]*24))*$detalle[1])/$detalle[4];
                $cant = ceil($cant/($detalle[3]));
            }
            pg_query("SELECT rebajar_articulos(".$detalle[0].", $bodega_id, CURRVAL('logs_log_id_seq'), $cant);");
        }				
    }
    $receta_creada = pg_query($conn, "SELECT CURRVAL('receta_receta_id_seq');");
    $receta_id = pg_fetch_row($receta_creada);
    if(isset($_GET['prov_alta']))
    {
        pg_query("UPDATE receta SET receta_prov_alta=true WHERE receta_id=CURRVAL('receta_receta_id_seq');");
    }
    pg_query($conn, 'COMMIT;');		
    print(json_encode(Array(true, $receta_id)));
?>