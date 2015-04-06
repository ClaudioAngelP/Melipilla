<?php 

	require_once('../../conectar_db.php');
	
	$t_id=$_GET['t_id']*1;

	$t=cargar_registro("
		SELECT * FROM tareas WHERE t_id=$t_id
	");
	
	if($t_id==0) {
		$u=cargar_registro("SELECT * FROM funcionario WHERE func_id=".$_SESSION['sgh_usuario_id']);
		$t['t_usuario_solicitante']=$u['func_nombre'];
	}
?>

<html>
<title>Editor de Tareas</title>


<?php cabecera_popup('../..'); ?>


<script>

guardar_tarea=function() {
	
	$('t_establecimiento').disabled=false;
	$('t_usuario_solicitante').disabled=false;
	
	var myAjax=new Ajax.Request(
		'sql.php',
		{
			method: 'post',
			parameters: $('tarea').serialize(),
			onComplete:function() {
				alert("Tarea modificada exitosamente.");
				var fn=window.opener.listar_tareas.bind(window.opener);
				fn();
				//window.close();
			}
		}
	);
	
}

</script>


<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<img src='../../iconos/clock_edit.png'>
<b>Editor de Tareas</b>
</div>

<form id='tarea' name='tarea' onsubmit='return false;'>

	<input type='hidden' id='t_id' name='t_id' value='<?php echo $t_id; ?>' /> 

<!----


DROP TABLE tareas;

CREATE TABLE tareas
(
  t_id bigserial NOT NULL,
  t_tipo text,
  t_categorias text,
  t_resumen text,
  t_descripcion text,
  t_diagnostico text,
  t_conclusion text,
  func_id bigint,
  t_usuario_solicitante text,
  t_usuario_encargado text,
  t_usuario_revisor text,
  t_prioridad smallint default 0,
  t_fecha_ingreso timestamp without time zone,
  t_fecha_limite timestamp without time zone,
  t_estado smallint default 0,
  CONSTRAINT tareas_t_id_key PRIMARY KEY (t_id)
)
WITH (
  OIDS=FALSE
);



---->

<table style='width:100%;'>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Establecimiento:</td>
		<td><input type='text' id='t_establecimiento' name='t_establecimiento' DISABLED
		size=45 value='HOSPITAL DR. GUSTAVO FRICKE' /></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Tipo:</td>
		<td><select id='t_tipo' name='t_tipo'>
		<option value='0' <?php if($t['t_tipo']*1==0) echo 'SELECTED'; ?> >Modificaci&oacute;n</option>
		<option value='1' <?php if($t['t_tipo']*1==1) echo 'SELECTED'; ?> >Reparaci&oacute;n</option>
		<option value='2' <?php if($t['t_tipo']*1==2) echo 'SELECTED'; ?> >Requerimiento Nuevo</option>
		</select>
		</td>
		
	</tr>
	
	<tr>
		<td style='text-align:right;width:25%;' class='tabla_fila2'>Resumen:</td>
		<td><input type='text' id='t_resumen' name='t_resumen' 
		size=45 value='<?php echo htmlentities($t['t_resumen']); ?>' /></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Prioridad:</td>
		<td><select id='t_prioridad' name='t_prioridad'>
		<option value='0' <?php if($t['t_prioridad']*1==0) echo 'SELECTED'; ?> >Normal</option>
		<option value='1' <?php if($t['t_prioridad']*1==1) echo 'SELECTED'; ?> >Urgente</option>
		<option value='2' <?php if($t['t_prioridad']*1==1) echo 'SELECTED'; ?> >Grave</option>
		</select>
		</td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Descripci&oacute;n:</td>
		<td><textarea id='t_descripcion' name='t_descripcion' 
		rows=4 cols=65><?php echo htmlentities($t['t_descripcion']); ?></textarea></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Fecha Ingreso:</td>
		<td>
		
		<input type='text' id='t_fecha_ingreso' name='t_fecha_ingreso' onKeyUp='validacion_fecha(this);' style='text-align:center;'
		size=10 value='<?php if ($t['t_fecha_ingreso']!='') echo htmlentities($t['t_fecha_ingreso']); else echo date('d/m/Y'); ?>' />
		
		<input type='text' id='t_hora_ingreso' name='t_hora_ingreso'  onKeyUp='validacion_hora(this);' style='text-align:center;'
		size=5 value='<?php if($t['t_hora_ingreso']!='') echo htmlentities($t['t_hora_ingreso']); else echo date('H:i'); ?>' />
		
		</td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Fecha L&iacute;mite:</td>
		<td>
		
		<input type='text' id='t_fecha_limite' name='t_fecha_limite' onKeyUp='validacion_fecha(this);' style='text-align:center;'
		size=10 value='<?php echo htmlentities($t['t_fecha_limite']); ?>' />
		
		<input type='text' id='t_hora_limite' name='t_hora_limite'  onKeyUp='validacion_hora(this);' style='text-align:center;'
		size=5 value='<?php echo htmlentities($t['t_hora_limite']); ?>' />
		
		</td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Diagn&oacute;stico:</td>
		<td><textarea id='t_diagnostico' name='t_diagnostico' 
		rows=4 cols=65><?php echo htmlentities($t['t_diagnostico']); ?></textarea></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Conclusi&oacute;n:</td>
		<td><textarea id='t_conclusion' name='t_conclusion'
		rows=4 cols=65><?php echo htmlentities($t['t_conclusion']); ?></textarea></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Usuario Solicitante:</td>
		<td><input type='text' id='t_usuario_solicitante' name='t_usuario_solicitante' DISABLED
		size=45 value='<?php echo htmlentities($t['t_usuario_solicitante']); ?>' /></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Usuario Encagado:</td>
		<td><input type='text' id='t_usuario_encargado' name='t_usuario_encargado' 
		size=45 value='<?php echo htmlentities($t['t_usuario_encargado']); ?>' /></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Usuario Q.A.:</td>
		<td><input type='text' id='t_usuario_revisor' name='t_usuario_revisor' 
		size=45 value='<?php echo htmlentities($t['t_usuario_revisor']); ?>' /></td>
		
	</tr>

	<tr>
		<td style='text-align:right' class='tabla_fila2'>Estado de la Tarea:</td>
		<td><select id='t_estado' name='t_estado'>
		<option value='0' <?php if($t['t_estado']*1==0) echo 'SELECTED'; ?> >Pendiente</option>
		<option value='1' <?php if($t['t_estado']*1==1) echo 'SELECTED'; ?> >Asignado</option>
		<option value='2' <?php if($t['t_estado']*1==2) echo 'SELECTED'; ?> >Terminado</option>
		<option value='3' <?php if($t['t_estado']*1==3) echo 'SELECTED'; ?> >Entregado</option>
		<option value='4' <?php if($t['t_estado']*1==4) echo 'SELECTED'; ?> >Anulado</option>
		</select>
		</td>

	</tr>


</table>

<center><br /><br />

<input type='button' id='guarda' name='guarda' value='Guardar Informaci&oacute;n de la Tarea...' onClick='guardar_tarea();' />
<br /><br />

</center>

</form>

</body>


</html>



<script>

validacion_fecha($('t_fecha_ingreso'));
validacion_fecha($('t_fecha_limite'));

validacion_hora($('t_hora_ingreso'));
validacion_hora($('t_hora_limite'));

</script>
