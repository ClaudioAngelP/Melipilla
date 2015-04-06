<?php

  require_once('../../conectar_db.php');

  function img($t) {
    if($t=='t') return '<center><img src="iconos/tick.png" 
                                  width=8 height=8></center>';
    else return '<center><img src="iconos/cross.png" 
                                  width=8 height=8></center>';
  }
  
  function turno($val) {
		switch($val) {
			case 0: return 'NORMAL'; break;	
			case 1: return 'TURNO'; break;	
			case 2: return 'CONVENIO'; break;	
			case 3: return 'PRIVADO'; break;	
			case 4: return 'DOCENCIA'; break;	
		}				
  }
  
  function timediff($hr1, $hr2) {
	  
	  $h1=explode(':',trim($hr1));
	  $h2=explode(':',trim($hr2));
	  
	  $t1=$h1[0]*3600+$h1[1]*60+$h1[2]*1;
	  $t2=$h2[0]*3600+$h2[1]*60+$h2[2]*1;
	  
	  $lapso=$t1-$t2;
	  
	  if($lapso<0) $lapso=($t1+86400)-$t2;
	  
	  $hr=floor($lapso/3600);
	  $mn=floor(($lapso%3600)/60);
	  $sc=(($lapso%3600)%60);
	  
	  if($hr<10) $hr='0'.$hr;
	  if($mn<10) $mn='0'.$mn;
	  if($sc<10) $sc='0'.$sc;
	  
	  return $hr.':'.$mn.':'.$sc;
	  
  }

  $fecha1 = pg_escape_string($_POST['fecha1']);
  $fecha2 = pg_escape_string($_POST['fecha2']);
  $pac_id=$_POST['pac_id']*1;
  $xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);

  	if($xls) {
  	
  	   header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_FAP.xls\";");

	}

	if($pac_id!=0) $pac_w="pac_id=$pac_id"; else $pac_w='true';

  $lista = cargar_registros_obj("
	  
		SELECT DISTINCT
		fap_pabellon.*, 
		date_trunc('second',fap_fecha)::date AS fap_fecha,
		date_trunc('second',fap_fecha)::time AS fap_hora,
		fappab_pabellones.*,
		th.*, pacientes.*, prevision.*, p1.*,
		ta1.fapta_id AS fapta_id1,
		ta1.fapta_desc AS fapta_desc1,
		ta2.fapta_id AS fapta_id2,
		ta2.fapta_desc AS fapta_desc2,
		d1.diag_desc AS diag_desc_1, 		
		d2.diag_desc AS diag_desc_2, 		
		d3.diag_desc AS diag_desc_3,
		COALESCE(c1.centro_nombre,cc1.tcama_tipo) AS centro_nombre,
		COALESCE(c2.centro_nombre,cc2.tcama_tipo) AS centro_nombre2,
		f1.func_nombre AS func_nombre1,
		f2.func_nombre AS func_nombre2,
		date_part('year',age(fap_fecha::date, pac_fc_nac)) as edad_anios,  
		date_part('month',age(fap_fecha::date, pac_fc_nac)) as edad_meses,  
		date_part('day',age(fap_fecha::date, pac_fc_nac)) as edad_dias,
		'' AS edad 		 
		FROM fap_pabellon 
		LEFT JOIN pacientes USING (pac_id)		
		LEFT JOIN prevision ON prevision.prev_id=pacientes.prev_id		
		LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id		
		LEFT JOIN fappab_tipo_herida AS th USING (fapth_id)		
		LEFT JOIN fappab_tipo_anestesia AS ta1 ON fapta_id1=ta1.fapta_id		
		LEFT JOIN fappab_tipo_anestesia AS ta2 ON fapta_id2=ta2.fapta_id		
		LEFT JOIN diagnosticos AS d1 ON fap_diag_cod_1=d1.diag_cod
		LEFT JOIN diagnosticos AS d2 ON fap_diag_cod_2=d2.diag_cod
		LEFT JOIN diagnosticos AS d3 ON fap_diag_cod_3=d3.diag_cod
		LEFT JOIN centro_costo AS c1 ON fap_pabellon.centro_ruta=c1.centro_ruta
		LEFT JOIN centro_costo AS c2 ON fap_pabellon.centro_ruta2=c2.centro_ruta
		LEFT JOIN clasifica_camas AS cc1 ON fap_pabellon.centro_ruta=cc1.tcama_id::text
		LEFT JOIN clasifica_camas AS cc2 ON fap_pabellon.centro_ruta2=cc2.tcama_id::text
		LEFT JOIN funcionario AS f1 ON f1.func_id=fap_pabellon.func_id
		LEFT JOIN funcionario AS f2 ON f2.func_id=fap_pabellon.func_id2
		LEFT JOIN fap_equipo_quirurgico ON fap_pabellon.fap_id=fap_equipo_quirurgico.fap_id AND fapeq_num=0
		LEFT JOIN personal_pabellon AS p1 ON p1.pp_id=cir1 		
		WHERE (fap_fecha::date BETWEEN '$fecha1' AND '$fecha2') AND $pac_w
		ORDER BY fap_pabellon.fap_fecha ASC	
	  
  ", true);
  
  
  $max_presta=cargar_registro("SELECT max(count) from (
			SELECT count(*) FROM fap_prestacion
			LEFT JOIN fap_pabellon USING(fap_id)
			WHERE fap_fecha::date BETWEEN '$fecha1 00:00:00' AND '$fecha2 23:59:59'
			GROUP BY fap_id
			)as foo
			");
  	
?>

<table style='width:100%;' class='lista_small'>

<tr class='tabla_header'>

<td>N&uacute;mero</td>
<td>Fecha</td>
<td>Hora</td>
<td>Nro. Ficha</td>
<td>R.U.T.</td>
<td>Nombre</td>

<td>Edad</td>
<td>Sexo</td>

<td>Func. Creador</td>
<td>Func. Digitador</td>
<td>A.S.A.</td>
<td>Tipo Atenci&oacute;n</td>
<td>Modo de Atenci&oacute;n</td>
<!-- EGF -->
<!-- <td>Tabla</td> -->
<td>Pabell&oacute;n</td>
<td>Serv. Origen</td>
<td>Serv. Destino</td>
<td>Especialidad</td>

<td>Diag. Preoperatorio</td>
<td>Diag. Post. 1 (CIE10)</td>
<td>Diag. Post. 1 (TAPSA)</td>
<td>Diag. Post. 2 (CIE10)</td>
<td>Diag. Post. 2 (TAPSA)</td>
<td>Diag. Post. 3 (CIE10)</td>
<td>Diag. Post. 3 (TAPSA)</td>

<?php 
for($j=0;$j<(($max_presta['max']));$j++)
	print("<td>Prestaci&oacute;n ".($j+1)."</td><td>Tipo</td>");
 ?>
<td>Eval. Pre. Anest.</td>
<td>Entrega Anest.</td>
<td>E.V.A.</td>
<td>Tipo Herida</td>
<td>Anestesia Principal</td>
<td>Anestesia Secundaria</td>
<td>Biopsia</td>

<!-- EGF -->
<!--<td>Sospecha GES</td>-->
<td>Reoperado</td>
<td>Suspensi&oacute;n FAP</td>

<td>Ingreso Pabell&oacute;n</td>
<td>Ingreso Quir&oacute;fano</td>
<td>Inicio Anestesia</td>
<td>Inicio Intervenci&oacute;n</td>
<td>T&eacute;rmino Intervenci&oacute;n</td>
<td>T&eacute;rmino Anestesia</td>
<td>Salida Pab./Ing. Recup.</td>
<td>Salida Recuperaci&oacute;n</td>

<td>Tiempo Interveci&oacute;n</td>
<td>Tiempo Anestesia</td>
<td>Tiempo Total</td>


<?php for($i=1;$i<=3;$i++) { ?>

<td>EQ <?php echo $i; ?> : Cirujano 1 </td>
<td>EQ <?php echo $i; ?> : Cirujano 1 (Tipo) </td> 
<td>EQ <?php echo $i; ?> : Cirujano 2 </td> 
<td>EQ <?php echo $i; ?> : Cirujano 2 (Tipo) </td> 
<td>EQ <?php echo $i; ?> : Cirujano 3 </td> 
<td>EQ <?php echo $i; ?> : Cirujano 3 (Tipo) </td> 
<td>EQ <?php echo $i; ?> : Anest. 1 </td> 
<td>EQ <?php echo $i; ?> : Anest. 1 (Tipo)</td> 
<td>EQ <?php echo $i; ?> : Anest. 2 </td> 
<td>EQ <?php echo $i; ?> : Anest. 2 (Tipo)</td> 
<td>EQ <?php echo $i; ?> : Instrumentista </td> 
<td>EQ <?php echo $i; ?> : Pabellonero </td> 
<td>EQ <?php echo $i; ?> : Tec. Anes. </td> 
<td>EQ <?php echo $i; ?> : Tec. Perf. </td> 
<td>EQ <?php echo $i; ?> : Tec. Rayos </td> 
<td>EQ <?php echo $i; ?> : Tec. Recup. </td> 

<?php } ?>

</tr>

<?php 

  if($lista)
  for($i=0;$i<count($lista);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
		$fap=$lista[$i];    

		$fap_id=$fap['fap_id']*1;

		$presta=cargar_registros_obj("SELECT * FROM fap_prestacion WHERE fap_id=".$fap['fap_id']." order by fappr_id asc");
    
    	$edad='';
      
      if($fap['edad_anios']*1>1) $edad.=$fap['edad_anios'].' a ';
		elseif($fap['edad_anios']*1==1) $edad.=$fap['edad_anios'].' a ';

		if($fap['edad_meses']*1>1) $edad.=$fap['edad_meses'].' m ';	
		elseif($fap['edad_meses']*1==1) $edad.=$fap['edad_meses'].' m ';

		if($fap['edad_dias']*1>1) $edad.=$fap['edad_dias'].' d';
		elseif($fap['edad_dias']*1==1) $edad.=$fap['edad_dias'].' d';

    print("
    <tr class='$clase'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"".$clase."\";'>
    <td style='text-align:center;font-weight:bold;'>".$lista[$i]['fap_fnumero']."</td>
    ");
    
	 print("<td style='text-align:center;'>".$lista[$i]['fap_fecha']."</td>");
	 print("<td style='text-align:center;'>".$lista[$i]['fap_hora']."</td>");
    
    print("<td style='text-align:center;'>".$lista[$i]['pac_ficha']."</td>");
    print("<td style='text-align:center;'>".$lista[$i]['pac_rut']."</td>");
    
	 print("
	    <td>".trim((($lista[$i]['pac_appat']))."
	    ".(($lista[$i]['pac_apmat']))."
	    ".(($lista[$i]['pac_nombres'])))."</td>
	 ");    

	 if($fap['sex_id']==0) $sexo='MASCULINO';
	 elseif($fap['sex_id']==1) $sexo='FEMENINO';
	 else $sexo='INDEFINIDO';

	 print("<td style='text-align:left;'>".$edad."</td>");		
	 print("<td style='text-align:left;'>".$sexo."</td>");		
    
	 print("<td style='text-align:left;'>".$lista[$i]['func_nombre1']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['func_nombre2']."</td>");		

	 if($lista[$i]['fap_tipopab']==0) $lista[$i]['fap_tipopab']='PROGRAMADA';
	 elseif($lista[$i]['fap_tipopab']==1) $lista[$i]['fap_tipopab']='NO PROGRAMADA';
	 elseif($lista[$i]['fap_tipopab']==2) $lista[$i]['fap_tipopab']='URGENCIA';
	 elseif($lista[$i]['fap_tipopab']==3) $lista[$i]['fap_tipopab']='COMPRA SERVICIOS';
	 elseif($lista[$i]['fap_tipopab']==4) $lista[$i]['fap_tipopab']='EXTENSI&Oacute;N HORARIA';
	 elseif($lista[$i]['fap_tipopab']==-1) $lista[$i]['fap_tipopab']='SIN DIGITAR';

	 if($lista[$i]['fap_subtipopab']==0) $lista[$i]['fap_subtipopab']='AMBULATORIO';
	 elseif($lista[$i]['fap_subtipopab']==1) $lista[$i]['fap_subtipopab']='HOSPITALIZADO';
	 elseif($lista[$i]['fap_subtipopab']==-1) $lista[$i]['fap_subtipopab']='SIN DIGITAR';

/* EGF */
/*	 if($lista[$i]['fap_tipopab']!='URGENCIA') {
	 	if($lista[$i]['fap_tablapab']==0) $lista[$i]['fap_tablapab']='PROGRAMADA';
	 	elseif($lista[$i]['fap_tablapab']==1) $lista[$i]['fap_tablapab']='CONDICIONAL';
	 	elseif($lista[$i]['fap_tablapab']==-1) $lista[$i]['fap_tablapab']='SIN DIGITAR';
	 } else {
	 	$lista[$i]['fap_tablapab']='(NO APLICABLE)';
	 }
*/

	if($lista[$i]['fap_asa']==-1) $lista[$i]['fap_asa']='S/D';

	 print("<td style='text-align:left;'>".$lista[$i]['fap_asa']."</td>");
	 print("<td style='text-align:left;'>".$lista[$i]['fap_tipopab']."</td>");
	 print("<td style='text-align:left;'>".$lista[$i]['fap_subtipopab']."</td>");
/* EGF */ //	 print("<td style='text-align:left;'>".$lista[$i]['fap_tablapab']."</td>");		
    
	 print("<td style='text-align:left;'>".$lista[$i]['fapp_desc']."</td>");		

	 print("<td style='text-align:left;'>".$lista[$i]['centro_nombre']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['centro_nombre2']."</td>");		

	 print("<td style='text-align:left;'>".$lista[$i]['pp_desc']."</td>");		

	 print("<td style='text-align:left;'>".$lista[$i]['fap_diag_cod']."</td>");		

	 print("<td style='text-align:left;'>".$lista[$i]['fap_diag_cod_1']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fap_diagnostico_1']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fap_diag_cod_2']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fap_diagnostico_2']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fap_diag_cod_3']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fap_diagnostico_3']."</td>");		

	 for($j=0;$j<(($max_presta['max']));$j++)
	 print("<td style='text-align:left;'>".$presta[$j]['fappr_codigo']."</td>
	 <td style='text-align:center;'>".$presta[$j]['fappr_tipo']."</td>");		

	 if($lista[$i]['fap_entrega_ane']==0) $lista[$i]['fap_entrega_ane']='NO';
	 elseif($lista[$i]['fap_entrega_ane']==1) $lista[$i]['fap_entrega_ane']='SI';
	 elseif($lista[$i]['fap_entrega_ane']==-1) $lista[$i]['fap_entrega_ane']='(S/D)';

	 if($lista[$i]['fap_eval_pre']==0) $lista[$i]['fap_eval_pre']='NO';
	 elseif($lista[$i]['fap_eval_pre']==1) $lista[$i]['fap_eval_pre']='SI';
	 elseif($lista[$i]['fap_eval_pre']==-1) $lista[$i]['fap_eval_pre']='(S/D)';
	 elseif($lista[$i]['fap_eval_pre']==-2) $lista[$i]['fap_eval_pre']='(S/D)';

	if($lista[$i]['fap_eva']==-1) $lista[$i]['fap_eva']='S/D';

	if($lista[$i]['fapta_desc1']=='') $lista[$i]['fapta_desc1']='(SIN DATO...)';
	if($lista[$i]['fapta_desc2']=='') $lista[$i]['fapta_desc2']='(SIN DATO...)';
	 
	 print("<td style='text-align:left;'>".$lista[$i]['fap_eval_pre']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fap_entrega_ane']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fap_eva']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fapth_desc']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fapta_desc1']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['fapta_desc2']."</td>");		

	 if($lista[$i]['fap_biopsia']==0) $lista[$i]['fap_biopsia']='NO';
	 elseif($lista[$i]['fap_biopsia']==1) $lista[$i]['fap_biopsia']='RAPIDA';
	 elseif($lista[$i]['fap_biopsia']==2) $lista[$i]['fap_biopsia']='DIFERIDA';
	 elseif($lista[$i]['fap_biopsia']==3) $lista[$i]['fap_biopsia']='AMBAS';
	 elseif($lista[$i]['fap_biopsia']==-1) $lista[$i]['fap_biopsia']='SIN DIGITAR';
	 elseif($lista[$i]['fap_biopsia']==-2) $lista[$i]['fap_biopsia']='SIN DATO';

	 print("<td style='text-align:left;'>".$lista[$i]['fap_biopsia']."</td>");

/* EGF */	 //print("<td style='text-align:left;'>".($lista[$i]['fap_sospecha_ges']=='t'?'SI':'NO')."</td>");
	 print("<td style='text-align:left;'>".($lista[$i]['fap_reoperado']=='t'?'SI':'NO')."</td>");
	 print("<td style='text-align:left;'>".$lista[$i]['fap_suspension']."</td>");		

	for($k=1;$k<=8;$k++) {
	
		print("<td align='center'>".$lista[$i]['fap_pab_hora'.$k]."</td>");	
		
	}

	// Tiempos Interveción, Anestesia y Total...

	if($lista[$i]['fap_pab_hora1']!='' AND $lista[$i]['fap_pab_hora5']!='' AND $lista[$i]['fap_pab_hora6']!='' AND
		$lista[$i]['fap_pab_hora4']!='' AND $lista[$i]['fap_pab_hora3']!='') {

		print("<td style='text-align:center;'>".timediff($lista[$i]['fap_pab_hora5'], $lista[$i]['fap_pab_hora4'])."</td>");		
		print("<td style='text-align:center;'>".timediff($lista[$i]['fap_pab_hora6'], $lista[$i]['fap_pab_hora3'])."</td>");		
		
		if($lista[$i]['fap_pab_hora8']!='')
			print("<td style='text-align:center;'>".timediff($lista[$i]['fap_pab_hora8'], $lista[$i]['fap_pab_hora4'])."</td>");		
		else
			print("<td style='text-align:center;'>".timediff($lista[$i]['fap_pab_hora7'], $lista[$i]['fap_pab_hora4'])."</td>");		
		
	} else {

		print("<td style='text-align:center;'>00:00:00</td><td style='text-align:center;'>00:00:00</td><td style='text-align:center;'>00:00:00</td>");
		
	}


	$equipo=cargar_registros_obj("
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

	for($j=0;$j<3;$j++) {
		for($k=1;$k<=11;$k++) {

			if(trim($equipo[$j]['pp'.$k.'_nombre'])!='')
				print("<td>[".$equipo[$j]['pp'.$k.'_rut']."] ".$equipo[$j]['pp'.$k.'_nombre']."</td>");
			else 
				print("<td>&nbsp;</td>");
				
			if($k<6) {
				if(trim($equipo[$j]['pp'.$k.'_nombre'])!='')	
					print("<td>".turno($equipo[$j]['pp'.$k.'_turno'])."</td>");
				else 
					print("<td>&nbsp;</td>");			
			}
					
		}				
	}		
    
	 print("
    </tr>
    ");

  }

?>

</table>

<script>
	
	datos_fap=<?php echo json_encode($lista); ?>;

	if(datos_fap)
		$('cant_reg').innerHTML='Total de Registros: <b>'+datos_fap.length+'</b>';
    else
		$('cant_reg').innerHTML='<i>No hay registros.</i>';

</script>
