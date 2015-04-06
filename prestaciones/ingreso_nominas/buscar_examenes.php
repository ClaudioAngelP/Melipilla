<?php
    ini_set("memory_limit","800M");
    require_once("../../conectar_db.php");
    set_time_limit(0);
    $pac_id=($_POST['pac_id']*1);
    $esp_id=($_POST['esp_id']*1);
    $list=($_POST['list']*1);
    $fecha1=pg_escape_string($_POST['fecha1']);
    $fecha2=pg_escape_string($_POST['fecha2']);

    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    /*
    if(isset($_POST['document_propios']))
    {
        if($_POST['document_propios']==1)
        {
            $func="AND docs_func_id=".($_SESSION['sgh_usuario_id']*1);
        }
        else
        {
            $func="";
        }
    }
    else
    {
        $func="";
    }
    */
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($list==1)
    {
        //$examen_propios=($_POST['examen_propios']*1);
        /*
        if(isset($_POST['enviado_por']))
        {
            if($_POST['enviado_por']!=-1)
            {
                if(strstr($_POST['enviado_por'],'.'))
                {
                    $enviado_bodega=0;
                    $enviado_centro=$_POST['enviado_por'];
                }
                else
                {
                    $enviado_bodega=($_POST['enviado_por']*1);
                    $enviado_centro=0;
                }
                $string_enviado="and
                (
                ((select logdoc_bod_id_origen from logs_documentos left join bodega on logdoc_bod_id_origen=bod_id where logdoc_doc_id=docs_id order by logdoc_id desc limit 1)=".$enviado_bodega.")
                and
                ((select logdoc_centro_ruta_origen from logs_documentos left join bodega on logdoc_bod_id_origen=bod_id where logdoc_doc_id=docs_id order by logdoc_id desc limit 1)='".$enviado_centro."')
                )";
            }
            else
            {
                $string_enviado="";
            }
        }
        else
        {
            $string_enviado="";
        }
        */
        /*
        $consulta="
        select solicitud_examen.*,
        date_trunc('second', solicitud_examen.sol_fecha) AS solicitud_fecha,
        esp_desc as nom_esp,
        func_nombre,
        foo2.* from (
        select sol_exam_id,sol_terminada,
        (SELECT count(*) FROM solicitud_examen_detalle WHERE sol_examd_solexam_id=foo.sol_exam_id)as total,
        (SELECT count(*) FROM solicitud_examen_detalle WHERE sol_examd_solexam_id=foo.sol_exam_id AND sol_examd_realizado=true)as realizadas
        from (
            SELECT sol_exam_id,sol_terminada FROM solicitud_examen WHERE sol_pac_id=$pac_id
        )as foo
        )as foo2
        left join solicitud_examen on solicitud_examen.sol_exam_id=foo2.sol_exam_id
        left join especialidades on solicitud_examen.sol_esp_id=especialidades.esp_id
        left join funcionario on solicitud_examen.sol_func_id=func_id
        order by sol_fecha desc
        ";
        */
        $consulta="SELECT *,date_trunc('second',sol_fecha)as fecha_solicitud
        FROM solicitud_examen_detalle
        left JOIN solicitud_examen ON sol_examd_solexam_id=sol_exam_id
        left JOIN doctores ON sol_examd_doc_id=doc_id
        left JOIN procedimiento_codigo on sol_examd_cod_presta=pc_id
        left JOIN especialidades on sol_esp_id=especialidades.esp_id
        left join funcionario on solicitud_examen.sol_func_id=func_id
        WHERE sol_pac_id=$pac_id order by sol_fecha desc";

        $reg_examenes = cargar_registros_obj($consulta,true);
        if(!$reg_examenes)
        {
            $reg_examenes=false;
        }
        echo json_encode(array($reg_examenes));
    }
    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    if($list==2)
    {
        if($pac_id!="0")
        {
            $pac_string="and sol_pac_id=".$pac_id."";
            $w_fecha="true";
        }
        else
        {
            $pac_string="";
            $w_fecha="(date_trunc('day', sol_fecha)>='$fecha1' AND date_trunc('day', sol_fecha)<='$fecha2')";
        }

        if($esp_id!="-1")
            $esp_string="and sol_esp_id=".$esp_id."";
        else
            $esp_string="";

        $estado_solicitudes=($_POST['estado_solicitudes']*1);

        if($estado_solicitudes==0)
        {
            $where_estado="where total_examenes!=total_agendados and sol_examd_nomd_id=0";
        }
        else
        {
            $where_estado="where total_examenes=total_agendados and sol_examd_nomd_id!=0";
        }
        switch($estado_solicitudes) {
          case 0: $AND_estado="AND sol_examd_nomd_id=0"; break;
          case 1: $AND_estado="AND sol_examd_nomd_id!=0 and nd1.nomd_diag_cod!='T'"; break;
          case 2: $AND_estado="AND sol_examd_nomd_id!=0 and nd1.nomd_diag_cod='T'"; break;
        }

        $consulta="
        SELECT sol_nomd_id_original,date_trunc('second',sol_fecha)as fecha_solicitud,sol_tipo_examen,solicitud_examen_detalle.*,
        procedimiento_codigo.*,nomina_detalle.*,nomina.*,
        pac_nombres || ' ' || ' ' || pac_appat || ' '|| pac_apmat as nombre_paciente,
        pac_rut,
        func_nombre,
        sol_esp_id,
        sol_exam_id,sol_estado,
        tbesp.esp_desc as tbesp_desc,
        doc_rut,(doc_nombres ||' '||doc_paterno || ' ' || doc_materno)as doc_nombre,
        tbesp2.esp_desc as tbesp2_desc,
        sol_obsgeneral,sol_pac_id,
        nd1.nomd_id as nd1_nomd_id,nd1.nomd_diag_cod as nd1_nomd_diag_cod,
        upper(sol_origen)as solicitud_origen, sol_institucion,
        prof_rut,
        (prof_nombres || ' ' || prof_paterno || ' ' || prof_materno)as prof_nombre,
        inst_nombre,
        substr(nd1.nomd_hora::text,1,5) as hora_citacion,substr(nom1.nom_fecha::text,1,10) as fecha_citacion,
        solicitud_examen.*,centro_nombre,
        nd1.nomd_diag_cod as nomd_diag_cod_destino,
        nd1.nomd_codigo_cancela as nd1_codigo_cancela, susp_desc
        FROM solicitud_examen_detalle
        JOIN solicitud_examen on sol_examd_solexam_id=sol_exam_id
        LEFT JOIN pacientes on sol_pac_id=pac_id
        LEFT JOIN funcionario on sol_func_id=func_id
        LEFT JOIN procedimiento_codigo on sol_examd_cod_presta=pc_id
        LEFT JOIN nomina_detalle on sol_nomd_id_original=nomd_id
        LEFT JOIN nomina on nomina.nom_id=nomina_detalle.nom_id
        LEFT JOIN especialidades tbesp on tbesp.esp_id=nom_esp_id
        LEFT JOIN doctores on doc_id=sol_doc_id
        LEFT JOIN especialidades tbesp2 on tbesp2.esp_id=sol_esp_solicita
        LEFT JOIN nomina_detalle nd1 on nd1.nomd_id=sol_examd_nomd_id
        LEFT JOIN nomina nom1 on nom1.nom_id=nd1.nom_id
        LEFT JOIN profesionales_externos on prof_id=sol_doc_id
        LEFT JOIN instituciones on instituciones.inst_id=sol_institucion
        LEFT JOIN centro_costo on centro_ruta=sol_centro_ruta
        LEFT JOIN nomina_codigo_suspende ncs on susp_id=nd1.nomd_codigo_cancela
        WHERE sol_terminada=false ".$pac_string." ".$esp_string."
        AND ".$w_fecha."
        AND case when (sol_nomd_id_original is null or sol_nomd_id_original=0) then true else nomina_detalle.nomd_diag_cod not in ('T','X','B') end
        ".$AND_estado."
        order by sol_fecha desc, solicitud_examen.sol_nomd_id_original,solicitud_examen.sol_tipo_examen,sol_examd_solexam_id
        ";

		    //print($consulta);

        $reg_examenes = cargar_registros_obj($consulta,true);
        if(!$reg_examenes)
        {
            $reg_examenes=false;
        }
        echo json_encode(array($reg_examenes));
    }


    //--------------------------------------------------------------------------
    //--------------------------------------------------------------------------
    /*
    if($list==6)
    {
        $consulta="Select paquete_documentos.*,date_trunc('second',fecha_paquete)as fecha,
        bod_glosa,
        centro_nombre,
        (select count(*) from paquete_detalle where paqueted_paquete_id=id_paquete)as cantidad_docs
        from paquete_documentos
        left join bodega on paquete_bod_remitente=bod_id
        left join centro_costo on paquete_centro_ruta_remitente=centro_ruta
        where (paquete_bod_remitente=$bodega_origen AND paquete_centro_ruta_remitente='$centro_ruta')";
        $registros = cargar_registros_obj($consulta,true);
        if(!$registros)
        {
            $registros=false;
        }
        echo json_encode(array($registros));
    }
     *
     */

?>
