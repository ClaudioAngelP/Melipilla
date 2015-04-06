<?php 

	require_once('../../conectar_db.php');
	
	$pac_id=$_POST['pac_id']*1;
	$codigo=pg_escape_string($_POST['codigo']);
	
	$pp=cargar_registros_obj("
		SELECT * FROM fap_pabellon_prestaciones 
		WHERE ppresta_codigo='$codigo' ORDER BY ppresta_id;
	");
	
	if(!$pp) {
		exit("PPI");
	}
	
	$pac=cargar_registro("
		SELECT *,
		date_part('year',age(now()::date, pac_fc_nac)) as edad_anios
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
		
		exit($pp[$i]['ppresta_tipo']);
		
	}
	
	exit("PPI");

?>
