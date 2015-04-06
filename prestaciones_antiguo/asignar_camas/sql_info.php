<?php 

	require_once('../../conectar_db.php');
	
	$hosp_id=$_POST['hosp_id']*1;
	$esp_id=$_POST['esp_id']*1;
	$esp_id2=$_POST['esp_id2']*1;
	$serv_id=$_POST['centro_ruta0']*1;
	$doc_id=$_POST['doc_id']*1;
	$inst_id=$_POST['inst_id']*1;
	
	$hosp_fecha_ing=pg_escape_string($_POST['hosp_fecha_ing']);
	$hosp_hora_ing=pg_escape_string($_POST['hosp_hora_ing']);

	$cie10=pg_escape_string(utf8_decode($_POST['diag_cod']));
	$diag=pg_escape_string(utf8_decode($_POST['diagnostico']));
	$cie102=pg_escape_string(utf8_decode($_POST['diag_cod2']));
	$diag2=pg_escape_string(utf8_decode($_POST['diagnostico2']));
	$otro_destino=pg_escape_string(utf8_decode($_POST['hosp_otro_destino']));
	
	$hest_id=$_POST['hest_id']*1;
	$hcon_id=$_POST['hcon_id']*1;
	$observacion=pg_escape_string(utf8_decode($_POST['observacion']));
	$necesidad=pg_escape_string(utf8_decode($_POST['necesidad']));
	
	$hh=cargar_registro("SELECT * FROM hospitalizacion WHERE hosp_id=$hosp_id");
	
	$hosp_cama_actual=$hh['hosp_numero_cama']*1;
	
	$condicion_egr=$_POST['hosp_destino']*1;
	
	if($condicion_egr!=2) $inst_id=0;
	
	if($condicion_egr!=5) $otro_destino='';
	
	if($condicion_egr==0) $condicion_egr='null';
	
	pg_query("
		UPDATE hospitalizacion SET
			hosp_fecha_ing='$hosp_fecha_ing $hosp_hora_ing',
			hosp_doc_id=$doc_id,
			hosp_esp_id=$esp_id,
			hosp_esp_id2=$esp_id2,
			hosp_diag_cod='$cie10',
			hosp_diagnostico='$diag',
			hosp_diag_cod2='$cie102',
			hosp_diagnostico2='$diag2',
			hosp_servicio=$serv_id,
			hosp_condicion_egr=$condicion_egr,
			hosp_inst_id=$inst_id,
			hosp_otro_destino='$otro_destino'
		WHERE hosp_id=$hosp_id	
	");

	$tcama_id=$_POST['tcama_id']*1;
		
	if(isset($_POST['cama_id']) OR $tcama_id==-2) {
	
		if($tcama_id!=-2) {
			$ncama=$_POST['cama_id']*1;
		} else {
			$ncama=-1;
			pg_query("UPDATE hospitalizacion SET hosp_cama_egreso=hosp_numero_cama, hosp_fecha_egr=CURRENT_TIMESTAMP WHERE hosp_id=$hosp_id");			
		}

		pg_query("UPDATE hospitalizacion SET hosp_numero_cama=$ncama WHERE hosp_id=$hosp_id");
		
		if($hosp_cama_actual!=0 AND $tcama_id!=-1 AND $tcama_id!=-2) {
			
			pg_query("INSERT INTO paciente_traslado 
				VALUES (DEFAULT, current_timestamp, 
				'', 
				$hosp_id, 
				$hosp_cama_actual, 
				$ncama
				);");
				
			$hosp_id2=$_POST['hosp_id2']*1;		
			
			if($hosp_id2!=0) {
		
				pg_query("UPDATE hospitalizacion SET hosp_numero_cama=$hosp_cama_actual WHERE hosp_id=$hosp_id2");

				pg_query("INSERT INTO paciente_traslado 
				VALUES (DEFAULT, current_timestamp, 
				'', 
				$hosp_id2, 
				$ncama, 
				$hosp_cama_actual
				);");
				
			}
			
		} elseif($hosp_cama_actual==0 AND $tcama_id>=0) {
			
			pg_query("UPDATE hospitalizacion SET hosp_fecha_hospitalizacion=CURRENT_TIMESTAMP WHERE hosp_id=$hosp_id");
			
		}
		
	}
	
	pg_query("INSERT INTO hospitalizacion_registro
		VALUES (DEFAULT,$hosp_id ,current_timestamp,$hest_id ,$hcon_id );
	");
	if(trim($observacion)!='')
	pg_query("INSERT INTO hospitalizacion_observaciones
		VALUES (DEFAULT,$hosp_id ,current_timestamp,'$observacion',".$_SESSION['sgh_usuario_id']." );
	");
	if(trim($necesidad)!='')
	pg_query("INSERT INTO hospitalizacion_necesidades
		VALUES (DEFAULT,$hosp_id ,current_timestamp,'$necesidad',".$_SESSION['sgh_usuario_id']." );
	");

?>
