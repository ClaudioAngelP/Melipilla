<?php
    require_once('../../conectar_db.php');
    if(!isset($_POST['ultimo']))
    {
        $tipo_inf=$_POST['tipo_inf']*1;
        $fecha=pg_escape_string($_POST['fecha1']);
        $barras=trim($_POST['barras']);
        if($barras=='')
            exit('');

        if(strstr($barras,'-'))
                $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$barras' LIMIT 1");
        else
            $tmp=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_ficha='$barras' LIMIT 1");


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
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
            FROM pacientes
            WHERE pac_id=$pac_id
            ";
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
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado
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
                <td>Etiqueta</td>
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
                        if(_cax(20002))
                            $ficha.="<input type='button' style='font-size:8px;margin:0px;padding:0px;' id='asigna_".$regs[$i]['pac_id']."' name='asigna_".$regs[$i]['pac_id']."' onClick='asignar_ficha(".$regs[$i]['pac_id'].",1);' alt='Asignar Nuevo Nro de Ficha' title='Asignar Nuevo Nro de Ficha' value='[A]' />";
                    }
                    else
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

                    print("<tr class='$clase' style='color:".$opts_color[$regs[$i]['am_estado']*1].";$color' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>
                        <td style='text-align:center;font-size:14px;font-weight:bold;white-space:nowrap;vertical-align: top;'><center>".$ficha."</center></td>
                        <td style='text-align:right;'>".$regs[$i]['pac_rut']."</td>
                        <td style='text-align:left;'>".htmlentities($regs[$i]['pac_nombre'])."</td>
                        <td style='text-align:left;'>".htmlentities($regs[$i]['ubic_anterior'])."</td>
                        <td style='text-align:left;'>".htmlentities($regs[$i]['ubic_actual'])."</td>
                        <td style='text-align:center;font-weight:bold;'>".$opts[$regs[$i]['am_estado']*1]."</td>
                        <td>
                            <center>
                                <img src='iconos/printer.png'  style='cursor:pointer;' alt='Imprimir Etiqueta' title='Imprimir Etiqueta' onClick='imprimir_etiqueta(".$regs[$i]['pac_id'].");' />
                            </center>
                        </td>
                        <td>
                            <center>
                                <img src='iconos/magnifier.png'  style='cursor:pointer;' alt='Ver Historial' title='Ver Historial' onClick='historial_ficha(".$regs[$i]['pac_id'].");' />
                            </center>
                        </td>
                        </tr>");
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