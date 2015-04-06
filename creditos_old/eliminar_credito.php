<?php 

	require_once('../conectar_db.php');

	$bolnum=$_GET['bolnum'];
	
	if(!_cax(13)) {
	
		die('ACCESO NO AUTORIZADO.');	
	
	}
	
	$bol=cargar_registro("SELECT *, bolfec::date AS bolfec FROM boletines WHERE bolnum=$bolnum");
	
	if($bol['crecod']*1!=0)
		$cre=cargar_registro("SELECT * FROM creditos WHERE crecod=".$bol['crecod']);
	else
		$cre=false;
		
	if($cre) {
		$bols=cargar_registros_obj("SELECT *, bolfec::date AS bolfec FROM boletines WHERE crecod=".$bol['crecod']." ORDER BY bolnum");
		$cuos=cargar_registros_obj("SELECT * FROM cuotas WHERE crecod=".$bol['crecod']);	
	} else {
		$bols=false;
		$cuos=false;
	}

?>

<html>
<title>Eliminar Bolet&iacute;n / Cr&eacute;dito</title>

<?php cabecera_popup('..'); ?>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../iconos/delete.png' />
<b>Eliminar Bolet&iacute;n / Cr&eacute;dito</b>
</div>

<form id='' name='' action='sql_eliminar.php' method='post' onSubmit='
	var conf = confirm( "NO HAY OPCIONES PARA DESHACER --- &iquest;EST&Aacute; SEGURO?".unescapeHTML() );
	return conf;
'>
<input type='hidden' id='bolnum' name='bolnum' value='<?php echo $bolnum; ?>' />

<div class='sub-content'>

<table style='width:100%;'>

<tr class='tabla_header'><td>N&uacute;mero</td><td>Fecha</td><td>Monto $</td></tr>
<?php 

	if($bols) {
		for($i=0;$i<sizeof($bols);$i++) {
			$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
			print("<tr class='$clase'>
			<td style='text-align:center;'>".vboletin($bols[$i]['bolnum'],false,'../')."</td>			
			<td style='text-align:center;'>".$bols[$i]['bolfec']."</td>			
			<td style='text-align:right;font-weight:bold;'>$ ".number_format($bols[$i]['bolmon'],0,',','.').".-</td>			
			</tr>");
		}
	} else {

			print("<tr class='tabla_fila'>
			<td style='text-align:center;'>".vboletin($bol['bolnum'],false,'../')."</td>			
			<td style='text-align:center;'>".$bol['bolfec']."</td>			
			<td style='text-align:right;font-weight:bold;'>$ ".number_format($bol['bolmon'],0,',','.').".-</td>			
			</tr>");
	
	}

?>
</table>

</div>

<center>
<input type='submit' id='' name='' value='--- Confirmar Eliminaci&oacute;n ---'>
</center>

</form>

</body>
</html>
