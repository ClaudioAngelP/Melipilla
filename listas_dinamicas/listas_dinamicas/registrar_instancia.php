<?php 

	require_once('../conectar_db.php');
	
	$in_id=$_GET['in_id']*1;
	
	$in=cargar_registro("SELECT *, UPPER(pac_appat) as pac_appat, UPPER(pac_apmat) AS pac_apmat, UPPER(pac_nombres) AS pac_nombres,
						 (CURRENT_TIMESTAMP::date-in_fecha::date)::integer AS dias
						 FROM lista_dinamica_instancia 
						 JOIN lista_dinamica_caso USING (caso_id)
						 JOIN pacientes USING (pac_id)
						 WHERE in_id=$in_id", true);

	$pac=cargar_registro("
		SELECT *,
		UPPER(pac_appat) as pac_appat, UPPER(pac_apmat) AS pac_apmat, UPPER(pac_nombres) AS pac_nombres,
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	 	date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	 	date_part('day',age(now()::date, pac_fc_nac)) as edad_dias
		FROM pacientes 
		LEFT JOIN comunas USING (ciud_id)
		LEFT JOIN prevision USING (prev_id)
		WHERE pac_id=".$in['pac_id']);

	
	$lista_id=$in['lista_id']*1;
	
	$caso_id=$in['caso_id']*1;
	
	$ca=cargar_registro("SELECT * FROM lista_dinamica_caso WHERE caso_id=$caso_id", true);
	
	$li=cargar_registro("SELECT * FROM lista_dinamica WHERE lista_id=$lista_id");	

	$m=cargar_registro("SELECT * FROM monitoreo_ges 
	LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
	LEFT JOIN patologias_auge USING (pst_patologia_interna)
	WHERE mon_id=".$in['mon_id'], true);
	
	$obs=cargar_registro("SELECT * FROM monitoreo_ges_registro WHERE mon_id=".$in['mon_id']);
	
?>

<html>

<title>Registro: <?php echo htmlentities($li['lista_nombre']); ?></title>

<?php cabecera_popup('..'); ?>

<script>

function guardar_registro() {
	
	var myAjax=new Ajax.Request(
		'sql_instancia.php',
		{
			method:'post',
			parameters:$('reg').serialize(),
			onComplete:function(r) {
				
				alert('Registro guardado exitosamente.');
				window.close();
				
			}
		}
	);
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content' style='font-weight:bold;'>
<img src='../iconos/table_go.png' />
<?php echo htmlentities($li['lista_nombre']); ?>
</div>

<table style='width:100%;font-size:14px;'>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>R.U.T.:</td>
<td class='tabla_fila' style='font-size:14px;'><b><?php echo $pac['pac_rut']; ?></b> [<i>Ficha:</i> <b><u><?php echo $pac['pac_ficha']; ?></u></b>]</td>

<!--- <img src='../../iconos/magnifier.png' onClick='abrir_ficha(<?php echo $pac['pac_id']*1; ?>);' style='cursor:pointer;' /> -->
<td class='tabla_fila2' style='width:15%;text-align:right;'>Edad:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['edad_anios'].' a '.$pac['edad_meses'].' m '.$pac['edad_dias'].' d '); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre GIS:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim(($pac['pac_nombres']." ".$pac['pac_appat']." ".$pac['pac_apmat'])); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Nac./Def.:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_fc_nac'].' --- '.$pac['pac_fc_def']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre Reg. Civil:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila' id='nombre_regcivil'></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Reg. Civil Nac./Def.:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila' id='fechas_regcivil'></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Direcci&oacute;n:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_direccion']); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Ciudad:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['ciud_desc']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Tel&eacute;fono Fijo:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_fono']); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Tel&eacute;fono Celular:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_celular']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>email:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_mail']); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Previsi&oacute;n (Cert. FONASA):</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['prev_desc']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Patolog&iacute;a:</td>
<td class='tabla_fila'><i><?php echo $m['mon_patologia']; ?></i></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Garant&iacute;a:</td>
<td class='tabla_fila'><i><?php echo $m['mon_garantia']; ?></i></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Inicio:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_inicio']; ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha L&iacute;mite:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_limite']; ?></td>
</tr>
<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Observaciones:</td>
<td class='tabla_fila' colspan=3><?php echo $obs['monr_observaciones']; ?></td>
</tr>

</table>



<form id='reg' name='reg' onSubmit='return false;'>

<input type='hidden' id='in_id' name='in_id' value='<?php echo $in_id; ?>' />

<div class='sub-content'>
<table style='width:100%;'>

<?php 
	
	if($li['lista_campos_formulario']!='') {
	
		$campos=explode('|', $li['lista_campos_formulario']);
		$valores=explode('|', $in['in_valor']);
		
		for($i=0;$i<sizeof($campos);$i++) {
		
			if(strstr($campos[$i],'>>>')) {
				$cmp=explode('>>>',$campos[$i]);
				$nombre=htmlentities($cmp[0]); $tipo=$cmp[1]*1;
			} else {
				$cmp=$campos[$i]; $tipo=2;
			}
			
			print("<tr>
				<td style='text-align:right;width:200px;white-space:nowrap;' valign='top' class='tabla_fila2'>$nombre :</td>
				<td class='tabla_fila'>");
			
			if($tipo==0) {

				if(isset($valores[$i])) 
					$vact=($valores[$i]=='true')?'CHECKED':'';
				else 
					$vact='';

				print("<input type='checkbox' id='campo_$i' name='campo_$i' $vact />");	

			} elseif($tipo==1) {

				if(isset($valores[$i])) 
					$vact=($valores[$i]=='true')?'CHECKED':'';
				else 
					$vact='CHECKED';

				print("<input type='checkbox' id='campo_$i' name='campo_$i' $vact />");
								
			} elseif($tipo==5) {
			
				$opts=explode('//', $cmp[2]);
							
				if(isset($valores[$i])) 
					$vact=$valores[$i];
				else 
					$vact='';

				print("<select id='campo_$i' name='campo_$i'>");
				
				for($k=0;$k<sizeof($opts);$k++) {
					
					$opts[$k]=trim($opts[$k]);
					
					if($vact==$opts[$k]) $sel='SELECTED'; else $sel='';
					
					print("<option value='".$opts[$k]."' $sel>".$opts[$k]."</option>");	
				}			
				
				print("</select>");		
				
			} elseif($tipo==10) {

				if(isset($valores[$i])) 
					$vact=$valores[$i];
				else 
					$vact='';
				
				print("<textarea id='campo_$i' name='campo_$i' style='width:100%;height:70px;'>$vact</textarea>");
								
			} else {

				if(isset($valores[$i])) 
					$vact=$valores[$i];
				else 
					$vact='';
				
				print("<input type='text' id='campo_$i' name='campo_$i' value='$vact' />");
								
			}	
			
			print("</td></tr>");	
			
		}	
	
	}
	
?>

<tr>
<td style='text-align:right;width:200px;white-space:nowrap;' valign='top' class='tabla_fila2'>Comentarios :</td>
<td class='tabla_fila'>
<textarea id='in_comentarios' name='in_comentarios' style='width:100%;height:70px;'><?php echo $in['in_comentarios']; ?></textarea>
</td></tr>

</table>
</div>

<center>
<input type='button' id='guardar_reg' name='guardar_reg' 
onClick='guardar_registro();' value='-- Guardar Registro... --' />
</center>

</form>

</body>

</html>
