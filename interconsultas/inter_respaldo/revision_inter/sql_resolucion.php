<?php
   require_once('../../conectar_db.php');
  
   if(isset($_POST['inter_id'])) {
		$id=($_POST['inter_id']*1);
	} else {
		$id=($_POST['oa_id']*1);	
	}
	
	$prioridad=($_POST['prioridad']*1);
	$observa=pg_escape_string(utf8_decode($_POST['observaciones']));
	$inter_diag_cod=pg_escape_string(utf8_decode($_POST['inter_diag_cod']));
	
	if(isset($_POST['inter_id'])) {
	pg_query($conn,"
    			inter_prioridad=$prioridad,
   	 	   func_id2=".$_SESSION['sgh_usuario_id'].", 
	   		inter_diag_cod='$inter_diag_cod',
	   		inter_diagnostico='$inter_diagnostico',
	   		inter_inst_id2=$sgh_inst_id 
	");
	$inter=cargar_registro("SELECT inter_pac_id FROM interconsulta WHERE inter_id=$id");
	$pac_id=$inter['inter_pac_id']*1;
	} else { 
	pg_query($conn,"
    			oa_prioridad=$prioridad,
   	 	   func_id2=".$_SESSION['sgh_usuario_id'].",
   	 	   oa_rev_med='$observa', 
	   		oa_diagnostico='$inter_diagnostico',
	   		oa_inst_id2=$sgh_inst_id 
	");
	$orden=cargar_registro("SELECT oa_pac_id FROM orden_atencion WHERE oa_id=$id");
	$pac_id=$orden['oa_pac_id']*1;
	}
	
	 if($estado==1) {
    $datos = pg_query($conn,"
    $data = pg_fetch_row($datos);
    $paciente = $data[1];
    if($estado==1) {
    	
	   $prioridad=$_POST['prioridad']*1;
      // Activa el Caso GES que agrupar� el tratamiento del paciente...
		if(isset($_POST['inter_id'])) {

		pg_query("
			UPDATE interconsulta SET
				inter_prioridad=$prioridad,
				inter_unidad=$especialidad
			WHERE inter_id=$id		
		");
		
		} else {

		pg_query("
			UPDATE orden_atencion SET
				oa_prioridad=$prioridad,
				oa_especialidad2=$especialidad
			WHERE oa_id=$id		
		");
			
		}

      /*pg_query($conn, "
			WHERE ca_estado=-1 AND id_sigges=".$ic['id_caso']."
      ");*/
    }

    
    pg_query("DELETE FROM pacientes_queue 
    		WHERE func_id=".$_SESSION['sgh_usuario_id']." AND 
    		pac_id=$pac_id;");
	print("OK");
?>