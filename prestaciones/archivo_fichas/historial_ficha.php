<?php
    require_once('../../conectar_db.php');
    $pac_id=$_GET['pac_id']*1;
    if(!isset($_GET['solicitudes']))
    {
        $pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id;");
        if($pac['pac_ficha']==null)
        {
            $ficha_w="pac_ficha is null";
            
        }
        else if($pac['pac_ficha']=='')
        {
            $ficha_w="pac_ficha=''";
        }
        else
        {
            $pac_ficha=pg_escape_string($pac['pac_ficha']);
            $ficha_w="pac_ficha='$pac_ficha'";
        }
            
        $consulta="SELECT *, 
        (case when origen_esp_id!=0 then (SELECT esp_desc FROM especialidades WHERE esp_id=origen_esp_id) 
        else 
        (select centro_nombre from centro_costo where am_centro_ruta_origen=centro_ruta) end
        )AS esp_desc_origen, 
        (case when origen_doc_id!=0 then (SELECT doc_paterno || ' ' || doc_materno || ' ' || doc_nombres FROM doctores WHERE doc_id=origen_doc_id) 
        else
        (select func_nombre from funcionario where am_func_id=func_id)
        end) AS doc_desc_origen, 
        (case when destino_esp_id!=0 then (SELECT esp_desc FROM especialidades WHERE esp_id=destino_esp_id) 
        else 
        (select centro_nombre from centro_costo where am_centro_ruta_destino=centro_ruta) end
        )AS esp_desc_destino, 
        (case when destino_doc_id!=0 then (SELECT doc_paterno || ' ' || doc_materno || ' ' || doc_nombres FROM doctores WHERE doc_id=destino_doc_id) 
        else
        (select func_nombre from funcionario where am_func_id=func_id)
        end)AS doc_desc_destino 
        FROM archivo_movimientos 
        LEFT JOIN funcionario ON am_func_id=func_id 
        WHERE $ficha_w AND pac_id=$pac_id
        ORDER BY am_fecha DESC;";
        //print($consulta);
        $m=cargar_registros_obj($consulta, true);
?>
        <html>
            <title>Historial de Movimientos Ficha Paciente</title>
            <?php cabecera_popup('../..'); ?>
            <body class='fuente_por_defecto popup_background'>
                <div class='sub-content'>
                    <img src='../../iconos/script.png'><b>Historial de Movimientos Ficha Paciente</b>
                </div>
                <table style='font-size:18px;'>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Ficha Paciente:</td>
                        <td style='font-size:28px;'><?php echo ($pac['pac_ficha']); ?></td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>R.U.N.:</td>
                        <td><?php echo formato_rut($pac['pac_rut']); ?></td>
                    </tr>
                    <tr>
                        <td style='text-align:right;' class='tabla_fila2'>Nombre Completo:</td>
                        <td><?php echo ($pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']); ?></td>
                    </tr>
                </table>
                <table style='width:100%;font-size:10px;' cellpadding=1 cellspacing=0>
                    <tr class='tabla_header'>
                        <td>#</td>
                        <td>Fecha/Hora</td>
                        <td>Local Origen</td>
                        <td>Prof./Serv. Origen</td>
                        <td>&nbsp;</td>
                        <td>Local Destino</td>
                        <td>Prof./Serv. Destino</td>
                        <td>Funcionario</td>
                        <td>Estado</td>
                    </tr>
                    <?php
                    $opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
                    $opts_color=Array('black','yellowgreen','yellowgreen','purple','green','red');
                    for($i=0;$i<sizeof($m);$i++)
                    {
                        $clase=($i%2==0?'tabla_fila':'tabla_fila2');
                        $estado=$opts[$m[$i]['am_estado']*1];
                        if($i==0)
                            $style='background-color:gray;color:black;border:1px solid black;';
                        else
                            $style='';
                        
                        print("<tr class='$clase' style='$style'>");
                            print("<td style='text-align:center;font-weight:bold;font-size:20px;'>".(sizeof($m)-$i)."</td>");
                            print("<td style='text-align:center;font-weight:bold;'>".substr($m[$i]['am_fecha'],0,16)."</td>");
		
                            if($m[$i]['esp_desc_origen']!='' AND $m[$i]['doc_desc_origen']!='')
                            {
                                print("<td style='text-align:left;'>".$m[$i]['esp_desc_origen']."</td>");
                                print("<td style='text-align:left;'>".$m[$i]['doc_desc_origen']."</td>");
                            }
                            else
                            {
                                print("<td style='text-align:center;font-weight:bold;font-size:18px;' colspan=2>Archivo</td>");
                            }	
                            print("<td style='font-size:18px;'>&gt;</td>");
                            if($m[$i]['esp_desc_destino']!='' AND $m[$i]['doc_desc_destino']!='')
                            {
                                print("<td style='text-align:left;font-weight:bold;'>".$m[$i]['esp_desc_destino']."</td>");
                                print("<td style='text-align:left;font-weight:bold;'>".$m[$i]['doc_desc_destino']."</td>");
                            }
                            else
                            {
                                print("<td style='text-align:center;font-weight:bold;font-size:18px;' colspan=2>Archivo</td>");
                            }
                            print("<td style='text-align:left;'>".$m[$i]['func_nombre']."</td>");
                            if($m[$i]['esp_desc_destino']!='' AND $m[$i]['doc_desc_destino']!='')
                            {
                                print("<td style='text-align:center;font-weight:bold;font-size:18px;'>".$estado."</td>");
                            }
                            else
                            {
                                if($m[$i]['am_estado']*1==3)
                                {
                                    print("<td style='text-align:center;font-weight:bold;font-size:12px;'>".$estado."-Archivo</td>");
                                }
                                else
                                {
                                    print("<td style='text-align:center;font-weight:bold;font-size:18px;'>".$estado."</td>");
                                }
                            }
                        print("</tr>");
                    }
                    ?>
                </table>
            </body>
        </html>
<?php
    }
    else
    {
        $consulta="
            SELECT pac_rut,pac_ficha,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,
            COALESCE(
            (
            SELECT COALESCE(esp_desc,(
            SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') 
            FROM archivo_movimientos 
            LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
            AND archivo_movimientos.pac_id=pacientes.pac_id 
            ORDER BY am_fecha DESC LIMIT 1 
            )) 
            FROM archivo_movimientos 
            LEFT JOIN especialidades ON origen_esp_id=esp_id 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
            ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_anterior, 
            COALESCE(
            (
                SELECT COALESCE(esp_desc,(
                SELECT COALESCE(centro_nombre,'ARCHIVO') FROM archivo_movimientos 
                LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
                WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                ORDER BY am_fecha DESC LIMIT 1
            )) 
            FROM archivo_movimientos 
            LEFT JOIN especialidades ON destino_esp_id=esp_id 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
            ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_actual, 
            COALESCE
            (
                        (
                            SELECT COALESCE(am_estado,null) 
                            FROM archivo_movimientos 
                            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
                            ORDER BY am_fecha DESC LIMIT 1
                        ),null
            ) as am_estado,
            

            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=CURRENT_TIMESTAMP::date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_0,
            
            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('1 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_1,
            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('2 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_2,
            
            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('3 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_3,
            
            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('4 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_4,
            
            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('5 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_5,
            

            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('6 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_6,
            
            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('7 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_7,
            
            COALESCE
            (
                (
                    select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno  || '|' || nomd_hora)as datos from nomina_detalle 
                    left join nomina using (nom_id)
                    left join especialidades on nom_esp_id=esp_id
                    left join doctores on nom_doc_id=doc_id
                    where nomina_detalle.pac_id=pacientes.pac_id and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('6 days' AS INTERVAL))::Date
                    AND nomd_diag_cod NOT IN ('X','T','H','B') order by nomd_hora asc limit 1
                )
                ,
                null
            )as proxima_programada_8,
            
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=CURRENT_TIMESTAMP::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_0,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('1 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_1,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('2 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_2,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('3 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_3,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('4 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_4,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('5 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_5,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('6 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_6,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('7 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_7,
            COALESCE
            (
                (
                    select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                    left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                    left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                    left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                    left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                    where ficha_espontanea.pac_id=pacientes.pac_id
                    and fesp_fecha::date=(CURRENT_TIMESTAMP + CAST('8 days' AS INTERVAL))::date order by ((date_trunc('second',fesp_fecha))::time) asc limit 1
                )
                ,
                null
            )as proxima_prestamo_8
            FROM pacientes
            WHERE pac_id=$pac_id
            ";
            
            $regs=cargar_registros_obj($consulta,true);
            if(!$regs)
            {
                exit("<center><h2>(No se encontr&oacute; R.U.N./Ficha especificados...)</h2></center>");
            }
?>
        <html>
            <title>Solicitudes del Paciente</title>
            <?php cabecera_popup('../..'); ?>
            <body class='fuente_por_defecto popup_background'>
                <div class='sub-content'>
                    <img src='../../iconos/script.png'><b>Solictudes del Paciente</b>
                </div>
                <div id="listado_solicitudes" style="height:360px;overflow:auto;" class="sub-content2">
                    <table style='width:100%;' class='lista_small'>
                        <tr class='tabla_header'>
                            <td style='width:12%;'>Ficha</td>
                            <td style='width:12%;'>RUN</td>
                            <td style='width:40%;'>Nombre Completo</td>
                            <td>Ubic. Anterior</td>
                            <td>Ubic. Actual</td>
                            <td>Estado Actual</td>
                        </tr>
                        <?php
                        $opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
                        $opts_color=Array('black','gray','blue','purple','green','red');
                        if($regs)
                            for($i=0;$i<count($regs);$i++)
                            {
                                if(($regs[$i]['am_estado']*1)==3)
                                {
                                    if($regs[$i]['ubic_actual']=="ARCHIVO")
                                    {
                                        $color_estado="IndianRed";
                                    }
                                    else
                                    {
                                        $color_estado=$opts_color[$regs[$i]['am_estado']*1];
                                    }
                                }
                                else
                                {
                                    $color_estado=$opts_color[$regs[$i]['am_estado']*1];
                                }
                                print("<tr class='$clase' style='color:".$color_estado.";$color' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
                                    print("<td style='text-align:center;font-size:14px;font-weight:bold;white-space:nowrap;vertical-align: top;'>");
                                        print("<center>".$regs[$i]['pac_ficha']."</center>");
                                    print("</td>");
                                    print("<td style='text-align:center;'>".$regs[$i]['pac_rut']."</td>");
                                    print("<td style='text-align:left;'>".$regs[$i]['pac_nombre']."</td>");
                                    print("<td style='text-align:center;'>".$regs[$i]['ubic_anterior']."</td>");
                                    print("<td style='text-align:center;'>".$regs[$i]['ubic_actual']."</td>");
                                    if(($regs[$i]['am_estado']*1)==3)
                                    {
                                        if($regs[$i]['ubic_actual']=="ARCHIVO")
                                        {
                                            print("<td style='text-align:center;font-weight:bold;'>".$opts[$regs[$i]['am_estado']*1]."-Archivo</td>");
                                        }
                                        else
                                        {
                                            print("<td style='text-align:center;font-weight:bold;'>".$opts[$regs[$i]['am_estado']*1]."</td>");
                                        }
                                    }
                                    else
                                    {
                                        if($regs[$i]['am_estado']=='')
                                        {
                                            print("<td style='text-align:center;font-weight:bold;'>(SIN MOVIMIENTOS)</td>");
                                        }
                                        else
                                        {
                                            print("<td style='text-align:center;font-weight:bold;'>".$opts[$regs[$i]['am_estado']*1]."</td>");
                                        }
                                        
                                    }
                                print("</tr>");
                                print("<tr>");
                                    print("<td colspan='8'>");
                                        print("<table style='width:100%;'>");
                                            print("<tr class='tabla_header'>");
                                                print("<td colspan='5'><b>PR&Oacute;XIMA SOLICITUD PROGRAMADA</b></td>");
                                            print("</tr>");
                                            print("<tr class='tabla_header'>");
                                                print("<td><b>N&deg;</b></td>");
                                                print("<td><b>Fecha Citaci&oacute;n</b></td>");
                                                print("<td><b>Hora Citaci&oacute;n</b></td>");
                                                print("<td><b>Especialidad / Servicio</b></td>");
                                                print("<td><b>Solicitado Por</b></td>");
                                            print("</tr>");
                                            $cont=0;
                                            $nom_fecha="";
                                            for($xx=0;$xx<7;$xx++)
                                            {
                                                $consulta="select (nom_fecha::date || '|' || esp_desc || '|' || doc_nombres || ' ' || doc_paterno || ' ' || doc_materno || '|' || nomd_hora)as datos 
                                                from nomina_detalle 
                                                left join nomina using (nom_id) 
                                                left join especialidades on nom_esp_id=esp_id 
                                                left join doctores on nom_doc_id=doc_id 
                                                where nomina_detalle.pac_id=".$regs[$i]['pac_id']." and nom_fecha::date=(CURRENT_TIMESTAMP + CAST('$xx days' AS INTERVAL))::date
                                                AND nomd_diag_cod NOT IN ('X','T','H','B') 
                                                order by nom_fecha,nomd_hora asc 
                                                ";
                                                $regs_proximas_programada=cargar_registros_obj($consulta,true);
                                                if(!$regs_proximas_programada)
                                                    $regs_proximas_programada=false;
                                                
                                                if($regs_proximas_programada)
                                                {
                                                    for($ii=0;$ii<count($regs_proximas_programada);$ii++)
                                                    {
                                                        if($regs_proximas_programada[$ii]['datos']!="")
                                                        {
                                                            $camp_programada=explode("|",$regs_proximas_programada[$ii]['datos']);
                                                            if($nom_fecha!=$camp_programada[0] and $cont!=0)
                                                                $border_top="border-top: 3px dotted black;";
                                                            else
                                                                $border_top="";
                                                
                                                            print("<tr class='$clase' style='' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
                                                                print("<td style='text-align:center;$border_top'>".($ii+1)."</td>");
                                                                print("<td style='text-align:center;$border_top'>".$camp_programada[0]."</td>");
                                                                print("<td style='text-align:center;$border_top'>".$camp_programada[3]."</td>");
                                                                print("<td style='text-align:left;$border_top'>".$camp_programada[1]."</td>");
                                                                print("<td style='text-align:left;$border_top'>".$camp_programada[2]."</td>");
                                                            print("</tr>");
                                                            $cont=$cont+1;
                                                            $nom_fecha=$camp_programada[0];
                                                        }
                                                    }
                                                }
                                            }
                                            if($cont==0)
                                            {
                                                print("<tr class='$clase' style='' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
                                                    print("<td colspan='5' style='text-align:left;'>Sin Solicitud</td>");
                                                print("</tr>");
                                            }
                                            print("<tr class='tabla_header'>");
                                                print("<td colspan='5'><b>PR&Oacute;XIMA SOLICITUD PARA PRESTAMO</b></td>");
                                            print("</tr>");
                                            print("<tr class='tabla_header'>");
                                                print("<td><b>N&deg;</b></td>");
                                                print("<td><b>Fecha</b></td>");
                                                print("<td><b>Hora Solicitud</b></td>");
                                                print("<td><b>Especialidad / Servicio</b></td>");
                                                print("<td><b>Solicitado Por</b></td>");
                                            print("</tr>");
                                            $cont=0;
                                            $presta_fecha="";
                                            for($xx=0;$xx<7;$xx++)
                                            {
                                                $consulta="select (fesp_fecha::date || '|' || COALESCE(centro_nombre,esp_desc)|| '|' || COALESCE(doc_nombres || ' ' || doc_paterno || ' ' || doc_materno ,func_nombre) || '|' || (date_trunc('second',fesp_fecha))::time)as datos from ficha_espontanea 
                                                left join especialidades on ficha_espontanea.esp_id=especialidades.esp_id
                                                left join centro_costo on ficha_espontanea.fesp_centro_ruta=centro_costo.centro_ruta
                                                left join doctores on ficha_espontanea.doc_id=doctores.doc_id
                                                left join funcionario on ficha_espontanea.fesp_func_id= funcionario.func_id
                                                where ficha_espontanea.pac_id=".$regs[$i]['pac_id']."
                                                and fesp_fecha::date>=(CURRENT_TIMESTAMP + CAST('$xx days' AS INTERVAL))::date
                                                order by ((date_trunc('second',fesp_fecha))::time) asc 
                                                ";
                                                $regs_proximas_prestamo=cargar_registros_obj($consulta,true);
                                                if(!$regs_proximas_prestamo)
                                                    $regs_proximas_prestamo=false;
                                                if($regs_proximas_prestamo)
                                                {
                                                    for($ii=0;$ii<count($regs_proximas_prestamo);$ii++)
                                                    {
                                                        if($regs_proximas_prestamo[$ii]['datos']!="")
                                                        {
                                                            $camp_prestamo=explode("|",$regs_proximas_prestamo[$ii]['datos']);
                                                            if($presta_fecha!=$camp_prestamo[0] and $cont!=0)
                                                                $border_top="border-top: 3px dotted black;";
                                                            else
                                                                $border_top="";
                                                
                                                            print("<tr class='$clase' style='' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
                                                                print("<td style='text-align:center;$border_top'>".($ii+1)."</td>");
                                                                print("<td style='text-align:center;$border_top'>".$camp_prestamo[0]."</td>");
                                                                print("<td style='text-align:center;$border_top'>".$camp_prestamo[3]."</td>");
                                                                print("<td style='text-align:left;$border_top'>".$camp_prestamo[1]."</td>");
                                                                print("<td style='text-align:left;$border_top'>".$camp_prestamo[2]."</td>");
                                                            print("</tr>");
                                                            $cont=$cont+1;
                                                            $presta_fecha=$camp_prestamo[0];
                                                        }
                                                    }
                                                }
                                            }
                                            if($cont==0)
                                            {
                                                print("<tr class='$clase' style='' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
                                                    print("<td colspan='5' style='text-align:left;'>Sin Solicitud</td>");
                                                print("</tr>");
                                            }
                                        print("</table>");
                                    print("</td>");
                                print("</tr>");
                            }
                        ?>
                    </table>
                </div>
<?php
    }
?>