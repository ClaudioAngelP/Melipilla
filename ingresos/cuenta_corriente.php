<?php 

	require_once('../conectar_db.php');

	$hosp_id=$_POST['hosp_id']*1;

	$datos=cargar_registro("SELECT *, date_part('year',age(now()::date, pac_fc_nac)) as edad_anios FROM cuenta_corriente_encabezado JOIN pacientes USING (pac_id) WHERE hosp_id=$hosp_id", true);

	if(!$datos) {
		$datos=cargar_registro("SELECT *, date_part('year',age(now()::date, pac_fc_nac)) as edad_anios FROM hospitalizacion JOIN pacientes ON hosp_pac_id=pac_id WHERE hosp_id=$hosp_id", true);
	}

	$p=cargar_registros_obj("SELECT * FROM cuenta_corriente WHERE hosp_id=$hosp_id", true);

	$presta=Array();

	if($p) 
	for($i=0;$i<sizeof($p);$i++) {

		//$presta[$i]=Object();
		$presta[$i]->codigo=$p[$i]['codigo'];
		$presta[$i]->glosa=$p[$i]['glosa'];
		$presta[$i]->fecha=$p[$i]['fecha'];
		$presta[$i]->presta_id=$p[$i]['presta_id']*1;
		$presta[$i]->cantidad=$p[$i]['cantidad'];
		$presta[$i]->precio=$p[$i]['precio']*1;
		$presta[$i]->copago=$p[$i]['copago']*1;
		$presta[$i]->pab=$p[$i]['pab'];
		$presta[$i]->tipo=$p[$i]['tipo'];
		$presta[$i]->modalidad=$p[$i]['modalidad'];
		$presta[$i]->ptipo=$p[$i]['ptipo'];
		$presta[$i]->cobro=$p[$i]['cobro'];


	}

	exit(json_encode(array($datos,$presta)));

?>
