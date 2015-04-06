<?php 

	require_once('../conectar_db.php');
	
	$lista_id=pg_escape_string($_POST['lista_id']);
	$dividir=$_POST['redividir']*1;
	
	if($dividir==0) {
		$dividir=1;
	}
	
	pg_query("START TRANSACTION;");
	
	pg_query("UPDATE lista_dinamica_bandejas SET lista_dividir=$dividir WHERE codigo_bandeja='$lista_id';");
	
	if($dividir>1) {
		$tmp=cargar_registros_obj("SELECT monr_id FROM monitoreo_ges_registro WHERE monr_subclase='$lista_id' AND monr_estado=0 ORDER BY monr_fecha;");
	
		print(sizeof($tmp));
		
		for($i=0;$i<sizeof($tmp);$i++) {
			$monr_id=$tmp[$i]['monr_id']*1;
			$div=($i%$dividir);
			pg_query("UPDATE monitoreo_ges_registro SET monr_dividir=$div WHERE monr_id=$monr_id;");
			print("UPDATE monitoreo_ges_registro SET monr_dividir=$div WHERE monr_id=$monr_id;");
		}
	} else {
		pg_query("UPDATE monitoreo_ges_registro SET monr_dividir=0 WHERE monr_subclase='$lista_id' AND monr_estado=0;");		
	}
	
	pg_query("COMMIT;");

?>
