<?php 

	require_once('../../conectar_db.php');
	
	$carp_id=$_POST['carp_id']*1;

	$filtro=pg_escape_string(trim(utf8_decode($_POST['filtro'])));
	
	$v=$_POST['ver'];

	if($v=='P') {
		$ver_w='oa_motivo_salida=0'; 
		$ver_p='Solo Pendientes';
	} elseif($v=='R') {
		$ver_w='oa_motivo_salida>0';
		$ver_p='Solo Rebajados';
	} else {
		$ver_w='true';		  
		$ver_p='(Todos...)';
	}
	
	$v2=$_POST['ver_c']*1;
		  
	if($v2!=-1) {
		$ver2_w='oa_tipo_aten='.$v2; 
	} else {
		$ver2_w='true';		  
	}


 	header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: filename=\"Informe_LEC.xls\";");

    print("
		<h2>Informe de Listas de Espera</h2>    	
    	<table>
    	<tr><td align='right'>Fecha Descarga:</td><td>".date('d/m/Y')."</td></tr>
    ");
    
    if($carp_id!=0) {
		$r=cargar_registro("SELECT * FROM orden_carpeta WHERE carp_id=$carp_id",true);
		print("
			<tr><td align='right'>Carpeta:</td><td>".$r['carp_nombre']."</td></tr>
		");
		
	}
    
    print("
    	<tr><td align='right'>Ver:</td><td>".$ver_p."</td></tr>
    	<tr><td align='right'>Filtro:</td><td>".($filtro==''?'(Sin Filtro...)':htmlentities($filtro))."</td></tr>
    	</table>	
    ");
   		 
	if($filtro!='') {
		$wfiltro = " AND (pac_rut ILIKE '%$filtro%' OR 
                (pac_appat || ' ' || pac_apmat || ' ' || pac_nombres) ILIKE '%$filtro%')"; 
  	} else {
    	$wfiltro = "";
  	}
  		
  	if($carp_id==0 AND $wfiltro=="") {

			
			$query="
			select carp_nombre, (COALESCE(oa_fecha_salida::date, current_date)-oa_fecha::date) AS dias_espera 
			from orden_carpeta 
			join orden_atencion on oa_carpeta_id=carp_id AND $ver_w AND $ver2_w
			order by carp_nombre;
			";
			
			$resumen=cargar_registros_obj($query); 
			
			$r=array();
			
			for($i=0;$i<sizeof($resumen);$i++) {
				
				if(!isset($r[$resumen[$i]['carp_nombre']])) {
					$r[$resumen[$i]['carp_nombre']]['total']=0;
					$r[$resumen[$i]['carp_nombre']]['a']=0;
					$r[$resumen[$i]['carp_nombre']]['b']=0;
					$r[$resumen[$i]['carp_nombre']]['c']=0;
				}
				
				$r[$resumen[$i]['carp_nombre']]['total']++;
				
				if($resumen[$i]['dias_espera']*1<30)
					$r[$resumen[$i]['carp_nombre']]['a']++;
				elseif($resumen[$i]['dias_espera']*1>=30 AND $resumen[$i]['dias_espera']*1<60)
					$r[$resumen[$i]['carp_nombre']]['b']++;
				elseif($resumen[$i]['dias_espera']*1>=60)
					$r[$resumen[$i]['carp_nombre']]['c']++;
				
				
				
			}
			
			print("<table style='width:100%;'>
					<tr class='tabla_header'><td colspan=5 style='font-size:16px;font-weight:bold;'>Resumen de Lista Espera I.Q.</td></tr>
					   <tr class='tabla_header'>
						   <td>Carpeta</td>
						   <td>0-30</td>
						   <td>30-60</td>
						   <td>&gt;60</td>
						   <td>Total</td>
					   </tr>");
					   
			$total_a=0;
			$total_b=0;
			$total_c=0;
			$total_t=0;		   
					   
			foreach($r AS $key => $val) {
				
				$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
				
				print("<tr class='$clase'>
					<td>".htmlentities($key)."</td>
					<td style='text-align:right;'>".$val['a']."</td>
					<td style='text-align:right;'>".$val['b']."</td>
					<td style='text-align:right;'>".$val['c']."</td>
					<td style='text-align:right;'>".$val['total']."</td>
					</tr>");
					
				$total_a+=($val['a']*1);
				$total_b+=($val['b']*1);
				$total_c+=($val['c']*1);
				$total_t+=($val['total']*1);
				
			}		   
					   
			print("
			<tr class='tabla_header'><td style='text-align:right;'>Totales:</td>
			<td style='text-align:right;'>$total_a</td>
			<td style='text-align:right;'>$total_b</td>
			<td style='text-align:right;'>$total_c</td>
			<td style='text-align:right;'>$total_t</td>
			</tr>
			</table>
			");
			
			exit();
			
	}
	
	$query="
	select 
	pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres, 
	pac_fc_nac,
	date_part('year',age(CURRENT_DATE, pac_fc_nac)) as edad,  
	(CASE WHEN sex_id=0 THEN 'M' WHEN sex_id=1 THEN 'F' END) AS sexo,
	oa_fecha::date,
	(COALESCE(oa_fecha_salida::date, CURRENT_DATE)-oa_fecha::date) AS dias_espera,
	centro_nombre AS servicio,
	carp_nombre AS carpeta,
	(CASE WHEN oa_ges=0 THEN 'NO' WHEN oa_ges=1 THEN 'SI' END) AS ges, 
	(CASE WHEN oa_tipo_aten=0 THEN 'HOSP. QUIRURGICA' WHEN oa_tipo_aten=1 THEN 'HOSP. MEDICA' WHEN oa_tipo_aten=2 THEN 'PROCEDIMIENTO' END) AS tipo_atencion, 
	COALESCE(doc_rut,'(Sin Dato...)')AS doc_rut,
	COALESCE(trim(doc_paterno || ' ' || doc_materno || ' ' || doc_nombres),'(Sin Dato...)') AS doc_nombre,
	oa_diag_cod, diag_desc,
	oa_codigo, 
	glosa,
	oa_motivo_salida,
	icc_desc AS causal_salida,
	oa_fecha_salida::date,
	oa_observacion,
	oa_fecha_ingreso_hosp
	from orden_atencion 
	join pacientes on oa_pac_id=pac_id
	join orden_carpeta on oa_carpeta_id=carp_id
	join centro_costo on oa_centro_ruta=centro_ruta
	left join doctores on oa_doc_id=doc_id
	left join codigos_prestacion ON oa_codigo=codigo
	left join interconsulta_cierre ON oa_motivo_salida=icc_id
	left join diagnosticos ON oa_diag_cod=diag_cod
	";
	  	
	  if($carp_id>0)
	  		$query.=" WHERE oa_carpeta_id=$carp_id AND $ver_w AND $ver2_w";
	  elseif($carp_id==-1)
	  		$query.=" WHERE oa_carpeta_id IS NOT NULL AND $ver_w AND $ver2_w";
	  else
			$query.=" WHERE $ver_w AND $ver2_w";
	  		
	$query.=$wfiltro."
		ORDER BY oa_fecha
	  ";
	  
	$pacs=pg_query($query);
	
  
  if($carp_id!=0) {  
	 
	 print("<div class='sub-content' style='font-size:16px;'>Total Carpeta: <b>".pg_num_rows($pacs)."</b></div><br /><br />");
	 
  }	

?>

<table style='width:100%;'>
<tr class='tabla_header' style='font-weight:bold;text-align:center;'>
<td>RUT</td>
<td>Ficha</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombres</td>
<td>Fecha de Nacimiento</td>
<td>Edad</td>
<td>Sexo</td>
<td>Fecha de Ingreso</td>
<td>Fec. Conf. IIEH</td>
<td>D&iacute;as de Espera</td>

<td>Fecha de Salida</td>
<td>Cod.</td>
<td>Causal de Salida</td>

<td>Servicio</td>
<td>Carpeta</td>
<td>GES</td>
<td>Tipo Atenci&oacute;n</td>
<td>RUT M&eacute;dico</td>
<td>Nombre M&eacute;dico</td>
<td>CIE10</td>
<td>Glosa Diag.</td>
<td>Prestaci&oacute;n</td>
<td>Glosa Presta.</td>
<td>Observaciones</td>
</tr>


<?php 

	while($r=pg_fetch_assoc($pacs)) {
		
		print("
			<tr>
			<td style='text-align:right;'>".$r['pac_rut']."</td>
			<td style='text-align:center;'>".$r['pac_ficha']."</td>
			<td>".htmlentities($r['pac_appat'])."</td>
			<td>".htmlentities($r['pac_apmat'])."</td>
			<td>".htmlentities($r['pac_nombres'])."</td>
			<td style='text-align:center;'>".$r['pac_fc_nac']."</td>
			<td style='text-align:center;'>".$r['edad']."</td>
			<td style='text-align:center;'>".$r['sexo']."</td>
			<td style='text-align:center;'>".$r['oa_fecha']."</td>
			<td style='text-align:center;'>".$r['oa_fecha_ingreso_hosp']."</td>
			<td style='text-align:right;'>".$r['dias_espera']."</td>
		");
		
		print("	
			<td style='text-align:center;'>".$r['oa_fecha_salida']."</td>
			<td style='text-align:center;'>".htmlentities($r['oa_motivo_salida'])."</td>
			<td style='text-align:left;'>".htmlentities($r['causal_salida'])."</td>
		");
			
		print("	
			<td>".$r['servicio']."</td>
			<td>".htmlentities($r['carpeta'])."</td>
			<td style='text-align:center;'>".$r['ges']."</td>
			<td>".htmlentities($r['tipo_atencion'])."</td>
			<td style='text-align:right;'>".$r['doc_rut']."</td>
			<td>".htmlentities($r['doc_nombre'])."</td>
			<td style='text-align:center;'>".$r['oa_diag_cod']."</td>
			<td style='text-align:left;'>".htmlentities($r['diag_desc'])."</td>
			<td style='text-align:center;'>".$r['oa_codigo']."</td>
			<td>".htmlentities($r['glosa'])."</td>
			<td>".htmlentities($r['oa_observacion'])."</td>
			</tr>
		");
		
	}

?>


</table>
