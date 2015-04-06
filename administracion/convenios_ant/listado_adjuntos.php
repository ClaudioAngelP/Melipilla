<?php 	require_once('../../conectar_db.php');
	
	$convenio_id=$_GET['convenio_id']*1;
	
	if(isset($_POST['adjunto_id'])) {
		
		$adjunto_id=$_POST['adjunto_id'];
		
		pg_query("DELETE FROM convenio_adjuntos WHERE cad_id=$adjunto_id;");
		
	}
	
	$l=pg_query("SELECT * FROM convenio_adjuntos WHERE convenio_id=$convenio_id");
	
	 print("<table>");
	 
	 for($i=0;$i<pg_num_rows($l);$i++) {
		 
		 $adjunto = pg_fetch_assoc($l);
		 list($nombre,$tipo,$peso,$md5)=explode('|',$adjunto['cad_adjunto']);
		 		 
			print("<tr class='$clase'
				onMouseOver='this.className=\"mouse_over\"'
			onMouseOut='this.className=\"$clase\"'>
			<table style='cursor:pointer;border:1px solid black;background-color:white;font-size:12px;' 
			onClick='window.open(\"administracion/convenios/descargar_adjunto.php?adjunto_id=".$adjunto['cad_id']."\", \"_self\");'>
				<tr>
					<td><i>Archivo:</i></td>
					<td><img src='iconos/application_put.png'></td>
					<td><b><u>".$nombre."</u></b></td>
					<td><i>(".number_format($peso/1024,1,',','.')." Kb)</i></td>
					<td>.</td>
				</tr>
			</table> 
			</td></tr>");
	
	}
	
	print("</table>");
	
?>
