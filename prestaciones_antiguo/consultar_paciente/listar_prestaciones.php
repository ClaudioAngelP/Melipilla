<?php 

	require_once('../../conectar_db.php');

	$tipo=$_POST['tipo']*1;
	$busca=pg_escape_string(utf8_decode($_POST['busca']));

	if($tipo==0) {
		$busca=str_replace('.','',trim($busca));
		$pac_w="pac_rut='$busca'";	
	} elseif($tipo==1) {
		$busca*=1;
		$pac_w="pac_ficha='$busca'";
	} elseif($tipo==2) {
		$busca=trim($busca);
		$pac_w="nom_folio='$busca'";
	}

  $pac = cargar_registro("SELECT * FROM pacientes 
							LEFT JOIN comunas USING (ciud_id)
							WHERE $pac_w ", true);

	if(!$pac) {
		exit("
			<script>
				alert('Paciente no encontrado.');
			</script>		
		");	
	}

  $lista = cargar_registros_obj("
  SELECT 
	nom_fecha::date, nomd_hora, doc_rut, doc_paterno, doc_materno, doc_nombres, 
	COALESCE(diag_desc, cancela_desc) AS diag_desc, nomd_diag_cod,
	esp_desc  
  FROM nomina_detalle
  JOIN nomina USING (nom_id)
  JOIN pacientes USING (pac_id)
  LEFT JOIN diagnosticos ON diag_cod=nomd_diag_cod
  LEFT JOIN doctores ON nom_doc_id=doc_id
  LEFT JOIN especialidades ON nom_esp_id=esp_id 
  LEFT JOIN nomina_codigo_cancela ON nomd_codigo_cancela=cancela_id
  WHERE $pac_w  
    ORDER BY nomina.nom_fecha DESC, nomd_hora
  ");
  
  if($lista AND $tipo==2) {

	$nom_id=$lista[0]['nom_id']*1;
  
  	$n=cargar_registro("SELECT *, nom_fecha::date FROM nomina
  							 LEFT JOIN doctores ON nom_doc_id=doc_id
  							 LEFT JOIN especialidades ON nom_esp_id=esp_id
  							 WHERE nom_id=$nom_id", true);

	print("
		<div class='sub-content'>
		<table style='width:100%;'>
		<tr><td style='text-align:right;width:30%;'>Nro. N&oacute;mina:</td><td style='font-weight:bold;'>".$n['nom_folio']."</td></tr>		
		<tr><td style='text-align:right;'>Fecha:</td><td>".$n['nom_fecha']."</td></tr>		
		<tr><td style='text-align:right;'>Especialidad:</td><td>".$n['esp_desc']."</td></tr>		
		<tr><td style='text-align:right;'>Profesional:</td><td>".$n['doc_nombres'].' '.$n['doc_paterno'].' '.$n['doc_materno']."</td></tr>		
		</table>	
		</div>
	");
  	
  }
  
?>

<input type='hidden' id='nom_id' name='nom_id' value='<?php echo $nom_id; ?>' />

<?php if($tipo!=2) { ?>

<table style='width:100%;font-size:18px;'>

<tr>

<td style='text-align:right;' class='tabla_fila2'>RUT:</td>
<td class='tabla_fila' style='font-size:16px;'><?php echo $pac['pac_rut']; ?></td>
<td style='text-align:right;' class='tabla_fila2'>Ficha:</td>
<td class='tabla_fila' style='font-size:16px;'><?php echo $pac['pac_ficha']; ?></td>

</tr>

<tr>

<td style='text-align:right;' class='tabla_fila2'>
Nombre:
</td>
<td class='tabla_fila' colspan=3 style='font-size:18px;'>
<?php echo trim($pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']); ?>
</td>

</tr>

<tr>

<td style='text-align:right;' class='tabla_fila2'>
Tel&eacute;fono Fijo:
</td>
<td class='tabla_fila' style='font-size:18px;'>
<?php echo trim($pac['pac_fono']); ?>
</td>
<td style='text-align:right;' class='tabla_fila2'>
Tel&eacute;fono Celular:
</td>
<td class='tabla_fila' style='font-size:18px;'>
<?php echo trim($pac['pac_celular']); ?>
</td>

</tr>


<tr>

<td style='text-align:right;' class='tabla_fila2'>
Direcci&oacute;n:
</td>
<td class='tabla_fila' style='font-size:18px;'>
<?php echo trim($pac['pac_direccion']); ?>
</td>
<td style='text-align:right;' class='tabla_fila2'>
Ciudad:
</td>
<td class='tabla_fila' style='font-size:18px;'>
<?php echo trim($pac['ciud_desc']); ?>
</td>

</tr>

</table>

<?php } ?>

<table style='width:100%;font-size:11px;' class='lista_small celdas'><tr class='tabla_header'><td>Fecha/Hora</td>
<td>Especialidad</td>
<?php if($tipo!=2) { ?>	<td>RUT</td>	<td>Profesional</td>
<?php } else { ?>
	<td>Nro. Ficha</td>	<td>RUT Paciente</td>	<td>Nombre Paciente</td>
<?php } ?>
<td>Tipo</td><td>Extra</td><td style='width:150px;'>Diagn&oacute;stico</td><td>S/Ficha</td><td>CIE10</td><td>Motivo</td><td>Destino</td><td>AUGE</td></tr>

<?php

  if($lista)  for($i=0;$i<count($lista);$i++) {
    ($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
    print("    <tr class='$clase'    onMouseOver='this.className=\"mouse_over\";'    onMouseOut='this.className=\"".$clase."\";'>
	 <td><center>".$lista[$i]['nom_fecha']."<br />".substr($lista[$i]['nomd_hora'],0,5)."</center></td>    
	 <td><center>".htmlentities($lista[$i]['esp_desc'])."</center></td>    
	 ");
	 
	 if($tipo!=2)
	 	print("
    		<td style='text-align:right;font-weight:bold;'>".formato_rut($lista[$i]['doc_rut'])."</td>    		<td>".htmlentities(strtoupper($lista[$i]['doc_nombres'].' '.$lista[$i]['doc_paterno'].' '.$lista[$i]['doc_materno']))."</td>
    	");    
    else 
	 	print("
    		<td style='text-align:right;font-weight:bold;'>".($lista[$i]['pac_ficha'])."</td>    		<td style='text-align:right;font-weight:bold;'>".formato_rut($lista[$i]['pac_rut'])."</td>    		<td>".htmlentities(strtoupper($lista[$i]['pac_nombres'].' '.$lista[$i]['pac_appat'].' '.$lista[$i]['pac_apmat']))."</td>
    	");        

	 print("
	 <td><center>".$lista[$i]['nomd_tipo']."</center></td>    
	 <td><center>".$lista[$i]['nomd_extra']."</center></td>    
	 ");
	 
	 if($lista[$i]['nomd_diag_cod']=='X') {
		 $color='#FF0000;';
	 } else {
		 $color='';
	 }
	 
	 print("
	 <td style='color:$color'><center>".htmlentities($lista[$i]['diag_desc'])."</center></td>
	 ");
	 
	 print("
	 <td><center>".$lista[$i]['nomd_sficha']."</center></td>    
	 ");
	 
	 print("
	 <td style='color:$color'><center>".$lista[$i]['nomd_diag_cod']."</center></td>
	 ");
	 
	 print("
	 <td><center>".$lista[$i]['nomd_motivo']."</center></td>    
	 <td><center>".$lista[$i]['nomd_destino']."</center></td>    
	 <td><center>".$lista[$i]['nomd_auge']."</center></td>    

    </tr>    ");
  
  }
  
?>

</table>
