<?php 

	require_once('../../conectar_db.php');
	
	$xls=isset($_GET['xls']);
	
	$bod_id=$_GET['bodega']*1;
	$critico=$_GET['critico']*1;
	
	if($critico==1)
		$wr='stock_actual<=critico_critico';
	else if($critico==2)
		$wr='stock_actual<=critico_pedido';
	else
		$wr='true';
		
	$b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id");
	
	$compra=($b['bod_id_reposicion']=='0');
	
	$order=$compra?'convenio_licitacion, convenio.convenio_id ASC, art_nombre':'convenio_licitacion,art_nombre';
	
  	$consulta="SELECT * FROM(
	SELECT *, 
        (
                COALESCE
		(
			( 
				SELECT SUM(-stock_cant) 
				FROM stock 
				JOIN logs ON stock_log_id=log_id AND log_fecha>=(CURRENT_DATE-('3 months'::interval)) AND log_tipo IN (2,9,15,16,17) 
				WHERE stock_art_id=id AND stock_bod_id=$bod_id AND stock_cant<0 
			)/3
			,
			0
		)
        
        ) AS consumo,
        (
		SELECT SUM(pedidod_cant) FROM pedido_detalle
		JOIN pedido  USING (pedido_id)
		WHERE art_id=id AND pedido_estado IN (0,1) AND destino_bod_id=0 AND NOT pedidod_estado
	) AS transito,
	( SELECT SUM(pedidod_cant) FROM pedido_detalle JOIN pedido USING (pedido_id) 
		WHERE art_id=id AND pedido_estado IN (0,1) AND destino_bod_id=0 
		AND NOT pedidod_estado AND NOT pedidod_tramite) AS interno,
	( SELECT SUM(pedidod_cant) FROM pedido_detalle JOIN pedido USING (pedido_id) 
		WHERE art_id=id AND pedido_estado IN (0,1) AND destino_bod_id=0 
		AND NOT pedidod_estado AND pedidod_tramite) AS externo	
	
	FROM (
	SELECT *, articulo.art_id AS id, (critico_gasto - stock_actual) AS pedido_cantidad FROM (
	SELECT *,
	COALESCE(calcular_stock(critico_art_id,$bod_id),0) AS stock_actual 
	FROM stock_critico 
	WHERE critico_bod_id=$bod_id
	) AS foo 
	JOIN articulo ON critico_art_id=art_id
	JOIN articulo_bodega ON artb_art_id=articulo.art_id AND artb_bod_id=$bod_id
	LEFT JOIN item_presupuestario ON art_item=item_codigo
	LEFT JOIN convenio_detalle AS cd ON cd.art_id=articulo.art_id
	LEFT JOIN convenio ON cd.convenio_id=convenio.convenio_id AND convenio_fecha_inicio<=CURRENT_DATE AND convenio_fecha_final>CURRENT_DATE
	LEFT JOIN proveedor ON convenio.prov_id=proveedor.prov_id
	WHERE $wr AND art_activado
	ORDER BY $order) AS foo2)AS foo3 WHERE (pedido_cantidad - case when transito is null then 0 else transito end) > 0;
	";
       //print($consulta);
  	$l=pg_query($consulta);
	
	$c=0;
	$html='';

	
	while($r=pg_fetch_assoc($l)) {
		
		$clase=($c%2==0)?'tabla_fila':'tabla_fila2';
		
		if($r['stock_actual']*1<=$r['critico_critico']*1) {
			$estilo='color:red;';
		} else {
			$estilo='';
		}
		
		if($r['consumo']==0 OR $r['consumo']=='') $r['consumo']=(0.01);
		else $r['consumo']=number_format($r['consumo'],0,',','.');
		
		if(!isset($_GET['xls'])) {
			$html.="<input type='hidden' id='art_stock_".$r['id']."' name='art_stock_".$r['id']."' value='".$r['stock_actual']."' />";
			$html.="<input type='hidden' id='consumo_".$r['id']."' name='consumo_".$r['id']."' value='".$r['consumo']."' />";
			$html.="<input type='hidden' id='pedido_".$r['id']."' name='pedido_".$r['id']."' value='".$r['pedido_cantidad']."' />";
		}
		
		$html.="
			<tr class='$clase' style='$estilo'>
				<td class='tabla_header' style='text-align:right;font-weight:bold;'>".($c+1)."</td>
				<td style='text-align:right;font-weight:bold;'>".$r['art_codigo']."</td>
				<td style='width:50%;font-size:11px;'>".htmlentities($r['art_glosa'])."</td>";
		
		if(isset($_GET['xls'])) {
			
			$html.="<td style='text-align:right;'>".htmlentities($r['item_glosa'])."</td>";			
					
		}
			
		$html.="
				<td style='text-align:right;font-weight:bold;'>".number_format($r['stock_actual'],0,',','.')."</td>
				
				<td style='text-align:right;'>".$r['consumo']."</td>
				
				<td style='text-align:right;'>".number_format($r['critico_pedido'],0,',','.')."</td>
				<td style='text-align:right;'>".number_format($r['critico_critico'],0,',','.')."</td>";

		//$html.="<td style='text-align:right;'>".number_format($r['transito'],0,',','.')."</td>";
		$html.="<td style='text-align:right;'>".number_format($r['interno'],0,',','.')."</td>
				<td style='text-align:right;'>".number_format($r['externo'],0,',','.')."</td>";

		$html.="<td style='text-align:right;' id='dias_stock_".$r['id']."'>".number_format(($r['consumo']*1==0?'0':(($r['stock_actual']/$r['consumo'])*30)),2,',','.')."</td>
				<td style='text-align:right;' id='dias_repo_".$r['id']."'>".number_format(($r['consumo']*1==0?'0':(($r['pedido_cantidad']/$r['consumo'])*30)),2,',','.')."</td>
				
				";

		if(!$xls)
			$html.="
				<td style='text-align:center;font-weight:bold;'>
				<center>
				<input type='text' id='art_".$r['id']."' name='art_".$r['id']."' 
				style='text-align:right;' size=10 onKeyUp='actualizar_datos(".$r['id'].");'
				value='".($r['pedido_cantidad']-$r['transito']>=0?ceil($r['pedido_cantidad']-$r['transito']):'0')."' />
				</center>
				</td>
			";
		else
			$html.="
				<td style='text-align:right;font-weight:bold;'>
				".($r['pedido_cantidad']-$r['transito']>=0?ceil($r['pedido_cantidad']-$r['transito']):'0')."
				</td>
			";
			
		if($compra) {
			
			if($r['convenio_licitacion']=='' AND $r['convenio_id']*1!=0)
				$r['convenio_licitacion']='(n/a)';
			
			if($r['convenio_id']*1!=0) {
				$html.="<td style='text-align:center;'>".htmlentities($r['convenio_licitacion'])."</td>";			
				$html.="<td style='text-align:left;'>".htmlentities($r['convenio_nombre'])."</td>";			
			} else {
				$html.='<td colspan=2 style="text-align:center;"><i>(Sin Contrato...)</i></td>';
			}
			
		}
			
		$html.="</tr>";
			
		$c++;
		
	}
	
	if(isset($_GET['xls'])) {
	    header("Content-type: application/vnd.ms-excel");
      	header("Content-Disposition: filename=\"ListadoReposicion.XLS\";");
    	$strip_html=true;
    	$border='border=1';
  	} else {$border='';}

?>

<table style='width:100%;'>

<tr class='tabla_header' <?php echo $border;?>>
<td>#</td>
<td>C&oacute;digo</td>
<td>Nombre</td>
<?php  if(isset($_GET['xls'])) { ?>
<td>Item Presupuestario</td>			
<?php } ?>					
<td>Stock</td>
<td>Prom. Consumo</td>
<td>P.Pedido</td>
<td>P.Cr&iacute;tico</td>
<!--<td>En Tr&aacute;nsito</td>-->
<td>Tr&aacute;nsito Interno</td>
<td>Tr&aacute;nstito Externo</td>
<td>D&iacute;as Stock</td>
<td>D&iacute;as Duraci&oacute;n Reposici&oacute;n</td>
<td>Reposici&oacute;n</td>
<?php if($compra) { ?><td>Licitaci&oacute;n</td><td>Convenio</td><?php } ?>
</tr>
<?php echo $html; ?>
</table>
