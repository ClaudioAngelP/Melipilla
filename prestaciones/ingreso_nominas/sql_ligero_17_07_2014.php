<?php    require_once('../../conectar_db.php');    $nom_id=$_POST['nom_id']*1;    if(isset($_POST['nomd_id']))    {        $nomd_id=$_POST['nomd_id']*1;    }    else    {        $nomd_id=0;    }    $esp_id=$_POST['esp_id']*1;    $estado=$_POST['estado_nomina']*1;    $nom=cargar_registro("SELECT *, nom_fecha::date AS nom_fecha FROM nomina WHERE nom_id=$nom_id");    $esp_id=$nom['nom_esp_id']*1;    $doc_id=$nom['nom_doc_id']*1;    $fecha=pg_escape_string($nom['nom_fecha']);    $nd=cargar_registros_obj("SELECT nomina_detalle.* FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomina_detalle.nom_id=$nom_id and nomd_id=$nomd_id");    $proc=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=$esp_id");    if($proc)    {        $presta_proc=cargar_registros_obj("SELECT * FROM procedimiento_codigo WHERE esp_id=$esp_id");    }    pg_query("START TRANSACTION;");    pg_query("UPDATE nomina SET nom_func_id=".$_SESSION['sgh_usuario_id'].", nom_fecha_digitacion=current_timestamp, nom_estado_digitacion=$estado    WHERE nom_esp_id=$esp_id AND nom_doc_id=$doc_id AND nom_fecha::date='$fecha' and nom_id=$nom_id");    for($i=0;$i<sizeof($nd);$i++)    {        if(!isset($_POST['nomd_tipo_'.$nd[$i]['nomd_id']]))            continue;                                $tipo=pg_escape_string(utf8_decode($_POST['nomd_tipo_'.$nd[$i]['nomd_id']]));        $extra=pg_escape_string(utf8_decode($_POST['nomd_extra_'.$nd[$i]['nomd_id']]));        //$diag=pg_escape_string(utf8_decode($_POST['nomd_diag_'.$nd[$i]['nomd_id']]));        $sficha=pg_escape_string(utf8_decode($_POST['nomd_sficha_'.$nd[$i]['nomd_id']]));        $motivo=pg_escape_string(utf8_decode($_POST['nomd_motivo_'.$nd[$i]['nomd_id']].''.($_POST['nomd_motivo2_'.$nd[$i]['nomd_id']])));        $origen=pg_escape_string(utf8_decode($_POST['nomd_origen_'.$nd[$i]['nomd_id']]));        $auge=pg_escape_string(utf8_decode($_POST['nomd_auge_'.$nd[$i]['nomd_id']]));        $cod_cancela=pg_escape_string(utf8_decode($_POST['nomd_codigo_susp_'.$nd[$i]['nomd_id']]));        $cod_no_atiende=pg_escape_string(utf8_decode($_POST['nomd_codigo_no_atiende_'.$nd[$i]['nomd_id']]));        $inst_id=pg_escape_string(utf8_decode($_POST['nomd_institucion_'.$nd[$i]['nomd_id']]))*1;        $pac=cargar_registro("SELECT prev_id FROM pacientes WHERE pac_id=".$nd[$i]['pac_id']);	$prev_id=$pac['prev_id']*1;        if(!$proc)        {            $diag_cod=pg_escape_string(utf8_decode($_POST['nomd_diag_cod_'.$nd[$i]['nomd_id']]));            if($diag_cod=="N")            {                $nomd_diag="nomd_diag='',";            }            else            {                $nomd_diag="";            }                                    pg_query("UPDATE nomina_detalle SET            nomd_tipo='$tipo',            nomd_extra='$extra',            nomd_sficha='$sficha',            $nomd_diag            nomd_diag_cod='$diag_cod',            nomd_motivo='$motivo',            nomd_destino='',            nomd_auge='$auge',            nomd_origen='$origen',            nomd_codigo_cancela='$cod_cancela',            nomd_codigo_no_atiende='$cod_no_atiende',            nomd_prev_id=$prev_id,            inst_id=$inst_id            WHERE nomd_id=".$nd[$i]['nomd_id']);        }        else        {            //$diag_cod=isset($_POST['nomd_diag_cod_'.$nd[$i]['nomd_id']])?'':"nomd_diag_cod='NSP',";            $diag_cod=pg_escape_string(utf8_decode($_POST['nomd_diag_cod_'.$nd[$i]['nomd_id']]));            //$dia_cod            pg_query("UPDATE nomina_detalle SET            nomd_tipo='$tipo',            nomd_extra='$extra',            nomd_sficha='$sficha',            nomd_diag_cod='$diag_cod',            nomd_motivo='$motivo',            nomd_origen='$origen',            nomd_auge='$auge',            nomd_codigo_cancela='$cod_cancela',            nomd_codigo_no_atiende='$cod_no_atiende',            nomd_prev_id=$prev_id,            inst_id=$inst_id            WHERE nomd_id=".$nd[$i]['nomd_id']);                        if(!isset($_POST['nomd_diag_cod_'.$nd[$i]['nomd_id']]))                $diag_cod='NSP';        }	if($diag_cod=='NSP')        {            pg_query("DELETE FROM nomina_detalle_prestaciones WHERE nomd_id=".$nd[$i]['nomd_id']);            pg_query("DELETE FROM nomina_detalle_campos WHERE nomd_id=".$nd[$i]['nomd_id']);				            pg_query("DELETE FROM nomina_detalle_informe WHERE nomd_id=".$nd[$i]['nomd_id']);					}        $nomd_id=$nd[$i]['nomd_id']*1;	$nomd_hora=pg_escape_string($nd[$i]['nomd_hora']);        //if($diag_cod=='T' AND $nd[$i]['nomd_extra']!='S' AND $nd[$i]['nomd_diag_cod']!='T')        if($diag_cod=='T' AND $nd[$i]['nomd_diag_cod']!='T')        {            //SUSPENSION            // CREA UN CUPO DE REEMPLAZO POR EL CUPO SUSPENDIDO            if($extra!="S")            {                pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_hora, nomd_diag_cod, pac_id, nomd_extra) VALUES (DEFAULT, $nom_id, '$nomd_hora', '', 0,'$extra');");            }        }	if($diag_cod!='NSP' AND $proc AND sizeof($presta_proc)==1)        {            $chk=cargar_registros_obj("SELECT * FROM nomina_detalle_prestaciones WHERE nomd_id=".$nd[$i]['nomd_id']);            if(!$chk)            {                pg_query("INSERT INTO nomina_detalle_prestaciones VALUES (DEFAULT, ".$nd[$i]['nomd_id'].",'".pg_escape_string($presta_proc[0]['pc_codigo'])."', 1, ".($presta_proc[0]['pc_id']*1).");");            }         }                if($nomd_id!="")        {            $reg_inter=cargar_registro("select * from interconsulta where inter_nomd_id=$nomd_id");            if($reg_inter)            {                if($diag_cod=='T' AND $nd[$i]['nomd_diag_cod']!='T')                {                    //pg_query("INSERT INTO inter_nomd VALUES (DEFAULT,".$reg_inter['inter_id'].",$nomd_id,now());");                    //pg_query("update interconsulta set inter_estado=1, inter_nomd_id=0 where inter_id=".$reg_inter['inter_id']."");                                    }                                                                                            }        }    }    pg_query("COMMIT");    print(json_encode(true));?>