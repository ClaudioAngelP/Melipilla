<?php 

	require_once('../../conectar_db.php');
	
	$turnos=cargar_registros_obj("
		SELECT * FROM tareas AS t1 ORDER BY t_prioridad DESC, t_resumen ASC;
	");


?>

<table style='width:100%;'>

<tr class='tabla_header'>
<td>ID</td>
<td>Tarea</td>
<td>Prioridad</td>
<td>Fecha L&iacute;mite</td>
<td>Editar</td>
</tr>

<?php
	
	if($turnos)
	for($i=0;$i<sizeof($turnos);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
		<tr class='$clase'>
		<td style='text-align:right;'>".$turnos[$i]['t_id']."</td>
		<td>".$turnos[$i]['t_resumen']."</td>
		<td style='text-align:right;'>".$turnos[$i]['t_prioridad']."</td>
		<td style='text-align:right;'>".$turnos[$i]['t_fecha_limite']."</td>
		<td><center><img src='iconos/pencil.png' onClick='editar_tarea(".$tareas[$i]['t_id'].");' style='cursor:pointer;' /></center></td>
		</tr>
		");
	}

		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("
		<tr class='$clase'>
		<td style='text-align:right;'>&nbsp;</td>
		<td colspan=3><i>(Crear Tarea Nueva...)</i></td>
		<td><center><img src='iconos/add.png' onClick='editar_tarea(0);' style='cursor:pointer;' /></center></td>
		</tr>
		");


?>

</table>

