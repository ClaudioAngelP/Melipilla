<?php 

	require_once('../../conectar_db.php');
	
	$hosp_id=$_GET['hosp_id']*1;
	
	$r=cargar_registros_obj("SELECT * FROM hospitalizacion 
											JOIN pacientes ON pac_id=hosp_pac_id
											LEFT JOIN doctores ON doc_id=hosp_doc_id
											LEFT JOIN comunas USING (ciud_id)
											WHERE hosp_id=".$hosp_id);
	
	$pac_id=$r[0]['hosp_pac_id']*1;
											
	$r5=cargar_registros_obj("SELECT * FROM hospitalizacion 
											JOIN pacientes ON pac_id=hosp_pac_id
											LEFT JOIN doctores ON doc_id=hosp_doc_id
											LEFT JOIN comunas USING (ciud_id)
											WHERE hosp_id=".$hosp_id);										
												
											
	$uval=pg_query("SELECT *, hreg_fecha::date  AS fecha, hreg_fecha::time AS hora FROM hospitalizacion_registro 
	JOIN hospitalizacion_estado USING (hest_id)
	JOIN hospitalizacion_condicion USING (hcon_id)	
	WHERE hosp_id = $hosp_id ORDER BY hreg_fecha DESC LIMIT 1;");
	
	$r2=pg_query("
	
	SELECT *,
	t1.cama_tipo AS origen_tipo,
	c1.tcama_tipo AS origen_clase,
	c1.tcama_num_ini AS origen_ini, 
	t2.cama_tipo AS destino_tipo,
	c2.tcama_tipo AS destino_clase, 
	c2.tcama_num_ini AS destino_ini,
	
	(p1.ptras_fecha::date - COALESCE(
		(SELECT ptras_fecha 
		FROM paciente_traslado AS p2 
		WHERE hosp_id=$hosp_id AND 
		p2.ptras_fecha<=p1.ptras_fecha AND 
		p2.ptras_id<p1.ptras_id
		ORDER BY ptras_fecha DESC LIMIT 1),
		
		(SELECT hosp_fecha_hospitalizacion FROM hospitalizacion WHERE hosp_id=$hosp_id),

		(SELECT hosp_fecha_ing FROM hospitalizacion WHERE hosp_id=$hosp_id)

	)::date) AS dias_hosp
	
	FROM paciente_traslado AS p1
					LEFT JOIN tipo_camas AS t1 ON
					t1.cama_num_ini<=ptras_cama_origen AND t1.cama_num_fin>=ptras_cama_origen
					LEFT JOIN clasifica_camas AS c1 ON 
				   c1.tcama_num_ini<=ptras_cama_origen AND c1.tcama_num_fin>=ptras_cama_origen					
					LEFT JOIN tipo_camas AS t2 ON
					t2.cama_num_ini<=ptras_cama_destino AND t2.cama_num_fin>=ptras_cama_destino
					LEFT JOIN clasifica_camas AS c2 ON 
				   c2.tcama_num_ini<=ptras_cama_destino AND c2.tcama_num_fin>=ptras_cama_destino					
					WHERE hosp_id = $hosp_id	
					
	ORDER BY ptras_fecha DESC		
	");
	
	$r3=pg_query(" 
	
			SELECT * 
			FROM hospitalizacion_observaciones
			JOIN funcionario ON hospo_func_id=func_id
			WHERE hosp_id = $hosp_id ORDER BY hospo_fecha DESC LIMIT 1;
	
	
	");
	
	$r4=pg_query(" 
	
			SELECT * 
			FROM hospitalizacion_necesidades
			JOIN funcionario ON hospn_func_id=func_id
			WHERE hosp_id = $hosp_id ORDER BY hospn_fecha DESC LIMIT 1;
	");
	
	
	$l=cargar_registros_obj("
			SELECT *, 
			hosp_fecha_ing::date AS hosp_fecha_ing,
			hosp_fecha_ing::time AS hosp_hora_ing,
			hosp_fecha_egr::date 
			FROM hospitalizacion
			WHERE hosp_solicitud	
			");					
?>

<html>
<title>Historial de Hospitalizaci&oacute;n</title>

<?php cabecera_popup('../..'); ?>

<script>

var cmb=0;

function ver_historial() {
	
	if(cmb==0) {
		$('tab_prestaciones_content').show();
		$('hosp').hide();
		$('cambiar_hist').value=('-- Visualizar Historial de Hospitalizaci&oacute;n Actual --'.unescapeHTML());
		cmb=1;
	} else {
		$('tab_prestaciones_content').hide();
		$('hosp').show();
		$('cambiar_hist').value='-- Visualizar Historial de Prestaciones/Medicamentos --';		
		cmb=0;
	}
	
}

	abrir_presta = function(id) {

		inter_ficha = window.open('../../prestaciones/visualizar_prestacion.php?presta_id='+id,
		'inter_ficha', 'left='+(screen.width-500)+',top='+(screen.height-470)+',width=480,height=400,status=0,scrollbars=1');

		inter_ficha.focus();

	}

	var myAjax=new Ajax.Updater(
	'tab_prestaciones_content',
	'listar_prestaciones.php',
	{
				method:'post',
				parameters:'pac_id=<?php echo $pac_id; ?>'
	}		
	);	


</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/script_edit.png' />
<b>Historial de Hospitalizaci&oacute;n</b>
</div>

<center><input type='button' id='cambiar_hist' onClick='ver_historial();' value='-- Visualizar Historial de Prestaciones/Medicamentos --' /></center>

<div class='sub-content' id='hosp'>

<table width="956">
<tr>
<td width="100" style='text-align:right;width:100px;' class='tabla_fila2'><b>R.U.T.:</b></td>
<td width="165" style='text-align:left;width:300px;'><?php echo $r[0]['pac_rut']; ?></span></td>
<td style='text-align:right;' class='tabla_fila2'><b>Ingreso:</b></td><td style='text-align:center;'><?php echo substr($r[0]['hosp_fecha_ing'],0,16); ?></td>
<td width="357">&nbsp;</td>
</tr>
<tr>
  <td style='text-align:right;' class='tabla_fila2'><b>Nombre:</b></td>
<td style='text-align:left;'><?php echo $r[0]['pac_nombres'].' '.$r[0]['pac_appat'].' '.$r[0]['pac_apmat']; ?></span></td>
<td style='text-align:right;' class='tabla_fila2'><b>Hospitalizaci&oacute;n:</b></td><td style='text-align:center;'><?php echo substr($r[0]['hosp_fecha_hospitalizacion'],0,16); ?></td>
</tr>
<tr>
  <td style='text-align:right;' class='tabla_fila2'><b>R.U.T. M&eacute;dico:</b>  
  <td style='text-align:left;'><?php echo $r[0]['doc_rut']; ?>
  <td>&nbsp;</td>
  <td>&nbsp;</td>
</tr>
<tr>
<td style='text-align:right;' class='tabla_fila2'><b>M&eacute;dico:</b></td>
<td style='text-align:left;'><?php echo trim($r[0]['doc_paterno'].' '.$r[0]['doc_materno'].' '.$r[0]['doc_nombres']); ?></td>
<td style='text-align:right;' class='tabla_fila2'><b>Egreso:</b><td style='text-align:center;'><?php echo substr ($r[0]['hosp_fecha_egr'],0,16);?></td>
</tr>


</table>

<table style='width:100%;'>
<tr class='tabla_header'>
<td style='text-align:center;font-weight:bold;font-size:13px;' valign='top' class='tabla_fila2'>Evoluci&oacute;n Categor&iacute;a Riesgo/Dependencia</td>
</tr>
<tr class='tabla_fila2'>
<td>
	<center><img style='cursor:pointer;' onClick='$("censo_completo").toggle();'
	src='variacion_crd.php?hosp_id=<?php echo $hosp_id; ?>&tipo=1&r=<?php echo microtime(false); ?>' style='border:1px solid black;' />
	
<div id='censo_completo' style='width:450px;height:150px;overflow:auto;display:none;'>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>Fecha/Hora</td>
		<td>Categor&iacute;a R-D</td>
		<td>Funcionario</td>
	</tr>

<?php 

	$censo=cargar_registros_obj("
				SELECT * FROM censo_diario 
				JOIN funcionario USING (func_id)
				WHERE hosp_id=$hosp_id ORDER BY censo_fecha DESC;");
	
	for($i=0;$i<sizeof($censo);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("<tr class='$clase'>
			<td style='text-align:center;'>".$censo[$i]['censo_fecha']."</td>
			<td style='text-align:center;font-weight:bold;'>".$censo[$i]['censo_diario']."</td>
			<td>".$censo[$i]['func_nombre']."</td>
		</tr>");
	}

?>

</table>

</div>	

</center>
	
</td>
</tr>
</table>

<table style='width:100%;'>
<tr class='tabla_header'><td colspan=3  style='font-weight:bold;font-size:13px;'>Evoluci&oacute;n de Estado/Condici&oacute;n</td></tr>
<tr class='tabla_header'>
<td style='width:25%;'>Fecha Estado y Cond.</td>
<td>Estado Paciente</td>
<td>Condici&oacute;n Paciente</td>
</tr>

<?php

	$c=0;

	while($rc=pg_fetch_assoc($uval)){
	
	$class=($c%2==0)?'tabla_fila':'tabla_fila2';
	
   print('<tr class="'.$class.'">
		<td style="text-align:center;">'.$rc['fecha'].' '.substr($rc['hora'],0,8).'</td>         
         <td>'.htmlentities($rc['hest_nombre']).'</td>
         <td>'.htmlentities($rc['hcon_nombre']).'</td>
   		</tr>');	
	
	$c++;	
	
	}
?>
</table>


	<table style='width:100%;'>
	<tr class='tabla_header'><td colspan=6 style='font-weight:bold;font-size:13px;'>Traslados del Paciente</td></tr>
	<tr class='tabla_header'>
	<td style='width:25%;'>Fecha Asig. Cama</td>
	<td><u>ORIGEN:</u><br /> Servicio / Sala</td>
	<td>Cama</td>
	<td><u>DESTINO:</u><br />Servicio / Sala</td>
	<td>Cama</td>
	<td>D&iacute;as Hosp.</td>  
	</tr>
	
<?php
		
	$tmpr=array();
	
	while($rt=pg_fetch_assoc($r2)){
		
		$tmpr=$rt;
		
		$class=($c%2==0)?'tabla_fila':'tabla_fila2';
			
		print('<tr class="'.$class.'">
			 <td style="text-align:center;">'.substr($rt['ptras_fecha'],0,19).'</td>
			 <td style="text-align:left;">'.$rt['origen_clase'].' - '.$rt['origen_tipo'].' </td>
			 <td style="text-align:right;">'.(($rt['ptras_cama_origen']*1-$rt['origen_ini']*1)+1).'</td>	         
			 <td style="text-align:left;">'.$rt['destino_clase'].' - '.$rt['destino_tipo'].' </td>
			 <td style="text-align:right;">'.(($rt['ptras_cama_destino']*1-$rt['destino_ini']*1)+1).'</td>	         
			 <td style="text-align:right;">'.$rt['dias_hosp'].'</td> 			 
			 </tr>');
			 
			 $c++;
			 
			  $clasetipo=$rt['origen_clase'].' - '.$rt['origen_tipo'];
			 $camaorigen=(($rt['ptras_cama_origen']*1-$rt['origen_ini']*1)+1);
	
	}
	
			print('<tr class="'.$class.'">
			 <td style="text-align:center;">'.substr($r[0]['hosp_fecha_hospitalizacion'],0,19).'</td>
			 <td style="text-align:left;">&nbsp;</td>
			 <td style="text-align:right;">&nbsp;</td>	         
			 <td style="text-align:left;">'.$clasetipo.' </td>
			 <td style="text-align:right;">'.$camaorigen.'</td>	         
			 <td style="text-align:right;">&nbsp;</td> 			 
			 </tr>');
	
?>

<!-- 
<td style="text-align:center;">'.$r['destino_tipo'].' - '.$r['destino_clase'].'</td>
<td style="text-align:center;">'.(($r['ptras_cama_destino']*1-$r['destino_ini']*1)+1).'</td>	         
  
 Impresion de destino camas
 -->

</table>
	<table style='width:100%;'>
	<tr class='tabla_header'><td colspan=2 style='font-weight:bold;font-size:13px;'>Historial de Observaciones</td></tr>
	<tr class='tabla_header'>
	<td style='width:25%;'>Fecha Obs.</td>
	<td style='width:80%;'>Observaciones</td>  
	</tr>
	
<?php

	$c=0;

	while($r=pg_fetch_assoc($r3)){
	
	$class=($c%2==0)?'tabla_fila':'tabla_fila2';	
	
	print('<tr class="'.$class.'">
			 <td style="text-align:center;">'.substr($r['hospo_fecha'],0,19).'</td>	         
			 <td style="text-align:left;">'.htmlentities($r['hospo_observacion']).'<br/>(<i>'.htmlentities($r['func_nombre']).'</i>)</td> 			 
			 </tr>');
	
	$c++;
	
	}


?>

<table style='width:100%;'>
	<tr class='tabla_header'><td colspan=2 style='font-weight:bold;font-size:13px;'>Historial de Necesidades</td></tr>
	<tr class='tabla_header'>
	<td style='width:25%;'>Fecha Nec.</td>
	<td style='width:80%;'>Necesidades</td>  
	</tr>
	
<?php

	$c=0;

	while($r=pg_fetch_assoc($r4)){
	
	$class=($c%2==0)?'tabla_fila':'tabla_fila2';	
	
	print('<tr class="'.$class.'">
			 <td style="text-align:center;">'.substr($r['hospn_fecha'],0,19).'</td>	         
			 <td style="text-align:left;">'.htmlentities($r['hospn_observacion']).'<br/>(<i>'.htmlentities($r['func_nombre']).'</i>)</td> 			 
			 </tr>');
	
	$c++;
	
	}


?>	
	
</table>	

	
</table>	

</div>

<div class='sub-content' id='tab_prestaciones_content' style='display:none;'>

</div>

</body>
</html>

