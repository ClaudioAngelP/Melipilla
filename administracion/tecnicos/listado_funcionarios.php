<?php

  require_once('../../conectar_db.php');
  
  $busqueda = iconv("UTF-8", "ISO-8859-1", $_GET['buscar']);
	$orden = ($_GET['orden']*1);
	
	if(trim($busqueda)=="") {
		$buscar="";
	} else {
		$buscar="WHERE
		tec_rut ILIKE '%".pg_escape_string($busqueda)."%' OR
		tec_nombre ILIKE '%".pg_escape_string($busqueda)."%'";
	}
	
	if($orden) { $orderby = "ORDER BY tec_rut, tec_nombre"; } else { $orderby = "ORDER BY tec_nombre, tec_rut"; }
	
	$personal = pg_query($conn,"
	SELECT
	tec_id,
	tec_rut,
	tec_nombre
	FROM tecnico
	$buscar
	$orderby
	LIMIT 20
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
		onClick='seleccionar_usuario(\"".($datos[0]*1)."\",1);'
		onMouseOver='this.className=\"mouse_over\"'
		onMouseOut='this.className=\"$clase\"'>
		<td width='80' style='text-align: right;'>
		".htmlentities($datos[1])."</td><td>".htmlentities($datos[2])."</td>
		</tr>
		");
  
  }
	
	print("</table>");

?>
