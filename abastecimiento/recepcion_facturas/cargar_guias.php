<?php 

	require_once('../../conectar_db.php');
	
	$prov_id=$_POST['prov_id']*1;
		
	$g=cargar_registros_obj("
		SELECT *, (COALESCE(subt1,0)+COALESCE(subt2,0)) AS subtotal FROM (
		SELECT *,
		(SELECT SUM(stock_subtotal) FROM stock WHERE stock_log_id IN (SELECT log_id FROM logs WHERE log_doc_id=doc_id)) AS subt1, 
		(SELECT SUM(serv_subtotal) FROM servicios WHERE serv_log_id IN (SELECT log_id FROM logs WHERE log_doc_id=doc_id)) AS subt2
		FROM documento 
		LEFT JOIN orden_compra ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
		WHERE doc_prov_id=$prov_id AND doc_tipo=0
		ORDER BY doc_fecha_recepcion ASC) AS foo;
	");
	
	$oc=array();
	

?>

<center>
<h3>Gu&iacute;as Pendientes: <?php if($g) echo sizeof($g); else echo '0'; ?></h3>

<table style='width:520px;'>
	<tr class='tabla_header'>
		<td>Sel.</td>
		<td>Fecha Recep.</td>
		<td>Orden Compra</td>
		<td>Num. Documento</td>
		<td>Neto</td>
		<td>Detalle</td>
	</tr>
	
<?php 

	if($g)
	for($i=0;$i<sizeof($g);$i++) {
		
		$clase=($i%2==0)?'tabla_fila':'tabla_fila2';
		
		if($g[$i]['orden_numero']!='')
			$oc[]=$g[$i]['orden_numero'];
		
		$doc_id=$g[$i]['doc_id'];
		
		$g[$i]['detalle1']=cargar_registros_obj("SELECT *, (stock_subtotal/stock_cant) AS punit FROM stock JOIN articulo ON stock_art_id=art_id LEFT JOIN bodega_forma ON art_forma=forma_id WHERE stock_log_id IN (SELECT log_id FROM logs WHERE log_doc_id=$doc_id);");
		$g[$i]['detalle2']=cargar_registros_obj("SELECT *, (serv_subtotal/serv_cant) AS punit FROM servicios WHERE serv_log_id IN (SELECT log_id FROM logs WHERE log_doc_id=$doc_id);");
		
		print("
		<tr class='$clase'>
		<td><center><input type='checkbox' id='doc_".$g[$i]['doc_id']."' name='doc_".$g[$i]['doc_id']."' onChange='redibujar_tabla();recalcular_total();' CHECKED /></center></td>
		<td style='text-align:center;font-weight:bold;'>".$g[$i]['doc_fecha_recepcion']."</td>
		<td style='text-align:center;font-weight:bold;font-size:12px;'>".$g[$i]['orden_numero']."</td>
		<td style='text-align:center;font-weight:bold;font-size:20px;'>".$g[$i]['doc_num']."</td>
		<td style='text-align:right;font-weight:bold;'>$ ".number_format($g[$i]['subtotal'],0,',','.')." .-</td>
		<td style='text-align:center;font-weight:bold;'>
		<center>
		<img src='iconos/magnifier.png' style='width:12px;height:12px;cursor:pointer;' onClick='abrir_recep(".$g[$i]['doc_id'].");' />
		</center>
		</td>
		</tr>
		");
		
	}
	
	$oc=array_values(array_unique($oc));
	
	$ochtml="<select id='orden_compra' name='orden_compra' style='font-size:20px;font-weight:bold;' onChange='marcar_oc();'>";
	
	for($i=0;$i<sizeof($oc);$i++) {
		$ochtml.="<option value='".$oc[$i]."'>".$oc[$i]."</option>";
	}
	
	$ochtml.="</select>";

?>
</table>
</center>

<script>

guias = <?php echo json_encode($g); ?>;

$('listado_oc').innerHTML="<?php echo $ochtml; ?>";

marcar_oc();

recalcular_total();

</script>
