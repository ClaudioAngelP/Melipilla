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
		
	if(isset($_POST['cama_id']) OR $hest_id==-2) {

		if($hest_id!=-2) {
			$ncama=$_POST['cama_id']*1;
		} else {
			$ncama=-1;	
		}

		if($hest_id==-2){
			if($_POST['hosp_fecha_egr']!="" AND $_POST['hosp_hora_egr']!="") {
			$fecha_egreso = "'".$_POST['hosp_fecha_egr']." ".$_POST['hosp_hora_egr']."'";
			} else {
				$fecha_egreso = 'CURRENT_TIMESTAMP';
			}
				
			pg_query("UPDATE hospitalizacion SET hosp_cama_egreso=hosp_numero_cama, hosp_fecha_egr=$fecha_egreso, hosp_func_id2=".$_SESSION['sgh_usuario_id']." WHERE hosp_id=$hosp_id");		
		}
		
		pg_query("UPDATE hospitalizacion SET hosp_numero_cama=$ncama WHERE hosp_id=$hosp_id");
		
		if($hosp_cama_actual!=0 AND $tcama_id!=-1 AND $hest_id!=-2) {
			
			pg_query("INSERT INTO paciente_traslado 
				VALUES (DEFAULT, current_timestamp, 
				'', 
				$hosp_id, 
				$hosp_cama_actual, 
				$ncama,
				".$_SESSION['sgh_usuario_id']."
				);");
				
			$hosp_id2=$_POST['hosp_id2']*1;		
			
			if($hosp_id2!=0) {
		
				pg_query("UPDATE hospitalizacion SET hosp_numero_cama=$hosp_cama_actual WHERE hosp_id=$hosp_id2");

				pg_query("INSERT INTO paciente_traslado 
				VALUES (DEFAULT, current_timestamp, 
				'', 
				$hosp_id2, 
				$ncama, 
				$hosp_cama_actual,
				".$_SESSION['sgh_usuario_id']."
				);");
				
			}
			
		} elseif($hosp_cama_actual==0 AND $tcama_id>=0) {
			
			pg_query("UPDATE hospitalizacion SET hosp_fecha_hospitalizacion=CURRENT_TIMESTAMP WHERE hosp_id=$hosp_id");
			
		}
		
/** INICIO ENVIO Y ACTUALIZACION DE DATOS A INTE_HOSPITALIZADOS**/
	$up_hosp = cargar_registro("SELECT hosp_numero_cama, cama_tipo || ' ' ||(hosp_numero_cama-cama_num_ini)+1 as sala_cama, COALESCE(cod_unidad_hosp,'28000000000') as un_hospitalaria
									from hospitalizacion
									JOIN pacientes ON hosp_pac_id=pac_id AND hosp_id=$hosp_id
									LEFT JOIN tipo_camas ON cama_num_ini<=hosp_numero_cama AND cama_num_fin>=hosp_numero_cama
									LEFT JOIN clasifica_camas ON tcama_num_ini<=hosp_numero_cama AND tcama_num_fin>=hosp_numero_cama");
	
	if($hest_id!=-2)	
		pg_query("UPDATE inte_hospitalizados SET intehosp_sala_cama='".$up_hosp['sala_cama']."', hosp_cod_un_hospitalaria=".$up_hosp['un_hospitalaria'].", hosp_estado_leido=0 WHERE intehosp_cta_corriente=$hosp_id");

	if($hest_id==-2){
		/** SE CAMBIA EL ESTADO EN inte_hospitalizados (tabla paso) A PACIENTES DE ALTA ****/
		$chk = cargar_registro("SELECT hosp_fecha_egr::date as fecha_egr, hosp_anulado FROM hospitalizacion
									LEFT JOIN clasifica_camas ON 
									tcama_num_ini<=hosp_servicio AND tcama_num_fin>=hosp_servicio
									WHERE hosp_fecha_egr IS NOT NULL AND hosp_condicion_egr<6
									AND hosp_id=$hosp_id");
			
			if($chk){
				$fecha_egr=$chk['fecha_egr'];
				
				pg_query("UPDATE inte_hospitalizados
						  SET hosp_fecha_egreso='$fecha_egr',
						  hosp_estado_hosp=2,
						  hosp_estado_leido=0
						  WHERE intehosp_cta_corriente=$hosp_id");
							    
			    pg_query("UPDATE hospitalizacion SET hosp_estado_envio=1 WHERE hosp_id=$hosp_id");
			}elseif($chk['hosp_anulado']=='1'){
				
				pg_query("UPDATE inte_hospitalizados
						  SET hosp_estado_hosp=3,
						  hosp_estado_leido=0
						  WHERE intehosp_cta_corriente=$hosp_id");
				pg_query("UPDATE hospitalizacion SET hosp_estado_envio=1 WHERE hosp_id=$hosp_id");
			}
		
	}/**FIN ENVIO Y ACTUALIZACION DE DATOS A INTE_HOSPITALIZADOS**/		
	}
	
	if($hest_id==-2 && $condicion_egr==1) $hest_id=3;
	
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
	
	// INTEGRACION GRIFOLS (PYXIS ADT)....

    $_GET['hosp_id']=$hosp_id*1; $script=true;
    ob_start();
    require_once('../../conectores/grifols/send_ADT.php');
    ob_end_clean();

?>
