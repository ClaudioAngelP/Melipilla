<?php 

  require_once('../../conectar_db.php');

  $ord=$_POST['orden']*1;

  $orden="nomd_hora,nomd_folio,pac_appat,pac_apmat,pac_nombres";
  
	$n=false;


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
  							 WHERE nom_id=$nom_id");
  							 
  $nom_id=$n['nom_id'];
  $esp_id=$n['nom_esp_id'];
  $doc_id=$n['nom_doc_id'];
  $fecha=$n['nom_fecha'];
  
  } else {

   $nom_folio = pg_escape_string($_POST['nom_folio']);
  
   $chk=cargar_registro("
		SELECT *, nom_fecha::date FROM nomina 
  		LEFT JOIN doctores ON nom_doc_id=doc_id
  		LEFT JOIN especialidades ON nom_esp_id=esp_id
		WHERE nom_folio='$nom_folio';  
   ");

	if($chk) {
		  
		$nom_id=$chk['nom_id']; 
  		$esp_id=$chk['nom_esp_id'];
  		$doc_id=$chk['nom_doc_id'];
  		$fecha=$chk['nom_fecha'];
		 
  
		$n=cargar_registro("SELECT *, nom_fecha::date FROM nomina
  							 LEFT JOIN doctores ON nom_doc_id=doc_id
  							 LEFT JOIN especialidades ON nom_esp_id=esp_id
  							 WHERE
			nom_esp_id=$esp_id AND nom_doc_id=$doc_id AND nom_fecha::date='$fecha'
			ORDER BY nom_folio, nom_id");
  							 
  		$nom_id=$n['nom_id'];
  	
  	} /*else {
  	
		$n=cargar_registro("SELECT * FROM nomina 
  							 	LEFT JOIN doctores ON nom_doc_id=doc_id
  							 	LEFT JOIN especialidades ON nom_esp_id=esp_id		
								WHERE nom_folio='$nom_folio'", true);  	
  	
		$nom_id=$n['nom_id'];  	
  		$esp_id=$n['nom_esp_id'];
  		$doc_id=$n['nom_doc_id'];
  		$fecha=$n['nom_fecha'];
  	
  	}*/
  	
  }
  
  if(!$n) exit();
  
  $lnom=cargar_registros_obj("
		SELECT DISTINCT 
			nom_id, nom_folio,
			(SELECT COUNT(*) FROM nomina_detalle AS foo 
				WHERE foo.nom_id=nomina.nom_id) AS cantidad
		FROM nomina 
		WHERE  
		nom_esp_id=$esp_id AND nom_doc_id=$doc_id AND nom_fecha::date='$fecha'
		ORDER BY nom_folio  
  ");
  
  if($lnom AND sizeof($lnom)>1) {  
  
	  $htmlnom='Adjuntas ('.sizeof($lnom).') : 
	  		<select id="folios_nominas" name="folios_nominas" style="font-size:10px;" 
	  		onChange="abrir_nomina($(\'nom_id\').value*1, 0);">
	  		<option value="-1" SELECTED>(Todas...)</option>';  
	  
	  for($i=0;$i<sizeof($lnom);$i++) {
	  
			$htmlnom.='<option value="'.$lnom[$i]['nom_id'].'">'.$lnom[$i]['nom_folio'].' ('.$lnom[$i]['cantidad'].')</option>';  
	  	
	  }
	  
	  $htmlnom.='</select>';
  
  } else {
  	
  	$htmlnom='<i>(No hay n&oacute;minas adjuntas.)</i>';
  		
  }

  if( isset($_POST['folios_nominas']) AND 
  		$_POST['folios_nominas']*1!=-1) {

	  $lista = cargar_registros_obj("
	  SELECT 
		pacientes.*, nomina_detalle.*, diag_desc, nom_motivo, esp_recurso,
		date_part('year',age(nom_fecha::date, pac_fc_nac)) as edad,
		nom_esp_id  
	  FROM nomina_detalle
	  JOIN nomina USING (nom_id)
	  JOIN especialidades ON nom_esp_id=esp_id
	  LEFT JOIN pacientes USING (pac_id)
	  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	  WHERE nom_id=$nom_id OR nomd_nom_id=$nom_id AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL)
	  ORDER BY $orden
	  ");
	  
	  //'X',
	  
	  $nom_id=$lista[0]['nom_id']*1;
	  $esp_id=$lista[0]['nom_esp_id']*1;
	  $nom_recurso=($lista[0]['esp_recurso']=='t');

	} else {

	  $lista = cargar_registros_obj("
	  SELECT 
		pacientes.*, nomina_detalle.*, diag_desc,nom_motivo,esp_recurso, 
		date_part('year',age(pac_fc_nac)) as edad  
	  FROM nomina_detalle
	  JOIN nomina USING (nom_id)
	  JOIN especialidades ON nom_esp_id=esp_id
	  LEFT JOIN pacientes USING (pac_id)
	  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
	  WHERE
	  nom_esp_id=$esp_id AND nom_doc_id=$doc_id AND nom_fecha::date='$fecha' AND (nomd_diag_cod NOT IN ('H','T') OR nomd_diag_cod IS NULL)
	  ORDER BY $orden
	  ");
	  
	  // 'X',

	 $nom_recurso=($lista[0]['esp_recurso']=='t');


	}

	print("
		<input type='hidden' id='nom_id' name='nom_id' value='$nom_id' />
	");

	// <input type='hidden' id='esp_id' name='esp_id' value='$esp_id' />
	
	$proc=cargar_registro("SELECT * FROM procedimiento WHERE esp_id=".$esp_id);
	
	if($proc) {

		print("
			<input type='hidden' id='proc' name='proc' value='1' />
		");
		
		require_once('abrir_nomina_proc.php');
		exit(0);	
	} 
	
?>

<table style='width:100%;font-size:11px;' class='lista_small celdas'>
<tr class='tabla_header'>
<td>#</td>
<td>Hora</td>
<td>RUT/Ficha</td>
<td>Paciente</td>
<td>S</td>
<td>E</td>
<td>Tipo</td>
<td>Sobrecupo</td>
<td>Observaciones</td>
<td>S/Ficha</td>
<td>Estado</td>
<td>Pertinente<br />Prot/Tiempo</td>
<td>Alta</td>
<td>G.E.S.</td>
<td>Eliminar</td>
</tr>

<?php

    if(!$nom_recurso) $horas_html="<select id='nomd_hora' name='nomd_hora'><option value='00:00'>EXTRA</option>";
	else $horas_html="<select id='nomd_hora' name='nomd_hora'>";

	$nombrecupo=$nom_recurso?'BLOQUE':'CUPO';

	$cc=0;

  if($lista)
  for($i=0;$i<count($lista);$i++) {

    ($cc%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
 
	$cc++;
    
    if($lista[$i]['sex_id']==0)
    	$sexo='M';
    elseif($lista[$i]['sex_id']==1)
    	$sexo='F';
    else 
    	$sexo='I';
    	

	if($lista[$i]['nomd_diag_cod']=='B') {

		($cc%2==0) ? $color='#AAAAAA' : $color='#BBBBBB';

		 print("
                <tr style='height:30px;background-color:$color;'
                onMouseOver='this.style.background=\"#dddddd\";'
                onMouseOut='this.style.background=\"".$color."\";'>

                <td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($i+1)."</td>
                <td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
                <td style='text-align:center;font-weight:bold;font-size:16px;' colspan=12><i>BLOQUE OCUPADO</i></td>

		<td><center>
                <img src='iconos/delete.png'  style='cursor:pointer;'
                onClick='eliminar(".($lista[$i]['nomd_id']).")' />
                </center></td>
                
                </tr>

                ");	

		continue;
		
	}

	if($lista[$i]['pac_id']==0) {

		($cc%2==0) ? $color='#BBDDBB' : $color='#BBEEBB';
    
		if($lista[$i]['nomd_diag_cod']=='X') {

			($cc%2==0) ? $color='#ff8888' : $color='#ee8888';
			$cestado='ELIMINADO';
			$boton1='';


		} else {

		 	$ntipo=$lista[$i]['nom_motivo'];	
			if($ntipo!='') $cestado='DISPONIBLE ('.$ntipo.')';
			else $cestado='DISPONIBLE';

			$horas_html.="<option value='".substr($lista[$i]['nomd_hora'],0,5)."'>".substr($lista[$i]['nomd_hora'],0,5)."</option>";
			$boton1="<img src='iconos/pencil.png'  style='cursor:pointer;' 
			onClick='asignar(".($lista[$i]['nomd_id']).");' />";
		}
	
		print("
		<tr style='height:30px;background-color:$color'
		onMouseOver='this.style.background=\"#dddddd\";'
		onMouseOut='this.style.background=\"".$color."\";'>

		<td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($i+1)."</td>
		<td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
		<td style='text-align:center;font-weight:bold;font-size:16px;' colspan=12><i>$nombrecupo $cestado</i></td>

		<td><center>
		$boton1
		<img src='iconos/delete.png'  style='cursor:pointer;' 
		onClick='eliminar(".($lista[$i]['nomd_id']).")' />	 	
		</center></td>
		</tr>

		");
		
		
		continue;
		
	}
    
    print("
    <tr class='$clase' style='height:30px;'
    onMouseOver='this.className=\"mouse_over\";'
    onMouseOut='this.className=\"".$clase."\";'>

    <td style='text-align:right;font-weight:bold;font-size:14px;' class='tabla_header'>".($i+1)."</td>
	<td style='text-align:center;font-weight:bold;font-size:20px;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
    <td style='text-align:center;font-weight:bold;'>".($lista[$i]['pac_rut']!=''?$lista[$i]['pac_rut']:$lista[$i]['pac_ficha'])."</td>
	 ");
    
    if($ord!=2)
    print("
    <td>".htmlentities(strtoupper($lista[$i]['pac_nombres'].' '.$lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat']))."</td>
    ");    
    else 
    print("
    <td>".htmlentities(strtoupper($lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat'].' '.$lista[$i]['pac_nombres']))."</td>
    ");   
	
		print("
	 <td style='text-align:center;font-weight:bold;' 
	 id='nomd_sexo_".$lista[$i]['nomd_id']."'>".$sexo."</td>
    <td style='text-align:center;font-weight:bold;' 
    id='nomd_edad_".$lista[$i]['nomd_id']."'>".$lista[$i]['edad']."</td>
    
	 <td><center><select onChange='calcular_totales();'
	 id='nomd_tipo_".$lista[$i]['nomd_id']."' 
	 name='nomd_tipo_".$lista[$i]['nomd_id']."'>
	 <option value='N' ".($lista[$i]['nomd_tipo']=='N'?'SELECTED':'').">N</option>
	 <option value='C' ".($lista[$i]['nomd_tipo']=='C'?'SELECTED':'').">C</option>
	 <option value='P' ".($lista[$i]['nomd_tipo']=='P'?'SELECTED':'').">P</option>
	 </select></center></td>    

	 <td><center><select onChange='calcular_totales();'
	 id='nomd_extra_".$lista[$i]['nomd_id']."' 
	 name='nomd_extra_".$lista[$i]['nomd_id']."'>
	 <option value='S' ".($lista[$i]['nomd_extra']=='S'?'SELECTED':'').">Si</option>
	 <option value='N' ".($lista[$i]['nomd_extra']!='S'?'SELECTED':'').">No</option>
	 </select></center></td>    

	 <td><center>
		<input type='text'
		id='nomd_diag_".$lista[$i]['nomd_id']."' 
		name='nomd_diag_".$lista[$i]['nomd_id']."' style='width:80%;' 
		value='".htmlentities($lista[$i]['nomd_observaciones'])."' />	 
	 </center></td>

	 <td><center><select 
	 id='nomd_sficha_".$lista[$i]['nomd_id']."' 
	 name='nomd_sficha_".$lista[$i]['nomd_id']."'>
	 <option value='S' ".($lista[$i]['nomd_sficha']=='S'?'SELECTED':'').">S</option>
	 <option value='N' ".($lista[$i]['nomd_sficha']!='S'?'SELECTED':'').">N</option>
	 </select></center></td>    

	 <td><center><select onChange='calcular_totales();' style='width:100px;'
	 id='nomd_diag_cod_".$lista[$i]['nomd_id']."' 
	 name='nomd_diag_cod_".$lista[$i]['nomd_id']."'>
	 <option value='' ".($lista[$i]['nomd_diag_cod']==''?'SELECTED':'').">AGENDADO</option>
	 <option value='OK' ".($lista[$i]['nomd_diag_cod']=='OK'?'SELECTED':'').">ATENDIDO</option>
 	<option value='ALTA' ".($lista[$i]['nomd_diag_cod']=='ALTA'?'SELECTED':'').">ALTA DE ESPECIALIDAD</option>
	 <option value='NSP' ".($lista[$i]['nomd_diag_cod']=='NSP'?'SELECTED':'').">NO ATENDIDO</option>
	 <option value='X' ".($lista[$i]['nomd_diag_cod']=='X'?'SELECTED':'').">SUSPENDIDO</option>
	 </select></center></td>    


	 <td style='white-space:nowrap'><center><select
	 id='nomd_motivo_".$lista[$i]['nomd_id']."' 
	 name='nomd_motivo_".$lista[$i]['nomd_id']."'>
	 <option value='S' ".($lista[$i]['nomd_motivo'][0]!='N'?'SELECTED':'').">S</option>
	 <option value='N' ".($lista[$i]['nomd_motivo'][0]=='N'?'SELECTED':'').">N</option>
	 </select><select
         id='nomd_motivo2_".$lista[$i]['nomd_id']."'
         name='nomd_motivo2_".$lista[$i]['nomd_id']."'>
         <option value='S' ".($lista[$i]['nomd_motivo'][1]!='N'?'SELECTED':'').">S</option>
         <option value='N' ".($lista[$i]['nomd_motivo'][1]=='N'?'SELECTED':'').">N</option>
         </select></center></td>    

	 <td><center><input style='text-align:center;' size=3 
	 id='nomd_destino_".$lista[$i]['nomd_id']."' 
	 name='nomd_destino_".$lista[$i]['nomd_id']."'
	 value='".$lista[$i]['nomd_destino']."'/></center></td>    

	 <td><center><select
	 id='nomd_auge_".$lista[$i]['nomd_id']."' 
	 name='nomd_auge_".$lista[$i]['nomd_id']."'>
	 <option value='S' ".($lista[$i]['nomd_auge']=='S'?'SELECTED':'').">S</option>
	 <option value='N' ".($lista[$i]['nomd_auge']!='S'?'SELECTED':'').">N</option>
	 </select></center></td>    
   ");
   
   if($lista[$i]['nomd_via_ingreso']!='A')
		$icono='user';
	else
		$icono='calendar';
		
	print("<td style='white-space:nowrap;'><center>
	
		<img src='iconos/phone.png'  style='cursor:pointer;'
                alt='Gestiones Citaci&oacute;n' title='Gestiones Citaci&oacute;n' onClick='gestiones_citacion(".$lista[$i]['nomd_id'].");' />
	
		<img src='iconos/printer.png'  style='cursor:pointer;' 
		alt='Imprimir Citaci&oacute;n' title='Imprimir Citaci&oacute;n' onClick='imprimir_citacion(".$lista[$i]['nomd_id'].");' />	 	

		<img src='iconos/layout.png'  style='cursor:pointer;' 
		alt='Imprimir Hoja AT.' title='Imprimir Hoja AT.' onClick='imprimir_citacion2(".$lista[$i]['nomd_id'].");' />	 	

		<!--- <img src='iconos/$icono.png'  />	---->
		
	");

	
	if($lista[$i]['id_sidra']=='')
		print("
		<img src='iconos/delete.png'  style='cursor:pointer;' 
		onClick='eliminar(".($lista[$i]['nomd_id']).")' />	 	
		");
	else
		print("
		<img src='iconos/stop.png'  style='cursor:pointer;' 
		alt='SIDRA' title='SIDRA' />	 	
		");
	
    print("</center></td></tr>");
  

  }
  
  $horas_html.='</select>';
  
?>

</table>

<script>


	$('td_horas').innerHTML="<?php echo $horas_html; ?>";

	<?php if($nom_recurso) { ?>
		$('td_duracion').show(); $('duracion').value='1'; $('duracion').disabled=false;

	<?php } else { ?>
		$('td_duracion').hide(); $('duracion').disabled=true;

	<?php } ?>
	
	try {

	<?php   
	
	if(!(isset($_POST['folios_nominas']))) {
			
	?>



	$('folio_nomina').value='<?php echo $n['nom_folio']; ?>';
	$('nro_nomina').innerHTML='<?php echo $n['nom_folio']; ?>';
	$('fecha_nomina').innerHTML='<?php echo $n['nom_fecha']; ?>';
	$('medico_nomina').innerHTML='<?php echo htmlentities($n['doc_nombres'].' '.$n['doc_paterno'].' '.$n['doc_materno']); ?>';
	$('esp_nomina').innerHTML='<?php echo htmlentities($n['esp_desc']); ?>';
	$('estado_nomina').value='<?php echo $n['nom_estado_digitacion']*1; ?>';

	//$('select_nominas').innerHTML=<?php echo json_encode($htmlnom); ?>;
	//$('select_nominas').style.display='';

	lnomina=<?php echo json_encode($lnom); ?>;	
	
	<?php } ?>	
	
	dnomina=<?php echo json_encode($lista); ?>;

	if($('folio_nomina').value.substr(0,3)=='SN-')
		$('eliminar_nominas').style.display='';
	else
		$('eliminar_nominas').style.display='none';
	
	//calcular_totales();

	} catch(err) {
		
		alert(err);
			
	}

</script>
