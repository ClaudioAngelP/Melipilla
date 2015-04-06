<?php
    require_once('../../conectar_db.php');
    $doc_id=$_POST['doc_id']*1;
    if(!isset($_POST['eliminar']))
    {
        $esp_id=$_POST['esp_id']*1;
        $desde=pg_escape_string($_POST['desde']);
        $hasta=pg_escape_string($_POST['hasta']);
        $fecha=pg_escape_string($_POST['fecha']);
        $cantn=$_POST['cantn']*1;
        $cantc=$_POST['cantc']*1;
        $cantr=$_POST['canttr']*1;
        $canti=$_POST['cantti']*1;
        $canth=$_POST['cantth']*1;
        $cante=$_POST['cantte']*1;
        (isset($_POST['extras'])) ? $extras='true' : $extras='false';
	(isset($_POST['ficha'])) ? $ficha='true' : $ficha='false';
	(isset($_POST['adr'])) ? $adr='true' : $adr='false';
        $chk=cargar_registros_obj("
	SELECT * FROM cupos_atencion
	WHERE cupos_doc_id=$doc_id AND cupos_fecha='$fecha' AND 
	((cupos_horainicio>='$desde' AND cupos_horafinal<='$hasta') OR
	(cupos_horainicio<='$desde' AND cupos_horafinal>='$hasta') OR
	(cupos_horainicio>'$desde' AND cupos_horainicio<'$hasta') OR
	(cupos_horafinal>'$desde' AND cupos_horafinal<'$hasta'))
        ");
        if($chk)
        {
            exit('1');
        }
        if(isset($_POST['select_nom_motivo']))
        {
            $tipo_atencion=pg_escape_string($_POST['select_nom_motivo']);
        }
        else
        {
            $tipo_atencion="";
        }
        
        if(isset($_POST['select_nom_contrato']))
        {
            if($_POST['select_nom_contrato']!="-1")
            {
                $tipo_contrato=pg_escape_string(utf8_decode($_POST['select_nom_contrato']));
            }
            else
            {
                $tipo_contrato="";
            }
        }
        else
        {
            $tipo_contrato="";
        }
        if($tipo_contrato!="")
        {
            pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_centro_ruta, nom_tipo, nom_urgente, nom_fecha,nom_motivo,nom_tipo_contrato)
            VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 0, false, '$fecha','$tipo_atencion','$tipo_contrato');");
        }
        else
        {
            pg_query("INSERT INTO nomina (nom_id, nom_folio, nom_esp_id, nom_doc_id, nom_centro_ruta, nom_tipo, nom_urgente, nom_fecha,nom_motivo)
            VALUES (DEFAULT, 'AGENDA' || CURRVAL('nomina_nom_id_seq'), $esp_id, $doc_id, '', 0, false, '$fecha','$tipo_atencion');");
        }
        
        
        
        pg_query("
        INSERT INTO cupos_atencion VALUES (
        DEFAULT,
        $esp_id, $doc_id,
        '$fecha', '$desde', '$hasta',
        $cantn, $cantc, $canti, $canth, $cante, $extras, $cantr, CURRVAL('nomina_nom_id_seq'), $ficha, $adr
        )");
        $h1=explode(':', $desde);
        $h2=explode(':', $hasta);
        $hh1=($h1[0]*60)+($h1[1]*1);
        $hh2=($h2[0]*60)+($h2[1]*1);
        $cantc=0;
        $dif=($hh2-$hh1)/($cantn+$cantc);
        for($i=0;$i<($cantn+$cantc);$i++)
        {
            $_hora=$hh1+($dif*$i);
            $hora=floor($_hora/60);
            $minutos=$_hora%60;
            if($minutos<10)
                $minutos='0'.$minutos;
            $hr=$hora.':'.$minutos;
            //if($i<$cantn)
            //{
                $nomd_extra="N";
            //}
            //else
            //{
            //    $nomd_extra="S";
            //}
            pg_query("INSERT INTO nomina_detalle (nomd_id, nom_id, nomd_tipo,nomd_extra, nomd_hora, pac_id,nomd_diag_cod) VALUES (DEFAULT, CURRVAL('nomina_nom_id_seq'), 0,'$nomd_extra', '$hr', 0,'');");
        }
    }
    else
    {
        $esp_id=$_POST['esp_id']*1;
        $cupos_id=$_POST['cupos_id']*1;
        $tmp=cargar_registro("SELECT * FROM cupos_atencion WHERE cupos_id=$cupos_id");
        $nom_id=$tmp['nom_id']*1;
        $nom_desde=$tmp['cupos_horainicio'];
        $nom_hasta=$tmp['cupos_horafinal'];
        if($nom_id==0)
            exit('2');
        
        $nomd=cargar_registros_obj("SELECT * FROM nomina_detalle WHERE nom_id=$nom_id AND nomd_hora BETWEEN '$nom_desde' AND '$nom_hasta'");
        pg_query("START TRANSACTION;");
        if($nomd)
        {
            for($i=0;$i<sizeof($nomd);$i++)
            {
                pg_query("DELETE FROM nomina_detalle_prestaciones WHERE nomd_id=".$nomd[$i]['nomd_id']);
                pg_query("DELETE FROM nomina_detalle_campos WHERE nomd_id=".$nomd[$i]['nomd_id']);
                pg_query("DELETE FROM nomina_detalle WHERE nomd_id=".$nomd[$i]['nomd_id']);
            }
        }
        $reg_nomd=cargar_registro("SELECT count(*)as cantidad FROM nomina_detalle WHERE nom_id=$nom_id");
        if(($reg_nomd['cantidad']*1)==0)
            pg_query("DELETE FROM nomina WHERE nom_id=$nom_id;");
        
        pg_query("DELETE FROM cupos_atencion WHERE cupos_id=$cupos_id");
        pg_query("COMMIT;");
    }
    

    
    
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
    
    print(json_encode($fechas));
?>