<?php

  require_once('../../conectar_db.php');
  
  $busqueda = iconv("UTF-8", "ISO-8859-1", $_GET['buscar']);
	$orden = ($_GET['orden']*1);
	
	if(trim($busqueda)=="") {
		$buscar="";
	} else {
		$buscar="WHERE
		pac_rut ILIKE '%".pg_escape_string($busqueda)."%' OR
		pac_nombre ILIKE '%".pg_escape_string($busqueda)."%'";
	}
	
	if($orden) { $orderby = "ORDER BY pac_rut, pac_nombre"; } else { $orderby = "ORDER BY pac_nombre, pac_rut"; }
	
	$personal = pg_query($conn,"
	SELECT * FROM(SELECT pac_id,pac_rut,(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre
	FROM pacientes
)AS foo
	$buscar
	$orderby
	LIMIT 20
	");
	
	print("
	
	<table width=100%>
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
		onClick='seleccionar_paciente(\"".($datos[0])."\",1);'
		onMouseOver='this.className=\"mouse_over\"'
		onMouseOut='this.className=\"$clase\"'>
		<td width='80' style='text-align: right;'>
		".htmlentities(trim($datos[1]))."</td><td>".htmlentities(trim($datos[2]))."</td>
		</tr>
		");
  
  }
	
	print("</table>");

?>
