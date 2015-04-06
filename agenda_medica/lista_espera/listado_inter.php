<?php 
	
	
	require_once('../../conectar_db.php');
	
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	//$serv=pg_escape_string($_POST['centro_ruta']);	 
	 $xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);

		
	if ($xls==1){
			//error_reporting(E_ALL);
		$query="select * from (
SELECT inter_id,'7' as SERVICIO
,pac_rut,pac_nombres, pac_appat,(CASE WHEN pac_apmat='' THEN pac_appat ELSE pac_apmat END) AS pac_apmat,pac_fc_nac ,date_part('year',age(now()::date, pac_fc_nac)) as edad ,
( CASE WHEN sex_id=1 THEN '2 MUJER' WHEN sex_id=0 THEN '1 HOMBRE' ELSE '3 INDEFINIDO' END) as sex_desc,
( CASE WHEN prev.prev_id=0 THEN '1 FONASA' WHEN prev.prev_id=1 THEN '1 FONASA' WHEN prev.prev_id=2 THEN '1 FONASA' WHEN prev.prev_id=3 THEN '1 FONASA' WHEN prev.prev_id=4 THEN '1 FONASA' ELSE '2 OTRO' END) as prev_desc,
'1 CONSULTA NUEVA' AS tipo_prestacion,e1.esp_desc AS especialidad, e1.esp_codigo_ifl_usuario as cod_ministerial,inter_fecha_ingreso::date as entrada,
i1.inst_codigo_ifl as origen ,i2.inst_codigo_ifl || ' ' || i2.inst_nombre  as destino, 
date_trunc('hour', inter_fecha_salida)::date as salida, icc_desc as causal_salida,i3.inst_codigo_ifl || ' ' || i3.inst_nombre  as establecimiento,( CASE WHEN pac_prais=TRUE THEN ' 1 '  ELSE ' 2 ' END) as pac_prais,'5' as reg,coalesce(ciud.ciud_desc,'Quillota') as ciudad,
 coalesce(substr(('0'|| ciud_cod_nacional)::text, 1, 2) || '-' || substr(ciud_cod_nacional::text, 2, 3),'05-501') as codigo_comuna,'1' as ruralidad, coalesce(inter_fundamentos,inter_diagnostico) as inter_fundamentos,NULL as oa_hipotesis ,(coalesce(inter_fecha_salida::date,now()::date) -  inter_fecha_ingreso::date)::bigint AS dias_espera,pac_ficha

			FROM interconsulta
			JOIN pacientes ON inter_pac_id=pacientes.pac_id 
			LEFT JOIN instituciones AS i1 ON inter_inst_id1=i1.inst_id
			LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id
			LEFT JOIN instituciones AS i3 ON inter_inst_id3=i3.inst_id
			LEFT JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id
			LEFT JOIN especialidades AS e2 ON inter_unidad=e2.esp_id
			LEFT JOIN prioridad ON inter_prioridad=prior_id
			LEFT JOIN prevision as prev ON prev.prev_id=pacientes.prev_id	
			LEFT JOIN profesionales_externos ON inter_prof_id=prof_id 
			LEFT JOIN sexo USING (sex_id)
			LEFT JOIN comunas  as ciud ON ciud.ciud_id=pacientes.ciud_id
			LEFT JOIN provincias as provin ON provin.prov_id=ciud.prov_id
			LEFT JOIN regiones as reg ON reg.reg_id=provin.reg_id
			LEFT JOIN interconsulta_cierre ON icc_id=inter_motivo_salida
			where inter_estado=1 and id_caso=0 AND i2.inst_codigo_ifl='07-101' 

UNION


SELECT oa_id,'7' as SERVICIO,pac_rut,pac_nombres, pac_appat,(CASE WHEN pac_apmat='' THEN pac_appat ELSE pac_apmat END) AS pac_apmat,pac_fc_nac ,date_part('year',age(now()::date, pac_fc_nac)) as edad ,
( CASE WHEN sex_id=1 THEN '2 MUJER' WHEN sex_id=0 THEN '1 HOMBRE'ELSE '3 INDEFINIDO' END) AS sex_desc,
( CASE WHEN prev.prev_id=0 THEN '1 FONASA' WHEN prev.prev_id=1 THEN '1 FONASA' WHEN prev.prev_id=2 THEN '1 FONASA' WHEN prev.prev_id=3 THEN '1 FONASA' WHEN prev.prev_id=4 THEN '1 FONASA' ELSE '2 OTRO' END) as prev_desc,'4 INTERVENCIÓN QUIRÚRGICA' AS tipo_prestacion,
coalesce(e2.carp_nombre,esp_desc) AS especialidad, substr(oa_codigo, 1, 2) || '-' || substr(oa_codigo, 3, 2) || '-' || substr(oa_codigo, 5, 3) as cod_ministerial,oa_fecha::date as ingreso,
i1.inst_codigo_ifl  as origen,i2.inst_codigo_ifl || ' ' || i2.inst_nombre AS destino, 
date_trunc('hour', oa_fecha_salida)::date as salida, icc_desc as causal_salida,i3.inst_codigo_ifl || ' ' || i3.inst_nombre as establecimiento,( CASE WHEN pac_prais=TRUE THEN ' 1 '  ELSE ' 2 ' END) as pac_prais,'5' as reg,coalesce(ciud.ciud_desc,'Quillota') as ciudad,
 coalesce(substr(('0'|| ciud_cod_nacional)::text, 1, 2) || '-' || substr(ciud_cod_nacional::text, 2, 3),'05-501') as codigo_comunda,'1' as ruralidad,NULL as inter_fundamentos,oa_hipotesis,(coalesce(oa_fecha_salida::date,now()::date) -  oa_fecha::date)::bigint AS dias_espera ,pac_ficha

			FROM orden_atencion
			JOIN pacientes ON oa_pac_id=pacientes.pac_id 
			LEFT JOIN instituciones AS i1 ON oa_inst_id=i1.inst_id
			LEFT JOIN instituciones AS i2 ON oa_inst_id2=i2.inst_id
			LEFT JOIN instituciones AS i3 ON oa_inst_id3=i3.inst_id
			LEFT JOIN especialidades AS e1 ON oa_especialidad=e1.esp_id
			LEFT JOIN orden_carpeta AS e2 ON e2.carp_id=oa_carpeta_id
			LEFT JOIN prioridad ON oa_prioridad=prior_id
			LEFT JOIN prevision as prev ON prev.prev_id=pacientes.prev_id	
			LEFT JOIN profesionales_externos ON oa_prof_id=prof_id 
			LEFT JOIN sexo USING (sex_id)
			LEFT JOIN comunas  as ciud ON ciud.ciud_id=pacientes.ciud_id
			LEFT JOIN provincias as provin ON provin.prov_id=ciud.prov_id
			LEFT JOIN regiones as reg ON reg.reg_id=provin.reg_id
			LEFT JOIN interconsulta_cierre ON icc_id=oa_motivo_salida
			where oa_estado=1   

) as foo order by dias_espera DESC";
		
		$header="SERVICIO DE SALUD,RUT,NOMBRES,APELLIDO PATERNO,APELLIDO MATERNO,FECNAC,EDAD,SEXO,PREVISION,TIPO PRESTACION,PRESTACION,ESTABLECIMIENTO,PRESTACION MINSAL,FECHA_ENTRADA,ESTABLECIMIENTO ORIGEN,ESTABLECIMIENTO DESTINO,FECHA SALIDA,CAUSAL,SALIDA,ESTABLECIMEINTO,OTORGA,ATENCION,PRAIS,REGION,CIUDAD,COMUNA,RURALIDAD,SOSPECHA DIAGNOSTICA,CONFIRMACION DIAGNOSTICA,POSIBLE, FECHA,INTERVENCION,espera,INTERVENCION,OBS";

		pg_query("COPY ($query) TO '/var/tmp/foto.csv'  WITH CSV DELIMITER AS ';' HEADER ;");
		$archivo='/var/tmp/foto.csv';
		
		if (file_exists($archivo)) {
			header('Content-Description: File Transfer');
			header("Content-type: application/csv");
       		header("Content-Disposition: filename=\"foto.csv\";");
			header('Content-Disposition: attachment; filename='.basename($archivo));
			ob_clean();
			flush();
			readfile($archivo);
			exit;
		}
		
		}else{
		print("
		<h2>Informe de Listas de Espera</h2>    	
    	<table>
		
    			<tr><td align='right'>Fecha Descarga:</td><td>".date('d/m/Y')."</td></tr>
		");	
					$t=cargar_registros_obj("
select * from (
			SELECT i2.inst_servicio_cod_ifl as id,i2.inst_nombre as id2,pac_rut,pac_nombres, pac_appat, pac_apmat,pac_fc_nac ,date_part('year',age(now()::date, pac_fc_nac)) as edad ,sex_desc,
prev_desc,'1 Consulta Nueva' AS tipo_prestacion,e1.esp_desc AS especialidad, e1.esp_codigo_ifl_usuario as cod_ministerial,inter_fecha_ingreso::date as entrada,
i1.inst_codigo_ifl || ' ' || i1.inst_nombre as intitucion_origen,i2.inst_codigo_ifl || ' ' || i2.inst_nombre AS institucion_destino,e2.esp_desc AS unidad_receptora, 
date_trunc('hour', inter_fecha)::date as salida, icc_desc,'07-101 Hospital San Martin' as establecimiento,'S/P' as prais,reg.reg_desc as reg,ciud.ciud_desc as ciudad,
' 2 ' as cod_comuna,sector_nombre, inter_fundamentos,inter_diag_cod,'' as inter_diag,pac_ficha, (now()::date - coalesce(inter_fecha_salida, inter_fecha_ingreso)::date) AS dias_espera, '' as inter

			FROM interconsulta
			JOIN pacientes ON inter_pac_id=pacientes.pac_id 
			LEFT JOIN instituciones AS i1 ON inter_inst_id1=inst_id
			LEFT JOIN instituciones AS i2 ON inter_inst_id2=i2.inst_id 
			LEFT JOIN especialidades AS e1 ON inter_especialidad=e1.esp_id
			LEFT JOIN especialidades AS e2 ON inter_unidad=e2.esp_id
			LEFT JOIN prioridad ON inter_prioridad=prior_id
			LEFT JOIN prevision as prev ON prev.prev_id=pacientes.prev_id	
			LEFT JOIN profesionales_externos ON inter_prof_id=prof_id 
			LEFT JOIN sexo USING (sex_id)
			LEFT JOIN comunas  as ciud ON ciud.ciud_id=pacientes.ciud_id
			LEFT JOIN provincias as provin ON provin.prov_id=ciud.prov_id
			LEFT JOIN regiones as reg ON reg.reg_id=provin.reg_id
			LEFT JOIN interconsulta_cierre ON icc_id=inter_motivo_salida
			where inter_estado=1 and id_caso=0 AND inter_fecha::date BETWEEN '$fecha1' AND '$fecha2'

UNION


SELECT '7' as id,i2.inst_nombre as id2,pac_rut,pac_nombres, pac_appat, pac_apmat,pac_fc_nac ,date_part('year',age(now()::date, pac_fc_nac)) as edad ,sex_desc,
prev_desc,'4 Intervención Quirúrgica' AS tipo_prestacion, e2.carp_nombre AS especialidad, oa_codigo as cod_ministerial,oa_fecha::date as entrada,
i1.inst_codigo_ifl || ' ' || i1.inst_nombre as intitucion_origen,i2.inst_codigo_ifl || ' ' || i2.inst_nombre AS institucion_destino,e1.esp_desc AS unidad_receptora, 
date_trunc('hour', oa_fecha_salida)::date as salida, icc_desc,'07-101 Hospital San Martin' as establecimiento,'S/P' as prais,reg.reg_desc as reg,ciud.ciud_desc as ciudad,
' 2 ' as cod_comuna,sector_nombre, '' ,'','' as inter_diag,pac_ficha, (now()::date - COALESCE(oa_fecha_salida, oa_fecha)::date) AS dias_espera, '' as inter

			FROM orden_atencion
			JOIN pacientes ON oa_pac_id=pacientes.pac_id 
			LEFT JOIN instituciones AS i1 ON oa_inst_id=inst_id
			LEFT JOIN instituciones AS i2 ON oa_inst_id2=i2.inst_id 
			LEFT JOIN especialidades AS e1 ON oa_especialidad=e1.esp_id
			LEFT JOIN orden_carpeta AS e2 ON e2.carp_id=oa_carpeta_id
			LEFT JOIN prioridad ON oa_prioridad=prior_id
			LEFT JOIN prevision as prev ON prev.prev_id=pacientes.prev_id	
			LEFT JOIN profesionales_externos ON oa_prof_id=prof_id 
			LEFT JOIN sexo USING (sex_id)
			LEFT JOIN comunas  as ciud ON ciud.ciud_id=pacientes.ciud_id
			LEFT JOIN provincias as provin ON provin.prov_id=ciud.prov_id
			LEFT JOIN regiones as reg ON reg.reg_id=provin.reg_id
			LEFT JOIN interconsulta_cierre ON icc_id=oa_motivo_salida
			where oa_estado=1  AND oa_fecha::date BETWEEN '$fecha1' AND '$fecha2'
			
			) as foo order by pac_rut  LIMIT 1000;
			 ");
		}
	if ($t>0){
?>

			<table style='width:100%;'>
			<tr class='tabla_header'>
				<td>COD</td>
                <td>INST. NOMBRE</td>
				<td>R.U.T</td>
				<?php
                if(isset($_POST['xls']) AND $_POST['xls']=='1'){
					print("
						  				<td>Nombres</td>
				<td>Apellido Pat.</td>
				<td>Apellido Mat.</td>
				");
				}else{
					print("
				<td>Nombre</td>
				");
				}
				?>		
				<td>FECHA NAC.</td>
				<td>EDAD</td>
				<td>SEXO</td>
				<td>PREVISION</td>
				<td>TIPO PRESTACION</td>
				<td>PRESTACION ESTABLECIMIENTO</td>
				<td>PRESTACION MINSAL</td>
				<td>FECHA_ENTRADA</td>
				<td>ESTABLECIMIENTO ORIGEN</td>
				<td>ESTABLECIMIENTO DESTINO</td>
				<td>FECHA SALIDA</td>
				<td>CAUSAL SALIDA</td>
				<td>ESTABLECIMEINTO OTORGA ATENCION</td>
				<td>PRAIS</td>
				<td>REGION</td>
				<td>CIUDAD</td>
				<td>COMUNA</td>
				<td>RURALIDAD</td>
				<td>SOSPECHA DIAGNOSTICA</td>
				<td>CONFIRMACION DIAGNOSTICA</td>
				<td>FICHA</td>
				<td>TIEMPO ESPERA</td>
				<td>INTERVENCION</td>
  </tr>
  <?php		
		
		if($t)  for($i=0;$i<count($t);$i++) { 
		for($i=0;$i<sizeof($t);$i++) {
		$rr=$rr+1;		            
				$clase=($i%2==0)?'tabla_fila':'tabla_fila2';		
		
		
				print("<tr class='$clase' style='height:35px;'
					onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
					<td style='text-align:center;font-weight:bold;'>".htmlentities($t[$i]['id'])."</td>");
				
					if(isset($_POST['xls']) AND $_POST['xls']=='1'){
					print("<td style='text-align:center;font-weight:bold;'>".htmlentities($t[$i]['id2'])."</td>");	
					}else{
					print("<td style='text-align:center;'>".substr(htmlentities($t[$i]['id2']),0,-10)."...</td>");	
					}
					print("<td style='text-align:center;'>".htmlentities($t[$i]['pac_rut'])."</td>");	
				
					if(isset($_POST['xls']) AND $_POST['xls']=='1'){
					print("<td style='text-align:left;'>".htmlentities($t[$i]['pac_nombres'])."</td>");
					print("<td style='text-align:left;'>".htmlentities($t[$i]['pac_appat'])."</td>");
					print("<td style='text-align:left;'>".htmlentities($t[$i]['pac_apmat'])."</td>");
					}else{
										print("<td style='text-align:left;'>
					".htmlentities($t[$i]['pac_nombres'].' '.$t[$i]['pac_appat'].' '.$t[$i]['pac_apmat'])."</td>");
					}
					print("<td style='text-align:left;'>".htmlentities($t[$i]['pac_fc_nac'])."</td>
							<td style='text-align:center;'>".htmlentities($t[$i]['edad'])."</td>");
							
					if($t[$i]['sex_desc']=='Femenino'){ $sexo='F';}
					else {$sexo='M';}
					print("<td style='text-align:center;'>".$sexo."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['prev_desc'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['tipo_prestacion'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['unidad_receptora'])."</td>

							<td style='text-align:center;'>".htmlentities($t[$i]['cod_ministerial'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['entrada'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['intitucion_origen'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['institucion_destino'])."</td>
							
							<td style='text-align:left;'>".htmlentities($t[$i]['salida'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['icc_desc'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['establecimiento'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['prais'])."</td>	
							<td style='text-align:left;'>".htmlentities($t[$i]['reg'])."</td>");
					
					print("<td style='text-align:left;'>".htmlentities($t[$i]['ciudad'])."</td>
							<td style='text-align:center;'>".htmlentities($t[$i]['cod_comuna'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['sector_nombre'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['inter_fundamentos'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['inter_diagnostico'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['pac_ficha'])."</td>
							
							<td style='text-align:left;'>".htmlentities($t[$i]['dias_espera'])."</td>
							<td style='text-align:left;'>".htmlentities($t[$i]['inter'])."</td>												
					");

 
				
					
			}	//Fin for		
		
			print("<tr class='tabla_header' style='font-weight:bold;'>");
			print("<td colspan=9 style='text-align:right;'>Total de Registros</td>");
				
			print("<td style='text-align:center;'>".count($t)."</td>");
			print("</tr>");		
		
			print("</table>");	
		}
	}
	else
	{
			print('<div class=sub-content style="text-align:center;">"NO HAY REGISTROS PARA ESTE CRITERIO DE BUSQUEDA"</div>');		
	}
?>
