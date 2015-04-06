<?php

	require_once('../../conectar_db.php');

	$e=cargar_registros_obj("SELECT * FROM especialidades ORDER BY esp_desc", true);

?>

<table style='width:100%;'>
<tr class='tabla_header'>
<td>Nombre</td>
<td>Imprime Receta</td>
<td>Solicita Ficha</td>
</tr>

<?php 

function chkbox($val, $id) {
	if($val=='t') $chk='CHECKED'; else $chk='';

	return "<center><input type='checkbox' id='$id' name='$id' onClick='cambiar_esp(\"$id\");' $chk /></center>";

}

for($i=0;$i<sizeof($e);$i++) {

	$clase=($i%2==0?'tabla_fila':'tabla_fila2');

	print("
		<tr class='$clase' onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'>
		<td style='text-align:left;font-weight:bold;'>".$e[$i]['esp_desc']."</td>
		<td style='text-align:left;font-weight:bold;'>".chkbox($e[$i]['esp_receta'], 'receta_'.$e[$i]['esp_id'])."</td>
		<td style='text-align:left;font-weight:bold;'>".chkbox($e[$i]['esp_ficha'], 'ficha_'.$e[$i]['esp_id'])."</td>
</tr>
	");


}		

?>


</table>
