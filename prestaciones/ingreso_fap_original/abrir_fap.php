<?php

	require_once('../../conectar_db.php');
	
	$fap_id=$_POST['fap_id']*1;
	$tipof=$_POST['tipo']*1;
	
	if($tipof!=5) {

		// CARGA FAP de URGENCIAS

		$fap=cargar_registros_obj("
			SELECT *,
			fap.fap_comuna AS ciud_id1,
			pacientes.ciud_id AS ciud_id2,
			COALESCE(p2.prev_id, p1.prev_id) AS prev_id,
			COALESCE(p2.prev_desc, p1.prev_desc) AS prev_desc,
			date_part('year',age(fap_fecha::date, pac_fc_nac)) as edad_anios,  
			date_part('month',age(fap_fecha::date, pac_fc_nac)) as edad_meses,  
			date_part('day',age(fap_fecha::date, pac_fc_nac)) as edad_dias,
			COALESCE(fap_hora_atencion, '00:00') AS fap_hora_atencion,
			COALESCE(fap_hora_alta, '00:00') AS fap_hora_alta,
			COALESCE(fap_hora_nsp, '00:00') AS fap_hora_nsp
			FROM fap 
			JOIN pacientes ON pac_id=fap_pac_id
			LEFT JOIN prevision AS p1 ON p1.prev_id=pacientes.prev_id		
			LEFT JOIN prevision AS p2 ON p2.prev_id=fap.fap_prevision		
			LEFT JOIN doctores ON fap_doc_id=doc_id
			LEFT JOIN diagnosticos ON fap_diag_cod=diag_cod	
			WHERE fap_id=$fap_id	
		");

	} else {

		// CARGA FAP DE PABELL�N

		$fap=cargar_registros_obj("
			SELECT 
			fap_pabellon.*, fappab_pabellones.*,
			th.*, pacientes.*, 
			COALESCE(p2.prev_id, p1.prev_id) AS prev_id,
			COALESCE(p2.prev_desc, p1.prev_desc) AS prev_desc,
			ta1.fapta_id AS fapta_id1,
			ta1.fapta_desc AS fapta_desc1,
			ta2.fapta_id AS fapta_id2,
			ta2.fapta_desc AS fapta_desc2,
			d0.diag_desc AS diag_desc, 		
			d1.diag_desc AS diag_desc_1, 		
			d2.diag_desc AS diag_desc_2, 		
			d3.diag_desc AS diag_desc_3,
			c1.centro_nombre AS centro_nombre,
			c2.centro_nombre AS centro_nombre2,
			date_part('year',age(now()::date, pac_fc_nac)) as edad_anios,  
			date_part('month',age(now()::date, pac_fc_nac)) as edad_meses,  
			date_part('day',age(now()::date, pac_fc_nac)) as edad_dias,
			'' AS edad 		 
			FROM fap_pabellon 
			LEFT JOIN pacientes USING (pac_id)		
			LEFT JOIN prevision AS p1 ON p1.prev_id=pacientes.prev_id		
			LEFT JOIN prevision AS p2 ON p2.prev_id=fap_pabellon.prev_id		
			LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id		
			LEFT JOIN fappab_tipo_herida AS th USING (fapth_id)		
			LEFT JOIN fappab_tipo_anestesia AS ta1 ON fapta_id1=ta1.fapta_id		
			LEFT JOIN fappab_tipo_anestesia AS ta2 ON fapta_id2=ta2.fapta_id		
			LEFT JOIN diagnosticos AS d0 ON fap_diag_cod=d0.diag_cod
			LEFT JOIN diagnosticos AS d1 ON fap_diag_cod_1=d1.diag_cod
			LEFT JOIN diagnosticos AS d2 ON fap_diag_cod_2=d2.diag_cod
			LEFT JOIN diagnosticos AS d3 ON fap_diag_cod_3=d3.diag_cod
			LEFT JOIN centro_costo AS c1 ON fap_pabellon.centro_ruta=c1.centro_ruta
			LEFT JOIN centro_costo AS c2 ON fap_pabellon.centro_ruta2=c2.centro_ruta
			WHERE fap_id=$fap_id	
		");
		
		$equipos=cargar_registros_obj("
			SELECT fap_equipo_quirurgico.*,
	
				p1.pp_id AS pp1_id,	
				p1.pp_rut AS pp1_rut,
				p1.pp_paterno || ' ' || p1.pp_materno || ' ' || p1.pp_nombres AS pp1_nombre,
				cir1_t AS pp1_turno,
	
				p2.pp_id AS pp2_id,	
				p2.pp_rut AS pp2_rut,
				p2.pp_paterno || ' ' || p2.pp_materno || ' ' || p2.pp_nombres AS pp2_nombre,
				cir2_t AS pp2_turno,
	
				p3.pp_id AS pp3_id,	
				p3.pp_rut AS pp3_rut,
				p3.pp_paterno || ' ' || p3.pp_materno || ' ' || p3.pp_nombres AS pp3_nombre,
				cir3_t AS pp3_turno,
	
				p4.pp_id AS pp4_id,	
				p4.pp_rut AS pp4_rut,
				p4.pp_paterno || ' ' || p4.pp_materno || ' ' || p4.pp_nombres AS pp4_nombre,
				ane1_t AS pp4_turno,
	
				p5.pp_id AS pp5_id,	
				p5.pp_rut AS pp5_rut,
				p5.pp_paterno || ' ' || p5.pp_materno || ' ' || p5.pp_nombres AS pp5_nombre,
				ane2_t AS pp5_turno,
	
				p6.pp_id AS pp6_id,	
				p6.pp_rut AS pp6_rut,
				p6.pp_paterno || ' ' || p6.pp_materno || ' ' || p6.pp_nombres AS pp6_nombre,
	
				p7.pp_id AS pp7_id,	
				p7.pp_rut AS pp7_rut,
	
				p7.pp_paterno || ' ' || p7.pp_materno || ' ' || p7.pp_nombres AS pp7_nombre,
	
				p8.pp_id AS pp8_id,	
				p8.pp_rut AS pp8_rut,
				p8.pp_paterno || ' ' || p8.pp_materno || ' ' || p8.pp_nombres AS pp8_nombre,
	
				p9.pp_id AS pp9_id,	
				p9.pp_rut AS pp9_rut,
				p9.pp_paterno || ' ' || p9.pp_materno || ' ' || p9.pp_nombres AS pp9_nombre,
	
				p10.pp_id AS pp10_id,	
				p10.pp_rut AS pp10_rut,
				p10.pp_paterno || ' ' || p10.pp_materno || ' ' || p10.pp_nombres AS pp10_nombre,
	
				p11.pp_id AS pp11_id,	
				p11.pp_rut AS pp11_rut,
				p11.pp_paterno || ' ' || p11.pp_materno || ' ' || p11.pp_nombres AS pp11_nombre
				  
			FROM fap_equipo_quirurgico 
			LEFT JOIN personal_pabellon AS p1 ON p1.pp_id=cir1 		
			LEFT JOIN personal_pabellon AS p2 ON p2.pp_id=cir2 		
			LEFT JOIN personal_pabellon AS p3 ON p3.pp_id=cir3 		
			LEFT JOIN personal_pabellon AS p4 ON p4.pp_id=ane1 		
			LEFT JOIN personal_pabellon AS p5 ON p5.pp_id=ane2 		
			LEFT JOIN personal_pabellon AS p6 ON p6.pp_id=inst 		
			LEFT JOIN personal_pabellon AS p7 ON p7.pp_id=pab 		
			LEFT JOIN personal_pabellon AS p8 ON p8.pp_id=tecane 		
			LEFT JOIN personal_pabellon AS p9 ON p9.pp_id=tecperf 		
			LEFT JOIN personal_pabellon AS p10 ON p10.pp_id=tecrx		
			LEFT JOIN personal_pabellon AS p11 ON p11.pp_id=tecrecu 		
			WHERE fap_id=$fap_id	
			ORDER BY fapeq_num
			
		", true);		
		
		$nequipos=sizeof($equipos);
		if($nequipos==0) $nequipos=1;
		
		for($i=0;$i<sizeof($equipos);$i++) {
			$deq[$i+1]=array();
			for($j=0;$j<11;$j++) {
							
				if($equipos[$i]['pp'.($j+1).'_id']==null)
				 $equipos[$i]['pp'.($j+1).'_id']='';
				if($equipos[$i]['pp'.($j+1).'_rut']==null)
				 $equipos[$i]['pp'.($j+1).'_rut']='';
				if($equipos[$i]['pp'.($j+1).'_nombre']==null)
				 $equipos[$i]['pp'.($j+1).'_nombre']='';
				if($equipos[$i]['pp'.($j+1).'_turno']==null)
				 $equipos[$i]['pp'.($j+1).'_turno']=0;

				$deq[$i+1][$j]->id=$equipos[$i]['pp'.($j+1).'_id'];	
				$deq[$i+1][$j]->rut=$equipos[$i]['pp'.($j+1).'_rut'];	
				$deq[$i+1][$j]->nombre=$equipos[$i]['pp'.($j+1).'_nombre'];
				if($j<5)	
					$deq[$i+1][$j]->turno=$equipos[$i]['pp'.($j+1).'_turno'];
				else 
					$deq[$i+1][$j]->turno=0;						
			}
		}

	}
		
	$presta=cargar_registros_obj("
		SELECT * FROM fap_prestacion 
		LEFT JOIN codigos_prestacion ON fappr_codigo=codigo
		WHERE fap_id=$fap_id order by fappr_id asc
	", true);
	
	$prestaciones=array();	
	
	for($i=0;$i<sizeof($presta);$i++) {
	
		$n=sizeof($prestaciones);
		
		$prestaciones[$n]->codigo=$presta[$i]['fappr_codigo'];	
		$prestaciones[$n]->desc=$presta[$i]['glosa'];	
		$prestaciones[$n]->cantidad=$presta[$i]['fappr_cantidad'];	
		$prestaciones[$n]->fappr_tipo=$presta[$i]['fappr_tipo'];	
		
	}

if($tipof!=5) {

$tipohtml = desplegar_opciones("fap_tipo_consulta", 
	"faptc_id, faptc_desc",$fap[0]['fap_tipo_consulta'],'true','ORDER BY faptc_id');

$sexohtml = desplegar_opciones("sexo", 
	"sex_id, sex_desc",$fap[0]['sex_id'],'true','ORDER BY sex_id');

if($fap[0]['ciud_id1']!=-1) {
	$ciud_id=$fap[0]['ciud_id1'];
} else {
	$ciud_id=$fap[0]['ciud_id2'];	
}
	
$comunahtml = desplegar_opciones("comunas", 
	"ciud_id, ciud_desc",$ciud_id,'true','ORDER BY ciud_desc');
 
$previsionhtml = desplegar_opciones("prevision", 
	"prev_id, prev_desc",$fap[0]['prev_id'],'true','ORDER BY prev_id'); 
	
$destinohtml = desplegar_opciones("fap_destino", 
	"fapd_id, fapd_desc",$fap[0]['fap_destino'],'true','ORDER BY fapd_id'); 

$accidentehtml = desplegar_opciones("fap_accidente", 
	"fapa_id, fapa_desc",$fap[0]['fap_accidente'],'true','ORDER BY fapa_id'); 

$origenhtml = desplegar_opciones("fap_origen", 
	"fapo_id, fapo_desc",$fap[0]['fap_origen'],'true','ORDER BY fapo_id'); 

$atendidohtml = desplegar_opciones("fap_atendido", 
	"fapat_id, fapat_desc",$fap[0]['fap_atendido_por'],'true','ORDER BY fapat_id'); 

$geshtml = desplegar_opciones("fap_ges", 
	"fapg_id, fapg_desc",$fap[0]['fap_pat_id'],'true','ORDER BY fapg_id'); 

} else {

$pabellonhtml = desplegar_opciones("fappab_pabellones", 
	"fapp_id, fapp_desc",$fap[0]['fap_numpabellon'],'true','ORDER BY fapp_id'); 

$tipoheridahtml = desplegar_opciones("fappab_tipo_herida", 
	"fapth_id, '[' || fapth_id || '] ' || fapth_desc",$fap[0]['fapth_id'],'true','ORDER BY fapth_id'); 

$tipoanestesia1html = desplegar_opciones("fappab_tipo_anestesia", 
	"fapta_id, fapta_desc",$fap[0]['fapta_id1']*1,'true','ORDER BY fapta_id'); 

$tipoanestesia2html = desplegar_opciones("fappab_tipo_anestesia", 
	"fapta_id, fapta_desc",$fap[0]['fapta_id2']*1,'true','ORDER BY fapta_id'); 

}

function combo_prioriza($val) {
	
	$val=$val*1;	
	
	$html='<option value="0" '.($val==0?'SELECTED':'').'>S/P</option>';	
	$html.='<option value="1" '.($val==1?'SELECTED':'').'>1</option>';	
	$html.='<option value="2" '.($val==2?'SELECTED':'').'>2</option>';	
	$html.='<option value="3" '.($val==3?'SELECTED':'').'>3</option>';	
	$html.='<option value="4" '.($val==4?'SELECTED':'').'>4</option>';	
	$html.='<option value="5" '.($val==5?'SELECTED':'').'>5</option>';	

	return $html;
	
}

function combo_pronostico($val) {

	$val=$val*1;

	$html='<option value="0" '.($val==0?'SELECTED':'').'>Leve</option>';
	$html.='<option value="1" '.($val==1?'SELECTED':'').'>Mediana Gravedad</option>';
	$html.='<option value="2" '.($val==2?'SELECTED':'').'>Grave</option>';
	
	return $html;	
	
}

?>

<input type='hidden' id='fap_id' name='fap_id' value='<?php echo $fap_id; ?>' />
<input type='hidden' id='tipo' name='tipo' value='<?php echo $tipof; ?>' />
<input type='hidden' id='cambia_presta' name='cambia_presta' value='0' />

<table style='width:100%' cellpadding=0 cellspacing=0>
<tr>
<td style='width:50%;'>

<table style='width:100%;' cellpadding=1 cellspacing=1>

<!--
<tr>
<td class='tabla_fila2' style='text-align:right;'>FAP Tipo:</td>
<td class='tabla_fila' colspan=3><?php echo $tipof; ?></td>
</tr>
-->

<?php if($tipof!=5) { ?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>FAP Nro.:</td>
<td class='tabla_fila' colspan=3><b><?php echo $fap[0]['fap_fnumero']; ?></b></td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha de Nac.:</td>
<td class='tabla_fila'><?php echo $fap[0]['pac_fc_nac']; ?></td>
<td class='tabla_fila' colspan=2 id='pac_edad'>
Edad:<b>
<?php 
	
	if($fap[0]['edad_anios']*1>1) echo $fap[0]['edad_anios'].' a&ntilde;os ';
	elseif($fap[0]['edad_anios']*1==1) echo $fap[0]['edad_anios'].' a&ntilde;o ';

	if($fap[0]['edad_meses']*1>1) echo $fap[0]['edad_meses'].' meses ';
	elseif($fap[0]['edad_meses']*1==1) echo $fap[0]['edad_meses'].' mes ';

	if($fap[0]['edad_dias']*1>1) echo $fap[0]['edad_dias'].' d&iacute;as';
	elseif($fap[0]['edad_dias']*1==1) echo $fap[0]['edad_dias'].' d&iacute;a';
	
?> 
</b>
</td>
</tr>


<?php } else { ?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>FAP Nro.:</td>
<td class='tabla_fila' colspan=3><b><?php echo $fap[0]['fap_fnumero']; ?></b></td>
</tr>

<tr>
<td class='tabla_fila2' style='text-align:right;'>R.U.T.:</td>
<td class='tabla_fila'>
<input type='hidden' id='pac_id' name='pac_id' value='<?php echo $fap[0]['pac_id']; ?>' />
<input type='text' size=10 
id='pac_rut' name='pac_rut' value='<?php echo $fap[0]['pac_rut']; ?>' />
</td>
<td class='tabla_fila2'  style='text-align:right;'>Ficha Cl&iacute;nica:</td>
<td class='tabla_fila' style='text-align:center;font-weight:bold;' id='pac_ficha'>
<?php echo $fap[0]['pac_ficha']; ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Nombre:</td>
<td class='tabla_fila' colspan=3 style='font-size:12px;font-weight:bold;' id='pac_nombre'>
<?php echo htmlentities(trim($fap[0]['pac_nombres'].' '.$fap[0]['pac_appat'].' '.$fap[0]['pac_apmat'])); ?>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Fecha de Nac.:</td>
<td class='tabla_fila' id='pac_fc_nac'><?php echo $fap[0]['pac_fc_nac']; ?></td>
<td class='tabla_fila2' colspan=2 style='text-align:center;' id='pac_edad'>
Edad:<b>
<?php 
	
	if($fap[0]['edad_anios']*1>1) echo $fap[0]['edad_anios'].' a&ntilde;os ';
	elseif($fap[0]['edad_anios']*1==1) echo $fap[0]['edad_anios'].' a&ntilde;o ';

	if($fap[0]['edad_meses']*1>1) echo $fap[0]['edad_meses'].' meses ';
	elseif($fap[0]['edad_meses']*1==1) echo $fap[0]['edad_meses'].' mes ';

	if($fap[0]['edad_dias']*1>1) echo $fap[0]['edad_dias'].' d&iacute;as';
	elseif($fap[0]['edad_dias']*1==1) echo $fap[0]['edad_dias'].' d&iacute;a';
	
?> 
</b>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Previsi&oacute;n:</td>
<td class='tabla_fila' id='prev_desc'>
<?php echo $fap[0]['prev_desc']; ?>
</td>
<td class='tabla_fila2'  style='text-align:right;'>N&uacute;m. Pab.:</td>
<td class='tabla_fila'>
<select id='fap_numpabellon' name='fap_numpabellon' style='font-size:10px;'>
<?php echo $pabellonhtml; ?>
</select>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Tipo Atenci&oacute;n (I.Q.):</td>
<td class='tabla_fila'>
<select id='fap_tipopab' name='fap_tipopab'
onChange='
if(this.value==0) {
	$("sel_tabla").style.display="";
} else {
	$("sel_tabla").style.display="none";
}'>
<option value=0 <?php if($fap[0]['fap_tipopab']*1==0) echo 'SELECTED'; ?>>Normal</option>
<option value=1 <?php if($fap[0]['fap_tipopab']*1==1) echo 'SELECTED'; ?>>Urgencia Institucional</option>
<option value=2 <?php if($fap[0]['fap_tipopab']*1==2) echo 'SELECTED'; ?>>Ext. Horaria</option>
<option value=3 <?php if($fap[0]['fap_tipopab']*1==3) echo 'SELECTED'; ?>>Privado (Electivo)</option>
<option value=4 <?php if($fap[0]['fap_tipopab']*1==4) echo 'SELECTED'; ?>>Privado (Urgencia)</option>
</select>
</td>

<td class='tabla_fila2' style='text-align:right;'>Modo de Atenci&oacute;n:</td>
<td class='tabla_fila'>
<select id='fap_subtipopab' name='fap_subtipopab'>
<option value='0' <?php if($fap[0]['fap_subtipopab']*1==0) echo 'SELECTED'; ?>>Ambulatorio</option>
<option value='1' <?php if($fap[0]['fap_subtipopab']*1==1) echo 'SELECTED'; ?>>Hospitalizado</option>
</select>
</td>
</tr>

<!---<tr id='sel_tabla' <?php if($fap[0]['fap_tipopab']*1==1) echo "style='display:none;'"; ?>>
<td class='tabla_fila2'  style='text-align:right;'>Tabla:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_tablapab' name='fap_tablapab'>
<option value=0 <?php if($fap[0]['fap_tablapab']*1==0) echo 'SELECTED'; ?>>Programada</option>
<option value=1 <?php if($fap[0]['fap_tablapab']*1==1) echo 'SELECTED'; ?>>Condicional</option>
</select>
</td>
</tr>--->

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Serv. de Or&iacute;gen:</td>
<td class='tabla_fila' colspan=3>
<input type='hidden' id='centro_ruta' name='centro_ruta' value='<?php echo $fap[0]['centro_ruta']; ?>' />
<input type='text' id='centro_nombre' name='centro_nombre' value='<?php echo $fap[0]['centro_nombre']; ?>' />
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Serv. de Destino:</td>
<td class='tabla_fila' colspan=3>
<input type='hidden' id='centro_ruta2' name='centro_ruta2' value='<?php echo $fap[0]['centro_ruta2']; ?>' />
<input type='text' id='centro_nombre2' name='centro_nombre2' value='<?php echo $fap[0]['centro_nombre2']; ?>' />
</td>
</tr>

<?php } ?>


<?php if($tipof!=5) { ?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Tipo de Consulta:</td>
<td class='tabla_fila' colspan=3><select  style='font-size:11px;' 
id='fap_tipo_consulta' name='fap_tipo_consulta'>
<?php echo $tipohtml; ?></select></td>

</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Sexo:</td>
<td class='tabla_fila'><select  style='font-size:11px;' 
id='sex_id' name='sex_id'>
<?php echo $sexohtml; ?></select></td>
<td class='tabla_fila2'  style='text-align:right;'>Hora NSP:</td>
<td class='tabla_fila'>
<input type='text' id='fap_hora_nsp' name='fap_hora_nsp' size=3 onBlur='validar_hora(this);' 
value='<?php echo substr($fap[0]['fap_hora_nsp'],0,5); ?>' />
</td>

</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Comuna:</td>
<td class='tabla_fila' colspan=3><select  style='font-size:11px;'
id='ciud_id' name='ciud_id'>
<?php echo $comunahtml; ?></select></td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Previsi&oacute;n:</td>
<td class='tabla_fila' colspan=3><select style='font-size:11px;' 
id='prev_id' name='prev_id'>
<?php echo $previsionhtml; ?></select></td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Pron&oacute;stico:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_pronostico' name='fap_pronostico' style='font-size:11px;'>
<option value='-1'>(Seleccionar...)</option>
<?php echo combo_pronostico($fap[0]['fap_pronostico']); ?>
</select>
</td>
</tr>

<?php } ?>


<?php if($tipof!=5) { ?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Cod. Diagn&oacute;stico:</td>
<td class='tabla_fila' colspan=3>
<input type='text' id='diag_cod' name='diag_cod' size=8
value='<?php echo $fap[0]['fap_diag_cod']; ?>' /></td>
</tr>

<tr>
<td colspan=4 class='tabla_fila' style='text-align:center;' id='diagnostico'>
<?php if($fap[0]['diag_desc']=='') { ?>
<i>(Seleccione C&oacute;digo de Diagn&oacute;stico...)</i>
<?php } else { 

		echo htmlentities($fap[0]['diag_desc']);

		} ?>
</td>
</tr>

<?php } else { ?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Diag. Pre.:</td>
<td class='tabla_fila' colspan=2>
<input type='text' id='diag_cod' name='diag_cod' size=20
value='<?php echo $fap[0]['fap_diag_cod']; ?>' /></td><td style='text-align:right;' class='tabla_fila2'>
<center>
A.S.A.:
<select id='fap_asa' name='fap_asa'>
<option value='-1' <?php if($fap[0]['fap_asa']=='-1') echo 'SELECTED'; ?>>(S/D)</option>
<option value='1' <?php if($fap[0]['fap_asa']=='1') echo 'SELECTED'; ?>>1</option>
<option value='2' <?php if($fap[0]['fap_asa']=='2') echo 'SELECTED'; ?>>2</option>
<option value='3' <?php if($fap[0]['fap_asa']=='3') echo 'SELECTED'; ?>>3</option>
<option value='4' <?php if($fap[0]['fap_asa']=='4') echo 'SELECTED'; ?>>4</option>
</select>
</center>
</td>
</tr>

<?php 
		for($i=1;$i<4;$i++) { 
?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Cod. Diag. Post. (<?php echo $i; ?>):</td>
<td class='tabla_fila' colspan=3>

<input type='text' id='fap_diag_cod_<?php echo $i; ?>' name='fap_diag_cod_<?php echo $i; ?>' 
value='<?php echo $fap[0]['fap_diag_cod_'.$i]; ?>' DISABLED size=5 style='font-weight:bold;text-align:center;' />
<input type='text' id='fap_diagnostico_<?php echo $i; ?>' name='fap_diagnostico_<?php echo $i; ?>' 
value='<?php echo htmlentities($fap[0]['fap_diagnostico_'.$i]); ?>' onDblClick='this.value=""; $("fap_diag_cod_<?php echo $i; ?>").value="";' size=35>


</td>
</tr>

<?php 
		}
	} 
?>

<?php if($tipof!=5) { ?>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Destino:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_destino' name='fap_destino' style='font-size:11px;'>
<option value='-1'>(Seleccionar...)</option>
<?php echo $destinohtml; ?>
</select>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Tipo Accidente:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_accidente' name='fap_accidente' style='font-size:11px;'>
<option value='-1'>(Seleccionar...)</option>
<?php echo $accidentehtml; ?>
</select>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Or&iacute;gen:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_origen' name='fap_origen' style='font-size:11px;'>
<option value='-1'>(Seleccionar...)</option>
<?php echo $origenhtml; ?>
</select>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Hora Atenci&oacute;n:</td>
<td class='tabla_fila'>
<input type='text' id='fap_hora_atencion' name='fap_hora_atencion' onBlur='validar_hora(this);'
value='<?php echo substr($fap[0]['fap_hora_atencion'],0,5); ?>' size=3 />
</td>
<td class='tabla_fila2'  style='text-align:right;'>Hora Alta:</td>
<td class='tabla_fila'>
<input type='text' id='fap_hora_alta' name='fap_hora_alta' onBlur='validar_hora(this);' 
value='<?php echo substr($fap[0]['fap_hora_alta'],0,5); ?>' size=3 />
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Priorizaci&oacute;n Pre:</td>
<td class='tabla_fila'>
<select id='fap_prioridad' name='fap_prioridad'>
<?php echo combo_prioriza($fap[0]['fap_prioridad']); ?>
</select>
</td>
<td class='tabla_fila2'  style='text-align:right;'>Priorizaci&oacute;n Post:</td>
<td class='tabla_fila'>
<select id='fap_prioridad_post' name='fap_prioridad_post'>
<?php echo combo_prioriza($fap[0]['fap_prioridad_post']); ?>
</select>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>R.U.T. M&eacute;dico:</td>
<td class='tabla_fila' colspan=3>
<input type='text' id='rut_medico' name='rut_medico' size=8
style='text-align: center;font-size:11px;' 
value='<?php echo $fap[0]['doc_rut']; ?>' DISABLED>
</td></tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Nombre M&eacute;dico:</td>
<td class='tabla_fila' colspan=3>
<input type='hidden' id='fap_doc_id' name='fap_doc_id' 
value='<?php echo $fap[0]['fap_doc_id']; ?>' /> 
<input type='text' id='nombre_medico' name='nombre_medico' 
style='font-size:11px;' size=25
value='<?php echo htmlentities(trim($fap[0]['doc_paterno'].' '.$fap[0]['doc_materno'].' '.$fap[0]['doc_nombres'])); ?>'>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Atendido Por:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_atendido_por' name='fap_atendido_por'>
<option value='-1'>(Seleccionar...)</option>
<?php echo $atendidohtml; ?>
</select>
</td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>GES:</td>
<td class='tabla_fila'>

<input type='checkbox' id='fap_ges' name='fap_ges' 
<?php if($fap[0]['fap_ges']=='t') echo 'CHECKED'; ?> 
onChange="
	if(this.checked) {
		$('fap_ges_folio').disabled=false;	
		$('fap_pat_id').disabled=false;	
		$('fap_pat_id').value='-1';	
	} else {
		$('fap_ges_folio').disabled=true;	
		$('fap_pat_id').disabled=true;			
	}
" />

</td>
<td class='tabla_fila2'  style='text-align:right;'>Nro. Folio:</td>
<td class='tabla_fila'>
<input type='text' id='fap_ges_folio' name='fap_ges_folio' 
<?php if($fap[0]['fap_ges']!='t') echo 'DISABLED'; ?> 
value='<?php echo $fap[0]['fap_ges_folio']; ?>' size=10 /></td>
</tr>

<tr>
<td class='tabla_fila2'  style='text-align:right;'>Patolog&iacute;a GES:</td>
<td class='tabla_fila' colspan=3>
<select id='fap_pat_id' name='fap_pat_id'
<?php if($fap[0]['fap_ges']!='t') echo 'DISABLED'; ?> >
<option value='-1'>(Seleccionar...)</option>
<?php echo $geshtml; ?>
</select>
</td>
</tr>

</table>

<?php } else { ?>

		<table style='width:100%;font-size:11px;'>
		<tr><td class='sub-content' style='text-align:center;' 
		colspan=2><b>Horario Flujo de Paciente</b></td></tr>
		
		<tr><td class='tabla_fila2' style='text-align:right;'>
		Ingreso Pabell&oacute;n:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora1' name='fap_pab_hora1' value='<?php echo $fap[0]['fap_pab_hora1']; ?>' size=10 style='text-align:center;' DISABLED />
		</td></tr>
		
		<tr><td class='tabla_fila2' style='text-align:right;'>
		Ingreso Quir&oacute;fano:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora2' name='fap_pab_hora2' value='<?php echo $fap[0]['fap_pab_hora2']; ?>' size=10  style='text-align:center;'  onFocus='$("listado_fap").scrollTop=357;' onBlur='validar_hora(this);' />
		</td></tr>

		<tr><td class='tabla_fila2' style='text-align:right;'>
		Inicio Anestesia:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora3' name='fap_pab_hora3' value='<?php echo $fap[0]['fap_pab_hora3']; ?>' size=10 style='text-align:center;' onBlur='validar_hora(this);' />
		</td></tr>

		<tr><td class='tabla_fila2' style='text-align:right;'>
		Inicio Intevenci&oacute;n:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora4' name='fap_pab_hora4' value='<?php echo $fap[0]['fap_pab_hora4']; ?>' size=10 style='text-align:center;' onBlur='validar_hora(this);' />
		</td></tr>

		<tr><td class='tabla_fila2' style='text-align:right;'>
		T&eacute;rmino Intervenci&oacute;n:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora5' name='fap_pab_hora5' value='<?php echo $fap[0]['fap_pab_hora5']; ?>' size=10 style='text-align:center;' onBlur='validar_hora(this);' />
		</td></tr>

		<tr><td class='tabla_fila2' style='text-align:right;'>
		T&eacute;rmino Anestesia:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora6' name='fap_pab_hora6' value='<?php echo $fap[0]['fap_pab_hora6']; ?>' size=10  style='text-align:center;' onBlur='validar_hora(this);' />
		</td></tr>

		<tr><td class='tabla_fila2' style='text-align:right;'>
		Salida Quir&oacute;fano / Ingreso Recuperaci&oacute;n:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora7' name='fap_pab_hora7' value='<?php echo $fap[0]['fap_pab_hora7']; ?>' size=10  style='text-align:center;' onBlur='validar_hora(this);' />
		</td></tr>

		<tr><td class='tabla_fila2' style='text-align:right;'>
		Salida Recuperaci&oacute;n:
		</td><td class='tabla_fila'>
		<input type='text' id='fap_pab_hora8' name='fap_pab_hora8' value='<?php echo $fap[0]['fap_pab_hora8']; ?>' size=10  style='text-align:center;' onBlur='validar_hora(this);' />
		</td></tr>

		</table>

		<table style='width:100%;font-size:11px;'>
		<tr><td class='sub-content' style='text-align:center;' 
		colspan=2><b>Equipo(s) Quir&uacute;rgico(s)</b>
		<select id='cant_equipos' name='cant_equipos' onChange='generar_lista_equipos();'>
			<option value='1' <?php if($nequipos==1) echo 'SELECTED'; ?>>1</option>		
			<option value='2' <?php if($nequipos==2) echo 'SELECTED'; ?>>2</option>		
			<option value='3' <?php if($nequipos==3) echo 'SELECTED'; ?>>3</option>		
		</select></td></tr>
		</table>

		<div id='lista_equipos'>

		</div>

		</div>


<?php } ?>

<!--- <table style='width:100%;'>
<tr><td class='tabla_fila2'>Observaciones</td></tr>
<tr><td class='tabla_fila'>
<textarea style='width:100%;' id='fap_observaciones' name='fap_observaciones'><?php echo $fap[0]['fap_observaciones']; ?></textarea>
</td></tr> 

</table>--->

</td>
<td valign='top' style='width:50%;'>

	<div class='sub-content'>
	<img src='iconos/table.png'>
	<b>Registro de Prestaciones</b>
	</div>
	
	<div class='sub-content2' id='lista_presta' 
	style='height:150px;overflow:auto;'>
	
	</div>


<div class='sub-content' id='agrega_presta'>
<table style='width:100%;' cellpadding=0 cellspacing=0>
<tr><td style='width:15px;'>
<center>
<img src='iconos/add.png' />
</center>
</td><td style='width:100px;text-align:right;'>Agregar Prest.:</td>
<td>
<input type='hidden' id='desc_presta' name='desc_presta' value='' />
<input type='text' id='cod_presta' name='cod_presta' size=10 
onFocus='$("listado_fap").scrollTop=0;' />
</td><td style='text-align:right;'>
Cant.:
</td><td>
<input type='text' id='cantidad' name='cantidad'
onKeyUp='if(event.which==13) agregar_prestacion();' size=3 />
</td></tr>
</table>
</div>

<?php if($tipof==5) { ?>

<table style='width:100%;'>

<tr><td style='text-align:right;' class='tabla_fila2'>
Suspensi&oacute;n de FAP:
</td><td class='tabla_fila'>
<input type='text' id='fap_suspension' name='fap_suspension' 
value='<?php echo $fap[0]['fap_suspension']; ?>' size=30 />
</td></tr>

<!-- <tr><td style='text-align:right;' class='tabla_fila2'>
Sospecha G.E.S.:
</td><td class='tabla_fila'>
<input type='checkbox'
id='fap_sospecha_ges' name='fap_sospecha_ges'
<?php if($fap[0]['fap_sospecha_ges']=='t') echo 'CHECKED'; ?> />
</td>
</tr> -->

<tr><td style='text-align:right;' class='tabla_fila2'>
Reoperado:
</td><td class='tabla_fila'>
<input type='checkbox'
id='fap_reoperado' name='fap_reoperado'
<?php if($fap[0]['fap_reoperado']=='t') echo 'CHECKED'; ?> />
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Evaluaci&oacute;n Pre Anest&eacute;sica:
</td><td class='tabla_fila'>
<select id='fap_eval_pre' name='fap_eval_pre'>
<option value='-2' <?php echo (($fap[0]['fap_eval_pre']*1)==-2?'SELECTED':''); ?>>(SIN DATO)</option>
<option value='1' <?php echo (($fap[0]['fap_eval_pre']*1)==1?'SELECTED':''); ?>>SI</option>
<option value='0' <?php echo (($fap[0]['fap_eval_pre']*1)==0?'SELECTED':''); ?>>NO</option>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Tipo de Herida:
</td><td class='tabla_fila'>
<select id='fapth_id' name='fapth_id'>
<?php echo $tipoheridahtml; ?>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Anestesia Principal:
</td><td class='tabla_fila'>
<select id='fapta_id1' name='fapta_id1'>
<option value='-2'>(SIN DATO)</option>
<?php echo $tipoanestesia1html; ?>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Anestesia Secundaria:
</td><td class='tabla_fila'>
<select id='fapta_id2' name='fapta_id2'>
<option value='-2'>(SIN DATO)</option>
<?php echo $tipoanestesia2html; ?>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Entrega Anestesista:
</td><td class='tabla_fila'>
<select id='fap_entrega_ane' name='fap_entrega_ane'>
<option value='1' <?php echo (($fap[0]['fap_entrega_ane']*1)==1?'SELECTED':''); ?>>Si</option>
<option value='0' <?php echo (($fap[0]['fap_entrega_ane']*1)!=1?'SELECTED':''); ?>>No</option>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
E.V.A.:
</td><td class='tabla_fila'>
<select id='fap_eva' name='fap_eva'>
<?php 
	for($i=0;$i<11;$i++) {
		echo '<option value="'.$i.'" '.(($i==$fap[0]['fap_eva']*1)?'SELECTED':'').'>'.$i.'</option>';
	}
?>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Biopsia:
</td><td class='tabla_fila'>
<select id='fap_biopsia' name='fap_biopsia'>
<option value='-2' <?php echo (($fap[0]['fap_biopsia']*1)==-2?'SELECTED':''); ?>>(SIN DATO)</option>
<option value='1' <?php echo (($fap[0]['fap_biopsia']*1)==1?'SELECTED':''); ?>>RAPIDA</option>
<option value='2' <?php echo (($fap[0]['fap_biopsia']*1)==2?'SELECTED':''); ?>>DIFERIDA</option>
<option value='3' <?php echo (($fap[0]['fap_biopsia']*1)==3?'SELECTED':''); ?>>AMBAS</option>
<option value='0' <?php echo (($fap[0]['fap_biopsia']*1)==0?'SELECTED':''); ?>>NO</option>
</select>
</td></tr>

<tr><td style='text-align:right;' class='tabla_fila2'>
Nro. Hoja de Cargo:
</td><td class='tabla_fila'>
<input id='fap_hoja_cargo' name='fap_hoja_cargo' 
style='text-align:center;' value='<?php echo $fap[0]['fap_hoja_cargo']; ?>' />
</td></tr>


</table>

<?php } ?>

<br /><br />
<center>

<input type='button' onClick='boton_guardar();' 
style='width:80%;' id='btn_guardar' value='Guardar FAP ...' /><br />

<input type='button' onClick='ver_siguiente();' 
style='width:80%;' id='siguiente' value='Siguente Registro ...' /><br />

<input type='button' onClick='ver_anterior();' 
style='width:80%;' id='anterior' value='Anterior Registro ...' /><br />

<!-- <input type='button' onClick='eliminar_fap();' 
style='width:80%;' id='eliminar' value='Eliminar FAP ...' /><br /> -->

<input type='button' onClick='listar_fap();' 
style='width:80%;' id='volver' value='Volver Atr&aacute;s ...' /><br />

</center>

</td>
</tr>


</table>

<script>

	<?php if($presta) { ?>

	presta=<?php echo json_encode($prestaciones); ?>;
	
	<?php } else { ?>
	
	presta=[];	
	
	<?php } ?>
	
	<?php if($tipof==5) { ?>

	datos_equipo=<?php echo json_encode($deq); ?>;
	generar_lista_equipos(false);
	
	$('pac_rut').select();
	$('pac_rut').focus();
	
	<?php } ?>
	
	listar_prestaciones();

</script>
