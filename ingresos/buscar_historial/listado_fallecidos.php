<?php 

	require_once('../../conectar_db.php');
	
	$cad=pg_escape_string(utf8_decode($_POST['nombre']));	
	
	$l=cargar_registros_obj("
		SELECT * FROM uso_sepultura 
		WHERE us_vigente AND (us_rut ILIKE '%$cad%' OR
		us_nombre ILIKE '%$cad%')		
	");	
		
?>

<table style='width:100%;font-size:10px;'>

<tr class='tabla_header'>
<td>Bolet&iacute;n</td>
<td style='width:30%;'>Sepultura</td>
<td style='width:10%;'>RUT</td>
<td style='width:25%;'>Nombre</td>
<td>Bloqueado</td>
<td>Ubicaci&oacute;n</td>
<td>Ver</td>
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
								
			
			print("<tr onMouseOver='this.className=\"mouse_over\";' 
			onMouseOut='this.className=\"$rclase\";' class='$rclase'>
			<td style='text-align:center;'>".vboletin($l[$i]['bolnum'])."</td>
			<td style='text-align:center;font-weight:bold;'>
			".htmlentities($l[$i]['sep_clase'])." > 
			".htmlentities($l[$i]['sep_codigo'])." >
			".htmlentities($l[$i]['sep_numero'])."
			".htmlentities($l[$i]['sep_letra'])."
			</td>			
			<td style='text-align:right;'>".$l[$i]['us_rut']."</td>			
			<td style='text-align:left;'>".htmlentities($l[$i]['us_nombre'])."</td>");
			
			if($l[$i]['us_bloqueo']=='t') {
				print("<td style='text-align:center;'>
				S&iacute; (Bol. ".vboletin($l[$i]['bolnum2']).")				
				</td>");	
			} else {
				print("<td style='text-align:center;'>No</td>");	
			}			
			
			print("			
			<td style='text-align:center;'>".htmlentities($l[$i]['us_ubicacion'])."</td>
			<td><center><img src='iconos/user_go.png' style='cursor:pointer;' 
			onClick='abrir_sepultura(".$l[$i]['us_id'].");'/>
			</center></td>
			</tr>
			");
		
		/*
			<td><center>");
			if(($l[$i]['crecod']*1)==0)			
				print("<img src='../iconos/add.png' style='cursor:pointer;' onClick=\"agregar_bloqueo('$cad');\" />");
			else
				print("<img src='../iconos/lock.png' style='cursor:pointer;' onClick=\"alert('Previamente bloqueado en Cr&eacute;dito #".$l[$i]['crecod']."'.unescapeHTML());\" />");
			
			print("</center></td>			
			</tr>");
		*/	
			
		}

?>

</table>
