<?php 

	require_once('../conectar_db.php');
	
	$pac_id=$_GET['pac_id']*1;
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id;",true);
	
	$q=cargar_registros_obj("SELECT * FROM hospitalizacion 
			
			LEFT JOIN especialidades_gestion_camas ON hosp_esp_id=esp_id
			LEFT JOIN doctores ON hosp_doc_id=doc_id
			LEFT JOIN diagnosticos ON diag_cod=hosp_diag_cod	
			LEFT JOIN tipo_camas ON
				cama_num_ini<=hosp_cama_egreso AND cama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t1 ON 
				t1.tcama_num_ini<=hosp_cama_egreso AND t1.tcama_num_fin>=hosp_cama_egreso
			LEFT JOIN clasifica_camas AS t2 ON 
				t2.tcama_num_ini<=hosp_servicio AND t2.tcama_num_fin>=hosp_servicio
						
			WHERE hosp_pac_id=$pac_id ORDER BY COALESCE(hosp_fecha_egr,'01/01/2050') DESC;");


?>

<html>
<title>Res&uacute;men de Altas por Paciente</title>

<?php cabecera_popup('..'); ?>

<body class='fuente_por_defecto popup_background'>

<table style='width:100%;'>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>RUT:</td>
		<td style='font-weight:bold;'><?php echo $pac['pac_rut']; ?></td>
	</tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Ficha:</td>
		<td style='font-weight:bold;'><?php echo $pac['pac_ficha']; ?></td>
	</tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Nombre Completo:</td>
		<td><?php echo $pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']; ?></td>
	</tr>
</table>

<table style='font-size:12px;width:100%;'>
	<tr class='tabla_header'>
		<td>M&eacute;dico Tratante</td>
		<td>Especialidad</td>
		<td>Fecha Ingreso</td>
		<td>Fecha Egreso</td>
		<td>Condici&oacute;n Egreso</td>
	</tr>
	
	
<?php 

	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		if($q[$i]['esp_desc']!='')
			$especialidad=$q[$i]['esp_desc'];
		else
			$especialidad='<i>(Sin Asignar...)</i>';
			
		if($q[$i]['doc_rut']!='')
			$med_tratante=$q[$i]['doc_paterno']." ".$q[$i]['doc_materno']." ".$q[$i]['doc_nombres'];
		else
			$med_tratante='<i>(Sin Asignar...)</i>';

		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		$v=$q[$i]['hosp_condicion_egr']*1;
		
		$destino='';
			
			switch($v) {
				
				case 0: $destino='(Hospitalizado...)'; break;
				case 1: $destino='Alta a Domicilio'; break;
				case 2: $destino='Derivaci&oacute;n'; break;
				case 3: $destino='Fallecido'; break;
				case 4: $destino='Fugado'; break;
				case 5: $destino='Otro (<i>'.$q[$i]['hosp_otro_destino'].'</i>)'; break;
				
			}

		
		print("
			<tr class='$clase'>
			<td>".trim($med_tratante)."</td>
			<td>".$especialidad."</td>
			<td style='text-align:center;'>".substr($q[$i]['hosp_fecha_ing'],0,16)."</td>
			<td style='text-align:center;'>".substr($q[$i]['hosp_fecha_egr'],0,16)."</td>
			<td>".$destino."</td>
			</tr>
		");
		
	}

?>	
	
</table>

</body>
</html>
