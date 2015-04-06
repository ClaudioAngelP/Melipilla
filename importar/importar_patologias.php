<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	$archivo=str_replace("AVE\nAVE", "AVE", file_get_contents('patologias_paciente.csv'));
	
	$f=explode("\n", $archivo);
	
	$buenos=0; $malos=0;
	
	for( $i=1; $i < sizeof($f); $i++ ) {
		
		$r=explode('|',$f[$i]);
		
		$rut=explode('-',trim(strtoupper($r[6])));
		$rrut=($rut[0]*1).'-'.$rut[1];
		
		$ficha=trim($r[5])*1;
		
		$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_rut='$rrut';");
		
		if(!$pac) {
			
			$pac=cargar_registro("SELECT * FROM pacientes WHERE pac_ficha='$ficha';");
			
			if(!$pac) {
				
				$malos++;
				
				continue;
				
			}
			
			
		}
		
		$buenos++;
		
		$pac_id=$pac['pac_id']*1;
		
		$patologia=pg_escape_string(trim(strtoupper($r[2])));
		
		$fecha1='null';
		$fecha2='null';
		 
		pg_query("INSERT INTO pacientes_patologia VALUES (DEFAULT, $pac_id, '$patologia', $fecha1, $fecha2);");
		
		
		if($i%50==0)
			print("$i ... "); 
		
		flush();
		
	}
	
	print("b:$buenos m:$malos");

?>
