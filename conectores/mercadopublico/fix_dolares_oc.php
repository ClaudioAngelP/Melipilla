<?php 

    chdir(dirname(__FILE__));

	require_once('../../config.php');
	require_once('../sigh.php');
	
	$ocs=pg_query("
		SELECT * FROM orden_compra WHERE NOT orden_estado IN ('OC Aceptada','OC Enviada a Vendedor') AND orden_moneda='USD' AND orden_valor_moneda IS NULL AND orden_fecha::date<CURRENT_DATE;
	");
	
	pg_query("START TRANSACTION");
	
	while($oc=pg_fetch_assoc($ocs)) {
		
		$orden_id=$oc['orden_id']*1;
		$fecha=substr($oc['orden_fecha'],0,10);
		
		$tmp=cargar_registro("SELECT dolar_valor FROM dolar_observado WHERE dolar_fecha<='$fecha' ORDER BY dolar_fecha DESC;");
		$valor=$tmp['dolar_valor']*1;
		
		pg_query("UPDATE orden_compra SET orden_valor_moneda=$valor WHERE orden_id=$orden_id;");
		pg_query("UPDATE orden_detalle SET ordetalle_subtotal=ordetalle_subtotal*$valor WHERE ordetalle_orden_id=$orden_id;");
		pg_query("UPDATE orden_servicios SET orserv_subtotal=orserv_subtotal*$valor WHERE orserv_orden_id=$orden_id;");
		
	}
	
	pg_query("COMMIT;");

?>
