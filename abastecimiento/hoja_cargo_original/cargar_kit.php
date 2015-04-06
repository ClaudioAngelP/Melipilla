<?php 

	require_once('../../conectar_db.php');

	$kit_id=$_POST['art_id']*1;
	$bod_id=$_POST['bodega_id']*1;

	$cant=$_POST['cantidad']*1;
	
	$d=cargar_registro("SELECT * FROM articulo_kits WHERE kit_id=$kit_id", true);
	
	$arts=explode('|',trim($d['kit_detalle']));
	
	$datos=Array();
	
	for($i=0;$i<sizeof($arts);$i++) {
		
		$art_codigo=$arts[$i];
		
		if(strstr($art_codigo,'x')) {
			list($art_codigo, $art_cant) = explode('x', $art_codigo);
		} else {
			$art_cant='1';
		}
		
		$art=cargar_registro("SELECT *, calcular_stock(art_id, $bod_id) AS stock FROM articulo
		LEFT JOIN bodega_forma ON art_forma=forma_id
		LEFT JOIN item_presupuestario ON art_item=item_codigo
		WHERE art_codigo='$art_codigo';", true);

		if(!$art) continue;
		
		$art_id=$art['art_id']*1;
		
		$num=sizeof($datos);
		
		$datos[$num]->id=$art_id;
		$datos[$num]->codigo=$art['art_codigo'];
		$datos[$num]->glosa=$art['art_glosa'];
		$datos[$num]->clasificacion=$art['forma_nombre'];
		$datos[$num]->cantidad=$art_cant*$cant;
		$datos[$num]->stock=$art['stock'];
		
	}
	
	print(json_encode($datos, true));

?>
