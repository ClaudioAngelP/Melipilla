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
    $func_id=$_SESSION['sgh_usuario_id']*1;
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    
    if($extra=='S')
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
                pg_query("UPDATE nomina_detalle SET pac_id=$pac_id, nomd_tipo='$tipo' WHERE nom_id=$nom_id AND nomd_id=$nomd_id;");
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
                pg_query("UPDATE nomina_detalle SET pac_id=$pac_id, nomd_tipo='$tipo' WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora';");
                $tmp=cargar_registro("SELECT nomd_id AS id FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora='$nomd_hora' LIMIT 1;");
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
        if($_POST['codigos']!='')
        {
            $c=explode('|',pg_escape_string($_POST['codigos']));
            for($i=0;$i<sizeof($c);$i++)
            {
                $codigo=$c[$i];
                if(isset($_POST['presta_'.$codigo]))
                    pg_query("INSERT INTO nomina_detalle_prestaciones VALUES (DEFAULT, $nomd_id, '$codigo', 1, 0);");
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
                pg_query("UPDATE solicitud_examen SET sol_estado=1 WHERE sol_exam_id=$sol_exam_id;");
                pg_query("UPDATE solicitud_examen_detalle SET sol_examd_nomd_id=$nomd_id WHERE sol_examd_id IN ($examenes);");
            }
        }
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    pg_query("COMMIT;");
    print($nomd_id);
?>