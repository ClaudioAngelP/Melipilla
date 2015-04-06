<?php 

	require_once('../conectar_db.php');
	
	if(isset($_POST['nombre'])) {
	
		$cad=pg_escape_string(utf8_decode($_POST['nombre']));	
	
		$l=cargar_registros_obj("
			SELECT * FROM uso_sepultura 
			WHERE us_vigente AND (us_rut ILIKE '%$cad%' OR
			us_nombre ILIKE '%$cad%')		
		");	
		
	} else {

		$clase=pg_escape_string($_POST['sel_clases']);
		$codigo=pg_escape_string(utf8_decode($_POST['sel_codigos']));
		if($codigo=='-1')
			$codigo=pg_escape_string(utf8_decode($_POST['sel_codigon']));
		$numero=pg_escape_string($_POST['numero'])*1;
		$letra=pg_escape_string(trim(utf8_decode($_POST['letra'])));

		$l=cargar_registros_obj("
			SELECT * FROM uso_sepultura 
			WHERE us_vigente AND (sep_clase='$clase' AND
			sep_codigo='$codigo' AND sep_letra='$letra' AND
			sep_numero=$numero)		
		");	
			
	}

?>

<table style='width:100%;font-size:11px;'>

<tr class='tabla_header'>
<td>Sepultura</td>
<td>RUT</td>
<td>Nombre</td>
<td>Ubicaci&oacute;n</td>
<td>Seleccionar</td>
</tr>

<?php 

	if($l) 
		for($i=0;$i<sizeof($l);$i++) {
			
			$rclase=($i%2==0)?'tabla_fila':'tabla_fila2';	
			
			$cad=str_replace("'","\\'",htmlentities($l[$i]['us_id'].'|'.
					$l[$i]['sep_clase'].'|'.
					$l[$i]['sep_codigo'].'|'.
					$l[$i]['sep_numero'].'|'.
					$l[$i]['sep_letra'].'|'.
					$l[$i]['us_rut'].'|'.
					$l[$i]['us_nombre'].'|'.
					$l[$i]['us_ubicacion']));
								
			
			print("<tr class='$rclase'>
			<td style='text-align:center;'>
			".htmlentities($l[$i]['sep_clase'])." > 
			".htmlentities($l[$i]['sep_codigo'])." >
			".htmlentities($l[$i]['sep_numero'])."
			".htmlentities($l[$i]['sep_letra'])."
			</td>			
			<td style='text-align:right;'>".$l[$i]['us_rut']."</td>			
			<td style='text-align:left;'>".htmlentities($l[$i]['us_nombre'])."</td>			
			<td style='text-align:center;'>".htmlentities($l[$i]['us_ubicacion'])."</td>
			<td><center>");
			if(($l[$i]['crecod']*1)==0)			
				print("<img src='../iconos/add.png' style='cursor:pointer;' onClick=\"agregar_bloqueo('$cad');\" />");
			else
				print("<img src='../iconos/lock.png' style='cursor:pointer;' onClick=\"alert('Previamente bloqueado en Cr&eacute;dito #".$l[$i]['crecod']."'.unescapeHTML());\" />");
			
			print("</center></td>			
			</tr>");
			
			
		}

?>

</table>