<?php 

	require_once('../../conectar_db.php');
	
	$convenio_id=$_GET['convenio_id']*1;
	
	pg_query("


	UPDATE convenio_detalle AS c1 SET 
	
	conveniod_monto_utilizado=(
			SELECT SUM(stock_subtotal) FROM convenio AS c2
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c2.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=1 AND log_doc_id=doc_id 
			JOIN stock ON stock_log_id=log_id AND stock_art_id=c1.art_id
			WHERE c2.convenio_id=c1.convenio_id
	), 
	
	conveniod_cantidad_recepcionada=(
			SELECT SUM(stock_cant) FROM convenio AS c4
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c4.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN documento ON doc_orden_id=orden_id OR doc_orden_desc=orden_numero
			JOIN logs ON log_tipo=1 AND log_doc_id=doc_id
			JOIN stock ON stock_log_id=log_id AND stock_art_id=c1.art_id
			WHERE c4.convenio_id=c1.convenio_id
	), 
	
	conveniod_monto_comprometido=(
			SELECT SUM(ordetalle_subtotal) FROM convenio AS c3
			JOIN convenio_detalle on convenio_Detalle.convenio_id=c3.convenio_id AND convenio_detalle.art_id=c1.art_id
			join orden_compra on orden_licitacion=convenio_licitacion AND orden_prov_id=prov_id
			JOIN orden_detalle ON ordetalle_orden_id=orden_id AND ordetalle_art_id=c1.art_id
			WHERE c3.convenio_id=c1.convenio_id
	)
	
	WHERE convenio_id=$convenio_id;
		
	");
	
	$c=cargar_registro("
		SELECT * FROM convenio 
		JOIN proveedor USING (prov_id)
		WHERE convenio_id=$convenio_id
	", true);
	
	$lic=pg_escape_string($c['convenio_licitacion']);
	$prov_id=$c['prov_id']*1;
	
	$d=cargar_registros_obj("
		SELECT * FROM convenio_detalle 
		JOIN articulo USING (art_id)
		WHERE convenio_id=$convenio_id
		ORDER BY art_glosa;
	", true);
	
	$o=cargar_registros_obj(
		  "SELECT orden_id, 
			COALESCE(orden_numero, orden_id::text) AS orden_numero, 
			date_trunc('second', orden_fecha) AS orden_fecha, prov_rut, prov_glosa, 
			orden_estado 
		FROM orden_compra 
		LEFT JOIN proveedor ON orden_prov_id=prov_id
		WHERE orden_licitacion='$lic' AND orden_prov_id=$prov_id
		ORDER BY orden_fecha",
		 false
		 );
  
?>

<html>
<title>Visualizar Convenio</title>

<?php cabecera_popup('../..'); ?>


<script>

function ver_detalle(conveniod_id)  {
	
	window.open('ver_convenio_detalle.php?conveniod_id='+conveniod_id,
    'win_talonarios');

	
}

</script>

<body class='fuente_por_defecto popup_background'>

<center>
<h2><?php echo $c['convenio_nombre']; ?></h2><br />
Proveedor: <?php echo '<b>'.$c['prov_rut'].'</b> '.$c['prov_glosa']; ?><br />
<h3>Monto Total: $<?php echo number_format($c['convenio_monto']*1,0,',','.'); ?>.-</h3>
<?php 

$disponible=$c['convenio_monto']*1;

	for($n=0;$n<sizeof($d);$n++) { 
				
		$disponible-=($d[$n]['conveniod_monto_utilizado']*1.19);	
	}
	if($disponible<0) $disponible=0;
	print("<h3>Monto Disponible: $".number_format($disponible*1,0,',','.').".-</h3>");
?>
<br /><br />
</center>

<table style='width:100%;font-size:12px;'>
	<tr class='tabla_header'>
		<td>#</td>
		<td>C&oacute;digo &Oacute;rden</td>
		<td>Fecha Ingreso</td>
		<td>Estado</td>
		<td>Ver</td>
	</tr>
	
<?php 

	$mc=0; $md=0;

	if($o)
	for($i=0;$i<sizeof($o);$i++) {
		
		$clase=$i%2==0?'tabla_fila':'tabla_fila2';
		
		switch($o[$i]['orden_estado']) {
      		case 0: $estado='Espera Recep.'; break;
      		case 1: $estado='Recep. Parcial'; break;
      		case 2: $estado='Recep. Completa'; break;
      		case 3: $estado='Recep. Anulada'; break;
    	}

		print("
			<tr class='$clase'>
			<td style='text-align:right;'>".($i+1)."</td>
			<td style='text-align:center;font-size:14px;font-weight:bold;'>".$o[$i]['orden_numero']."</td>
			<td style='text-align:center;'>".$o[$i]['orden_fecha']."</td>
			<td style='text-align:center;'>".$estado."</td>
			<td><center>
			<img src='../../iconos/magnifier.png' 
				onClick='window.opener.visualizar_orden(".$o[$i]['orden_id'].");' style='cursor:pointer;' />
			</center></td>
			</tr>
		");
		
	}
	
	print("
	<tr class='tabla_header'>
	<td colspan=4>&nbsp;</td>
	</tr>
	");

?>	
	
	
</table>

<center><br/><br/><input type='button' value='[ IMPRIMIR CONVENIO ]' onClick='window.print();'><br/><br/></center>


</body>
</html>
