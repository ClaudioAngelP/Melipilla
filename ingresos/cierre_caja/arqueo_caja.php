<?php 

	require_once('../../conectar_db.php');

	$fecha1=$_GET['fecha1'];
	$fecha2=$_GET['fecha2'];

	$ac=cargar_registros_obj("SELECT * FROM apertura_cajas LEFT JOIN arqueo_cajas_detalle USING (ac_id) WHERE ac_fecha_apertura::date BETWEEN '$fecha1' AND '$fecha2' AND ac_fecha_cierre IS NOT NULL AND aqc_id IS NULL;");

	$ids='';

	for($i=0;$i<sizeof($ac);$i++) {

		if(isset($_GET['ac_'.$ac[$i]['ac_id']])) {
			$ids.=$ac[$i]['ac_id'].',';
		}

	}

	$ids=trim(trim($ids,','));

	if($ids=='') {

		exit('<script>alert("Debe seleccionar cierres de caja para el arqueo.");</script>');

	}

	$func_id=$_SESSION['sgh_usuario_id']*1;

	pg_query("INSERT INTO arqueo_cajas VALUES (DEFAULT, CURRENT_TIMESTAMP, $func_id, '');");

	$iids=explode(',',$ids);

	for($i=0;$i<sizeof($iids);$i++) {
		pg_query("INSERT INTO arqueo_cajas_detalle VALUES (CURRVAL('arqueo_cajas_aqc_id_seq'), ".$iids[$i].");");

	}

	$tmp=cargar_registro("SELECT CURRVAL('arqueo_cajas_aqc_id_seq') AS id;");

	$_GET['aqc_id']=$tmp['id']*1;

        require_once('imprimir_arqueo.php');


?>
