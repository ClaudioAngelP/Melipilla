<?php 

	require_once('../../conectar_db.php');

	$clases=cargar_registros_obj("SELECT DISTINCT tsep_clase, tsep_estructura FROM tipo_sepultura LEFT JOIN inventario ON tsep_clase=sep_clase ORDER BY tsep_clase");

	for($i=0;$i<sizeof($clases);$i++) {
		$clase[$i][0]=htmlentities($clases[$i]['tsep_clase']);
		$clase[$i][1]=htmlentities($clases[$i]['tsep_estructura']);
	}

?>

<script>

clases=<?php echo json_encode($clase); ?>;

select_clases=function() {

	var html='<select id="sel_clases" name="sel_clases" onClick="">';
	
	for(var i=-1;i<clases.length;i++) {
		var sel='';
		if(i==-1)
			html+='<option value="-1" '+sel+'>(Todas las Clases...)</option>';
		else 
			html+='<option value="'+clases[i][0]+'" '+sel+'>'+clases[i][0]+'</option>';
	}

	html+='</select>';

	$('select_clases').innerHTML=html;
	
}


listar_fosa=function() {

	var myAjax=new Ajax.Updater(
		'listado',
		'ingresos/informe_fosa/listado_fosa.php',
		{
			method:'post',
			parameters:$('filtro').serialize(),
			onComplete: function() {
				$('imprimir').style.display='';	
			}	
		}	
	);
	
}

mover = function(us_id, fosa) {

	conf=confirm( ("&iquest;Desea mover al fallecido a la fosa?").unescapeHTML() );
	
	if(!conf) return;

	var myAjax=new Ajax.Request(
		'ingresos/informe_fosa/sql_fosa.php',
		{
			method:'post',
			parameters:'us_id='+us_id+'&fosa='+encodeURIComponent(fosa),
			onComplete:function(resp) {
				alert('Sepultado movido a fosa exitosamente.');
				listar_fosa();
			}	
		}	
	);
	
}

marcar = function(us_id) {

	conf=confirm( ("&iquest;Desea marcar al fallecido con un a&ntilde;o adicional de espera para traslado a fosa?").unescapeHTML() );
	
	if(!conf) return;

	var myAjax=new Ajax.Request(
		'ingresos/informe_fosa/sql_fosa.php',
		{
			method:'post',
			parameters:'us_id='+us_id,
			onComplete:function(resp) {
				alert( 'Sepultado marcado para fosa en un a&ntilde;o m&aacute;s exitosamente.'.unescapeHTML() );
				listar_fosa();
			}	
		}	
	);
	
}

imprimir_informe=function() {

	_datos = $('listado').innerHTML;
  
  _separador = '<h3>Informe de Usuarios Vencidos</h3><hr>';
  
  imprimirHTML(_separador+_datos);

	
}


</script>

<center>

<div class='sub-content' style='width:750px;'>
<div class='sub-content'>
<img src='iconos/clock.png'>
<b>Informe de Usuarios Vencidos</b>
</div>

<form id='filtro' name='filtro' onSubmit='return false;'>

<div class='sub-content'>
<table style='width:100%;'>

<tr>
<td style='text-align:right;'>
Filtro de Clases:
</td>
<td>
<span id='select_clases' style=''></span>
</td></tr>

<tr>
<td style='text-align:right;'>Filtro de C&oacute;digos:</td>
<td>
<input type='text' id='filtro_codigo' name='filtro_codigo' value='' />
</td>
</tr>


<tr><td style='text-align:right;width:30%;'>
A&ntilde;os de Vencimiento:
</td><td>
<input type='text' id='anios' name='anios' value='1' size=5 />
</td></tr>

<tr><td style='text-align:right;'>
Cantidad a Mover:
</td><td>
<input type='text' id='cant' name='cant' value='' size=5 DISABLED />
<input type='checkbox' id='traslado' name='traslado' 
onClick='
	if(this.checked) { $("cant").disabled=false; $("cant").value="10"; } 
	else { $("cant").disabled=true; $("cant").value=""; } ' 
/> Realizar Traslado a Fosa
</td></tr>
<tr><td colspan=2>
<center>
<input type='button' value='Actualizar Listado...' onClick='listar_fosa();' />
</center>
</td></tr>

</table>
</div>

</form>

<div class='sub-content2' id='listado' style='height:230px;overflow:auto;'>

</div>

<center>
<input type='button' id='imprimir' name='imprimir' style='display:none;' 
onClick='imprimir_informe();' 
value='-- Imprimir Informe --' />
</center>

</div>

</center>

<script>
	select_clases();
</script>