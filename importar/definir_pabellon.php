<?php 

	require_once('../conectar_db.php');
	
	$faps=cargar_registros_obj("
		SELECT * FROM fap_prestacion 
		JOIN fap_pabellon USING (fap_id)
	");
	
	for($x=0;$x<sizeof($faps);$x++) {
	
	$pac_id=$faps[$x]['pac_id'];
	$codigo=pg_escape_string($faps[$x]['fappr_codigo']);
	$fappr_id=$faps[$x]['fappr_id']*1;
	$fecha=$faps[$x]['fap_fecha'];
	$fnd=false;
	
	$pp=cargar_registros_obj("
		SELECT * FROM fap_pabellon_prestaciones 
		WHERE ppresta_codigo='$codigo' ORDER BY ppresta_id;
	");
	
	print("
		SELECT * FROM fap_pabellon_prestaciones 
		WHERE ppresta_codigo='$codigo' ORDER BY ppresta_id;
	<br><br>");
	
	flush();
	
	if(!$pp) {
		pg_query("UPDATE fap_prestacion SET fappr_tipo='PPI' WHERE fappr_id=$fappr_id");
		print("NO ENCUENTRA!<br>");
		flush();
		continue;
	}
	
	$pac=cargar_registro("
		SELECT *,
		date_part('year',age('$fecha'::date, pac_fc_nac)) as edad_anios
		FROM pacientes WHERE pac_id=$pac_id;
	");
	
	for($i=0;$i<sizeof($pp);$i++) {
		
		if($pp[$i]['ppresta_edad']!='') {
			
			$signo=preg_replace('/[0-9]/','',$pp[$i]['ppresta_edad']);
			$valor=preg_replace('/[<>=]/','',$pp[$i]['ppresta_edad']);
			
			if($signo=='>=') {
				if(!($pac['edad_anios']*1>=$valor)) continue;
			} elseif($signo=='>') {
				if(!($pac['edad_anios']*1>$valor)) continue;
			} elseif($signo=='<=') {
				if(!($pac['edad_anios']*1<=$valor)) continue;
			} elseif($signo=='<') {
				if(!($pac['edad_anios']*1<$valor)) continue;
			}
			
		} 

		if($pp[$i]['ppresta_sexo']!='') {

			if($pp[$i]['ppresta_sexo']=='M' AND $pac['sex_id']*1!=0) continue;
			if($pp[$i]['ppresta_sexo']=='F' AND $pac['sex_id']*1!=1) continue;

		}
		
		pg_query("UPDATE fap_prestacion SET fappr_tipo='".$pp[$i]['ppresta_tipo']."' WHERE fappr_id=$fappr_id");
		$fnd=true; break;
		
	}
	
	if(!$fnd)
		pg_query("UPDATE fap_prestacion SET fappr_tipo='PPI' WHERE fappr_id=$fappr_id");
	
	}

?>
