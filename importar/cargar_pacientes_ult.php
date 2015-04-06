<?php 

	require_once('../conectar_db.php');
	
	$bod_id=13;
	
	$f=explode("\n", utf8_decode(file_get_contents('pac_ult.csv')));
	
	pg_query("START TRANSACTION;");
	
	for($i=1;$i<sizeof($f);$i++) {
	
		if(trim($f[$i])=="") continue;
	
		$r=explode('|',$f[$i]);
		
		$rut=trim($r[1]);
		$nombre=trim($r[5]);
		$paterno=trim($r[3]);
		$materno=trim($r[4]);
		if(trim($r[6])!=''){
		$fecha_nacimiento="'".trim($r[6])."'";
		} else {
			$fecha_nacimiento="null";
			}
		
		//Viene con valores, 1, 2, M, F
		switch (trim($r[7])) {
			case '1':
				$genero = 0;
			break;
			case '2':
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
		$sector_nombre= '';
		$getn_id=-1;
		$sang_id=-1;
		$direccion=trim($r[12]);
		$ciudad=trim($r[17]);
		$comuna=cargar_registro("SELECT * FROM comunas WHERE ciud_desc ILIKE '%$ciudad%'");
		$ciud_id=($comuna['ciud_id']*1);
		$nacion=0;
		//estciv_id (No viene en excel).
		$estado_civil=0;
		//pac_fono, pac_celular (ojo con los espacios y guiones)
		$fono=trim($r[19]);
		//pac_padre, pac_madre - No vienen en Excel
		$pac_padre = '';
		$pac_madre = '';
		//pac_tramo - No viene en Excel
		$tramo='';
		//pac_pasaporte - No viene en Excel
		$pasaporte='';
		$ficha= trim($r[0]);
		//id_sigges - No viene (?)
		$sigges=0;
		//pac_mail- No viene en Excel
		$mail='';
		//pac_celular - Sólo viene un dato de fono, mezclado
		$celular='';
		//pac_prais - Vienen con valores 1 Si y 2 No / En la base de datos es Boolean
		if(trim($r[10])==''){
			$prais = "false";
		} elseif (trim($r[10])=='SI') {
			$prais = "true";
		}
		//id_sidra - No viene en Excel
		$id_sidra = '';
		//pac_fc_def - No viene en Excel
		$pac_fc_def = "null";
		$clave = "null";
		
		/*pac_id, pac_rut, pac_nombres, pac_appat, pac_apmat, pac_fc_nac, sex_id, prev_id, sector_nombre, getn_id, sang_id, pac_direccion,
		ciud_id, nacion_id, estciv_id, pac_fono, pac_padre, pac_madre, pac_tramo, pac_pasaporte, pac_ficha, id_sigges, pac_mail,
		pac_celular, pac_prais, id_sidra, pac_fc_def, pac_clave*/
		
		$query="INSERT INTO pacientes VALUES (default, '$rut', '$nombre', '$paterno', '$materno', $fecha_nacimiento 	,
				$genero, $prevision, '$sector_nombre', $getn_id, $sang_id, '$direccion', $ciud_id, $nacion, $estado_civil,
				'$fono','$pac_padre', '$pac_madre', '$tramo', '$pasaporte', '$ficha', $sigges, '$mail', '$celular', $prais,
				'$id_sidra', $pac_fc_def, $clave);";
				
		pg_query($query);
		echo $query;
		flush();
	
	}
	pg_query("COMMIT;");

?>
