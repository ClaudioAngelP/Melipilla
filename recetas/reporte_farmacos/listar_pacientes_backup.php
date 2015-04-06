<?php 

	require_once('../../conectar_db.php');
	
	function nformat($num) {
		
		GLOBAL $xls;
		
		if(!$xls) {
			return number_format($num,0,',','.');
		} else {
			return number_format($num,0,'.','');			
		}
		
	}

	function nformat2($num) {
		
		GLOBAL $xls;
		
		if(!$xls) {
			return '$'.number_format($num,0,',','.').'.-';
		} else {
			return number_format($num,0,'.','');			
		}
		
	}
	
	$autf_id=$_POST['autf_id']*1;
	$bod_id=$_POST['bod_id']*1;
	$ges=$_POST['ges']*1;
	$finicio=pg_escape_string($_POST['finicio']);
	$ffinal=pg_escape_string($_POST['ffinal']);
	$xls=isset($_POST['xls']);
	
	$aut=cargar_registro("SELECT * FROM autorizacion_farmacos WHERE autf_id=$autf_id;");

	if($autf_id==-1) {
		$autf_w='autf_id not in (102, 98)';
		$nombre_aut='(Todas las patolog&iacute;as/programas...)';
	} else {
		$autf_w="autf_id=$autf_id";
		$nombre_aut=htmlentities($aut['autf_nombre']);
	}

	if($bod_id==-1) {
		$bod_w='true';
		$nombre_bod='(Todas las farmacias...)';
	} else {
		$bod_w="stock_bod_id=$bod_id";
		$tmp=cargar_registro("SELECT bod_glosa FROM bodega WHERE bod_id=$bod_id");
		$nombre_bod=htmlentities($tmp['bod_glosa']);
	}

	if($ges==0) {
		$ges_w='true';
		$nombre_ges='(Todos)';
	} elseif($ges==1) {
		$ges_w="autfp_ges='S'";
		$nombre_ges='SI';
	} elseif($ges==2) {
		$ges_w="autfp_ges='N'";
		$nombre_ges='NO';
	}


	if($xls) {
	
		header("Content-type: application/vnd.ms-excel");
		header("Content-Disposition: filename=\"reporte_farmacos.xls\";");
		
		print("<table>
				   <tr>
					   <td style='text-align:right;'>Tipo:</td><td style='font-size:16px;'><b>".$nombre_aut."</b></td>
				   </tr>
				   <tr>
					   <td style='text-align:right;'>Rango de Fechas:</td><td>$finicio - $ffinal</td>
				   </tr>
				   <tr>
					   <td style='text-align:right;'>Ubicaci&oacute;n:</td><td style='font-size:16px;'>".$nombre_bod."</td>
				   </tr>
				   <tr>
					   <td style='text-align:right;'>GES:</td><td style='font-size:16px;'>".$nombre_ges."</td>
				   </tr>
				   <tr>
					   <td style='text-align:right;'>Fecha Generaci&oacute;n:</td><td>".date('d/m/Y H:i:s')."</td>
				   </tr>
			   </table>");
    }
	
	if($autf_id==102 OR $autf_id==98) {
		$protege=true;
	} else {
		$protege=false;
	}
	
	
	if($aut['autf_patologia_ges']=='') {

		$lista=cargar_registros_obj("
			SELECT *,
			
			(substr(trim(pac_nombres), 1, 1) || 
			substr(trim(pac_appat), 1, 1) || 
			substr(trim(pac_apmat), 1, 1) || 
			lpad(date_part('day',pac_fc_nac)::text,2,'0') || 
			lpad(date_part('month',pac_fc_nac)::text,2,'0') || 
			substr(date_part('year',pac_fc_nac)::text,3,2) || 
			substr(trim(pac_rut), length(trim(pac_rut))-4,5)) AS pac_codigo
			
			FROM (
			select pac_rut, pac_ficha, upper(pac_appat) AS pac_appat, upper(pac_apmat) AS pac_apmat, pac_fc_nac, upper(pac_nombres) AS pac_nombres, log_fecha::date AS fecha, SUM(-stock_cant) AS cantidad, art_codigo, art_glosa, art_val_ult AS punit, autf_codigo_presta, autfp_ges, autf_nombre from logs 
			join stock ON stock_log_id=log_id AND $bod_w AND stock_art_id IN (select art_id from autorizacion_farmacos_detalle WHERE $autf_w)
			join recetas_detalle ON log_recetad_id=recetad_id
			join receta ON recetad_receta_id=receta_id AND receta_paciente_id IN (select pac_id from autorizacion_farmacos_pacientes WHERE $autf_w)
			join articulo on stock_art_id=art_id
			join pacientes ON receta_paciente_id=pac_id
			join autorizacion_farmacos_detalle AS adet ON $autf_w AND articulo.art_id=adet.art_id
			join autorizacion_farmacos AS autf USING (autf_id)
			join autorizacion_farmacos_pacientes AS autfp ON autf.autf_id=autfp.autf_id AND autfp.pac_id=pacientes.pac_id AND $ges_w
			where log_fecha::date between '$finicio' and '$ffinal'
			group by pac_rut, pac_ficha, pac_appat, pac_apmat, pac_nombres, pac_fc_nac, log_fecha::date, art_codigo, art_glosa, art_val_ult, autf_codigo_presta, autfp_ges, autf_nombre
			order by autf_nombre, pac_appat, pac_apmat, pac_nombres, log_fecha::date, autf_codigo_presta
			) AS foo;
		", true);
	
	} else {
	
		$lista=cargar_registros_obj("
		SELECT *,
		
		(substr(trim(pac_nombres), 1, 1) || 
		substr(trim(pac_appat), 1, 1) || 
		substr(trim(pac_apmat), 1, 1) || 
		lpad(date_part('day',pac_fc_nac)::text,2,'0') || 
		lpad(date_part('month',pac_fc_nac)::text,2,'0') || 
		substr(date_part('year',pac_fc_nac)::text,3,2) || 
		substr(trim(pac_rut), length(trim(pac_rut))-4,5)) AS pac_codigo
		
		FROM (

		select refoo.pac_id, log_fecha::date AS fecha, SUM(-stock_cant) AS cantidad, art_codigo, art_glosa, art_val_ult AS punit, autf_codigo_presta, autf_nombre, recetad_cant, recetad_horas, recetad_dias, (CASE WHEN (((recetad_dias*24/recetad_horas)*recetad_cant)*30)>1 THEN (((recetad_dias*24/recetad_horas)*recetad_cant)*30) ELSE 1 END) AS cantidad_mes, ('$finicio'::date-log_fecha::date) AS cuando 
		from logs 
		join stock ON stock_log_id=log_id AND $bod_w AND stock_art_id IN (select art_id from autorizacion_farmacos_detalle WHERE $autf_w)
		join recetas_detalle ON log_recetad_id=recetad_id
		join receta ON recetad_receta_id=receta_id
		join (
			SELECT *, (SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut ORDER BY pac_id DESC LIMIT 1) AS pac_id
			FROM patologias_sigges_traductor
			join monitoreo_ges ON mon_pst_id=pst_id
			WHERE pst_patologia_interna=(SELECT autf_patologia_ges FROM autorizacion_farmacos WHERE $autf_w)
		) AS refoo ON receta_paciente_id=pac_id
		join articulo on stock_art_id=art_id
		join autorizacion_farmacos_detalle AS adet ON $autf_w AND articulo.art_id=adet.art_id
		join autorizacion_farmacos AS autf USING (autf_id)
		
		where log_fecha::date between ('$finicio'::date-('3 months'::interval)) and '$ffinal'
		group by refoo.pac_id, mon_rut, log_fecha::date, art_codigo, art_glosa, art_val_ult, autf_codigo_presta, autf_nombre, recetad_cant, recetad_horas, recetad_dias
		order by autf_nombre, log_fecha::date, autf_codigo_presta
		
		
		) AS foo
		LEFT JOIN pacientes USING (pac_id)
		WHERE cuando<=0 OR ((cantidad/cantidad_mes)>1.0 AND (cantidad/cantidad_mes)>(cuando/30)) 
		LIMIT 1000;
		
		", true);
	
	}
		
?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>#</td>
		<?php if(!$protege) { ?>
		<td>RUT</td>
		<td>Ficha</td>
		<td>Nombre</td>
		<?php } else { ?>
		<td>C&oacute;digo Paciente</td>
		<?php } ?>
		<?php if($autf_id==-1) { ?>
		<td>Patolog&iacute;a/Programa</td>
		<?php } ?>
		<td>Fecha Despacho</td>
		<td>F&aacute;rmaco</td>
		<td>Cantidad</td>
		<td>Cnt. Mes</td>
		<td>Subtotal</td>
		<td>GES</td>
		<td>Prestaci&oacute;n</td>
	</tr>
	
<?php 

	if($lista)
	for($i=0;$i<sizeof($lista);$i++) {
		
		$class=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
			<tr class='$class'>
			<td style='text-align:right;'>".($i+1)."</td>
			");
			
		if(!$protege)
			print("
			<td style='text-align:right;font-weight:bold;'>".$lista[$i]['pac_rut']."</td>
			<td style='text-align:center;font-weight:bold;'>".$lista[$i]['pac_ficha']."</td>
			<td>".trim($lista[$i]['pac_appat']." ".$lista[$i]['pac_apmat']." ".$lista[$i]['pac_nombres'])."</td>
			");
		else
			print("
			<td style='text-align:center;font-weight:bold;'>".$lista[$i]['pac_codigo']."</td>
			");
		
		if($autf_id==-1)
			print("
			<td style='text-align:left;'>".$lista[$i]['autf_nombre']."</td>
			");
		
		print("
			<td style='text-align:center;'>".$lista[$i]['fecha']."</td>
			<td style='text-align:left;'>".$lista[$i]['art_glosa']."</td>
			<td style='text-align:right;'>".nformat($lista[$i]['cantidad'])."</td>
			<td style='text-align:right;'>".nformat($lista[$i]['cantidad_mes'])."</td>
			<td style='text-align:right;'>".nformat2(floor($lista[$i]['punit']*$lista[$i]['cantidad']))."</td>
			<td style='text-align:center;'>".$lista[$i]['autfp_ges']."</td>
			<td style='text-align:center;'>".$lista[$i]['autf_codigo_presta']."</td>
			</tr>
		");
		
	}

?>	
	
	
</table>

