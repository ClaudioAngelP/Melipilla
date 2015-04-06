<?php

  require_once('../../conectar_db.php');
  
  $busqueda = iconv("UTF-8", "ISO-8859-1", $_GET['buscar']);
	
	if(trim($busqueda)=="") {
		$buscar="";
	} else {
		$buscar="WHERE
		bod_glosa ILIKE '%".pg_escape_string($busqueda)."%'";
	}
	
	$personal = pg_query($conn,"
	SELECT
	bod_id,
	bod_glosa
	FROM bodega
	$buscar
	ORDER BY bod_glosa
	LIMIT 20
	");
	
	print("
	
	<table width=320>
	<tr class='tabla_header'><td><b>Nombre</b></td></tr>
	
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
		onClick='seleccionar_bodega(\"".$datos[0]."\",1);'
		onMouseOver='this.className=\"mouse_over\"'
		onMouseOut='this.className=\"$clase\"'>
		<td>".htmlentities($datos[1])."</td>
		</tr>
		");
	
	}
	
	print("</table>");

?>
