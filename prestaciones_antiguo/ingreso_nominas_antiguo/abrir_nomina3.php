<?php 

  require_once('../../conectar_db.php');

  if( isset($_POST['nom_id']) ) {
  
  if( isset($_POST['folios_nominas']) AND 
  		$_POST['folios_nominas']*1!=-1) {

  	$nom_id = pg_escape_string($_POST['folios_nominas']*1);
  
  } else { 
  	
  	$nom_id = pg_escape_string($_POST['nom_id']*1);
  
  }
  
  $n=cargar_registro("SELECT *, nom_fecha::date FROM nomina
  							 LEFT JOIN doctores ON nom_doc_id=doc_id
  							 LEFT JOIN especialidades ON nom_esp_id=esp_id
  							 WHERE nom_id=$nom_id", true);
  							 
  $esp_id=$n['nom_esp_id'];
  
  } else {

   $nom_folio = pg_escape_string($_POST['nom_folio']);
  
   $chk=cargar_registro("
		SELECT * FROM nomina_detalle WHERE nomd_folio='$nom_folio';  
   ");

	if($chk) {
		  
		$nom_id=$chk['nom_id'];  
  
   	$n=cargar_registro("SELECT *, nom_fecha::date FROM nomina
  							 LEFT JOIN doctores ON nom_doc_id=doc_id
  							 LEFT JOIN especialidades ON nom_esp_id=esp_id
  							 WHERE nom_id=$nom_id", true);
  							 
  		$nom_id=$n['nom_id'];
  		$esp_id=$n['nom_esp_id'];
  	
  	} else {
  	
		$n=cargar_registro("SELECT * FROM nomina 
  							 	LEFT JOIN doctores ON nom_doc_id=doc_id
  							 	LEFT JOIN especialidades ON nom_esp_id=esp_id		
								WHERE nom_folio='$nom_folio'", true);  	
  	
		$nom_id=$n['nom_id'];  	
  		$esp_id=$n['nom_esp_id'];
  	
  	}
  	
  }
  
  if(!$n) exit();
  $lnom=cargar_registros_obj("
		SELECT DISTINCT 
			nomd_nom_id, nomd_folio,
			(SELECT COUNT(*) FROM nomina_detalle AS foo 
				WHERE foo.nomd_nom_id=nomina_detalle.nomd_nom_id) AS cantidad
		FROM nomina_detalle 
		WHERE nom_id=$nom_id AND 
				NOT nomd_folio='' AND 
				NOT nomd_folio='0'
		ORDER BY nomd_folio  
  ");
  
  if($lnom AND sizeof($lnom)>1) {  
  
	  $htmlnom='Adjuntas ('.sizeof($lnom).') : 
	  		<select id="folios_nominas" name="folios_nominas" 
	  		onChange="abrir_nomina($(\'nom_id\').value*1, 0);">
	  		<option value="-1" SELECTED>(Todas...)</option>';  
	  
	  for($i=0;$i<sizeof($lnom);$i++) {
	  
			$htmlnom.='<option value="'.$lnom[$i]['nomd_nom_id'].'">'.$lnom[$i]['nomd_folio'].' ('.$lnom[$i]['cantidad'].')</option>';  
	  	
	  }
	  
	  $htmlnom.='</select>';
  
  } else {
  	
  	$htmlnom='<i>(No hay n&oacute;minas adjuntas.)</i>';
  		
  }

  if( isset($_POST['folios_nominas']) AND 
  		$_POST['folios_nominas']*1!=-1) {

	  $lista = cargar_registros_obj("
	  SELECT 
		pacientes.*, nomina_detalle.*, diag_desc,
		date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
		nom_esp_id  
	  FROM nomina_detalle
	  JOIN nomina USING (nom_id)
	  JOIN pacientes USING (pac_id)
	  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	  WHERE nomd_nom_id=$nom_id	  ORDER BY nomd_folio, 
	  (CASE WHEN trim(both from pac_ficha)='' THEN '0' 
	  	ELSE pac_ficha END)::bigint
	  ");
	  
	  $nom_id=$lista[0]['nom_id']*1;
	  $esp_id=$lista[0]['nom_esp_id']*1;

	} else {

	  $lista = cargar_registros_obj("
	  SELECT 
		pacientes.*, nomina_detalle.*, diag_desc, 
		date_part('year',age(pac_fc_nac)) as edad  
	  FROM nomina_detalle
	  JOIN pacientes USING (pac_id)
	  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	  WHERE nom_id=$nom_id	  ORDER BY nomd_folio, 
	  (CASE WHEN trim(both from pac_ficha)='' THEN '0' 
	  	ELSE pac_ficha END)::bigint
	  ");

	}


	print("
		<input type='hidden' id='nom_id' name='nom_id' value='$nom_id' />
		<input type='hidden' id='esp_id' name='esp_id' value='$esp_id' />
	");
	
	$proc=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=".$esp_id);
	
	if($proc) {

		print("
			<input type='hidden' id='proc' name='proc' value='1' />
		");
		
		require_once('abrir_nomina_proc.php');
		exit(0);	
	} 
	
?>


<table style='width:100%;font-size:11px;' class='lista_small celdas'><tr class='tabla_header'><td>Folio</td><td>Ficha</td><td>Paciente</td><td>S</td><td>E</td><td>Tipo</td><td>P/Extra</td><td>Diagn&oacute;stico</td><td>Pertinencia</td><td>CIE10</td><td>Motivo</td><td>Destino</td><td>AUGE</td><td>Eliminar</td></tr>

<?php

  if($lista)  for($i=0;$i<count($lista);$i++) {
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    if($lista[$i]['sex_id']==0)
    	$sexo='M';
    elseif($lista[$i]['sex_id']==1)
    	$sexo='F';
    else 
    	$sexo='I';
    	
    
    print("    <tr class='$clase'    onMouseOver='this.className=\"mouse_over\";'    onMouseOut='this.className=\"".$clase."\";'>

    <td style='text-align:center;font-weight:bold;'>".$lista[$i]['nomd_folio']."</td>        <td style='text-align:center;font-weight:bold;'>".$lista[$i]['pac_ficha']."</td>    <td>".htmlentities(strtoupper($lista[$i]['pac_nombres'].' '.$lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat']))."</td>    
	
	 <td style='text-align:center;font-weight:bold;' 
	 id='nomd_sexo_".$lista[$i]['nomd_id']."'>".$sexo."</td>    <td style='text-align:center;font-weight:bold;' 
    id='nomd_edad_".$lista[$i]['nomd_id']."'>".$lista[$i]['edad']."</td>    
	 <td><center><select onChange='calcular_totales();'
	 id='nomd_tipo_".$lista[$i]['nomd_id']."' 
	 name='nomd_tipo_".$lista[$i]['nomd_id']."'>
	 <option value='N' ".($lista[$i]['nomd_tipo']!='C'?'SELECTED':'').">N</option>
	 <option value='C' ".($lista[$i]['nomd_tipo']=='C'?'SELECTED':'').">C</option>
	 </select></center></td>    

	 <td><center><select onChange='calcular_totales();'
	 id='nomd_extra_".$lista[$i]['nomd_id']."' 
	 name='nomd_extra_".$lista[$i]['nomd_id']."'>
	 <option value='S' ".($lista[$i]['nomd_extra']=='S'?'SELECTED':'').">Si</option>
	 <option value='N' ".($lista[$i]['nomd_extra']!='S'?'SELECTED':'').">No</option>
	 </select></center></td>    

	 <td><center>
		<input type='text' readonly='1'
		id='nomd_diag_".$lista[$i]['nomd_id']."' 
		name='nomd_diag_".$lista[$i]['nomd_id']."' style='width:100%;' 
		value='".htmlentities($lista[$i]['diag_desc'])."' />	 
	 </center></td>

	 <td><center><select 
	 id='nomd_sficha_".$lista[$i]['nomd_id']."' 
	 name='nomd_sficha_".$lista[$i]['nomd_id']."'>
	 <option value='S' ".($lista[$i]['nomd_sficha']=='S'?'SELECTED':'').">Si</option>
	 <option value='N' ".($lista[$i]['nomd_sficha']!='S'?'SELECTED':'').">No</option>
	 </select></center></td>    

	 <td><center>
		<input type='text' style='text-align:center;' 
		onChange='cargar_diagnostico(".$lista[$i]['nomd_id'].");'
		id='nomd_diag_cod_".$lista[$i]['nomd_id']."' 
		name='nomd_diag_cod_".$lista[$i]['nomd_id']."' 
		size=5 value='".$lista[$i]['nomd_diag_cod']."' />
	</center></td>


	 <td><center><input style='text-align:center;' size=3 
	 id='nomd_motivo_".$lista[$i]['nomd_id']."' 
	 name='nomd_motivo_".$lista[$i]['nomd_id']."'
	 value='".$lista[$i]['nomd_motivo']."'/></center></td>    

	 <td><center><input style='text-align:center;' size=3 
	 onChange='calcular_totales();'
	 id='nomd_destino_".$lista[$i]['nomd_id']."' 
	 name='nomd_destino_".$lista[$i]['nomd_id']."'
	 value='".$lista[$i]['nomd_destino']."'/></center></td>    

	 <td><center><input style='text-align:center;' size=3 
	 id='nomd_auge_".$lista[$i]['nomd_id']."' 
	 name='nomd_auge_".$lista[$i]['nomd_id']."'
	 value='".$lista[$i]['nomd_auge']."'/></center></td>    
   ");
   
   if($lista[$i]['nomd_via_ingreso']!='A')
		print("<td><center>
			<img src='iconos/delete.png'  style='cursor:pointer;' 
			onClick='eliminar(".($lista[$i]['nomd_id']).")' />	 	
	 	</center></td>");
	 else 	 	
		print("<td><center>
			<img src='iconos/tick.png'  />	 	
	 	</center></td>");
	
    print("</tr>");
  
  }
  
?>

</table>

<script>

	try {

	<?php   
	
	if(!(isset($_POST['folios_nominas']))) {
			
	?>

	$('folio_nomina').value='<?php echo $n['nom_folio']; ?>';
	$('nro_nomina').innerHTML='<?php echo $n['nom_folio']; ?>';
	$('fecha_nomina').innerHTML='<?php echo $n['nom_fecha']; ?>';
	$('medico_nomina').innerHTML='<?php echo $n['doc_nombres'].' '.$n['doc_paterno'].' '.$n['doc_materno']; ?>';
	$('esp_nomina').innerHTML='<?php echo ($n['esp_desc']); ?>';
	$('estado_nomina').value='<?php echo $n['nom_estado_digitacion']*1; ?>';

	$('select_nominas').innerHTML=<?php echo json_encode($htmlnom); ?>;
	$('select_nominas').style.display='';

	lnomina=<?php echo json_encode($lnom); ?>;	
	
	<?php } ?>	
	
	dnomina=<?php echo json_encode($lista); ?>;

	if($('folio_nomina').value.substr(0,3)=='SN-')
		$('eliminar_nominas').style.display='';
	else
		$('eliminar_nominas').style.display='none';
	
	calcular_totales();

	} catch(err) {
		
		alert(err);
			
	}

</script>
