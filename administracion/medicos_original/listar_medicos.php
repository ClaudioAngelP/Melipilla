<?php

  require_once('../../conectar_db.php');

	$busqueda = pg_escape_string(iconv("UTF-8", "ISO-8859-1", $_GET['buscar']));
	
	if(trim($busqueda)=="") {
		$buscar="";
	} else {
		$buscar="WHERE
		(doc_rut ILIKE '%$busqueda%' OR (doc_paterno || ' ' || doc_materno || ' ' || doc_nombres) ILIKE '%$busqueda%')";
	}
	
	$personal = pg_query($conn,"
	SELECT
	doc_id,
	doc_rut,
	doc_paterno || ' ' || doc_materno || ' ' || doc_nombres
	FROM doctores
	$buscar
	ORDER BY doc_rut
	LIMIT 15
	");
	
	print("
	
	<table width=320>
	<tr class='tabla_header'><td><b>RUT</b></td><td><b>Nombre</b></td></tr>
	
	");
	
	for($i=0;$i<pg_num_rows($personal);$i++) {
	
		$datos=pg_fetch_row($personal);
		
		if(($i%2)==0) {
				$clase='tabla_fila';
		} else {
				$clase='tabla_fila2';
		}
		
		print("
		<tr class='$clase'
		onClick='seleccionar_medico(\"".$datos[0]."\",1);'
		onMouseOver='this.className=\"mouse_over\"'
		onMouseOut='this.className=\"$clase\"'>
		<td width='80' style='text-align: right;'>
		".htmlentities($datos[1])."</td><td>".htmlentities($datos[2])."</td>
		</tr>
		");
	
	}
	
	print("</table>");


?>
