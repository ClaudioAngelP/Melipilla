<?php 

	require_once('../../conectar_db.php');
	
	$lista=cargar_registros_obj("SELECT * FROM lista_dinamica ORDER BY lista_id", true);

?>

<script>

	editar_lista=function(lista_id) {
	
      top=Math.round(screen.height/2)-200;
      new_win = 
      new_win.focus();
	}
	

</script>

<center>

<div class='sub-content' style='width:800px;'>

<div class='sub-content'>
<img src='iconos/building.png'>
<b>Mantenedor de Listas Din&aacute;micas</b>
</div>

<div class='sub-content2' style='height:350px;overflow:auto;' id='listado'>

<?php 

	print("<table style='width:100%;'><tr class='tabla_header'>
	<td>Nombre Lista Din&aacute;mica</td>
	<td style='width:10%;'>Editar</td>
	</tr>");

	if($lista)
	for($i=0;$i<sizeof($lista);$i++) {
	
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';	
	
		print("
			<tr class='$clase'
			onMouseOver='this.className=\"mouse_over\";'
			onMouseOut='this.className=\"$clase\";'>
			<td>".$lista[$i]['lista_nombre']."</td>
			<td><center><img src='iconos/pencil.png' style='cursor:pointer;' 
			onClick='editar_lista(".$lista[$i]['lista_id'].");' /></center></td>
			</tr>		
		");	
		
	}
	
	print("</table>");

?>

</div>

<center>
<input type='button' id='' value='Crear Nueva Lista Din&aacute;mica...' onClick='editar_lista(0);' />
</center>

</div>

</center>