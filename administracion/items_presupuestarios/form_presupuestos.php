<?php 

	require_once('../../conectar_db.php');
	
	$centros=cargar_registros_obj("
	SELECT *,
	length(regexp_replace(centro_ruta, '[^.]', '', 'g')) AS centro_nivel
	FROM centro_costo 
	ORDER BY centro_ruta;
	", true);
	
	$bodegas=cargar_registros_obj("SELECT * FROM bodega ORDER BY bod_glosa",true);
	
?>

<center>

<div class='sub-content' style='width:950px;'>

<div class='sub-content'>
<img src='iconos/coins.png' /> <b>Asignaci&oacute;n de Presupuestos</b>
</div>

<div class='sub-content2' style='height:350px;overflow:auto;'>

<table>
	<tr class='tabla_header'>
		<td style='width:400px;'>Centro de Costo</td>
<?php 
		for($j=0;$j<sizeof($bodegas);$j++)
			print("<td>".($bodegas[$j]['bod_glosa'])."</td>");
?>
	</tr>

<?php
	
	$c=0;
	
	for($i=0;$i<sizeof($centros);$i++) {
	
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
	
		$espaciado = str_repeat("<img src='iconos/blank.gif'>", $centros[$i]['centro_nivel']*2);
	
		print("<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";' 
		onMouseOut='this.className=\"$clase\";'>
		<td style='width:400px;white-space:nowrap;padding:3px;'>
		$espaciado".$centros[$i]['centro_nombre']."
		</td>");
		
		for($j=0;$j<sizeof($bodegas);$j++)
			print("<td><input type='text' id='cc_$i_$j' name='cc_$i_$j' style='text-align:right;' value='0' /></td>");
			
		print("</tr>");
		
		$c++;
	
	}

?>

</table>

</div>

</div>

</center>
