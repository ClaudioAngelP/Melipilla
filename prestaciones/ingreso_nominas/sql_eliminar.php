<?php 

	require_once('../../conectar_db.php');
	
	$nom_id=$_POST['nom_id']*1;
	
	$nomd=cargar_registros_obj("SELECT * FROM nomina_detalle WHERE nom_id=$nom_id");

	pg_query("START TRANSACTION;");
		
	if($nomd) {
	
		for($i=0;$i<sizeof($nomd);$i++) {
		
			pg_query("DELETE FROM nomina_detalle_prestaciones WHERE nomd_id=".$nomd[$i]['nomd_id']);					
			pg_query("DELETE FROM nomina_detalle_campos WHERE nomd_id=".$nomd[$i]['nomd_id']);					
			pg_query("DELETE FROM nomina_detalle WHERE nomd_id=".$nomd[$i]['nomd_id']);					
			
		}				
		
	}
	
	pg_query("DELETE FROM nomina WHERE nom_id=$nom_id;");
	pg_query("DELETE FROM cupos_atencion WHERE nom_id=$nom_id;");					
	
	pg_query("COMMIT;");		

?>
