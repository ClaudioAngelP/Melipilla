<?php 

	require_once("../../conectar_db.php");

?>

<div class='sub-content'>
<div class='sub-content'>
<img src='iconos/chart.png' /> <b>Ingreso de Variables de Gesti&oacute;n</b>
</div>
<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='text-align:right;'>
Fecha:
</td><td>
<select id='mes' name='mes'>
<option value=1>Enero</option>
<option value=2>Febrero</option>
<option value=3>Marzo</option>
<option value=4>Abril</option>
<option value=5>Mayo</option>
<option value=6>Junio</option>
<option value=7>Julio</option>
<option value=8>Agosto</option>
<option value=9>Septiembre</option>
<option value=10>Octubre</option>
<option value=11>Noviembre</option>
<option value=12>Diciembre</option>
</select>
</td><td>
<select id='anio' name='anio'>
<option value=2009>2009</option>
<option value=2010 SELECTED>2010</option>
<option value=2011>2011</option>
</select>
</td></tr>
</table>
</div>
<div class='sub-content2' id='variables' style='height:450px;overflow:auto;'>
<table style='width:100%;'>
<?php 

	$v=cargar_registros_obj("SELECT * FROM variables_gestion ORDER BY var_codigo");
	
	for($i=0;$i<sizeof($v);$i++) {
		print("<tr>
		<td rowspan=2 class='tabla_fila2'><b>".$v[0]['var_nombre']."</b><br />
		<i>".$v[0]['var_descripcion']."</i></td>		
		<td>".$v[0]['var_descripcion_n']."</td>
		<td class='tabla_fia'><input size=15 id='regv_n_".$v[0]['var_id']."' name='regv_n_".$v[0]['var_id']."' /></td>		
		</tr><tr>
		<td class='tabla_fila'>".$v[0]['var_descripcion_d']."</td>
		<td><input size=15 id='regv_d_".$v[0]['var_id']."' name='regv_d_".$v[0]['var_id']."' /></td>		
		</tr>");	
	}

?>
</table>
</div>
<center>
<input type='button' value='Guardar Registro...' />
</center>
</div>
