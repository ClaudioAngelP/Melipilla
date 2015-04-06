<?php 

	require_once('../conectar_db.php');
	
	if(isset($_POST['tarea_encargado'])) {
		
		$encargado=pg_escape_string(utf8_decode($_POST['tarea_encargado']));
		$descripcion=pg_escape_string(utf8_decode($_POST['tarea_descripcion']));
		
		$flimite=$_POST['tarea_fecha_limite'];
		
		if($flimite=='') $flimite='null';
		else $flimite="'$flimite'";
		
		$func_id=$_SESSION['sgh_usuario_id']*1;
		
		pg_query("INSERT INTO lista_dinamica_tareas 
		VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, '$encargado', $flimite, '$descripcion', 0, '');");
		
	}

	if(isset($_POST['tarea_id'])) {
		
		$tarea_id=$_POST['tarea_id']*1;
		
		pg_query("DELETE FROM lista_dinamica_tareas WHERE tarea_id=$tarea_id;");
		
	}
	

	if($_POST['tipo']=='antiguas')
	$t=cargar_registros_obj("
		SELECT *, tarea_fecha::date AS fecha FROM lista_dinamica_tareas 
		JOIN funcionario USING (func_id)
		WHERE tarea_fecha::date<CURRENT_DATE
		ORDER BY tarea_fecha ASC;
	");
	else
	$t=cargar_registros_obj("
                SELECT *, tarea_fecha::date AS fecha FROM lista_dinamica_tareas
                JOIN funcionario USING (func_id)
                WHERE tarea_fecha::date>=CURRENT_DATE
                ORDER BY tarea_fecha ASC;
        ");

?>

<table style='width:100%;'> 
	<tr class='tabla_header'>
		<td>Fecha Ing.</td>
		<td>Toma Acta</td>
		<td>Encargado</td>
		<td>Fecha L&iacute;mite</td>
		<td>Descripci&oacute;n</td>
		<td>Estado</td>
		<td>Eliminar</td>
	</tr>
	
<?php 

	if($t)
	for($i=0;$i<sizeof($t);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		switch($t[$i]['tarea_estado']*1) {
			case 0: $icono='clock.png'; break;
			case 1: $icono='tick.png'; break;
			case 2: $icono='cross.png'; break;
		}

		$estado="<select id='estado_".$t[$i]['tarea_id']."' name='".$t[$i]['tarea_id']."'>";
		$estado.="<option value='0'>Pendiente</option>";
		$estado.="<option value='1'>En Proceso</option>";
		$estado.="<option value='2'>Terminada</option>";
		$estado.="<option value='3'>Cancelada</option>";
		$estado.="</select>";
		
		
		print("
			<tr class='$clase'>
			<td style='text-align:center;'>".$t[$i]['fecha']."</td>
			<td style='text-align:center;font-weight:bold;'>".htmlentities($t[$i]['func_nombre'])."</td>
			<td style='text-align:center;font-weight:bold;'>".htmlentities($t[$i]['tarea_encargado'])."</td>
			<td style='text-align:center;'>".$t[$i]['tarea_fecha_limite']."</td>
			<td style='text-align:justify;'>".htmlentities($t[$i]['tarea_descripcion'])."</td>
			<td><center><img src='../iconos/$icono' style='width:32px;height:32px;' /><br/>$estado</center></td>
			<td><center><img src='../iconos/delete.png' style='cursor:pointer;' onClick='eliminar_tarea(".$t[$i]['tarea_id'].");' /></center></td>
			</tr>
		");
		
	}

?>	
	
	
</table>
