<?php 
	
	require_once('../../conectar_db.php');
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$serv=pg_escape_string($_POST['centro_ruta']);	
	
	if($serv=='-1') {
		$serv_w='true';	
	} else {
		$serv_w="hosp_centro_egreso='$serv'";
	}

	if($serv=='-1') {
		$t=cargar_registros_obj("

			SELECT centro_ruta, centro_nombre, 
			(SELECT COUNT(*) FROM hospitalizacion 
			WHERE hosp_centro_ruta=centro_ruta AND
			hosp_fecha_ing::date BETWEEN '$fecha1' AND '$fecha2') AS ingresos,
			(SELECT COUNT(*) FROM (
					SELECT hospitalizacion.hosp_id, 
					COALESCE( ( SELECT centro_ruta FROM paciente_traslado WHERE 
					ptras_fecha=(SELECT MAX(ptras_fecha) FROM paciente_traslado WHERE 
						     paciente_traslado.hosp_id=hospitalizacion.hosp_id) AND
					paciente_traslado.hosp_id=hospitalizacion.hosp_id ), hosp_centro_ruta ) AS hosp_centro_egreso
					FROM hospitalizacion
					WHERE
					hosp_fecha_egr::date BETWEEN '$fecha1' AND '$fecha2') AS foo
			WHERE hosp_centro_egreso=centro_ruta) AS egresos
			FROM centro_costo
			WHERE	centro_hosp ORDER BY centro_nombre		

		");	
	
			print("
			<table style='width:100%;'>
			<tr class='tabla_header'>
			<td>Servicio</td>
			<td>Total Egresos</td>
			<td>Total Ingresos</td>
			</tr>			
		");	
	
		$ing=0;$egr=0;	
	
		for($i=0;$i<sizeof($t);$i++) {
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';		
		
			print("
				<tr class='$clase'>
					<td style='text-align:center;'>".htmlentities($t[$i]['centro_nombre'])."</td>			
					<td style='text-align:right;'>".$t[$i]['egresos']."</td>			
					<td style='text-align:right;'>".$t[$i]['ingresos']."</td>			
				</tr>");		
		
			$ing+=$t[$i]['ingresos']*1;
			$egr+=$t[$i]['egresos']*1;
					
		}
		
		print("
		<tr class='tabla_header'><td style='text-align:right;'>Totales:</td>
		<td style='text-align:right;'>$egr</td>
		<td style='text-align:right;'>$ing</td>
		</tr>		
		</table>");	

	}
	
	$t=cargar_registros_obj("SELECT *, hosp_fecha_ing::date, hosp_fecha_egr::date, 
	(hosp_fecha_egr::date - hosp_fecha_ing::date) AS dias FROM (
	SELECT *,
	COALESCE( ( SELECT centro_ruta FROM paciente_traslado WHERE 
	ptras_fecha=(SELECT MAX(ptras_fecha) FROM paciente_traslado WHERE 
	paciente_traslado.hosp_id=hospitalizacion.hosp_id) AND
	paciente_traslado.hosp_id=hospitalizacion.hosp_id ), hosp_centro_ruta ) AS hosp_centro_egreso
	FROM hospitalizacion) AS foo
	LEFT JOIN centro_costo ON hosp_centro_egreso=centro_ruta
	JOIN pacientes ON hosp_pac_id=pac_id
	WHERE
	foo.hosp_fecha_egr::date BETWEEN '$fecha1' AND '$fecha2'
	AND $serv_w
	ORDER BY foo.hosp_folio");
	
	
	$resultados="SELECT *, hosp_fecha_ing::date, hosp_fecha_egr::date, 
	(hosp_fecha_egr::date - hosp_fecha_ing::date) AS dias FROM (
	SELECT *,
	COALESCE( ( SELECT centro_ruta FROM paciente_traslado WHERE 
	ptras_fecha=(SELECT MAX(ptras_fecha) FROM paciente_traslado WHERE 
	paciente_traslado.hosp_id=hospitalizacion.hosp_id) AND
	paciente_traslado.hosp_id=hospitalizacion.hosp_id ), hosp_centro_ruta ) AS hosp_centro_egreso
	FROM hospitalizacion) AS foo
	LEFT JOIN centro_costo ON hosp_centro_egreso=centro_ruta
	JOIN pacientes ON hosp_pac_id=pac_id
	WHERE
	foo.hosp_fecha_egr::date BETWEEN '$fecha1' AND '$fecha2'
	AND $serv_w
	ORDER BY foo.hosp_folio";
	
		
	$datos=pg_query($conn,$resultados);

	if(pg_num_rows($datos)!=0)
	{		
		print("
			<table style='width:100%;'>
			<tr class='tabla_header'>
			<td>Nro. Folio</td>
			<td>Servicio</td>
			<td>Fec. Ingreso</td>
			<td>Fec. Egreso</td>
			<td>Cond. Egr.</td>
			<td>R.U.T.</td>
			<td>Nro. Ficha</td>
			<td>Nombre</td>
			</tr>			
		");
		for($i=0;$i<sizeof($t);$i++) {
				            
				$clase=($i%2==0)?'tabla_fila':'tabla_fila2';		
		
				if(trim($t[$i]['pac_rut'])!='')
					$rut=$t[$i]['pac_rut'];
				else
					$rut='F:'.$t[$i]['pac_ficha'];		

				if($t[$i]['hosp_condicion_egr']=='2')
					$cnd='F';
				elseif($t[$i]['hosp_condicion_egr']=='1')
					$cnd='V';
				else
					$cnd='?';
		
				print("<tr class='$clase' style='height:35px;'
					onMouseOver='this.className=\"mouse_over\";'
					onMouseOut='this.className=\"$clase\";'>
					<td style='text-align:center;font-weight:bold;'>".htmlentities($t[$i]['hosp_folio'])."</td>			
					<td style='text-align:center;'>".htmlentities($t[$i]['centro_nombre'])."</td>			
					<td style='text-align:center;'>".$t[$i]['hosp_fecha_ing']."</td>			
					<td style='text-align:center;'>".$t[$i]['hosp_fecha_egr']."</td>			
					<td style='text-align:center;font-weight:bold;'>".$cnd."</td>			
					<td style='text-align:right;font-weight:bold;'>".$rut."</td>
					<td style='text-align:right;font-weight:bold;'>".$t[$i]['pac_ficha']."</td>
					<td style='text-align:left;'>
					".htmlentities($t[$i]['pac_appat'].' '.$t[$i]['pac_apmat'].' '.$t[$i]['pac_nombres'])."</td>
					</td>			
				</tr>");		
					
			}			
		
			print("<tr class='tabla_header' style='font-weight:bold;'>");
			print("<td colspan=7 style='text-align:right;'>Total de Registros</td>");
				
			print("<td style='text-align:center;'>".count($t)."</td>");
			print("</tr>");		
		
			print("</table>");	
		}
		else
		{
			print('<div class=sub-content style="text-align:center;">"NO HAY REGISTROS PARA ESTE CRITERIO DE BUSQUEDA"</div>');		
		}
?>