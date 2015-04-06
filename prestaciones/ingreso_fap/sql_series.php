<?php

	require_once('../../conectar_db.php');

	$fap_id=$_POST['fap_id']*1;
	$num_series=$_POST['num_series']*1;
	
	for($i=0;$i<$num_series;$i++) {
	
		$nombre=pg_escape_string(utf8_decode($_POST['nombre_serie_'.$i]));
		$color=pg_escape_string(utf8_decode($_POST['color_serie_'.$i]));
		$datos=pg_escape_string(utf8_decode($_POST['editor_serie_'.$i]));
		$fs_id=$_POST['serie_'.$i]*1;
		
		$val_min=$_POST['val_min_'.$i]*1;
		$val_max=$_POST['val_max_'.$i]*1;

		if($fs_id!=0)
			pg_query("UPDATE fap_series SET fs_nombre='$nombre', fs_val_min=$val_min, fs_val_max=$val_max, fs_datos='$datos' WHERE fs_id=$fs_id");
		else
			pg_query("INSERT INTO fap_series VALUES (DEFAULT, $fap_id, '$nombre', '$color', 100, $val_min, $val_max, '$datos');");

	}


?>