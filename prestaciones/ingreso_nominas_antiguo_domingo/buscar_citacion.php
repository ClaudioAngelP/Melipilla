<?php 

	require_once('../../conectar_db.php');

	$nomd_id=$_GET['nomd_id']*1;
	
	$nd=cargar_registro("SELECT * FROM nomina_detalle JOIN nomina USING (nom_id) WHERE nomd_id=$nomd_id;");
	
	//$pac_id=$nd['pac_id']*1;
	$pac_id=0;
	$esp_id=$nd['nom_esp_id']*1;
	$doc_id=$nd['nom_doc_id']*1;
	$f1=pg_escape_string(date('d/m/Y'));
	$f2='';
	$h1='';
	$h2='';
	
	$esp=cargar_registro("SELECT * FROM especialidades WHERE esp_id=$esp_id;");
	$doc=cargar_registro("SELECT * FROM doctores WHERE doc_id=$doc_id;");
	
	if($pac_id!=0) 
		$pac_w="pac_id=$pac_id";
	else
		$pac_w='pac_id=0';

	if($doc_id!=0) 
		$doc_w="nom_doc_id=$doc_id";
	else
		$doc_w='true';

	if($esp_id!=0) 
		$esp_w="nom_esp_id=$esp_id";
	else
		$esp_w='true';
		
	if($f1!='')
		$f1_w="nom_fecha>='$f1'";
	else
		$f1_w='true';

	if($f2!='')
		$f2_w="nom_fecha<='$f2'";
	else
		$f2_w='true';

	if($h1!='')
		$h1_w="nomd_hora>='$h1'";
	else
		$h1_w='true';

	if($h2!='')
		$h2_w="nomd_hora<='$h2'";
	else
		$h2_w='true';
		
		
	if($pac_w=='pac_id=0' AND $esp_w=='true' AND $doc_id=='true') {
?>

<center><h2>Ingrese par&aacute;metros para su b&uacute;squeda.</h2></center>

<?php 

	exit();	
	
}



	$c=cargar_registros_obj("
		SELECT *, to_char(nom_fecha, 'D') AS dow  FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL)  AND $pac_w AND $esp_w AND $doc_w AND
		$f1_w AND $f2_w AND $h1_w AND $h2_w
		ORDER BY nom_fecha, nomd_hora
		LIMIT 50
	", true);

	$tmp=cargar_registro("
		SELECT count(*) AS cuenta  FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND NOT pac_id=0 AND $esp_w AND $doc_w AND
		$f1_w AND $f2_w AND $h1_w AND $h2_w
	", true);
		
	$num=$tmp['cuenta']*1;

	$tmp2=cargar_registro("
		SELECT count(*) AS cuenta  FROM nomina_detalle
		JOIN nomina USING (nom_id)
		JOIN especialidades ON nom_esp_id=esp_id
		JOIN doctores ON nom_doc_id=doc_id
		WHERE (nomd_diag_cod NOT IN ('X','T','B') OR nomd_diag_cod IS NULL) AND pac_id=0 AND $esp_w AND $doc_w AND
		$f1_w AND $f2_w AND $h1_w AND $h2_w
	", true);
		
	$num2=$tmp2['cuenta']*1;
	
/*	if($num>0) {
	
		echo "<center><h3>Hay <u>$num cupos utilizados</u> y $num2 cupos libres seg&uacute;n su b&uacute;squeda.</h3></center>";
	
	} else {

		echo "<center><h3>No hay cupos utilizados y $num2 cupos libres seg&uacute;n su b&uacute;squeda.</h3></center>";
	
	}*/

	if(!$c) {
		
?>

<center><h2>No hay cupos libres similares para su b&uacute;squeda.</h2></center>

<?php 

	exit();
		
	}

?>


<html>
<title>Busqueda de Cupos</title>

<?php cabecera_popup('../..'); ?>

<body class='fuente_por_defecto popup_background'>

<center>

<div class='sub-content'>
<center>
<h2>Especialidad: <u><?php echo $esp['esp_desc']; ?></u><br />Profesional: <?php echo '['.$doc['doc_rut'].'] '.$doc['doc_nombres'].' '.$doc['doc_paterno'].' '.$doc['doc_materno']; ?></h2><br /><br />
</center>
</div>

<table style='width:100%;font-size:12px;' cellspacing=0>
	<tr class='tabla_header' style='font-size:14px;'>
		<td>D&iacute;a de la Semana</td>
		<td>Fecha</td>
		<td>Hora</td>
		<?php if($esp_w=='true') { ?><td>Especialidad</td><?php } ?>
		<?php if($doc_w=='true') { ?><td>Profesional</td><?php } ?>
		<td style='width:5%;'>Reagendar N&oacute;mina</td>
	</tr>
	
<?php 

	$dias=array('','Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado');

	for($i=0;$i<sizeof($c);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'
		>
		<td style='text-align:right;font-size:18px;'><i>".$dias[$c[$i]['dow']*1]."</i></td>
		<td style='text-align:center;font-size:16px;'>".substr($c[$i]['nom_fecha'],0,10)."</td>
		<td style='text-align:center;font-size:20px;'>".substr($c[$i]['nomd_hora'],0,5)."</td>");
		
		if($esp_w=='true')
			print("<td style='text-align:left;'>".$c[$i]['esp_desc']."</td>");
		
		if($doc_w=='true')
			print("<td style='text-align:left;'>".$c[$i]['doc_paterno']." ".$c[$i]['doc_materno']." ".$c[$i]['doc_nombres']."</td>");
		
		if($pac_id==0)
		print("
		<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='abrir_nom(\"".$c[$i]['nom_folio']."\");' /></center></td>
		");
		else
		print("
		<td><center><img src='../../iconos/arrow_refresh.png' style='cursor:pointer;width:24px;height:24px;' onClick='usar_citacion(".$c[$i]['nomd_id'].");' /></center></td>
		");
		
		print("</tr>");
		
	}

?>	
	
	
</table>

</center>

</body>
</html>