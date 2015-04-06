<?php 
	require_once('../../conectar_db.php');
	$servicio_ruta=pg_escape_string($_POST['serv_exam_solicita']);
		
	$tmp=cargar_registros_obj("SELECT centro_ruta,centro_nombre FROM centro_costo 
	WHERE centro_ruta ILIKE '".$servicio_ruta."%' 
	AND centro_medica AND NOT centro_ruta='".$servicio_ruta."'
	ORDER BY centro_nombre
	");
	
	print("<select id='centro_exam_solicita' name='centro_exam_solicita' Onclick=''>");
	print("<option value=''>(Seleccione Centro...)</option>");
	if($tmp) {
		for($i=0;$i<sizeof($tmp);$i++) {
			print("<option value='".$tmp[$i]['centro_ruta']."' $sel>".htmlentities($tmp[$i]['centro_nombre'])."</option>");
		}
	}
	print("</select>");
?>
<script>
	<?php if($tmp){ ?>
		$('centros_cant').value=<?php echo sizeof($tmp); ?>
	<?php } else { ?>
		$('centros_cant').value=0;
	<?php } ?>
</script>
