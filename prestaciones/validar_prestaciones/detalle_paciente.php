<?php 

	require_once('../../conectar_db.php');
	
	$fecha=$_POST['fecha1'];
	$pac_id=$_POST['pac_id']*1;
	
	
	$pac=cargar_registro("SELECT * FROM pacientes 
									LEFT JOIN comunas USING (ciud_id)							      LEFT JOIN provincias USING (prov_id)							      LEFT JOIN regiones USING (reg_id)							      LEFT JOIN sexo USING (sex_id)							      LEFT JOIN grupo_sanguineo USING (sang_id)							      LEFT JOIN grupos_etnicos USING (getn_id)							      LEFT JOIN prevision USING (prev_id)
								 WHERE pac_id=$pac_id", true);

	if($pac['prev_desc']=='') {
			$pac['prev_desc']='<i>Indefinida</i>';
			$pac['prev_id']=1;
	}
	
	$prevhtml=desplegar_opciones_sql(
		"SELECT prev_id, prev_desc FROM prevision ORDER BY prev_id", $pac['prev_id']	
	);
	
?>


<div class='sub-content'>

<table style='width:100%;'>

<tr><td style='width:150px;text-align:right;' class='tabla_fila2'>
R.U.T.:
</td><td class='tabla_fila' style='text-align:left;font-size:16px;font-weight:bold;'>
<?php echo $pac['pac_rut']; ?>
</td><td style='width:150px;text-align:right;' class='tabla_fila2'>
Nro. Ficha:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;font-weight:bold;'>
<?php echo $pac['pac_ficha']; ?>
</td></tr>

<tr><td style='width:100px;text-align:right;' class='tabla_fila2'>
Nombre Paciente:
</td><td colspan=3 class='tabla_fila' style='text-align:left;font-weight:bold;font-size:16px;'>
<?php echo $pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']; ?>
</td></tr>

<tr><td style='width:150px;text-align:right;' class='tabla_fila2'>
Fecha de Nac.:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;'>
<?php echo $pac['pac_fc_nac']; ?>
</td><td style='width:150px;text-align:right;' class='tabla_fila2'>
Edad:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;font-weight:bold;'>
<?php echo $edad; ?>
</td></tr>

<tr><td style='width:150px;text-align:right;' class='tabla_fila2'>
Previsi&oacute;n:
</td><td class='tabla_fila' style='text-align:center;font-size:16px;'>
<?php echo $pac['prev_desc']; ?>
</td><td class='tabla_fila2' style='text-align:right;'>
Sincronizar SIGGES:
</td><td class='tabla_fila'>
...
</td></tr>
<tr><td style='width:150px;text-align:right;' class='tabla_fila2'>
Casos AUGE:
</td><td id='casos' class='tabla_fila' colspan=3>
<img src='imagenes/ajax-loader1.gif' />
</td></tr>
</table>

<center>
<input type='button' id='' name='' value='--- Registro Monitoreo GES ---' 
onClick='registro_monitoreo_ges();' />

</center>

</div>

<div id='lista_prestaciones'>

</div>

<script> 

	lista_prestaciones(<?php echo $pac_id.', '.$pac['prev_id']; ?>); 
	cargar_casos(<?php echo $pac_id; ?>);
	
</script>