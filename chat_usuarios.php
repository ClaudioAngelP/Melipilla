<?php

  require_once('conectar_db.php');
  
  	
  $func_id=$_SESSION['sgh_usuario_id']*1;
  
  $busqueda = iconv("UTF-8", "ISO-8859-1", $_GET['buscar']);
	
	if(trim($busqueda)=="") {
		$buscar="WHERE NOT func_id=$func_id";
	} else {
		$buscar="WHERE func_nombre ILIKE '%".pg_escape_string($busqueda)."%' AND NOT func_id=$func_id";
	}
	
	$u=cargar_registros_obj("SELECT * FROM (SELECT *,COALESCE(((CURRENT_TIMESTAMP-func_ping)<'15 seconds'::interval), false ) AS online, upper(func_nombre) AS func_nombre2,upper(func_cargo) AS func_cargo2, func_fono,
	(SELECT COUNT(*) FROM chat WHERE func_id2=$func_id AND chat.func_id=funcionario.func_id AND chat_estado=1) AS leidos, 
	(SELECT COUNT(*) FROM chat WHERE func_id2=$func_id AND chat.func_id=funcionario.func_id AND chat_estado=0) AS nuevos 
	FROM funcionario $buscar) AS foo ORDER BY nuevos DESC, leidos DESC, online DESC, trim(func_nombre) LIMIT 50", true);
	
	pg_query("UPDATE funcionario SET func_ping=CURRENT_TIMESTAMP WHERE func_id=$func_id");

	print("<table style='width:100%;font-size:11px;' cellspacing=0>
			<tr class='tabla_header'>
				<td>Estado</td>
				<td>Usuario</td>
				<td>Tel&eacute;fono</td>				
				<td>Nuevos</td>
				<td>Leidos</td>
			</tr>");
 
		
		$j=0;
		$nuevo=0;
		
		for($i=0;$i<sizeof($u);$i++) {
			
			if(trim($u[$i]['func_nombre'])=='') continue;
			
			$clase=($j++)%2==0?'tabla_fila':'tabla_fila2';
			
			if($u[$i]['online']=='t'){
				$estado='status_online.png';
				$color='green';
				$negrita='font-weight:bold;';
			}else{
				$estado='status_offline.png';
				$color='red';				
				$negrita='';
			}
			
			if($u[$i]['func_cargo2']!='') {
				$cargo='<br><span style="font-style:italic;color:black;font-size:10px;">'.$u[$i]['func_cargo2'].'</span>';
			} else {
				$cargo='';
			}
			
			print("<tr class='$clase' 
			onMouseOver='this.className=\"mouse_over\"'
			onMouseOut='this.className=\"$clase\"'
			style='cursor:pointer;'
			onClick='$(\"func_id\").value=\"".$u[$i]['func_id']."\"; $(\"func_nombre\").innerHTML=\"".$u[$i]['func_nombre2']."\".unescapeHTML(); ver_mensajes(); $(\"mensaje\").value=\"\"; $(\"mensaje\").focus();'>
			<td><center><img src='iconos/$estado'></center></td>
			<td  style='color:$color;$negrita'>".$u[$i]['func_nombre2']."".$cargo."</td>
			<td>".$u[$i]['func_fono']."</td>
			<td style='text-align:right;font-weight:bold;'>".($u[$i]['nuevos']*1)."</td>
			<td style='text-align:right;'>".($u[$i]['leidos']*1)."</td></tr>");
			
			$nuevo+=$u[$i]['nuevos']*1;
		}
		
		
	print("</table>");
	
?>
<script>

	document.title='(<?php echo $nuevo; ?>) Mensajes';

</script>
  
