<?php
    require_once('../../conectar_db.php');
    $pac_id=$_POST['pac_id']*1;
    $hosp_id=$_POST['hosp_id']*1;
    $bod_id=$_POST['bod_id']*1;
    $meds=json_decode($_POST['meds']);
    $valor_alta=$_POST['alta']*1;
    $observaciones = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_POST['obsv']));
    
    $chk=cargar_registro("SELECT hospitalizacion.*,tcama_centro_ruta FROM hospitalizacion JOIN clasifica_camas ON tcama_id=hosp_servicio where hosp_id=$hosp_id");
    if(!$chk)
    {
        print(json_encode("Hospitalizacion No Encontrada"));
        exit();
    }
    $diag_cod=$chk['hosp_diag_cod'];
    $t_cama_centro_ruta=$chk['tcama_centro_ruta'];
    $doc_id=$chk['hosp_doc_id']*1;
    $diagnostico=$chk['hosp_diagnostico'];
    $hosp_servicio=$chk['hosp_servicio']*1;
    //--------------------------------------------------------------------------
    $alta="false";
    if($valor_alta==1)
    {
        $alta="true";
    }
    
    pg_query("START TRANSACTION;");
    //--------------------------------------------------------------------------
    //GRABADO DE RECETAS
    if(sizeof($meds)>0)
    {
        $bodega_id=$bod_id;
	$prescrip=sizeof($meds);
	$tipo_talonario=0;
        $cronica='false';
        $cant_mayor=0;
        $cant_menor=0;
        $pos_mayor="";
        $pos_menor="";
        $separar=false;
       
        
        for($i=0;$i<sizeof($meds);$i++)
        {
            if(($meds[$i][5]*1)>30)
            {
                $cant_mayor++;
                $pos_mayor.="|$i";
            }
            else
            {
                $cant_menor++;
                $pos_menor.="|$i";
            }
        }
        
        if($cant_mayor>0 and $cant_menor==0)
            $separar=false;
            
        elseif($cant_mayor==0 and $cant_menor>0)
            $separar=false;
        elseif($cant_mayor>0 and $cant_menor>0)
            $separar=true;
        
        if($separar)
        {
            $num_recetas="";
            for($x=0;$x<2;$x++)
            {
                if($x==0)
                {
                    $cronica='false';
                    $pos_menor = substr($pos_menor, 1);
                    $pos=explode("|",$pos_menor);
                }
                else
                {
                    $cronica='true';
                    $pos_mayor = substr($pos_mayor, 1);
                    $pos=explode("|",$pos_mayor);
                }
                pg_query($conn, "INSERT INTO receta(
                receta_id,
                receta_paciente_id,
                receta_doc_id,
                receta_numero,
                receta_comentarios,
                receta_fecha_emision,
                receta_diag_cod,
                receta_centro_ruta,
                receta_func_id,
                receta_cronica,
                receta_tipotalonario_id,
                receta_series,
                receta_bod_id,
                receta_prescrip,
                receta_despacho_inicial,
                receta_despacho_total,
                receta_diagnostico,
                receta_prov_alta,
                receta_hosp_id
                )
                VALUES (
                DEFAULT,
                $pac_id,
                $doc_id,
                nextval('nro_receta_normal'),
                '$observaciones',
                current_timestamp,
                '".$diag_cod."',
                '$t_cama_centro_ruta',
                ".$_SESSION['sgh_usuario_id'].",
                $cronica,
                $tipo_talonario,
                '', $bodega_id,
                $prescrip,
                null,
                null,
                '".$diagnostico."',
                $alta,
                $hosp_id)
                ");
                
                for($i=0;$i<sizeof($meds);$i++)
                {
                    $save=false;
                    for($y=0;$y<count($pos);$y++)
                    {
                        if($i==($pos[$y]*1))
                        {
                            $save=true;
                            break;
                        }
                    }
                    if($save)
                    {
                        $art_id=pg_escape_string($meds[$i][0]*1);
                        $cod_presta=pg_escape_string($meds[$i][1]);
                        $glosa=pg_escape_string($meds[$i][2]);
                        $total=1*(($meds[$i][5]*24))/($meds[$i][4])*($meds[$i][3]);
                        $indica=pg_escape_string(utf8_decode($meds[$i][6]));
                        //print($indica);
                        $m=cargar_registro("SELECT * FROM articulo WHERE art_id=$art_id;");
                        $val=$m['art_val_ult']*1;
                        //Termina Prescripciones Anteriores
                        pg_query("UPDATE recetas_detalle SET recetad_terminada=true WHERE recetad_terminada=false AND recetad_art_id=$art_id AND recetad_receta_id IN
                        (SELECT receta_id FROM receta WHERE receta_paciente_id=$pac_id);");
                        //Inserta prescripción nueva
                        pg_query($conn, "
                        INSERT INTO recetas_detalle
                        VALUES (
                        DEFAULT,
                        $art_id,
                        ".$meds[$i][3].",
                        ".$meds[$i][4].",
                        ".$meds[$i][5].",
                        CURRVAL('receta_receta_id_seq'),
                        '$indica',
                        0,
                        false,
                        '$indica'
                        )");
                        //if(($meds[$i][5]*1)>30)
                        //{
                        //    pg_query("UPDATE receta SET receta_cronica=true WHERE receta_id=CURRVAL('receta_receta_id_seq')");
                        //}
                        pg_query("INSERT INTO prestacion VALUES (DEFAULT, CURRENT_TIMESTAMP, $pac_id, -1, 0, 0,'$cod_presta', '$cod_presta',".($total*1).",  false, ".$sgh_inst_id.",
                        $hosp_servicio, '', '$glosa', $val, 0, 100, ".$_SESSION['sgh_usuario_id'].", 0, 1, 0, 0);");	
                    }
                }
                $last_receta=cargar_registro("SELECT receta_numero FROM receta where receta_id=CURRVAL('receta_receta_id_seq');");
                if($last_receta)
                {
                    $num_recetas.=$last_receta['receta_numero']."|";
                }
            }
            $num_recetas = trim($num_recetas, '|');
        }
        else
        {
            
            pg_query($conn, "INSERT INTO receta(
                receta_id,
                receta_paciente_id,
                receta_doc_id,
                receta_numero,
                receta_comentarios,
                receta_fecha_emision,
                receta_diag_cod,
                receta_centro_ruta,
                receta_func_id,
                receta_cronica,
                receta_tipotalonario_id,
                receta_series,
                receta_bod_id,
                receta_prescrip,
                receta_despacho_inicial,
                receta_despacho_total,
                receta_diagnostico,
                receta_prov_alta,
                receta_hosp_id
                )
                VALUES (
                DEFAULT,
                $pac_id,
                $doc_id,
                nextval('nro_receta_normal'),
                '$observaciones',
                current_timestamp,
                '".$diag_cod."',
                '$t_cama_centro_ruta',
                ".$_SESSION['sgh_usuario_id'].",
                $cronica,
                $tipo_talonario,
                '', $bodega_id,
                $prescrip,
                null,
                null,
                '".$diagnostico."',
                $alta,
                $hosp_id)
                ");
            
            
            for($i=0;$i<sizeof($meds);$i++)
            {
                $art_id=pg_escape_string($meds[$i][0]*1);
                $cod_presta=pg_escape_string($meds[$i][1]);
                $glosa=pg_escape_string($meds[$i][2]);
                $total=1*(($meds[$i][5]*24))/($meds[$i][4])*($meds[$i][3]);
                $indica=pg_escape_string(utf8_decode($meds[$i][6]));
                //print($indica);
                $m=cargar_registro("SELECT * FROM articulo WHERE art_id=$art_id;");
                $val=$m['art_val_ult']*1;
                //Termina Prescripciones Anteriores
                pg_query("UPDATE recetas_detalle SET recetad_terminada=true WHERE recetad_terminada=false AND recetad_art_id=$art_id AND recetad_receta_id IN
                (SELECT receta_id FROM receta WHERE receta_paciente_id=$pac_id);");
						
                //Inserta prescripción nueva
                
                
                pg_query($conn, "
                INSERT INTO recetas_detalle
                VALUES (
                DEFAULT,
                $art_id,
                ".$meds[$i][3].",
                ".$meds[$i][4].",
                ".$meds[$i][5].",
                CURRVAL('receta_receta_id_seq'),
                '$indica',
                0,
                false,
                '$indica' 
                )");
                  
                 
                if(($meds[$i][5]*1)>30)
                {
                    pg_query("UPDATE receta SET receta_cronica=true WHERE receta_id=CURRVAL('receta_receta_id_seq')");
                }
                
                pg_query("INSERT INTO prestacion VALUES (DEFAULT, CURRENT_TIMESTAMP, $pac_id, -1, 0, 0,'$cod_presta', '$cod_presta',".($total*1).",  false, ".$sgh_inst_id.",
                $hosp_servicio, '', '$glosa', $val, 0, 100, ".$_SESSION['sgh_usuario_id'].", 0, 1, 0, 0);");	
            }
            $num_recetas="";
            $last_receta=cargar_registro("SELECT receta_numero FROM receta where receta_id=CURRVAL('receta_receta_id_seq');");
            if($last_receta)
            {
                $num_recetas=$last_receta['receta_numero'];
            }
        }
        
        
	//actualiza estado de recetas del paciente
        pg_query("UPDATE receta SET receta_vigente=foo3.estado FROM(
            SELECT *,receta_vigente,
            CASE WHEN total>terminadas THEN true ELSE false END AS estado 
            FROM(
                SELECT receta_id,receta_vigente,(SELECT count(*) FROM recetas_detalle WHERE recetad_receta_id=foo.receta_id)AS total,
                (SELECT count(*) FROM recetas_detalle WHERE recetad_receta_id=foo.receta_id AND recetad_terminada=true)AS terminadas
                FROM (
                    SELECT receta_id,receta_vigente FROM receta WHERE receta_paciente_id=$pac_id and receta_vigente and receta_fecha_cierre is null
                )AS foo
            )AS foo2
        )AS foo3 
        WHERE receta.receta_id=foo3.receta_id;");
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    pg_query("COMMIT;");
    print(json_encode($num_recetas));
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
?>