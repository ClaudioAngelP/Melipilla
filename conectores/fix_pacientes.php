<?php 

	chdir(dirname(__FILE__));

	require_once('../config.php');
	require_once('sigh.php');

	function join_pac($id, $id2) {
		
		pg_query("UPDATE nomina_detalle SET pac_id=$id WHERE pac_id=$id2");		
		pg_query("UPDATE prestacion SET pac_id=$id WHERE pac_id=$id2");		
		pg_query("UPDATE fap_pabellon SET pac_id=$id WHERE pac_id=$id2");		
		pg_query("UPDATE fap SET fap_pac_id=$id WHERE fap_pac_id=$id2");		
		pg_query("UPDATE interconsulta SET inter_pac_id=$id WHERE inter_pac_id=$id2");		
		pg_query("UPDATE orden_atencion SET oa_pac_id=$id WHERE oa_pac_id=$id2");
		pg_query("UPDATE formulario_ipd SET ipd_pac_id=$id WHERE ipd_pac_id=$id2");
		pg_query("UPDATE formulario_ccaso SET ccaso_pac_id=$id WHERE ccaso_pac_id=$id2");
		pg_query("UPDATE casos_auge SET ca_pac_id=$id WHERE ca_pac_id=$id2");		
		pg_query("UPDATE receta SET receta_paciente_id=$id WHERE receta_paciente_id=$id2");		
		pg_query("UPDATE pacientes_queue SET pac_id=$id WHERE pac_id=$id2");		
		
		pg_query("DELETE FROM pacientes WHERE pac_id=$id2");
		
	}
	
	$pac=cargar_registros_obj("
		select * from (select pac_rut, count(*) AS cnt from pacientes group by pac_rut) AS foo where cnt>1;	
	");

	for($i=0;$i<sizeof($pac);$i++) {
			
		$rut=$pac[$i]['pac_rut'];
		
		if($rut[0]*1>0 AND ($rut[1]=='K' OR $rut[1]*1<10)) {
						
			$pac_rut=$pac[$i]['pac_rut'];
		
			$pacs=cargar_registros_obj("SELECT * FROM pacientes WHERE pac_rut='$pac_rut' ORDER BY pac_id");		

			$sigges='-1';
			
			$time1=microtime(true);

			pg_query("START TRANSACTION;");

			for($j=0;$j<sizeof($pacs);$j++) {
				
				echo $pacs[$j]['pac_rut'].'|'.$pacs[$j]['pac_appat'].'|'.$pacs[$j]['pac_apmat'].'|'.$pacs[$j]['pac_nombres'].'|'.$pacs[$j]['pac_ficha'].'\n'

				if($j==0) {
					$pac_id1=$pacs[$j]['pac_id'];
					$paterno=$pacs[$j]['pac_appat'];
					$materno=$pacs[$j]['pac_apmat'];
					$nombres=$pacs[$j]['pac_nombres'];
					$fcnac=$pacs[$j]['pac_fc_nac'];	
				}
				
				if($pacs[$j]['id_sigges']*1>0) {
					$paterno=$pacs[$j]['pac_appat'];
					$materno=$pacs[$j]['pac_apmat'];
					$nombres=$pacs[$j]['pac_nombres'];	
					$fcnac=$pacs[$j]['pac_fc_nac'];	
					$sigges=$pacs[$j]['id_sigges']*1;	
				}

				if(trim($pacs[$j]['pac_ficha'])!='') $ficha=trim($pacs[$j]['pac_ficha']);	

				if($j>0) {
					join_pac($pac_id1, $pacs[$j]['pac_id']);	
				}

			}
			
			pg_query("UPDATE pacientes SET
				pac_appat='$paterno',
				pac_apmat='$materno',
				pac_nombres='$nombres',
				pac_fc_nac='$fcnac',
				pac_ficha='$ficha',
				id_sigges=$sigges
			WHERE pac_id=$pac_id1");
			
			pg_query("COMMIT;");
			
			$time2=microtime(true)-$time1;
			
			flush();
			
		}	
		
	}

?>
