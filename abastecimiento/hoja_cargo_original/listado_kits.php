<?php 

	require_once('../../conectar_db.php');
	
	$bod_id=$_POST['bod_id'];
	
	if(isset($_POST['kit_id'])) {
		
		$kit_id=$_POST['kit_id']*1;
		
		if(!isset($_POST['eliminar'])) {
			
			$kit_codigo=pg_escape_string(utf8_decode($_POST['kit_codigo_'.$kit_id]));
			$kit_nombre=pg_escape_string(utf8_decode($_POST['kit_nombre_'.$kit_id]));
			$kit_detalle=pg_escape_string(utf8_decode($_POST['kit_detalle_'.$kit_id]));
			
			if($kit_id==0)
				pg_query("INSERT INTO articulo_kits VALUES (DEFAULT, $bod_id, '$kit_codigo', '$kit_nombre', '$kit_detalle');");
			else
				pg_query("UPDATE articulo_kits SET kit_codigo='$kit_codigo', kit_nombre='$kit_nombre', kit_detalle='$kit_detalle' WHERE kit_id=$kit_id;");
			
		} else {
			pg_query("DELETE FROM articulo_kits WHERE kit_id=$kit_id;");
		}
		
	}
	
	$q=cargar_registros_obj("SELECT * FROM articulo_kits WHERE bod_id=$bod_id ORDER BY kit_nombre;", true);

?>

<table style='width:100%;'>
	<tr class='tabla_header'>
		<td>C&oacute;digo</td>
		<td style='width:25%;'>Nombre</td>
		<td style='width:40%;'>Detalle de Componentes</td>
		<td style='width:5%;'>Acciones</td>
	</tr>
	
<?php 

	$i=0;

	if($q)
	for($i=0;$i<sizeof($q);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		print("<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'>
		<td><input style='border:none;width:100%;background:inherit;' type='text' id='kit_codigo_".$q[$i]['kit_id']."' name='kit_codigo_".$q[$i]['kit_id']."' value='".$q[$i]['kit_codigo']."' /></td>
		<td><input style='border:none;width:100%;background:inherit;' type='text' id='kit_nombre_".$q[$i]['kit_id']."' name='kit_nombre_".$q[$i]['kit_id']."' value='".$q[$i]['kit_nombre']."' /></td>
		<td><input style='border:none;width:100%;background:inherit;' type='text' id='kit_detalle_".$q[$i]['kit_id']."' name='kit_detalle_".$q[$i]['kit_id']."' value='".$q[$i]['kit_detalle']."' /></td>
		<td>
		<center>
		<img src='../../iconos/disk.png' onClick='guardar(".$q[$i]['kit_id'].");' style='cursor:pointer;' />
		<img src='../../iconos/delete.png' onClick='eliminar(".$q[$i]['kit_id'].");' style='cursor:pointer;' />
		</center>
		</td>
		</tr>");
		
	}

		$clase=($i++%2==0)?'tabla_fila':'tabla_fila2';
		
		print("<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'>
		<td><input style='border:none;width:100%;background:inherit;' type='text' id='kit_codigo_0' name='kit_codigo_0' value='' /></td>
		<td><input style='border:none;width:100%;background:inherit;' type='text' id='kit_nombre_0' name='kit_nombre_0' value='' /></td>
		<td><input style='border:none;width:100%;background:inherit;' type='text' id='kit_detalle_0' name='kit_detalle_0' value='' /></td>
		<td>
		<center><img src='../../iconos/disk.png' onClick='guardar(0);' style='cursor:pointer;' /></center>
		</td>
		</tr>");

?>
</table>
