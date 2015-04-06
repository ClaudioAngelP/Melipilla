<?php 

	require_once('../conectar_db.php');
	
	$listahtml=desplegar_opciones_sql("
		SELECT lista_id, lista_nombre 
		FROM lista_dinamica 
		WHERE lista_id IN ("._cav(49).")
		ORDER BY lista_nombre;
	");

?>

<script>

	cargar_lista=function() {
		
		$('xls').value=0;
		
		var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/generar_tabla.php',{
			method:'post',parameters:$('datos_listado').serialize(), evalScripts: true
		});
			
	}


	cargar_listado=function() {
		
		$('xls').value=0;
		
		var myAjax=new Ajax.Updater('lista_tabla','listas_dinamicas/generar_tabla.php',{
			method:'post',parameters:$('datos_listado').serialize(), evalScripts: true
		});
			
	}


	descargar_xls=function() {

		$('xls').value=1;

		$('datos_listado').target='_self';

		$('datos_listado').action='listas_dinamicas/generar_tabla.php';
	
		$('datos_listado').submit();
			
	}
	
	guardar_listado=function() {
		
		var myAjax=new Ajax.Request(
			'listas_dinamicas/sql_tabla.php',
			{
				method:'post', parameters: $('datos_listado').serialize(),
				onComplete:function() {
					cargar_lista();
				}
			}
		);
		
	}

	reg_instancia=function(in_id) {
		
		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/registrar_instancia.php?in_id='+in_id,'ver_casos',
							'width=800, height=600, toolbar=false, scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}
	
	ver_caso=function(caso_id) {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/visualizar_caso.php?caso_id='+caso_id,'ver_casos',
							'width=800, height=600, toolbar=false, scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}

	crear_caso=function() {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('listas_dinamicas/crear_caso.php?'+$('lista_id').serialize(),'ver_casos',
							'width=800, height=600, toolbar=false scrollbars=yes'+
							', top='+top+', left='+left);
							
		win.focus();
		
	}

	seleccion_multiple=function() {

		var top=Math.round(screen.height/2)-300;
		var left=Math.round(screen.width/2)-400;
		
		var win=window.open('','sel_multiple',
							'width=800, height=600, toolbar=false scrollbars=yes'+
							', top='+top+', left='+left);

		$('datos_listado').target='sel_multiple';

		$('datos_listado').action='listas_dinamicas/seleccion_multiple.php';

		$('datos_listado').submit();
		
							
		win.focus();
		
	}


	
	cargar_lista();

</script>

<center>

<form id='datos_listado' name='datos_listado' method='post'
target='_self' onSubmit='return false;'>

<input type='hidden' id='xls' name='xls' value='0' />

<input type='hidden' id='pat' name='pat' value='' />
<input type='hidden' id='filtrogar' name='filtrogar' value='' />

<div class='sub-content' style='width:950px;'>
<div class='sub-content'>
<table style='width:100%;'>
<tr><td style='width:25px;'>
<img src='iconos/table_lightning.png' style='width:20px;height:20px;' />
</td><td>
<select id='lista_id' name='lista_id' style='font-size:14px;' onChange='cargar_lista();'>
<?php echo $listahtml; ?>
</select>
<input type='button' style='font-size:14px;' id='' name='' value='-- Actualizar --' onClick='cargar_lista();' />
</td><td style='text-align:right;'>
<input type='button' value='Selecci&oacute;n M&uacute;ltiple...' onClick='seleccion_multiple();' />
</td></tr>
</table>
</div>

<div id='lista_tabla' class='sub-content2' style='height:350px;overflow:auto;'>

</div>

<center>
<table style='width:100%;'>
<tr>
<td style='text-align:left;font-size:14px;'> 
<input type='checkbox' id='ver_detalle' name='ver_detalle' onChange='cargar_listado();' CHECKED='1' /> Ver Detalle
&nbsp;&nbsp;
<input type='checkbox' id='ver_resumen' name='ver_resumen' onChange='cargar_listado();' /> Ver Resumen
&nbsp;&nbsp;
<input type='button' id='quitar_filtro' value='-- Desactivar Filtro... --' onClick='$("pat").value="";$("filtrogar").value="";cargar_listado();' />


</td>
<td style='text-align:right;'>

<input type='button' value='-- Guardar Registros... --' onClick='guardar_listado();' />
<input type='button' value='-- Imprimir Listado... --' onClick='imprimir_listado();' />
<input type='button' value='-- Descargar XLS... --' onClick='descargar_xls();' />

</td>
</tr>
</table>
</center>

</div>

</form>

</center>

