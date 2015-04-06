<?php 

	require_once('conectar_db.php');
	
	$func_id=$_SESSION['sgh_usuario_id']*1;
	$func_id2=$_POST['func_id'];
	
	if(isset($_POST['mensaje'])) {
	
		$mensaje=pg_escape_string(utf8_decode($_POST['mensaje']));
	
		pg_query("INSERT INTO chat VALUES (DEFAULT, $func_id, $func_id2, CURRENT_TIMESTAMP, '$mensaje', 0);");
	
	}
	
	$m=cargar_registros_obj("SELECT *, (func_id=$func_id AND func_id2=$func_id2) AS enviado FROM chat 
		WHERE (func_id=$func_id AND func_id2=$func_id2) OR (func_id=$func_id2 AND func_id2=$func_id)
		ORDER BY chat_fecha", true);
		
	pg_query("UPDATE chat SET chat_estado=1 WHERE (func_id=$func_id2 AND func_id2=$func_id);");
	pg_query("UPDATE funcionario SET func_ping=CURRENT_TIMESTAMP WHERE func_id=$func_id;");
	
?>

<table style='font-size:12px;width:100%;' id='tabla_mensajes'>

<?php

	list($fecha_ult)=explode(' ',$m[0]['chat_fecha']);
	
	print("<tr><td style='background-color:black;color:white;font-size:20;' colspan=2><i><b>".$fecha_ult."</b></i></td></tr>");
	if($m)
	for($i=0;$i<sizeof($m);$i++) {
		
		if($m[$i]['enviado']=='t') {
			$color='green';
			$bold='font-style:italic;';
		} else {
			$color='blue';
			$bold='font-weight:bold;';
		}
		
		list($fecha,$hora)=explode(' ',$m[$i]['chat_fecha']);
		 
		if($fecha!=$fecha_ult) {
			print("<tr><td style='background-color:black;color:white;font-size:20;' colspan=2><i><b>".$fecha."</b></i></td></tr>");
			$fecha_ult=$fecha;
		}
			
		$clase=($i)%2==0?'tabla_fila':'tabla_fila2';
			
		if($m[$i]['chat_tipo']==0)
		print("<tr class='$clase'
				onMouseOver='this.className=\"mouse_over\"'
			onMouseOut='this.className=\"$clase\"'>
			<td style='text-align:center;width:10%;color:$color;font-weight:bold;'>".substr($hora,0,8)." </td>
			<td style='color:$color;$bold'>".$m[$i]['chat_mensaje']."</td></tr>");
		else if($m[$i]['chat_tipo']==1) {
			
			list($nombre,$tipo,$peso,$md5)=explode('|', $m[$i]['chat_mensaje']);
			
			print("<tr class='$clase'
				onMouseOver='this.className=\"mouse_over\"'
			onMouseOut='this.className=\"$clase\"'>
			<td style='text-align:center;width:10%;color:$color;font-weight:bold;'>".substr($hora,0,8)." </td>
			<td style='color:$color;$bold'>
			<table style='cursor:pointer;border:1px solid black;background-color:white;font-size:12px;' 
			onClick='window.open(\"chat_descargar.php?chat_id=".$m[$i]['chat_id']."\", \"_self\");'>
				<tr>
					<td><i>Archivo:</i></td>
					<td><img src='iconos/application_put.png'></td>
					<td><b><u>".$nombre."</u></b></td>
					<td><i>(".number_format($peso/1024,1,',','.')." Kb)</i></td>
				</tr>
			</table> 
			</td></tr>");
			
		}
		
		
	}

?>

</table>
