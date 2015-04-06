<?php 

	require_once('../conectar_db.php');
	
	$monr_id=$_GET['monr_id']*1;
	
	$m=cargar_registro("SELECT *, 
						 (CURRENT_TIMESTAMP::date-monr_fecha::date)::integer AS dias,
						 (SELECT pac_id FROM pacientes WHERE pac_rut=mon_rut LIMIT 1) AS pac_id
						 FROM monitoreo_ges_registro 
						 JOIN monitoreo_ges USING (mon_id)
						 LEFT JOIN patologias_sigges_traductor ON mon_pst_id=pst_id
						 LEFT JOIN patologias_auge USING (pst_patologia_interna)
						 WHERE monr_id=$monr_id", true);

	if($m['pac_id']!='')
	$pac=cargar_registro("
		SELECT *,
		UPPER(pac_appat) as pac_appat, UPPER(pac_apmat) AS pac_apmat, UPPER(pac_nombres) AS pac_nombres,
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
	 	date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
	 	date_part('day',age(now()::date, pac_fc_nac)) as edad_dias
		FROM pacientes 
		LEFT JOIN comunas USING (ciud_id)
		LEFT JOIN prevision USING (prev_id)
		WHERE pac_id=".$m['pac_id']);

	
	$lista_id=$m['monr_subclase'];
	
	$li=cargar_registro("SELECT * FROM lista_dinamica_bandejas WHERE codigo_bandeja='$lista_id';");	

?>

<html>

<title>Registro: <?php echo htmlentities($li['nombre_bandeja']); ?></title>

<?php cabecera_popup('..'); ?>

<script>

function cargar_historial() {
	
	var myAjax=new Ajax.Updater(
		'historial_caso',
		'visualizar_caso.php',
		{
			method:'get',
			parameters:'mon_id=<?php echo $m['mon_id']; ?>&modo=1'
		}
	);
	
}

function toggle_historial() {
	
	var val=$('boton_historial').value;
	
	if(val=='Ocultar...') {
		$('boton_historial').value='Mostrar...';
		$('historial_caso').hide();
	} else {		
		$('boton_historial').value='Ocultar...';
		$('historial_caso').show();
	}
	
}

function chequear_fechas() {

	var chequear=true;

	$$('input[class="ld_fechas"]').each(function(element) {
		
								if(trim(element.value)=='') {
									element.style.background='';
									element.value='';
								} else {
									
									if(!validacion_fecha(element) ) {
										alert("Fecha ingresada no es v&aacute;lida.".unescapeHTML());
										$(element).focus();
										chequear=false;
									}
									
								}
								
								});

	return chequear;

}

	ver_registro_civil=function() {
	
		$('nombre_regcivil').innerHTML="<img src='../imagenes/ajax-loader1.gif'> Cargando...";
		$('fechas_regcivil').innerHTML="<img src='../imagenes/ajax-loader1.gif'> Cargando...";
		
		var myAjax=new Ajax.Request(
			'registro_civil.php',
			{
				method:'get',
				parameters: 'rut=<?php echo $m['mon_rut']; ?>',
				onComplete:function(r) {
					
					var datos=r.responseText.evalJSON(true);
					
					datos[4]=datos[4].replace(/-/gi,'/');
					datos[5]=datos[5].replace(/-/gi,'/');
					datos[5]=datos[5].replace('//','');
					
					var nombre_regcivil=datos[0]+' '+datos[1]+' '+datos[2];
					nombre_regcivil = nombre_regcivil.replace(/@/gi, '&Ntilde;');
					
					$('nombre_regcivil').innerHTML=nombre_regcivil;
					$('fechas_regcivil').innerHTML=datos[4]+' --- '+datos[5];
									
				}
			}
		);
	
	}


function guardar_registro() {

	if(!chequear_fechas()) return;
	
	var myAjax=new Ajax.Request(
		'sql_instancia.php',
		{
			method:'post',
			parameters:$('reg').serialize(),
			onComplete:function(r) {
				
				alert("Registro modificado exitosamente.");
				var fn=window.opener.cargar_lista.bind(window.opener);
				fn();
				window.close();
				
			}
		}
	);
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content' style='font-weight:bold;'>
<img src='../iconos/table_go.png' />
<?php echo htmlentities($li['nombre_bandeja']); ?>
</div>

<table style='width:100%;font-size:14px;'>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>R.U.T.:</td>
<td class='tabla_fila' style='font-size:14px;'><b><?php echo $m['mon_rut']; ?></b> [<i>Ficha:</i> <b><u><?php echo $pac['pac_ficha']; ?></u></b>]</td>

<!--- <img src='../../iconos/magnifier.png' onClick='abrir_ficha(<?php echo $pac['pac_id']*1; ?>);' style='cursor:pointer;' /> -->
<td class='tabla_fila2' style='width:15%;text-align:right;'>Edad:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['edad_anios'].' a '.$pac['edad_meses'].' m '.$pac['edad_dias'].' d '); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($m['mon_nombre']); ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Nac./Def.:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila'><? echo trim($pac['pac_fc_nac'].' --- '.$pac['pac_fc_def']); ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre Reg. Civil:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila' id='nombre_regcivil'>
<input type='button' id='' name='' value='[Consultar Registro Civil...]' onClick='ver_registro_civil();' />
</td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Reg. Civil Nac./Def.:</td>
<td class='tabla_fila' style='font-weight:bold;' class='tabla_fila' id='fechas_regcivil'>
<input type='button' id='' name='' value='[Consultar Registro Civil...]' onClick='ver_registro_civil();' />
</td>
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
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Inicio:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_inicio']; ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha L&iacute;mite:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_limite']; ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Patolog&iacute;a:</td>
<td class='tabla_fila' colspan=3 style='font-weight:bold;'><i><?php echo $m['mon_patologia']; ?></i></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Garant&iacute;a:</td>
<td class='tabla_fila' colspan=3 style='font-weight:bold;'><i><?php echo $m['mon_garantia']; ?></i></td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Observaciones Monitoreo:</td>
<td  class="tabla_fila" style='font-size:16px;text-align:justify;' colspan=3><b>[<?php echo substr($m['monr_fecha'],0,10); ?>]</b> <?php echo $m['monr_observaciones']; ?></td>
</tr>


</table>

<?php 

	if($li['codigo_bandeja']=='R' OR $li['codigo_bandeja']=='S' OR $li['codigo_bandeja']=='T' OR $li['codigo_bandeja']=='U') {
	
	  $lista = cargar_registros_obj("
	  SELECT 
		nomina_detalle.nomd_id, nom_fecha::date, nomd_hora, doc_rut, doc_paterno, doc_materno, doc_nombres, 
		COALESCE(diag_desc, cancela_desc) AS diag_desc, nomd_diag_cod,
		esp_desc, nomd_tipo, CASE WHEN nom_fecha>=CURRENT_DATE THEN 'P' ELSE 'A' END AS estado,
		esp_lugar, COALESCE(esp_nombre_especialidad, esp_desc) AS esp_nombre_especialidad
	  FROM nomina_detalle
	  JOIN nomina USING (nom_id)
	  JOIN pacientes USING (pac_id)
	  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	  LEFT JOIN doctores ON nom_doc_id=doc_id
	  LEFT JOIN especialidades ON nom_esp_id=esp_id 
	  LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
	  WHERE pac_rut='".$m['mon_rut']."' AND nomd_diag_cod NOT IN ('T','X') AND nom_fecha>=CURRENT_DATE
	  
	  ORDER BY nomina.nom_fecha ASC, nomd_hora 
	  ", true);
		
?>

<table style='width:100%;font-size:16px;'>
	<tr class='tabla_header'>
		<td colspan=5>Consultas Pendientes</td>
	</tr>
	<tr class='tabla_header'>
		<td>Especialidad</td>
		<td>Lugar</td>
		<td>Profesional</td>
		<td>Fecha</td>
		<td>Hora</td>
	</tr>
	
<?php 

	for($i=0;$i<sizeof($lista);$i++) {
		
    ($i%2==0) ? $clase='#dddddd' : $clase='#eeeeee';

	$lista[$i]['esp_desc']=str_replace('-HDGF', '', $lista[$i]['esp_desc']);

	$prof=($lista[$i]['doc_nombres'].' '.$lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno']);

	$prof=str_replace('(AGEN)', '', $prof);

	if($lista[$i]['nomd_hora']=='00:00:00') $lista[$i]['nomd_hora']='08:30:00';


    print("
    <tr style='background-color:$clase;'>
	 <td style='text-align:left;font-weight:bold;'>".($lista[$i]['esp_nombre_especialidad'])."</td>   
	 <td style='text-align:left;font-weight:bold;'>".($lista[$i]['esp_lugar'])."</td>   
	 <td style='text-align:left;font-weight:bold;'>".($prof)."</td>
	 <td style='font-weight:bold;text-align:center;font-size:16px;'>".$lista[$i]['nom_fecha']."</td>    
	 <td style='font-weight:bold;text-align:center;font-size:16px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>    
	 </tr>
    ");    
	   
  }

?>	
	
</table>

<?php
		
	}

?>

<div class='sub-content'>
<table>
	<tr>
		<td style='width:20px;'> 
<img src='../iconos/clock.png' /></td><td style='width:200px;font-size:16px;'>
Historial del Caso </td><td>
<input type='button' id='boton_historial' name='boton_historial' value='Ocultar...' onClick='toggle_historial();' />
	</td></tr>
</table>

</div>
<div class='sub-content2' style='height:200px;overflow:auto;' id='historial_caso'>

</div>


<form id='reg' name='reg' onSubmit='return false;'>

<input type='hidden' id='monr_id' name='monr_id' value='<?php echo $monr_id; ?>' />

<div class='sub-content'>
<table style='width:100%;'>

<?php 
	
	if($li['lista_campos_tabla']!='') {
	
		$campos=explode('|', $li['lista_campos_tabla']);
		$valores=explode('|', $m['monr_valor']);
		
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
								
			} elseif($tipo==3) {

                                print("<input type='text' class='ld_fechas' style='text-align:center;' size=10 onBlur='validacion_fecha(this);' id='campo_$i' name='campo_$i' value='".$valores[$i]."' />");

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
<textarea id='in_comentarios' name='in_comentarios' style='width:100%;height:70px;'></textarea>
</td></tr>


<?php 

			if($m['monr_subclase']!='')
				$destinos=pg_query("
					SELECT * FROM (
						SELECT DISTINCT codigo_bandeja_n, id_condicion_n FROM (
							SELECT DISTINCT codigo_bandeja_n, id_condicion_n
							FROM lista_dinamica_proceso 
							WHERE codigo_bandeja='".$m['monr_subclase']."' AND (id_condicion=0 OR id_condicion=".$m['monr_clase'].")
						) AS foo2
					) AS foo
					LEFT JOIN lista_dinamica_bandejas AS ldb ON ldb.codigo_bandeja=foo.codigo_bandeja_n
					LEFT JOIN lista_dinamica_condiciones AS ldc ON ldc.id_condicion=foo.id_condicion_n
					ORDER BY id_condicion_n;
				");
			else
				$destinos=pg_query("
					SELECT * FROM (
						SELECT DISTINCT codigo_bandeja AS codigo_bandeja_n, id_condicion_n FROM (
							SELECT DISTINCT codigo_bandeja, 0 AS id_condicion_n
							FROM lista_dinamica_proceso 
							WHERE id_condicion='".$m['monr_clase']."'
						) AS foo2
					) AS foo
					LEFT JOIN lista_dinamica_bandejas AS ldb ON ldb.codigo_bandeja=foo.codigo_bandeja_n
					LEFT JOIN lista_dinamica_condiciones AS ldc ON ldc.id_condicion=foo.id_condicion_n
					ORDER BY id_condicion_n;
				");
			
			$lista_html='';
			
			while($dest=pg_fetch_assoc($destinos)) {
				
				if($dest['id_condicion_n']=='0' AND $dest['codigo_bandeja_n']=='') continue;

				$lista_html.='<option value="'.$dest['id_condicion_n'].'|'.$dest['codigo_bandeja_n'].'" style="color:blue;">['.htmlentities($dest['nombre_condicion']).'] &gt; '.htmlentities($dest['nombre_bandeja']).' &gt;&gt;</option>';						
				
			} 


?>

<tr>
<td style='text-align:right;width:200px;white-space:nowrap;' valign='top' class='tabla_fila2'>Estado :</td>
<td class='tabla_fila'>
<select id='sel_estado' name='sel_estado'>
<option value='0' SELECTED>(Sin Cambios...)</option>
<?php echo $lista_html; ?>
</select>
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

<script> cargar_historial(); </script>
