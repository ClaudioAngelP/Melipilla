<?php 

	require_once('../config.php');
	require_once('../conectores/sigh.php');
	
	//$bod_id=13;
	
	$f=explode("\n", (file_get_contents('pacientes_felix.csv')));
	
	
	for($i=850000;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=="") continue;
	
		$r=explode(';',$f[$i]);
		
		$tipo=trim($r[9]);
		
		if($i%5000==0) {
			print(number_format(($i*100/sizeof($f)),2,',','.')."% ...");
		}
		
		$rut=strtoupper(str_replace('.','',trim($r[0])));
		
		if(!strstr($rut, '-')) continue;
		
		if(strlen($rut)>11) continue;

		pg_query("START TRANSACTION;");
		
		$nombre=pg_escape_string(trim($r[3]));
		$paterno=pg_escape_string(trim($r[1]));
		$materno=pg_escape_string(trim($r[2]));
		
		if(trim($r[12])!=''){
			$fecha_nacimiento="'".trim(str_replace('-','/',$r[12]))."'";
		} else {
			$fecha_nacimiento="null";
		}
		
		//Viene con valores, 1, 2, M, F
		switch (trim($r[8])) {
			case 'Masculino':
				$genero = 0;
			break;
			case 'Femenino':
				$genero = 1;
			break;
			case 'M':
				$genero = 0;
			break;
			case 'F':
				$genero = 1;
			break;
		}
		
		$prevision=0;
		$getn_id=-1;
		$sang_id=-1;
		
		$direccion=pg_escape_string(trim($r[5]));
		$ciudad=pg_escape_string(trim($r[10]));
		
		$comuna=cargar_registro("SELECT * FROM comunas WHERE ciud_desc ILIKE '%$ciudad%'");
		
		if($comuna) {
			$ciud_id=($comuna['ciud_id']*1);
			$sector_nombre= '';
		} else {
			$cuid_id=0;
			$sector_nombre= $ciudad;			
		}
		
		$nacion=0;
		//estciv_id (No viene en excel).
		$estado_civil=0;
		//pac_fono, pac_celular (ojo con los espacios y guiones)
		$fono=pg_escape_string(trim($r[6]));
		//pac_padre, pac_madre - No vienen en Excel
		$pac_padre = '';
		$pac_madre = '';
		//pac_tramo - No viene en Excel
		$tramo=substr(trim($r[15]),0,1);
		//pac_pasaporte - No viene en Excel
		$pasaporte='';
		
		
		
		if($tipo=='10-120')
			$ficha=trim($r[4]);
		else
			$ficha='';
			
		//id_sigges - No viene (?)
		$sigges=0;
		//pac_mail- No viene en Excel
		$mail='';
		//pac_celular - Sólo viene un dato de fono, mezclado
		$celular=pg_escape_string(trim($r[30]));
		//pac_prais - Vienen con valores 1 Si y 2 No / En la base de datos es Boolean
		$prais='null';
		if(substr(trim($r[22]),0,1)=='S'){
			$prais = "true";
		} elseif (substr(trim($r[22]),0,1)=='N') {
			$prais = "false";
		}
		//id_sidra - No viene en Excel
		$id_sidra = '';
		//pac_fc_def - No viene en Excel
		$pac_fc_def = "null";
		$clave = "null";
		
		/*pac_id, pac_rut, pac_nombres, pac_appat, pac_apmat, pac_fc_nac, sex_id, prev_id, sector_nombre, getn_id, sang_id, pac_direccion,
		ciud_id, nacion_id, estciv_id, pac_fono, pac_padre, pac_madre, pac_tramo, pac_pasaporte, pac_ficha, id_sigges, pac_mail,
		pac_celular, pac_prais, id_sidra, pac_fc_def, pac_clave*/
		
		$chk=cargar_Registro("SELECT * FROM pacientes WHERE pac_rut='$rut';");
		
		if(!$chk) {
			$query="INSERT INTO pacientes VALUES (default, '$rut', '$nombre', '$paterno', '$materno', $fecha_nacimiento 	,
				$genero, $prevision, '$sector_nombre', $getn_id, $sang_id, '$direccion', $ciud_id, $nacion, $estado_civil,
				'$fono','$pac_padre', '$pac_madre', '$tramo', '$pasaporte', '$ficha', $sigges, '$mail', '$celular', $prais,
				'$id_sidra', $pac_fc_def, $clave);";
			$ok=pg_query($query);
			if(!$ok) {
				echo $query;
				exit();
			}
		} else {
			if($ficha!='') {
				$query="UPDATE pacientes SET pac_ficha='$ficha' WHERE pac_id=".$chk['pac_id'];
				$ok=pg_query($query);
				if(!$ok) {
					echo $query;
					exit();
				}
			}
		}

		pg_query("COMMIT;");
				
		flush();
	
	}

?>
