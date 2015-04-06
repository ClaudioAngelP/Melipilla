<?php 

	require_once('../../conectar_db.php');
	
	$hosp_id=$_POST['hosp_id']*1;

	//$pac_id=$h['hosp_pac_id']*1;
	
	$ha=cargar_registros_obj("

			SELECT *,d2.func_nombre AS autoriza_nombre
			FROM hospitalizacion_autorizacion_meds
			JOIN articulo USING (art_id)
			JOIN funcionario ON hospam_func_id=func_id
			JOIN doctores ON hospam_doc_id=doc_id
			LEFT JOIN funcionario AS d2 ON d2.func_id=hospam_func_id2
			LEFT JOIN bodega_forma ON art_forma=forma_id
			WHERE hosp_id=$hosp_id
			ORDER BY hospam_fecha_digitacion DESC;

	", true);
	
	
		
	print("
	<table style='width:100%;'>
		<tr class='tabla_header'>
			<td>Fecha/Hora</td>
			<td>C&oacute;digo</td>
			<td>Art&iacute;culo</td>
			<td>M&eacute;dico/Funcionario Dig.</td>
			<td>Estado</td>
			<td>Eliminar</td>
		</tr>
	");
	
	if($ha)
	for($i=0;$i<sizeof($ha);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		switch($ha[$i]['hospam_estado']) {
			case 0: $estado='<img src="../../iconos/clock.png" style="width:24px;height:24px;"><br/>Pendiente'; $color='blue'; break;
			case 1: $estado='<img src="../../iconos/tick.png" style="width:24px;height:24px;"><br/>Aceptado'; $color='green'; break;
			case 2: $estado='<img src="../../iconos/arrow_refresh.png" style="width:24px;height:24px;"><br/>Modificado'; $color='yellow'; break;
			case 3: $estado='<img src="../../iconos/cross.png" style="width:24px;height:24px;"><br/>Rechazado'; $color='red'; break;
		}
		
		if($ha[$i]['hospam_cultivo']=='' AND $ha[$i]['hospam_diagnostico']=='') {
			$rspan1=4;
			$rspan2=4;
			if($ha[$i]['autoriza_nombre']!='')$rspan1++;
			if($ha[$i]['hospam_estado']==2 OR $ha[$i]['hospam_estado']==3)$rspan1++;
			
		} else {
			//$rspan1=5;
			$rspan2=6;
			if($ha[$i]['autoriza_nombre']!='')$rspan1++;
			if($ha[$i]['hospam_estado']==2 OR $ha[$i]['hospam_estado']==3)$rspan1++;
			
			
		}
		
		print("<tr class='$clase'>
		<td style='text-align:center;font-weight:bold;' rowspan=$rspan1>".substr($ha[$i]['hospam_fecha_digitacion'],0,16)."</td>
		<td style='text-align:right;font-weight:bold;font-size:16px;'>".$ha[$i]['art_codigo']."</td>
		<td style='text-align:left;font-size:16px;'>".$ha[$i]['art_glosa']."</td>
		<td style='text-align:center;'><i>".$ha[$i]['doc_nombres']." ".$ha[$i]['doc_paterno']." ".$ha[$i]['doc_materno']."</i></td>
		<td style='text-align:center;color:font-weight:bold;color:$color;font-size:14px;' rowspan=$rspan2>".$estado."</td>
		<td rowspan=$rspan2><center>
		");
		
		if($ha[$i]['hospam_estado']==0)
			print("<img src='../../iconos/delete.png' style='cursor:pointer;' 
			onClick='eliminar_am(".$ha[$i]['hospam_id'].");'>");
		else
			print("<img src='../../iconos/stop.png'>");
			
		if($ha[$i]['hospam_observaciones']=='')
			$ha[$i]['hospam_observaciones']='<i>(Sin Observaciones...)</i>';
			
		print("</center></td>
		</tr><tr class='$clase'>
		<td style='text-align:right;font-weight:bold;'>Tipo:</td><td style='text-align:left;' colspan=1>".$ha[$i]['hospam_motivo']." (".$ha[$i]['hospam_terapia'].")</td><td style='text-align:center;'><i>".$ha[$i]['func_nombre']."</i></td>
		</tr><tr class='$clase'>
		<td style='text-align:right;font-weight:bold;'>Dosis:</td><td style='text-align:left;' colspan=2><b>".$ha[$i]['hospam_cant']." ".$ha[$i]['hospam_forma']."</b> cada <b>".$ha[$i]['hospam_horas']."</b> horas durante <b>".$ha[$i]['hospam_dias']."</b> d&iacute;as.</td>
		</tr><tr class='$clase'>
		<td style='text-align:right;font-weight:bold;'>Observaciones:</td><td style='text-align:left;' colspan=2>".$ha[$i]['hospam_observaciones']."</td>
		</tr>");
		
		if($rspan1==7) {
			print("<tr class='$clase'>
			<td style='text-align:right;font-weight:bold;'>Cultivo:</td><td style='text-align:left;' colspan=2>".$ha[$i]['hospam_cultivo']."</td>
			</tr><tr class='$clase'>
			<td style='text-align:right;font-weight:bold;'>Diagn&oacute;stico:</td><td style='text-align:left;' colspan=2>".$ha[$i]['hospam_diagnostico']."</td>
			</tr>");
		}
		
		if($ha[$i]['autoriza_nombre']!=''){
		
		if($ha[$i]['hospam_estado']==1){
			$visado='Autorizado';
		}else if($ha[$i]['hospam_estado']==2){
			$visado='Modificado';
		}else{
			$visado='Rechazado';
		}
		print("<tr class='$clase'>
			<td style='text-align:right;font-weight:bold;' colspan=3>$visado Por:</td>
			<td style='text-align:left;font-size:12;' colspan=2><i>".$ha[$i]['autoriza_nombre']."</i></td>
			</tr>");
			
		if($ha[$i]['hospam_estado']==2 OR $ha[$i]['hospam_estado']==3){
					print("<tr class='$clase'>
			<td style='text-align:right;font-weight:bold;' colspan=3>Fundamento:</td>
			<td style='text-align:left;font-size:12;' colspan=2><i>".$ha[$i]['hospam_fundamento']."</i></td>
			</tr>");
			
		
		
		}
		}
		
	}
		
	print("	
	</table>
	");

?>
