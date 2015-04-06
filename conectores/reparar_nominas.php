<?php 

	require_once('../config.php');
	require_once('sigh.php');
	
	$reg=cargar_registros_obj("
	
		SELECT * FROM (
			SELECT nom_fecha, nom_esp_id, pac_id, nomd_diag_cod, count(*) AS repite
			FROM nomina 
			JOIN nomina_detalle USING (nom_id) 
			WHERE nom_folio NOT ILIKE 'SN-%' AND nom_fecha::date>'01/06/2011'
			GROUP BY nom_fecha, nom_esp_id, pac_id, nomd_diag_cod
			ORDER BY nom_fecha, nom_esp_id, pac_id
		) AS fuu WHERE repite>1;
	
	");
	
	$count_1=0; $count_2=0; $count_3=0; $count_4=0;

	print('NUMERO DE REGISTROS '.sizeof($reg).'<br><br>');


	if($reg)
	for($i=0;$i<sizeof($reg);$i++) {
	
		$r=cargar_registros_obj("SELECT *, (SELECT COUNT(*) FROM nomina_detalle_prestaciones WHERE nomina_detalle_prestaciones.nomd_id=nomina_detalle.nomd_id) AS cnt FROM nomina_detalle JOIN nomina USING (nom_id)
		WHERE nom_fecha='".$reg[$i]['nom_fecha']."' AND nom_esp_id=".$reg[$i]['nom_esp_id']." 
		AND pac_id=".$reg[$i]['pac_id']);
		
		$fnd=false;
		
		for($k=0;$k<sizeof($r);$k++) {
			
			if($r[$k]['nomd_diag_cod']!='' OR $r[$k]['nomd_diag']!='' OR $r[$k]['cnt']*1>0) {
				$r[$k]['borrar']=false;
				$fnd=true;
			} else {
				$r[$k]['borrar']=true;	
			}
			
		}
		
		if(!$fnd) {
			$r[0]['borrar']=false;
		}
				
		for($k=0;$k<sizeof($r);$k++) {
			if($r[$k]['borrar']) {
				print_r($r[$k]);
				print('<br><br>');
				//pg_query("DELETE FROM nomina_detalle WHERE nomd_id=".$r[$k]['nomd_id']);
				$count_1++;
				if($r[$k]['nomd_diag_cod']!='') {
					$count_3++;
				}
			} else {
				print('NO BORRAR...<br><br>');				
				print_r($r[$k]);
				print('<br><br>');
				$count_2++;
				if($r[$k]['nomd_diag_cod']!='') {
					$count_4++;
				}
			}
		} 
		
	}
	
	print("BORRA $count_1 ($count_3) NO BORRA $count_2 ($count_4)");

?>
