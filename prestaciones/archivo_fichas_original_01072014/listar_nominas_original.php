<?php  

require_once('../../conectar_db.php');
error_reporting(E_ALL);

	$tipo=$_POST['tipo_inf'];

	$fecha = pg_escape_string($_POST['fecha1']); 
	
	$esp_id = $_POST['esp_id']*1;
	if($esp_id!=-1) $esp="especialidades.esp_id=$esp_id";
	else $esp="true";
	
	$doc_id= $_POST['doc_id']*1;
	if($doc_id!=-1) $doc="doctores.doc_id=$doc_id";
	else $doc="true";

	$agrupar=$_POST['agrupar']*1;


	if($agrupar==0) $agrupar_o='esp_desc,doc_nombre';
	if($agrupar==1) $agrupar_o='esp_desc';
	
    if($tipo==1 OR $tipo==3)
    {
        $nom_ant='';
	$doc_ant='';
	$esp_ant='';
        if($tipo==1)
        {
            $consulta="SELECT nomina.nom_id,nom_fecha::date AS fecha,nomd_hora, upper(esp_desc)AS esp,doc_rut,pacientes.pac_ficha,
            upper(doc_nombres||' '||doc_paterno||' '||doc_materno) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id, 
            date_trunc('second',COALESCE(nomd_fecha_asigna,nom_fecha))AS fecha_asigna,nomd_id,nomd_diag_cod,
            especialidades.esp_id,doctores.doc_id,
            
            COALESCE(
            (
                SELECT COALESCE(esp_desc,
                ( 
                    SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') 
                    FROM archivo_movimientos 
                    LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
                    WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                    AND archivo_movimientos.pac_id=pacientes.pac_id 
                    ORDER BY am_fecha DESC LIMIT 1 
                )
            ) 
            FROM archivo_movimientos 
            LEFT JOIN especialidades ON origen_esp_id=esp_id 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
            ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_anterior
            


            ,
            COALESCE(
            (
                SELECT COALESCE(esp_desc,(
			SELECT COALESCE('(' || centro_nombre ||')','ARCHIVO') FROM archivo_movimientos 
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
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
            (SELECT COUNT(*) FROM nomina_detalle AS nd2 JOIN nomina AS n2 USING (nom_id) WHERE nd2.pac_id=pacientes.pac_id AND n2.nom_fecha=nomina.nom_fecha AND nomd_diag_cod NOT IN ('X','T','H')) AS peticiones,
            (SELECT COUNT(*) FROM ficha_espontanea AS fesp WHERE fesp.pac_id=pacientes.pac_id AND fesp.fesp_fecha::date=nomina.nom_fecha::date AND fesp_estado=0) AS peticiones2
            FROM nomina
            LEFT JOIN nomina_detalle USING (nom_id)
            LEFT JOIN especialidades ON nom_esp_id=esp_id
            LEFT JOIN doctores ON nom_doc_id=doc_id
            JOIN pacientes USING (pac_id)
            WHERE esp_ficha AND nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('B','T')
            AND $esp AND $doc
            ORDER BY nom_fecha,$agrupar_o,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
            
            $salidas = cargar_registros_obj($consulta);
        }
        if($tipo==3)
        {
            $consulta="SELECT 
            fesp_fecha::date AS fecha_asigna,
            (case when ficha_espontanea.esp_id!=0 then upper(esp_desc) else (select upper(centro_nombre) from centro_costo where centro_ruta=fesp_centro_ruta) end)AS esp,
            doc_rut,
            pacientes.pac_ficha, 
            (
            case when ficha_espontanea.esp_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
            else (select func_nombre from funcionario where func_id=fesp_func_id) 
            end ) AS doc_nombre, 
            pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
            fesp_estado, especialidades.esp_id,doctores.doc_id ,fesp_id AS nomd_id,pac_id,amp_nombre,'' AS nomd_diag_cod,
            

            COALESCE(
            (
                SELECT COALESCE(esp_desc,
                ( 
                    SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') 
                    FROM archivo_movimientos 
                    LEFT JOIN centro_costo ON am_centro_ruta_origen=centro_ruta 
                    WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
                    AND archivo_movimientos.pac_id=pacientes.pac_id 
                    ORDER BY am_fecha DESC LIMIT 1 
                )
            ) 
            FROM archivo_movimientos 
            LEFT JOIN especialidades ON origen_esp_id=esp_id 
            WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
            ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_anterior


            ,
            COALESCE(
            (
		SELECT COALESCE(esp_desc,(
			SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') FROM archivo_movimientos 
			LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
			WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
			ORDER BY am_fecha DESC LIMIT 1
		)) 
		FROM archivo_movimientos 
		LEFT JOIN especialidades ON destino_esp_id=esp_id 
		WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
		ORDER BY am_fecha DESC LIMIT 1
            ),'ARCHIVO'
            ) as ubic_actual
            ,
            COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
            COALESCE((SELECT COALESCE(nomd_id,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as nomd_id_sel,
            (SELECT COUNT(*) FROM nomina_detalle AS nd2 JOIN nomina AS n2 USING (nom_id) WHERE nd2.pac_id=pacientes.pac_id AND nom_fecha::date=ficha_espontanea.fesp_fecha::date AND nomd_diag_cod NOT IN ('X','T','H')) AS peticiones,
            (SELECT COUNT(*) FROM ficha_espontanea AS fesp WHERE fesp.pac_id=pacientes.pac_id AND fesp.fesp_fecha::date=ficha_espontanea.fesp_fecha::date AND fesp.fesp_estado=0) AS peticiones2,
            esp_ficha
            FROM ficha_espontanea
            LEFT JOIN especialidades using(esp_id)
            LEFT JOIN doctores using (doc_id )
            LEFT JOIN archivo_motivos_prestamo USING (amp_id)
            JOIN pacientes USING (pac_id)
            WHERE (esp_ficha or esp_ficha is null) AND fesp_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND fesp_estado=0 AND $esp AND $doc
            GROUP BY fesp_fecha,esp_desc, doc_rut,doc_nombres, doc_paterno,doc_materno, pacientes.pac_ficha,pac_rut,pac_nombres,
            pac_appat,pac_apmat, especialidades.esp_id,doctores.doc_id, fesp_estado,fesp_id,pacientes.pac_id, amp_nombre
            ORDER BY fesp_fecha,esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
            
            //print($consulta);
            
            $salidas=cargar_registros_obj($consulta);
        }
            // LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
    }
    elseif($tipo==2)
    {
        $nom_ant='';
        $doc_ant='';
        $esp_ant='';
        $consulta="SELECT 
        am_fecha::date AS fecha,
        am_fecha::time AS nomd_hora, 
        (case when esp_desc!='' then upper(esp_desc) else (select centro_nombre from centro_costo where centro_ruta=am_centro_ruta_destino) end)AS esp,
        doc_rut,
        pacientes.pac_ficha,
        (
	case when doc_id!=0 then upper(doc_nombres||' '||doc_paterno||' '||doc_materno) 
	else (select func_nombre from funcionario where func_id=am_func_id) 
	end 
        ) AS doc_nombre,
        pac_rut,upper(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,pacientes.pac_id,'' AS nomd_diag_cod, 
        date_trunc('second',
        COALESCE((SELECT nomd_fecha_asigna FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomina_detalle.pac_id=pacientes.pac_id AND nom_esp_id=destino_esp_id AND nom_doc_id=destino_doc_id ORDER BY nom_fecha DESC LIMIT 1),
        (SELECT fesp_fecha FROM ficha_espontanea WHERE ficha_espontanea.pac_id=pacientes.pac_id AND ficha_espontanea.esp_id=destino_esp_id AND ficha_espontanea.doc_id=destino_doc_id ORDER BY fesp_fecha DESC LIMIT 1))
        )AS fecha_asigna,
        especialidades.esp_id,doctores.doc_id,
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
			WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
			AND archivo_movimientos.pac_id=pacientes.pac_id 
			ORDER BY am_fecha DESC LIMIT 1
		),'ARCHIVO'
        ) as ubic_anterior, 



        COALESCE(
            (
			SELECT COALESCE(esp_desc,(
				SELECT COALESCE('(' || centro_nombre || ')','ARCHIVO') FROM archivo_movimientos 
				LEFT JOIN centro_costo ON am_centro_ruta_destino=centro_ruta 
				WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id 
				ORDER BY am_fecha DESC LIMIT 1
			)) 
			FROM archivo_movimientos 
			LEFT JOIN especialidades ON destino_esp_id=esp_id 
			WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha 
			AND archivo_movimientos.pac_id=pacientes.pac_id 
			ORDER BY am_fecha DESC LIMIT 1
		),'ARCHIVO') 
        as ubic_actual
        ,
        COALESCE((SELECT COALESCE(am_estado,0) FROM archivo_movimientos WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id ORDER BY am_fecha DESC LIMIT 1),0) as am_estado,
        (SELECT am_fecha FROM archivo_movimientos LEFT JOIN especialidades ON destino_esp_id=esp_id WHERE archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id AND am_estado=2 ORDER BY am_fecha DESC LIMIT 1) as fecha_envio
        FROM archivo_movimientos
        LEFT JOIN especialidades ON destino_esp_id=esp_id
        LEFT JOIN doctores ON destino_doc_id=doc_id
        JOIN pacientes USING (pac_id)
        WHERE 
        am_final AND am_estado IN (2,3) AND archivo_movimientos.pac_ficha=pacientes.pac_ficha AND archivo_movimientos.pac_id=pacientes.pac_id
        AND $esp AND $doc
        ORDER BY esp_desc,doc_nombre,(CASE WHEN pacientes.pac_ficha='' THEN '0' WHEN pacientes.pac_ficha IS NULL THEN '0' ELSE pacientes.pac_ficha END)::bigint";
        //print($consulta);
        
        $salidas = cargar_registros_obj($consulta);
        // nom_fecha BETWEEN '$fecha 00:00:00' AND '$fecha 23:59:59' AND nomd_diag_cod NOT IN ('X','T','B')
        // LEFT JOIN archivo_fichas ON pacientes.pac_ficha=archivo_fichas.pac_ficha AND arc_estado=1
    }
    $opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
    $opts_color=Array('black','gray','blue','purple','green','red');
    if($tipo==2 AND !$salidas)
    {
        print("<center><h1>(No tiene fichas pendientes por recepcionar...)</h1></center>");
    }
    if($salidas)
        for($i=0;$i<count($salidas);$i++)
        {
            ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
            $checked='';
            $color='';
            if($salidas[$i]['esp_id']!="")
            {
                $esp_string=$salidas[$i]['esp_id'];
            }
            else
            {
                $esp_string=$salidas[$i]['esp'];
            }
            if(($agrupar==0 AND $doc_ant!=$salidas[$i]['doc_id']) OR $esp_ant!=$esp_string)
            {
                
                $doc_ant=$salidas[$i]['doc_id'];
                if($salidas[$i]['esp_id']!="")
                {
                    $esp_ant=$salidas[$i]['esp_id'];
                }
                else
                {
                    $esp_ant=$salidas[$i]['esp'];
                }
                
                
                
		$cont=1;
		if($i>0)
                    print("</table>");
			
		if(isset($salidas[$i]['amp_nombre']) AND $salidas[$i]['amp_nombre']!='')
                {
                    $motivo='<br/>Motivo Solicitud: <b>'.htmlentities($salidas[$i]['amp_nombre']).'</b>';
		}
                else
                {
                    $motivo='';
		}
		$motivo='';
                if($tipo==3)
                    $colspan=11;
                else
                    $colspan=10;
                
                if($salidas[$i]['esp_id']==0)
                {
                    print("<table style='width:100%;' class='lista_small' cellspacing=0 cellpadding=1>
                    <tr class='tabla_header'>
                    <td style='text-align:left;font-size:16px;' colspan=$colspan>
                    Servicio: <b>".htmlentities($salidas[$i]['esp'])."</b><br/>
                    ");
                }
                else
                {
                    print("<table style='width:100%;' class='lista_small' cellspacing=0 cellpadding=1>
                    <tr class='tabla_header'>
                    <td style='text-align:left;font-size:16px;' colspan=$colspan>
                    Programa: <b>".htmlentities($salidas[$i]['esp'])."</b><br/>
                    ");
                }
                if($agrupar==0)
                    print("Profesional/Servicio: <b>".htmlentities($salidas[$i]['doc_nombre'])."</b>");
                
                print("
                $motivo
                </td>
                <td>");
                if($tipo==1)
                {
                    print("<input type='button' id='imprimir_".$salidas[$i]['esp_id']."' name='imprimir_".$salidas[$i]['esp_id']."' value='Imprimir' onClick='imprimir_especialidad(".$salidas[$i]['esp_id'].",".$agrupar.",".$salidas[$i]['doc_id'].",\"".$fecha."\");'/>");
                }
                else
                {
                    print("&nbsp;");
                }
                print("</td>
                </tr>
                <tr class='tabla_header'>
                    <td style='width:3%;'>#</td>
                    <td style='width:10%;'>Solicitado</td>
                ");
		if($tipo==2)
                    print("<td style='width:10%;'>Enviada</td>");
		
                print("<td style='width:10%;'>Ficha</td>");
                if($tipo==1 OR $tipo==3)
                    print("<td style='width:3%;'>P*</td>");
                
                print("
		<td style='width:10%;'>RUN</td>
		<td style='width:25%;'>Nombre Completo</td>
		");
                        
                if($tipo==3)
                {
                    print("<td style='width:10%;'>Motivo Solicitud</td>");
                }
			
		if($tipo!=2)
                    print("
                    <td style='width:15%;'>Ubic. Anterior</td>
                    <td style='width:15%;'>Ubic. Actual</td>
                    ");
			
		print("
		<td>Estado Actual</td>
		<td>Etiqueta</td>
		<td>Historial</td>
		</tr>");
            }
            $options='';
            for($l=0;$l<sizeof($opts);$l++)
            {
                if($salidas[$i]['am_estado']*1==$l)
                    $sel='SELECTED'; else $sel='';
                
                $options.='<option value="'.$l.'" '.$sel.'>'.$opts[$l].'</option>';
            }
            if($salidas[$i]['pac_ficha']!='' && $salidas[$i]['pac_ficha']!='0')
            {
                $ficha=$salidas[$i]['pac_ficha'];
                $ficha.="<input type=hidden id='".$salidas[$i]['pac_rut']."_ficha' name='".$salidas[$i]['pac_rut']."_ficha' value='".$salidas[$i]['pac_ficha']."' />";
                $ficha.="<input type=hidden id='".$salidas[$i]['pac_ficha']."_ficha' name='".$salidas[$i]['pac_ficha']."_ficha' value='".$salidas[$i]['pac_ficha']."' />";
            }
            else
            {
                $ficha="<center>";
                $ficha.="<input type=hidden id='".$salidas[$i]['pac_rut']."_ficha' name='".$salidas[$i]['pac_rut']."_ficha' value='".$salidas[$i]['pac_ficha']."' />";
                $ficha.="<input type=hidden id='".$salidas[$i]['pac_ficha']."_ficha' name='".$salidas[$i]['pac_ficha']."_ficha' value='".$salidas[$i]['pac_ficha']."' />";
		if(_cax(20002))
                    $ficha.="<input type='button' style='font-size:8px;margin:0px;padding:0px;' id='asigna_".$salidas[$i]['pac_id']."' name='asigna_".$salidas[$i]['pac_id']."' onClick='asignar_ficha(".$salidas[$i]['pac_id'].");' value='[ASIGNAR]' />";

		$ficha.="<input type='button' style='font-size:8px;margin:0px;padding:0px;' id='crea_".$salidas[$i]['pac_id']."' name='crea_".$salidas[$i]['pac_id']."' onClick='crear_ficha(".$salidas[$i]['pac_id'].");' value='[CREAR]' /></center>";
            }
            if($tipo!=2)
            {
                if($salidas[$i]['nomd_id_sel']*1==$salidas[$i]['nomd_id']*1)
                {
			$color='background-color:#bbbbff;';
		}
                else
                {
                    $color='';
		}
            }
            if($salidas[$i]['nomd_diag_cod']=='X' OR $salidas[$i]['nomd_diag_cod']=='T')
            {
                $tachar='text-decoration:line-through;';
            }
            else
            {
                $tachar='';
            }
            
            print("<tr class='$clase' style='color:".$opts_color[$salidas[$i]['am_estado']*1].";$color;$tachar'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"".$clase."\";'>
			<td style='text-align:right;font-weight:bold;'>".$cont."</td>
			<td style='text-align:center;'>".substr($salidas[$i]['fecha_asigna'],0,16)."</td>
		");
		
		if($tipo==2)
		print("
			<td style='text-align:center;'>".substr($salidas[$i]['fecha_envio'],0,16)."</td>	
		");
		
		print("
			<td style='text-align:center;font-size:14px;font-weight:bold;white-space:nowrap;'>".$ficha."</td>");

		if($tipo==1 OR $tipo==3)
		print("<td style='text-align:center;'>".(($salidas[$i]['peticiones']*1)+($salidas[$i]['peticiones2']*1))."</td>");

		print("
			<td style='text-align:right;'>".$salidas[$i]['pac_rut']."</td>
			<td style='text-align:left;'>".htmlentities($salidas[$i]['pac_nombre'])."</td>
		");
                
                if($tipo==3)
                {
                    if(isset($salidas[$i]['amp_nombre']) AND $salidas[$i]['amp_nombre']!='')
                    {
                        $amp_nombre=''.htmlentities($salidas[$i]['amp_nombre']).'';
                    }
                    else
                    {
                        $amp_nombre='&nbsp;';
                    }
                    print("<td style='text-align:left;'>".$amp_nombre."</td>");

                }
		
		if($tipo!=2) {
		print("
			<td style='text-align:center;'>".htmlentities($salidas[$i]['ubic_anterior'])."</td>
			<td style='text-align:center;font-weight:bold;'>".htmlentities($salidas[$i]['ubic_actual'])."</td>
		");
		}
		
		print("
			<td style='text-align:center;font-weight:bold;'>
			".$opts[$salidas[$i]['am_estado']*1]."
			</td><td>
			 <center><img src='iconos/printer.png'  style='cursor:pointer;'
                alt='Imprimir Etiqueta' title='Imprimir Etiqueta' onClick='imprimir_etiqueta(".$salidas[$i]['pac_id'].");' /></center>
			</td><td>
			 <center><img src='iconos/magnifier.png'  style='cursor:pointer;'
                alt='Ver Historial' title='Ver Historial' onClick='historial_ficha(".$salidas[$i]['pac_id'].");' /></center>
			</td></tr>");
			    
		//$nom_ant=$salidas[$i]['nom_id'];
		$cont++;
  
	}


	print("</table>");




?>
