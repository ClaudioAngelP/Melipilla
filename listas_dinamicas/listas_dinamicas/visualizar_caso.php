<?php 

	require_once('../conectar_db.php');
	
	$caso_id=$_GET['caso_id']*1;

	$m=cargar_registro("SELECT * FROM monitoreo_ges WHERE mon_id=(SELECT mon_id FROM lista_dinamica_caso WHERE caso_id=$caso_id LIMIT 1)", true);
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='".$m['mon_rut']."'");
	
	$c=cargar_registros_obj("SELECT *, date_trunc('second', in_fecha) AS fecha FROM lista_dinamica_instancia 
						JOIN lista_dinamica USING (lista_id)
						LEFT JOIN funcionario USING (func_id)
						WHERE caso_id=$caso_id ORDER BY in_fecha ASC;");

?>

<html>

<title>Visualizar Caso #<?php echo $caso_id; ?></title>

<?php cabecera_popup('..'); ?>

<body class='fuente_por_defecto popup_background'>

<input type='hidden' id='mon_id' name='mon_id' value='<?php echo $mon_id; ?>' />

<table style='width:100%;font-size:13px;'>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>R.U.T.:</td>
<td class='tabla_fila' style='font-size:14px;' colspan=3><b><?php echo $m['mon_rut']; ?></b> [<i>Ficha:</i> <b><u><?php echo $pac['pac_ficha']; ?></u></b>]
<!--- <img src='../../iconos/magnifier.png' onClick='abrir_ficha(<?php echo $pac['pac_id']*1; ?>);' style='cursor:pointer;' /> -->
</td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Nombre:</td>
<td class='tabla_fila' style='font-weight:bold;' colspan=3><?php echo $m['mon_nombre']; ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Patolog&iacute;a:</td>
<td class='tabla_fila'><i><?php echo $m['mon_patologia']; ?></i></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Garant&iacute;a:</td>
<td class='tabla_fila'><i><?php echo $m['mon_garantia']; ?></i></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha de Inicio:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_inicio']; ?></td>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Fecha L&iacute;mite:</td>
<td class='tabla_fila'><?php echo $m['mon_fecha_limite']; ?></td>
</tr>

<tr>
<td class='tabla_fila2' style='width:15%;text-align:right;'>Estado Actual:</td>

<?php 
	
	switch($m['mon_condicion']) {
		case 0: echo '<td class="tabla_fila" style="color:blue;" colspan=3>GARANTIA VIGENTE</td>'; break;
		case 1: echo '<td class="tabla_fila" style="color:red;" colspan=3>GARANTIA VENCIDA</td>'; break;
		case 2: echo '<td class="tabla_fila" style="color:black;" colspan=3>GARANTIA CERRADA</td>'; break;
	} 
	
?>

</tr>

</table>


<div class='sub-content'>
<img src='../iconos/clock.png' />
Historial de Eventos
</div>

<div class='sub-content2'>

<table style='width:100%;font-size:14px;'>

<?php 

	for($i=0;$i<sizeof($c);$i++) {
		
		print("
		
		<tr><td rowspan=3 class='tabla_header'
		style='width:30px;text-align:center;font-weight:bold;font-size:24px;'>".($i+1)."</td>
		
		
		<td style='text-align:right;' class='tabla_fila2'>Evento:</td>
		<td class='tabla_fila' style='font-weight:bold;'>".$c[$i]['lista_nombre']."</td>
		</tr>
		
		<tr>
		<td style='text-align:right;width:100px;' class='tabla_fila2'>Fecha:</td>
		<td class='tabla_fila'>".$c[$i]['fecha']."</td>
		</tr>

		
		");
		
		if($c[$i]['func_nombre']!='')
		
			print("
			
			<tr>
			<td style='text-align:right;' class='tabla_fila2'>Funcionario:</td>
			<td class='tabla_fila'>".$c[$i]['func_nombre']."</td>
			</tr>
			
			");
			
		else
		
			print("
			
			<tr>
			<td style='text-align:right;' class='tabla_fila2'>Funcionario:</td>
			<td class='tabla_fila'><i>(No ejecutado...)</i></td>
			</tr>
			
			");
		
	}

?>

</table>

</div>

</body>


</html>
