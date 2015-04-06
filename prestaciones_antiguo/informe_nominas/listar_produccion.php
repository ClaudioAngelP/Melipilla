<?php

	require_once('../../conectar_db.php');
	
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	
	$esp_id=$_POST['esp_id']*1;

	$filtro=pg_escape_string(trim($_POST['filtro']));

   if($esp_id!=-1) {
  	 $w_esp='nom_esp_id='.$esp_id;	
   } else {
  	 $w_esp='true';
   }

   if($filtro!='') {
  	 $w_filtro="nomdp_codigo ILIKE '".$filtro."%'";	
   } else {
  	 $w_filtro='true';
   }
  
   if($_POST['xls']*1==1) {
   
    	header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"informe_nominas.xls\";");   
   	
	}
		   	
	$l=cargar_registros_obj("
		SELECT DISTINCT
		pac_id, pac_ficha,
		date_part('year',age('$fecha1'::date, pac_fc_nac)) as edad,
		sex_id,
		pac_rut, pac_appat, pac_apmat, pac_nombres, esp_desc, prev_desc
 		FROM (SELECT * FROM nomina WHERE 
 			nom_fecha::date>='$fecha1' AND 
			nom_fecha::date<='$fecha2' AND
			$w_esp
			) AS foo
 		LEFT JOIN nomina_detalle USING (nom_id)
 		LEFT JOIN especialidades ON foo.nom_esp_id=esp_id
		JOIN nomina_detalle_prestaciones USING (nomd_id)
		LEFT JOIN pacientes USING (pac_id)
		LEFT JOIN prevision on nomd_prev_id=prevision.prev_id
		WHERE 
			NOT nomina_detalle.pac_id=-1 AND
			NOT nomina_detalle.nomd_diag_cod='NSP' AND
			$w_filtro
		ORDER BY pac_appat, pac_apmat, pac_nombres
	", true);	
	
	$p=cargar_registros_obj("
		SELECT pac_id, nomdp_codigo, SUM(nomdp_cantidad) AS cantidad 
		FROM (SELECT * FROM nomina WHERE 
 			nom_fecha::date>='$fecha1' AND 
			nom_fecha::date<='$fecha2' AND
			$w_esp
			) AS foo
		JOIN nomina_detalle USING (nom_id)
		JOIN nomina_detalle_prestaciones USING (nomd_id)
		WHERE 
			NOT nomina_detalle.pac_id=-1 AND
			NOT nomina_detalle.nomd_diag_cod='NSP' AND
			$w_filtro
		GROUP BY pac_id, nomdp_codigo
		ORDER BY nomdp_codigo		
	");

	$prod=array();
	$pres=array();
	$totales=array();

	$tpres=cargar_registros_obj("SELECT * FROM procedimiento_codigo WHERE esp_id=$esp_id ORDER BY pc_codigo");

	if($p)
	for($i=0;$i<sizeof($p);$i++) {
		
		$pres[]=$p[$i]['nomdp_codigo'];
		
		if(!isset($prod[$p[$i]['pac_id']][$p[$i]['nomdp_codigo']])) {
			$prod[$p[$i]['pac_id']][$p[$i]['nomdp_codigo']]=$p[$i]['cantidad']*1;	
		} else {
			$prod[$p[$i]['pac_id']][$p[$i]['nomdp_codigo']]+=$p[$i]['cantidad']*1;		
		}

		if(!isset($totales[$p[$i]['nomdp_codigo']])) {
			$totales[$p[$i]['nomdp_codigo']]=$p[$i]['cantidad']*1;	
		} else {
			$totales[$p[$i]['nomdp_codigo']]+=$p[$i]['cantidad']*1;		
		}
		
	}
	
	$pres=array_values(array_unique($pres));
	
	$num_ausente=0;
	$num_presente=0;
	$num_nuevo=0;
	$num_control=0;	
	$num_extra=0;
	$num_masc=0;
	$num_feme=0;
	$num_altas=0;
		
	$geta=array(0,0,0,0,0,0);
	$getn=array('< 10','10-14','15-19','20-24','25-64','> 65');
	
	for($i=0;$i<sizeof($l);$i++) {
		
			$r=$l[$i];		
		
			$val=$r['nomd_diag_cod'];
			
			if($val=='NSP')
				$num_ausente++;
			else
				$num_presente++;		
			
			if($val!='NSP') {
			
				$val=$r['nomd_tipo'];
				
				if($val=='N')
					$num_nuevo++;
				else
					$num_control++;
					
				$val=$r['nomd_extra'];
				
				if($val=='S')
					$num_extra++;
					
				$val=$r['edad']*1;

				if($val<10) { $geta[0]++; }
				if($val>=10 AND $val<=14) { $geta[1]++; }
				if($val>=15 AND $val<=19) { $geta[2]++; }
				if($val>=20 AND $val<=24) { $geta[3]++; }
				if($val>=25 AND $val<=64) { $geta[4]++; }
				if($val>=65) { $geta[5]++; }
				
				$val=$r['sex_id']*1;
				
				if($val==0)
					$num_masc++;
				else
					$num_feme++;
					
				$val=$r['nomd_destino']*1;
				
				if($val==6)
					$num_altas++;
				
			}
				
		}
		
	if(sizeof($l)>0) {
			$factor=100/sizeof($l);
			$factor2=100/$num_presente;
	} else {
			$factor=0;
			$factor2=0;
	}
		
	$html='<table style="width:100%;"><tr><td>';
		
	$html.='<table style="width:100%;"><tr class="tabla_header"><td colspan=3>Grupos Et&aacute;reos</td></tr>';
		
	for($j=0;$j<sizeof($getn);$j++) {
		$clase=($j%2==0)?'tabla_fila':'tabla_fila2';		
		$html.='<tr class="'.$clase.'"><td style="text-align:right;width:40%;">'.$getn[$j].':</td><td style="font-weight:bold;text-align:center;width:20%;">'.$geta[$j].'</td><td style="text-align:center;">'.number_format($geta[$j]*$factor2,2,',','.').'%</td></tr>';
	}	
				
	$html.='</table>';
		
	$html.='</td></tr></table>';

	echo $html;

?>

<table style='width:100%;font-size:10px;'>
<tr class='tabla_header'>

<td>#</td>

<?php if($esp_id==-1) { ?><td>Especialidad</td> <?php } ?>
<td>R.U.T.</td>
<td>Ficha</td>
<td>Paterno</td>
<td>Materno</td>
<td>Nombre</td>
<td>Previsi&oacute;n</td>
<td>Sexo</td>
<td>Edad</td>

<?php 

	for($i=0;$i<sizeof($pres);$i++) {
	
		print('<td align="center"><b>'.$pres[$i].'</b></td>');	
		
	}		

?>

</tr>

<?php 

	if($l) {
			
	
		for($i=0;$i<sizeof($l);$i++) {
		
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			
			if($l[$i]['sex_id']==0)
		    	$sexo='M';
		    elseif($l[$i]['sex_id']==1)
		    	$sexo='F';
		    else 
		    	$sexo='I';

			print("
				<tr class='$clase'>
				<td align='center' class='tabla_header'>".($i+1)."</td>
			");

			if($esp_id==-1) print("<td align='center'>".$l[$i]['esp_desc']."</td>");

			print("
				<td align='right'><b>".$l[$i]['pac_rut']."</b></td>
				<td align='center'><b>".$l[$i]['pac_ficha']."</b></td>
				<td align='left'>".$l[$i]['pac_appat']."</td>
				<td align='left'>".$l[$i]['pac_apmat']."</td>
				<td align='left'>".$l[$i]['pac_nombres']."</td>
				
				<td align='center'>".$l[$i]['prev_desc']."</td>
				<td align='center'>".$sexo."</td>
				<td align='center'>".$l[$i]['edad']."</td>
			");
			
			for($j=0;$j<sizeof($pres);$j++) {
				echo '<td align="center"><b>'.$prod[$l[$i]['pac_id']][$pres[$j]].'</b></td>';	
			}			
			
			print("</tr>");		
			
			flush();			
			
		}

		if($esp_id==-1) $colspan=10; else $colspan=9;

		print("<tr class='tabla_header'><td colspan=$colspan style='text-align:right;'>Totales:</td>");

			for($j=0;$j<sizeof($pres);$j++) {
				echo '<td align="center"><b>'.$totales[$pres[$j]].'</b></td>';	
			}			

		print("</tr>");
		
	}

?>

</table>

<?php

if($_POST['mostrar']*1==1) {

$det=cargar_registros_obj("
	SELECT *,
	date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
	func_rut, func_nombre,doc_nomina.doc_rut,(doc_nomina.doc_nombres||' '||doc_nomina.doc_paterno||' '||doc_nomina.doc_materno) AS doc_nombre
	,COALESCE(diag_desc,nomd_diag)AS nomd_diag
 	FROM nomina
	JOIN nomina_detalle USING (nom_id)
	LEFT JOIN nomina_detalle_origen USING (nomd_id)
	LEFT JOIN centro_costo ON origen_centro_ruta=centro_ruta
	LEFT JOIN especialidades ON origen_esp_id=esp_id
	LEFT JOIN instituciones ON origen_inst_id=inst_id
	LEFT JOIN doctores AS doc_origen ON origen_doc_id=doc_id
	LEFT JOIN doctores AS doc_nomina ON doc_nomina.doc_id=nom_doc_id
	LEFT JOIN profesionales_externos ON origen_prof_id=prof_id
	LEFT JOIN interconsulta ON origen_inter_id=inter_id
	LEFT JOIN orden_atencion ON origen_oa_id=orden_atencion.oa_id
	LEFT JOIN funcionario ON nom_func_id=funcionario.func_id
	LEFT JOIN pacientes USING (pac_id)
	LEFT JOIN prevision on nomd_prev_id=prevision.prev_id
	LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	WHERE
 			nom_fecha::date>='$fecha1' AND
			nom_fecha::date<='$fecha2' AND
			$w_esp
", true);

	$proc=cargar_registro("SELECT * FROM procedimiento
											WHERE esp_id=$esp_id");
if($proc) {

	$campos=explode('|',$proc["esp_campos"]);

} else {

	$campos=array();

}

?>

<table style='font-size:10px;'>
<tr class='tabla_header'>
<td>Nro. Folio</td>
<td>Fecha</td>
<td>Rut Prof.</td>
<td>Nombre Profesional</td>
<td>Func. Digitador</td>
<td>R.U.T.</td>
<td>Nro. Ficha</td>
<td>Nombre Completo</td>
<td>Prevision</td>
<td>Sexo</td>
<td>Edad</td>
<td>CIE10</td>
<td>Diagn&oacute;stico</td>
<td>Or&iacute;gen</td>
<td>Motivo</td>
<td>Destino</td>
<td>AUGE</td>

<?php

	for($i=0;$i<sizeof($tpres);$i++) {
		print('<td align="center"><b>'.$tpres[$i]['pc_codigo'].'</b><br /><font style="font-size:8px;">'.htmlentities($tpres[$i]['pc_desc']).'</font></td>');
	}

	for($i=0;$i<sizeof($campos);$i++) {
		$campos[$i]=explode('>>>', $campos[$i]);
		print("<td>".htmlentities($campos[$i][0])."</td>");
	}

?>

<td>Documento</td>
<td>Fecha de Solicitud</td>
<td>Instituci&oacute;n/Servicio Or&iacute;gen</td>
<td>Especialidad Or&iacute;gen</td>
<td>R.U.T. Solicitante</td>
<td>M&eacute;dico/Profesional Solicitante</td>

<td>Observaciones</td>

</tr>

<?php

for($i=0;$i<sizeof($det);$i++) {

	$clase=($i%2==0)?'tabla_fila':'tabla_fila2';

	if($det[$i]['sex_id']==0)
		$sexo='M';
	elseif($det[$i]['sex_id']==1)
		$sexo='F';
	else
		$sexo='I';

	switch($det[$i]['nomd_origen']) {
		case 'A': $origen='Ambulatorio'; break;
		case 'H': $origen='Hospitalizado'; break;
		default: $origen='Urgencias'; break;
	}

	print("
		<tr class='$clase'>
		<td align='center'>".$det[$i]['nom_folio']."</td>
		<td align='center'>".$det[$i]['nom_fecha']."</td>
		<td align='center'>".$det[$i]['doc_rut']."</td>
		<td align='center'>".$det[$i]['doc_nombre']."</td>
		<td align='left'>".$det[$i]['func_nombre']."</td>
		<td align='right'>".$det[$i]['pac_rut']."</td>
		<td align='center'>".$det[$i]['pac_ficha']."</td>
		<td align='left'>".trim($det[$i]['pac_appat']." ".$det[$i]['pac_apmat']." ".$det[$i]['pac_nombres'])."</td>
		<td align='center'>".$det[$i]['prev_desc']."</td>
		<td align='center'>".$sexo."</td>
		<td align='center'>".$det[$i]['edad']."</td>
		<td align='center'>".$det[$i]['nomd_diag_cod']."</td>
		<td align='center'>".$det[$i]['nomd_diag']."</td>
		<td align='center'>".$origen."</td>
		<td align='center'>".$det[$i]['nomd_motivo']."</td>
		<td align='center'>".$det[$i]['nomd_destino']."</td>
		<td align='center'>".$det[$i]['nomd_auge']."</td>
	");

	$pre=cargar_registros_obj("SELECT * FROM nomina_detalle_prestaciones WHERE nomd_id=".$det[$i]['nomd_id'], true);

	for($k=0;$k<sizeof($tpres);$k++) {
	
		print('<td align="center">');

		$count=0;	
	
		for($j=0;$j<sizeof($pre);$j++) {
	
			if($tpres[$k]['pc_id']==$pre[$j]['pc_id']) {
				$count+=($pre[$j]['nomdp_cantidad']*1);
			}
						
		}

		print($count);

		print('</td>');
		
	} 	

	if($esp_id!=-1) {

		$cmp=cargar_registros_obj("SELECT * FROM nomina_detalle_campos WHERE nomd_id=".$det[$i]['nomd_id'], true);
		
		for($j=0;$j<sizeof($cmp);$j++) {
			$cc[$cmp[$j]['nomdc_offset']*1]=$cmp[$j]['nomdc_valor'];	
		}	
		
		for($j=0;$j<sizeof($campos);$j++) {
			if($campos[$j][1]<=1) {
				print("<td align='center'>".(($cc[$j]=='true')?'S':'N')."</td>");	
			} else {
				print("<td align='left'>".$cc[$j]."</td>");		
			}
		}
	
	}

	if($det[$i]['origen_inter_id']*1!=0) {
		print("<td align='center'><b>IC#".$det[$i]['inter_folio']."</b></td>");		
	} elseif($det[$i]['origen_oa_id']*1!=0) {
		print("<td align='center'><b>OA#".$det[$i]['oa_folio']."</b></td>");
	} else {
		print("<td align='center'>(n/a)</td>");	
	}				
	
	if($det[$i]['origen_tipo']*1==1) {
		print("
			<td align='center'>".$det[$i]['origen_fecha_solicitud']."</td>		
			<td>".$det[$i]['centro_nombre']."</td>		
			<td>".$det[$i]['esp_desc']."</td>		
			<td align='right'>".$det[$i]['doc_rut']."</td>		
			<td>".trim($det[$i]['doc_paterno']." ".$det[$i]['doc_materno']." ".$det[$i]['doc_nombres'])."</td>		
		");	
	} else {
		print("
			<td align='center'>".$det[$i]['origen_fecha_solicitud']."</td>		
			<td>".$det[$i]['inst_nombre']."</td>		
			<td>".$det[$i]['esp_desc']."</td>		
			<td align='right'>".$det[$i]['prof_rut']."</td>		
			<td>".trim($det[$i]['prof_paterno']." ".$det[$i]['prof_materno']." ".$det[$i]['prof_nombres'])."</td>		
		");			
	}	

	print("<td>".$det[$i]['nomd_observaciones']."</td>");
	
	print("
		</tr>	
	");	
	
}

?>

</table>

<?php	
	
}

?>