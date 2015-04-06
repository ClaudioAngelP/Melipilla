<?php 

	require_once('../conectar_db.php');

?>

<html>
<title>Acta Directorio GES</title>

<?php cabecera_popup('..'); ?>


<script>

	validacion_fecha2=function(obj) {
		
		if(obj.value=='') {
			obj.style.background='';
			return true;
		} else {
			return validacion_fecha(obj);
		}
		
	}


	function cargar_lista(t) {

		if(t==0) {
			obj='listado_tareas';
			tipo='nuevas';
		} else {
			obj='listado_tareas2';
			tipo='antiguas';
		}
		
		var myAjax=new Ajax.Updater(
			obj,
			'listado_tareas.php',
			{
				method:'post',
				parameters: 'tipo='+tipo
			}
		);
		
	}

	function cargar_compras(t) {

                var myAjax=new Ajax.Updater(
                        'listado_compras',
                        'listado_compras.php',
                        {
                                method:'post'
                        }
                );

        }

	
	function guardar_tarea() {
		
	if($('tarea_encargado').value=='') {
                        alert('Debe ingresar un encargado para la tarea.'.unescapeHTML());
                        return;
                }
	if(!validacion_fecha2($('tarea_fecha_limite'))) {
			alert('Debe ingresar una fecha l&iacute;mite v&aacute;lida.'.unescapeHTML());
			return;
		}
		
		var myAjax=new Ajax.Updater(
			'listado_tareas',
			'listado_tareas.php',
			{
				method:'post',
				parameters:$('tarea').serialize(),
				onComplete: function(r){
					alert('Tarea guardada exitosamente.');
					cargar_lista(0); cargar_lista(1);
				}
			}			
		);
		
	}



	function eliminar_tarea(tarea_id) {
		
		if(!confirm('&iquest;Est&aacute; seguro que desea eliminar esta tar&eacute;a?'.unescapeHTML()))
			return;
		
		var myAjax=new Ajax.Updater(
			'listado_tareas',
			'listado_tareas.php',
			{
				method:'post',
				parameters:'tarea_id='+tarea_id,
				onComplete: function(r){
					alert('Tarea eliminada exitosamente.');
				}
			}			
		);
		
	}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<div class='sub-content' style='font-size:24px'>
<img src='../iconos/building.png'> <b>Acta Directorio GES <u><?php echo date('d/m/Y'); ?></u></b>
</div>

<div class='sub-content'>
<img src='../iconos/cog_go.png'> <b>Nuevos Varios</b>
</div>
<div class='sub-content2' id='listado_tareas' style='height:200px;overflow:auto;'>

</div>

<div class='sub-content'>
<form id='tarea' name='tarea' onSubmit='return false;'>
<table style='width:100%;'>
	<tr>
		<td><img src='../iconos/add.png' /></td>
		<td><center>Encargado(s):<br/><select id='tarea_encargado' name='tarea_encargado'>
<option value=''>(Seleccionar...)</option>
<option value='Directora'>Directora</option>
<option value='SDA'>SDA</option>
<option value='SDGU'>SDGU</option>
<option value='SDM'>SDM</option>
<option value='Jefe UGAC'>Jefe UGAC</option>
<option value='Jefe UGAA'>Jefe UGAA</option>
<option value='Coordinador GES'>Coordinador GES</option>
<option value='Profesional GES'>Profesional GES</option>
<option value='Monitor SIGGES'>Monitor SIGGES</option>
<option value='Sistemas Expertos'>Sistemas Expertos</option>
		</select></center></td>
		<td><textarea id='tarea_descripcion' name='tarea_descripcion'  style='width:250px;height:50px;' value=''></textarea></td>
		<td><center>Fecha L&iacute;mite:<br/><input type='text' style='text-align:center;' id='tarea_fecha_limite' name='tarea_fecha_limite' value='' onKeyUp='validacion_fecha2(this);' /></center></td>
		<td><input type='button' style='font-size:18px;' value='[Guardar...]' onClick='guardar_tarea();' /></td>
	</tr>
</table>
</form>
</div>

<div class='sub-content'>
<img src='../iconos/cog.png'> <b>Otros Varios</b>
</div>
<div class='sub-content2' id='listado_tareas2' style='height:300px;overflow:auto;'>

</div>

<div class='sub-content'>
<img src='../iconos/money.png'> <b>Compras Autorizadas</b>
</div>
<div class='sub-content2' id='listado_compras' style='height:200px;overflow:auto;'>

</div>

<br /><br />
<center><input type='button' id='enviar' name='enviar' value='-- Enviar Acta --' style='font-size:24px' onClick='' /></center>

</div>

</body>

</html>


<script> cargar_lista(0); cargar_lista(1); cargar_compras(); </script>
