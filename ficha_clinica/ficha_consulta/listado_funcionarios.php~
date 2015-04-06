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
	SELECT * FROM(SELECT pac_rut,	(pac_nombres||' '||pac_appat||' '||pac_apmat)AS pac_nombre,
       pac_fc_nac,sex_desc,estciv_nombre,prev_desc,getn_desc,sang_desc,pac_direccion,ciud_desc,nacion_nombre,pac_fono
	FROM pacientes 
LEFT JOIN sexo USING (sex_id)
LEFT JOIN prevision USING (prev_id)
LEFT JOIN grupos_etnicos USING (getn_id)
LEFT JOIN grupo_sanguineo USING (sang_id)
LEFT JOIN estado_civil USING (estciv_id)
LEFT JOIN comunas USING (ciud_id)
LEFT JOIN nacionalidad USING (nacion_id)
)AS foo
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
