<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$f=explode("\n",utf8_decode(file_get_contents('cambiar_polis.csv')));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($f);$i++) {
		
		if(trim($f[$i])=='') continue;
		
		$r=explode('|',$f[$i]);
		
		$pn=trim($r[3]);
		$pa=trim($r[4]);
		
		if(strstr($pa, '-HDGF')) continue;
		
		$nid=cargar_registro("SELECT esp_id FROM especialidades WHERE esp_desc='$pn'");
		$nid=$nid['esp_id']*1;
		
		$aid=cargar_registro("SELECT esp_id FROM especialidades WHERE esp_desc='$pa'");
		$aid=$aid['esp_id']*1;
		
		if($nid==0 OR $aid==0) {
		
			$test=cargar_registro("SELECT * FROM especialidades WHERE esp_desc='$pa-HDGF'");
			
			if($aid!=0 AND $test) {
				
				$nid=$test['esp_id']*1;
				
			} else continue;
			
		}
		
		pg_query("UPDATE nomina SET nom_esp_id=$nid WHERE nom_esp_id=$aid;");
		
		print("CAMBIADO: '$pa' por '$pn' ...<br><br>"); flush();
		
	}
	
	$otros=cargar_registros_obj("select distinct e1.esp_desc AS esp_desc1, e2.esp_desc AS esp_desc2, e1.esp_id AS esp_id1, e2.esp_id AS esp_id2 from nomina 
		join especialidades AS e1 on nom_esp_id=esp_id 
		join especialidades AS e2 on e2.esp_desc=e1.esp_desc || '-HDGF'
		order by e1.esp_desc;");
	
	for($i=0;$i<sizeof($otros);$i++) {

		$pa=$otros[$i]['esp_desc1'];
		$pn=$otros[$i]['esp_desc2'];
		$aid=$otros[$i]['esp_id1'];
		$nid=$otros[$i]['esp_id2'];
		
		pg_query("UPDATE nomina SET nom_esp_id=$nid WHERE nom_esp_id=$aid;");
		
		print("CAMBIADO: '$pa' por '$pn' ...<br><br>"); flush();
		
	}
	
	pg_query("COMMIT;");

?>
