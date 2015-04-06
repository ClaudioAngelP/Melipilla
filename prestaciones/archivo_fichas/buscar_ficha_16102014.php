<?php
    require_once('../../conectar_db.php');
    if(!isset($_POST['ultimo']))
    {
        $tipo_inf=$_POST['tipo_inf']*1;
        $fecha=pg_escape_string($_POST['fecha1']);
        $buzon=false;
        if(isset($_POST['buzon']))
        {
            $buzon=true;
        }
        
        $barras=trim($_POST['barras']);
        if($barras=='')
            exit('');

        if(isset($_POST['select_busqueda']))
        {
            if($_POST['select_busqueda']*1==0)
            {
                if(strstr($barras,'-'))
                    $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$barras' LIMIT 1");
                else
                    $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$barras' LIMIT 1");
            }
            if($_POST['select_busqueda']*1==1)
            {
                $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_id='$barras' LIMIT 1");
            }
        }
        else
        {
            if(strstr($barras,'-'))
                $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$barras' LIMIT 1");
            else
                $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$barras' LIMIT 1");
        }

        $pac_id=$tmp[0]['pac_id']*1;
        $pac_ficha=pg_escape_string($tmp[0]['pac_ficha']);
        if(!isset($_POST['list_ficha']))
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
            //print($consulta);
            
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
            COALESCE((SELECT COALESCE(am_estado,0)as am_estado FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
            FROM pacientes
            WHERE pac_id=$pac_id
            ";

        }
        //print($consulta);
        $regs=cargar_registros_obj($consulta,true);
        if(!$regs)
        {
            if(isset($_POST['list_ficha']))
            {
                exit(json_encode($regs));
            }
            else
            {
                exit("<center><h2>(No se encontr&oacute; R.U.N./Ficha especificados...)</h2></center>");
            }

        }
        else
        {
            if(isset($_POST['list_ficha']))
            {
                exit(json_encode($regs));
            }
        }
        ?>
        <table style='width:100%;' class='lista_small'>
            <tr class='tabla_header'>
                <td style='width:12%;'>Ficha</td>
                <td style='width:12%;'>RUN</td>
                <td style='width:40%;'>Nombre Completo</td>
                <td>Ubic. Anterior</td>
                <td>Ubic. Actual</td>
                <td>Estado Actual</td>
                <?php
                if(!$buzon)
                {
                ?>
                    <td>Etiqueta</td>
                <?php
                }
                ?>
                <td>Historial</td>
            </tr>
        <?php 
            $opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
            $opts_color=Array('black','gray','blue','purple','green','red');
            if($regs)
                for($i=0;$i<count($regs);$i++)
                {
                    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
                    if($regs[$i]['pac_ficha']!='' and $regs[$i]['pac_ficha']!='0' and $regs[$i]['pac_ficha']!=0)
                    {
                        $ficha=$regs[$i]['pac_ficha'];
                        if(!$buzon)
                        {
                            if(_cax(20002))
                                $ficha.="<input type='button' style='font-size:8px;margin:0px;padding:0px;' id='asigna_".$regs[$i]['pac_id']."' name='asigna_".$regs[$i]['pac_id']."' onClick='asignar_ficha(".$regs[$i]['pac_id'].",1);' alt='Asignar Nuevo Nro de Ficha' title='Asignar Nuevo Nro de Ficha' value='[A]' />";
                        }
                    }
                    else
                    {
                        if(!$buzon)
                        {
                            if(_cax(20002))
                            {
                                $ficha="<center>
                                <input type='button' style='font-size:8px;margin:0px;padding:0px;' id='asigna_".$regs[$i]['pac_id']."' name='asigna_".$regs[$i]['pac_id']."' onClick='asignar_ficha(".$regs[$i]['pac_id'].",0);' value='[ASIGNAR]' />
                                <input type='button' style='font-size:8px;margin:0px;padding:0px;' id='crea_".$regs[$i]['pac_id']."' name='crea_".$regs[$i]['pac_id']."' onClick='crear_ficha(".$regs[$i]['pac_id'].");' value='[CREAR]' />
                                </center>";
                            }
                            else
                            {
                                $ficha="";
                            }
                        }
                        else
                        {
                            $ficha="";
                        }
                    }
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
                            print("<center>".$ficha."</center>");
                        print("</td>");
                        print("<td style='text-align:right;'>".$regs[$i]['pac_rut']."</td>");
                        print("<td style='text-align:left;'>".$regs[$i]['pac_nombre']."</td>");
                        print("<td style='text-align:left;'>".$regs[$i]['ubic_anterior']."</td>");
                        print("<td style='text-align:left;'>".$regs[$i]['ubic_actual']."</td>");
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
                        if(!$buzon)
                        {
                            print("<td>");
                                print("<center>");
                                    print("<img src='iconos/printer.png'  style='cursor:pointer;' alt='Imprimir Etiqueta' title='Imprimir Etiqueta' onClick='imprimir_etiqueta(".$regs[$i]['pac_id'].");' />");
                                print("</center>");
                            print("</td>");
                        }
                        print("<td>");
                            print("<center>");
                                print("<img src='iconos/magnifier.png'  style='cursor:pointer;' alt='Ver Historial' title='Ver Historial' onClick='historial_ficha(".$regs[$i]['pac_id'].");' />");
                            print("</center>");
                        print("</td>");
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
                                                //if($xx!=0 and $cont!=0)
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
    <?php
    }
    else
    {
        $last_ficha=cargar_registro("select pac_ficha from pacientes where (pac_ficha!='' and pac_ficha!='0' and pac_ficha is not null) and pac_ficha::int<606797  order by pac_ficha::bigint desc limit 1");
        if($last_ficha)
        {
            $nro_ficha=$last_ficha['pac_ficha'];
            //$string="El &Uacute;ltimo N&uacute;mero de ficha ingresado corresponde al:".$nro_fecha;
        }
        else
        {
            $nro_ficha='0';
            //$string="Problemas al encontrar último número de ficha";
        }
        echo $nro_ficha;
    }
    ?>