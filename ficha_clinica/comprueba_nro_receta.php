<?php
	require_once('../conectar_db.php');
	$numero=isset($_GET['nro_receta'])?($_GET['nro_receta']*1):0;
	$tipo_talonario=isset($_GET['tipo_talonario'])?($_GET['tipo_talonario']*1):0;
	if($numero==0)
		die(json_encode(Array(false, 'Tipo de Talonario  y/o N&uacute;mero de Receta ingresados son incorrectos.')));
		
	$consulta="SELECT receta_numero, 0 as tipo,'' AS causal FROM receta WHERE
	receta_numero=".$numero." AND receta_tipotalonario_id=".$tipo_talonario."
	UNION
	SELECT receta_numero, 1 as tipo,causal FROM receta_anulada WHERE receta_numero=".$numero." 
	AND receta_tipotalonario_id=".$tipo_talonario."";
	
	//print($consulta);

	$comprobacion1 = pg_query($conn, $consulta);

	if(pg_num_rows($comprobacion1)!=0) {
		$dato = pg_fetch_row($comprobacion1);
		if($dato[1]==0)
			die(json_encode(Array(false, 'N&uacute;mero de Receta ya est&aacute; ingresado en el sistema.')));
		else
			die(json_encode(Array(false, 'N&uacute;mero de Receta est&aacute; inv&aacute;lida por '.$dato[2].'.')));
	}else{
		$comprobacion2 = pg_query($conn,"select * from talonario where ".$numero.">=talonario_inicio AND ".$numero."<=talonario_final");
		if(pg_num_rows($comprobacion2)!=0)
			die(json_encode(Array(true, 'Receta V&aacute;lida.')));
		else
			die(json_encode(Array(false, 'N&uacute;mero de Receta NO registrado en el sistema.')));
	}
?>
