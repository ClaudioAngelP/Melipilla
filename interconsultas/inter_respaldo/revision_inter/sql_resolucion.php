<?php
   require_once('../../conectar_db.php');
  
   if(isset($_POST['inter_id'])) {
		$id=($_POST['inter_id']*1);
	} else {
		$id=($_POST['oa_id']*1);	
	}
	
	$prioridad=($_POST['prioridad']*1);	$estado=$_POST['estado']*1;
	$observa=pg_escape_string(utf8_decode($_POST['observaciones']));
	$inter_diag_cod=pg_escape_string(utf8_decode($_POST['inter_diag_cod']));	$inter_diagnostico=pg_escape_string(utf8_decode($_POST['inter_diagnostico']));
	
	if(isset($_POST['inter_id'])) {
	pg_query($conn,"		UPDATE interconsulta     	SET inter_estado=$estado,
    			inter_prioridad=$prioridad,
   	 	   func_id2=".$_SESSION['sgh_usuario_id'].", 	   		inter_rev_med='$observa',
	   		inter_diag_cod='$inter_diag_cod',
	   		inter_diagnostico='$inter_diagnostico',
	   		inter_inst_id2=$sgh_inst_id 		WHERE inter_id=$id; 
	");
	$inter=cargar_registro("SELECT inter_pac_id FROM interconsulta WHERE inter_id=$id");
	$pac_id=$inter['inter_pac_id']*1;
	} else { 
	pg_query($conn,"		UPDATE orden_atencion     	SET oa_estado=$estado,
    			oa_prioridad=$prioridad,
   	 	   func_id2=".$_SESSION['sgh_usuario_id'].",
   	 	   oa_rev_med='$observa', 	   		oa_diag_cod='$inter_diag_cod',
	   		oa_diagnostico='$inter_diagnostico',
	   		oa_inst_id2=$sgh_inst_id 		WHERE oa_id=$id; 
	");
	$orden=cargar_registro("SELECT oa_pac_id FROM orden_atencion WHERE oa_id=$id");
	$pac_id=$orden['oa_pac_id']*1;
	}
	
	 if($estado==1) {
    $datos = pg_query($conn,"	    SELECT 	    inter_especialidad,	    inter_pac_id, inter_diag_cod, inter_inst_id1	    FROM 	    interconsulta	    WHERE 	    inter_id=$id;    ");
    $data = pg_fetch_row($datos);
    $paciente = $data[1];    $diag = pg_escape_string($data[2]);    $institucion = $data[3];
    if($estado==1) {
    	
	   $prioridad=$_POST['prioridad']*1;   	$especialidad = $_POST['esp_id']*1;
      // Activa el Caso GES que agrupará el tratamiento del paciente...
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

      /*pg_query($conn, "			UPDATE casos_auge SET ca_estado=0 
			WHERE ca_estado=-1 AND id_sigges=".$ic['id_caso']."
      ");*/
    }
    }
    
    pg_query("DELETE FROM pacientes_queue 
    		WHERE func_id=".$_SESSION['sgh_usuario_id']." AND 
    		pac_id=$pac_id;");
	print("OK");
?>