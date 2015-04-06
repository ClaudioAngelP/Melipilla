<?php 

	require_once('../../conectar_db.php');
	
	$pac_id=$_GET['pac_id']*1;
	
	$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_id=$pac_id;");
	
	$pac_ficha=pg_escape_string($pac['pac_ficha']);
	
	$m=cargar_registros_obj("SELECT *, 
	(SELECT esp_desc FROM especialidades WHERE esp_id=origen_esp_id) AS esp_desc_origen, 
	(SELECT doc_paterno || ' ' || doc_materno || ' ' || doc_nombres FROM doctores WHERE doc_id=origen_doc_id) AS doc_desc_origen, 
	(SELECT esp_desc FROM especialidades WHERE esp_id=destino_esp_id) AS esp_desc_destino, 
	(SELECT doc_paterno || ' ' || doc_materno || ' ' || doc_nombres FROM doctores WHERE doc_id=destino_doc_id) AS doc_desc_destino
	FROM archivo_movimientos 
	LEFT JOIN funcionario ON am_func_id=func_id
	WHERE pac_ficha='$pac_ficha' AND pac_id=$pac_id
	ORDER BY am_fecha DESC;", true);

?>

<html>
<title>Historial de Movimientos Ficha Paciente</title>

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../../iconos/script.png'><b>Historial de Movimientos Ficha Paciente</b>
</div>

<table style='font-size:18px;'>
<tr><td style='text-align:right;' class='tabla_fila2'>Ficha Paciente:</td><td style='font-size:28px;'><?php echo ($pac['pac_ficha']); ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>R.U.N.:</td><td><?php echo formato_rut($pac['pac_rut']); ?></td></tr>
<tr><td style='text-align:right;' class='tabla_fila2'>Nombre Completo:</td><td><?php echo ($pac['pac_nombres'].' '.$pac['pac_appat'].' '.$pac['pac_apmat']); ?></td></tr>
</table>

<table style='width:100%;font-size:10px;' cellpadding=1 cellspacing=0>

<tr class='tabla_header'>
<td>#</td>
<td>Fecha/Hora</td>
<td>Local Origen</td>
<td>Prof./Serv. Origen</td>
<td>&nbsp;</td>
<td>Local Destino</td>
<td>Prof./Serv. Destino</td>
<td>Funcionario</td>
<td>Estado</td>
</tr>

<?php

  $opts=Array('Solicitada', 'Retirada', 'Enviada', 'Recepcionada', 'Devuelta', 'Extraviada');
  $opts_color=Array('black','yellowgreen','yellowgreen','purple','green','red');


	for($i=0;$i<sizeof($m);$i++) {
	
		$clase=($i%2==0?'tabla_fila':'tabla_fila2');
		
		$estado=$opts[$m[$i]['am_estado']*1];
		
		if($i==0)
			$style='background-color:gray;color:black;border:1px solid black;';
		else
			$style='';
		
		print("
			<tr class='$clase' style='$style'>
			<td style='text-align:center;font-weight:bold;font-size:20px;'>".(sizeof($m)-$i)."</td>
			<td style='text-align:center;font-weight:bold;'>".substr($m[$i]['am_fecha'],0,16)."</td>
		");
		
		if($m[$i]['esp_desc_origen']!='' AND $m[$i]['doc_desc_origen']!='')
			print("
			<td style='text-align:left;'>".$m[$i]['esp_desc_origen']."</td>
			<td style='text-align:left;'>".$m[$i]['doc_desc_origen']."</td>
			");
		else
			print("
			<td style='text-align:center;font-weight:bold;font-size:18px;' colspan=2>Archivo</td>
			");
			
		print("<td style='font-size:18px;'>&gt;</td>");
		
		
		if($m[$i]['esp_desc_destino']!='' AND $m[$i]['doc_desc_destino']!='')
			print("
			<td style='text-align:left;font-weight:bold;'>".$m[$i]['esp_desc_destino']."</td>
			<td style='text-align:left;font-weight:bold;'>".$m[$i]['doc_desc_destino']."</td>
			");
		
		else
			print("
			<td style='text-align:center;font-weight:bold;font-size:18px;' colspan=2>Archivo</td>
			");
		
		print("
			<td style='text-align:left;'>".$m[$i]['func_nombre']."</td>
			<td style='text-align:center;font-weight:bold;font-size:18px;'>".$estado."</td>
			</tr>
		");
	
	}

?>


</table>

</body>
</html>