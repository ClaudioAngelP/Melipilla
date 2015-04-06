<?php

  require_once('../../conectar_db.php');

	$bod_id=$_POST['bodega']*1;
	$critico=$_POST['critico']*1;
	
	if($critico==1)
		$wr='stock_actual<=critico_critico';
	else if($critico==2)
		$wr='stock_actual<critico_pedido';
	else
		$wr='true';
		
	$b=cargar_registro("SELECT * FROM bodega WHERE bod_id=$bod_id");
	
	$compra=($b['bod_id_reposicion']=='0');
	
	$order=$compra?'proveedor.prov_id,convenio_licitacion, convenio.convenio_id ASC, art_nombre':'proveedor.prov_id,art_nombre';
  
   $lista=cargar_registros_obj("
	SELECT *, ((
		SELECT SUM(-stock_cant) FROM stock 
		JOIN logs ON stock_log_id=log_id AND log_fecha>=(CURRENT_DATE-('3 months'::interval)) AND log_tipo IN (2,9,15,16,17)
		WHERE stock_art_id=id AND stock_bod_id=$bod_id AND stock_cant<0
	)/3) AS consumo 
	
	FROM (
	SELECT *, articulo.art_id AS id, (critico_gasto - stock_actual) AS pedido_cantidad FROM (
	SELECT *,
	calcular_stock(critico_art_id,$bod_id) AS stock_actual 
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
	ORDER BY $order) AS foo2;
	");
	
	$prov_id=0;
	$ids=Array();
	$p=0;
	
	for($i=0;$i<sizeof($lista);$i++) {
		
		if($prov_id!=$lista[$i]['prov_id']){
			
			if($lista[$i]['prov_id'])
				$coment='LISTADO DE COMPRAS PARA PROVEEDOR '.$lista[$i]['prov_rut'].' '.$lista[$i]['prov_glosa'];
			else
				$coment='LISTADO DE REPOSICIÓN GENERADO MANUALMENTE';
			pg_query("
			    INSERT INTO pedido VALUES (
			    DEFAULT,
			    nextval('global_numero_pedido'),
			    current_timestamp,
			    ".($_SESSION['sgh_usuario_id']*1).",
			    ".($_SESSION['sgh_usuario_id']*1).",
			    $bod_id,
			    (SELECT bod_id_reposicion FROM bodega WHERE bod_id=$bod_id),
			    '$coment',
			    0, true
			    )
			  ");
			  
			  $pedido_id=cargar_registro("SELECT CURRVAL('pedido_pedido_id_seq')AS id;");
			  $pedido_nro=cargar_registro("SELECT CURRVAL('global_numero_pedido')AS nro;");
			  $prov_id=$lista[$i]['prov_id'];
			  $ids[$p]['pedido_id']=$pedido_id['id'];
			  $ids[$p]['pedido_nro']=$pedido_nro['nro'];
			  $ids[$p]['prov_glosa']=$lista[$i]['prov_glosa'];
			  $ids[$p]['convenio_nombre']=$lista[$i]['convenio_nombre'];
			  $p++;
		}
		  
		
		$r=$lista[$i];
		
		$r['pedidod_cantidad']=$_POST['art_'.$r['id']]*1;
		
		if($r['pedidod_cantidad']>0)
			pg_query("INSERT INTO pedido_detalle (pedido_id, art_id, pedidod_cant, pedidod_estado) 
			VALUES (
			CURRVAL('pedido_pedido_id_seq'),
			".$r['id'].",
			".$r['pedidod_cantidad'].",
			false
			);");
		
	}

  //$pedido_q = pg_query("SELECT CURRVAL('global_numero_pedido')");
  
  //list($pedido_nro)=pg_fetch_row($pedido_q);
    
  print(json_encode($ids));

?>
