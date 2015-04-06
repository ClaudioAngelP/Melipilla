<?php 

	require_once('../conectar_db.php');
	require_once('../conectores/hgf.php');
	require_once('../conectores/hgf_sqlserver.php');
	
	pg_query("TRUNCATE TABLE doctores;");	
	
	$medicos=mssql_query("SELECT * FROM dbo.SER_Profesiona", $sybase);
	
	while($r=mssql_fetch_object($medicos)) {

		$prut=explode('-',trim($r->SER_PRO_Rut));
		$rut=($prut[0]*1).'-'.strtoupper($prut[1]);		
		$pat=trim(utf8_decode($r->SER_PRO_ApellPater));		
		$mat=trim(utf8_decode($r->SER_PRO_ApellMater));		
		$nom=trim(utf8_decode($r->SER_PRO_Nombres));

		$q="
			INSERT INTO doctores VALUES (
				DEFAULT,
				'$rut',
				'$pat',
				'$mat',
				'$nom'			
			);		
		";
		
		print($q.'<br><br>');

		pg_query($q);		
		
	}

	//pg_query("TRUNCATE TABLE especialidades;");	
	
	$espec = mssql_query( "SELECT * FROM dbo.SER_Especiali", $sybase );
	
	while( $r=mssql_fetch_object($espec) ) {

		$q="INSERT INTO especialidades VALUES (
				DEFAULT,
				'".pg_escape_string($r->SER_ESP_Descripcio)."',
				0,
				0,
				'',
				'".pg_escape_string(trim($r->SER_ESP_Codigo))."'			
			);";
		
		print($q.'<br><br>');

		pg_query($q);
		
	}


	$sespec=mssql_query("SELECT * FROM dbo.SER_Servicios", $sybase );
	
	while( $r=mssql_fetch_object($sespec) ) {

		$e=cargar_registro("SELECT * FROM especialidades 
									WHERE esp_codigo_int='".pg_escape_string(trim($r->SER_SER_CodigEspec))."'");

		if($e) {
			$padre_id=$e['esp_id'];	
		} else {
			print("Especialidad esp_codigo_int='".pg_escape_string(trim($r->SER_SER_CodigEspec))."' no existe.<br><br>");
			$padre_id=0;
		}

		$q="
			INSERT INTO especialidades VALUES (
				DEFAULT,
				'".pg_escape_string($r->SER_SER_Descripcio)."',
				0,
				$padre_id,
				'',
				'".pg_escape_string(trim($r->SER_SER_Codigo))."'			
			);		
		";
		
		print($q.'<br><br>');

		pg_query($q);
		
	}


?>