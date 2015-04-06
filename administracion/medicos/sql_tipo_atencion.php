<?php
    require_once("../../conectar_db.php");
    $nomd_id=($_POST['nomd_id']*1);
    $doc_id=($_POST['doc_id']*1);
    $esp_id=($_POST['esp_id']*1);
    $fecha=pg_escape_string($_POST['fecha']);
    $nomd_hora=pg_escape_string($_POST['nomd_hora']);
    $tipo_atencion_original=pg_escape_string($_POST['tipo_atencion_original']);
    $tipo_atencion_nueva=pg_escape_string($_POST['tipo_atencion_nueva']);
    $cupo_id=($_POST['cupo_id']*1);
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    function resta ($inicio, $fin)
    {
        $dif=date("H:i:s", strtotime("00:00:00") + strtotime($fin) - strtotime($inicio));
        return $dif;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $consulta="SELECT nom_id FROM nomina_detalle WHERE nomd_id=$nomd_id";
    $consulta="SELECT nom_id,(SELECT count(*) FROM nomina_detalle WHERE nom_id=foo.nom_id)as cantidad FROM nomina_detalle as foo WHERE nomd_id=$nomd_id";
    $reg_nomina_detalle=cargar_registro($consulta, true);
    if($reg_nomina_detalle)
    {
        $nom_id_ori=$reg_nomina_detalle['nom_id'];
        $cantidad=$reg_nomina_detalle['cantidad'];
    }
    else
    {
        $reg_nomina_detalle=false;
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    $consulta="select * from nomina_detalle join nomina USING (nom_id) 
    where nom_fecha='$fecha' and nomina.nom_esp_id=$esp_id and nom_doc_id=$doc_id and nom_motivo!='$tipo_atencion_original'
    and nomd_hora='$nomd_hora'  and nomd_diag_cod NOT IN ('H','T')";
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    $reg_hora=cargar_registros_obj($consulta, true);
    if($reg_hora)
    {
       $existe_hora=true;
       exit(json_encode(Array($existe_hora)));
    }
    else
    {
        $existe_hora=false;
    }
    
    if(!$existe_hora)
    {
        pg_query("START TRANSACTION;");
        $consulta="SELECT * FROM nomina
        WHERE nomina.nom_fecha='$fecha' AND nomina.nom_esp_id=$esp_id AND nomina.nom_doc_id=$doc_id AND nomina.nom_motivo='$tipo_atencion_nueva'";
        //----------------------------------------------------------------------
        //----------------------------------------------------------------------
        $nom_destino=cargar_registro($consulta, true);
        if($nom_destino)
        {
            A
            $nom_id_destino=$nom_destino['nom_id']*1;
            pg_query("UPDATE nomina_detalle set nom_id=$nom_id_destino,nomd_func_id=".$_SESSION['sgh_usuario_id']." where nomina_detalle.nomd_id=$nomd_id;");
            //pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_extra, nomd_hora, pac_id, nomd_diag_cod, nomd_func_id, nomd_fecha_asigna,nomd_presta_glosa,nomd_motivo_sobrecupo) VALUES (DEFAULT, ".$nom_id_destino.", 'N', 'N', '$nomd_hora', 0, '', ".$_SESSION['sgh_usuario_id'].", null,'null',null);");
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            if($reg_nomina_detalle)
            {
                if(($cantidad*1)==1)
                {
                    pg_query("DELETE FROM cupos_atencion WHERE nom_id=$nom_id_ori;");
                    pg_query("DELETE FROM nomina WHERE nom_id=$nom_id_ori;");
                }
                else
                {
                    pg_query("UPDATE cupos_atencion 
                        SET cupos_horainicio=(
                            SELECT 
                            min(nomd_hora) 
                            FROM nomina_detalle 
                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$cupo_id." AND NOT nomd_hora='00:00:00'
                            and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                            AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                        ),
                        cupos_horafinal=(
                            SELECT max(nomd_hora) FROM nomina_detalle 
                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$cupo_id." AND NOT nomd_hora='00:00:00' 
                            and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                            AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                        )+('15 minutes'::interval),
                        cupos_cantidad_n=
                        (
                            SELECT COUNT(*) FROM nomina_detalle 
                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$cupo_id." and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                        )
                        WHERE cupos_atencion.nom_id=$nom_id_ori and cupos_id=".$cupo_id."");
                }
                $consulta="SELECT * from cupos_atencion WHERE nom_id=$nom_id_destino order by cupos_horainicio asc";
                $reg_cupos=cargar_registros_obj($consulta, true);
                if($reg_cupos)
                {
                    for($i=0;$i<count($reg_cupos);$i++)
                    {
                        if(strtotime($nomd_hora)<strtotime($reg_cupos[$i]['cupos_horainicio']))
                        {
                            pg_query("UPDATE cupos_atencion SET cupos_horainicio='$nomd_hora' WHERE cupos_id=".$reg_cupos[$i]['cupos_id']."");
                            
                            pg_query("UPDATE cupos_atencion 
                            SET cupos_horainicio=(
                                SELECT 
                                min(nomd_hora) 
                                FROM nomina_detalle 
                                join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00'
                                and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                            ),
                            cupos_horafinal=(
                                SELECT max(nomd_hora) FROM nomina_detalle 
                                join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00' 
                                and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                            )+('15 minutes'::interval),
                            cupos_cantidad_n=
                            (
                                SELECT COUNT(*) FROM nomina_detalle 
                                join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                    AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                            )
                            WHERE cupos_atencion.nom_id=$nom_id_destino and cupos_id=".$reg_cupos[$i]['cupos_id']."");
                            break;
                        }
                        else
                        {
                            if(strtotime($nomd_hora)<strtotime($reg_cupos[$i]['cupos_horafinal']))
                            {
                                
                                pg_query("UPDATE cupos_atencion 
                                SET cupos_horainicio=(
                                    SELECT 
                                    min(nomd_hora) 
                                    FROM nomina_detalle 
                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00'
                                    and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                    AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                ),
                                cupos_horafinal=(
                                    SELECT max(nomd_hora) FROM nomina_detalle 
                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00' 
                                    and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                    AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                )+('15 minutes'::interval),
                                cupos_cantidad_n=
                                (
                                    SELECT COUNT(*) FROM nomina_detalle 
                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                        AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                )
                                WHERE cupos_atencion.nom_id=$nom_id_destino and cupos_id=".$reg_cupos[$i]['cupos_id']."");
                                break;
                            }
                            else
                            {
                                if(($i+1)==count($reg_cupos))
                                {
                                    if(strtotime($nomd_hora)>strtotime($reg_cupos[$i]['cupos_horafinal']))
                                    {
                                        
                                        pg_query("UPDATE cupos_atencion SET cupos_horafinal='$nomd_hora' WHERE cupos_id=".$reg_cupos[$i]['cupos_id']."");
                                        pg_query("UPDATE cupos_atencion 
                                        SET cupos_horainicio=(
                                            SELECT 
                                            min(nomd_hora) 
                                            FROM nomina_detalle 
                                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00'
                                            and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                            AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                        ),
                                        cupos_horafinal=(
                                            SELECT max(nomd_hora) FROM nomina_detalle 
                                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00' 
                                            and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                            AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                            )+('15 minutes'::interval),
                                        cupos_cantidad_n=
                                        (
                                            SELECT COUNT(*) FROM nomina_detalle 
                                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                                AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                        )
                                        WHERE cupos_atencion.nom_id=$nom_id_destino and cupos_id=".$reg_cupos[$i]['cupos_id']."");
                                        break;
                                    }
                                    
                                }
                                else
                                {
                                    if(count($reg_cupos)==1)
                                    {
                                        
                                        pg_query("UPDATE cupos_atencion SET cupos_horafinal='$nomd_hora' WHERE cupos_id=".$reg_cupos[$i]['cupos_id']."");
                                        pg_query("UPDATE cupos_atencion 
                                        SET cupos_horainicio=(
                                            SELECT 
                                            min(nomd_hora) 
                                            FROM nomina_detalle 
                                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00'
                                            and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                            AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                        ),
                                        cupos_horafinal=(
                                            SELECT max(nomd_hora) FROM nomina_detalle 
                                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00' 
                                            and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                            AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                            )+('15 minutes'::interval),
                                        cupos_cantidad_n=
                                        (
                                            SELECT COUNT(*) FROM nomina_detalle 
                                            join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                            AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                        )
                                        WHERE cupos_atencion.nom_id=$nom_id_destino and cupos_id=".$reg_cupos[$i]['cupos_id']."");
                                        break;
                                    }
                                    else
                                    {
                                        if(strtotime($nomd_hora)<strtotime($reg_cupos[$i+1]['cupos_horainicio']))
                                        {
                                            $diferencia_1=resta($reg_cupos[$i]['cupos_horafinal'],$nomd_hora);
                                            $diferencia_2=resta($nomd_hora,$reg_cupos[$i+1]['cupos_horainicio']);
                                            if(strtotime($diferencia_1)<strtotime($diferencia_2))
                                            {
                                                pg_query("UPDATE cupos_atencion SET cupos_horafinal='$nomd_hora' WHERE cupos_id=".$reg_cupos[$i]['cupos_id']."");
                                                pg_query("UPDATE cupos_atencion 
                                                SET cupos_horainicio=(
                                                    SELECT 
                                                    min(nomd_hora) 
                                                    FROM nomina_detalle 
                                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00'
                                                    and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                                    AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                                ),
                                                cupos_horafinal=(
                                                    SELECT max(nomd_hora) FROM nomina_detalle 
                                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." AND NOT nomd_hora='00:00:00' 
                                                    and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                                    AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                                    )+('15 minutes'::interval),
                                                cupos_cantidad_n=
                                                (
                                                    SELECT COUNT(*) FROM nomina_detalle 
                                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i]['cupos_id']." and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                                        AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                                )
                                                WHERE cupos_atencion.nom_id=$nom_id_destino and cupos_id=".$reg_cupos[$i]['cupos_id']."");
                                                break;
                                            }
                                            else
                                            {
                                                pg_query("UPDATE cupos_atencion SET cupos_horainicio='$nomd_hora' WHERE cupos_id=".$reg_cupos[$i+1]['cupos_id']."");
                                                pg_query("UPDATE cupos_atencion 
                                                SET cupos_horainicio=(
                                                    SELECT 
                                                    min(nomd_hora) 
                                                    FROM nomina_detalle 
                                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i+1]['cupos_id']." AND NOT nomd_hora='00:00:00'
                                                    and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                                    AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                                ),
                                                cupos_horafinal=(
                                                    SELECT max(nomd_hora) FROM nomina_detalle 
                                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i+1]['cupos_id']." AND NOT nomd_hora='00:00:00' 
                                                    and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                                    AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                                    )+('15 minutes'::interval),
                                                cupos_cantidad_n=
                                                (
                                                    SELECT COUNT(*) FROM nomina_detalle 
                                                    join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                                                    WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=".$reg_cupos[$i+1]['cupos_id']." and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                                                        AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                                                )
                                                WHERE cupos_atencion.nom_id=$nom_id_destino and cupos_id=".$reg_cupos[$i+1]['cupos_id']."");
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        else
        {
            pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_centro_ruta, nom_tipo, nom_urgente, nom_fecha,nom_motivo) VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 0, false, '$fecha',initcap(lower('$tipo_atencion_nueva')));");

            $nom_id_destino="CURRVAL('nomina_nom_id_seq')";
            
            pg_query("INSERT INTO cupos_atencion VALUES (DEFAULT, $esp_id,$doc_id, '$fecha', '00:00:00', '00:00:00', 0, 0, 0, 0, 0, true, 0, $nom_id_destino,true,true);");
            
            
            pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo, nomd_extra, nomd_hora, pac_id, nomd_diag_cod, nomd_func_id, nomd_fecha_asigna,nomd_presta_glosa,nomd_motivo_sobrecupo) VALUES (DEFAULT, ".$nom_id_destino.", 'N', 'N', '$nomd_hora', 0, '', ".$_SESSION['sgh_usuario_id'].", null,'null',null);");
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            pg_query("UPDATE cupos_atencion 
            SET cupos_horainicio=(
            SELECT 
            min(nomd_hora) 
            FROM nomina_detalle 
            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND NOT nomd_hora='00:00:00'
            ),
            cupos_horafinal=(
            SELECT max(nomd_hora) FROM nomina_detalle 
            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND NOT nomd_hora='00:00:00'
            )+('15 minutes'::interval)
            , 
            cupos_cantidad_n=(
            SELECT COUNT(*) FROM nomina_detalle 
            WHERE nomina_detalle.nom_id=cupos_atencion.nom_id
            )
            WHERE cupos_horainicio='00:00:00'");
            //------------------------------------------------------------------
            //------------------------------------------------------------------
            
            $consulta="SELECT nom_id FROM nomina_detalle WHERE nomd_id=$nomd_id";
            $reg_nomina_detalle=cargar_registro($consulta, true);
            if($reg_nomina_detalle)
            {
                $nom_id_ori=$reg_nomina_detalle['nom_id'];
            }
            else
            {
                $nom_id_ori=false;
            }
            
            if($nom_id_ori!=false)
            {
                $reg_nomd=cargar_registro("SELECT count(*)as cantidad FROM nomina_detalle WHERE nom_id=$nom_id_ori");
                if(($reg_nomd['cantidad']*1)==1)
                {
                    pg_query("DELETE FROM cupos_atencion WHERE nom_id=$nom_id_ori;");
                    pg_query("DELETE FROM nomina WHERE nom_id=$nom_id_ori;");
                }
                pg_query("DELETE FROM nomina_detalle WHERE nomd_id=$nomd_id;");
                              
                pg_query("UPDATE cupos_atencion 
                SET cupos_horainicio=(
                        SELECT 
                        min(nomd_hora) 
                        FROM nomina_detalle 
                        join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                        WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=$cupo_id AND NOT nomd_hora='00:00:00'
                        and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                        AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                    ),
                    cupos_horafinal=(
                        SELECT max(nomd_hora) FROM nomina_detalle 
                        join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                        WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=$cupo_id AND NOT nomd_hora='00:00:00' 
                        and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                        AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                    )+('15 minutes'::interval),
                    cupos_cantidad_n=
                    (
                        SELECT COUNT(*) FROM nomina_detalle 
                        join cupos_atencion on nomina_detalle.nom_id=cupos_atencion.nom_id
                        WHERE nomina_detalle.nom_id=cupos_atencion.nom_id AND cupos_atencion.cupos_id=$cupo_id and nomina_detalle.nomd_hora between cupos_horainicio and cupos_horafinal
                        AND (nomina_detalle.nomd_diag_cod NOT IN ('H','T') OR nomina_detalle.nomd_diag_cod IS NULL)
                    )
                    WHERE cupos_atencion.nom_id=$nom_id_ori and cupos_id=$cupo_id");
            }
        }
        /*
        $fechas = cargar_registros_obj("
        SELECT DISTINCT
        date_trunc('day', cupos_fecha) AS cupos_fecha,
        cupos_horainicio, cupos_horafinal, cupos_id,
        cupos_cantidad_n, cupos_cantidad_c, cupos_ficha, cupos_adr,
        esp_desc, nom_motivo,
        nom_id,
        (select count(*) from nomina_detalle where nomina_detalle.nom_id=nomina.nom_id and (pac_id!=0 and pac_id is not null) AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL))as cant
        FROM cupos_atencion
        JOIN especialidades ON esp_id=cupos_esp_id
        LEFT JOIN nomina USING (nom_id)
        WHERE cupos_doc_id=$doc_id and cupos_esp_id=$esp_id ORDER BY cupos_fecha, cupos_horainicio;
        ", true);
        */
        pg_query("COMMIT;");
        $consulta="SELECT DISTINCT
        date_trunc('day', cupos_fecha) AS cupos_fecha,
        cupos_horainicio, cupos_horafinal, cupos_id, 
        cupos_cantidad_n, cupos_cantidad_c, cupos_ficha, cupos_adr,
        esp_desc, nom_motivo,nom_tipo_contrato,
        nom_id,
        (select count(*) from nomina_detalle where nomina_detalle.nom_id=nomina.nom_id and (pac_id!=0 and pac_id is not null) AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL))as cant,
        esp_id
        FROM cupos_atencion
        JOIN especialidades ON esp_id=cupos_esp_id
        LEFT JOIN nomina USING (nom_id)
        WHERE cupos_doc_id=$doc_id ORDER BY cupos_fecha, cupos_horainicio;";
        $fechas = cargar_registros_obj($consulta, true);
                
        echo json_encode(Array('OK',$fechas));
        
        
    }
    
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    
    
    
    
?>
