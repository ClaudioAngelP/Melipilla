<?php 

	require_once('../conectar_db.php');
	
	$lista_id=$_GET['lista_id']*1;

?>

<html>

<title>Creaci&oacute;n de Casos Nuevos</title>

<?php cabecera_popup('..'); ?>

<script>

function guardar_caso() {
	
	var myAjax=new Ajax.Request('sql_caso.php', {
			method:'post', parameters: $('caso').serialize(), onComplete:function() {
				var fn = window.opener.cargar_lista.bind(window.opener);
				fn();
				window.close();
			}
	});
	
}

</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../iconos/table_add.png' />
Crear Caso
</div>

<form id='caso' name='caso' onSubmit='return false;'>

<input type='hidden' name='lista_id' value='<?php echo $lista_id; ?>' />

<table style='width:100%;'>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Paciente:</td>
		<td class='tabla_fila'>
		
		<table id='busca_pac' cellpadding=0 cellspacing=0><tr><td>
		<input type='hidden' id='pac_id' name='pac_id' value='-1' />
		<input type='text' size=45 id='pac_rut' name='pac_rut' value='' />
		</td></tr></table>

		
		</td>
	</tr>

	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Nombre:</td>
		<td class='tabla_fila' id='pac_nombre'>
		<i>(Seleccione Paciente...)</i>
		</td>
	</tr>
	<tr>
		<td class='tabla_fila2' style='text-align:right;'>Comentarios:</td>
		<td class='tabla_fila'>
			<textarea id='comentarios' name='comentarios' style='width:100%;height:50px;'></textarea>
		</td>
	</tr>
</table>

<center>
<input type='button' value='-- Crear Caso... --' onClick='guardar_caso();' />
</center>

</form>

</body>

</html>

<script>

    seleccionar_paciente = function(d) {
    
		$('pac_rut').value=d[0];
		$('pac_nombre').innerHTML=d[2];
		$('pac_id').value=d[4];
		$('pac_ficha').innerHTML=d[3];
    	
    }

    autocompletar_pacientes = new AutoComplete(
      'pac_rut', 
      '../autocompletar_sql.php',
      function() {
        if($('pac_rut').value.length<2) return false;

        return {
          method: 'get',
          parameters: 'tipo=pacientes&nompac='+encodeURIComponent($('pac_rut').value)
        }
      }, 'autocomplete', 500, 200, 150, 1, 3, seleccionar_paciente);
   

</script>
