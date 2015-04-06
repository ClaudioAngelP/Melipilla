<?php 

	require_once('../conectar_db.php');

	function vboletin($bolnum,$xls=false,$ruta='') {
	
			if(!$xls) {
				return "<span style='cursor:pointer;text-decoration:underline;font-weight:bold;color:blue;' onClick='abrir_boletin($bolnum, \"$ruta\");'>
							".number_format($bolnum,0,',','.')."<img src='".$ruta."iconos/magnifier.png' width=10 height=10>
							</span>";
			} else {
				return ($bolnum*1);
			}
	
	}

	
	$bolnum=$_GET['bolnum'];
	
	if(!_cax(322)) {
	
		die('ACCESO NO AUTORIZADO.');	
	
	}
	
	$bol=cargar_registro("SELECT *, bolfec::date AS bolfec, (SELECT ac_id FROM apertura_cajas WHERE apertura_cajas.func_id=boletines.func_id AND bolfec BETWEEN ac_fecha_apertura AND COALESCE(ac_fecha_cierre, CURRENT_TIMESTAMP)) AS ac_id FROM boletines WHERE bolnum=$bolnum");
	$ac=cargar_registro("SELECT * FROM apertura_cajas WHERE ac_id=".$bol['ac_id']);
	
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
<title>Eliminar Comprobante de Pago</title>

<?php cabecera_popup('..'); ?>


<script>

<?php if($ac['ac_fecha_cierre']!='') { ?>

alert('ERROR:\n\nCIERRE DE CAJA YA FUE REALIZADO, NO ES POSIBLE ANULAR COMPROBANTE.');
window.close();

</script>

<?php exit(); } ?>

function validar() {

	if(trim($("motivo").value)=='') {
		alert("Debe ingresar motivo de anulaci&oacute;n".unescapeHTML());
		return;
	}
		
	var conf = confirm( "NO HAY OPCIONES PARA DESHACER --- &iquest;EST&Aacute; SEGURO?".unescapeHTML() );
	if(!conf) return;

	$('datos').submit();

	//window.open('visualizar_boletin.php?bolnum='+$('bolnum').value,'_self');
}


</script>

<body class='fuente_por_defecto popup_background'>

<div class='sub-content'>
<img src='../iconos/delete.png' />
<b>Eliminar Bolet&iacute;n / Cr&eacute;dito</b>
</div>

<form id='datos' name='datos' action='sql_eliminar.php' method='post' onSubmit='return false;'>
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
Ingrese Motivo Anulaci&oacute;n:<br/>
<textarea id='motivo' name='motivo' rows=3 cols=40></textarea>
<br/>
<input type='button' onClick='validar();' id='' name='' value='--- Confirmar Anulaci&oacute;n ---'>
</center>

</form>

</body>
</html>
