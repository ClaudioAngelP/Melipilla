<?php require_once('../../conectar_db.php');
	
	$cta=$_POST['cta_cte'];
	pg_query("update hospitalizacion set hosp_fecha_egr=null,hosp_condicion_egr=null, hosp_func_id2=NULL, hosp_numero_cama=0, hosp_cama_egreso=NULL where hosp_id=$cta");
	pg_query("update inte_hospitalizados set hosp_estado_hosp=1, hosp_fecha_egreso=NULL, hosp_estado_leido=0 where intehosp_cta_corriente=$cta");

	$cond = cargar_registro("SELECT hcon_id FROM hospitalizacion_registro WHERE hosp_id=$cta;");
	$cond_id=$cond['hcon_id']*1;
	
	pg_query("INSERT INTO hospitalizacion_registro VALUES(DEFAULT,$cta,CURRENT_TIMESTAMP,1,$cond_id);");
	
	//print("Alta de paciente revertida exitosamente!");

?>
