<?php

	require_once('../../conectar_db.php');
	
	
	$tipo=$_POST['tipo']*1;
	$fecha1=pg_escape_string($_POST['fecha1']);
	$fecha2=pg_escape_string($_POST['fecha2']);
	$inf=$_POST['tipo_informe']*1;
	$xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);
	$serv_id=$_POST['centro_ruta0']*1;
	$tipo_camas=$_POST['tipo_camas']*1;	

if($serv_id!=0) {
		$serv_w="hosp_servicio=$serv_id";
	} else {
		$serv_w='true';
	}
 
	if($xls) {
  	
  	   header("Content-type: application/vnd.ms-excel");
       header("Content-Disposition: filename=\"Informe_FAP.xls\";");
		
		if($tipo!=13)
			print("<table><tr><td colspan=4 align='center'><b>$titulo</b></td></tr>
			<tr><td>&nbsp;</td><td align='right'>Tipo FAP:</td><td>".$tfap."</td></tr>
			<tr><td>&nbsp;</td><td align='right'>Fecha Inicio:</td><td>".$fecha1."</td></tr>
			<tr><td>&nbsp;</td><td align='right'>Fecha T&eacute;rmino:</td><td>".$fecha2."</td></tr>
			</table>");  	
  		
	}	
	
	
	
	
	
	if ($tipo==6){
	
	$query=cargar_registros_obj("SELECT foo.fappr_codigo, count(fappr_codigo) AS cantidad, fappr_tipo, glosa,tipo,fap_suspension
FROM(
SELECT fappr_codigo,fappr_tipo,glosa, fap_fecha,tipo,fap_suspension
FROM fap_prestacion
LEFT JOIN codigos_prestacion_recaudacion ON fappr_codigo=codigo
JOIN fap_pabellon USING(fap_id)
WHERE fap_fecha::date>='$fecha1' AND fap_fecha::date<='$fecha2' AND tipo='mai' AND fap_suspension=''
)foo
GROUP BY foo.fappr_codigo,foo.fappr_tipo,foo.glosa,foo.tipo,foo.fap_suspension
ORDER BY foo.fappr_codigo ASC");

?>
<table style='width:100%;' class='lista_small'>
<tr class='tabla_header'>
	<td>Código</td></td>
	<td>Glosa</td>
	<td>Tipo</td>
	<td>Cantidad</td>
</tr>
<?php
	if ($query)
	for ($i=0;$i<count($query);$i++){
	
	($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	
	
	print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";'onMouseOut='this.className=\"".$clase."\";'>");
	print("<td style='text-align:center;'>".$query[$i]['fappr_codigo']."</td>");
	print("<td style='text-align:left;'>".htmlentities($query[$i]['glosa'])."</td>");
	print("<td style='text-align:center;'>".$query[$i]['fappr_tipo']."</td>");
	print("<td style='text-align:center;'>".$query[$i]['cantidad']."</td>");
	}

}

if ($tipo==7){
	
	$query = cargar_registros_obj("
	  
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
		'' AS edad 	,	 
		fappr_tipo,
		fappr_codigo,
		fap_suspension
		FROM fap_pabellon 
		LEFT JOIN pacientes USING (pac_id)		
		LEFT JOIN prevision ON prevision.prev_id=pacientes.prev_id		
		LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id		
		LEFT JOIN fap_prestacion USING (fap_id)
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
		
		WHERE fap_fecha::date BETWEEN '$fecha1' AND '$fecha2' AND fap_suspension=''
		ORDER BY fap_pabellon.fap_fecha ASC	
	  
  ", true);
  	
?>
<table style='width:100%;' class='lista_small'>
<tr class='tabla_header'>
	<td>Fecha</td>
	<td>Hora</td>
	<td>Nro. Ficha</td>
	<td>R.U.T.</td>
	<td>Nombre</td>
	<td>Edad</td>
	<td>Sexo</td>
	<td>Tipo Atenci&oacute;n</td>
	<td>Modo de Atenci&oacute;n</td>
	<td>Pabell&oacute;n</td>
	<td>Serv. Origen</td>
	<td>Serv. Destino</td>
	<td>Prestaciones</td>
	<td>Tipo</td>
</tr>
<?php 

  if($query)
  for($i=0;$i<count($query);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
		$fap=$query[$i];    

		$fap_id=$fap['fap_id']*1;
	
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
    
    ");
    
	 print("<td style='text-align:center;'>".$query[$i]['fap_fecha']."</td>");
	 print("<td style='text-align:center;'>".$query[$i]['fap_hora']."</td>");
    
    print("<td style='text-align:center;'>".$query[$i]['pac_ficha']."</td>");
    print("<td style='text-align:center;'>".$query[$i]['pac_rut']."</td>");
    
	 print("<td>".trim((($query[$i]['pac_appat']))." ".(($query[$i]['pac_apmat']))." ".(($query[$i]['pac_nombres'])))."</td>");    

	 if($fap['sex_id']==0) $sexo='MASCULINO';
	 elseif($fap['sex_id']==1) $sexo='FEMENINO';
	 else $sexo='INDEFINIDO';
	 print("<td style='text-align:left;'>".$edad."</td>");		
	 print("<td style='text-align:left;'>".$sexo."</td>");		
	 if($query[$i]['fap_tipopab']==0) $query[$i]['fap_tipopab']='ELECTIVO';
	 elseif($query[$i]['fap_tipopab']==1) $query[$i]['fap_tipopab']='URGENCIA';
	 elseif($query[$i]['fap_tipopab']==2) $query[$i]['fap_tipopab']='EXT. HORARIA';
	 elseif($query[$i]['fap_tipopab']==3) $query[$i]['fap_tipopab']='PRIVADO INST.';
	 elseif($query[$i]['fap_tipopab']==4) $query[$i]['fap_tipopab']='PRIVADO URG.';
	 elseif($query[$i]['fap_tipopab']==-1) $query[$i]['fap_tipopab']='SIN DIGITAR';
	 if($query[$i]['fap_subtipopab']==0) $query[$i]['fap_subtipopab']='AMBULATORIO';
	 elseif($query[$i]['fap_subtipopab']==1) $query[$i]['fap_subtipopab']='HOSPITALIZADO';
	 elseif($query[$i]['fap_subtipopab']==-1) $query[$i]['fap_subtipopab']='SIN DIGITAR';
	 print("<td style='text-align:left;'>".$query[$i]['fap_tipopab']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fap_subtipopab']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fapp_desc']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['centro_nombre']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['centro_nombre2']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fappr_codigo']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fappr_tipo']."</td>");
	}
}

if ($tipo==8){
	
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
  $xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);

  	if($xls) {
  	
  	   header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_FAP.xls\";");

	}

  $query = cargar_registros_obj("
	  
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
		'' AS edad,
		fappr_codigo,
		fappr_tipo,
		fap_suspension
		FROM fap_pabellon 
		LEFT JOIN fap_prestacion USING (fap_id)
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
		WHERE fap_fecha::date BETWEEN '$fecha1' AND '$fecha2' AND fap_suspension=''
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
<td>Prestación</td>
<td>Código</td>
<td>Eval. Pre. Anest.</td>
<td>Entrega Anest.</td>
<td>E.V.A.</td>
<td>Tipo Herida</td>
<td>Anestesia Principal</td>
<td>Anestesia Secundaria</td>
<td>Biopsia</td>
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

  if($query)
  for($i=0;$i<count($query);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
		$fap=$query[$i];    

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
    <td style='text-align:center;font-weight:bold;'>".$query[$i]['fap_fnumero']."</td>
    ");
    
	 print("<td style='text-align:center;'>".$query[$i]['fap_fecha']."</td>");
	 print("<td style='text-align:center;'>".$query[$i]['fap_hora']."</td>");
    
    print("<td style='text-align:center;'>".$query[$i]['pac_ficha']."</td>");
    print("<td style='text-align:center;'>".$query[$i]['pac_rut']."</td>");
    
	 print("
	    <td>".trim((($query[$i]['pac_appat']))."
	    ".(($query[$i]['pac_apmat']))."
	    ".(($query[$i]['pac_nombres'])))."</td>
	 ");    

	 if($fap['sex_id']==0) $sexo='MASCULINO';
	 elseif($fap['sex_id']==1) $sexo='FEMENINO';
	 else $sexo='INDEFINIDO';

	 print("<td style='text-align:left;'>".$edad."</td>");		
	 print("<td style='text-align:left;'>".$sexo."</td>");		
    
	 print("<td style='text-align:left;'>".$query[$i]['func_nombre1']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['func_nombre2']."</td>");		

	 if($query[$i]['fap_tipopab']==0) $query[$i]['fap_tipopab']='PROGRAMADA';
	 elseif($query[$i]['fap_tipopab']==1) $query[$i]['fap_tipopab']='NO PROGRAMADA';
	 elseif($query[$i]['fap_tipopab']==2) $query[$i]['fap_tipopab']='URGENCIA';
	 elseif($query[$i]['fap_tipopab']==3) $query[$i]['fap_tipopab']='COMPRA SERVICIOS';
	 elseif($query[$i]['fap_tipopab']==4) $query[$i]['fap_tipopab']='EXTENSI&Oacute;N HORARIA';
	 elseif($query[$i]['fap_tipopab']==-1) $query[$i]['fap_tipopab']='SIN DIGITAR';

	 if($query[$i]['fap_subtipopab']==0) $query[$i]['fap_subtipopab']='AMBULATORIO';
	 elseif($query[$i]['fap_subtipopab']==1) $query[$i]['fap_subtipopab']='HOSPITALIZADO';
	 elseif($query[$i]['fap_subtipopab']==-1) $query[$i]['fap_subtipopab']='SIN DIGITAR';

	if($query[$i]['fap_asa']==-1) $query[$i]['fap_asa']='S/D';

	 print("<td style='text-align:left;'>".$query[$i]['fap_asa']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fap_tipopab']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fap_subtipopab']."</td>");
/* EGF */ //	 print("<td style='text-align:left;'>".$query[$i]['fap_tablapab']."</td>");		
    
	 print("<td style='text-align:left;'>".$query[$i]['fapp_desc']."</td>");		

	 print("<td style='text-align:left;'>".$query[$i]['centro_nombre']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['centro_nombre2']."</td>");		

	 print("<td style='text-align:left;'>".$query[$i]['pp_desc']."</td>");		

	 print("<td style='text-align:left;'>".$query[$i]['fap_diag_cod']."</td>");		

	 print("<td style='text-align:left;'>".$query[$i]['fap_diag_cod_1']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fap_diagnostico_1']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fap_diag_cod_2']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fap_diagnostico_2']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fap_diag_cod_3']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fap_diagnostico_3']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fappr_codigo']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fappr_tipo']."</td>");
	 
	 if($query[$i]['fap_entrega_ane']==0) $query[$i]['fap_entrega_ane']='NO';
	 elseif($query[$i]['fap_entrega_ane']==1) $query[$i]['fap_entrega_ane']='SI';
	 elseif($query[$i]['fap_entrega_ane']==-1) $query[$i]['fap_entrega_ane']='(S/D)';

	 if($query[$i]['fap_eval_pre']==0) $query[$i]['fap_eval_pre']='NO';
	 elseif($query[$i]['fap_eval_pre']==1) $query[$i]['fap_eval_pre']='SI';
	 elseif($query[$i]['fap_eval_pre']==-1) $query[$i]['fap_eval_pre']='(S/D)';
	 elseif($query[$i]['fap_eval_pre']==-2) $query[$i]['fap_eval_pre']='(S/D)';

	if($query[$i]['fap_eva']==-1) $query[$i]['fap_eva']='S/D';

	if($query[$i]['fapta_desc1']=='') $query[$i]['fapta_desc1']='(SIN DATO...)';
	if($query[$i]['fapta_desc2']=='') $query[$i]['fapta_desc2']='(SIN DATO...)';
	 
	 print("<td style='text-align:left;'>".$query[$i]['fap_eval_pre']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fap_entrega_ane']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fap_eva']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fapth_desc']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fapta_desc1']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fapta_desc2']."</td>");		

	 if($query[$i]['fap_biopsia']==0) $query[$i]['fap_biopsia']='NO';
	 elseif($query[$i]['fap_biopsia']==1) $query[$i]['fap_biopsia']='RAPIDA';
	 elseif($query[$i]['fap_biopsia']==2) $query[$i]['fap_biopsia']='DIFERIDA';
	 elseif($query[$i]['fap_biopsia']==3) $query[$i]['fap_biopsia']='AMBAS';
	 elseif($query[$i]['fap_biopsia']==-1) $query[$i]['fap_biopsia']='SIN DIGITAR';
	 elseif($query[$i]['fap_biopsia']==-2) $query[$i]['fap_biopsia']='SIN DATO';

	 print("<td style='text-align:left;'>".$query[$i]['fap_biopsia']."</td>");

/* EGF */	 //print("<td style='text-align:left;'>".($query[$i]['fap_sospecha_ges']=='t'?'SI':'NO')."</td>");
	 print("<td style='text-align:left;'>".($query[$i]['fap_reoperado']=='t'?'SI':'NO')."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fap_suspension']."</td>");		

	for($k=1;$k<=8;$k++) {
	
		print("<td align='center'>".$query[$i]['fap_pab_hora'.$k]."</td>");	
		
	}

	// Tiempos Interveción, Anestesia y Total...

	if($query[$i]['fap_pab_hora1']!='' AND $query[$i]['fap_pab_hora5']!='' AND $query[$i]['fap_pab_hora6']!='' AND
		$query[$i]['fap_pab_hora4']!='' AND $query[$i]['fap_pab_hora3']!='') {

		print("<td style='text-align:center;'>".timediff($query[$i]['fap_pab_hora5'], $query[$i]['fap_pab_hora4'])."</td>");		
		print("<td style='text-align:center;'>".timediff($query[$i]['fap_pab_hora6'], $query[$i]['fap_pab_hora3'])."</td>");		
		
		if($query[$i]['fap_pab_hora8']!='')
			print("<td style='text-align:center;'>".timediff($query[$i]['fap_pab_hora8'], $query[$i]['fap_pab_hora4'])."</td>");		
		else
			print("<td style='text-align:center;'>".timediff($query[$i]['fap_pab_hora7'], $query[$i]['fap_pab_hora4'])."</td>");		
		
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

}

if ($tipo==9){
	
	$query=cargar_registros_obj("SELECT fap_fecha,fap_id,fap_fnumero,fap_suspension,pac_id,pac_ficha,fap_diag_cod,
								pac_nombres ||' '||  pac_appat ||' '||  pac_apmat AS nombre_completo,
								date_part('year',age(fap_fecha::DATE, pac_fc_nac)) AS edad_anios,  
								date_part('month',age(fap_fecha::DATE, pac_fc_nac)) AS edad_meses,  
								date_part('day',age(fap_fecha::DATE, pac_fc_nac)) AS edad_dias,
								'' AS edad ,COALESCE(c1.centro_nombre,cc1.tcama_tipo) AS centro_nombre
								FROM fap_pabellon
								LEFT JOIN pacientes USING (pac_id)
								LEFT JOIN centro_costo AS c1 ON fap_pabellon.centro_ruta=c1.centro_ruta
								--LEFT JOIN centro_costo AS c2 ON fap_pabellon.centro_ruta2=c2.centro_ruta
								LEFT JOIN clasifica_camas AS cc1 ON fap_pabellon.centro_ruta=cc1.tcama_id::text
								--LEFT JOIN clasifica_camas AS cc2 ON fap_pabellon.centro_ruta2=cc2.tcama_id::text
								WHERE fap_suspension <> '' AND fap_fecha >= '$fecha1' AND fap_fecha <= '$fecha2'
								ORDER BY fap_fecha ASC");
?>
<table style='width:100%;' class='lista_small'>
	<tr class='tabla_header'>
		<td>Fecha FAP</td>
		<td>N° FAP</td>
		<td>N° Ficha</td>
		<td> Nombre Paciente</td>
		<td>Edad</td>
		<td>Diagn&oacute;stico</td>
		<td>Fecha Solicitud</td>
		<td>Motivo Suspensión</td>
		<td>Servicio</td>
	</tr>

<?php
 if ($query)
 for($i=0;$i<count($query);$i++){
	 ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
	 print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' 
    onMouseOut='this.className=\"".$clase."\";'>");
	 print("<td style='text-align:left;'>".$query[$i]['fap_fecha']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fap_fnumero']."</td>");
	 print("<td style ='text-align:left;'>".$query[$i]['pac_ficha']."</td>");
	 print("<td style ='text-align:left;'>".$query[$i]['nombre_completo']."</td>");
	 print("<td style ='text-align:left;'>".$query[$i]['edad_anios']." Años ".$query[$i]['edad_meses']." Meses ".$query[$i]['edad_dias']." Días". "</td>");
	 print("<td style ='text-align:left;'>".$query[$i]['fap_diag_cod']."</td>");
	 print("<td style ='text-align:left;'>".$query[$i]['fecha_solicitud']."</td>");
	 print("<td style ='text-align:left;'>".utf8_decode($query[$i]['fap_suspension'])."</td>");
	 print("<td style ='text-align:left;'>".$query[$i]['centro_nombre']."</td>");
	 
 }//cierra for
}


if ($tipo==10){
	
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
  $xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);

  	if($xls) {
  	
  	   header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_FAP.xls\";");

	}

  $query = cargar_registros_obj("
	  
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
		'' AS edad,
		fappr_codigo,
		fappr_tipo,
		glosa,
		fap_suspension
		FROM fap_pabellon 
		LEFT JOIN fap_prestacion USING (fap_id)
		LEFT JOIN codigos_prestacion_recaudacion ON codigo=fappr_codigo
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
		WHERE fap_fecha::date BETWEEN '$fecha1' AND '$fecha2' AND fap_suspension=''
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
	<td>Fecha</td>
	<td>Hora</td>
	<td>Nro. Ficha</td>
	<td>R.U.T.</td>
	<td>Nombre</td>
	<td>Edad</td>
	<td>Sexo</td>
	<td>Tipo Atenci&oacute;n</td>
	<td>Modo de Atenci&oacute;n</td>
	<td>Pabell&oacute;n</td>
	<td>Serv. Origen</td>
	<td>Serv. Destino</td>
	<td>Prestaciones</td>
	<td>Glosa</td>
	<td>Tipo</td>
</tr>

<?php 

  if($query)
  for($i=0;$i<count($query);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
		$fap=$query[$i];    

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
    <tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
    
	 print("<td style='text-align:center;'>".$query[$i]['fap_fecha']."</td>");
	 print("<td style='text-align:center;'>".$query[$i]['fap_hora']."</td>");
    
    print("<td style='text-align:center;'>".$query[$i]['pac_ficha']."</td>");
    print("<td style='text-align:center;'>".$query[$i]['pac_rut']."</td>");
    print("<td>".trim((($query[$i]['pac_appat']))." ".(($query[$i]['pac_apmat']))." ".(($query[$i]['pac_nombres'])))."</td>");    

	if($fap['sex_id']==0) $sexo='MASCULINO';
		elseif($fap['sex_id']==1) $sexo='FEMENINO';
		else $sexo='INDEFINIDO';

	 print("<td style='text-align:left;'>".$edad."</td>");		
	 print("<td style='text-align:left;'>".$sexo."</td>");		
    	 
	 if($query[$i]['fap_tipopab']==0) $query[$i]['fap_tipopab']='ELECTIVO';
		 elseif($query[$i]['fap_tipopab']==1) $query[$i]['fap_tipopab']='URGENCIA';
		 elseif($query[$i]['fap_tipopab']==2) $query[$i]['fap_tipopab']='EXT. HORARIA';
		 elseif($query[$i]['fap_tipopab']==3) $query[$i]['fap_tipopab']='PRIVADO INST.';
		 elseif($query[$i]['fap_tipopab']==4) $query[$i]['fap_tipopab']='PRIVADO URG.';
		 elseif($query[$i]['fap_tipopab']==-1) $query[$i]['fap_tipopab']='SIN DIGITAR';
	 if($query[$i]['fap_subtipopab']==0) $query[$i]['fap_subtipopab']='AMBULATORIO';
		 elseif($query[$i]['fap_subtipopab']==1) $query[$i]['fap_subtipopab']='HOSPITALIZADO';
		 elseif($query[$i]['fap_subtipopab']==-1) $query[$i]['fap_subtipopab']='SIN DIGITAR';
	 print("<td style='text-align:left;'>".$query[$i]['fap_tipopab']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fap_subtipopab']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fapp_desc']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['centro_nombre']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['centro_nombre2']."</td>");		
	 print("<td style='text-align:left;'>".$query[$i]['fappr_codigo']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['glosa']."</td>");
	 print("<td style='text-align:left;'>".$query[$i]['fappr_tipo']."</td>");

  }

}







if ($tipo==11){
	
function img($t) {
    if($t=='t') return '<center><img src="iconos/tick.png" 
                                  width=8 height=8></center>';
    else return '<center><img src="iconos/cross.png" 
                                  width=8 height=8></center>';
  }
    
  
  $fecha1 = pg_escape_string($_POST['fecha1']);
  $fecha2 = pg_escape_string($_POST['fecha2']);
  $xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);
  $serv_id=$_POST['centro_ruta0']*1;
  
  		if($serv_id!=0) {
			$serv_w="t1.tcama_id=$serv_id";
		} else {
			$serv_w='true';
		}		
  
   
  	if($xls) {
  	
  	   header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_FAP.xls\";");

	}

  $query = cargar_registros_obj("SELECT
								 DISTINCT
								fap_id,fap_suspension,fap_fecha,pac_ficha,pac_rut,pac_nombres,
								pac_appat ||' ' || pac_apmat as apellidos,
								fappr_codigo,glosa,hosp_fecha_ing,hosp_servicio,
								--fap_fecha::DATE-(COALESCE(hosp_fecha_ing,hosp_fecha_hospitalizacion)::DATE) AS dias_espera,
								fap_fecha::DATE-hosp_fecha_ing::DATE AS dias_espera,
								COALESCE(c1.centro_nombre,cc1.tcama_tipo) AS centro_nombre,
								t1.tcama_tipo AS tcama_tipo, t1.tcama_num_ini AS tcama_num_ini,
								t2.tcama_tipo AS servicio,hosp_id
								FROM fap_pabellon
								LEFT JOIN pacientes USING (pac_id)
								LEFT JOIN fap_prestacion USING (fap_id)
								LEFT JOIN codigos_prestacion_recaudacion ON fappr_codigo=codigo
								LEFT JOIN hospitalizacion ON pac_id=hosp_pac_id
								LEFT JOIN clasifica_camas ON hosp_pac_id=tcama_id
								LEFT JOIN centro_costo AS c1 ON fap_pabellon.centro_ruta=c1.centro_ruta
								LEFT JOIN centro_costo AS c2 ON fap_pabellon.centro_ruta2=c2.centro_ruta
								LEFT JOIN clasifica_camas AS cc1 ON fap_pabellon.centro_ruta=cc1.tcama_id::text
								LEFT JOIN clasifica_camas AS t1 ON t1.tcama_num_ini<=hosp_numero_cama AND t1.tcama_num_fin>=hosp_numero_cama
								LEFT JOIN clasifica_camas AS t2 ON t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
								WHERE fap_suspension = '' AND fap_fecha>= '$fecha1' AND fap_fecha<= '$fecha2' AND $serv_w
								", true);
?>

<table style='width:100%;' class='lista_small'>

<tr class='tabla_header'>
	<td>Ficha</td>
	<td>R.U.T</td>
	<td>Nombres</td>
	<td>Apellidos</td>
	<td>Servicio Hosp.</td>
	<td>Fecha Hosp.</td>
	<td>C&oacute;digo</td>
	<td>Intervenci&oacute;n</td>
	<td>Fecha Intervenci&oacute;n</td>
	<td>D&iacute;as</td>
	
</tr>

<?php 

  if($query)
  for($i=0;$i<count($query);$i++) {

    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    
    	
		print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>");
		print("<td style='text-align:center;'>".$query[$i]['pac_ficha']."</td>");
		print("<td style='text-align:center;'>".$query[$i]['pac_rut']."</td>");
		print("<td style='text-align:left;'>".$query[$i]['pac_nombres']."</td>");
		print("<td style='text-align:left;'>".$query[$i]['apellidos']."</td>");
		print("<td style='text-align:left;'>".$query[$i]['tcama_tipo']."</td>");
		//print("<td style='text-align:left;'>".$servicio."</td>");
		print("<td style='text-align:left;'>".$query[$i]['hosp_fecha_ing']."</td>");
		print("<td style='text-align:left;'>".$query[$i]['fappr_codigo']."</td>");		
		print("<td style='text-align:left;'>".$query[$i]['glosa']."</td>");		
		print("<td style='text-align:left;'>".$query[$i]['fap_fecha']."</td>");
		print("<td style='text-align:left;'>".$query[$i]['dias_espera']."</td>");		
	
  }

}
?>

<?php if($tipo==12){

	$hora1=pg_escape_string($_POST['hora1']);
	$hora2=pg_escape_string($_POST['hora2']);
	$pabellon=$_POST['fap_numpabellon']*1;
		
	if($hora1=='')
		$hora1='00:00:00';
	
	if($hora2=='')
		$hora2='23:59:59';
	
	if($pabellon!=0)
		$pabellon_q="fap_numpabellon=$pabellon";
	else
		$pabellon_q="true";

	$query=cargar_registros_obj("SELECT fap_fnumero, fap_fecha, fap_diag_cod, (pp_nombres ||' '|| pp_paterno ||' '|| pp_materno) AS cir_nombres, 
								tcama_tipo AS centro_nombre, fap_pab_hora3 AS inicio_anestesia, fap_pab_hora7 AS salida_pabellon, fapp_desc
								FROM fap_pabellon 
								LEFT JOIN fap_equipo_quirurgico USING(fap_id)
								LEFT JOIN personal_pabellon AS p1 ON p1.pp_id=cir1
								LEFT JOIN clasifica_camas ON fap_pabellon.centro_ruta=tcama_id::text
								LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id
								WHERE fap_fecha>='$fecha1 $hora1' AND fap_fecha<='$fecha2 $hora2' AND $pabellon_q
								ORDER BY fap_fecha DESC");
?>
	<table style='width:100%;' class='lista_small'>

		<tr class='tabla_header'>
			<td>Fecha</td>
			<td>N&uacute;mero</td>
			<td>Pabell&oacute;n</td>
			<td>Diagn&oacute;tico Pre.</td>
			<td>Cirujano</td>
			<td>Servicio Origen</td>
			<td>Inicio Anestesia</td>
			<td>Salida Pabell&oacute;n</td>			
		</tr>
		
	<?php
		
		  if($query)
			for($i=0;$i<count($query);$i++) {

				($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';

					print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>
						<td style='text-align:center;'>".$query[$i]['fap_fecha']."</td>
						<td style='text-align:center;font-weight:bold;'>".$query[$i]['fap_fnumero']."</td>
						<td style='text-align:center;font-weight:bold;'>".$query[$i]['fapp_desc']."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['fap_diag_cod'])."</td>
						<td style='text-align:left;'>".$query[$i]['cir_nombres']."</td>
						<td style='text-align:left;'>".$query[$i]['centro_nombre']."</td>
						<td style='text-align:center;'>".$query[$i]['inicio_anestesia']."</td>
						<td style='text-align:center;'>".$query[$i]['salida_pabellon']."</td>");
				
					print("</tr>");		
			}
			print("</table>");
	
	?>

<?php
}
?>

<?php if($tipo==13){

	$hora1=pg_escape_string($_POST['hora1']);
	$hora2=pg_escape_string($_POST['hora2']);
		
	if($hora1=='')
		$hora1='00:00:00';
	
	if($hora2=='')
		$hora2='23:59:59';
	
	$query=cargar_registros_obj(" SELECT (pac_nombres || ' ' || pac_appat ||' '|| pac_apmat) as pac_nombres,  
							  date_part('year',age(now()::date, pac_fc_nac)) as edad_anios, pac_rut, pac_ficha, fap_subtipopab as modo_at,
							  fap_tipopab as tipo_at, tcama_tipo as serv_origen, prev_desc, fapp_desc, fap_diag_cod as diag_pre,
							  fap_diag_cod_1 ||'- '||fap_diagnostico_1 as diag_post, fappr_codigo as prestacion, 
							  (select pp_rut FROM personal_pabellon WHERE pp_id=fap_equipo_quirurgico.cir1) as equipo_rut,
							  (select pp_nombres || ' ' || pp_paterno || ' '|| pp_materno  FROM personal_pabellon WHERE pp_id=fap_equipo_quirurgico.cir1) as equipo_nombre,  
							  fap_hallazgos, fap_protocolo, fap_fecha,
							  (SELECT DISTINCT glosa FROM codigos_prestacion_recaudacion WHERE codigo=fappr_codigo LIMIT 1) AS prestacion_glosa
							  FROM fap_pabellon
							  JOIN pacientes USING(pac_id)
							  LEFT JOIN clasifica_camas ON CASE WHEN centro_ruta='' THEN 0 ELSE centro_ruta::bigint END=tcama_id
							  LEFT JOIN prevision ON pacientes.prev_id=prevision.prev_id
							  LEFT JOIN fap_prestacion USING(fap_id)
							  LEFT JOIN fappab_pabellones ON fap_numpabellon=fapp_id	
							  LEFT JOIN fap_equipo_quirurgico ON fap_pabellon.fap_id=fap_equipo_quirurgico.fap_id AND fapeq_num=0
							  WHERE fap_fecha>='$fecha1 $hora1' AND fap_fecha<='$fecha2 $hora2'
							  ORDER BY fap_fecha DESC");
?>
	<table style='width:100%;' class='lista_small'>

		<tr class='tabla_header'>
			<td>Fecha</td>
			<td>RUT</td>
			<td>Paciente</td>
			<td>Ficha</td>
			<td>Edad</td>
			<td>Mod. Atenci&oacute;n</td>
			<td>Tipo Atenci&oacute;n</td>
			<td>Servicio Origen</td>
			<td>Previsi&oacute;n</td>
			<td>Pabell&oacute;n</td>	
			<td>Diag. Pre. Op.</td>
			<td>Diag. Post. Op.</td>	
			<td>Prestaci&oacute;n</td>
			<td>Glosa</td>
			<td>RUT Cirujano</td>
			<td>Cirujano</td>
			<td>Hallazgos Intraoperatorios</td>
			<td>Descripci&oacute;n  Operatorio</td>	
		</tr>
		
	<?php
		
		  if($query)
			for($i=0;$i<count($query);$i++) {

				($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
				
				$modo_at='';
				switch($query[$i]['modo_at']*1){
					case 0:
						$modo_at='Ambulatorio';
					break;
					case 1:
						$modo_at='Hospitalizado';
					break;
				}

				$tipo_at='';
				switch($query[$i]['tipo_at']*1){
					case 0:
						$tipo_at='Programada';
					break;
					case 1:
						$tipo_at='No programada';
					break;
					case 2:
						$tipo_at='Urgencia';
					break;
					case 3:
						$tipo_at='Compra Servicios';
					break;
					case 4:
						$tipo_at='Extensi&oacute;n Horaria';
					break;
					case 5:
						$tipo_at='Privada';
					break;
					case 6:
						$tipo_at='Pab. Electivo/Pab. Extendido';
					break;
				}

					print("<tr class='$clase' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"".$clase."\";'>
						<td style='text-align:center;'>".$query[$i]['fap_fecha']."</td>
						<td style='text-align:center;'>".$query[$i]['pac_rut']."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['pac_nombres'])."</td>
						<td style='text-align:center;'>".$query[$i]['pac_ficha']."</td>
						<td style='text-align:center;'>".$query[$i]['edad_anios']."</td>
						<td style='text-align:left;'>$modo_at</td>
						<td style='text-align:left;'>$tipo_at</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['serv_origen'])."</td>
						<td style='text-align:center;'>".$query[$i]['prev_desc']."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['fapp_desc'])."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['diag_pre'])."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['diag_post'])."</td>
						<td style='text-align:center;'>".$query[$i]['prestacion']."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['prestacion_glosa'])."</td>
						<td style='text-align:center;'>".$query[$i]['equipo_rut']."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['equipo_nombre'])."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['fap_hallazgos'])."</td>
						<td style='text-align:left;'>".htmlentities($query[$i]['fap_protocolo'])."</td>");
				
					print("</tr>");		
			}
			print("</table>");
}
?>
<script>
	
	datos_fap=<?php echo json_encode($query); ?>;
	
	if(datos_fap)
		$('cant_reg').innerHTML='Total de Registros: <b>'+datos_fap.length+'</b>';
    else
		$('cant_reg').innerHTML='<i>No hay registros.</i>';

	
</script>
