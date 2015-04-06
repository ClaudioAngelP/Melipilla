<?php

  require_once('../../conectar_db.php');

  function img($t) {
    if($t=='t') return '<center><img src="iconos/tick.png" 
                                  width=8 height=8></center>';
    else return '<center><img src="iconos/cross.png" 
                                  width=8 height=8></center>';
  }
  
  $fecha1 = pg_escape_string($_POST['fecha1']);
  $fecha2 = pg_escape_string($_POST['fecha2']);
  $xls=(isset($_POST['xls']) AND $_POST['xls']*1==1);

  	if($xls) {
  	
  	   header("Content-type: application/vnd.ms-excel");
    	header("Content-Disposition: filename=\"Informe_FAP.xls\";");

	}

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
		'' AS edad,
		fap_suspension 		 
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
	 if($lista[$i]['fap_tipopab']==0) $lista[$i]['fap_tipopab']='ELECTIVO';
	 elseif($lista[$i]['fap_tipopab']==1) $lista[$i]['fap_tipopab']='URGENCIA';
	 elseif($lista[$i]['fap_tipopab']==2) $lista[$i]['fap_tipopab']='EXT. HORARIA';
	 elseif($lista[$i]['fap_tipopab']==3) $lista[$i]['fap_tipopab']='PRIVADO INST.';
	 elseif($lista[$i]['fap_tipopab']==4) $lista[$i]['fap_tipopab']='PRIVADO URG.';
	 elseif($lista[$i]['fap_tipopab']==-1) $lista[$i]['fap_tipopab']='SIN DIGITAR';
	 if($lista[$i]['fap_subtipopab']==0) $lista[$i]['fap_subtipopab']='AMBULATORIO';
	 elseif($lista[$i]['fap_subtipopab']==1) $lista[$i]['fap_subtipopab']='HOSPITALIZADO';
	 elseif($lista[$i]['fap_subtipopab']==-1) $lista[$i]['fap_subtipopab']='SIN DIGITAR';
	 print("<td style='text-align:left;'>".$lista[$i]['fap_tipopab']."</td>");
	 print("<td style='text-align:left;'>".$lista[$i]['fap_subtipopab']."</td>");
	 print("<td style='text-align:left;'>".$lista[$i]['fapp_desc']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['centro_nombre']."</td>");		
	 print("<td style='text-align:left;'>".$lista[$i]['centro_nombre2']."</td>");		
	
	 ?>
	 <td style='text-align:left;'>
	 <?php
	 for($j=0;$j<3;$j++)
	 print($presta[$j]['fappr_codigo']."<br>");
	 ?></td>
	 <?php
	?>
	 <td style='text-align:left;'>
	 <?php
	 for($j=0;$j<3;$j++)
	 print($presta[$j]['fappr_tipo']."<br>");
	 ?></td>
	 <?php
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
