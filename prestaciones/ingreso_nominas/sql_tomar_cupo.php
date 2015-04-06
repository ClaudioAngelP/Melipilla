<?php
    require_once('../../conectar_db.php');
    $nom_id=$_POST['nom_id']*1;
    $pac_id=$_POST['pac_id']*1;
    $nomd_hora=pg_escape_string($_POST['nomd_hora']);
    $nomd_hora_extra=pg_escape_string($_POST['nomd_hora_extra']);
    $hora_extra=pg_escape_string($_POST['hora_extra']);
    
    pg_query("START TRANSACTION;");
    if(isset($_POST['pac_nombres']))
    {
        $pac_ficha=pg_escape_string(utf8_decode($_POST['pac_ficha']));
        $pac_pasaporte=pg_escape_string(utf8_decode($_POST['pac_pasaporte']));
        $pac_nombres=pg_escape_string(utf8_decode($_POST['pac_nombres']));
        $pac_appat=pg_escape_string(utf8_decode($_POST['pac_appat']));
        $pac_apmat=pg_escape_string(utf8_decode($_POST['pac_apmat']));
        $pac_fc_nac=pg_escape_string(utf8_decode($_POST['pac_fc_nac']));
        $sex_id=pg_escape_string(utf8_decode($_POST['sex_id']))*1;
        $pac_direccion=pg_escape_string(utf8_decode($_POST['pac_direccion']));
        $ciud_id=pg_escape_string(utf8_decode($_POST['ciud_id']))*1;
        $pac_fono=pg_escape_string(utf8_decode($_POST['pac_fono']));
        $pac_celular=pg_escape_string(utf8_decode($_POST['pac_celular']));
        $pac_recados=pg_escape_string(utf8_decode($_POST['pac_recados']));
        $pac_padre=pg_escape_string(utf8_decode($_POST['pac_padre']));
        $pac_ocupacion=pg_escape_string(utf8_decode($_POST['pac_ocupacion']));
        //$prev_id=pg_escape_string(utf8_decode($_POST['prev_id']))*1;
        pg_query("UPDATE pacientes SET 
        pac_fc_nac='$pac_fc_nac',
        pac_direccion='$pac_direccion',
        ciud_id=$ciud_id,
        pac_fono='$pac_fono',
        pac_celular='$pac_celular',
        pac_recados='$pac_recados',
        pac_padre='$pac_padre',
        pac_ocupacion='$pac_ocupacion'
        WHERE pac_id=$pac_id");
    }
    $tipo='N';
    
    if($nomd_hora=='00:00')
    {
        $extra='S';
        $nomd_hora=$nomd_hora_extra;
    }
    /*
    if($hora_extra=='S')
    {
        $extra='S';
    }
    else
    {
        $extra='N';
    }
    */
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $diag='';
    $sficha='';
    $diag_cod='';
    $motivo='';
    $destino='';
    $auge='';
    $estado='';
    $hora='';
    $n=cargar_registro("SELECT * FROM nomina WHERE nom_id=$nom_id");
    $folio=$n['nom_folio'];
    $doc_id=$n['nom_doc_id'];
    $esp_id=$n['nom_esp_id'];
    $fecha=$n['nom_fecha'];
    $nom_tipo=$n['nom_tipo']*1;
    $func_id=$_SESSION['sgh_usuario_id']*1;
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    if($extra=='S')
    {
        if($nom_tipo!=1)
        {
            $consulta="select nomina.*,(
            (select sum(cupos_cantidad_c) from cupos_atencion where cupos_atencion.nom_id=nomina.nom_id group by cupos_atencion.nom_id)
            -
            (SELECT COUNT(*) FROM nomina_detalle AS nd WHERE nd.nom_id=nomina.nom_id AND NOT pac_id=0 AND nomd_extra='S' AND (nomd_diag_cod NOT IN ('T') OR nomd_diag_cod IS NULL))
            )as extras_disponibles
            from nomina
            where nom_id=$nom_id";

            $tmp=cargar_registro($consulta);
            if($tmp)
            {
                if(($tmp['extras_disponibles']*1)>0)
                {
                    pg_query("INSERT INTO nomina_detalle VALUES (
                    DEFAULT,
                    $nom_id,
                    $pac_id,
                    '$tipo','$extra',
                    '$diag','$sficha',
                    '$diag_cod','$motivo',
                    '$destino','$auge',
                    '$estado',
                    0, '$nomd_hora', 'M', '$folio', $nom_id				
                    );");
                    $tmp=cargar_registro("SELECT CURRVAL('nomina_detalle_nomd_id_seq') AS id;");
                    $nomd_id=$tmp['id']*1;
                    pg_query("UPDATE nomina_detalle SET nomd_func_id=$func_id, nomd_fecha_asigna=CURRENT_TIMESTAMP WHERE nomd_id=$nomd_id;");

                }
                else
                {
                    $nomd_id="X";
                }

            }
            else
            {
                $nomd_id="X";
            }
        }
        else 
        {
            pg_query("INSERT INTO nomina_detalle VALUES (
            DEFAULT,
            $nom_id,
            $pac_id,
            '$tipo','N',
            '$diag','$sficha',
            '$diag_cod','$motivo',
            '$destino','$auge',
            '$estado',
            0, '$nomd_hora', 'M', '$folio', $nom_id				
            );");
            $tmp=cargar_registro("SELECT CURRVAL('nomina_detalle_nomd_id_seq') AS id;");
            $nomd_id=$tmp['id']*1;
            pg_query("UPDATE nomina_detalle SET nomd_func_id=$func_id, nomd_fecha_asigna=CURRENT_TIMESTAMP WHERE nomd_id=$nomd_id;");
        }
    }
    else
    {
    
        if(strstr($nomd_hora,'_'))
        {
            $cmp=explode('_',$nomd_hora);
            $nomd_id=$cmp[1];
            //print("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_id=$nomd_id AND pac_id=0 LIMIT 1;");
            //die();
            $tmp=cargar_registro("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_id=$nomd_id AND pac_id=0 LIMIT 1;");
            if($tmp)
            {
                pg_query("UPDATE nomina_detalle SET pac_id=$pac_id, nomd_tipo='$tipo', nomd_func_id=$func_id,nomd_fecha_asigna=CURRENT_TIMESTAMP  WHERE nom_id=$nom_id AND nomd_id=$nomd_id;");
                $tmp=cargar_registro("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_id=$nomd_id LIMIT 1;");
                $nomd_id=$tmp['id']*1;
            }
            else
            {
                $nomd_id="X";
            }
        }
        else
        {
            
            $tmp=cargar_registro("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora' AND pac_id=0 LIMIT 1;");
            if($tmp)
            {
                pg_query("UPDATE nomina_detalle SET pac_id=$pac_id, nomd_tipo='$tipo', nomd_func_id=$func_id,nomd_fecha_asigna=CURRENT_TIMESTAMP WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora' AND nomd_diag_cod NOT IN ('X','T','B') and pac_id=0 and (nomd_extra is null or  nomd_extra='N' or nomd_extra='');");
                $tmp=cargar_registro("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora' AND nomd_diag_cod NOT IN ('X','T','B') and (nomd_extra is null or  nomd_extra='N' or nomd_extra='') and pac_id=$pac_id LIMIT 1;");
                //pg_query("UPDATE nomina_detalle SET pac_id=$pac_id, nomd_tipo='$tipo', nomd_func_id=$func_id,nomd_fecha_asigna=CURRENT_TIMESTAMP WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora';");
                //$tmp=cargar_registro("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora' LIMIT 1;");
                $nomd_id=$tmp['id']*1;
            }
            else
            {
                $nomd_id="X";
            }
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if(isset($_POST['duracion']) AND $_POST['duracion']*1)
    {
        $bloques=($_POST['duracion']*1);
        pg_query("UPDATE nomina_detalle SET nomd_diag_cod='B', pac_id=$pac_id, nomd_fecha_asigna=CURRENT_TIMESTAMP, nomd_func_id=$func_id WHERE nomd_id in (SELECT nomd_id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora>'$nomd_hora' ORDER BY nomd_hora LIMIT $bloques);");
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($nomd_id!="X")
    {
        if(isset($_POST['codigos']))
        {
            if($_POST['codigos']!='')
            {
                $c=explode('|',pg_escape_string($_POST['codigos']));
                for($i=0;$i<sizeof($c);$i++)
                {
                    $codigo=$c[$i];
                    if(isset($_POST['presta_'.$i.'_'.$codigo]))
                        pg_query("INSERT INTO nomina_detalle_prestaciones VALUES (DEFAULT, $nomd_id, '$codigo', 1, 0);");
                }
            }
        }
    }
    
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($nomd_id!="X")
    {
        if(isset($_POST['reagendar'])) 
        {
            pg_query("UPDATE nomina_detalle SET nomd_func_id=$func_id, nomd_fecha_asigna=CURRENT_TIMESTAMP WHERE nomd_id=$nomd_id;");
        }
    }
    
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($nomd_id!="X")
    {
        if(isset($_POST['reagendar']))
        {
            if(($_POST['reagendar']*1)==1)
            {
                $nomd_id_ant=$_POST['nomd_id']*1;
                pg_query("UPDATE nomina_detalle SET nomd_estado='1' WHERE nomd_id=$nomd_id_ant;");
            }
        }
    }
    
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($nomd_id!="X")
    {
        if(isset($_POST['examen']))
        {
            if(($_POST['examen']*1)==1)
            {
                $sol_exam_id=$_POST['sol_exam_id']*1;
                $examenes=pg_escape_string($_POST['examenes_sol']);
                $examenes=str_replace("|",",",$examenes);
                pg_query("UPDATE solicitud_examen SET sol_estado=1 WHERE sol_exam_id=".$sol_exam_id.";");
                if($examenes!=""){
					pg_query("UPDATE solicitud_examen_detalle SET sol_examd_nomd_id=".$nomd_id." WHERE sol_examd_id IN (".$examenes.");");
				} else {
					$sol_examd_nomd_id=$_POST['sol_examd_nomd_id']*1;
					pg_query("UPDATE solicitud_examen_detalle SET sol_examd_nomd_id=".$nomd_id." WHERE sol_examd_solexam_id=".$sol_exam_id." AND sol_examd_nomd_id=".$sol_examd_nomd_id.";");
				}
            }
            
        } else {
            if(isset($_POST['examenes'])) {
				$examenes=json_decode($_POST['examenes'],true);
                $fecha_sol_exam=pg_escape_string($_POST['fecha_sol_exam']);
                
                
                $tipo_solicitud=$_POST['tipo_sol']*1;
                
				if($tipo_solicitud==1) {
					$origen='local';
					
					$esp_exam_solicita=$_POST['esp_exam_solicita']*1;
					
					$serv_exam_solicita=pg_escape_string($_POST['serv_exam_solicita']);
					
					$centro_exam_solicita=pg_escape_string($_POST['centro_exam_solicita']);
					
					$dau=pg_escape_string($_POST['txt_dau']);
					
					$opcion_prof=$_POST['opcion_prof']*1;
					
					if($opcion_prof==0) {
						$doc_id_exam=$_POST['doc_id_exam']*1;
					} else {
						$prof_rut_sol=pg_escape_string($_POST['prof_rut']);
						$prof_nombres_sol=pg_escape_string($_POST['prof_nombres']);
						$prof_paterno_sol=pg_escape_string($_POST['prof_paterno']);
						$prof_materno_sol=pg_escape_string($_POST['prof_materno']);
						
						$tmp=cargar_registro("SELECT * FROM doctores WHERE upper(doc_rut)=upper('".$prof_rut_sol."');");
						if($tmp){
							$doc_id_exam=$tmp['doc_id']*1;
						} else {
							pg_query($conn, "INSERT INTO doctores VALUES (DEFAULT, upper('".$prof_rut_sol."'),upper('".$prof_paterno_sol."'),upper('".$prof_materno_sol."'),upper('".$prof_nombres_sol."'));");
							$tmp=cargar_registro("SELECT * FROM doctores WHERE upper(doc_rut)=upper('".$prof_rut_sol."');");
							if($tmp){
								$doc_id_exam=$tmp['prof_id']*1;
							}
						}
					}
				} else {
					$origen='externa';
					
					$inst_sol=$_POST['inst_id_sol']*1;
					
					$esp_exam_solicita=$_POST['esp_id2_sol']*1;
						
					$opcion_prof=$_POST['opcion_prof']*1;
					
					if($opcion_prof==0){
						$doc_id_exam=$_POST['prof_id_sol']*1;
					} else {
						$prof_rut_sol=pg_escape_string($_POST['prof_rut']);
						$prof_nombres_sol=pg_escape_string($_POST['prof_nombres']);
						$prof_paterno_sol=pg_escape_string($_POST['prof_paterno']);
						$prof_materno_sol=pg_escape_string($_POST['prof_materno']);
							
						$tmp=cargar_registro("SELECT * FROM profesionales_externos WHERE upper(prof_rut)=upper('".$prof_rut_sol."');");
						if($tmp){
							$doc_id_exam=$tmp['prof_id']*1;
						} else {
							pg_query($conn, "INSERT INTO profesionales_externos VALUES (DEFAULT, upper('".$prof_paterno_sol."'),upper('".$prof_materno_sol."'),upper('".$prof_nombres_sol."'),upper('".$prof_rut_sol."'));");
							$tmp=cargar_registro("SELECT * FROM profesionales_externos WHERE upper(prof_rut)=upper('".$prof_rut_sol."');");
							if($tmp){
								$doc_id_exam=$tmp['prof_id']*1;
							}
						}
					}
				}
                
                $obsgeneral=pg_escape_string($_POST['obsgeneral']);
                $esp_exam=$_POST['esp_exam']*1;
                $grupo_exam=pg_escape_string($_POST['grupo_exam']);
                $array_examenes = array();
                $tipo_examen='';
                $cont=0;
                
                for($i=0;$i<count($examenes);$i++) {
                    if($tipo_examen!=$examenes[$i]['tipo_examen']) {
                        $array_examenes[$cont][0]=$examenes[$i]['tipo_examen'];
                        $array_examenes[$cont][1]=$examenes[$i]['esp'];
                        $tipo_examen=$examenes[$i]['tipo_examen'];
                        $cont=$cont+1;
                    }
                }
                
                for($i=0;$i<count($array_examenes);$i++) {
                    $tipo_examen=$array_examenes[$i][0];
                    $esp=$array_examenes[$i][1];
                    
                    if($tipo_solicitud==1) {
						
						if($centro_exam_solicita!="") {
							$centro=$centro_exam_solicita;
						} else {
							$centro=$serv_exam_solicita;
						}
						
						if($dau!="") {
							pg_query($conn, "INSERT INTO solicitud_examen VALUES (DEFAULT, ".$esp.",'".$tipo_examen."',".$pac_id.",".($_SESSION['sgh_usuario_id']*1).", '".$fecha_sol_exam." ".date("H:i:s")."',0,0,false,".$doc_id_exam.",0,'".$obsgeneral."','".$centro."','".$origen."',0,'".$dau."');");
						} else {
							pg_query($conn, "INSERT INTO solicitud_examen VALUES (DEFAULT, ".$esp.",'".$tipo_examen."',".$pac_id.",".($_SESSION['sgh_usuario_id']*1).", '".$fecha_sol_exam." ".date("H:i:s")."',0,0,false,".$doc_id_exam.",0,'".$obsgeneral."','".$centro."','".$origen."',0,null);");
						}
						
					} else {
						
						pg_query($conn, "INSERT INTO solicitud_examen VALUES (DEFAULT, ".$esp.",'".$tipo_examen."',".$pac_id.",".($_SESSION['sgh_usuario_id']*1).", '".$fecha_sol_exam." ".date("H:i:s")."',0,0,false,".$doc_id_exam.",".$esp_exam_solicita.",'".$obsgeneral."',null,'".$origen."',".$inst_sol.");");
						
					}
                    
                    
                    for($x=0;$x<count($examenes);$x++)  {
                        if($tipo_examen==$examenes[$x]['tipo_examen']) {
                            if(strstr($examenes[$x]['desc'],'[')) {
                                $array_organo=explode("[", $examenes[$x]['desc']);
                                $organo=trim($array_organo[1], ']');
                                //$organo=trim($myString, ',');
                            } else {
                                $organo='';
                            }
                            pg_query($conn, "INSERT INTO solicitud_examen_detalle VALUES (DEFAULT, CURRVAL('solicitud_examen_sol_exam_id_seq'),'".$examenes[$x]['pc_id']."','$organo','".$examenes[$x]['cantidad']."',$nomd_id,0,null,null,null,null,null,null,'".$examenes[$x]['obs_examen']."');");
                        }
                    }
                    
                    
                }
                
            }
        }
    }
    
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($nomd_id!="X")
    {
        if(isset($_POST['interconsulta']))
        {
            if(($_POST['interconsulta']*1)==1)
            {
                $inter_id=$_POST['inter_id']*1;
                pg_query("UPDATE nomina_detalle SET nomd_inter_id=$inter_id where nomd_id=$nomd_id;");
                pg_query("UPDATE interconsulta SET inter_nomd_id=$nomd_id,inter_estado=8 WHERE inter_id=$inter_id;");
                
            }
        }
    }
    
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    
    pg_query("COMMIT;");
    print($nomd_id);
?>
