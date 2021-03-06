<?php

  require_once('../../conectar_db.php');
  
		$id=($_GET['paciente_id']*1);
		
    $tipo_id=($_GET['paciente_tipo_id']*1);
		
    if($tipo_id==0) {
		  $rut=pg_escape_string($_GET['paciente_rut']);
		  $pasaporte='';
		} else if($tipo_id==1) {
      $pasaporte=pg_escape_string($_GET['paciente_rut']);
      $rut='';
		} else {
      $pasaporte='';
      $rut='';
    }
		
		$nombre=pg_escape_string(iconv("UTF-8", "ISO-8859-1", 
        $_GET['paciente_nombre']));
		$paterno=pg_escape_string(iconv("UTF-8", "ISO-8859-1", 
        $_GET['paciente_paterno']));
		$materno=pg_escape_string(iconv("UTF-8", "ISO-8859-1", 
        $_GET['paciente_materno']));
		$fechanac=pg_escape_string($_GET['paciente_fecha']);
		$sexo=pg_escape_string($_GET['paciente_sexo']);
		$prev=pg_escape_string($_GET['paciente_prevision']);
		$tramo=pg_escape_string($_GET['paciente_tramo']);
		$grupo=pg_escape_string($_GET['paciente_grupo']);
		$comuna=pg_escape_string($_GET['paciente_comuna']);
		$direccion=pg_escape_string(iconv("UTF-8", "ISO-8859-1",
        $_GET['paciente_dire']));
		$sangre=pg_escape_string($_GET['paciente_sangre']);
		$sector=pg_escape_string(iconv("UTF-8", "ISO-8859-1", 
        $_GET['paciente_sector']));
		$fono=pg_escape_string(iconv("UTF-8", "ISO-8859-1", 
        $_GET['paciente_fono']));
		$nacion=pg_escape_string($_GET['paciente_nacion']);
		$estciv=pg_escape_string($_GET['paciente_estciv']);
		$padre=pg_escape_string(iconv("UTF-8", "ISO-8859-1", 
        $_GET['paciente_padre']));
		$madre=pg_escape_string(iconv("UTF-8", "ISO-8859-1", 
        $_GET['paciente_madre']));
		

    if($fechanac!='') {
      $fechanac="'".$fechanac."'";
    } else {
      $fechanac='null';
    }

		// Ingreso de Paciente...
		
		if($id==0) {
		
		if($tipo_id==0) {
      $existe = pg_query($conn, "
      SELECT * FROM pacientes 
      WHERE pac_rut='".$rut."'
      ");
      
      if(pg_num_rows($existe)>0) {
      die(json_encode(Array(false, 'Paciente ya existe en base de datos y no puede ser ingresado como nuevo.')));
      }
    } else if($tipo_id==1) {
      $existe = pg_query($conn, "
      SELECT * FROM pacientes 
      WHERE pac_pasaporte='".$pasaporte."'
      ");
      
      if(pg_num_rows($existe)>0) {
      die(json_encode(Array(false, 'Paciente ya existe en base de datos y no puede ser ingresado como nuevo.')));
      }
    
    }
    
    
		pg_query($conn,"
    INSERT INTO pacientes VALUES
		(
		DEFAULT,
		'$rut',
		'$nombre',
		'$paterno',
		'$materno',
		$fechanac,
		$sexo,
		$prev,
		'$sector',
		$grupo,
		$sangre,
		'$direccion',
		$comuna,
		$nacion,
		$estciv,
		'$fono',
		'$padre',
		'$madre',
		'$tramo',
		'$pasaporte'
		);
		");
		
		
		// Obtiene el ID del paciente ingresado para
		// pasarlo a la aplicación...
		
		$id_nuevo = pg_query($conn, "
    SELECT CURRVAL('pacientes_pac_id_seq');
    ");
    
    $id_a = pg_fetch_row($id_nuevo);
    
    $id=$id_a[0];
		
		} else {
    
    pg_query($conn,"
    UPDATE pacientes SET 
		pac_nombres='$nombre',
		pac_appat='$paterno',
		pac_apmat='$materno',
		pac_fc_nac=$fechanac,
		sex_id=$sexo,
		prev_id=$prev,
		sector_nombre='$sector',
    getn_id=$grupo,
		sang_id=$sangre,
		pac_direccion='$direccion',
		ciud_id=$comuna,
		nacion_id=$nacion,
		estciv_id=$estciv,
		pac_fono='$fono',
		pac_padre='$padre',
		pac_madre='$madre',
		pac_tramo='$tramo'
		WHERE
		pac_id=$id;
		");
		
    
    }

  print(json_encode(Array(true, $id)));


?>
