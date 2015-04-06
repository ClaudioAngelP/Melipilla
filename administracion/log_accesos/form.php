<?php

	require_once('../../conectar_db.php');

?>

<script>

cargar_listado=function() {

	$('listado').innerHTML='<center><br><br><img src="imagenes/ajax-loader3.gif" /><br>Cargando...';

	var myAjax=new Ajax.Updater(
	'listado',
	'administracion/log_accesos/listado.php',
	{ method:'post',parameters:$('func_id').serialize()+'&'+$('fecha1').serialize()+'&'+$('fecha2').serialize()+'&'+$('ver').serialize() }
	);

}

</script>


<center>

<div class='sub-content' style='width:800px;'>
<div class='sub-content'>
<img src='iconos/user.png'>
<b>Monitor de Accesos</b>
</div>

<div class='sub-content'>
<table style='width:100%;'>
<tr><td>Usuario:</td><td>
<select id='func_id' name='func_id'>
<option value=''>(Todos los Usuarios...)</option>
<?php
$f=cargar_registros_obj("SELECT func_id, upper(func_nombre) AS func_nombre FROM (SELECT DISTINCT func_id FROM logs_acceso) AS foo JOIN funcionario USING (func_id) ORDER BY func_nombre",true);
for($i=0;$i<sizeof($f);$i++) {
print("<option value='".$f[$i]['func_id']."'>".$f[$i]['func_nombre']."</option>");
}

?>
</select>
</td><td>Fecha:</td>
<td>
<input type='text' id='fecha1' name='fecha1' value='<?php echo date('d/m/Y'); ?>' size=10 style='text-align:center;' />
</td>
<td>
<input type='text' id='fecha2' name='fecha2' value='<?php echo date('d/m/Y'); ?>' size=10 style='text-align:center;' />
</td>
<td>
<select id='ver' name='ver'>
<option value=0>Ver Resumen</option>
<option value=1>Ver Detalle</option>
<option value=2>Totales por Sistema</option>
</select>
</td>
<td><input type='button' value='Actualizar...' onClick='cargar_listado();'></td></tr></table>
</div>

<div class='sub-content2' id='listado' style='height:300px;overflow:auto;'>

</div>

</div>

<script> cargar_listado(); </script>
