
<table style='width:100%;font-size:11px;' class='lista_small celdas'><tr class='tabla_header'><td>Folio</td><td>Hora</td><td>Ficha</td><td>Paciente</td><td>Sexo</td><td>Edad</td><td>Tipo</td><td>Extra</td><td>Asiste</td>
<td>Destino</td><td>AUGE</td><td>Registro</td><?php if($proc['esp_informe']=='t') { ?>
<td>Informe</td><?php } ?>
<td>Eliminar</td></tr>

<?php

  if($lista)  for($i=0;$i<count($lista);$i++) {
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    
    if($lista[$i]['sex_id']==0)
    	$sexo='M';
    elseif($lista[$i]['sex_id']==1)
    	$sexo='F';
    else 
    	$sexo='I';

	if($lista[$i]['pac_id']==0) {

		print("
		<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"".$clase."\";'>

		<td style='text-align:center;font-weight:bold;'>".$lista[$i]['nomd_folio']."</td>
		<td style='text-align:center;font-weight:bold;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>
		<td style='text-align:center;font-weight:bold;' colspan=10><i>CUPO DISPONIBLE</i></td>

		<td><center>
		<img src='iconos/add.png'  style='cursor:pointer;' 
		onClick='asignar(".($lista[$i]['nomd_id']).")' />	 	
		<img src='iconos/delete.png'  style='cursor:pointer;' 
		onClick='eliminar(".($lista[$i]['nomd_id']).")' />	 	
		</center></td>
		</tr>

		");
		
		continue;
		
	}
    	
    
    print("    <tr class='$clase'    onMouseOver='this.className=\"mouse_over\";'    onMouseOut='this.className=\"".$clase."\";'>

    <td style='text-align:center;font-weight:bold;'>".$lista[$i]['nomd_folio']."</td>    <td style='text-align:center;font-weight:bold;'>".substr($lista[$i]['nomd_hora'],0,5)."</td>        <td style='text-align:center;font-weight:bold;'>".$lista[$i]['pac_ficha']."</td>	 ");
    
    if($ord!=2)
    print("    <td>".htmlentities(strtoupper($lista[$i]['pac_nombres'].' '.$lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat']))."</td>
    ");    
    else 
    print("    <td>".htmlentities(strtoupper($lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat'].' '.$lista[$i]['pac_nombres']))."</td>
    ");   
	
		print("	
	 <td style='text-align:center;font-weight:bold;' 
	 id='nomd_sexo_".$lista[$i]['nomd_id']."'>".$sexo."</td>    <td style='text-align:center;font-weight:bold;' 
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

	 <td><center><input type='checkbox' onChange='calcular_totales();'
	 id='nomd_diag_cod_".$lista[$i]['nomd_id']."' 
	 name='nomd_diag_cod_".$lista[$i]['nomd_id']."' 
	 ".(($lista[$i]['nomd_diag_cod']!='NSP')?'CHECKED':'').">
	 </center></td>

	 <td><center><input style='text-align:center;' size=3 
	 onChange='calcular_totales();'
	 id='nomd_destino_".$lista[$i]['nomd_id']."' 
	 name='nomd_destino_".$lista[$i]['nomd_id']."'
	 value='".$lista[$i]['nomd_destino']."'/></center></td>    

	 <td><center><input style='text-align:center;' size=3 
	 id='nomd_auge_".$lista[$i]['nomd_id']."' 
	 name='nomd_auge_".$lista[$i]['nomd_id']."'
	 value='".$lista[$i]['nomd_auge']."'/></center></td>    
	 
	 <td><center>
		<img src='iconos/table_edit.png'  style='cursor:pointer;' 
		onClick='registrar(".($lista[$i]['nomd_id']).");' />	 	
	 </center></td>
	");
	
	if($proc['esp_informe']=='t') 
	print("
	 <td><center>
		<img src='iconos/script_edit.png'  style='cursor:pointer;' 
		onClick='informe(".($lista[$i]['nomd_id']).");' />	 	
	 </center></td>

   ");
   
   if($lista[$i]['nomd_via_ingreso']!='A')
		$icono='user';
	else
		$icono='calendar';
		
	print("<td><center>
	<img src='iconos/$icono.png'  />	 	
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
