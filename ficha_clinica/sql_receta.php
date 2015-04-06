<?php
    require_once('../conectar_db.php');
    $receta_id = ($_GET['receta_id']*1);
    
    $paciente = ($_GET['paciente']*1);
    if($paciente==0){
		die(json_encode(Array(false, 'PROBLEMA AL RESCATAR PACIENTE PARA LA RECETA.')));
	}
	
    
    
    
    $medicamentos = ($_GET['medica']);
    $observacion_m = ($_GET['observacion_m']);
    $diagnostico = pg_escape_string($_GET['nomd_diag_cod']);
    $fecha_retro=pg_escape_string($_GET['fecha_retro']);
    $centro_costo = pg_escape_string($_GET['centro_costo']);
    $centro_servicio = pg_escape_string($_GET['centro_servicio']);
    $rut_medico = pg_escape_string($_GET['rut_medico']);
    $despachar=isset($_GET['despachar']);
    $nombre_medico = pg_escape_string($_GET['nombre_medico']);
    
    /*
    $_SESSION['receta_centro_costo']=$centro_costo;
    $_SESSION['receta_centro_servicio']=$centro_servicio;
    $_SESSION['receta_rut_medico']=$rut_medico;
    $_SESSION['receta_nombre_medico']=$nombre_medico;
    $_SESSION['receta_doc_estamento']=pg_escape_string($_GET['doc_estamento']);
    $_SESSION['receta_doc_especialidad']=pg_escape_string($_GET['doc_especialidad']);
     * 
     */
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
    $_SESSION['receta_doc_id']=$doc_id;
    
    if($centro_servicio!='-1')
    {
        $centro_costo=$centro_servicio;
    }
    
    $num_serie= pg_escape_string(utf8_decode($_GET['num_serie']));
    $observaciones = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['observaciones']));
    
    
    
    
    if(isset($_GET['cheque']))
    {
       $cronica='false';
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
            if($receta_id==0)
            {
                if(pg_num_rows($comprobacion1)!=0)
                {
                    $dato = pg_fetch_row($comprobacion1);
                    if($dato[1]==0)
                        die(json_encode(Array(false, 'N&uacute;mero de Receta ya est&aacute; ingresado en el sistema.')));
                    else
                        die(json_encode(Array(false, 'N&uacute;mero de Receta est&aacute; inv&aacute;lida por extrav&iacute;o.')));
                }
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
    $ob_m = split("!",$observacion_m);
    
    
    
    
    
    if($receta_id==0)
    {
        pg_query($conn, "INSERT INTO receta VALUES (
            DEFAULT,
            $paciente,
            $doc_id,
            $numero,
            '$observaciones',
            '$fecha_retro ".date("H:i:s")."',
            '".$diagnostico."',
            '".$centro_costo."',
            ".$_SESSION['sgh_usuario_id'].",
            $cronica,
            $tipo_talonario,
            '$num_serie',
            $bodega_id,
            $prescrip)
        ");
        
        $_receta_id="CURRVAL('receta_receta_id_seq')";
    }
    else
    {
        $log=Array('fecha' => date('d/m/Y H:i:s'), 'usuario' => cargar_registro("select func_rut, func_nombre FROM funcionario WHERE func_id=".$_SESSION['sgh_usuario_id'], true), 'receta'=>cargar_registro("SELECT * FROM receta WHERE receta_id=$receta_id;", true),'receta_detalle'=>cargar_registro("SELECT * FROM recetas_detalle join articulo on recetad_art_id=art_id WHERE recetad_receta_id=$receta_id;", true));
        file_put_contents('log_recetas/'.date('Ymdhis').'__'.$receta_id.'.log',json_encode($log));
        pg_query($conn, "
	UPDATE receta
	SET
	receta_doc_id=$doc_id,
	receta_comentarios='$observaciones',
	receta_centro_ruta='$centro_costo',
	receta_diag_cod='".$diagnostico."',
	receta_func_id=".$_SESSION['sgh_usuario_id'].",
	receta_cronica=$cronica,
	receta_tipotalonario_id=$tipo_talonario,
	receta_series='$num_serie', 
	receta_bod_id=$bodega_id,
	receta_prescrip=$prescrip
	WHERE receta_id=$receta_id
	");			
			
	pg_query("DELETE FROM receta_adquiriente WHERE receta_id=$receta_id;");
	pg_query("DELETE FROM stock WHERE stock_log_id IN (SELECT log_id FROM logs WHERE log_recetad_id IN (SELECT recetad_id FROM recetas_detalle WHERE recetad_receta_id=$receta_id));");
	pg_query("DELETE FROM logs WHERE log_id IN (SELECT log_id FROM logs WHERE log_recetad_id IN (SELECT recetad_id FROM recetas_detalle WHERE recetad_receta_id=$receta_id));");
	pg_query("DELETE FROM recetas_detalle WHERE recetad_receta_id=$receta_id;");
			
	$_receta_id=$receta_id;
        
    }
    if(isset($_GET['cheque']))
    {
        $insertar_adq = pg_query($conn, "SELECT tipotalonario_adquiriente FROM receta_tipo_talonario WHERE tipotalonario_id=$tipo_talonario");
        if(pg_fetch_result($insertar_adq, 0, 0)=='t')
        {
            $adq_rut = pg_escape_string($_GET['adq_rut']);
            $adq_nombres = pg_escape_string(utf8_decode($_GET['adq_nombres']));
            $adq_appat = pg_escape_string(utf8_decode($_GET['adq_appat']));
            $adq_apmat = pg_escape_string(utf8_decode($_GET['adq_apmat']));
            $adq_direccion = pg_escape_string(utf8_decode($_GET['adq_direccion']));
            $adq_query = "INSERT INTO receta_adquiriente VALUES (
            DEFAULT,
            '$adq_rut',
            '$adq_nombres',
            '$adq_appat',
            '$adq_apmat',
            '$adq_direccion',
            0,
            $_receta_id
            )";
            pg_query($conn, $adq_query);
        }
    }
    
    for($i=0;$i<count($medica)-1;$i++)
    {
        $detalle = split("/",$medica[$i]);
        /*
        if($cronica=='true')
        {
            $mul1=24;
            $mul2=30;
        }
        else
        {
            $mul1=1;
            $mul2=1;
        }
         * 
         */
        if($detalle[5]*1>0 OR $detalle[6]*1>0 OR $detalle[7]*1>0)
            $indicaciones="'[M:".($detalle[5]*1)."|T:".($detalle[6]*1)."|N:".($detalle[7]*1)."]'";
        else
            $indicaciones='null';

        
	$observacion_med = pg_escape_string(iconv("UTF-8", "ISO-8859-1",$ob_m[$i]));

        /*
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
         * 
         */
        pg_query($conn, "
            INSERT INTO recetas_detalle VALUES (
            DEFAULT,
            ".$detalle[0].",
            ".$detalle[1].",
            ".($detalle[2]).",
            ".($detalle[3]).",
            $_receta_id,
            '$observacion_med',
            0,
            false,
            '$observacion_med'
            )
        ");	
        if($detalle[9]=='false')
            continue;
        if(($detalle[10]*1)==0)
        	continue;
        //print_r($detalle); 
        if(($cronica=='false' and $directo and $despachar) or ($cronica=='true' and $directo and $despachar))
        {
            $lotes = pg_query($conn, "SELECT * FROM lotes_vigentes(".$detalle[0].", $bodega_id);");
            $lote = pg_fetch_row($lotes);
            if(!$lote) {
                continue;
            }
            $lotes = pg_query($conn, "SELECT * FROM lotes_vigentes(".$detalle[0].", $bodega_id);");
            
            pg_query($conn, "INSERT INTO logs VALUES (DEFAULT,".$_SESSION['sgh_usuario_id'].",9,
            '$fecha_retro ".date("H:i:s")."', CURRVAL('recetas_detalle_recetad_id_seq'),
            NULL,0 )");
            
            if($cronica=='false' OR $detalle[3]<=30)
            {
                $cant = ceil(($detalle[3]*24/$detalle[2])*$detalle[1])/$detalle[4];
            }
            else
            {
                $cant = (($detalle[3]*24/$detalle[2])*$detalle[1])/$detalle[4];
                $cant = ceil($cant/($detalle[3]/30));
            }
            
            $cant=$detalle[10];
            
            $watchdog=0;
            while ($cant>0)
            {
                $watchdog++;
                if($watchdog>300)
                    die('Bucle infinito.');
          
                $lote = pg_fetch_row($lotes);
                //print_r($lote);
                if(!$lote)
                {
                    $cant=0;
                    continue;
                    
                }
                if($lote[0]<$cant)
                {
                    $cnt=$lote[0];
                }
                else
                {
                    $cnt=$cant;
                }
                if($lote[1]!='')
                    $vence = "'".pg_escape_string($lote[1])."'";
                else
                    $vence = 'null';
                
                
                
                $query="
                INSERT INTO 
                stock
		VALUES (
		DEFAULT,
		".$detalle[0].",
		$bodega_id,
		-(".$cnt."),
		CURRVAL('logs_log_id_seq'),
		".$vence.",
		0
                )
		";
                if($cnt>0)		
                    pg_query($conn, $query);
                $cant-=$cnt;
            }
            $log_idq=cargar_registro("SELECT CURRVAL('logs_log_id_seq') as id;");
            $_GET['log_id']=$log_idq['id']*1;
            if(($bodega_id!=2 AND $bodega_id!=23 AND $bodega_id!=53)OR isset($_GET['prov_alta']))
            {
                //imprimir_despacho($log_idq['id']);	
            }
        }	
        //}
    }
    if($receta_id==0)
    {
        $receta_creada = pg_query($conn, "SELECT CURRVAL('receta_receta_id_seq');");
        $receta_id = pg_fetch_row($receta_creada);
    }
    if(isset($_GET['cheque']))
    {
        $receta_numero=isset($_GET['nro_receta'])?($_GET['nro_receta']*1):0;
    }
    else
    {
        $receta = pg_query($conn, "SELECT receta_numero from receta where receta_id=$_receta_id;");
        $receta_numero = pg_fetch_row($receta);
    }
    if(isset($_GET['prov_alta']))
    {
        pg_query("UPDATE receta SET receta_prov_alta=true WHERE receta_id=$_receta_id;");
    }
    pg_query($conn, 'COMMIT;');		
    print(json_encode(Array(true, $receta_id,$receta_numero)));
    
    
        
    
    
    
        
        /*
        // Para ingreso directo, calcula los lotes a rebajar
        // y realiza la salida de medicamentos en forma directa...
        
        if(($cronica=='false' and $directo) or ($cronica=='true' and $directo and $despachar))
        {
            pg_query($conn, "INSERT INTO logs VALUES (
            DEFAULT,
            ".$_SESSION['sgh_usuario_id'].",
            9,
            '$fecha_retro ".date("H:i:s")."',
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
    * 
    */
?>
