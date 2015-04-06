<?php
    ini_set("memory_limit","512M");
    require_once('../conectar_db.php');
    if($_GET['tipo']=='estado_interconsultas' or $_GET['tipo']=='revisar_interconsultas')
    {
        if($_GET['tipo']=='estado_interconsultas')
        {
            $tipo_inter=($_GET['tipo_inter']*1);
            $estado_inter=($_GET['estado_inter']*1);
            $tipo_salida=($_GET['tipo_salida']*1);
            $pagina=($_GET['pagina']*1);
            
            
            $w_tipo_salida="";
            if($tipo_salida==13)
            {
                $w_tipo_salida="and inter_nomd_id!=0";
                
            }
            else
            {
                if($tipo_salida!=-1)
                {
                    if($tipo_salida!=-1)
                    {
                        $w_tipo_salida="and inter_motivo_salida=$tipo_salida and inter_fecha_egreso is null and inter_estado=1";
                    }
                    else
                    {
                        $w_tipo_salida="and inter_motivo_salida=$tipo_salida";
                    }
                }
            }
            
            
            $w_tipo_inter="";
            if(isset($_GET['inst_id1']))
            {
                if($_GET['inst_id1']!="")
                {
                    $inst=$_GET['inst_id1']*1;
                    $w_tipo_inter="and inter_inst_id1=$inst";
                }
                else
                {
                    if($tipo_inter==0)
                    {
                        $w_tipo_inter="and inter_inst_id1!=inter_inst_id2";
                    }
                    if($tipo_inter==1)
                    {
                        $w_tipo_inter="and inter_inst_id1=inter_inst_id2";
                    }
                }
            }
            else
            {
                if($tipo_inter==0)
                {
                    $w_tipo_inter="and inter_inst_id1!=inter_inst_id2";
                }
                if($tipo_inter==1)
                {
                    $w_tipo_inter="and inter_inst_id1=inter_inst_id2";
                }
            }
            /*
            if(isset($_GET['inst_id1']))
                    $institucion = $_GET['inst_id']*1;
                else
                    $institucion = $_GET['inst_id1']*1;
            */
			
            $buscar = $_GET['buscar'];
            $orden = $_GET['orden'];
            
            if(isset($_GET['ascendente']))
            {
                $ascen = '';
            }
            else
            {
                $ascen='DESC';
            }
            
            switch ($orden)
            {
                case 0: $orden='inter_ingreso'; break;
		case 1: $orden='pac_rut'; break;
		case 2: $orden='pac_appat, pac_apmat, pac_nombres'; break;
		case 3: $orden='esp_desc'; break;
		case 4: $orden='inter_folio'; break;
            }
            
            $w_estado_inter="";
            if($estado_inter!=-2)
            {
                $w_estado_inter="and inter_estado=$estado_inter";
            }
            
            if(trim($buscar)!='')
            {
                if($tipo_salida==13)
                {
                    $condicion="
                    WHERE inter_inst_id2=$sgh_inst_id AND (
                    inter_folio || ' ' || pac_rut || ' ' ||	pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$buscar%' 
                    )";
                }
                else
                {
                    $condicion="
                    WHERE inter_inst_id2=$sgh_inst_id AND inter_estado>=0 AND (
                    inter_folio || ' ' || pac_rut || ' ' ||	pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$buscar%' 
                    )";
                }
		
                $condicion2="
		WHERE oa_inst_id2=$sgh_inst_id AND oa_estado>=0 AND (
		oa_folio || ' ' || pac_rut || ' ' ||	pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$buscar%' 
		)";
            }
            else
            {
                if($tipo_salida==13)
                {
                    $condicion="WHERE inter_inst_id2=$sgh_inst_id ";
                }
                else
                {
                    $condicion="WHERE inter_inst_id2=$sgh_inst_id AND inter_estado>=0";
                }
		$condicion2="WHERE oa_inst_id2=$sgh_inst_id AND oa_estado>=0";
            }
            
            
            $limit = 100;
            $pag = (int) $pagina;
            if ($pag < 1)
            {
                $pag = 1;
            }
            $offset = ($pag-1) * $limit;
            $limite = "LIMIT $limit OFFSET $offset";
            
            $query="
            SELECT * FROM (
            SELECT 
            inter_folio, inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 
            esp_desc, inter_estado, inter_id, ice_desc, ice_icono, 'IC',inter_fecha_salida,inter_motivo_salida,esp_id
            FROM interconsulta 
            LEFT JOIN pacientes ON inter_pac_id=pac_id
            LEFT JOIN especialidades ON inter_especialidad=esp_id
            LEFT JOIN interconsulta_estado ON inter_estado=ice_id
            $condicion $w_tipo_inter $w_estado_inter $w_tipo_salida 
            UNION
            SELECT 
            oa_folio AS inter_folio, oa_fecha::date AS inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 
            esp_desc, oa_estado AS inter_estado, oa_id, ice_desc, ice_icono, 'OA',null as inter_fecha_salida,null as inter_motivo_salida,esp_id
            FROM orden_atencion 
            LEFT JOIN pacientes ON oa_pac_id=pac_id
            LEFT JOIN especialidades ON oa_especialidad=esp_id
            LEFT JOIN interconsulta_estado ON oa_estado=ice_id
            $condicion2
            ) AS foo
            ORDER BY $orden $ascen
            ";
            
            //print($query);
            
            
            
            $resultado2=cargar_registros_obj($query);
            if($resultado2)
            {
                $total=count($resultado2)*1;
            }
            else
            {
                $total=0;
            }
            
            $query="
            SELECT * FROM (
            SELECT 
            inter_folio, 
            inter_ingreso, 
            pac_rut, 
            pac_appat, 
            pac_apmat, 
            pac_nombres, 
            especialidades.esp_desc, 
            inter_estado, 
            inter_id, 
            ice_desc, 
            ice_icono, 
            'IC',
            inter_fecha_salida,
            inter_motivo_salida,
            especialidades.esp_id,
            pac_id,
            inter_unidad,
            inter_fecha_egreso,
            tab_esp.esp_desc as unidad_esp_desc,
            inter_prioridad,
            inter_nomd_id,
            inter_especialidad,
            inter_espdesc
            FROM interconsulta 
            LEFT JOIN pacientes ON inter_pac_id=pac_id
            LEFT JOIN especialidades ON inter_especialidad=esp_id
	     LEFT JOIN especialidades as tab_esp ON inter_unidad=tab_esp.esp_id 
            LEFT JOIN interconsulta_estado ON inter_estado=ice_id
            $condicion $w_tipo_inter $w_estado_inter $w_tipo_salida
            UNION
            SELECT 
            oa_folio AS inter_folio, oa_fecha::date AS inter_ingreso, pac_rut, pac_appat, pac_apmat, pac_nombres, 
            esp_desc, oa_estado AS inter_estado, oa_id, ice_desc, ice_icono, 'OA',null as inter_fecha_salida,null as inter_motivo_salida,esp_id,pac_id,null as inter_unidad,
            null as inter_fecha_egreso,
            null as unidad_esp_desc,
            oa_prioridad,
            0 as inter_nomd_id,
            oa_especialidad,
            '' as inter_espdesc
            FROM orden_atencion 
            LEFT JOIN pacientes ON oa_pac_id=pac_id
            LEFT JOIN especialidades ON oa_especialidad=esp_id
            LEFT JOIN interconsulta_estado ON oa_estado=ice_id
            $condicion2
            ) AS foo
            ORDER BY $orden
            $ascen
            $limite
            ";
            
		//print($query);
            ?>
<?php

            $resultado = pg_query($conn, $query);
            
            if(isset($_GET['xls']))
            {
                header("Content-type: application/vnd.ms-excel");
            }
            print("<table width=100%>");
                print("<tr>");
                    print("<td colspan='9' align='center'>");
                        print("P&aacute;gina:");
                        print("<select id='pagina' onchange='realizar_busqueda(this.value);'>");
                            $totalPag = ceil($total/$limit);
                            $links = array();
                            for( $i=1; $i<=$totalPag ; $i++)
                            {
                                if($i==$pag)
                                {
                                    echo  "<option value='$i' SELECTED>$i</option>"; 
                                }
                                else
                                {
                                    echo  "<option value='$i'>$i</option>";
                                }
                            }
                        print ("</select>");
                    print("</td>");
                print("</tr>");
                print("<tr class='tabla_header' style='font-weight: bold;'>");
                    print("<td>Fecha Ing.</td>");
                    print("<td>Documento</td>");
                    print("<td>Rut Paciente</td>");
                    print("<td>Paterno</td>");
                    print("<td>Materno</td>");
                    print("<td>Nombre</td>");
                    print("<td>Especialidad</td>");
                    print("<td>Especialidad Destino</td>");
                    print("<td>Prioridad</td>");
                    print("<td>Estado</td>");
                    if($_GET['tipo']=='estado_interconsultas')
                    {
                        print("<td>&nbsp;</td>");
                        print("<td>&nbsp;</td>");
                        print("<td>&nbsp;</td>");
                    }
            print("</tr>");
        }
        else
        {
            $filtro=trim(pg_escape_string(utf8_decode($_GET['filtro'])));			
            $institucion=($_GET['inst_id1']*1);
            $especialidad=($_GET['especialidad']*1);
            $tipo_inter=($_GET['tipo_inter']*1);
            $tipo_esp=($_GET['tipo_esp']*1);
            $fecha1=trim(pg_escape_string($_GET['fecha1']));
            $fecha2=trim(pg_escape_string($_GET['fecha2']));
            
            
            $w_sin_esp="";
            if($tipo_esp!=0)
            {
                $w_sin_esp="and inter_especialidad=-1";
            }
            else
            {
                $w_sin_esp="and inter_especialidad not in (-1)";
            }
            
            
            $w_tipo_inter="";
            if($tipo_inter==0)
            {
                $w_tipo_inter="and inter_inst_id1!=inter_inst_id2";
            }
            if($tipo_inter==1)
            {
                $w_tipo_inter="and inter_inst_id1=inter_inst_id2";
            }
            if($filtro!='')
            {
                $w_filtro="inter_folio || ' ' || pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$filtro%'";
                $w_filtro2="oa_folio || ' ' || pac_rut || ' ' || pac_appat || ' ' || pac_apmat || ' ' || pac_nombres ILIKE '%$filtro%'";
            }
            else
            { 
                $w_filtro='true';
                $w_filtro2='true';
            }
            if($institucion==0)
            {
                $w_inst='true';
                $w_inst2='true';
            }
            else
            {
                $w_inst='inter_inst_id1='.$institucion;
                $w_inst2='oa_inst_id='.$institucion;
            }
            if($especialidad==-1)
            {
                $w_esp='true';
                $w_esp2='true';
            }
            else
            { 
                $w_esp='inter_especialidad='.$especialidad;
                $w_esp2='oa_especialidad='.$especialidad;
            }
            
            $pagina=($_GET['pagina']*1);
            
            $limit = 100;
            $pag = (int) $pagina;
            if ($pag < 1)
            {
                $pag = 1;
            }
            $offset = ($pag-1) * $limit;
            $limite = "LIMIT $limit OFFSET $offset";
            
            $consulta="
            SELECT * FROM (
            SELECT 
            inter_folio, 
            inter_ingreso, 
            pac_rut, pac_appat, pac_apmat, pac_nombres, 
            esp_desc, 
            inter_estado,
            inst_nombre,
            inter_inst_id1,
            inter_id, ice_icono, ice_desc, 'IC'
            FROM interconsulta 
            LEFT JOIN pacientes ON inter_pac_id=pac_id
            LEFT JOIN especialidades ON inter_especialidad=esp_id
            LEFT JOIN instituciones ON inter_inst_id1=inst_id
            LEFT JOIN interconsulta_estado ON inter_estado=ice_id
            WHERE 
            $w_esp AND $w_inst AND $w_filtro AND 
            inter_inst_id2=".($sgh_inst_id)." AND
            inter_estado=0
            $w_tipo_inter $w_sin_esp
            and inter_fecha_entrada::date between '$fecha1' and '$fecha2'
            UNION 
            SELECT 
            oa_folio AS inter_folio, 
            oa_fecha::date AS inter_ingreso, 
            pac_rut, pac_appat, pac_apmat, pac_nombres, 
            esp_desc, 
            oa_estado AS inter_estado,
            inst_nombre,
            oa_inst_id AS inter_inst_id1,
            oa_id, ice_icono, ice_desc, 'OA'
            FROM orden_atencion 
            LEFT JOIN pacientes ON oa_pac_id=pac_id
            LEFT JOIN especialidades ON oa_especialidad=esp_id
            LEFT JOIN instituciones ON oa_inst_id=inst_id
            LEFT JOIN interconsulta_estado ON oa_estado=ice_id
            WHERE 
            $w_esp2 AND $w_inst2 AND $w_filtro2 AND 
            oa_inst_id2=".($sgh_inst_id)." AND
            oa_estado=0 AND NOT oa_motivo=-1
            
            ) AS foo
            ORDER BY inter_ingreso asc 
            ";
            
            //print($consulta);
            //die();
            
            $resultado2=cargar_registros_obj($consulta);
            if($resultado2)
            {
                $total=count($resultado2)*1;
            }
            else
            {
                $total=0;
            }
            
            $consulta="
            SELECT * FROM (
            SELECT 
            inter_folio, 
            inter_ingreso, 
            pac_rut, pac_appat, pac_apmat, pac_nombres, 
            esp_desc, 
            inter_estado,
            inst_nombre,
            inter_inst_id1,
            inter_id, ice_icono, ice_desc, 'IC',inter_espdesc,inter_fundamentos,
            inter_fecha_entrada::date as fecha_entrada
            FROM interconsulta 
            LEFT JOIN pacientes ON inter_pac_id=pac_id
            LEFT JOIN especialidades ON inter_especialidad=esp_id
            LEFT JOIN instituciones ON inter_inst_id1=inst_id
            LEFT JOIN interconsulta_estado ON inter_estado=ice_id
            WHERE 
            $w_esp AND $w_inst AND $w_filtro AND 
            inter_inst_id2=".($sgh_inst_id)." AND
            inter_estado=0
            $w_tipo_inter $w_sin_esp
            and inter_fecha_entrada::date between '$fecha1' and '$fecha2'
            UNION 
            SELECT 
            oa_folio AS inter_folio, 
            oa_fecha::date AS inter_ingreso, 
            pac_rut, pac_appat, pac_apmat, pac_nombres, 
            esp_desc, 
            oa_estado AS inter_estado,
            inst_nombre,
            oa_inst_id AS inter_inst_id1,
            oa_id, ice_icono, ice_desc, 'OA',null,null,
            null
            FROM orden_atencion 
            LEFT JOIN pacientes ON oa_pac_id=pac_id
            LEFT JOIN especialidades ON oa_especialidad=esp_id
            LEFT JOIN instituciones ON oa_inst_id=inst_id
            LEFT JOIN interconsulta_estado ON oa_estado=ice_id
            WHERE 
            $w_esp2 AND $w_inst2 AND $w_filtro2 AND 
            oa_inst_id2=".($sgh_inst_id)." AND
            oa_estado=0 AND NOT oa_motivo=-1
            ) AS foo
            ORDER BY inter_ingreso asc $limite
            ";
            
            //print($consulta);
            //die();
            
            $resultado = pg_query($conn, $consulta);
            print("<table width=100%>");
                print("<tr>");
                    print("<td colspan='8' align='center'>");
                        print("P&aacute;gina:");
                        print("<select onchange='realizar_busqueda(this.value);'>");
                            $totalPag = ceil($total/$limit);
                            $links = array();
                            for( $i=1; $i<=$totalPag ; $i++)
                            {
                                if($i==$pag)
                                {
                                    echo  "<option value='$i' SELECTED>$i</option>"; 
                                }
                                else
                                {
                                    echo  "<option value='$i'>$i</option>";
                                }
                            }
                        print ("</select>");
                    print("</td>");
                print("</tr>");
                
            print("<tr class='tabla_header' style='font-weight: bold;'>
                    <td>Fecha Ing.</td>
                    <td>Procedencia</td>
                    <td>Documento</td>
                    <td>R.U.T. Paciente</td>
                    <td>Paterno</td>
                    <td>Materno</td>
                    <td>Nombre</td>
                    <td>Especialidad Origen</td>
                    <td>Fundamentos Clinicos</td>
                    <td>Estado</td>
                    ");
            print("</tr>");
        }
        for($i=0;$i<pg_num_rows($resultado);$i++)
        {
            $fila = pg_fetch_row($resultado);
            for($a=0;$a<count($fila);$a++) $fila[$a] = htmlentities($fila[$a]);
            ($i%2)==1? $clase='tabla_fila' : $clase='tabla_fila2';
            if($fila[0]=='-1') $fila[0]='(s/n)';
            if($_GET['tipo']=='estado_interconsultas')
            {
                if($fila[11]=='IC') $tipo='ficha'; else $tipo='oa';
                print("
                <tr class='".$clase."' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";' >
                    <td style='text-align: center;'><i>".$fila[1]."</i></td>
                    <td style='text-align: center;'>".$fila[11]."#<b>".$fila[0]."</b></td>
                    <td style='text-align: center;'><b>".$fila[2]."</b></td>
                    <td><b>".$fila[3]."</b></td>
                    <td><b>".$fila[4]."</b></td>
                    <td><b>".$fila[5]."</b></td>");
                    if($fila[21]=="-1") 
                        print("<td>".$fila[22]."</td>");
                    else
                        print("<td>".$fila[6]."</td>");
                            
                print("<td>".$fila[18]."</td>");
                if($fila[19]==0) print('<td>Sin Priorizaci&oacute;n</td>');
                if($fila[19]==1) print('<td>Baja</td>');
                if($fila[19]==2) print('<td>Media</td>');
                if($fila[19]==3) print('<td>Alta</td>');
                if($fila[19]==4) print('<td>Fecha Asignada</td>');
                if($fila[19]==5) print('<td>Documento en Auditor&iacute;a</td>');
                
                print("<td>
                    <center>");
                    if($tipo_salida==13)
                    {
                        print("<img src='iconos/calendar_view_day.png' alt='INTERCONSULTA AGENDADA' title='INTERCONSULTA AGENDADA'>");
                    }
                    else
                    {
                        print("<img src='iconos/".$fila[10].".png' alt='".$fila[9]."' title='".$fila[9]."'>");
                    }
                        
                print("</center>
                    </td>
                    <td><center>
                        <img src='iconos/script_edit.png' onClick='abrir_".$tipo."(".$fila[8].");'>
                    </center></td>
                    <td><center>
                        <img src='iconos/printer.png' onClick='print_inter_".$tipo."(".$fila[8].");'>
                    </center></td>
                    ");
                if($tipo_salida==13)
                {
                    print("
                        <td width=8%><center>
                            <img src='iconos/layout.png'  style='cursor:pointer;' alt='Imprimir Hoja AT.' title='Imprimir Hoja AT.' onClick='imprimir_citacion2(".$fila[20].");' />
                            <img width='22px;' height='22px;' onclick='gestiones_citacion_inter(".$fila[8].",".$fila[16].",".$fila[15].")' title='Gestiones Citaci&oacute;n' alt='Gestiones Citaci&oacute;n' style='cursor:pointer;' src='iconos/phone.png'>
                        </center></td>");
                }
                else
                {
                    if(($fila[7]*1)==1 && $fila[13]==0 && $fila[17]=='')
                    {
                        print("
                        <td width=8%><center>
                            <img width='22px;' height='22px;' onclick='buscar_citacion_inter(".$fila[8].",".$fila[16].",".$fila[15].");' title='Asignar Citaci&oacute;n' alt='Asignar Citaci&oacute;n' style='cursor:pointer;' src='iconos/date_magnify.png'>
                            <img width='22px;' height='22px;' onclick='gestiones_citacion_inter(".$fila[8].",".$fila[16].",".$fila[15].")' title='Gestiones Citaci&oacute;n' alt='Gestiones Citaci&oacute;n' style='cursor:pointer;' src='iconos/phone.png'>
                        </center></td>");
                    }
                    else
                    {
                        print("<td>&nbsp;</td>");
                    }
                }
                print("
                </tr>
                ");
            }
            else
            {
                if($fila[13]=='IC') $tipo='ficha'; else $tipo='oa';
                print("
                <tr class='".$clase."' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";' onClick='abrir_".$tipo."(".$fila[10].",".$fila[9].");'>
                    <td style='text-align: center;'><i>".$fila[16]."</i></td>
                    <td style='text-align: center;font-size:9px;'><i>".$fila[8]."</i></td>
                    <td style='text-align: center;'>".$fila[13]."#<b>".$fila[0]."</b></td>
                    <td style='text-align: right;'><b>".$fila[2]."</b></td>
                    <td><b>".$fila[3]."</b></td>
                    <td><b>".$fila[4]."</b></td>
                    <td><b>".$fila[5]."</b></td>
                    <td><b>".$fila[14]."</b></td>
                    <td><b>".$fila[15]."</b></td>
                    <td><center>
                    <img src='iconos/".$fila[11].".png' alt='".$fila[12]."' title='".$fila[12]."'>
                    </center></td>
                </tr>
                ");
            }	
        }
        print("</table>");
        print("<span style='display: none;'>[[".count($resultado2)."]]</span>");

    }
?>