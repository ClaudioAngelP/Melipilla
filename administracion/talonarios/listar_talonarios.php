<?php
	require_once('../../conectar_db.php');
	if($_GET['ver_talonario']==1) {
		$verfunc=true;
	} else {
		$verfunc=false;
	}
	$numero_receta=$_GET['n_receta'];
	$doc_id=$_GET['doc_id'];
?>
<table width=100%>
	<tr class='tabla_header' style='font-weight: bold;'>
		<td rowspan=2>Fecha</td>
		<td>Talonario</td>
		<?php 
		if($verfunc)
			print('<td colspan=2>Funcionario/M&eacute;dico Responsable</td>');
		else
			print('<td rowspan=2>Centro de Costo</td>');
		?>
		<td colspan=2>Numeraci&oacute;n</td>
		<td rowspan=2>Estado</td>
		<td rowspan=2>Entrega</td>
		<?php if (_cax(30251)){ ?> <td rowspan=2>Editar</td> <?php } ?>
		<?php if (_cax(30250)){ ?> <td rowspan=2>Eliminar</td> <?php } ?>
	</tr>
	<tr class='tabla_header' style='font-weight: bold;'>
		<td>Tipo</td>
		<!--<td>Folio</td>-->
		<?php 
		if($verfunc)
			print('<td>RUT</td><td>Nombre</td>');
		?>
		<td>Inicial</td>
		<td>Final</td>
	</tr>
	<?php
	$tipo_q='true';
	$estado_q='true';
	$numero_q='true';
	$doc_q='true';
	if($_GET['tipo_talonario']!=-1) {
		$tipo_q = "talonario_tipotalonario_id=".($_GET['tipo_talonario']*1);
	}
	if($_GET['estado_talonario']!=-1) {
		$estado_q = "talonario_estado=".($_GET['estado_talonario']*1);
	}
	if ($numero_receta!=''){
		$numero_q=$numero_receta.">=talonario_inicio AND ".$numero_receta."<=talonario_final";
	}
	if($_GET['doc_id']!=''){
		$doc_q=" talonario_func_id=$doc_id";
	}
	
	$consulta="
	SELECT 
	talonario_id,
	date_trunc('second', talonario_fecha),
	tipotalonario_nombre_corto,
	doc_rut,
	(doc_paterno || ' ' || doc_materno || ' ' || doc_nombres),
	talonario_inicio,
	talonario_final,
	talonario_estado,
	centro_nombre,
	tipotalonario_funcionario,
	talonario_numero,
	func_nombre
	FROM talonario
	LEFT JOIN doctores ON talonario_func_id=doc_id
	LEFT JOIN centro_costo ON centro_ruta=talonario_centro_ruta
	JOIN receta_tipo_talonario ON talonario_tipotalonario_id=tipotalonario_id
	LEFT JOIN funcionario ON func_id=talonario_func_id2
	WHERE
	$tipo_q AND
	$estado_q AND
	$numero_q AND
	$doc_q
	ORDER BY talonario_fecha DESC";
	
	//print($consulta);
	
	$talonarios_q = pg_query($consulta);
	
	
	// <td style='text-align: right;'>".$talonario_a[3]."</td>
	for($i=0;$i<pg_num_rows($talonarios_q);$i++) {
		$talonario_a = pg_fetch_row($talonarios_q);
		($i%2==0) ? $clase='tabla_fila' : $clase='tabla_fila2';
		switch($talonario_a[7]) {
			case 1: $estado_talonario='Activo'; break;
			case 2: $estado_talonario='Inactivo'; break;
			default: $estado_talonario='Sin Asignar'; break;
		}
		print("
		<tr class='".$clase."' id='fila_talonario_".$talonario_a[0]."' onMouseOver='this.className=\"mouse_over\";' onMouseOut='this.className=\"$clase\";'>
			<td style='text-align: center;'>".$talonario_a[1]."</td>
			<td style='text-align: center;'>".$talonario_a[2]."</td>
			<!--<td style='text-align: center;'>".$talonario_a[10]."</td>-->
		");    
		if($verfunc) {
			if($talonario_a[9]==1) $color='';
			if($talonario_a[9]==2) $color='color: blue;';
			print("
			<td style='text-align: center; font-weight: bold; ".$color."'>".htmlentities($talonario_a[3])."</td>
			<td style='text-align: left; font-weight: bold; ".$color."'>".htmlentities($talonario_a[4])."</td>"
			);
		} else
			print("<td style='text-align: center; font-weight: bold;'>".htmlentities($talonario_a[8])."</td>");
		
		print("
		<td style='text-align: right;'>".$talonario_a[5]."</td>
		<td style='text-align: right;'>".$talonario_a[6]."</td>
		<td style='text-align: center;'>".$estado_talonario."</td>
		<td style='text-align: center;'>".htmlentities($talonario_a[11])."</td>");
		if (_cax(30251)) 
			print("<td><center><img src='iconos/pencil.png' onClick='cargar_talonario(".$talonario_a[0].");' style='cursor:pointer;' /></center></td>");
		if (_cax(30250)) 
			print ("<td><center><img src='iconos/delete.png' onClick='borrar_talonarios(".$talonario_a[0].");' style='cursor:pointer;' /></center></td>");
		
		print("</tr>");
	}
?>
</table>
