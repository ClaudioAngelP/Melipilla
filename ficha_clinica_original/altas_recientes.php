<?php 

	require_once('../conectar_db.php');
	
	$pac_id=$_POST['paciente_id']*1;
	
	$q=pg_query("SELECT * FROM hospitalizacion WHERE (hosp_fecha_egr IS NULL OR hosp_fecha_egr>=(CURRENT_DATE-('2 days'::interval))) AND hosp_pac_id=$pac_id;");
	
	if(pg_num_rows($q)>0) {
			exit('true');
	} else {
			exit('false');
	}

?>
