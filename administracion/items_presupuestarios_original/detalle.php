<?php 

	require_once("../../conectar_db.php");
	
	$item=pg_escape_string($_GET['item_codigo']);
	
	list($mes, $anio) = explode('/', $_GET['mesanio']);
	
	$f1=date('d/m/Y', mktime(0,0,0,$mes*1,1,$anio*1));
	$f2=date('d/m/Y', mktime(0,0,0,($mes*1)+1,1,$anio*1));

	$oc=cargar_registros_obj("
		select * from orden_detalle
		join articulo on ordetalle_art_id=art_id AND art_item ILIKE '$item%'
		join orden_compra on ordetalle_orden_id=orden_id AND 
		orden_fecha::date>='$f1' AND orden_fecha::date<'$f2'
		ORDER BY orden_fecha
	", true);

?>
	
<html>
<title>Detalle Item Presupuestario</title>

<?php cabecera_popup('../..'); ?>

<script>
	
	function abrir_cert(cert_id) {
		
		window.open('cert_pdf.php?cert_id='+cert_id,'_self');
		
	}
	
	function abrir_orden(orden_id) {

	  l=(screen.availWidth/2)-250;

	  t=(screen.availHeight/2)-200;

	  

	  win = window.open('../../visualizar.php?orden_id='+orden_id, 'ver_orden',

						'scrollbars=no, toolbar=no, left='+l+', top='+t+', '+

						'resizable=no, width=700, height=465');

						

	  win.focus();



	}


</script>

<body class='popup_background fuente_por_defecto'>

<div class='sub-content'>
<img src='../../iconos/money.png'> <b>Detalle Compromisos Presupuestarios</b>
</div>


<table style='width:100%;font-size:11px;'>

	<tr class='tabla_header'>
		<td>#</td>
		<td>Fecha</td>
		<td>Orden de Compra</td>
		<td>C&oacute;digo</td>
		<td>Descripci&oacute;n</td>
		<td>Cantidad</td>
		<td>Subtotal</td>
		<td>Ver</td>
	</tr>
	
<?php 

	$suma=0;

	if($oc)
	for($i=0;$i<sizeof($oc);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		echo "
		<tr class='$clase'
		onMouseOver='this.className=\"mouse_over\";'
		onMouseOut='this.className=\"$clase\";'>
		<td style='text-align:right;font-weight:bold;' class='tabla_header'>".($i+1)."</td>
		<td style='text-align:center;'>".substr($oc[$i]['orden_fecha'],0,10)."</td>
		<td style='text-align:center;font-weight:bold;'>".$oc[$i]['orden_numero']."</td>
		<td style='text-align:right;'>".$oc[$i]['art_codigo']."</td>
		<td style='text-align:left;'>".$oc[$i]['art_glosa']."</td>
		<td style='text-align:right;font-weight:bold;'>".number_format($oc[$i]['ordetalle_cant'],0,',','.')."</td>
		<td style='text-align:right;font-weight:bold;'>$".number_format($oc[$i]['ordetalle_subtotal'],0,',','.').".-</td>
		<td><center><img src='../../iconos/layout.png' style='cursor:pointer;' onClick='abrir_orden(".$oc[$i]['orden_id'].");' /></center></td>
		</tr>
		";
		
		$suma+=$oc[$i]['ordetalle_subtotal']*1;
		
	}



		echo "
		<tr class='tabla_header'>
		<td>&nbsp;</td>
		<td style='text-align:center;font-weight:bold;' colspan=5>Total del Mes:</td>
		<td style='text-align:right;font-weight:bold;'>$".number_format($suma,0,',','.').".-</td>
		<td>&nbsp;</td>
		</tr>
		";


?>	
	
	
</table>

</body>
</html>
